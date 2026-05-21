<?php

namespace App\Http\Controllers;

use App\Models\AdmissionBatch;
use App\Models\Batch;
use App\Models\Course;
use App\Models\CourseMail;
use Illuminate\Http\Request;
use App\Helpers\RoleHelper;
use Illuminate\Validation\ValidationException;

class CourseMailController extends Controller
{
    public const ALL_ADMISSION_BATCHES = 'all';

    private function canManage(): bool
    {
        return RoleHelper::is_admin_or_super_admin() || RoleHelper::is_admission_counsellor();
    }

    private function isAllAdmissionBatches($value): bool
    {
        return $value === self::ALL_ADMISSION_BATCHES
            || $value === null
            || $value === '';
    }

    private function resolveAdmissionBatchId(Request $request): ?int
    {
        return $this->isAllAdmissionBatches($request->admission_batch_id)
            ? null
            : (int) $request->admission_batch_id;
    }

    private function validateHierarchy(Request $request): void
    {
        $batch = Batch::find($request->batch_id);
        if (!$batch || (int) $batch->course_id !== (int) $request->course_id) {
            throw ValidationException::withMessages([
                'batch_id' => ['The selected batch does not belong to the selected course.'],
            ]);
        }

        if ($this->isAllAdmissionBatches($request->admission_batch_id)) {
            return;
        }

        $admissionBatch = AdmissionBatch::find($request->admission_batch_id);
        if (!$admissionBatch || (int) $admissionBatch->batch_id !== (int) $request->batch_id) {
            throw ValidationException::withMessages([
                'admission_batch_id' => ['The selected admission batch does not belong to the selected batch.'],
            ]);
        }
    }

    private function baseRules(): array
    {
        return [
            'course_id' => 'required|exists:courses,id',
            'batch_id' => 'required|exists:batches,id',
            'admission_batch_id' => [
                'required',
                function (string $attribute, mixed $value, \Closure $fail): void {
                    if ($this->isAllAdmissionBatches($value)) {
                        return;
                    }

                    if (! AdmissionBatch::where('id', $value)->exists()) {
                        $fail('The selected admission batch is invalid.');
                    }
                },
            ],
            'content' => 'required|string',
        ];
    }

    public function index()
    {
        if (!$this->canManage()) {
            return redirect()->route('dashboard')->with('message_danger', 'Access denied.');
        }

        $mails = CourseMail::with(['course', 'batch', 'admissionBatch'])
            ->orderByDesc('created_at')
            ->get();

        return view('admin.mails.index', compact('mails'));
    }

    public function update(Request $request, $id)
    {
        if (!$this->canManage()) {
            if ($request->ajax()) {
                return response()->json(['error' => 'Access denied.'], 403);
            }
            return redirect()->route('dashboard')->with('message_danger', 'Access denied.');
        }

        $request->validate($this->baseRules());
        $this->validateHierarchy($request);

        $mail = CourseMail::findOrFail($id);
        $mail->update([
            'course_id' => $request->course_id,
            'batch_id' => $request->batch_id,
            'admission_batch_id' => $this->resolveAdmissionBatchId($request),
            'content' => $request->content,
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Mail updated successfully.',
                'data' => $mail,
            ]);
        }

        return redirect()->route('admin.mails.index')->with('message_success', 'Mail updated successfully!');
    }

    public function ajax_add()
    {
        if (!$this->canManage()) {
            return redirect()->route('dashboard')->with('message_danger', 'Access denied.');
        }

        $courses = Course::active()->orderBy('title')->get();
        return view('admin.mails.add', compact('courses'));
    }

    public function submit(Request $request)
    {
        if (!$this->canManage()) {
            if ($request->ajax()) {
                return response()->json(['error' => 'Access denied.'], 403);
            }
            return redirect()->route('dashboard')->with('message_danger', 'Access denied.');
        }

        $request->validate($this->baseRules());
        $this->validateHierarchy($request);

        $mail = CourseMail::create([
            'course_id' => $request->course_id,
            'batch_id' => $request->batch_id,
            'admission_batch_id' => $this->resolveAdmissionBatchId($request),
            'content' => $request->content,
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Mail created successfully!',
                'data' => $mail,
            ]);
        }

        return redirect()->route('admin.mails.index')->with('message_success', 'Mail created successfully!');
    }

    public function ajax_edit($id)
    {
        if (!$this->canManage()) {
            return redirect()->route('dashboard')->with('message_danger', 'Access denied.');
        }

        $edit_data = CourseMail::findOrFail($id);
        $courses = Course::active()->orderBy('title')->get();
        return view('admin.mails.edit', compact('edit_data', 'courses'));
    }

    public function delete($id)
    {
        if (!$this->canManage()) {
            return redirect()->route('dashboard')->with('message_danger', 'Access denied.');
        }

        $mail = CourseMail::findOrFail($id);
        $mail->delete();

        return redirect()->route('admin.mails.index')->with('message_success', 'Mail deleted successfully!');
    }
}
