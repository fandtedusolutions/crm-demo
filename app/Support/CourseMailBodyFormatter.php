<?php

namespace App\Support;

class CourseMailBodyFormatter
{
    public static function toHtml(string $body): string
    {
        $body = trim($body);

        if ($body === '') {
            return '';
        }

        if (self::looksLikeHtml($body)) {
            return self::wrapLayout($body);
        }

        $normalized = self::normalizePlainTextBlocks($body);
        $paragraphs = array_values(array_filter(
            preg_split('/\n\s*\n/', $normalized) ?: [],
            static fn (string $paragraph): bool => trim($paragraph) !== ''
        ));

        $inner = '';
        foreach ($paragraphs as $index => $paragraph) {
            $inner .= self::formatParagraph(trim($paragraph), $index === 0);
        }

        return self::wrapLayout($inner);
    }

    private static function looksLikeHtml(string $body): bool
    {
        return (bool) preg_match('/<\s*(p|div|br|table|html|body|h[1-6])\b/i', $body);
    }

    private static function normalizePlainTextBlocks(string $text): string
    {
        $text = trim(str_replace(["\r\n", "\r"], "\n", $text));

        if (str_contains($text, "\n\n")) {
            return $text;
        }

        $text = preg_replace(
            '/^(NATDEMY ACADEMY OFFICE)\s+(Subject:)/iu',
            "$1\n\n$2",
            $text
        ) ?? $text;

        $markers = [
            '/\s+(Subject:\s)/iu',
            '/\s+(This notice\b)/iu',
            '/\s+(📞\s*Support Contact)/u',
            '/\s+(Support Contact Number:)/iu',
            '/\s+(https?:\/\/)/iu',
            '/\s+(Upon receiving\b)/iu',
            '/\s+(Issued by:)/iu',
        ];

        foreach ($markers as $pattern) {
            $text = preg_replace($pattern, "\n\n$1", $text, 1) ?? $text;
        }

        return $text;
    }

    private static function formatParagraph(string $paragraph, bool $isFirst): string
    {
        $content = self::escapeAndLinkify($paragraph);

        if (preg_match('/^NATDEMY ACADEMY OFFICE$/iu', $paragraph)) {
            return '<p style="margin:0 0 8px;font-size:17px;font-weight:bold;color:#1a1a1a;text-align:left;">'
                .$content
                .'</p>';
        }

        if (preg_match('/^Subject:/iu', $paragraph)) {
            return '<p style="margin:0 0 16px;font-size:15px;font-weight:bold;color:#333333;text-align:left;">'
                .$content
                .'</p>';
        }

        if (preg_match('/^https?:\/\//i', $paragraph)) {
            return '<p style="margin:0 0 16px;text-align:left;">'.$content.'</p>';
        }

        if (preg_match('/^(📞|Support Contact Number:)/u', $paragraph)) {
            return '<p style="margin:0 0 12px;font-size:15px;text-align:left;">'.$content.'</p>';
        }

        if (preg_match('/^Issued by:/iu', $paragraph)) {
            return '<p style="margin:20px 0 0;font-size:14px;color:#555555;text-align:left;">'.$content.'</p>';
        }

        if ($isFirst && preg_match('/^NATDEMY ACADEMY OFFICE/iu', $paragraph)) {
            return '<p style="margin:0 0 16px;font-size:17px;font-weight:bold;color:#1a1a1a;text-align:left;">'
                .$content
                .'</p>';
        }

        return '<p style="margin:0 0 16px;font-size:15px;color:#333333;text-align:left;line-height:1.65;">'
            .$content
            .'</p>';
    }

    private static function escapeAndLinkify(string $text): string
    {
        $escaped = e($text);

        $withBreaks = nl2br($escaped, false);

        return preg_replace(
            '#(https?://[^\s<&]+)#i',
            '<a href="$1" style="color:#1a73e8;text-decoration:underline;">$1</a>',
            $withBreaks
        ) ?? $withBreaks;
    }

    private static function wrapLayout(string $innerHtml): string
    {
        return '<!DOCTYPE html>'
            .'<html><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1"></head>'
            .'<body style="margin:0;padding:0;background-color:#f4f4f4;font-family:Arial,Helvetica,sans-serif;">'
            .'<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#f4f4f4;padding:24px 12px;">'
            .'<tr><td align="center">'
            .'<table role="presentation" width="600" cellpadding="0" cellspacing="0" style="max-width:600px;width:100%;background-color:#ffffff;border:1px solid #e8e8e8;border-radius:6px;">'
            .'<tr><td style="padding:28px 24px;text-align:left;">'
            .$innerHtml
            .'</td></tr></table>'
            .'</td></tr></table>'
            .'</body></html>';
    }
}
