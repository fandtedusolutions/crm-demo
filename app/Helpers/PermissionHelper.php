<?php

namespace App\Helpers;

class PermissionHelper
{
    /**
     * Subject Areas, Mail templates, and Flags (admin menu items with delete actions).
     */
    public static function can_manage_subject_areas_mails_flags(): bool
    {
        return RoleHelper::is_admin_or_super_admin() || RoleHelper::is_admission_counsellor();
    }

    public static function can_delete_subject_areas_mails_flags(): bool
    {
        return self::can_manage_subject_areas_mails_flags();
    }

    /**
     * Check if user has permission for a specific page
     */
    public static function has_permission($permission = '')
    {
        // Check if super admin
        if (RoleHelper::is_super_admin()) {
            return self::has_permission_super_admin($permission);
        } elseif (RoleHelper::is_admin()) {
            return self::has_permission_admin($permission);
        } elseif (RoleHelper::is_telecaller()) {
            return self::has_permission_telecaller($permission);
        } elseif (RoleHelper::is_admission_counsellor()) {
            return self::has_permission_admission_counsellor($permission);
        } elseif (RoleHelper::is_academic_assistant()) {
            return self::has_permission_academic_assistant($permission);
        } elseif (RoleHelper::is_finance()) {
            return self::has_permission_finance($permission);
        } elseif (RoleHelper::is_post_sales()) {
            return self::has_permission_post_sales($permission);
        } elseif (RoleHelper::is_support_team()) {
            return self::has_permission_support_team($permission);
        } elseif (RoleHelper::is_general_manager()) {
            return self::has_permission_general_manager($permission);
        } elseif (RoleHelper::is_faculty()) {
            return self::has_permission_faculty($permission);
        } elseif (RoleHelper::is_mentor()) {
            return self::has_permission_mentor($permission);
        } elseif (RoleHelper::is_auditor()) {
            return self::has_permission_auditor($permission);
        } elseif (RoleHelper::is_marketing()) {
            return self::has_permission_marketing($permission);
        } elseif (RoleHelper::is_hod()) {
            return self::has_permission_hod($permission);
        } elseif (RoleHelper::is_placement_officer()) {
            return self::has_permission_placement_officer($permission);
        }

        return false;
    }

    public static function has_lead_action_permission()
    {
        if (RoleHelper::is_admin_or_super_admin()) {
            return true;
        } elseif (RoleHelper::is_academic_assistant()) {
            return false;
        } elseif (RoleHelper::is_admission_counsellor()) {
            return false;
        } elseif (RoleHelper::is_finance()) {
            return false;
        } elseif (RoleHelper::is_post_sales()) {
            return false;
        } elseif (RoleHelper::is_telecaller()) {
            return true;
        } elseif (RoleHelper::is_general_manager()) {
            return true;
        } elseif (RoleHelper::is_auditor()) {
            return false; // Auditor has view-only, no actions
        }

        return false;
    }

    /**
     * Super Admin permissions - has access to everything
     */
    public static function has_permission_super_admin($permission = '')
    {
        $permissions = [
        ];

        return ! in_array($permission, $permissions);
    }

    /**
     * Admin permissions
     */
    public static function has_permission_admin($permission = '')
    {
        $permissions = [
            'admin/settings/index',
            'admin/website/settings',
            // Advanced Reports - only lead-stage-movement is allowed
            'admin/reports/lead-efficiency',
            'admin/reports/lead-aging',
            'admin/reports/team-wise',
            'admin/reports/course-summary',
        ];

        // Admin has access to everything except the permissions listed above
        // This includes admin/marketing/d2d-form
        return ! in_array($permission, $permissions);
    }

    /**
     * Telecaller permissions
     */
    public static function has_permission_telecaller($permission = '')
    {
        $permissions = [
            'dashboard/index',
            'leads/index',
            'leads/followup',
            'leads/registration-form-submitted',
            'admin/converted-leads/index',
            'admin/reports/leads',
            'admin/payments/list',
            'profile/index',
        ];

        return in_array($permission, $permissions);
    }

    /**
     * Admission Counsellor permissions
     */
    public static function has_permission_admission_counsellor($permission = '')
    {
        $permissions = [
            'dashboard/index',
            'leads/index',
            'profile/index',
            'admin/converted-leads/index',
            'admin/online-teaching-faculties/index',
            'admin/call-analytics/index',
            'admin/notifications/index',
            'admin/reports/course-summary',
            'admin/courses/index',
            'admin/subjects/index',
            'admin/subject-areas/index',
            'admin/mails/index',
            'admin/flags/index',
            'admin/support-flags/index',
            'admin/course-flags/index',
            'admin/class-times/index',
            'admin/course-types/index',
            'admin/stream-specializations/index',
            'admin/offline-places/index',
            'admin/hod/index',
            'admin/sub-courses/index',
            'admin/course-documents/index',
            'admin/universities/index',
            'admin/university-courses/index',
            'admin/registration-links/index',
            'admin/teams/index',
            'admin/countries/index',
            'admin/boards/index',
            'admin/batches/index',
            'admin/admission-batches/index',
            'leads/registration-form-submitted',
            // User Management permissions
            'admin/teachers/index',
            'admin/academic-assistants/index',
            'admin/support-team/index',
            'admin/mentor/index',
            'admin/faculty/index',
            'admin/placement-officers/index',
            'admin/b2b-services/index',
            'admin/departments/index',
            'admin/telecallers/index',
            'admin/placement-list/index',
        ];

        return in_array($permission, $permissions);
    }

    /**
     * Academic Assistant permissions
     */
    public static function has_permission_academic_assistant($permission = '')
    {
        $permissions = [
            'dashboard/index',
            'leads/index',
            'profile/index',
            'admin/converted-leads/index',
            'admin/notifications/index',
            'admin/universities/index',
            'leads/registration-form-submitted',
            'admin/online-teaching-faculties/index',
            'admin/call-analytics/index',
            'admin/b2b-services/index',
            'admin/departments/index',
        ];

        return in_array($permission, $permissions);
    }

    /**
     * Academic Assistant permissions
     */
    public static function has_permission_finance($permission = '')
    {
        $permissions = [
            'dashboard/index',
            'leads/index',
            'profile/index',
            'admin/converted-leads/index',
            'admin/nios-converted-leads/index',
            'admin/bosse-converted-leads/index',
            'admin/ugpg-converted-leads/index',
            'admin/hotel-management-converted-leads/index',
            'admin/gmvss-converted-leads/index',
            'admin/ai-python-converted-leads/index',
            'admin/digital-marketing-converted-leads/index',
            'admin/ai-automation-converted-leads/index',
            'admin/web-development-converted-leads/index',
            'admin/vibe-coding-converted-leads/index',
            'admin/graphic-designing-converted-leads/index',
            'admin/ai-integrated-video-editing-converted-leads/index',
            'admin/ai-integrated-videography-converted-leads/index',
            'admin/ai-integrated-photography-converted-leads/index',
            'admin/eduthanzeel-converted-leads/index',
            'admin/e-school-converted-leads/index',
            'admin/payments/list',
            'admin/courses/index',
            'admin/batches/index',
            'admin/universities/index',
            'admin/university-courses/index',
        ];

        return in_array($permission, $permissions);
    }

    /**
     * Post Sales permissions
     */
    public static function has_permission_post_sales($permission = '')
    {
        $permissions = [
            'dashboard/index',
            'leads/index',
            'profile/index',
            'admin/converted-leads/index',
            'admin/post-sales-converted-leads/index',
            'admin/call-analytics/index',
            'admin/payments/list',
        ];

        return in_array($permission, $permissions);
    }

    /**
     * Support Team permissions
     */
    public static function has_permission_support_team($permission = '')
    {
        $permissions = [
            'dashboard/index',
            'leads/index',
            'profile/index',
            'admin/converted-leads/index',
            'admin/support-team/index',
            'admin/support-bosse-converted-leads/index',
            'admin/support-nios-converted-leads/index',
            'admin/support-hotel-management-converted-leads/index',
            'admin/support-gmvss-converted-leads/index',
            'admin/support-ai-python-converted-leads/index',
            'admin/support-digital-marketing-converted-leads/index',
            'admin/support-ai-automation-converted-leads/index',
            'admin/support-web-development-converted-leads/index',
            'admin/support-vibe-coding-converted-leads/index',
            'admin/support-graphic-designing-converted-leads/index',
            'admin/support-ai-integrated-video-editing-converted-leads/index',
            'admin/support-ai-integrated-videography-converted-leads/index',
            'admin/support-ai-integrated-photography-converted-leads/index',
            'admin/support-eduthanzeel-converted-leads/index',
            'admin/support-e-school-converted-leads/index',
        ];

        return in_array($permission, $permissions);
    }

    /**
     * General Manager permissions
     */
    public static function has_permission_general_manager($permission = '')
    {
        $permissions = [
            'dashboard/index',
            'leads/index',
            'leads/followup',
            'leads/registration-form-submitted',
            'leads/pullbacked',
            'admin/converted-leads/index',
            'admin/reports/leads',
            'admin/marketing/index',
            // User Management (index pages; actions are guarded in controllers)
            'admin/telecallers/index',
            'admin/post-sales/index',
            'admin/post-sales-converted-leads/index',
            'admin/marketing/d2d-form', // D2D Form access
            'admin/marketing/marketing-leads', // Marketing Leads listing
            'admin/call-analytics/index',
            'profile/index',
            'leads/pullback',
        ];

        return in_array($permission, $permissions);
    }

    /**
     * Mentor permissions
     */
    public static function has_permission_mentor($permission = '')
    {
        $permissions = [
            'dashboard/index',
            'leads/index',
            'admin/converted-leads/index',
            'profile/index',
        ];

        return in_array($permission, $permissions);
    }

    /**
     * Faculty permissions (same converted-leads access as mentor, faculty routes)
     */
    public static function has_permission_faculty($permission = '')
    {
        $permissions = [
            'dashboard/index',
            'leads/index',
            'admin/converted-leads/index',
            'profile/index',
            'admin/faculty-bosse-converted-leads/index',
            'admin/faculty-nios-converted-leads/index',
            'admin/faculty-ugpg-converted-leads/index',
            'admin/faculty-edumaster-converted-leads/index',
            'admin/faculty-eschool-converted-leads/index',
            'admin/faculty-eduthanzeel-converted-leads/index',
            'admin/gmvss-faculty-converted-leads/index',
            'admin/digital-marketing-faculty-converted-leads/index',
            'admin/data-science-faculty-converted-leads/index',
            'admin/graphic-designing-faculty-converted-leads/index',
            'admin/ai-integrated-video-editing-faculty-converted-leads/index',
            'admin/ai-integrated-videography-faculty-converted-leads/index',
            'admin/ai-integrated-photography-faculty-converted-leads/index',
            'admin/machine-learning-faculty-converted-leads/index',
            'admin/medical-coding-faculty-converted-leads/index',
            'admin/python-faculty-converted-leads/index',
            'admin/flutter-faculty-converted-leads/index',
            'admin/rpa-faculty-converted-leads/index',
            'admin/junior-vlogger-faculty-converted-leads/index',
        ];

        return in_array($permission, $permissions);
    }

    /**
     * Auditor permissions - view only access
     */
    public static function has_permission_auditor($permission = '')
    {
        $permissions = [
            'dashboard/index',
            'leads/index', // View only, no actions
            'admin/converted-leads/index', // View only, no actions
            'leads/followup', // Followups leads
            'admin/reports/leads', // Lead reports
            'admin/reports/lead-efficiency', // Advanced Reports - Source Efficiency
            'admin/reports/lead-stage-movement', // Advanced Reports - Stage Movement
            'admin/reports/lead-aging', // Advanced Reports - Lead Aging
            'admin/reports/team-wise', // Advanced Reports - Team-Wise Report
            'admin/reports/course-summary', // Advanced Reports - Course Reports
            'admin/telecaller-tracking/dashboard', // Telecaller Tracking - Dashboard
            'admin/telecaller-tasks/index', // Telecaller Tracking - Task Management
            'admin/auditors/index', // Auditor management (similar to general managers)
            'profile/index',
        ];

        return in_array($permission, $permissions);
    }

    /**
     * Marketing permissions
     */
    public static function has_permission_marketing($permission = '')
    {
        $permissions = [
            'dashboard/index',
            'admin/marketing/d2d-form', // D2D Form access
            'admin/marketing/marketing-leads', // Marketing Leads listing
            'profile/index',
        ];

        return in_array($permission, $permissions);
    }

    /**
     * HOD permissions - only main converted leads index and mentor list pages
     */
    public static function has_permission_hod($permission = '')
    {
        $permissions = [
            'dashboard/index',
            'profile/index',
            'admin/converted-leads/index',
            'admin/online-teaching-faculties/index',
            'admin/call-analytics/index',
            'admin/mentor-bosse-converted-leads/index',
            'admin/mentor-nios-converted-leads/index',
            'admin/mentor-eschool-converted-leads/index',
            'admin/mentor-eduthanzeel-converted-leads/index',
            'admin/gmvss-mentor-converted-leads/index',
            'admin/faculty-bosse-converted-leads/index',
            'admin/faculty-nios-converted-leads/index',
            'admin/faculty-eschool-converted-leads/index',
            'admin/faculty-eduthanzeel-converted-leads/index',
            'admin/gmvss-faculty-converted-leads/index',
        ];

        return in_array($permission, $permissions);
    }

    /**
     * Placement Manager permissions (role_id = 15)
     */
    public static function has_permission_placement_officer($permission = '')
    {
        $permissions = [
            'dashboard/index',
            'admin/placement-list/index',
        ];

        return in_array($permission, $permissions);
    }

    /**
     * Get app permissions for a specific role
     */
    public static function has_permission_app($role_id, $is_team_lead = 0, $is_team_manager = 0, $current_role = '')
    {
        if ($role_id == 1) { // Super Admin
            return self::get_permission_super_admin_app();
        } elseif ($role_id == 2) { // Admin
            return self::get_permission_admin_app();
        } elseif ($role_id == 3) { // Telecaller
            if ($is_team_manager == 1) {
                if ($current_role == 'telecaller') {
                    return self::get_permission_telecaller_app();
                } else {
                    return self::get_permission_team_manager_app();
                }
            } elseif ($is_team_lead == 1) {
                if ($current_role == 'telecaller') {
                    return self::get_permission_telecaller_app();
                } else {
                    return self::get_permission_team_lead_app();
                }
            } else {
                return self::get_permission_telecaller_app();
            }
        }

        return self::get_permission_telecaller_app(); // Default to telecaller permissions
    }

    /**
     * Super Admin app permissions
     */
    public static function get_permission_super_admin_app()
    {
        return [
            [
                'teams' => 1,
                'members' => 1,
                'leads' => 1,
                'follow_ups' => 1,
                'call' => 1,
                'candidate' => 1,
                'invoice' => 1,
                'enrollments' => 1,
                'admin_panel' => 1,
                'user_roles' => 1,
                'reports' => 1,
            ],
        ];
    }

    /**
     * Admin app permissions
     */
    public static function get_permission_admin_app()
    {
        return [
            [
                'teams' => 1,
                'members' => 1,
                'leads' => 1,
                'follow_ups' => 1,
                'call' => 1,
                'candidate' => 1,
                'invoice' => 0,
                'enrollments' => 1,
                'admin_panel' => 1,
                'user_roles' => 0,
                'reports' => 1,
            ],
        ];
    }

    /**
     * Team Manager app permissions
     */
    public static function get_permission_team_manager_app()
    {
        return [
            [
                'teams' => 1,
                'members' => 1,
                'leads' => 1,
                'follow_ups' => 1,
                'call' => 1,
                'candidate' => 1,
                'invoice' => 0,
                'enrollments' => 0,
                'admin_panel' => 0,
                'user_roles' => 0,
                'reports' => 1,
            ],
        ];
    }

    /**
     * Team Lead app permissions
     */
    public static function get_permission_team_lead_app()
    {
        return [
            [
                'teams' => 0,
                'members' => 0,
                'leads' => 1,
                'follow_ups' => 1,
                'call' => 1,
                'candidate' => 1,
                'invoice' => 0,
                'enrollments' => 0,
                'admin_panel' => 0,
                'user_roles' => 0,
                'reports' => 1,
            ],
        ];
    }

    /**
     * Telecaller app permissions
     */
    public static function get_permission_telecaller_app()
    {
        return [
            [
                'teams' => 0,
                'members' => 0,
                'leads' => 1,
                'follow_ups' => 1,
                'call' => 1,
                'candidate' => 1,
                'staff' => 0,
                'invoice' => 0,
                'enrollments' => 0,
                'admin_panel' => 0,
                'user_roles' => 0,
                'reports' => 0,
            ],
        ];
    }

    /**
     * Check if user can access a specific menu item
     */
    public static function can_access_menu($menu_permission)
    {
        return self::has_permission($menu_permission);
    }
}
