<?php

namespace App\Http\Controllers;

use App\Helpers\RoleHelper;
use App\Models\Course;
use App\Models\LmsCourse;
use App\Models\LmsCourseMapping;
use App\Services\LmsCourseService;
use Illuminate\Http\Request;
use RuntimeException;

class CourseMappingController extends Controller
{
    private function authorizeAccess(): ?\Illuminate\Http\RedirectResponse
    {
        if (! RoleHelper::is_admin_or_super_admin() && ! RoleHelper::is_admission_counsellor()) {
            return redirect()->route('dashboard')->with('message_danger', 'Access denied.');
        }

        return null;
    }

    public function index()
    {
        if ($denied = $this->authorizeAccess()) {
            return $denied;
        }

        $lmsCourses = LmsCourse::with(['mapping.course'])
            ->orderBy('title')
            ->get();

        $courses = Course::where('is_active', true)
            ->orderBy('title')
            ->get(['id', 'title']);

        $stats = [
            'total' => $lmsCourses->count(),
            'mapped' => $lmsCourses->filter(fn ($course) => $course->mapping !== null)->count(),
            'unmapped' => $lmsCourses->filter(fn ($course) => $course->mapping === null)->count(),
        ];

        return view('admin.lms-setup.course-mapping.index', compact('lmsCourses', 'courses', 'stats'));
    }

    public function refresh(LmsCourseService $lmsCourseService)
    {
        if ($denied = $this->authorizeAccess()) {
            return $denied;
        }

        try {
            $result = $lmsCourseService->refreshCourses();

            $message = sprintf(
                'LMS courses refreshed. Fetched: %d, New: %d, Updated: %d.',
                $result['fetched'],
                $result['created'],
                $result['updated']
            );

            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'data' => $result,
                ]);
            }

            return redirect()
                ->route('admin.course-mapping.index')
                ->with('message_success', $message);
        } catch (RuntimeException $e) {
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                ], 422);
            }

            return redirect()
                ->route('admin.course-mapping.index')
                ->with('message_danger', $e->getMessage());
        } catch (\Exception $e) {
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'An error occurred while refreshing LMS courses.',
                ], 500);
            }

            return redirect()
                ->route('admin.course-mapping.index')
                ->with('message_danger', 'An error occurred while refreshing LMS courses.');
        }
    }

    public function map(Request $request, $lmsCourseId)
    {
        if ($denied = $this->authorizeAccess()) {
            return $denied;
        }

        try {
            $request->validate([
                'course_id' => 'required|exists:courses,id',
            ]);

            $lmsCourse = LmsCourse::findOrFail($lmsCourseId);

            LmsCourseMapping::updateOrCreate(
                ['lms_course_id' => $lmsCourse->id],
                ['course_id' => (int) $request->course_id]
            );

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Course mapping saved successfully!',
                ]);
            }

            return redirect()
                ->route('admin.course-mapping.index')
                ->with('message_success', 'Course mapping saved successfully!');
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please select a CRM course.',
                    'errors' => $e->validator->errors(),
                ], 422);
            }

            return redirect()
                ->back()
                ->withErrors($e->validator)
                ->with('message_danger', 'Please select a CRM course.');
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'An error occurred while saving the mapping.',
                ], 500);
            }

            return redirect()
                ->route('admin.course-mapping.index')
                ->with('message_danger', 'An error occurred while saving the mapping.');
        }
    }

    public function unmap($lmsCourseId)
    {
        if ($denied = $this->authorizeAccess()) {
            return $denied;
        }

        try {
            $mapping = LmsCourseMapping::where('lms_course_id', $lmsCourseId)->first();

            if ($mapping) {
                $mapping->delete();
            }

            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Course mapping removed successfully!',
                ]);
            }

            return redirect()
                ->route('admin.course-mapping.index')
                ->with('message_success', 'Course mapping removed successfully!');
        } catch (\Exception $e) {
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'An error occurred while removing the mapping.',
                ], 500);
            }

            return redirect()
                ->route('admin.course-mapping.index')
                ->with('message_danger', 'An error occurred while removing the mapping.');
        }
    }
}
