<?php

if (! function_exists('has_permission')) {
    /**
     * Check if user has permission for a specific action
     */
    function has_permission($permission = '')
    {
        return \App\Helpers\PermissionHelper::has_permission($permission);
    }
}

if (! function_exists('can_access_menu')) {
    /**
     * Check if user can access a specific menu item
     */
    function can_access_menu($menu_permission)
    {
        return \App\Helpers\PermissionHelper::can_access_menu($menu_permission);
    }
}

if (! function_exists('can_delete_subject_areas_mails_flags')) {
    /**
     * Delete permission for Subject Areas, Mail, and Flags admin pages only.
     */
    function can_delete_subject_areas_mails_flags(): bool
    {
        return \App\Helpers\PermissionHelper::can_delete_subject_areas_mails_flags();
    }
}

if (! function_exists('is_super_admin')) {
    /**
     * Check if current user is Super Admin
     */
    function is_super_admin()
    {
        return \App\Helpers\RoleHelper::is_super_admin();
    }
}

if (! function_exists('is_admin')) {
    /**
     * Check if current user is Admin
     */
    function is_admin()
    {
        return \App\Helpers\RoleHelper::is_admin();
    }
}

if (! function_exists('is_telecaller')) {
    /**
     * Check if current user is Telecaller
     */
    function is_telecaller()
    {
        return \App\Helpers\RoleHelper::is_telecaller();
    }
}

if (! function_exists('is_senior_manager')) {
    /**
     * Check if current user is Senior Manager (must be telecaller and is_senior_manager = 1)
     */
    function is_senior_manager()
    {
        return \App\Helpers\RoleHelper::is_senior_manager();
    }
}

if (! function_exists('is_admission_counsellor')) {
    /**
     * Check if current user is Admission Counsellor
     */
    function is_admission_counsellor()
    {
        return \App\Helpers\RoleHelper::is_admission_counsellor();
    }
}

if (! function_exists('is_academic_assistant')) {
    /**
     * Check if current user is Academic Assistant
     */
    function is_academic_assistant()
    {
        return \App\Helpers\RoleHelper::is_academic_assistant();
    }
}

if (! function_exists('is_finance')) {
    /**
     * Check if current user is Finance
     */
    function is_finance()
    {
        return \App\Helpers\RoleHelper::is_finance();
    }
}

if (! function_exists('is_mentor')) {
    /**
     * Check if current user is Mentor or Faculty
     */
    function is_mentor()
    {
        return \App\Helpers\RoleHelper::is_mentor();
    }
}

if (! function_exists('is_faculty')) {
    /**
     * Check if current user is Faculty
     */
    function is_faculty()
    {
        return \App\Helpers\RoleHelper::is_faculty();
    }
}

if (! function_exists('is_general_manager')) {
    /**
     * Check if current user is General Manager
     */
    function is_general_manager()
    {
        return \App\Helpers\RoleHelper::is_general_manager();
    }
}

if (! function_exists('is_auditor')) {
    /**
     * Check if current user is Auditor
     */
    function is_auditor()
    {
        return \App\Helpers\RoleHelper::is_auditor();
    }
}

if (! function_exists('is_marketing')) {
    /**
     * Check if current user is Marketing
     */
    function is_marketing()
    {
        return \App\Helpers\RoleHelper::is_marketing();
    }
}

if (! function_exists('is_logged_in')) {
    /**
     * Check if user is logged in
     */
    function is_logged_in()
    {
        return \App\Helpers\RoleHelper::is_logged_in();
    }
}

if (! function_exists('get_country_code')) {
    /**
     * Get country codes array
     */
    function get_country_code()
    {
        return \App\Helpers\CountriesHelper::get_country_code();
    }
}

if (! function_exists('get_phone_code')) {
    /**
     * Parse phone number to extract country code and phone number
     */
    function get_phone_code($phone_number)
    {
        return \App\Helpers\PhoneNumberHelper::get_phone_code($phone_number);
    }
}
