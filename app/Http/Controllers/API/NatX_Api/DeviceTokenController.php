<?php

namespace App\Http\Controllers\API\NatX_Api;

use App\Http\Controllers\Controller;
use App\Models\NatXDeviceToken;
use App\Services\FcmPushService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DeviceTokenController extends Controller
{
    /**
     * Register or update the mentor's FCM device token.
     */
    public function store(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 401);
        }

        $validator = Validator::make($request->all(), [
            'fcm_token' => 'required|string|max:512',
            'device_type' => 'required|in:android,ios,web',
            'device_name' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        $token = NatXDeviceToken::updateOrCreate(
            ['fcm_token' => $request->fcm_token],
            [
                'user_id' => $user->id,
                'device_type' => $request->device_type,
                'device_name' => $request->device_name,
                'last_used_at' => now(),
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Device token saved successfully',
            'data' => [
                'id' => $token->id,
                'user_id' => $token->user_id,
                'fcm_token' => $token->fcm_token,
                'device_type' => $token->device_type,
                'device_name' => $token->device_name,
                'last_used_at' => optional($token->last_used_at)->toDateTimeString(),
            ],
        ], 200);
    }

    /**
     * Remove a device token (e.g. on logout or token refresh).
     */
    public function destroy(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 401);
        }

        $validator = Validator::make($request->all(), [
            'fcm_token' => 'required|string|max:512',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        $deleted = NatXDeviceToken::forUser($user->id)
            ->where('fcm_token', $request->fcm_token)
            ->delete();

        return response()->json([
            'success' => true,
            'message' => $deleted
                ? 'Device token removed successfully'
                : 'Device token not found',
            'deleted' => (bool) $deleted,
        ], 200);
    }

    /**
     * Send a test push to the logged-in mentor's registered devices.
     */
    public function test(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 401);
        }

        $tokens = NatXDeviceToken::forUser($user->id)
            ->pluck('fcm_token')
            ->filter()
            ->unique()
            ->values()
            ->all();

        if (empty($tokens)) {
            return response()->json([
                'success' => false,
                'message' => 'No device token registered for this user. Call POST /device-token first.',
            ], 404);
        }

        $result = FcmPushService::sendToTokens(
            $tokens,
            'NatX Test Push',
            'Push notification is working correctly.',
            [
                'type' => 'test',
                'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
            ]
        );

        return response()->json([
            'success' => (bool) ($result['success'] ?? false),
            'message' => $result['message'] ?? 'Push attempted',
            'data' => $result,
        ], ($result['success'] ?? false) ? 200 : 500);
    }
}
