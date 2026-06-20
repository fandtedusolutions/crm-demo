<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

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

    public function getFileUrlAttribute(): ?string
    {
        $path = $this->storedStoragePath();
        if (!$path) {
            return null;
        }

        return Storage::disk('public')->url($path);
    }

    public function getStreamUrlAttribute(): ?string
    {
        if (!$this->call_app_log_id) {
            return null;
        }

        return route('admin.call-analytics.recording.stream', $this->call_app_log_id);
    }

    /**
     * Resolve the uploaded file path on disk (original API upload).
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

    public function playbackMimeType(): string
    {
        $mime = strtolower(trim((string) ($this->mime_type ?? '')));
        if ($mime !== '' && $mime !== 'application/octet-stream') {
            return $mime;
        }

        $path = $this->file_path;
        $extension = strtolower(pathinfo((string) ($this->file_name ?: $path), PATHINFO_EXTENSION));

        if ($extension === 'aac' && $path && Storage::disk('public')->exists(preg_replace('/\.aac$/i', '.m4a', $path))) {
            return 'audio/mp4';
        }

        $detected = $this->detectMimeTypeFromFileHeader($this->storedStoragePath());

        if ($detected) {
            return $detected;
        }

        return match ($extension) {
            'aac' => 'audio/aac',
            'm4a' => 'audio/mp4',
            'mp3' => 'audio/mpeg',
            'wav' => 'audio/wav',
            'amr' => 'audio/amr',
            '3gp' => 'audio/3gpp',
            default => 'application/octet-stream',
        };
    }

    /**
     * Path for inline browser playback. Keeps the original upload intact for download.
     */
    public function playbackStoragePath(): string
    {
        $storedPath = $this->storedStoragePath();
        if (!$storedPath) {
            return (string) $this->file_path;
        }

        if (!str_ends_with(strtolower($storedPath), '.aac')) {
            return $storedPath;
        }

        $disk = Storage::disk('public');
        $m4aPath = preg_replace('/\.aac$/i', '.m4a', $storedPath);

        if ($disk->exists($m4aPath)) {
            return $m4aPath;
        }

        $input = $disk->path($storedPath);
        $output = $disk->path($m4aPath);

        foreach (['ffmpeg', '/usr/bin/ffmpeg'] as $ffmpeg) {
            $command = sprintf(
                '%s -y -i %s -c:a copy -bsf:a aac_adtstoasc %s',
                escapeshellarg($ffmpeg),
                escapeshellarg($input),
                escapeshellarg($output)
            );

            @exec($command . ' 2>/dev/null', $ignored, $exitCode);

            if ($exitCode === 0 && is_file($output) && filesize($output) > 0) {
                return $m4aPath;
            }
        }

        return $storedPath;
    }

    private function detectMimeTypeFromFileHeader(?string $path = null): ?string
    {
        $path = $path ?: $this->file_path;
        if (!$path || !Storage::disk('public')->exists($path)) {
            return null;
        }
        $handle = fopen(Storage::disk('public')->path($path), 'rb');
        if ($handle === false) {
            return null;
        }

        $header = fread($handle, 12);
        fclose($handle);

        if ($header === false || $header === '') {
            return null;
        }

        if (str_contains($header, 'ftyp')) {
            return 'audio/mp4';
        }

        $firstByte = ord($header[0]);
        $secondByte = strlen($header) > 1 ? ord($header[1]) : 0;
        if ($firstByte === 0xFF && ($secondByte & 0xF6) === 0xF0) {
            return 'audio/aac';
        }

        if (str_starts_with($header, 'ID3') || str_starts_with($header, "\xFF\xFB")) {
            return 'audio/mpeg';
        }

        if (str_starts_with($header, '#!AMR')) {
            return 'audio/amr';
        }

        if (str_starts_with($header, 'RIFF')) {
            return 'audio/wav';
        }

        return null;
    }

    public function getFormattedFileSizeAttribute(): string
    {
        $bytes = (int) $this->file_size_bytes;
        if ($bytes >= 1048576) {
            return round($bytes / 1048576, 2) . ' MB';
        }
        if ($bytes >= 1024) {
            return round($bytes / 1024, 2) . ' KB';
        }

        return $bytes . ' B';
    }
}
