<?php

namespace App\Http\Controllers;

use App\Helpers\RoleHelper;
use App\Models\Course;
use App\Models\StreamSpecialization;
use Illuminate\Http\Request;

class StreamSpecializationController extends Controller
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

        $query = StreamSpecialization::with('course')->orderBy('created_at', 'desc');

        if ($selectedCourseId) {
            $query->where('course_id', $selectedCourseId);
        }

        $streamSpecializations = $query->get();

        return view('admin.stream-specializations.index', compact('streamSpecializations', 'courses', 'selectedCourseId'));
    }

    public function ajax_add()
    {
        if ($denied = $this->authorizeAccess()) {
            return $denied;
        }

        $courses = Course::where('is_active', true)->orderBy('title')->get();

        return view('admin.stream-specializations.add', compact('courses'));
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

            $streamSpecialization = StreamSpecialization::create([
                'title' => $request->title,
                'course_id' => $request->course_id,
                'is_active' => $request->boolean('is_active'),
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Stream / specialization created successfully!',
                    'data' => $streamSpecialization,
                ]);
            }

            return redirect()->route('admin.stream-specializations.index')->with('message_success', 'Stream / specialization created successfully!');
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
                    'message' => 'An error occurred while creating the stream / specialization. Please try again.',
                ], 500);
            }

            return redirect()->back()
                ->withInput()
                ->with('message_danger', 'An error occurred while creating the stream / specialization. Please try again.');
        }
    }

    public function ajax_edit($id)
    {
        if ($denied = $this->authorizeAccess()) {
            return $denied;
        }

        $edit_data = StreamSpecialization::findOrFail($id);
        $courses = Course::where('is_active', true)->orderBy('title')->get();

        return view('admin.stream-specializations.edit', compact('edit_data', 'courses'));
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

            $streamSpecialization = StreamSpecialization::findOrFail($id);
            $streamSpecialization->update([
                'title' => $request->title,
                'course_id' => $request->course_id,
                'is_active' => $request->boolean('is_active'),
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Stream / specialization updated successfully!',
                    'data' => $streamSpecialization,
                ]);
            }

            return redirect()->route('admin.stream-specializations.index')->with('message_success', 'Stream / specialization updated successfully!');
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
                    'message' => 'Stream / specialization not found.',
                ], 404);
            }

            return redirect()->route('admin.stream-specializations.index')->with('message_danger', 'Stream / specialization not found.');
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'An error occurred while updating the stream / specialization. Please try again.',
                ], 500);
            }

            return redirect()->back()
                ->withInput()
                ->with('message_danger', 'An error occurred while updating the stream / specialization. Please try again.');
        }
    }

    public function delete($id)
    {
        if ($denied = $this->authorizeAccess()) {
            return $denied;
        }

        try {
            $streamSpecialization = StreamSpecialization::findOrFail($id);

            if ($streamSpecialization->leadDetails()->exists()) {
                $message = 'Cannot delete stream / specialization. It is used in registration records.';

                if (request()->ajax()) {
                    return response()->json(['success' => false, 'message' => $message], 422);
                }

                return redirect()->route('admin.stream-specializations.index')->with('message_danger', $message);
            }

            $streamSpecialization->delete();

            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Stream / specialization deleted successfully!',
                ]);
            }

            return redirect()->route('admin.stream-specializations.index')->with('message_success', 'Stream / specialization deleted successfully!');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->route('admin.stream-specializations.index')->with('message_danger', 'Stream / specialization not found.');
        } catch (\Exception $e) {
            return redirect()->route('admin.stream-specializations.index')->with('message_danger', 'An error occurred while deleting the stream / specialization. Please try again.');
        }
    }

    public function getByCourse($courseId)
    {
        $streamSpecializations = StreamSpecialization::where('course_id', $courseId)
            ->where('is_active', true)
            ->orderBy('title')
            ->get(['id', 'title', 'course_id', 'is_active']);

        return response()->json([
            'success' => true,
            'stream_specializations' => $streamSpecializations,
        ]);
    }
}
