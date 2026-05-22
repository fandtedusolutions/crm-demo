<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class WatiService
{
    public function isEnabled(): bool
    {
        return (bool) config('wati.enabled')
            && filled(config('wati.api_endpoint'))
            && filled(config('wati.api_token'));
    }

    public function canSendTemplate(): bool
    {
        return $this->isEnabled() && filled(config('wati.channel_phone_number'));
    }

    /**
     * @param  list<array{name: string, value: string}>  $parameters
     * @return array<string, mixed>
     */
    public function sendTemplateMessage(string $whatsappNumber, array $parameters = []): array
    {
        if (! $this->canSendTemplate()) {
            throw new RuntimeException(
                'Wati template messaging is not configured. Set WATI_ENABLED, WATI_API_ENDPOINT, WATI_API_TOKEN, and WATI_CHANNEL_PHONE_NUMBER.'
            );
        }

        $endpoint = rtrim((string) config('wati.api_endpoint'), '/');
        $url = $endpoint.'/api/v1/sendTemplateMessage';

        $channelNumber = $this->normalizeDigits((string) config('wati.channel_phone_number'));
        $payload = [
            'template_name' => (string) config('wati.template_name', 'support_desk'),
            'broadcast_name' => (string) config('wati.broadcast_name', 'support_desk'),
            'channel_number' => $channelNumber,
            'parameters' => array_values($parameters),
        ];

        $response = Http::withToken((string) config('wati.api_token'))
            ->acceptJson()
            ->timeout(30)
            ->post($url.'?'.http_build_query(['whatsappNumber' => $whatsappNumber]), $payload);

        $body = $response->json() ?? [];

        if (! $response->successful() || ! ($body['result'] ?? false)) {
            Log::warning('Wati sendTemplateMessage failed', [
                'status' => $response->status(),
                'body' => $body,
                'whatsapp_number' => $whatsappNumber,
                'template_name' => $payload['template_name'],
            ]);

            $error = $body['message'] ?? $body['error'] ?? $response->body();
            throw new RuntimeException(
                is_string($error) ? $error : 'Failed to send WhatsApp template via Wati.'
            );
        }

        return $body;
    }

    public function normalizeDigits(?string $value): string
    {
        return preg_replace('/\D+/', '', (string) $value) ?? '';
    }
}
