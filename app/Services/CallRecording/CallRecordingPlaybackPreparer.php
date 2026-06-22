<?php

namespace App\Services\CallRecording;

use Illuminate\Support\Facades\Storage;

class CallRecordingPlaybackPreparer
{
    public function __construct(
        private readonly AdtsAacToMp4Converter $adtsConverter = new AdtsAacToMp4Converter(),
    ) {
    }

    /**
     * Return a browser-playable storage path for the given public-disk file.
     * Raw .aac uploads are remuxed to .m4a; other formats are returned unchanged.
     */
    public function prepare(string $storedPath, string $disk = 'public'): string
    {
        if (!str_ends_with(strtolower($storedPath), '.aac')) {
            return $storedPath;
        }

        $storage = Storage::disk($disk);
        if (!$storage->exists($storedPath)) {
            return $storedPath;
        }

        $m4aPath = preg_replace('/\.aac$/i', '.m4a', $storedPath);
        if ($storage->exists($m4aPath)) {
            return $m4aPath;
        }

        $input = $storage->path($storedPath);
        $output = $storage->path($m4aPath);

        if ($this->convertWithFfmpeg($input, $output) || $this->adtsConverter->convert($input, $output)) {
            if (is_file($output) && filesize($output) > 0) {
                return $m4aPath;
            }
        }

        return $storedPath;
    }

    private function convertWithFfmpeg(string $input, string $output): bool
    {
        $candidates = array_values(array_filter([
            env('FFMPEG_PATH'),
            'ffmpeg',
            '/usr/bin/ffmpeg',
            '/usr/local/bin/ffmpeg',
        ]));

        foreach ($candidates as $ffmpeg) {
            $stderrRedirect = PHP_OS_FAMILY === 'Windows' ? '2>NUL' : '2>/dev/null';
            $command = sprintf(
                '%s -y -i %s -c:a copy -bsf:a aac_adtstoasc %s %s',
                escapeshellarg($ffmpeg),
                escapeshellarg($input),
                escapeshellarg($output),
                $stderrRedirect
            );

            @exec($command, $ignored, $exitCode);

            if ($exitCode === 0 && is_file($output) && filesize($output) > 0) {
                return true;
            }
        }

        return false;
    }
}
