<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Helpers\RoleHelper;
use App\Services\CrmAppSettingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class CrmAppSettingController extends Controller
{
    public function index()
    {
        if (!RoleHelper::is_admin_or_super_admin()) {
            abort(403, 'Access denied.');
        }

        $settings = CrmAppSettingService::getSettings();
        $apiPreview = CrmAppSettingService::buildApiPayload('1.0.0');

        return view('admin.settings.crm-app', compact('settings', 'apiPreview'));
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
            CrmAppSettingService::VERSION_KEY,
            $request->app_version,
            'text',
            'Latest CRM mobile app version',
            'crm_app'
        );

        Setting::set(
            CrmAppSettingService::FORCE_UPDATE_KEY,
            $request->boolean('force_update'),
            'boolean',
            'Force users to update the CRM mobile app',
            'crm_app'
        );

        Setting::set(
            CrmAppSettingService::DOWNLOAD_URL_KEY,
            $request->input('download_url', ''),
            'text',
            'External CRM mobile app download URL',
            'crm_app'
        );

        if ($request->hasFile('apk_file')) {
            $existingPath = Setting::get(CrmAppSettingService::APK_PATH_KEY, '');
            if ($existingPath && Storage::disk('public')->exists($existingPath)) {
                Storage::disk('public')->delete($existingPath);
            }

            $path = $request->file('apk_file')->store('crm-app', 'public');
            Setting::set(
                CrmAppSettingService::APK_PATH_KEY,
                $path,
                'file',
                'Uploaded CRM mobile app APK file path',
                'crm_app'
            );
        }

        $settings = CrmAppSettingService::getSettings();

        return response()->json([
            'success' => true,
            'message' => 'CRM App settings updated successfully.',
            'data' => $settings,
        ]);
    }

    public function removeApk()
    {
        if (!RoleHelper::is_admin_or_super_admin()) {
            return response()->json(['success' => false, 'message' => 'Access denied.'], 403);
        }

        $existingPath = Setting::get(CrmAppSettingService::APK_PATH_KEY, '');
        if ($existingPath && Storage::disk('public')->exists($existingPath)) {
            Storage::disk('public')->delete($existingPath);
        }

        Setting::set(
            CrmAppSettingService::APK_PATH_KEY,
            '',
            'file',
            'Uploaded CRM mobile app APK file path',
            'crm_app'
        );

        return response()->json([
            'success' => true,
            'message' => 'Uploaded APK removed successfully.',
        ]);
    }
}
