<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\OfflinePlace;
use Illuminate\Http\Request;
use App\Helpers\RoleHelper;
use App\Support\CourseOfflinePlaceSupport;

class CourseController extends Controller
{
    public function index()
    {
        if (!RoleHelper::is_admin_or_super_admin() && !RoleHelper::is_admission_counsellor() && !RoleHelper::is_finance()) {
            return redirect()->route('dashboard')->with('message_danger', 'Access denied.');
        }

        $courses = Course::all();
        return view('admin.courses.index', compact('courses'));
    }

    public function store(Request $request)
    {
        if (!RoleHelper::is_admin_or_super_admin() && !RoleHelper::is_admission_counsellor() && !RoleHelper::is_finance()) {
            return response()->json(['error' => 'Access denied.'], 403);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'code' => 'nullable|string|max:50',
            'amount' => 'required|numeric|min:0',
            'hod_id' => 'nullable|exists:users,id',
            'hod_number' => 'nullable|string|max:20',
            'is_active' => 'nullable|boolean',
            'needs_time' => 'nullable|boolean',
            'is_online' => 'nullable|boolean',
            'is_offline' => 'nullable|boolean',
        ]);

        $course = Course::create([
            'title' => $request->title,
            'code' => $request->code,
            'amount' => $request->amount,
            'hod_id' => $request->hod_id,
            'hod_number' => $request->hod_number,
            'is_active' => $request->has('is_active') ? 1 : 0,
            'needs_time' => $request->has('needs_time') ? 1 : 0,
            'is_online' => $request->has('is_online') ? 1 : 0,
            'is_offline' => $request->has('is_offline') ? 1 : 0,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Course created successfully.',
            'data' => $course
        ]);
    }

    public function show(Course $course)
    {
        if (!RoleHelper::is_admin_or_super_admin() && !RoleHelper::is_admission_counsellor() && !RoleHelper::is_finance()) {
            return response()->json(['error' => 'Access denied.'], 403);
        }

        return response()->json($course);
    }


    public function destroy(Course $course)
    {
        if (!RoleHelper::is_super_admin()) {
            return response()->json(['error' => 'Access denied.'], 403);
        }

        // Check if course is being used by any leads
        if ($course->leads()->count() > 0) {
            return response()->json([
                'error' => 'Cannot delete course. It is being used by existing leads.'
            ], 422);
        }

        $course->delete();

        return response()->json([
            'success' => true,
            'message' => 'Course deleted successfully.'
        ]);
    }

    public function ajax_add()
    {
        if (!RoleHelper::is_admin_or_super_admin() && !RoleHelper::is_admission_counsellor() && !RoleHelper::is_finance()) {
            return redirect()->route('dashboard')->with('message_danger', 'Access denied.');
        }

        $hodUsers = \App\Models\User::where('role_id', 14)->where('is_active', true)->get();
        $offlinePlaces = OfflinePlace::active()->orderBy('name')->get();
        return view('admin.courses.add', compact('hodUsers', 'offlinePlaces'));
    }

    public function submit(Request $request)
    {
        if (!RoleHelper::is_admin_or_super_admin() && !RoleHelper::is_admission_counsellor() && !RoleHelper::is_finance()) {
            return redirect()->route('dashboard')->with('message_danger', 'Access denied.');
        }

        try {
            $request->validate([
                'title' => 'required|string|max:255',
                'code' => 'nullable|string|max:50',
                'amount' => 'required|numeric|min:0',
                'hod_id' => 'nullable|exists:users,id',
                'hod_number' => 'nullable|string|max:20',
                'is_active' => 'nullable|boolean',
                'needs_time' => 'nullable|boolean',
                'is_online' => 'nullable|boolean',
                'is_offline' => 'nullable|boolean',
                'offline_place_ids' => 'nullable|array',
                'offline_place_ids.*' => 'exists:offline_places,id',
            ]);

            $isOffline = $request->boolean('is_offline');

            $course = Course::create([
                'title' => $request->title,
                'code' => $request->code,
                'amount' => $request->amount,
                'hod_id' => $request->hod_id,
                'hod_number' => $request->hod_number,
                'is_active' => $request->boolean('is_active'),
                'needs_time' => $request->boolean('needs_time'),
                'is_online' => $request->boolean('is_online'),
                'is_offline' => $isOffline,
            ]);

            CourseOfflinePlaceSupport::syncForCourse(
                $course,
                $request->input('offline_place_ids', []),
                $isOffline
            );

            // For AJAX requests, return JSON response
            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Course created successfully!',
                    'data' => $course
                ]);
            }

            return redirect()->route('admin.courses.index')->with('message_success', 'Course created successfully!');
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
                    'message' => 'An error occurred while creating the course. Please try again.'
                ], 500);
            }
            
            return redirect()->back()
                ->withInput()
                ->with('message_danger', 'An error occurred while creating the course. Please try again.');
        }
    }

    public function ajax_edit($id)
    {
        if (!RoleHelper::is_admin_or_super_admin() && !RoleHelper::is_admission_counsellor() && !RoleHelper::is_finance()) {
            return redirect()->route('dashboard')->with('message_danger', 'Access denied.');
        }

        $edit_data = Course::with('offlinePlaces')->findOrFail($id);
        $hodUsers = \App\Models\User::where('role_id', 14)->where('is_active', true)->get();
        $offlinePlaces = OfflinePlace::active()->orderBy('name')->get();
        
        return view('admin.courses.edit', compact('edit_data', 'hodUsers', 'offlinePlaces'));
    }

    public function update(Request $request, $id)
    {
        if (!RoleHelper::is_admin_or_super_admin() && !RoleHelper::is_admission_counsellor() && !RoleHelper::is_finance()) {
            return redirect()->route('dashboard')->with('message_danger', 'Access denied.');
        }

        try {
            $request->validate([
                'title' => 'required|string|max:255',
                'code' => 'nullable|string|max:50',
                'amount' => 'required|numeric|min:0',
                'hod_id' => 'nullable|exists:users,id',
                'hod_number' => 'nullable|string|max:20',
                'is_active' => 'nullable|boolean',
                'needs_time' => 'nullable|boolean',
                'is_online' => 'nullable|boolean',
                'is_offline' => 'nullable|boolean',
                'offline_place_ids' => 'nullable|array',
                'offline_place_ids.*' => 'exists:offline_places,id',
            ]);

            $course = Course::findOrFail($id);
            $isOffline = $request->boolean('is_offline');

            $course->update([
                'title' => RoleHelper::is_super_admin() ? $request->title : $course->title,
                'code' => $request->code,
                'amount' => $request->amount,
                'hod_id' => $request->hod_id,
                'hod_number' => $request->hod_number,
                'is_active' => $request->boolean('is_active'),
                'needs_time' => $request->boolean('needs_time'),
                'is_online' => $request->boolean('is_online'),
                'is_offline' => $isOffline,
            ]);

            CourseOfflinePlaceSupport::syncForCourse(
                $course,
                $request->input('offline_place_ids', []),
                $isOffline
            );

            // For AJAX requests, return JSON response
            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Course updated successfully!',
                    'data' => $course
                ]);
            }

            return redirect()->route('admin.courses.index')->with('message_success', 'Course updated successfully!');
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
                    'message' => 'Course not found.'
                ], 404);
            }
            
            return redirect()->route('admin.courses.index')->with('message_danger', 'Course not found.');
        } catch (\Exception $e) {
            // For AJAX requests, return JSON response
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'An error occurred while updating the course. Please try again.'
                ], 500);
            }
            
            return redirect()->back()
                ->withInput()
                ->with('message_danger', 'An error occurred while updating the course. Please try again.');
        }
    }

    public function delete($id)
    {
        if (!RoleHelper::is_super_admin()) {
            return redirect()->route('dashboard')->with('message_danger', 'Access denied.');
        }

        try {
            $course = Course::findOrFail($id);
            
            // Check if course has leads
            if ($course->leads()->count() > 0) {
                return redirect()->route('admin.courses.index')->with('message_danger', 'Cannot delete course. It has assigned leads.');
            }

            $course->delete();
            return redirect()->route('admin.courses.index')->with('message_success', 'Course deleted successfully!');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->route('admin.courses.index')->with('message_danger', 'Course not found.');
        } catch (\Exception $e) {
            return redirect()->route('admin.courses.index')->with('message_danger', 'An error occurred while deleting the course. Please try again.');
        }
    }

    public function checkNeedsTime($courseId)
    {
        $course = Course::find($courseId);
        if (!$course) {
            return response()->json(['needs_time' => false], 404);
        }

        return response()->json(['needs_time' => (bool)$course->needs_time]);
    }
}