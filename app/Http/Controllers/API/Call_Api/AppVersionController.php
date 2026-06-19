<?php

namespace App\Http\Controllers\API\Call_Api;

use App\Http\Controllers\Controller;
use App\Services\CallAppSettingService;
use Illuminate\Http\Request;

class AppVersionController extends Controller
{
    /**
     * Check Call Tracker app version, force update flag, and download link.
     */
    public function check(Request $request)
    {
        $clientVersion = $request->input('app_version')
            ?? $request->query('app_version')
            ?? '0.0.0';

        return response()->json([
            'success' => true,
            'data' => CallAppSettingService::buildApiPayload($clientVersion),
        ]);
    }
}
