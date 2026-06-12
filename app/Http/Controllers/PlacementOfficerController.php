<?php

namespace App\Http\Controllers;

use App\Helpers\RoleHelper;
use App\Models\User;
use App\Models\UserRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class PlacementOfficerController extends Controller
{
    public function index()
    {
        if (!RoleHelper::is_admin_or_super_admin()) {
            return redirect()->route('dashboard')->with('message_danger', 'Access denied.');
        }

        $placementOfficers = User::where('role_id', 15)->with(['role'])->get();
        $roles = UserRole::all();

        return view('admin.placement-officers.index', compact('placementOfficers', 'roles'));
    }

    public function store(Request $request)
    {
        if (!RoleHelper::is_admin_or_super_admin()) {
            return response()->json(['error' => 'Access denied.'], 403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'code' => 'nullable|string|max:10',
            'ext_no' => 'nullable|string|max:20',
            'password' => 'required|string|min:8',
        ]);

        $data = $request->only(['name', 'email', 'phone', 'code', 'ext_no', 'password']);
        $data['password'] = Hash::make($data['password']);
        $data['role_id'] = 15;
        $data['is_active'] = 1;

        $placementOfficer = User::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Placement Officer created successfully.',
            'data' => $placementOfficer->load('role'),
        ]);
    }

    public function show(User $placementOfficer)
    {
        if (!RoleHelper::is_admin_or_super_admin()) {
            return response()->json(['error' => 'Access denied.'], 403);
        }

        return response()->json($placementOfficer->load('role'));
    }

    public function destroy(User $placementOfficer)
    {
        if (!RoleHelper::is_admin_or_super_admin()) {
            return response()->json(['error' => 'Access denied.'], 403);
        }

        if ($placementOfficer->leads()->count() > 0) {
            return response()->json([
                'error' => 'Cannot delete placement officer. They have assigned leads.',
            ], 422);
        }

        $placementOfficer->delete();

        return response()->json([
            'success' => true,
            'message' => 'Placement Officer deleted successfully.',
        ]);
    }

    public function ajax_add(Request $request)
    {
        if (!RoleHelper::is_admin_or_super_admin()) {
            return redirect()->route('dashboard')->with('message_danger', 'Access denied.');
        }

        $country_codes = get_country_code();

        return view('admin.placement-officers.add', compact('country_codes'));
    }

    public function submit(Request $request)
    {
        if (!RoleHelper::is_admin_or_super_admin()) {
            return redirect()->route('dashboard')->with('message_danger', 'Access denied.');
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'code' => 'nullable|string|max:10',
            'ext_no' => 'nullable|string|max:20',
            'password' => 'required|string|min:6',
            'is_active' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            $firstError = $validator->errors()->first();
            return redirect()->back()->with('message_danger', $firstError)->withInput();
        }

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'code' => $request->code,
            'ext_no' => $request->ext_no,
            'password' => Hash::make($request->password),
            'role_id' => 15,
            'is_active' => $request->has('is_active') ? 1 : 0,
        ]);

        return redirect()->route('admin.placement-officers.index')->with('message_success', 'Placement Officer created successfully!');
    }

    public function ajax_edit($id)
    {
        if (!RoleHelper::is_admin_or_super_admin()) {
            return redirect()->route('dashboard')->with('message_danger', 'Access denied.');
        }

        $placementOfficer = User::where('id', $id)->where('role_id', 15)->firstOrFail();
        $country_codes = get_country_code();

        return view('admin.placement-officers.edit', compact('placementOfficer', 'country_codes'));
    }

    public function update(Request $request, $id)
    {
        if (!RoleHelper::is_admin_or_super_admin()) {
            return redirect()->route('dashboard')->with('message_danger', 'Access denied.');
        }

        $placementOfficer = User::where('id', $id)->where('role_id', 15)->firstOrFail();

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'phone' => 'nullable|string|max:20',
            'code' => 'nullable|string|max:10',
            'ext_no' => 'nullable|string|max:20',
            'is_active' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            $firstError = $validator->errors()->first();
            return redirect()->back()->with('message_danger', $firstError)->withInput();
        }

        $placementOfficer->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'code' => $request->code,
            'ext_no' => $request->ext_no,
            'is_active' => $request->has('is_active') ? 1 : 0,
        ]);

        return redirect()->route('admin.placement-officers.index')->with('message_success', 'Placement Officer updated successfully!');
    }

    public function delete($id)
    {
        if (!RoleHelper::is_admin_or_super_admin()) {
            return redirect()->route('dashboard')->with('message_danger', 'Access denied.');
        }

        $placementOfficer = User::where('id', $id)->where('role_id', 15)->firstOrFail();

        if ($placementOfficer->leads()->count() > 0) {
            return redirect()->route('admin.placement-officers.index')->with('message_danger', 'Cannot delete placement officer. They have assigned leads.');
        }

        $placementOfficer->delete();

        return redirect()->route('admin.placement-officers.index')->with('message_success', 'Placement Officer deleted successfully!');
    }

    public function changePassword($id)
    {
        if (!RoleHelper::is_admin_or_super_admin()) {
            return redirect()->route('dashboard')->with('message_danger', 'Access denied.');
        }

        $placementOfficer = User::where('id', $id)->where('role_id', 15)->firstOrFail();

        return view('admin.placement-officers.change-password', compact('placementOfficer'));
    }

    public function updatePassword(Request $request, $id)
    {
        if (!RoleHelper::is_admin_or_super_admin()) {
            return redirect()->route('dashboard')->with('message_danger', 'Access denied.');
        }

        $placementOfficer = User::where('id', $id)->where('role_id', 15)->firstOrFail();

        $validator = Validator::make($request->all(), [
            'password' => 'required|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            $firstError = $validator->errors()->first();
            return redirect()->back()->with('message_danger', $firstError)->withInput();
        }

        $placementOfficer->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('admin.placement-officers.index')->with('message_success', 'Password updated successfully!');
    }
}
