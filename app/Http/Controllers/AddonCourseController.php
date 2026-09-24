<?php

namespace App\Http\Controllers;

use App\Helpers\RoleHelper;
use App\Models\AddonCourse;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class AddonCourseController extends Controller
{
    public function index()
    {
        if (!$this->canManage()) {
            return redirect()->route('dashboard')->with('message_danger', 'Access denied.');
        }

        $addonCourses = AddonCourse::with(['course', 'addonCourse'])
            ->orderBy('course_id')
            ->orderBy('addon_course_id')
            ->get();

        return view('admin.addon-courses.index', compact('addonCourses'));
    }

    public function ajax_add()
    {
        if (!$this->canManage()) {
            return redirect()->route('dashboard')->with('message_danger', 'Access denied.');
        }

        $courses = $this->courseOptions();

        return view('admin.addon-courses.create', compact('courses'));
    }

    public function submit(Request $request)
    {
        if (!$this->canManage()) {
            return redirect()->route('dashboard')->with('message_danger', 'Access denied.');
        }

        $validator = $this->validator($request);

        if ($validator->fails()) {
            return redirect()->route('admin.addon-courses.index')->with('message_danger', $validator->errors()->first());
        }

        AddonCourse::create([
            'course_id' => $request->course_id,
            'addon_course_id' => $request->addon_course_id,
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()->route('admin.addon-courses.index')->with('message_success', 'Addon course added successfully.');
    }

    public function ajax_edit(string $id)
    {
        if (!$this->canManage()) {
            return redirect()->route('dashboard')->with('message_danger', 'Access denied.');
        }

        $addonCourse = AddonCourse::findOrFail($id);
        $courses = $this->courseOptions($addonCourse);

        return view('admin.addon-courses.edit', compact('addonCourse', 'courses'));
    }

    public function updateForm(Request $request, string $id)
    {
        if (!$this->canManage()) {
            return redirect()->route('dashboard')->with('message_danger', 'Access denied.');
        }

        $addonCourse = AddonCourse::findOrFail($id);
        $validator = $this->validator($request, $addonCourse->id);

        if ($validator->fails()) {
            return redirect()->route('admin.addon-courses.index')->with('message_danger', $validator->errors()->first());
        }

        $addonCourse->update([
            'course_id' => $request->course_id,
            'addon_course_id' => $request->addon_course_id,
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()->route('admin.addon-courses.index')->with('message_success', 'Addon course updated successfully.');
    }

    public function destroy(string $id)
    {
        if (!$this->canManage()) {
            return response()->json(['error' => 'Access denied.'], 403);
        }

        $addonCourse = AddonCourse::findOrFail($id);
        $addonCourse->delete();

        return response()->json([
            'success' => true,
            'message' => 'Addon course deleted successfully.',
        ]);
    }

    private function canManage(): bool
    {
        return RoleHelper::is_admin_or_super_admin() || RoleHelper::is_admission_counsellor();
    }

    private function courseOptions(?AddonCourse $addonCourse = null)
    {
        $selectedIds = array_filter([
            $addonCourse->course_id ?? null,
            $addonCourse->addon_course_id ?? null,
        ]);

        return Course::query()
            ->where(function ($query) use ($selectedIds) {
                $query->where('is_active', true);
                if ($selectedIds) {
                    $query->orWhereIn('id', $selectedIds);
                }
            })
            ->orderBy('title')
            ->get(['id', 'title']);
    }

    private function validator(Request $request, ?int $ignoreId = null)
    {
        $request->merge([
            'is_active' => $request->boolean('is_active'),
        ]);

        $uniqueAddon = Rule::unique('addon_courses', 'addon_course_id')
            ->where(fn ($query) => $query->where('course_id', $request->course_id));

        if ($ignoreId) {
            $uniqueAddon->ignore($ignoreId);
        }

        return Validator::make($request->all(), [
            'course_id' => ['required', 'exists:courses,id', 'different:addon_course_id'],
            'addon_course_id' => ['required', 'exists:courses,id', 'different:course_id', $uniqueAddon],
            'is_active' => ['boolean'],
        ], [
            'course_id.required' => 'Select the course that needs an addon.',
            'course_id.different' => 'A course cannot be added as its own addon.',
            'addon_course_id.required' => 'Select the addon course.',
            'addon_course_id.different' => 'A course cannot be added as its own addon.',
            'addon_course_id.unique' => 'This addon course is already added under the selected course.',
        ]);
    }
}
