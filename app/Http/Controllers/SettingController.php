<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Helpers\PublicStorageHelper;
use App\Models\Setting;

class SettingController extends Controller
{
    public function index()
    {
        // Get all settings in one query to reduce database calls
        $settings = Setting::whereIn('key', [
            'site_name', 'site_description', 'site_logo', 'site_favicon', 'bg_image'
        ])->pluck('value', 'key');

        $siteSettings = [
            'site_name' => $settings->get('site_name', 'Base CRM'),
            'site_description' => $settings->get('site_description', 'CRM Management System'),
            'site_logo' => $settings->get('site_logo', 'storage/logo.png'),
            'site_favicon' => $settings->get('site_favicon', 'storage/favicon.ico'),
            'bg_image' => $settings->get('bg_image', 'assets/mantis/images/auth-bg.jpg'),
        ];
        
        return view('admin.settings.index', compact('siteSettings'));
    }

    public function updateLogo(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'logo' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Please correct the errors below.',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            PublicStorageHelper::storeFile($request->file('logo'), 'logo.png');
            Setting::set('site_logo', 'storage/logo.png', 'file', 'Website logo file path', 'site');

            return response()->json([
                'success' => true,
                'message' => 'Logo updated successfully!',
                'logo_url' => PublicStorageHelper::publicUrl('storage/logo.png')
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while updating the logo. Please try again.'
            ], 500);
        }
    }

    public function updateFavicon(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'favicon' => 'required|image|mimes:ico,png,jpg,jpeg|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Please correct the errors below.',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            PublicStorageHelper::storeFile($request->file('favicon'), 'favicon.ico');
            Setting::set('site_favicon', 'storage/favicon.ico', 'file', 'Website favicon file path', 'site');

            return response()->json([
                'success' => true,
                'message' => 'Favicon updated successfully!',
                'favicon_url' => PublicStorageHelper::publicUrl('storage/favicon.ico')
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while updating the favicon. Please try again.'
            ], 500);
        }
    }

    public function updateSiteSettings(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'site_name' => 'required|string|max:255',
            'site_description' => 'required|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Please correct the errors below.',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Update site name
            Setting::set('site_name', $request->site_name, 'text', 'Website name displayed in title and header', 'site');
            
            // Update site description
            Setting::set('site_description', $request->site_description, 'text', 'Website description for SEO and meta tags', 'site');

            return response()->json([
                'success' => true,
                'message' => 'Site settings updated successfully!'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while updating site settings. Please try again.'
            ], 500);
        }
    }


    public function updateBackgroundImage(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'bg_image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048', // 2MB max
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Please correct the errors below.',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $oldBg = Setting::get('bg_image');
            if ($oldBg && str_starts_with($oldBg, 'storage/')) {
                PublicStorageHelper::deleteFile(substr($oldBg, strlen('storage/')));
            }

            $extension = strtolower($request->file('bg_image')->getClientOriginalExtension() ?: 'jpg');
            $filename = 'auth-bg.' . $extension;

            PublicStorageHelper::storeFile($request->file('bg_image'), $filename);
            Setting::set('bg_image', 'storage/' . $filename, 'file', 'Login page background image', 'site');

            return response()->json([
                'success' => true,
                'message' => 'Background image updated successfully!',
                'bg_image_url' => PublicStorageHelper::publicUrl('storage/' . $filename)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while updating the background image. Please try again.'
            ], 500);
        }
    }

    public function removeBackgroundImage(Request $request)
    {
        try {
            $currentBg = Setting::get('bg_image');
            if ($currentBg && str_starts_with($currentBg, 'storage/')) {
                PublicStorageHelper::deleteFile(substr($currentBg, strlen('storage/')));
            }

            Setting::set('bg_image', 'assets/mantis/images/auth-bg.jpg', 'file', 'Login page background image', 'site');

            return response()->json([
                'success' => true,
                'message' => 'Background image removed successfully!'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while removing the background image. Please try again.'
            ], 500);
        }
    }

}
