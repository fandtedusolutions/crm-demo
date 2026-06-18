<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CallAppRecording extends Model
{
    protected $table = 'call_app_recordings';

    protected $fillable = [
        'call_app_log_id',
        'telecaller_id',
        'file_path',
        'file_name',
        'mime_type',
        'file_size_bytes',
        'duration_seconds',
        'recorded_at_ms',
    ];

    protected $casts = [
        'file_size_bytes' => 'integer',
        'duration_seconds' => 'integer',
        'recorded_at_ms' => 'integer',
    ];

    public function callLog(): BelongsTo
    {
        return $this->belongsTo(CallAppLog::class, 'call_app_log_id');
    }

    public function telecaller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'telecaller_id');
    }
}
