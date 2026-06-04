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

class TelecallerController extends Controller
{
    public function index(Request $request)
    {
        if (!(RoleHelper::is_admin_or_super_admin() || RoleHelper::is_general_manager() || RoleHelper::is_admission_counsellor())) {
            return redirect()->route('dashboard')->with('message_danger', 'Access denied.');
        }

        $search = trim((string) $request->input('search', ''));
        $selectedTeamId = $request->filled('team_id') ? (int) $request->team_id : null;

        $query = User::where('role_id', 3)
            ->with(['role', 'team'])
            ->orderBy('name');

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%')
                    ->orWhere('phone', 'like', '%' . $search . '%');
            });
        }

        if ($selectedTeamId) {
            $query->where('team_id', $selectedTeamId);
        }

        $telecallers = $query->get();
        $teams = Team::orderBy('name')->get();

        $hasActiveFilters = $search !== '' || $selectedTeamId;

        return view('admin.telecallers.index', compact(
            'telecallers',
            'teams',
            'search',
            'selectedTeamId',
            'hasActiveFilters'
        ));
    }

    public function store(Request $request)
    {
        if (!(RoleHelper::is_admin_or_super_admin() || RoleHelper::is_general_manager() || RoleHelper::is_admission_counsellor())) {
            return response()->json(['error' => 'Access denied.'], 403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'code' => 'nullable|string|max:10',
            'password' => 'required|string|min:6',
            'team_id' => 'nullable|exists:teams,id',
        ]);

        // Filter only the fields we need
        $data = $request->only(['name', 'email', 'phone', 'code', 'password', 'team_id']);
        $data['password'] = Hash::make($data['password']);
        $data['role_id'] = 3; // Static role for Telecaller

        $telecaller = User::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Telecaller created successfully.',
            'data' => $telecaller->load('role', 'team')
        ]);
    }

    public function show(User $telecaller)
    {
        if (!(RoleHelper::is_admin_or_super_admin() || RoleHelper::is_general_manager() || RoleHelper::is_admission_counsellor())) {
            return response()->json(['error' => 'Access denied.'], 403);
        }

        return response()->json($telecaller->load('role', 'team'));
    }


    public function destroy(User $telecaller)
    {
        if (!(RoleHelper::is_admin_or_super_admin() || RoleHelper::is_general_manager() || RoleHelper::is_admission_counsellor())) {
            return response()->json(['error' => 'Access denied.'], 403);
        }

        // Check if telecaller has leads
        if ($telecaller->leads()->count() > 0) {
            return response()->json([
                'error' => 'Cannot delete telecaller. They have assigned leads.'
            ], 422);
        }

        $telecaller->delete();

        return response()->json([
            'success' => true,
            'message' => 'Telecaller deleted successfully.'
        ]);
    }


    public function ajax_add(Request $request)
    {
        if (!(RoleHelper::is_admin_or_super_admin() || RoleHelper::is_general_manager() || RoleHelper::is_admission_counsellor())) {
            return redirect()->route('dashboard')->with('message_danger', 'Access denied.');
        }

        $teams = Team::all();
        $country_codes = get_country_code();
        $selectedTeamId = $request->get('team_id'); // Get team_id from query parameter
        
        return view('admin.telecallers.add', compact('teams', 'country_codes', 'selectedTeamId'));
    }

    public function submit(Request $request)
    {
        if (!(RoleHelper::is_admin_or_super_admin() || RoleHelper::is_general_manager() || RoleHelper::is_admission_counsellor())) {
            return redirect()->route('dashboard')->with('message_danger', 'Access denied.');
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'code' => 'nullable|string|max:10',
            'ext_no' => 'nullable|string|max:20',
            'password' => 'required|string|min:6',
            'team_id' => 'nullable|exists:teams,id',
            'is_team_lead' => 'nullable|boolean',
            'is_senior_manager' => 'nullable|boolean',
            'is_b2b' => 'nullable|boolean',
            'joining_date' => 'nullable|date',
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
            'role_id' => 3, // Static role for Telecaller
            'team_id' => $request->team_id,
            'is_team_lead' => $request->has('is_team_lead') ? 1 : 0,
            'is_senior_manager' => $request->has('is_senior_manager') ? 1 : 0,
            'is_b2b' => $request->has('is_b2b') ? 1 : 0,
            'joining_date' => $request->joining_date,
        ]);

        // If user is marked as team lead and has a team, set them as team lead
        if ($request->has('is_team_lead') && $request->team_id) {
            // First, remove any existing team lead from this team
            $existingTeamLead = Team::where('id', $request->team_id)->first();
            if ($existingTeamLead && $existingTeamLead->team_lead_id) {
                User::where('id', $existingTeamLead->team_lead_id)->update(['is_team_lead' => 0]);
            }
            
            // Set the new team lead
            Team::where('id', $request->team_id)->update(['team_lead_id' => $user->id]);
        }

        return redirect()->route('admin.telecallers.index')->with('message_success', 'Telecaller created successfully!');
    }

    public function ajax_edit($id)
    {
        if (!(RoleHelper::is_admin_or_super_admin() || RoleHelper::is_general_manager() || RoleHelper::is_admission_counsellor())) {
            return redirect()->route('dashboard')->with('message_danger', 'Access denied.');
        }

        $edit_data = User::findOrFail($id);
        $teams = Team::all();
        $country_codes = get_country_code();
        return view('admin.telecallers.edit', compact('edit_data', 'teams', 'country_codes'));
    }

    public function update(Request $request, $id)
    {
        if (!(RoleHelper::is_admin_or_super_admin() || RoleHelper::is_general_manager() || RoleHelper::is_admission_counsellor())) {
            return redirect()->route('dashboard')->with('message_danger', 'Access denied.');
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'phone' => 'required|string|max:20',
            'code' => 'required|string|max:10',
            'ext_no' => 'nullable|string|max:20',
            'team_id' => 'nullable|exists:teams,id',
            'is_team_lead' => 'nullable|boolean',
            'is_senior_manager' => 'nullable|boolean',
            'is_b2b' => 'nullable|boolean',
            'joining_date' => 'required|date',
        ]);

        if ($validator->fails()) {
            $firstError = $validator->errors()->first();
            return redirect()->back()->with('message_danger', $firstError)->withInput();
        }

        $telecaller = User::findOrFail($id);
        
        // Filter only the fields we need
        $updateData = $request->only(['name', 'email', 'phone', 'code', 'ext_no', 'team_id', 'joining_date']);
        $updateData['is_team_lead'] = $request->has('is_team_lead') ? 1 : 0;
        $updateData['is_senior_manager'] = $request->has('is_senior_manager') ? 1 : 0;
        $updateData['is_b2b'] = $request->has('is_b2b') ? 1 : 0;

        $telecaller->update($updateData);

        // Handle team lead assignment
        if ($request->has('is_team_lead') && $request->team_id) {
            // First, remove any existing team lead from this team
            $existingTeamLead = Team::where('id', $request->team_id)->first();
            if ($existingTeamLead && $existingTeamLead->team_lead_id && $existingTeamLead->team_lead_id != $telecaller->id) {
                User::where('id', $existingTeamLead->team_lead_id)->update(['is_team_lead' => 0]);
            }
            
            // Set this user as team lead for the team
            Team::where('id', $request->team_id)->update(['team_lead_id' => $telecaller->id]);
        } elseif (!$request->has('is_team_lead')) {
            // If user is no longer team lead, remove them as team lead from any team
            Team::where('team_lead_id', $telecaller->id)->update(['team_lead_id' => null]);
        }

        return redirect()->route('admin.telecallers.index')->with('message_success', 'Telecaller updated successfully!');
    }

    public function delete($id)
    {
        if (!(RoleHelper::is_admin_or_super_admin() || RoleHelper::is_general_manager() || RoleHelper::is_admission_counsellor())) {
            return redirect()->route('dashboard')->with('message_danger', 'Access denied.');
        }

        $telecaller = User::findOrFail($id);
        
        // Check if telecaller has leads
        if ($telecaller->leads()->count() > 0) {
            return redirect()->route('admin.telecallers.index')->with('message_danger', 'Cannot delete telecaller. They have assigned leads.');
        }

        $telecaller->delete();
        return redirect()->route('admin.telecallers.index')->with('message_success', 'Telecaller deleted successfully!');
    }

    public function changePassword(Request $request, $id)
    {
        if (!(RoleHelper::is_admin_or_super_admin() || RoleHelper::is_general_manager() || RoleHelper::is_admission_counsellor())) {
            return redirect()->route('dashboard')->with('message_danger', 'Access denied.');
        }

        $edit_data = User::findOrFail($id);
        return view('admin.telecallers.change-password', compact('edit_data'));
    }

    public function updatePassword(Request $request, $id)
    {
        if (!(RoleHelper::is_admin_or_super_admin() || RoleHelper::is_general_manager() || RoleHelper::is_admission_counsellor())) {
            return redirect()->route('dashboard')->with('message_danger', 'Access denied.');
        }

        $validator = Validator::make($request->all(), [
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            $firstError = $validator->errors()->first();
            return redirect()->back()->with('message_danger', $firstError)->withInput();
        }

        $telecaller = User::findOrFail($id);
        $telecaller->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('admin.telecallers.index')->with('message_success', 'Password changed successfully!');
    }
}