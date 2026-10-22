<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Models\User;
use App\Models\UserRole;
use App\Models\Setting;

class AuthController extends Controller
{
    /**
     * Show the login form.
     */
    public function index()
    {
        // Check if user is already logged in using our custom AuthHelper
        if (\App\Helpers\AuthHelper::isLoggedIn()) {
            return redirect()->route('dashboard');
        }
        
        // Get site settings for dynamic logo and site name
        $siteSettings = [
            'site_name' => Setting::get('site_name', config('app.name', 'Base CRM')),
            'site_description' => Setting::get('site_description', 'CRM Management System'),
            'site_logo' => Setting::get('site_logo', 'assets/mantis/images/logo-dark.svg'),
            'site_favicon' => Setting::get('site_favicon', 'assets/mantis/images/favicon.svg'),
            'bg_image' => Setting::get('bg_image', 'assets/mantis/images/auth-bg.jpg'),
            'login_primary_color' => Setting::get('login_primary_color', '#667eea'),
            'login_secondary_color' => Setting::get('login_secondary_color', '#764ba2'),
            'login_form_style' => Setting::get('login_form_style', 'modern'),
        ];
        
        return view('auth.login', compact('siteSettings'));
    }

    /**
     * Handle login request.
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $email = $request->email;
        $password = $request->password;
        
        $loginResult = User::login($email, $password);

        if ($loginResult['status'] == 1) {
            $user = $loginResult['user'];

            // Log in the user via Laravel's Auth guard
            \Illuminate\Support\Facades\Auth::login($user);

            $userRole = UserRole::find($user->role_id);
            
            // Set session data
            Session::put([
                'user_id' => $user->id,
                'is_team_lead' => $user->is_team_lead,
                'is_team_manager' => $user->is_team_manager,
                'role_id' => $user->role_id,
                'role_title' => $userRole ? $userRole->title : '',
                'user_designation' => $user->designation,
                'user_name' => $user->name,
                'user_email' => $user->email,
                'is_logged_in' => true,
                'logged_in_at' => time(),
            ]);

            // Ensure the sessions database table has user_id updated immediately for current session
            $sessionId = $request->session()->getId();
            if ($sessionId) {
                \Illuminate\Support\Facades\DB::table(config('session.table', 'sessions'))
                    ->where('id', $sessionId)
                    ->update(['user_id' => $user->id]);
            }

            return redirect()->route('dashboard')
                ->with('message_success', "Welcome back! <b>{$user->name}</b>");
        } else {
            return back()->withErrors(['error' => $loginResult['message']]);
        }
    }

    /**
     * Logout the user.
     */
    public function logout(Request $request)
    {
        $sessionId = $request->session()->getId();

        \Illuminate\Support\Facades\Auth::logout();

        Session::flush();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        if ($sessionId) {
            \Illuminate\Support\Facades\DB::table(config('session.table', 'sessions'))
                ->where('id', $sessionId)
                ->delete();
        }
        
        return redirect()->route('login')
            ->with('message', 'Session expired, Login Again!');
    }
}
