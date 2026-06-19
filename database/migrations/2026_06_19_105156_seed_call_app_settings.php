<?php

use App\Models\Setting;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        $settings = [
            ['call_app_version', '1.0.0', 'text', 'Latest Call Tracker app version', 'call_app'],
            ['call_app_force_update', '0', 'boolean', 'Force users to update the Call Tracker app', 'call_app'],
            ['call_app_download_url', '', 'text', 'External Call Tracker app download URL', 'call_app'],
            ['call_app_apk_path', '', 'file', 'Uploaded Call Tracker APK file path', 'call_app'],
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
            'call_app_version',
            'call_app_force_update',
            'call_app_download_url',
            'call_app_apk_path',
        ])->delete();
    }
};
