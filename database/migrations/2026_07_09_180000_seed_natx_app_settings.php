<?php

use App\Models\Setting;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        $settings = [
            ['natx_app_version', '1.0.0', 'text', 'Latest NatX app version', 'natx_app'],
            ['natx_app_force_update', '0', 'boolean', 'Force users to update the NatX app', 'natx_app'],
            ['natx_app_download_url', '', 'text', 'External NatX app download URL', 'natx_app'],
            ['natx_app_apk_path', '', 'file', 'Uploaded NatX APK file path', 'natx_app'],
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
            'natx_app_version',
            'natx_app_force_update',
            'natx_app_download_url',
            'natx_app_apk_path',
        ])->delete();
    }
};
