<?php
/*
* 13-09-2025
* @AmeerSuhail
*/

namespace App\Helpers;

use Brick\PhoneNumber\PhoneNumber;
use Brick\PhoneNumber\PhoneNumberParseException;

class PhoneNumberHelper
{
    public static function format(?string $code, ?string $phone): string
    {
        if (empty($phone)) {
            return 'N/A';
        }
        $formattedCode = $code ? (str_starts_with($code, '+') ? $code : '+' . $code) : '';
        return trim($formattedCode . ' ' . $phone);
    }

    public static function display(?string $code, ?string $phone): string
    {
        return self::format($code, $phone);
    }

    public static function forCall(?string $code, ?string $phone): string
    {
        if (empty($phone)) {
            return '';
        }
        $formattedCode = $code ? (str_starts_with($code, '+') ? $code : '+' . $code) : '';
        return $formattedCode . $phone;
    }

    /**
     * E.164-style digits for WhatsApp providers (country code + national number, no +).
     */
    public static function toWhatsAppDigits(?string $code, ?string $phone): string
    {
        if (empty($phone)) {
            return '';
        }

        $phoneDigits = preg_replace('/\D+/', '', (string) $phone) ?? '';
        if ($phoneDigits === '') {
            return '';
        }

        $codeDigits = preg_replace('/\D+/', '', (string) $code) ?? '';
        if ($codeDigits !== '' && ! str_starts_with($phoneDigits, $codeDigits)) {
            return $codeDigits.$phoneDigits;
        }

        return $phoneDigits;
    }

    public static function get_phone_code($phone_number)
    {
        // Clean the phone number - remove spaces, dashes, parentheses
        $phone_number = preg_replace('/[\s\-\(\)]/', '', $phone_number);
        
        // If empty, return empty
        if (empty($phone_number)) {
            return [
                'code' => '',
                'phone' => ''
            ];
        }
        
        // Handle different formats
        $formatted_number = $phone_number;
        
        // If it doesn't start with +, add it
        if (!str_starts_with($phone_number, '+')) {
            $formatted_number = '+' . $phone_number;
        }
        
        try {
            $number = PhoneNumber::parse($formatted_number);
            return [
                'code' => $number->getCountryCode(),
                'phone' => $number->getNationalNumber()
            ];
        } catch (PhoneNumberParseException $e) {
            // Fallback: try to parse manually for common formats
            return self::parsePhoneManually($phone_number);
        }
    }
    
    /**
     * Manual phone number parsing for common formats
     */
    private static function parsePhoneManually($phone_number)
    {
        // Remove any non-numeric characters except +
        $cleaned = preg_replace('/[^\d+]/', '', $phone_number);
        
        // Common country codes and their patterns
        $country_codes = [
            '91' => 10,    // India
            '971' => 9,    // UAE
            '966' => 9,    // Saudi Arabia
            '965' => 8,    // Kuwait
            '973' => 8,    // Bahrain
            '974' => 8,    // Qatar
            '968' => 8,    // Oman
            '1' => 10,     // US/Canada
            '44' => 10,    // UK
            '33' => 9,     // France
            '49' => 10,    // Germany
        ];
        
        // Try to match country codes
        foreach ($country_codes as $code => $expected_length) {
            // Check if phone starts with country code
            if (str_starts_with($cleaned, $code)) {
                $phone_part = substr($cleaned, strlen($code));
                
                // Check if the remaining part has the expected length
                if (strlen($phone_part) == $expected_length && is_numeric($phone_part)) {
                    return [
                        'code' => $code,
                        'phone' => $phone_part
                    ];
                }
            }
            
            // Check with + prefix
            if (str_starts_with($cleaned, '+' . $code)) {
                $phone_part = substr($cleaned, strlen('+' . $code));
                
                if (strlen($phone_part) == $expected_length && is_numeric($phone_part)) {
                    return [
                        'code' => $code,
                        'phone' => $phone_part
                    ];
                }
            }
        }
        
        // If no country code found, assume it's a local number
        // Default to India (91) if it looks like an Indian number (10 digits)
        if (strlen($cleaned) == 10 && is_numeric($cleaned)) {
            return [
                'code' => '91',
                'phone' => $cleaned
            ];
        }
        
        // If it's 11 digits and starts with 0, remove the 0 and assume India
        if (strlen($cleaned) == 11 && str_starts_with($cleaned, '0')) {
            return [
                'code' => '91',
                'phone' => substr($cleaned, 1)
            ];
        }
        
        // If it's 12 digits and starts with 91, extract code and phone
        if (strlen($cleaned) == 12 && str_starts_with($cleaned, '91')) {
            return [
                'code' => '91',
                'phone' => substr($cleaned, 2)
            ];
        }
        
        // If it's 13 digits and starts with +91, extract code and phone
        if (strlen($cleaned) == 13 && str_starts_with($cleaned, '+91')) {
            return [
                'code' => '91',
                'phone' => substr($cleaned, 3)
            ];
        }
        
        // Default fallback - return as is with default code
        return [
            'code' => '91',
            'phone' => $cleaned
        ];
    }
    
    /**
     * Check if two phone numbers are the same (considering different formats)
     */
    public static function isSamePhone($phone1, $phone2)
    {
        $parsed1 = self::get_phone_code($phone1);
        $parsed2 = self::get_phone_code($phone2);
        
        return $parsed1['code'] === $parsed2['code'] && $parsed1['phone'] === $parsed2['phone'];
    }
    
    /**
     * Normalize phone number for consistent storage
     */
    public static function normalize($phone_number)
    {
        $parsed = self::get_phone_code($phone_number);
        return [
            'code' => $parsed['code'],
            'phone' => $parsed['phone'],
            'formatted' => '+' . $parsed['code'] . $parsed['phone']
        ];
    }
}


