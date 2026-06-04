<?php

namespace App\Http\Controllers;

use App\Helpers\PermissionHelper;
use App\Models\AdmissionBatch;
use App\Models\Batch;
use App\Models\Course;
use App\Models\CourseMail;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class CourseMailController extends Controller
{
    public const ALL_ADMISSION_BATCHES = 'all';

    private function canManage(): bool
    {
        return PermissionHelper::can_manage_subject_areas_mails_flags();
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
        if (! $batch || (int) $batch->course_id !== (int) $request->course_id) {
            throw ValidationException::withMessages([
                'batch_id' => ['The selected batch does not belong to the selected course.'],
            ]);
        }

        if ($this->isAllAdmissionBatches($request->admission_batch_id)) {
            return;
        }

        $admissionBatch = AdmissionBatch::find($request->admission_batch_id);
        if (! $admissionBatch || (int) $admissionBatch->batch_id !== (int) $request->batch_id) {
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

    public function index(Request $request)
    {
        if (! $this->canManage()) {
            return redirect()->route('dashboard')->with('message_danger', 'Access denied.');
        }

        $courses = Course::active()->orderBy('title')->get();

        $selectedCourseId = $request->filled('course_id') ? (int) $request->course_id : null;
        $selectedBatchId = $request->filled('batch_id') ? (int) $request->batch_id : null;
        $selectedAdmissionBatchId = null;

        if ($request->has('admission_batch_id') && $request->admission_batch_id !== '') {
            $selectedAdmissionBatchId = $this->isAllAdmissionBatches($request->admission_batch_id)
                ? self::ALL_ADMISSION_BATCHES
                : (int) $request->admission_batch_id;
        }

        if ($selectedBatchId && $selectedCourseId) {
            $batch = Batch::find($selectedBatchId);
            if (! $batch || (int) $batch->course_id !== $selectedCourseId) {
                $selectedBatchId = null;
                $selectedAdmissionBatchId = null;
            }
        } elseif ($selectedBatchId && ! $selectedCourseId) {
            $batch = Batch::find($selectedBatchId);
            if ($batch) {
                $selectedCourseId = (int) $batch->course_id;
            }
        }

        if ($selectedAdmissionBatchId
            && $selectedAdmissionBatchId !== self::ALL_ADMISSION_BATCHES
            && $selectedBatchId) {
            $admissionBatch = AdmissionBatch::find($selectedAdmissionBatchId);
            if (! $admissionBatch || (int) $admissionBatch->batch_id !== $selectedBatchId) {
                $selectedAdmissionBatchId = null;
            }
        }

        $batches = collect();
        if ($selectedCourseId) {
            $batches = Batch::where('course_id', $selectedCourseId)
                ->orderBy('title')
                ->get();
        }

        $admissionBatches = collect();
        if ($selectedBatchId) {
            $admissionBatches = AdmissionBatch::where('batch_id', $selectedBatchId)
                ->orderBy('title')
                ->get();
        }

        $query = CourseMail::with(['course', 'batch', 'admissionBatch'])
            ->orderByDesc('created_at');

        if ($selectedCourseId) {
            $query->where('course_id', $selectedCourseId);
        }

        if ($selectedBatchId) {
            $query->where('batch_id', $selectedBatchId);
        }

        if ($selectedAdmissionBatchId !== null) {
            if ($selectedAdmissionBatchId === self::ALL_ADMISSION_BATCHES) {
                $query->whereNull('admission_batch_id');
            } else {
                $query->where('admission_batch_id', $selectedAdmissionBatchId);
            }
        }

        $mails = $query->get();

        $hasActiveFilters = $selectedCourseId
            || $selectedBatchId
            || $selectedAdmissionBatchId !== null;

        return view('admin.mails.index', compact(
            'mails',
            'courses',
            'batches',
            'admissionBatches',
            'selectedCourseId',
            'selectedBatchId',
            'selectedAdmissionBatchId',
            'hasActiveFilters'
        ));
    }

    public function update(Request $request, $id)
    {
        if (! $this->canManage()) {
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
        if (! $this->canManage()) {
            return redirect()->route('dashboard')->with('message_danger', 'Access denied.');
        }

        $courses = Course::active()->orderBy('title')->get();

        return view('admin.mails.add', compact('courses'));
    }

    public function submit(Request $request)
    {
        if (! $this->canManage()) {
            if ($request->ajax()) {
                return response()->json(['error' => 'Access denied.'], 403);
            }

            return redirect()->route('dashboard')->with('message_danger', 'Access denied.');
        }

        $validated = $request->validate($this->baseRules());
        $this->validateHierarchy($request);

        $admissionBatchId = $this->resolveAdmissionBatchId($request);
        $content = trim((string) $validated['content']);

        $mail = CourseMail::where('course_id', $validated['course_id'])
            ->where('batch_id', $validated['batch_id'])
            ->where('admission_batch_id', $admissionBatchId)
            ->where('content', $content)
            ->first();
        $wasCreated = false;

        if (! $mail) {
            $mail = CourseMail::create([
                'course_id' => $validated['course_id'],
                'batch_id' => $validated['batch_id'],
                'admission_batch_id' => $admissionBatchId,
                'content' => $content,
            ]);
            $wasCreated = true;
        }

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $wasCreated ? 'Mail created successfully!' : 'Duplicate mail content already exists for this course/batch/admission batch.',
                'data' => $mail,
            ]);
        }

        return redirect()->route('admin.mails.index')->with(
            'message_success',
            $wasCreated ? 'Mail created successfully!' : 'Duplicate mail content already exists for this course/batch/admission batch.'
        );
    }

    public function ajax_edit($id)
    {
        if (! $this->canManage()) {
            return redirect()->route('dashboard')->with('message_danger', 'Access denied.');
        }

        $edit_data = CourseMail::findOrFail($id);
        $courses = Course::active()->orderBy('title')->get();

        return view('admin.mails.edit', compact('edit_data', 'courses'));
    }

    public function delete($id)
    {
        if (! $this->canManage()) {
            return redirect()->route('dashboard')->with('message_danger', 'Access denied.');
        }

        $mail = CourseMail::findOrFail($id);
        $mail->delete();

        return redirect()->route('admin.mails.index')->with('message_success', 'Mail deleted successfully!');
    }
}
