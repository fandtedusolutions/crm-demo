<?php

namespace App\Support;

use App\Helpers\PhoneNumberHelper;
use App\Models\ConvertedLead;

class ConvertedLeadWhatsAppSupport
{
    /**
     * Prefer phone + code on converted_leads; fall back to lead detail WhatsApp when invalid.
     *
     * @return array{number: string, source: string, display: string}|null
     */
    public static function resolveRecipient(ConvertedLead $convertedLead): ?array
    {
        $convertedLead->loadMissing('leadDetail');

        $fromConvertedLead = self::buildRecipient(
            $convertedLead->code,
            $convertedLead->phone,
            'phone'
        );

        if ($fromConvertedLead !== null) {
            return $fromConvertedLead;
        }

        return self::buildRecipient(
            $convertedLead->leadDetail?->whatsapp_code,
            $convertedLead->leadDetail?->whatsapp_number,
            'whatsapp'
        );
    }

    /**
     * @return array{number: string, source: string, display: string}|null
     */
    protected static function buildRecipient(?string $code, ?string $phone, string $source): ?array
    {
        if (! self::isProperNumber($code, $phone)) {
            return null;
        }

        $digits = PhoneNumberHelper::toWhatsAppDigits($code, $phone);

        return [
            'number' => $digits,
            'source' => $source,
            'display' => PhoneNumberHelper::display($code, trim((string) $phone)),
        ];
    }

    public static function isProperNumber(?string $code, ?string $phone): bool
    {
        $phone = trim((string) $phone);
        if ($phone === '') {
            return false;
        }

        $digits = PhoneNumberHelper::toWhatsAppDigits($code, $phone);
        if ($digits === '') {
            return false;
        }

        $length = strlen($digits);

        return $length >= 10 && $length <= 15;
    }

    /**
     * Build Wati template parameters for the configured template (e.g. support_desk).
     *
     * @return list<array{name: string, value: string}>
     */
    public static function resolveTemplateParameters(ConvertedLead $convertedLead): array
    {
        $parameters = [];
        $paramNames = config('wati.template_parameters', ['name']);

        if (! is_array($paramNames)) {
            $paramNames = ['name'];
        }

        foreach ($paramNames as $paramName) {
            $value = self::resolveTemplateParameterValue($convertedLead, (string) $paramName);
            if ($value === '') {
                continue;
            }

            $parameters[] = [
                'name' => (string) $paramName,
                'value' => $value,
            ];
        }

        return $parameters;
    }

    public static function resolveTemplateParameterValue(ConvertedLead $convertedLead, string $paramName): string
    {
        return match ($paramName) {
            'name' => trim((string) ($convertedLead->name ?? 'Student')) ?: 'Student',
            default => '',
        };
    }
}
