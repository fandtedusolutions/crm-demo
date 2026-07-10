<?php

namespace App\Http\Controllers\API\NatX_Api;

use App\Http\Controllers\Controller;
use App\Models\NatXNotification;
use App\Models\NatXNotificationRead;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * List NatX notifications and mark unread ones as read for the current user.
     */
    public function index(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 401);
        }

        $notifications = NatXNotification::visible()
            ->forUser($user->id)
            ->with(['reads' => function ($query) use ($user) {
                $query->where('user_id', $user->id);
            }])
            ->orderByDesc('date')
            ->orderByDesc('created_at')
            ->get();

        $unreadIds = $notifications
            ->filter(fn (NatXNotification $notification) => $notification->reads->isEmpty())
            ->pluck('id')
            ->values()
            ->all();

        if (!empty($unreadIds)) {
            $now = now();
            $rows = array_map(fn ($notificationId) => [
                'natx_notification_id' => $notificationId,
                'user_id' => $user->id,
                'read_at' => $now,
                'created_at' => $now,
                'updated_at' => $now,
            ], $unreadIds);

            NatXNotificationRead::upsert(
                $rows,
                ['natx_notification_id', 'user_id'],
                ['read_at', 'updated_at']
            );
        }

        $data = $notifications->map(function (NatXNotification $notification) use ($unreadIds) {
            $wasUnread = in_array($notification->id, $unreadIds, true);

            return [
                'id' => $notification->id,
                'title' => $notification->title,
                'type' => $notification->type,
                'description' => $notification->description,
                'date' => optional($notification->date)->format('Y-m-d'),
                'upto_date' => optional($notification->upto_date)->format('Y-m-d'),
                'is_read' => true,
                'was_unread' => $wasUnread,
                'created_at' => optional($notification->created_at)->toDateTimeString(),
                'updated_at' => optional($notification->updated_at)->toDateTimeString(),
            ];
        })->values();

        return response()->json([
            'success' => true,
            'data' => $data,
            'marked_read_count' => count($unreadIds),
        ], 200);
    }

    /**
     * Unread notification count for the authenticated user.
     */
    public static function unreadCount(int $userId): int
    {
        return NatXNotification::visible()
            ->forUser($userId)
            ->whereDoesntHave('reads', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->count();
    }
}
