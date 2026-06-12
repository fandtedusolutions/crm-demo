<?php

namespace App\Helpers;

use App\Models\User;
use App\Models\UserRole;

class RoleHelper
{
    /**
     * Check if user is logged in
     */
    public static function is_logged_in()
    {
        return AuthHelper::isLoggedIn();
    }

    /**
     * Check if current user is Super Admin
     */
    public static function is_super_admin()
    {
        if (!self::is_logged_in()) {
            return false;
        }

        $user = AuthHelper::getCurrentUser();
        if (!$user) {
            return false;
        }

        $role = UserRole::find($user->role_id);
        return $role && $role->title === 'Super Admin';
    }

    /**
     * Check if current user is Admin
     */
    public static function is_admin()
    {
        if (!self::is_logged_in()) {
            return false;
        }

        $user = AuthHelper::getCurrentUser();
        if (!$user) {
            return false;
        }

        $role = UserRole::find($user->role_id);
        return $role && $role->title === 'Admin';
    }

    /**
     * Check if current user is Telecaller
     */
    public static function is_telecaller()
    {
        if (!self::is_logged_in()) {
            return false;
        }

        $user = AuthHelper::getCurrentUser();
        if (!$user) {
            return false;
        }

        return $user->role_id == 3;
    }

    /**
     * Check if current user is Team Lead
     */
    public static function is_team_lead()
    {
        if (!self::is_logged_in()) {
            return false;
        }

        $user = AuthHelper::getCurrentUser();
        if (!$user) {
            return false;
        }

        return $user->is_team_lead == 1;
    }

    /**
     * Check if current user is Senior Manager (must be telecaller and is_senior_manager = 1)
     */
    public static function is_senior_manager()
    {
        if (!self::is_logged_in()) {
            return false;
        }

        $user = AuthHelper::getCurrentUser();
        if (!$user) {
            return false;
        }

        // Check if user is telecaller (role_id == 3) and is_senior_manager == 1
        return $user->role_id == 3 && $user->is_senior_manager == 1;
    }

    /**
     * Check if current user is Admission Counsellor
     */
    public static function is_admission_counsellor()
    {
        if (!self::is_logged_in()) {
            return false;
        }

        $user = AuthHelper::getCurrentUser();
        if (!$user) {
            return false;
        }

        return $user->role_id == 4;
    }

    /**
     * Check if current user is Academic Assistant
     */
    public static function is_academic_assistant()
    {
        if (!self::is_logged_in()) {
            return false;
        }

        $user = AuthHelper::getCurrentUser();
        if (!$user) {
            return false;
        }

        return $user->role_id == 5;
    }

    /**
     * Check if current user is Finance
     */
    public static function is_finance()
    {
        if (!self::is_logged_in()) {
            return false;
        }

        $user = AuthHelper::getCurrentUser();
        if (!$user) {
            return false;
        }

        return $user->role_id == 6;
    }

    /**
     * Check if current user is Mentor (role_id = 9).
     */
    public static function is_mentor()
    {
        if (!self::is_logged_in()) {
            return false;
        }

        $user = AuthHelper::getCurrentUser();
        if (!$user) {
            return false;
        }

        return (int) $user->role_id === 9;
    }

    /**
     * Check if current user is Faculty (role_id = 16).
     */
    public static function is_faculty()
    {
        if (!self::is_logged_in()) {
            return false;
        }

        $user = AuthHelper::getCurrentUser();
        if (!$user) {
            return false;
        }

        return (int) $user->role_id === 16;
    }

    /**
     * Check if current user is Mentor Head (mentor with is_head = 1)
     */
    public static function is_mentor_head()
    {
        if (!self::is_logged_in()) {
            return false;
        }

        $user = AuthHelper::getCurrentUser();
        if (!$user) {
            return false;
        }

        // Must be mentor (role_id == 9) and is_head == 1
        return $user->role_id == 9 && $user->is_head == 1;
    }

    /**
     * Check if current user is Post Sales
     */
    public static function is_post_sales()
    {
        if (!self::is_logged_in()) {
            return false;
        }

        $user = AuthHelper::getCurrentUser();
        if (!$user) {
            return false;
        }

        return $user->role_id == 7;
    }

    /**
     * Check if current user is Post Sales Head
     */
    public static function is_post_sales_head()
    {
        if (!self::is_logged_in()) {
            return false;
        }

        $user = AuthHelper::getCurrentUser();
        if (!$user) {
            return false;
        }

        // Must be post sales (role_id == 7) and is_head == 1
        return $user->role_id == 7 && $user->is_head == 1;
    }

    /**
     * Check if current user is Support Team
     */
    public static function is_support_team()
    {
        if (!self::is_logged_in()) {
            return false;
        }

        $user = AuthHelper::getCurrentUser();
        if (!$user) {
            return false;
        }

        return $user->role_id == 8;
    }


    /**
     * Check if current user is General Manager
     */
    public static function is_general_manager()
    {
        if (!self::is_logged_in()) {
            return false;
        }

        $user = AuthHelper::getCurrentUser();
        if (!$user) {
            return false;
        }

        return $user->role_id == 11;
    }

    /**
     * Check if current user is Auditor
     */
    public static function is_auditor()
    {
        if (!self::is_logged_in()) {
            return false;
        }

        $user = AuthHelper::getCurrentUser();
        if (!$user) {
            return false;
        }

        return $user->role_id == 12;
    }

    /**
     * Check if current user is Marketing
     */
    public static function is_marketing()
    {
        if (!self::is_logged_in()) {
            return false;
        }

        $user = AuthHelper::getCurrentUser();
        if (!$user) {
            return false;
        }

        return $user->role_id == 13;
    }

    /**
     * Check if current user is HOD
     */
    public static function is_hod()
    {
        if (!self::is_logged_in()) {
            return false;
        }

        $user = AuthHelper::getCurrentUser();
        if (!$user) {
            return false;
        }

        return $user->role_id == 14;
    }

    /**
     * Check if current user is Placement Manager (role_id = 15).
     */
    public static function is_placement_officer()
    {
        if (!self::is_logged_in()) {
            return false;
        }

        $user = AuthHelper::getCurrentUser();
        if (!$user) {
            return false;
        }

        return (int) $user->role_id === 15;
    }


    /**
     * Check if current user has admin or super admin role
     */
    public static function is_admin_or_super_admin()
    {
        return self::is_admin() || self::is_super_admin();
    }

    /**
     * Get current user's role title
     */
    public static function get_current_user_role()
    {
        if (!self::is_logged_in()) {
            return null;
        }

        $user = AuthHelper::getCurrentUser();
        if (!$user) {
            return null;
        }

        $role = UserRole::find($user->role_id);
        return $role ? $role->title : null;
    }

    /**
     * Check if user has specific role
     */
    public static function has_role($roleTitle)
    {
        if (!self::is_logged_in()) {
            return false;
        }

        $user = AuthHelper::getCurrentUser();
        if (!$user) {
            return false;
        }

        $role = UserRole::find($user->role_id);
        return $role && $role->title === $roleTitle;
    }

    /**
     * Check if current user is Academic Counselor
     */
    public static function is_academic_counselor()
    {
        if (!self::is_logged_in()) {
            return false;
        }

        $user = AuthHelper::getCurrentUser();
        if (!$user) {
            return false;
        }

        // Assuming Academic Counselor refers to Admission Counsellor (4) or Academic Assistant (5)
        // Adjust role IDs as per actual requirement if different
        return in_array($user->role_id, [4, 5]);
    }
}
