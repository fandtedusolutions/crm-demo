<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Session;
use App\Models\User;

class AuthHelper
{
    /**
     * Get session value
     */
    public static function getSessionValue($sessionKey)
    {
        return Session::get($sessionKey);
    }

    /**
     * Get user ID from session
     */
    public static function getUserId()
    {
        return self::getSessionValue('user_id');
    }

    /**
     * Get role ID from session
     */
    public static function getRoleId()
    {
        return self::getSessionValue('role_id');
    }

    /**
     * Check if user is team lead
     */
    public static function isTeamLead()
    {
        return self::getSessionValue('is_team_lead');
    }

    /**
     * Check if user is team manager
     */
    public static function isTeamManager()
    {
        return self::getSessionValue('is_team_manager');
    }

    /**
     * Get role title from session
     */
    public static function getRoleTitle()
    {
        return self::getSessionValue('role_title');
    }

    /**
     * Get user designation from session
     */
    public static function getUserDesignation()
    {
        return self::getSessionValue('user_designation');
    }

    /**
     * Get user name from session
     */
    public static function getUserName()
    {
        return self::getSessionValue('user_name');
    }

    /**
     * Check if user is logged in
     */
    public static function isLoggedIn()
    {
        $isLoggedIn = self::getSessionValue('is_logged_in');
        $isUserId = self::getUserId() > 0;
        return $isUserId && $isLoggedIn;
    }

    /**
     * Check if user is admin
     */
    public static function isSuperAdmin()
    {
        return self::getRoleId() == 1;
    }
    /**
     * Check if user is admin
     */
    public static function isAdmin()
    {
        return self::getRoleId() == 2;
    }

    /**
     * Check if user is telecaller
     */
    public static function isTelecaller()
    {
        return self::getRoleId() == 3;
    }

    /**
     * Get team member IDs for a team
     */
    public static function getTeamMemberIds($teamId)
    {
        if (!$teamId) {
            return [];
        }

        $query = User::where('team_id', $teamId)
            ->where('role_id', 3)
            ->whereNull('deleted_at');

        PostSaleLeadHelper::excludeHiddenTelecallersFromUserQuery($query);

        return $query->pluck('id')->toArray();
    }

    /**
     * Get current authenticated user ID
     */
    public static function getCurrentUserId()
    {
        return self::getUserId();
    }

    /**
     * Get current authenticated user
     */
    public static function getCurrentUser()
    {
        $userId = self::getUserId();
        if (!$userId) {
            return null;
        }
        
        return User::find($userId);
    }

    /**
     * Get current user role ID
     */
    public static function getCurrentUserRole()
    {
        return self::getRoleId();
    }

    /**
     * Get current user team ID
     */
    public static function getCurrentUserTeam()
    {
        $user = self::getCurrentUser();
        return $user ? $user->team_id : null;
    }
}
