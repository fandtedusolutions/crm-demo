<?php

namespace App\Models;

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
        'duration_seconds',
        'started_at_ms',
        'started_at',
        'has_recording',
        'recording_uploaded',
        'recording_duration_seconds',
        'recording_file_name',
        'app_version',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'has_recording' => 'boolean',
        'recording_uploaded' => 'boolean',
        'duration_seconds' => 'integer',
        'recording_duration_seconds' => 'integer',
        'started_at_ms' => 'integer',
    ];

    public function telecaller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'telecaller_id');
    }

    public function recording(): HasOne
    {
        return $this->hasOne(CallAppRecording::class, 'call_app_log_id');
    }
}
