<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class CallAppLog extends Model
{
    protected $table = 'call_app_logs';

    protected $fillable = [
        'telecaller_id',
        'device_id',
        'device_call_id',
        'phone_number',
        'contact_name',
        'call_type',
        'remarks',
        'duration_seconds',
        'started_at_ms',
        'started_at',
        'end_at_ms',
        'ended_at',
        'has_recording',
        'recording_uploaded',
        'recording_duration_seconds',
        'recording_file_name',
        'app_version',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
        'has_recording' => 'boolean',
        'recording_uploaded' => 'boolean',
        'duration_seconds' => 'integer',
        'recording_duration_seconds' => 'integer',
        'started_at_ms' => 'integer',
        'end_at_ms' => 'integer',
    ];

    /**
     * Convert epoch milliseconds from the Call Tracker API to app-local datetime.
     */
    public static function dateTimeFromMilliseconds(?int $milliseconds): ?Carbon
    {
        if ($milliseconds === null || $milliseconds <= 0) {
            return null;
        }

        return Carbon::createFromTimestampMs($milliseconds, config('app.timezone'));
    }

    /**
     * Inclusive millisecond range for filtering by calendar dates in app timezone.
     *
     * @return array{0: int, 1: int}
     */
    public static function millisecondRangeForDates(string $startDate, string $endDate): array
    {
        $timezone = config('app.timezone');

        return [
            Carbon::parse($startDate, $timezone)->startOfDay()->getTimestampMs(),
            Carbon::parse($endDate, $timezone)->endOfDay()->getTimestampMs(),
        ];
    }

    /**
     * Filter call_app_logs rows by call date (started_at_ms) in app timezone.
     */
    public function scopeForReportPeriod(Builder $query, string $fromDate, string $toDate): Builder
    {
        [$startMs, $endMs] = self::millisecondRangeForDates($fromDate, $toDate);

        return $query->whereBetween('started_at_ms', [$startMs, $endMs]);
    }

    /**
     * @param  array<int>  $telecallerIds
     */
    public function scopeForTelecallers(Builder $query, array $telecallerIds): Builder
    {
        if ($telecallerIds === []) {
            return $query->whereRaw('0 = 1');
        }

        return $query->whereIn('telecaller_id', $telecallerIds);
    }

    public static function notPickedSqlCondition(): string
    {
        return "(call_type = 'not_picked' OR remarks = 'Not Picked')";
    }

    public static function attendedCallsAggregateSql(): string
    {
        return "SUM(CASE WHEN call_type IN ('incoming', 'outgoing') THEN 1 ELSE 0 END) as attended_calls";
    }

    public static function attendedCallCount(int $incoming, int $outgoing): int
    {
        return $incoming + $outgoing;
    }

    public function scopeAttended(Builder $query): Builder
    {
        return $query->whereIn('call_type', ['incoming', 'outgoing']);
    }

    /**
     * Aggregate columns used by telecaller performance / call analytics reports.
     *
     * @return array<int, mixed>
     */
    public static function telecallerAggregateColumns(): array
    {
        return [
            'telecaller_id',
            DB::raw('COUNT(*) as total_calls'),
            DB::raw("COUNT(DISTINCT REGEXP_REPLACE(phone_number, '[^0-9]', '')) as connected_calls"),
            DB::raw(self::attendedCallsAggregateSql()),
            DB::raw("SUM(CASE WHEN call_type = 'incoming' THEN 1 ELSE 0 END) as incoming_calls"),
            DB::raw("SUM(CASE WHEN call_type = 'outgoing' THEN 1 ELSE 0 END) as outgoing_calls"),
            DB::raw('SUM(CASE WHEN ' . self::notPickedSqlCondition() . ' THEN 1 ELSE 0 END) as not_picked_calls'),
            DB::raw("SUM(CASE WHEN call_type = 'missed' THEN 1 ELSE 0 END) as missed_calls"),
            DB::raw("SUM(CASE WHEN call_type = 'rejected' THEN 1 ELSE 0 END) as rejected_calls"),
            DB::raw('SUM(duration_seconds) as total_duration_seconds'),
            DB::raw('SUM(CASE WHEN has_recording = 1 THEN 1 ELSE 0 END) as with_recording'),
            DB::raw('SUM(CASE WHEN recording_uploaded = 1 THEN 1 ELSE 0 END) as recordings_uploaded'),
        ];
    }

    public static function countDistinctConnectedContacts(Builder $query): int
    {
        return (int) (clone $query)
            ->select(DB::raw("COUNT(DISTINCT REGEXP_REPLACE(phone_number, '[^0-9]', '')) as connected_count"))
            ->value('connected_count');
    }

    /**
     * Per-telecaller call stats from call_app_logs.
     *
     * @param  array<int>  $telecallerIds
     */
    public static function aggregateByTelecaller(string $fromDate, string $toDate, array $telecallerIds): Collection
    {
        if ($telecallerIds === []) {
            return collect();
        }

        return static::query()
            ->forReportPeriod($fromDate, $toDate)
            ->forTelecallers($telecallerIds)
            ->select(static::telecallerAggregateColumns())
            ->groupBy('telecaller_id')
            ->get()
            ->keyBy('telecaller_id');
    }

    /**
     * Grand totals from call_app_logs for a set of telecallers.
     *
     * @param  array<int>  $telecallerIds
     * @return array<string, int>
     */
    public static function grandTotalsForTelecallers(string $fromDate, string $toDate, array $telecallerIds): array
    {
        if ($telecallerIds === []) {
            return static::emptyReportTotals();
        }

        $query = static::query()
            ->forReportPeriod($fromDate, $toDate)
            ->forTelecallers($telecallerIds);

        $aggregates = (clone $query)
            ->selectRaw('COUNT(*) as total_calls')
            ->selectRaw(self::attendedCallsAggregateSql())
            ->selectRaw("SUM(CASE WHEN call_type = 'incoming' THEN 1 ELSE 0 END) as incoming_calls")
            ->selectRaw("SUM(CASE WHEN call_type = 'outgoing' THEN 1 ELSE 0 END) as outgoing_calls")
            ->selectRaw('SUM(CASE WHEN ' . self::notPickedSqlCondition() . ' THEN 1 ELSE 0 END) as not_picked_calls')
            ->selectRaw("SUM(CASE WHEN call_type = 'missed' THEN 1 ELSE 0 END) as missed_calls")
            ->selectRaw("SUM(CASE WHEN call_type = 'rejected' THEN 1 ELSE 0 END) as rejected_calls")
            ->selectRaw('SUM(duration_seconds) as total_duration_seconds')
            ->selectRaw('SUM(CASE WHEN has_recording = 1 THEN 1 ELSE 0 END) as with_recording')
            ->selectRaw('SUM(CASE WHEN recording_uploaded = 1 THEN 1 ELSE 0 END) as recordings_uploaded')
            ->first();

        $incomingCalls = (int) ($aggregates->incoming_calls ?? 0);
        $outgoingCalls = (int) ($aggregates->outgoing_calls ?? 0);

        return [
            'total_calls' => (int) ($aggregates->total_calls ?? 0),
            'connected_calls' => static::countDistinctConnectedContacts($query),
            'attended_calls' => static::attendedCallCount($incomingCalls, $outgoingCalls),
            'incoming_calls' => $incomingCalls,
            'outgoing_calls' => $outgoingCalls,
            'not_picked_calls' => (int) ($aggregates->not_picked_calls ?? 0),
            'missed_calls' => (int) ($aggregates->missed_calls ?? 0),
            'rejected_calls' => (int) ($aggregates->rejected_calls ?? 0),
            'total_duration_seconds' => (int) ($aggregates->total_duration_seconds ?? 0),
            'with_recording' => (int) ($aggregates->with_recording ?? 0),
            'recordings_uploaded' => (int) ($aggregates->recordings_uploaded ?? 0),
        ];
    }

    /**
     * @return array<string, int>
     */
    public static function emptyReportTotals(): array
    {
        return [
            'total_calls' => 0,
            'connected_calls' => 0,
            'attended_calls' => 0,
            'incoming_calls' => 0,
            'outgoing_calls' => 0,
            'not_picked_calls' => 0,
            'missed_calls' => 0,
            'rejected_calls' => 0,
            'total_duration_seconds' => 0,
            'with_recording' => 0,
            'recordings_uploaded' => 0,
        ];
    }

    public function getDisplayStartedAtAttribute(): ?Carbon
    {
        return self::dateTimeFromMilliseconds($this->started_at_ms)
            ?? ($this->started_at ? $this->started_at->copy()->timezone(config('app.timezone')) : null);
    }

    public function getDisplayEndedAtAttribute(): ?Carbon
    {
        return self::dateTimeFromMilliseconds($this->end_at_ms)
            ?? ($this->ended_at ? $this->ended_at->copy()->timezone(config('app.timezone')) : null);
    }

    public function telecaller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'telecaller_id');
    }

    public function recording(): HasOne
    {
        return $this->hasOne(CallAppRecording::class, 'call_app_log_id');
    }

    public static function formatDuration(int $seconds): string
    {
        if ($seconds <= 0) {
            return '0:00';
        }

        $hours = intdiv($seconds, 3600);
        $minutes = intdiv($seconds % 3600, 60);
        $secs = $seconds % 60;

        if ($hours > 0) {
            return sprintf('%d:%02d:%02d', $hours, $minutes, $secs);
        }

        return sprintf('%d:%02d', $minutes, $secs);
    }

    public function getFormattedDurationAttribute(): string
    {
        return self::formatDuration((int) $this->duration_seconds);
    }

    public function getCallTypeLabelAttribute(): string
    {
        return match ($this->call_type) {
            'not_picked' => 'Not Picked',
            default => ucfirst(str_replace('_', ' ', $this->call_type ?? 'unknown')),
        };
    }
}
