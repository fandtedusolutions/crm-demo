<?php

namespace App\Helpers;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class LeadDateFilterHelper
{
    /**
     * Resolve from/to date strings from common request parameter names.
     *
     * @return array{0: ?string, 1: ?string} [fromDate, toDate] as Y-m-d or null
     */
    public static function fromRequest(Request $request): array
    {
        $from = $request->input('date_from') ?? $request->input('from_date');
        $to = $request->input('date_to') ?? $request->input('to_date');

        return [self::normalizeDate($from), self::normalizeDate($to)];
    }

    public static function normalizeDate(mixed $date): ?string
    {
        if ($date === null) {
            return null;
        }

        $date = trim((string) $date);
        if ($date === '') {
            return null;
        }

        try {
            return Carbon::parse($date)->format('Y-m-d');
        } catch (\Throwable) {
            return null;
        }
    }

    /**
     * Apply created_at date range to a leads query (never first_created_at).
     */
    public static function applyToQuery(Builder $query, ?string $fromDate, ?string $toDate): Builder
    {
        $fromDate = self::normalizeDate($fromDate);
        $toDate = self::normalizeDate($toDate);

        if (!$fromDate && !$toDate) {
            return $query;
        }

        if ($fromDate && $toDate) {
            return $query->whereBetween('leads.created_at', [
                $fromDate . ' 00:00:00',
                $toDate . ' 23:59:59',
            ]);
        }

        if ($fromDate) {
            $query->where('leads.created_at', '>=', $fromDate . ' 00:00:00');
        }

        if ($toDate) {
            $query->where('leads.created_at', '<=', $toDate . ' 23:59:59');
        }

        return $query;
    }
}
