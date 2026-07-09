<?php

namespace App\Http\Controllers\API\NatX_Api;

use App\Http\Controllers\Controller;
use App\Services\NatXAppSettingService;
use Illuminate\Http\Request;

class AppVersionController extends Controller
{
    /**
     * Check NatX app version, force update flag, and download link.
     */
    public function check(Request $request)
    {
        $clientVersion = $request->input('app_version')
            ?? $request->query('app_version')
            ?? '0.0.0';

        return response()->json([
            'success' => true,
            'data' => NatXAppSettingService::buildApiPayload($clientVersion),
        ]);
    }
}
