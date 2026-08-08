<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class VerifyWebhookApiCredentials
{
    public function handle(Request $request, Closure $next): Response
    {
        $log = Log::channel('meta_whatsapp_webhook');

        $apiKey = (string) $request->header('X-API-Key', '');
        $apiSecret = (string) $request->header('X-API-Secret', '');

        $expectedKey = (string) config('services.natdemy_webhook.api_key', '');
        $expectedSecret = (string) config('services.natdemy_webhook.api_secret', '');

        $authContext = [
            'path' => $request->path(),
            'method' => $request->method(),
            'ip' => $request->ip(),
            'has_api_key_header' => $apiKey !== '',
            'has_api_secret_header' => $apiSecret !== '',
            'server_key_configured' => $expectedKey !== '',
            'server_secret_configured' => $expectedSecret !== '',
            'api_key_prefix' => $apiKey !== '' ? substr($apiKey, 0, 8).'...' : null,
        ];

        $log->info('Webhook auth check', $authContext);
        Log::info('[meta-whatsapp-webhook] auth check', $authContext);

        if ($expectedKey === '' || $expectedSecret === '') {
            $message = 'Webhook credentials are not configured on server (.env NATDEMY_API_KEY / NATDEMY_API_SECRET)';
            $log->error($message);
            Log::error('[meta-whatsapp-webhook] '.$message);

            return response()->json([
                'success' => false,
                'message' => 'Webhook credentials are not configured',
            ], 503);
        }

        if ($apiKey === '' || $apiSecret === '') {
            $context = ['headers_present' => array_keys($request->headers->all())];
            $log->warning('Missing X-API-Key or X-API-Secret header', $context);
            Log::warning('[meta-whatsapp-webhook] Missing X-API-Key or X-API-Secret header', $context);

            return response()->json([
                'success' => false,
                'message' => 'Missing X-API-Key or X-API-Secret header',
            ], 401);
        }

        if (! hash_equals($expectedKey, $apiKey) || ! hash_equals($expectedSecret, $apiSecret)) {
            $context = [
                'key_match' => hash_equals($expectedKey, $apiKey),
                'secret_match' => hash_equals($expectedSecret, $apiSecret),
            ];
            $log->warning('Invalid API credentials', $context);
            Log::warning('[meta-whatsapp-webhook] Invalid API credentials', $context);

            return response()->json([
                'success' => false,
                'message' => 'Invalid API credentials',
            ], 401);
        }

        $log->info('Webhook auth OK');
        Log::info('[meta-whatsapp-webhook] auth OK');

        return $next($request);
    }
}
