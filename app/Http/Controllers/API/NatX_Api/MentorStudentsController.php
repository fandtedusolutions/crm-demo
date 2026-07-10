<?php

namespace App\Http\Controllers\API\NatX_Api;

use App\Http\Controllers\Controller;
use App\Models\AdmissionBatch;
use App\Models\Batch;
use App\Models\ConvertedLead;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MentorStudentsController extends Controller
{
    /**
     * List mentor students (all courses) with optional filters.
     *
     * Query params: from_date, to_date, course, batch
     */
    public function students(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 401);
        }

        $validator = Validator::make($request->all(), [
            'from_date' => 'nullable|date',
            'to_date' => 'nullable|date|after_or_equal:from_date',
            'course' => 'nullable|integer|exists:courses,id',
            'batch' => 'nullable|integer|exists:batches,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        $mentorAdmissionBatchIds = AdmissionBatch::where('mentor_id', $user->id)
            ->pluck('id')
            ->toArray();

        if (empty($mentorAdmissionBatchIds)) {
            return response()->json([
                'success' => true,
                'data' => [],
            ], 200);
        }

        $query = ConvertedLead::with([
            'batch:id,title',
            'course:id,title',
            'leadDetail:id,lead_id,whatsapp_number,whatsapp_code',
        ])
            ->where('is_support_verified', 1)
            ->whereIn('admission_batch_id', $mentorAdmissionBatchIds);

        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        if ($request->filled('course')) {
            $query->where('course_id', $request->course);
        }

        if ($request->filled('batch')) {
            $query->where('batch_id', $request->batch);
        }

        $students = $query->orderBy('created_at', 'desc')
            ->get()
            ->map(function (ConvertedLead $student) {
                return [
                    'id' => $student->id,
                    'name' => $student->name,
                    'phone' => $student->phone,
                    'register_number' => $student->register_number,
                    'course_name' => $student->course?->title,
                    'batch_name' => $student->batch?->title,
                    'whatsapp_no' => $student->leadDetail?->whatsapp_number,
                ];
            })
            ->values();

        return response()->json([
            'success' => true,
            'data' => $students,
        ], 200);
    }

    /**
     * List active courses.
     */
    public function courses(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 401);
        }

        $courses = Course::active()
            ->orderBy('title')
            ->get(['id', 'title', 'code']);

        return response()->json([
            'success' => true,
            'data' => $courses,
        ], 200);
    }

    /**
     * List batches for a course.
     */
    public function batches(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 401);
        }

        $validator = Validator::make($request->all(), [
            'course_id' => 'required|integer|exists:courses,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        $batches = Batch::where('course_id', $request->course_id)
            ->orderBy('is_active', 'desc')
            ->orderBy('title')
            ->get(['id', 'title', 'course_id', 'is_active']);

        return response()->json([
            'success' => true,
            'data' => $batches,
        ], 200);
    }
}
