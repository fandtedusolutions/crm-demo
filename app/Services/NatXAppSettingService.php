<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Storage;

class NatXAppSettingService
{
    public const VERSION_KEY = 'natx_app_version';
    public const FORCE_UPDATE_KEY = 'natx_app_force_update';
    public const DOWNLOAD_URL_KEY = 'natx_app_download_url';
    public const APK_PATH_KEY = 'natx_app_apk_path';

    public static function getSettings(): array
    {
        $apkPath = Setting::get(self::APK_PATH_KEY, '');

        return [
            'app_version' => Setting::get(self::VERSION_KEY, '1.0.0'),
            'force_update' => (bool) Setting::get(self::FORCE_UPDATE_KEY, false),
            'download_url' => Setting::get(self::DOWNLOAD_URL_KEY, ''),
            'apk_path' => $apkPath,
            'apk_url' => $apkPath ? Storage::disk('public')->url($apkPath) : null,
            'resolved_download_url' => self::resolveDownloadUrl(),
        ];
    }

    public static function resolveDownloadUrl(): ?string
    {
        $apkPath = Setting::get(self::APK_PATH_KEY, '');
        if ($apkPath && Storage::disk('public')->exists($apkPath)) {
            return Storage::disk('public')->url($apkPath);
        }

        $externalUrl = trim((string) Setting::get(self::DOWNLOAD_URL_KEY, ''));

        return $externalUrl !== '' ? $externalUrl : null;
    }

    public static function buildApiPayload(?string $clientVersion = null): array
    {
        $settings = self::getSettings();
        $latestVersion = $settings['app_version'] ?: '1.0.0';
        $clientVersion = $clientVersion ?: '0.0.0';
        $updateRequired = version_compare($clientVersion, $latestVersion, '<');

        return [
            'latest_version' => $latestVersion,
            'app_version' => $clientVersion,
            'update_required' => $updateRequired,
            'force_update' => $settings['force_update'],
            'download_url' => $settings['resolved_download_url'],
        ];
    }
}
