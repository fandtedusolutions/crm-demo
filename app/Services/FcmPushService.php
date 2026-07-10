<?php

namespace App\Services;

use App\Models\NatXDeviceToken;
use App\Models\NatXNotification;
use Firebase\JWT\JWT;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FcmPushService
{
    private const TOKEN_CACHE_KEY = 'natx_fcm_access_token';

    /**
     * Send push for a NatX in-app notification to all of the mentor's devices.
     */
    public static function sendNatXNotification(NatXNotification $notification): void
    {
        if (!$notification->user_id) {
            Log::info('FCM skipped: notification has no user_id', [
                'notification_id' => $notification->id,
            ]);

            return;
        }

        $tokens = NatXDeviceToken::forUser((int) $notification->user_id)
            ->pluck('fcm_token')
            ->filter()
            ->unique()
            ->values()
            ->all();

        if (empty($tokens)) {
            Log::info('FCM skipped: no device tokens for mentor', [
                'notification_id' => $notification->id,
                'user_id' => $notification->user_id,
            ]);

            return;
        }

        $data = [
            'notification_id' => (string) $notification->id,
            'title' => (string) $notification->title,
            'body' => (string) ($notification->description ?? ''),
            'type' => (string) $notification->type,
            'date' => optional($notification->date)->format('Y-m-d') ?? '',
            'upto_date' => optional($notification->upto_date)->format('Y-m-d') ?? '',
            'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
        ];

        $result = self::sendToTokens(
            $tokens,
            (string) $notification->title,
            (string) ($notification->description ?? ''),
            $data
        );

        Log::info('FCM send result for NatX notification', [
            'notification_id' => $notification->id,
            'user_id' => $notification->user_id,
            'result' => $result,
        ]);
    }

    /**
     * Send FCM message to one or more device tokens.
     *
     * @param  array<int, string>  $tokens
     * @param  array<string, string>  $data
     */
    public static function sendToTokens(array $tokens, string $title, string $body, array $data = []): array
    {
        $projectId = config('services.firebase.project_id');
        $credentials = self::credentials();

        if (!$projectId || !$credentials) {
            Log::warning('FCM skipped: Firebase project_id or credentials not configured.', [
                'project_id' => $projectId,
                'has_credentials' => (bool) $credentials,
            ]);

            return [
                'success' => false,
                'message' => 'Firebase is not configured',
                'sent' => 0,
                'failed' => 0,
            ];
        }

        $accessToken = self::accessToken($credentials);
        if (!$accessToken) {
            return [
                'success' => false,
                'message' => 'Unable to get Firebase access token',
                'sent' => 0,
                'failed' => 0,
            ];
        }

        $url = "https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send";
        $sent = 0;
        $failed = 0;
        $invalidTokens = [];

        foreach ($tokens as $token) {
            $payload = [
                'message' => [
                    'token' => $token,
                    'notification' => [
                        'title' => $title,
                        'body' => $body,
                    ],
                    'data' => self::stringifyData($data),
                    'android' => [
                        'priority' => 'high',
                        'notification' => [
                            'sound' => 'default',
                            'channel_id' => 'natx_notifications',
                        ],
                    ],
                    'apns' => [
                        'payload' => [
                            'aps' => [
                                'sound' => 'default',
                                'content-available' => 1,
                            ],
                        ],
                    ],
                ],
            ];

            try {
                $response = Http::withToken($accessToken)
                    ->acceptJson()
                    ->timeout(15)
                    ->post($url, $payload);

                if ($response->successful()) {
                    $sent++;
                    NatXDeviceToken::where('fcm_token', $token)->update([
                        'last_used_at' => now(),
                    ]);
                    continue;
                }

                $failed++;
                $errorCode = data_get($response->json(), 'error.details.0.errorCode')
                    ?? data_get($response->json(), 'error.status');

                Log::warning('FCM send failed', [
                    'token' => substr($token, 0, 20) . '...',
                    'status' => $response->status(),
                    'error' => $response->json(),
                ]);

                if (in_array($errorCode, ['UNREGISTERED', 'NOT_FOUND', 'INVALID_ARGUMENT'], true)
                    || $response->status() === 404) {
                    $invalidTokens[] = $token;
                }
            } catch (\Throwable $e) {
                $failed++;
                Log::error('FCM send exception: ' . $e->getMessage(), [
                    'token' => substr($token, 0, 20) . '...',
                ]);
            }
        }

        if (!empty($invalidTokens)) {
            NatXDeviceToken::whereIn('fcm_token', $invalidTokens)->delete();
        }

        return [
            'success' => $sent > 0,
            'message' => "FCM sent={$sent}, failed={$failed}",
            'sent' => $sent,
            'failed' => $failed,
            'removed_invalid' => count($invalidTokens),
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, string>
     */
    private static function stringifyData(array $data): array
    {
        $out = [];
        foreach ($data as $key => $value) {
            $out[(string) $key] = is_scalar($value) || $value === null
                ? (string) $value
                : json_encode($value);
        }

        return $out;
    }

    /**
     * @return array<string, mixed>|null
     */
    private static function credentials(): ?array
    {
        $path = config('services.firebase.credentials');
        if (!$path) {
            return null;
        }

        if (!str_starts_with($path, DIRECTORY_SEPARATOR) && !preg_match('/^[A-Za-z]:[\\\\\\/]/', $path)) {
            $path = base_path($path);
        }

        if (!is_file($path)) {
            Log::warning('FCM credentials file not found', ['path' => $path]);

            return null;
        }

        $json = json_decode((string) file_get_contents($path), true);

        if (!is_array($json) || empty($json['client_email']) || empty($json['private_key'])) {
            Log::warning('FCM credentials file is invalid');

            return null;
        }

        return $json;
    }

    /**
     * @param  array<string, mixed>  $credentials
     */
    private static function accessToken(array $credentials): ?string
    {
        $cached = Cache::get(self::TOKEN_CACHE_KEY);
        if (is_string($cached) && $cached !== '') {
            return $cached;
        }

        try {
            $now = time();
            $jwt = JWT::encode([
                'iss' => $credentials['client_email'],
                'sub' => $credentials['client_email'],
                'aud' => 'https://oauth2.googleapis.com/token',
                'iat' => $now,
                'exp' => $now + 3600,
                'scope' => 'https://www.googleapis.com/auth/firebase.messaging',
            ], $credentials['private_key'], 'RS256');

            $response = Http::asForm()
                ->timeout(15)
                ->post('https://oauth2.googleapis.com/token', [
                    'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                    'assertion' => $jwt,
                ]);

            if (!$response->successful()) {
                Log::error('FCM OAuth token request failed', [
                    'status' => $response->status(),
                    'body' => $response->json(),
                ]);

                return null;
            }

            $accessToken = $response->json('access_token');
            $expiresIn = (int) ($response->json('expires_in') ?? 3600);

            if (!$accessToken) {
                return null;
            }

            Cache::put(self::TOKEN_CACHE_KEY, $accessToken, max(60, $expiresIn - 60));

            return $accessToken;
        } catch (\Throwable $e) {
            Log::error('FCM OAuth exception: ' . $e->getMessage());

            return null;
        }
    }
}
