<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class NatXAppRecording extends Model
{
    protected $table = 'natx_app_recordings';

    protected $fillable = [
        'natx_app_log_id',
        'user_id',
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

    public function natxLog(): BelongsTo
    {
        return $this->belongsTo(NatXAppLog::class, 'natx_app_log_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getFileUrlAttribute(): ?string
    {
        $path = $this->storedStoragePath();
        if (!$path) {
            return null;
        }

        return Storage::disk('public')->url($path);
    }

    /**
     * Resolve the uploaded file path on disk.
     */
    public function storedStoragePath(): ?string
    {
        if (!$this->file_path) {
            return null;
        }

        $candidates = array_values(array_unique(array_filter([
            $this->file_path,
            preg_replace('/\.m4a$/i', '.aac', $this->file_path),
            preg_replace('/\.aac$/i', '.m4a', $this->file_path),
        ])));

        foreach ($candidates as $path) {
            if (Storage::disk('public')->exists($path)) {
                return $path;
            }
        }

        return null;
    }
}
