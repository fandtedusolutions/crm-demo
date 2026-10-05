<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserRole;
use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Helpers\RoleHelper;
use App\Helpers\AuthHelper;

class PostSalesController extends Controller
{
    public function index()
    {
        if (!(RoleHelper::is_admin_or_super_admin() || RoleHelper::is_general_manager())) {
            return redirect()->route('dashboard')->with('message_danger', 'Access denied.');
        }

        $postSalesUsers = User::where('role_id', 7)->with(['role'])->get();
        $roles = UserRole::all();
        
        return view('admin.post-sales.index', compact('postSalesUsers', 'roles'));
    }

    public function store(Request $request)
    {
        if (!(RoleHelper::is_admin_or_super_admin() || RoleHelper::is_general_manager())) {
            return response()->json(['error' => 'Access denied.'], 403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'code' => 'nullable|string|max:10',
            'ext_no' => 'nullable|string|max:20',
            'password' => 'required|string|min:8',
            'is_head' => 'nullable|boolean',
        ]);

        // Filter only the fields we need
        $data = $request->only(['name', 'email', 'phone', 'code', 'ext_no', 'password', 'is_head']);
        $data['password'] = Hash::make($data['password']);
        $data['role_id'] = 7; // Static role for Post-sales
        $data['is_active'] = 1; // Default to active

        $postSalesUser = User::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Post-sales user created successfully.',
            'data' => $postSalesUser->load('role')
        ]);
    }

    public function show(User $postSalesUser)
    {
        if (!(RoleHelper::is_admin_or_super_admin() || RoleHelper::is_general_manager())) {
            return response()->json(['error' => 'Access denied.'], 403);
        }

        return response()->json($postSalesUser->load('role'));
    }

    public function destroy(User $postSalesUser)
    {
        if (!RoleHelper::is_admin_or_super_admin()) {
            return response()->json(['error' => 'Access denied.'], 403);
        }

        // Check if post-sales user has leads
        if ($postSalesUser->leads()->count() > 0) {
            return response()->json([
                'error' => 'Cannot delete post-sales user. They have assigned leads.'
            ], 422);
        }

        $postSalesUser->delete();

        return response()->json([
            'success' => true,
            'message' => 'Post-sales user deleted successfully.'
        ]);
    }

    public function ajax_add(Request $request)
    {
        if (!(RoleHelper::is_admin_or_super_admin() || RoleHelper::is_general_manager())) {
            return redirect()->route('dashboard')->with('message_danger', 'Access denied.');
        }

        $country_codes = get_country_code();
        
        return view('admin.post-sales.add', compact('country_codes'));
    }

    public function submit(Request $request)
    {
        if (!(RoleHelper::is_admin_or_super_admin() || RoleHelper::is_general_manager())) {
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
            'is_head' => 'nullable|boolean',
            'is_postsale_gm' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            $firstError = $validator->errors()->first();
            return redirect()->back()->with('message_danger', $firstError)->withInput();
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'code' => $request->code,
            'ext_no' => $request->ext_no,
            'password' => Hash::make($request->password),
            'role_id' => 7, // Static role for Post-sales
            'is_active' => $request->has('is_active') ? 1 : 0,
            'is_head' => $request->has('is_head') ? 1 : 0,
            'is_postsale_gm' => $request->has('is_postsale_gm') ? 1 : 0,
        ]);

        return redirect()->route('admin.post-sales.index')->with('message_success', 'Post-sales user created successfully!');
    }

    public function ajax_edit($id)
    {
        if (!(RoleHelper::is_admin_or_super_admin() || RoleHelper::is_general_manager())) {
            return redirect()->route('dashboard')->with('message_danger', 'Access denied.');
        }

        $postSalesUser = User::where('id', $id)->where('role_id', 7)->firstOrFail();
        $country_codes = get_country_code();
        
        return view('admin.post-sales.edit', compact('postSalesUser', 'country_codes'));
    }

    public function update(Request $request, $id)
    {
        if (!(RoleHelper::is_admin_or_super_admin() || RoleHelper::is_general_manager())) {
            return redirect()->route('dashboard')->with('message_danger', 'Access denied.');
        }

        $postSalesUser = User::where('id', $id)->where('role_id', 7)->firstOrFail();

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'phone' => 'nullable|string|max:20',
            'code' => 'nullable|string|max:10',
            'ext_no' => 'nullable|string|max:20',
            'is_active' => 'nullable|boolean',
            'is_head' => 'nullable|boolean',
            'is_postsale_gm' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            $firstError = $validator->errors()->first();
            return redirect()->back()->with('message_danger', $firstError)->withInput();
        }

        $postSalesUser->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'code' => $request->code,
            'ext_no' => $request->ext_no,
            'is_active' => $request->has('is_active') ? 1 : 0,
            'is_head' => $request->has('is_head') ? 1 : 0,
            'is_postsale_gm' => $request->has('is_postsale_gm') ? 1 : 0,
        ]);

        return redirect()->route('admin.post-sales.index')->with('message_success', 'Post-sales user updated successfully!');
    }

    public function delete($id)
    {
        if (!RoleHelper::is_admin_or_super_admin()) {
            return redirect()->route('dashboard')->with('message_danger', 'Access denied.');
        }

        $postSalesUser = User::where('id', $id)->where('role_id', 7)->firstOrFail();

        // Check if post-sales user has leads
        if ($postSalesUser->leads()->count() > 0) {
            return redirect()->route('admin.post-sales.index')->with('message_danger', 'Cannot delete post-sales user. They have assigned leads.');
        }

        $postSalesUser->delete();

        return redirect()->route('admin.post-sales.index')->with('message_success', 'Post-sales user deleted successfully!');
    }

    public function changePassword($id)
    {
        if (!RoleHelper::is_admin_or_super_admin()) {
            return redirect()->route('dashboard')->with('message_danger', 'Access denied.');
        }

        $postSalesUser = User::where('id', $id)->where('role_id', 7)->firstOrFail();
        
        return view('admin.post-sales.change-password', compact('postSalesUser'));
    }

    public function updatePassword(Request $request, $id)
    {
        if (!RoleHelper::is_admin_or_super_admin()) {
            return redirect()->route('dashboard')->with('message_danger', 'Access denied.');
        }

        $validator = Validator::make($request->all(), [
            'password' => 'required|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            $firstError = $validator->errors()->first();
            return redirect()->back()->with('message_danger', $firstError);
        }

        $postSalesUser = User::where('id', $id)->where('role_id', 7)->firstOrFail();
        
        $postSalesUser->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('admin.post-sales.index')->with('message_success', 'Password updated successfully!');
    }
}
