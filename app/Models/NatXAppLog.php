<?php

namespace App\Models;

use Carbon\Carbon;
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
     * Convert epoch milliseconds from the NatX app to app-local datetime.
     */
    public static function dateTimeFromMilliseconds(?int $milliseconds): ?Carbon
    {
        if ($milliseconds === null || $milliseconds <= 0) {
            return null;
        }

        return Carbon::createFromTimestampMs($milliseconds, config('app.timezone'));
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
}
