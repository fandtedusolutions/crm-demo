<?php

namespace App\Helpers;

use Carbon\Carbon;

class DateRangeHelper
{
    public const PRESET_TODAY = 'today';
    public const PRESET_YESTERDAY = 'yesterday';
    public const PRESET_THIS_WEEK = 'this_week';
    public const PRESET_LAST_WEEK = 'last_week';
    public const PRESET_THIS_MONTH = 'this_month';
    public const PRESET_LAST_MONTH = 'last_month';
    public const PRESET_CUSTOM = 'custom';

    public static function options(): array
    {
        return [
            self::PRESET_TODAY => 'Today',
            self::PRESET_YESTERDAY => 'Yesterday',
            self::PRESET_THIS_WEEK => 'This Week',
            self::PRESET_LAST_WEEK => 'Last Week',
            self::PRESET_THIS_MONTH => 'This Month',
            self::PRESET_LAST_MONTH => 'Last Month',
            self::PRESET_CUSTOM => 'Custom',
        ];
    }

    public static function defaultPreset(): string
    {
        return self::PRESET_TODAY;
    }

    public static function resolve(?string $dateRange = null, ?string $startDate = null, ?string $endDate = null): array
    {
        $today = Carbon::today();
        $preset = $dateRange ?: self::defaultPreset();

        if ($preset === self::PRESET_CUSTOM) {
            return [
                'date_range' => self::PRESET_CUSTOM,
                'start_date' => $startDate ?: $today->format('Y-m-d'),
                'end_date' => $endDate ?: $today->format('Y-m-d'),
            ];
        }

        [$start, $end] = match ($preset) {
            self::PRESET_YESTERDAY => [
                $today->copy()->subDay(),
                $today->copy()->subDay(),
            ],
            self::PRESET_THIS_WEEK => [
                $today->copy()->startOfWeek(),
                $today->copy()->endOfWeek(),
            ],
            self::PRESET_LAST_WEEK => [
                $today->copy()->subWeek()->startOfWeek(),
                $today->copy()->subWeek()->endOfWeek(),
            ],
            self::PRESET_THIS_MONTH => [
                $today->copy()->startOfMonth(),
                $today->copy()->endOfMonth(),
            ],
            self::PRESET_LAST_MONTH => [
                $today->copy()->subMonth()->startOfMonth(),
                $today->copy()->subMonth()->endOfMonth(),
            ],
            default => [
                $today->copy(),
                $today->copy(),
            ],
        };

        return [
            'date_range' => $preset,
            'start_date' => $start->format('Y-m-d'),
            'end_date' => $end->format('Y-m-d'),
        ];
    }

    public static function queryParams(array $filters): array
    {
        $params = ['date_range' => $filters['date_range'] ?? self::defaultPreset()];

        if (($params['date_range'] ?? '') === self::PRESET_CUSTOM) {
            $params['start_date'] = $filters['start_date'] ?? null;
            $params['end_date'] = $filters['end_date'] ?? null;
        }

        return array_filter($params, fn ($value) => $value !== null && $value !== '');
    }

    public static function formatDisplay(?string $date): string
    {
        if (empty($date)) {
            return '';
        }

        return Carbon::parse($date)->format('d-m-Y');
    }

    public static function displayPeriod(array $filters, string $separator = ' · '): string
    {
        $label = self::options()[$filters['date_range'] ?? self::defaultPreset()] ?? 'Custom';
        $start = self::formatDisplay($filters['start_date'] ?? null);
        $end = self::formatDisplay($filters['end_date'] ?? null);

        return $label . $separator . $start . ' to ' . $end;
    }
}
