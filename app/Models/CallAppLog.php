<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

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
