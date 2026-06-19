<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\CrmAppSettingService;
use Illuminate\Http\Request;

class AppVersionController extends Controller
{
    /**
     * Check CRM mobile app version, force update flag, and download link.
     */
    public function check(Request $request)
    {
        $clientVersion = $request->input('app_version')
            ?? $request->query('app_version')
            ?? '0.0.0';

        return response()->json([
            'success' => true,
            'data' => CrmAppSettingService::buildApiPayload($clientVersion),
        ]);
    }
}
