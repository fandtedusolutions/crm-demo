<?php

use App\Models\Setting;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        $settings = [
            ['crm_app_version', '1.0.0', 'text', 'Latest CRM mobile app version', 'crm_app'],
            ['crm_app_force_update', '0', 'boolean', 'Force users to update the CRM mobile app', 'crm_app'],
            ['crm_app_download_url', '', 'text', 'External CRM mobile app download URL', 'crm_app'],
            ['crm_app_apk_path', '', 'file', 'Uploaded CRM mobile app APK file path', 'crm_app'],
        ];

        foreach ($settings as [$key, $value, $type, $description, $group]) {
            if (!Setting::where('key', $key)->exists()) {
                Setting::set($key, $type === 'boolean' ? (bool) $value : $value, $type, $description, $group);
            }
        }
    }

    public function down(): void
    {
        Setting::whereIn('key', [
            'crm_app_version',
            'crm_app_force_update',
            'crm_app_download_url',
            'crm_app_apk_path',
        ])->delete();
    }
};
