<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class NatXAppLog extends Model
{
    protected $table = 'natx_app_logs';

    protected $fillable = [
        'user_id',
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
     * Normalize epoch values from the mobile app.
     * Some devices send seconds; the API contract expects milliseconds.
     */
    public static function normalizeEpochMilliseconds(?int $value): ?int
    {
        if ($value === null || $value <= 0) {
            return null;
        }

        if ($value < 1_000_000_000_000) {
            return $value * 1000;
        }

        return $value;
    }

    /**
     * Convert epoch milliseconds from the NatX app to app-local datetime.
     */
    public static function dateTimeFromMilliseconds(?int $milliseconds): ?Carbon
    {
        $milliseconds = self::normalizeEpochMilliseconds($milliseconds);
        if ($milliseconds === null) {
            return null;
        }

        return Carbon::createFromTimestampMs($milliseconds, config('app.timezone'));
    }

    /**
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

    public function scopeForReportPeriod(Builder $query, string $fromDate, string $toDate): Builder
    {
        [$startMs, $endMs] = self::millisecondRangeForDates($fromDate, $toDate);

        return $query->whereRaw(
            'IF(started_at_ms < 1000000000000, started_at_ms * 1000, started_at_ms) BETWEEN ? AND ?',
            [$startMs, $endMs]
        );
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

    public function getNormalizedStartedAtMsAttribute(): ?int
    {
        return self::normalizeEpochMilliseconds($this->started_at_ms);
    }

    public function getNormalizedEndAtMsAttribute(): ?int
    {
        return self::normalizeEpochMilliseconds($this->end_at_ms);
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

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function recording(): HasOne
    {
        return $this->hasOne(NatXAppRecording::class, 'natx_app_log_id');
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
