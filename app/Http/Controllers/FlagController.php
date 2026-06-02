<?php

namespace App\Http\Controllers;

use App\Helpers\PermissionHelper;
use App\Models\Flag;
use Illuminate\Http\Request;

class FlagController extends Controller
{
    public function listActive()
    {
        $flags = Flag::orderBy('title')->get(['id', 'title', 'description', 'color']);

        return response()->json($flags);
    }

    private function canManage(): bool
    {
        return PermissionHelper::can_manage_subject_areas_mails_flags();
    }

    private function baseRules(): array
    {
        return [
            'color' => ['required', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'title' => 'required|string|max:255',
            'description' => 'required|string',
        ];
    }

    public function index()
    {
        if (! $this->canManage()) {
            return redirect()->route('dashboard')->with('message_danger', 'Access denied.');
        }

        $flags = Flag::orderBy('title')->get();

        return view('admin.flags.index', compact('flags'));
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

        $flag = Flag::findOrFail($id);
        $flag->update([
            'color' => $request->color,
            'title' => $request->title,
            'description' => $request->description,
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Flag updated successfully.',
                'data' => $flag,
            ]);
        }

        return redirect()->route('admin.flags.index')->with('message_success', 'Flag updated successfully!');
    }

    public function ajax_add()
    {
        if (! $this->canManage()) {
            return redirect()->route('dashboard')->with('message_danger', 'Access denied.');
        }

        return view('admin.flags.add');
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

        $flag = Flag::where('color', $validated['color'])
            ->where('title', trim((string) $validated['title']))
            ->where('description', trim((string) $validated['description']))
            ->first();
        $wasCreated = false;

        if (! $flag) {
            $flag = Flag::create([
                'color' => $validated['color'],
                'title' => trim((string) $validated['title']),
                'description' => trim((string) $validated['description']),
            ]);
            $wasCreated = true;
        }

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $wasCreated ? 'Flag created successfully!' : 'Same flag already exists.',
                'data' => $flag,
            ]);
        }

        return redirect()->route('admin.flags.index')->with(
            'message_success',
            $wasCreated ? 'Flag created successfully!' : 'Same flag already exists.'
        );
    }

    public function ajax_edit($id)
    {
        if (! $this->canManage()) {
            return redirect()->route('dashboard')->with('message_danger', 'Access denied.');
        }

        $edit_data = Flag::findOrFail($id);

        return view('admin.flags.edit', compact('edit_data'));
    }

    public function delete($id)
    {
        if (! $this->canManage()) {
            return redirect()->route('dashboard')->with('message_danger', 'Access denied.');
        }

        $flag = Flag::findOrFail($id);
        $flag->delete();

        return redirect()->route('admin.flags.index')->with('message_success', 'Flag deleted successfully!');
    }
}
