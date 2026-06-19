<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Helpers\RoleHelper;
use App\Services\CallAppSettingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class CallAppSettingController extends Controller
{
    public function index()
    {
        if (!RoleHelper::is_admin_or_super_admin()) {
            abort(403, 'Access denied.');
        }

        $settings = CallAppSettingService::getSettings();
        $apiPreview = CallAppSettingService::buildApiPayload('1.0.0');

        return view('admin.settings.call-app', compact('settings', 'apiPreview'));
    }

    public function update(Request $request)
    {
        if (!RoleHelper::is_admin_or_super_admin()) {
            return response()->json(['success' => false, 'message' => 'Access denied.'], 403);
        }

        $request->merge([
            'download_url' => $request->input('download_url') ?: null,
        ]);

        $validator = Validator::make($request->all(), [
            'app_version' => 'required|string|max:20',
            'force_update' => 'nullable|boolean',
            'download_url' => 'nullable|url|max:500',
            'apk_file' => 'nullable|file|max:102400',
        ]);

        $validator->after(function ($validator) use ($request) {
            $file = $request->file('apk_file');
            if (!$file) {
                return;
            }

            $extension = strtolower($file->getClientOriginalExtension());
            if ($extension !== 'apk') {
                $validator->errors()->add('apk_file', 'The APK file must have a .apk extension.');
            }
        });

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        Setting::set(
            CallAppSettingService::VERSION_KEY,
            $request->app_version,
            'text',
            'Latest Call Tracker app version',
            'call_app'
        );

        Setting::set(
            CallAppSettingService::FORCE_UPDATE_KEY,
            $request->boolean('force_update'),
            'boolean',
            'Force users to update the Call Tracker app',
            'call_app'
        );

        Setting::set(
            CallAppSettingService::DOWNLOAD_URL_KEY,
            $request->input('download_url', ''),
            'text',
            'External Call Tracker app download URL',
            'call_app'
        );

        if ($request->hasFile('apk_file')) {
            $existingPath = Setting::get(CallAppSettingService::APK_PATH_KEY, '');
            if ($existingPath && Storage::disk('public')->exists($existingPath)) {
                Storage::disk('public')->delete($existingPath);
            }

            $path = $request->file('apk_file')->store('call-app', 'public');
            Setting::set(
                CallAppSettingService::APK_PATH_KEY,
                $path,
                'file',
                'Uploaded Call Tracker APK file path',
                'call_app'
            );
        }

        $settings = CallAppSettingService::getSettings();

        return response()->json([
            'success' => true,
            'message' => 'Call App settings updated successfully.',
            'data' => $settings,
        ]);
    }

    public function removeApk()
    {
        if (!RoleHelper::is_admin_or_super_admin()) {
            return response()->json(['success' => false, 'message' => 'Access denied.'], 403);
        }

        $existingPath = Setting::get(CallAppSettingService::APK_PATH_KEY, '');
        if ($existingPath && Storage::disk('public')->exists($existingPath)) {
            Storage::disk('public')->delete($existingPath);
        }

        Setting::set(
            CallAppSettingService::APK_PATH_KEY,
            '',
            'file',
            'Uploaded Call Tracker APK file path',
            'call_app'
        );

        return response()->json([
            'success' => true,
            'message' => 'Uploaded APK removed successfully.',
        ]);
    }
}
