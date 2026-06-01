<?php

namespace App\Http\Controllers;

use App\Helpers\PermissionHelper;
use App\Models\SubjectArea;
use Illuminate\Http\Request;

class SubjectAreaController extends Controller
{
    public function listActive()
    {
        $subjectAreas = SubjectArea::active()->orderBy('title')->get(['id', 'title']);

        return response()->json($subjectAreas);
    }

    private function canManage(): bool
    {
        return PermissionHelper::can_manage_subject_areas_mails_flags();
    }

    public function index()
    {
        if (! $this->canManage()) {
            return redirect()->route('dashboard')->with('message_danger', 'Access denied.');
        }

        $subjectAreas = SubjectArea::orderBy('title')->get();

        return view('admin.subject-areas.index', compact('subjectAreas'));
    }

    public function store(Request $request)
    {
        if (! $this->canManage()) {
            return response()->json(['error' => 'Access denied.'], 403);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'is_active' => 'boolean',
        ]);

        $subjectArea = SubjectArea::create([
            'title' => $request->title,
            'is_active' => $request->boolean('is_active'),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Subject Area created successfully.',
            'data' => $subjectArea,
        ]);
    }

    public function show(SubjectArea $subjectArea)
    {
        if (! $this->canManage()) {
            return response()->json(['error' => 'Access denied.'], 403);
        }

        return response()->json($subjectArea);
    }

    public function update(Request $request, SubjectArea $subjectArea)
    {
        if (! $this->canManage()) {
            if ($request->ajax()) {
                return response()->json(['error' => 'Access denied.'], 403);
            }

            return redirect()->route('dashboard')->with('message_danger', 'Access denied.');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'is_active' => 'boolean',
        ]);

        $subjectArea->update([
            'title' => $request->title,
            'is_active' => $request->boolean('is_active'),
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Subject Area updated successfully.',
                'data' => $subjectArea,
            ]);
        }

        return redirect()->route('admin.subject-areas.index')->with('message_success', 'Subject Area updated successfully!');
    }

    public function destroy(SubjectArea $subjectArea)
    {
        if (! $this->canManage()) {
            return response()->json(['error' => 'Access denied.'], 403);
        }

        $subjectArea->delete();

        return response()->json([
            'success' => true,
            'message' => 'Subject Area deleted successfully.',
        ]);
    }

    public function ajax_add()
    {
        if (! $this->canManage()) {
            return redirect()->route('dashboard')->with('message_danger', 'Access denied.');
        }

        return view('admin.subject-areas.add');
    }

    public function submit(Request $request)
    {
        if (! $this->canManage()) {
            if ($request->ajax()) {
                return response()->json(['error' => 'Access denied.'], 403);
            }

            return redirect()->route('dashboard')->with('message_danger', 'Access denied.');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'is_active' => 'boolean',
        ]);

        $subjectArea = SubjectArea::create([
            'title' => $request->title,
            'is_active' => $request->boolean('is_active'),
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Subject Area created successfully!',
                'data' => $subjectArea,
            ]);
        }

        return redirect()->route('admin.subject-areas.index')->with('message_success', 'Subject Area created successfully!');
    }

    public function ajax_edit($id)
    {
        if (! $this->canManage()) {
            return redirect()->route('dashboard')->with('message_danger', 'Access denied.');
        }

        $edit_data = SubjectArea::findOrFail($id);

        return view('admin.subject-areas.edit', compact('edit_data'));
    }

    public function delete($id)
    {
        if (! $this->canManage()) {
            return redirect()->route('dashboard')->with('message_danger', 'Access denied.');
        }

        $subjectArea = SubjectArea::findOrFail($id);
        $subjectArea->delete();

        return redirect()->route('admin.subject-areas.index')->with('message_success', 'Subject Area deleted successfully!');
    }
}
