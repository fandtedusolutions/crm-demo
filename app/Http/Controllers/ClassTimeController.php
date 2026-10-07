<?php

namespace App\Http\Controllers;

use App\Models\ClassTime;
use App\Models\Course;
use Illuminate\Http\Request;
use App\Helpers\RoleHelper;

class ClassTimeController extends Controller
{
    public function index()
    {
        if (!RoleHelper::is_admin_or_super_admin() && !RoleHelper::is_admission_counsellor()) {
            return redirect()->route('dashboard')->with('message_danger', 'Access denied.');
        }

        $classTimes = ClassTime::with('course')->get();
        return view('admin.class-times.index', compact('classTimes'));
    }

    public function store(Request $request)
    {
        if (!RoleHelper::is_admin_or_super_admin() && !RoleHelper::is_admission_counsellor()) {
            return response()->json(['error' => 'Access denied.'], 403);
        }

        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'class_type' => 'required|in:online,offline',
            'from_time' => 'required|date_format:H:i',
            'to_time' => 'required|date_format:H:i|after:from_time',
            'is_active' => 'boolean',
        ]);

        $classTime = ClassTime::create([
            'course_id' => $request->course_id,
            'class_type' => $request->class_type,
            'from_time' => $request->from_time,
            'to_time' => $request->to_time,
            'is_active' => $request->input('is_active', 0) == 1,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Class time created successfully.',
            'data' => $classTime
        ]);
    }

    public function show(ClassTime $classTime)
    {
        if (!RoleHelper::is_admin_or_super_admin() && !RoleHelper::is_admission_counsellor()) {
            return response()->json(['error' => 'Access denied.'], 403);
        }

        return response()->json($classTime->load('course'));
    }

    public function destroy(ClassTime $classTime)
    {
        if (!RoleHelper::is_admin_or_super_admin() && !RoleHelper::is_admission_counsellor()) {
            return response()->json(['error' => 'Access denied.'], 403);
        }

        $classTime->delete();

        return response()->json([
            'success' => true,
            'message' => 'Class time deleted successfully.'
        ]);
    }

    public function ajax_add()
    {
        if (!RoleHelper::is_admin_or_super_admin() && !RoleHelper::is_admission_counsellor()) {
            return redirect()->route('dashboard')->with('message_danger', 'Access denied.');
        }

        // Only get courses that have needs_time = true
        $courses = Course::where('needs_time', true)->active()->get();
        return view('admin.class-times.add', compact('courses'));
    }

    public function submit(Request $request)
    {
        if (!RoleHelper::is_admin_or_super_admin() && !RoleHelper::is_admission_counsellor()) {
            return redirect()->route('dashboard')->with('message_danger', 'Access denied.');
        }

        try {
            $request->validate([
                'course_id' => 'required|exists:courses,id',
                'class_type' => 'required|in:online,offline',
                'from_time' => 'required|date_format:H:i',
                'to_time' => 'required|date_format:H:i|after:from_time',
                'is_active' => 'nullable|boolean',
            ]);

            // Verify that the course has needs_time = true
            $course = Course::findOrFail($request->course_id);
            if (!$course->needs_time) {
                return response()->json([
                    'success' => false,
                    'message' => 'This course does not require class time.'
                ], 422);
            }

            $classTime = ClassTime::create([
                'course_id' => $request->course_id,
                'class_type' => $request->class_type,
                'from_time' => $request->from_time,
                'to_time' => $request->to_time,
                'is_active' => $request->boolean('is_active'),
            ]);

            // For AJAX requests, return JSON response
            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Class time created successfully!',
                    'data' => $classTime
                ]);
            }

            return redirect()->route('admin.class-times.index')->with('message_success', 'Class time created successfully!');
        } catch (\Illuminate\Validation\ValidationException $e) {
            // For AJAX requests, return JSON response
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please correct the errors below.',
                    'errors' => $e->validator->errors()
                ], 422);
            }
            
            return redirect()->back()
                ->withErrors($e->validator)
                ->withInput()
                ->with('message_danger', 'Please correct the errors below.');
        } catch (\Exception $e) {
            // For AJAX requests, return JSON response
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'An error occurred while creating the class time. Please try again.'
                ], 500);
            }
            
            return redirect()->back()
                ->withInput()
                ->with('message_danger', 'An error occurred while creating the class time. Please try again.');
        }
    }

    public function ajax_edit($id)
    {
        if (!RoleHelper::is_admin_or_super_admin() && !RoleHelper::is_admission_counsellor()) {
            return redirect()->route('dashboard')->with('message_danger', 'Access denied.');
        }

        $edit_data = ClassTime::with('course')->findOrFail($id);
        // Only get courses that have needs_time = true
        $courses = Course::where('needs_time', true)->active()->get();
        return view('admin.class-times.edit', compact('edit_data', 'courses'));
    }

    public function update(Request $request, $id)
    {
        if (!RoleHelper::is_admin_or_super_admin() && !RoleHelper::is_admission_counsellor()) {
            return redirect()->route('dashboard')->with('message_danger', 'Access denied.');
        }

        try {
            $request->validate([
                'course_id' => 'required|exists:courses,id',
                'class_type' => 'required|in:online,offline',
                'from_time' => 'required|date_format:H:i',
                'to_time' => 'required|date_format:H:i|after:from_time',
                'is_active' => 'nullable|boolean',
            ]);

            // Verify that the course has needs_time = true
            $course = Course::findOrFail($request->course_id);
            if (!$course->needs_time) {
                return response()->json([
                    'success' => false,
                    'message' => 'This course does not require class time.'
                ], 422);
            }

            $classTime = ClassTime::findOrFail($id);
            $classTime->update([
                'course_id' => $request->course_id,
                'class_type' => $request->class_type,
                'from_time' => $request->from_time,
                'to_time' => $request->to_time,
                'is_active' => $request->boolean('is_active'),
            ]);

            // For AJAX requests, return JSON response
            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Class time updated successfully!',
                    'data' => $classTime
                ]);
            }

            return redirect()->route('admin.class-times.index')->with('message_success', 'Class time updated successfully!');
        } catch (\Illuminate\Validation\ValidationException $e) {
            // For AJAX requests, return JSON response
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please correct the errors below.',
                    'errors' => $e->validator->errors()
                ], 422);
            }
            
            return redirect()->back()
                ->withErrors($e->validator)
                ->withInput()
                ->with('message_danger', 'Please correct the errors below.');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Class time not found.'
                ], 404);
            }
            
            return redirect()->route('admin.class-times.index')->with('message_danger', 'Class time not found.');
        } catch (\Exception $e) {
            // For AJAX requests, return JSON response
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'An error occurred while updating the class time. Please try again.'
                ], 500);
            }
            
            return redirect()->back()
                ->withInput()
                ->with('message_danger', 'An error occurred while updating the class time. Please try again.');
        }
    }

    public function delete($id)
    {
        if (!RoleHelper::is_admin_or_super_admin() && !RoleHelper::is_admission_counsellor()) {
            return redirect()->route('dashboard')->with('message_danger', 'Access denied.');
        }

        try {
            $classTime = ClassTime::findOrFail($id);
            $classTime->delete();
            return redirect()->route('admin.class-times.index')->with('message_success', 'Class time deleted successfully!');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->route('admin.class-times.index')->with('message_danger', 'Class time not found.');
        } catch (\Exception $e) {
            return redirect()->route('admin.class-times.index')->with('message_danger', 'An error occurred while deleting the class time. Please try again.');
        }
    }

    public function getByCourse($courseId, Request $request)
    {
        $query = ClassTime::where('course_id', $courseId)
            ->where('is_active', true);

        // Filter by class_type if provided
        if ($request->has('class_type') && in_array($request->class_type, ['online', 'offline'])) {
            $query->where('class_type', $request->class_type);
        }

        $classTimes = $query->get(['id', 'class_type', 'from_time', 'to_time']);

        return response()->json($classTimes);
    }
}
