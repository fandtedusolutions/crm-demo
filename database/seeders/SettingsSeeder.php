<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Setting;

class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            // Contact Settings
            [
                'key' => 'contact_phone',
                'value' => '+1-234-567-8900',
                'type' => 'text',
                'description' => 'Contact phone number',
                'group' => 'contact',
                'is_public' => true,
            ],
            [
                'key' => 'contact_email',
                'value' => 'info@basecrm.com',
                'type' => 'text',
                'description' => 'Contact email address',
                'group' => 'contact',
                'is_public' => true,
            ],
            [
                'key' => 'contact_address',
                'value' => '123 Business Street, City, State 12345',
                'type' => 'text',
                'description' => 'Contact address',
                'group' => 'contact',
                'is_public' => true,
            ],
            
            // Email Settings
            [
                'key' => 'email_from_name',
                'value' => 'Support Team',
                'type' => 'text',
                'description' => 'Email sender name',
                'group' => 'email',
                'is_public' => false,
            ],
            [
                'key' => 'email_from_address',
                'value' => 'support@skill-park.com',
                'type' => 'text',
                'description' => 'Email sender address',
                'group' => 'email',
                'is_public' => false,
            ],
            
            // Social Media Settings
            [
                'key' => 'facebook_url',
                'value' => '',
                'type' => 'text',
                'description' => 'Facebook page URL',
                'group' => 'social',
                'is_public' => true,
            ],
            [
                'key' => 'twitter_url',
                'value' => '',
                'type' => 'text',
                'description' => 'Twitter profile URL',
                'group' => 'social',
                'is_public' => true,
            ],
            [
                'key' => 'linkedin_url',
                'value' => '',
                'type' => 'text',
                'description' => 'LinkedIn profile URL',
                'group' => 'social',
                'is_public' => true,
            ],
            
            // System Settings
            [
                'key' => 'timezone',
                'value' => 'UTC',
                'type' => 'text',
                'description' => 'System timezone',
                'group' => 'system',
                'is_public' => false,
            ],
            [
                'key' => 'date_format',
                'value' => 'Y-m-d',
                'type' => 'text',
                'description' => 'Date format',
                'group' => 'system',
                'is_public' => false,
            ],
            [
                'key' => 'time_format',
                'value' => 'H:i:s',
                'type' => 'text',
                'description' => 'Time format',
                'group' => 'system',
                'is_public' => false,
            ],
            
            // Theme Settings
            [
                'key' => 'sidebar_color',
                'value' => '#db0000',
                'type' => 'color',
                'description' => 'Sidebar background color',
                'group' => 'theme',
                'is_public' => false,
            ],
            [
                'key' => 'topbar_color',
                'value' => '#ffffff',
                'type' => 'color',
                'description' => 'Topbar background color',
                'group' => 'theme',
                'is_public' => false,
            ],
            
            // Site Settings
            [
                'key' => 'site_name',
                'value' => 'Skillpark',
                'type' => 'text',
                'description' => 'Website name displayed in title and header',
                'group' => 'site',
                'is_public' => true,
            ],
            [
                'key' => 'site_description',
                'value' => 'CRM Management System',
                'type' => 'text',
                'description' => 'Website description for SEO and meta tags',
                'group' => 'site',
                'is_public' => true,
            ],
            [
                'key' => 'site_logo',
                'value' => 'storage/logo.png',
                'type' => 'file',
                'description' => 'Website logo file path',
                'group' => 'site',
                'is_public' => true,
            ],
            [
                'key' => 'site_favicon',
                'value' => 'storage/favicon.ico',
                'type' => 'file',
                'description' => 'Website favicon file path',
                'group' => 'site',
                'is_public' => true,
            ],
            [
                'key' => 'bg_image',
                'value' => 'storage/auth-bg.jpg',
                'type' => 'file',
                'description' => 'Login page background image',
                'group' => 'site',
                'is_public' => true,
            ],
            
            // Login Theme Settings
            [
                'key' => 'login_primary_color',
                'value' => '#667eea',
                'type' => 'color',
                'description' => 'Primary color for login form',
                'group' => 'theme',
                'is_public' => false,
            ],
            [
                'key' => 'login_secondary_color',
                'value' => '#764ba2',
                'type' => 'color',
                'description' => 'Secondary color for login form',
                'group' => 'theme',
                'is_public' => false,
            ],
            [
                'key' => 'app_maintenance',
                'value' => '0',
                'type' => 'boolean',
                'description' => 'Mobile app maintenance mode (0 = off, 1 = on)',
                'group' => 'app',
                'is_public' => true,
            ],
            [
                'key' => 'login_form_style',
                'value' => 'modern',
                'type' => 'text',
                'description' => 'Login form style',
                'group' => 'theme',
                'is_public' => false,
            ],

            // Mobile app settings
            [
                'key' => 'app_maintenance',
                'value' => '0',
                'type' => 'number',
                'description' => 'Mobile app maintenance mode (0 = off, 1 = on)',
                'group' => 'app',
                'is_public' => true,
            ],

            // Call Tracker app settings (version, force update, download)
            [
                'key' => 'call_app_version',
                'value' => '1.0.0',
                'type' => 'text',
                'description' => 'Latest Call Tracker app version',
                'group' => 'call_app',
                'is_public' => false,
            ],
            [
                'key' => 'call_app_force_update',
                'value' => '0',
                'type' => 'boolean',
                'description' => 'Force users to update the Call Tracker app',
                'group' => 'call_app',
                'is_public' => false,
            ],
            [
                'key' => 'call_app_download_url',
                'value' => '',
                'type' => 'text',
                'description' => 'External Call Tracker app download URL',
                'group' => 'call_app',
                'is_public' => false,
            ],
            [
                'key' => 'call_app_apk_path',
                'value' => '',
                'type' => 'file',
                'description' => 'Uploaded Call Tracker APK file path',
                'group' => 'call_app',
                'is_public' => false,
            ],

            // CRM mobile app settings (version, force update, download)
            [
                'key' => 'crm_app_version',
                'value' => '1.0.0',
                'type' => 'text',
                'description' => 'Latest CRM mobile app version',
                'group' => 'crm_app',
                'is_public' => false,
            ],
            [
                'key' => 'crm_app_force_update',
                'value' => '0',
                'type' => 'boolean',
                'description' => 'Force users to update the CRM mobile app',
                'group' => 'crm_app',
                'is_public' => false,
            ],
            [
                'key' => 'crm_app_download_url',
                'value' => '',
                'type' => 'text',
                'description' => 'External CRM mobile app download URL',
                'group' => 'crm_app',
                'is_public' => false,
            ],
            [
                'key' => 'crm_app_apk_path',
                'value' => '',
                'type' => 'file',
                'description' => 'Uploaded CRM mobile app APK file path',
                'group' => 'crm_app',
                'is_public' => false,
            ],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }
}