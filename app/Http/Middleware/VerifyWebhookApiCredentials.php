<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyWebhookApiCredentials
{
    public function handle(Request $request, Closure $next): Response
    {
        $apiKey = (string) $request->header('X-API-Key', '');
        $apiSecret = (string) $request->header('X-API-Secret', '');

        $expectedKey = (string) config('services.natdemy_webhook.api_key', '');
        $expectedSecret = (string) config('services.natdemy_webhook.api_secret', '');

        if ($expectedKey === '' || $expectedSecret === '') {
            return response()->json([
                'success' => false,
                'message' => 'Webhook credentials are not configured',
            ], 503);
        }

        if ($apiKey === '' || $apiSecret === '') {
            return response()->json([
                'success' => false,
                'message' => 'Missing X-API-Key or X-API-Secret header',
            ], 401);
        }

        if (! hash_equals($expectedKey, $apiKey) || ! hash_equals($expectedSecret, $apiSecret)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid API credentials',
            ], 401);
        }

        return $next($request);
    }
}
