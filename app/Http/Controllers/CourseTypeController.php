<?php

namespace App\Http\Controllers;

use App\Helpers\RoleHelper;
use App\Models\Course;
use App\Models\CourseType;
use Illuminate\Http\Request;

class CourseTypeController extends Controller
{
    private function authorizeAccess(): ?\Illuminate\Http\RedirectResponse
    {
        if (!RoleHelper::is_admin_or_super_admin() && !RoleHelper::is_admission_counsellor()) {
            return redirect()->route('dashboard')->with('message_danger', 'Access denied.');
        }

        return null;
    }

    public function index(Request $request)
    {
        if ($denied = $this->authorizeAccess()) {
            return $denied;
        }

        $courses = Course::where('is_active', true)->orderBy('title')->get();
        $selectedCourseId = $request->filled('course_id') ? (int) $request->course_id : null;

        $query = CourseType::with('course')->orderBy('created_at', 'desc');

        if ($selectedCourseId) {
            $query->where('course_id', $selectedCourseId);
        }

        $courseTypes = $query->get();

        return view('admin.course-types.index', compact('courseTypes', 'courses', 'selectedCourseId'));
    }

    public function ajax_add()
    {
        if ($denied = $this->authorizeAccess()) {
            return $denied;
        }

        $courses = Course::where('is_active', true)->orderBy('title')->get();

        return view('admin.course-types.add', compact('courses'));
    }

    public function submit(Request $request)
    {
        if ($denied = $this->authorizeAccess()) {
            return $denied;
        }

        try {
            $request->validate([
                'title' => 'required|string|max:255',
                'course_id' => 'required|exists:courses,id',
                'is_active' => 'nullable|boolean',
            ]);

            $courseType = CourseType::create([
                'title' => $request->title,
                'course_id' => $request->course_id,
                'is_active' => $request->boolean('is_active'),
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Course type created successfully!',
                    'data' => $courseType,
                ]);
            }

            return redirect()->route('admin.course-types.index')->with('message_success', 'Course type created successfully!');
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please correct the errors below.',
                    'errors' => $e->validator->errors(),
                ], 422);
            }

            return redirect()->back()
                ->withErrors($e->validator)
                ->withInput()
                ->with('message_danger', 'Please correct the errors below.');
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'An error occurred while creating the course type. Please try again.',
                ], 500);
            }

            return redirect()->back()
                ->withInput()
                ->with('message_danger', 'An error occurred while creating the course type. Please try again.');
        }
    }

    public function ajax_edit($id)
    {
        if ($denied = $this->authorizeAccess()) {
            return $denied;
        }

        $edit_data = CourseType::findOrFail($id);
        $courses = Course::where('is_active', true)->orderBy('title')->get();

        return view('admin.course-types.edit', compact('edit_data', 'courses'));
    }

    public function update(Request $request, $id)
    {
        if ($denied = $this->authorizeAccess()) {
            return $denied;
        }

        try {
            $request->validate([
                'title' => 'required|string|max:255',
                'course_id' => 'required|exists:courses,id',
                'is_active' => 'nullable|boolean',
            ]);

            $courseType = CourseType::findOrFail($id);
            $courseType->update([
                'title' => $request->title,
                'course_id' => $request->course_id,
                'is_active' => $request->boolean('is_active'),
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Course type updated successfully!',
                    'data' => $courseType,
                ]);
            }

            return redirect()->route('admin.course-types.index')->with('message_success', 'Course type updated successfully!');
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please correct the errors below.',
                    'errors' => $e->validator->errors(),
                ], 422);
            }

            return redirect()->back()
                ->withErrors($e->validator)
                ->withInput()
                ->with('message_danger', 'Please correct the errors below.');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Course type not found.',
                ], 404);
            }

            return redirect()->route('admin.course-types.index')->with('message_danger', 'Course type not found.');
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'An error occurred while updating the course type. Please try again.',
                ], 500);
            }

            return redirect()->back()
                ->withInput()
                ->with('message_danger', 'An error occurred while updating the course type. Please try again.');
        }
    }

    public function delete($id)
    {
        if ($denied = $this->authorizeAccess()) {
            return $denied;
        }

        try {
            $courseType = CourseType::findOrFail($id);

            if ($courseType->leadDetails()->exists()) {
                $message = 'Cannot delete course type. It is used in registration records.';

                if (request()->ajax()) {
                    return response()->json(['success' => false, 'message' => $message], 422);
                }

                return redirect()->route('admin.course-types.index')->with('message_danger', $message);
            }

            $courseType->delete();

            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Course type deleted successfully!',
                ]);
            }

            return redirect()->route('admin.course-types.index')->with('message_success', 'Course type deleted successfully!');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->route('admin.course-types.index')->with('message_danger', 'Course type not found.');
        } catch (\Exception $e) {
            return redirect()->route('admin.course-types.index')->with('message_danger', 'An error occurred while deleting the course type. Please try again.');
        }
    }

    public function getByCourse($courseId)
    {
        $courseTypes = CourseType::where('course_id', $courseId)
            ->where('is_active', true)
            ->orderBy('title')
            ->get(['id', 'title', 'course_id', 'is_active']);

        return response()->json([
            'success' => true,
            'course_types' => $courseTypes,
        ]);
    }
}
