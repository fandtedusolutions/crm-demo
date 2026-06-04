<?php

namespace App\Http\Controllers;

use App\Models\AdmissionBatch;
use App\Models\Batch;
use App\Models\Course;
use App\Models\User;
use Illuminate\Http\Request;
use App\Helpers\RoleHelper;
use App\Helpers\AuthHelper;

class AdmissionBatchController extends Controller
{
    public function index(Request $request)
    {
        if (!RoleHelper::is_admin_or_super_admin() && !RoleHelper::is_admission_counsellor()) {
            return redirect()->route('dashboard')->with('message_danger', 'Access denied.');
        }

        $courses = Course::active()->orderBy('title')->get();

        $selectedCourseId = $request->filled('course_id') ? (int) $request->course_id : null;
        $selectedBatchId = $request->filled('batch_id') ? (int) $request->batch_id : null;

        if ($selectedBatchId && $selectedCourseId) {
            $batch = Batch::find($selectedBatchId);
            if (! $batch || (int) $batch->course_id !== $selectedCourseId) {
                $selectedBatchId = null;
            }
        } elseif ($selectedBatchId && ! $selectedCourseId) {
            $batch = Batch::find($selectedBatchId);
            if ($batch) {
                $selectedCourseId = (int) $batch->course_id;
            }
        }

        $batches = collect();
        if ($selectedCourseId) {
            $batches = Batch::where('course_id', $selectedCourseId)
                ->orderBy('title')
                ->get();
        }

        $query = AdmissionBatch::with(['batch.course', 'mentor', 'createdBy', 'updatedBy'])
            ->orderBy('created_at', 'desc');

        if ($selectedBatchId) {
            $query->where('batch_id', $selectedBatchId);
        } elseif ($selectedCourseId) {
            $query->whereHas('batch', function ($q) use ($selectedCourseId) {
                $q->where('course_id', $selectedCourseId);
            });
        }

        $admissionBatches = $query->get();

        $hasActiveFilters = $selectedCourseId || $selectedBatchId;

        return view('admin.admission-batches.index', compact(
            'admissionBatches',
            'courses',
            'batches',
            'selectedCourseId',
            'selectedBatchId',
            'hasActiveFilters'
        ));
    }

    public function store(Request $request)
    {
        if (!RoleHelper::is_admin_or_super_admin() && !RoleHelper::is_admission_counsellor()) {
            return response()->json(['error' => 'Access denied.'], 403);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'batch_id' => 'required|exists:batches,id',
            'mentor_id' => 'nullable|exists:users,id',
            'description' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);

        $admissionBatch = AdmissionBatch::create([
            'title' => $request->title,
            'batch_id' => $request->batch_id,
            'mentor_id' => $request->mentor_id,
            'description' => $request->description,
            'is_active' => $request->has('is_active'),
            'created_by' => AuthHelper::getCurrentUserId(),
            'updated_by' => AuthHelper::getCurrentUserId(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Admission Batch created successfully.',
            'data' => $admissionBatch
        ]);
    }

    public function show(AdmissionBatch $admissionBatch)
    {
        if (!RoleHelper::is_admin_or_super_admin() && !RoleHelper::is_admission_counsellor()) {
            return response()->json(['error' => 'Access denied.'], 403);
        }

        return response()->json($admissionBatch);
    }

    public function destroy(AdmissionBatch $admissionBatch)
    {
        if (!RoleHelper::is_admin_or_super_admin() && !RoleHelper::is_admission_counsellor()) {
            return response()->json(['error' => 'Access denied.'], 403);
        }

        try {
            $admissionBatch->delete();
            return response()->json([
                'success' => true,
                'message' => 'Admission Batch deleted successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete admission batch: ' . $e->getMessage()
            ], 500);
        }
    }

    public function ajax_add()
    {
        if (!RoleHelper::is_admin_or_super_admin() && !RoleHelper::is_admission_counsellor()) {
            return redirect()->route('dashboard')->with('message_danger', 'Access denied.');
        }

        $batches = Batch::with('course')->where('is_active', true)->get();
        $mentors = User::where('role_id', 9)->where('is_active', true)->get();
        return view('admin.admission-batches.add', compact('batches', 'mentors'));
    }

    public function submit(Request $request)
    {
        if (!RoleHelper::is_admin_or_super_admin() && !RoleHelper::is_admission_counsellor()) {
            return redirect()->route('dashboard')->with('message_danger', 'Access denied.');
        }

        try {
            // Normalize checkbox to boolean before validation
            $request->merge([
                'is_active' => $request->has('is_active') ? 1 : 0,
            ]);

            $request->validate([
                'title' => 'required|string|max:255',
                'batch_id' => 'required|exists:batches,id',
                'description' => 'nullable|string',
                'is_active' => 'nullable|boolean',
            ]);

            $admissionBatch = AdmissionBatch::create([
                'title' => $request->title,
                'batch_id' => $request->batch_id,
                'mentor_id' => $request->mentor_id,
                'description' => $request->description,
                'is_active' => $request->is_active,
                'created_by' => AuthHelper::getCurrentUserId(),
                'updated_by' => AuthHelper::getCurrentUserId(),
            ]);

            return redirect()->route('admin.admission-batches.index')->with('message_success', 'Admission Batch created successfully!');
        } catch (\Throwable $e) {
            return back()->withInput()->with('message_danger', 'Failed to create admission batch: ' . $e->getMessage());
        }
    }

    public function ajax_edit($id)
    {
        if (!RoleHelper::is_admin_or_super_admin() && !RoleHelper::is_admission_counsellor()) {
            return redirect()->route('dashboard')->with('message_danger', 'Access denied.');
        }

        $edit_data = AdmissionBatch::findOrFail($id);
        $batches = Batch::with('course')->where('is_active', true)->get();
        $mentors = User::where('role_id', 9)->where('is_active', true)->get();
        return view('admin.admission-batches.edit', compact('edit_data', 'batches', 'mentors'));
    }

    public function update(Request $request, $id)
    {
        if (!RoleHelper::is_admin_or_super_admin() && !RoleHelper::is_admission_counsellor()) {
            return redirect()->route('dashboard')->with('message_danger', 'Access denied.');
        }

        try {
            // Normalize checkbox to boolean before validation
            $request->merge([
                'is_active' => $request->has('is_active') ? 1 : 0,
            ]);

            $request->validate([
                'title' => 'required|string|max:255',
                'batch_id' => 'required|exists:batches,id',
                'mentor_id' => 'nullable|exists:users,id',
                'description' => 'nullable|string',
                'is_active' => 'nullable|boolean',
            ]);

            $admissionBatch = AdmissionBatch::findOrFail($id);
            $admissionBatch->update([
                'title' => $request->title,
                'batch_id' => $request->batch_id,
                'mentor_id' => $request->mentor_id,
                'description' => $request->description,
                'is_active' => $request->is_active,
                'updated_by' => AuthHelper::getCurrentUserId(),
            ]);

            return redirect()->route('admin.admission-batches.index')->with('message_success', 'Admission Batch updated successfully!');
        } catch (\Throwable $e) {
            return back()->withInput()->with('message_danger', 'Failed to update admission batch: ' . $e->getMessage());
        }
    }

    public function delete($id)
    {
        if (!RoleHelper::is_admin_or_super_admin() && !RoleHelper::is_admission_counsellor()) {
            return redirect()->route('dashboard')->with('message_danger', 'Access denied.');
        }

        try {
            $admissionBatch = AdmissionBatch::findOrFail($id);
            $admissionBatch->delete();

            return redirect()->route('admin.admission-batches.index')->with('message_success', 'Admission Batch deleted successfully!');
        } catch (\Throwable $e) {
            return redirect()->route('admin.admission-batches.index')->with('message_danger', 'Failed to delete admission batch: ' . $e->getMessage());
        }
    }

    /**
     * Get admission batches by batch for AJAX requests
     */
    public function getByBatch($batchId)
    {
        $admissionBatches = AdmissionBatch::where('batch_id', $batchId)
            ->select('id', 'title', 'is_active')
            ->orderBy('is_active', 'desc')
            ->orderBy('title')
            ->get();

        return response()->json($admissionBatches);
    }
}
