<?php

namespace App\Services\CallRecording;

class RecordingFileTypeDetector
{
    /**
     * @return array{extension: string, mime: string}|null
     */
    public function detectFromPath(string $path): ?array
    {
        if (!is_readable($path)) {
            return null;
        }

        $handle = fopen($path, 'rb');
        if ($handle === false) {
            return null;
        }

        $header = fread($handle, 12);
        fclose($handle);

        return $this->detectFromHeader($header === false ? '' : $header);
    }

    /**
     * @return array{extension: string, mime: string}|null
     */
    public function detectFromHeader(string $header): ?array
    {
        if ($header === '') {
            return null;
        }

        if (str_contains($header, 'ftyp')) {
            return ['extension' => 'm4a', 'mime' => 'audio/mp4'];
        }

        $firstByte = ord($header[0]);
        $secondByte = strlen($header) > 1 ? ord($header[1]) : 0;
        if ($firstByte === 0xFF && ($secondByte & 0xF6) === 0xF0) {
            return ['extension' => 'aac', 'mime' => 'audio/aac'];
        }

        if (str_starts_with($header, 'ID3') || str_starts_with($header, "\xFF\xFB") || str_starts_with($header, "\xFF\xF3")) {
            return ['extension' => 'mp3', 'mime' => 'audio/mpeg'];
        }

        if (str_starts_with($header, '#!AMR')) {
            return ['extension' => 'amr', 'mime' => 'audio/amr'];
        }

        if (str_starts_with($header, 'RIFF')) {
            return ['extension' => 'wav', 'mime' => 'audio/wav'];
        }

        return null;
    }

    public function extensionFromMime(string $mime): ?string
    {
        return match (strtolower(trim($mime))) {
            'audio/aac', 'audio/x-aac', 'audio/aacp' => 'aac',
            'audio/mp4', 'audio/x-m4a', 'audio/mp4a-latm', 'video/mp4' => 'm4a',
            'audio/mpeg' => 'mp3',
            'audio/wav', 'audio/x-wav' => 'wav',
            'audio/amr' => 'amr',
            'audio/3gpp', 'audio/3gp' => '3gp',
            default => null,
        };
    }

    public function mimeFromExtension(string $extension): ?string
    {
        return match (strtolower(trim($extension))) {
            'aac' => 'audio/aac',
            'm4a' => 'audio/mp4',
            'mp3' => 'audio/mpeg',
            'wav' => 'audio/wav',
            'amr' => 'audio/amr',
            '3gp' => 'audio/3gpp',
            default => null,
        };
    }
}
