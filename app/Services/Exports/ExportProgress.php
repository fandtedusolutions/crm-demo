<?php

namespace App\Services\Exports;

use Carbon\Carbon;
use Illuminate\Support\Facades\File;

class ExportProgress
{
    public const UNCONVERTED_LEADS = 'unconverted-leads';

    public const TELECALLER_WISE = 'telecaller-wise-report';

    /**
     * @return array<int, string>
     */
    public static function keys(): array
    {
        return [
            self::UNCONVERTED_LEADS,
            self::TELECALLER_WISE,
        ];
    }

    public static function isValidKey(string $key): bool
    {
        return in_array($key, self::keys(), true);
    }

    public static function directory(): string
    {
        return storage_path('app/exports');
    }

    public static function statusPath(string $key): string
    {
        return self::directory().'/status/'.$key.'.json';
    }

    public static function filePath(string $key): string
    {
        return self::directory().'/'.$key.'.xlsx';
    }

    public static function downloadName(string $key): string
    {
        $date = now()->format('Y-m-d');

        return match ($key) {
            self::UNCONVERTED_LEADS => 'unconverted_leads_'.$date.'.xlsx',
            self::TELECALLER_WISE => 'telecaller_wise_report_'.$date.'.xlsx',
            default => $key.'.xlsx',
        };
    }

    public static function command(string $key): string
    {
        return match ($key) {
            self::UNCONVERTED_LEADS => 'exports:unconverted-leads',
            self::TELECALLER_WISE => 'exports:telecaller-wise-report',
            default => '',
        };
    }

    /**
     * @return array<string, mixed>
     */
    public static function read(string $key): array
    {
        $path = self::statusPath($key);
        if (! is_file($path)) {
            return self::defaults($key);
        }

        $handle = self::openQuiet($path, 'rb');
        if ($handle === false) {
            return self::defaults($key);
        }

        flock($handle, LOCK_SH);
        $raw = stream_get_contents($handle);
        flock($handle, LOCK_UN);
        fclose($handle);

        $decoded = json_decode($raw ?: '', true);

        return array_merge(self::defaults($key), is_array($decoded) ? $decoded : []);
    }

    /**
     * @param  array<string, mixed>  $changes
     * @return array<string, mixed>
     */
    public static function update(string $key, array $changes): array
    {
        $path = self::statusPath($key);
        self::ensureWritableDirectory(dirname($path));

        $handle = self::openQuiet($path, 'c+');
        if ($handle === false) {
            return array_merge(self::defaults($key), $changes, [
                'message' => 'Progress could not be saved. The export file is still written on the server.',
            ]);
        }

        flock($handle, LOCK_EX);
        $raw = stream_get_contents($handle);
        $current = json_decode($raw ?: '', true);
        $data = array_merge(self::defaults($key), is_array($current) ? $current : [], $changes);
        $data['updated_at'] = now()->toDateTimeString();

        rewind($handle);
        ftruncate($handle, 0);
        fwrite($handle, json_encode($data));
        fflush($handle);
        flock($handle, LOCK_UN);
        fclose($handle);
        @chmod($path, 0666);

        return $data;
    }

    /**
     * @return array<string, mixed>
     */
    public static function present(string $key): array
    {
        $status = self::normalizeStale(self::read($key));
        $filePath = self::filePath($key);
        $ready = self::isReadableFile($filePath);
        $size = $ready ? self::fileSizeQuiet($filePath) : 0;
        $status['file_ready'] = $ready;
        $status['file_size'] = $size;
        $status['file_size_label'] = $ready ? self::formatBytes($size) : '';
        $status['error'] = isset($status['error']) ? (string) $status['error'] : null;
        $status['message'] = (string) ($status['message'] ?? '');
        $status['download_name'] = self::downloadName($key);
        $status['command'] = 'php artisan '.self::command($key);
        $status['percent'] = max(0, min(100, (int) ($status['percent'] ?? 0)));

        return $status;
    }

    public static function isRunning(string $key): bool
    {
        $status = self::read($key);
        if (! in_array($status['status'] ?? '', ['queued', 'processing'], true)) {
            return false;
        }

        $pid = (int) ($status['pid'] ?? 0);
        if ($pid > 0) {
            return self::pidIsRunning($pid);
        }

        $updatedAt = $status['updated_at'] ?? null;
        if (! $updatedAt) {
            return false;
        }

        return Carbon::parse($updatedAt)->gt(now()->subMinutes(2));
    }

    public static function pidIsRunning(int $pid): bool
    {
        if ($pid <= 0) {
            return false;
        }

        if (PHP_OS_FAMILY !== 'Windows' && is_dir('/proc/'.$pid)) {
            return true;
        }

        if (PHP_OS_FAMILY === 'Windows') {
            $output = [];
            exec('tasklist /FI '.escapeshellarg('PID eq '.$pid).' /NH', $output);

            return str_contains(implode(' ', $output), (string) $pid);
        }

        if (function_exists('posix_kill')) {
            $running = @posix_kill($pid, 0);
            if ($running) {
                return true;
            }

            // A different Linux user (php-fpm vs the artisan user) gets EPERM for a live process.
            return function_exists('posix_get_last_error') && posix_get_last_error() === 1;
        }

        return false;
    }

    /**
     * @param  array<string, mixed>  $status
     * @return array<string, mixed>
     */
    private static function normalizeStale(array $status): array
    {
        $state = $status['status'] ?? 'idle';
        if (! in_array($state, ['queued', 'processing'], true)) {
            return $status;
        }

        $pid = (int) ($status['pid'] ?? 0);
        $updatedAt = null;
        if (! empty($status['updated_at'])) {
            try {
                $updatedAt = Carbon::parse($status['updated_at']);
            } catch (\Throwable) {
                $updatedAt = null;
            }
        }
        $stale = $updatedAt && $updatedAt->lt(now()->subMinutes(2));

        if ($pid > 0 && ! self::pidIsRunning($pid)) {
            return self::update($status['key'], [
                'status' => 'failed',
                'message' => 'The export process stopped before the file was saved.',
                'finished_at' => now()->toDateTimeString(),
            ]);
        }

        if ($pid === 0 && $stale) {
            return self::update($status['key'], [
                'status' => 'failed',
                'message' => 'The background process did not start. Run the artisan command on the server.',
                'finished_at' => now()->toDateTimeString(),
            ]);
        }

        return $status;
    }

    /**
     * @return array<string, mixed>
     */
    private static function defaults(string $key): array
    {
        return [
            'key' => $key,
            'status' => 'idle',
            'percent' => 0,
            'processed' => 0,
            'total' => 0,
            'message' => 'Not started',
            'pid' => null,
            'started_at' => null,
            'finished_at' => null,
            'updated_at' => null,
            'error' => null,
        ];
    }

    public static function ensureWritableDirectory(string $directory): void
    {
        if (! is_dir($directory)) {
            File::ensureDirectoryExists($directory);
        }

        @chmod($directory, 0777);
    }

    /**
     * @return resource|false
     */
    private static function openQuiet(string $path, string $mode)
    {
        $handle = false;
        self::withoutWarnings(function () use ($path, $mode, &$handle) {
            $handle = fopen($path, $mode);
        });

        return $handle;
    }

    private static function isReadableFile(string $path): bool
    {
        $readable = false;
        self::withoutWarnings(function () use ($path, &$readable) {
            $readable = is_readable($path);
        });

        return $readable;
    }

    private static function fileSizeQuiet(string $path): int
    {
        $size = 0;
        self::withoutWarnings(function () use ($path, &$size) {
            $read = filesize($path);
            $size = $read === false ? 0 : (int) $read;
        });

        return $size;
    }

    private static function withoutWarnings(callable $callback): void
    {
        $previous = set_error_handler(static function () {
            return true;
        });

        try {
            $callback();
        } finally {
            if ($previous !== null) {
                set_error_handler($previous);
            } else {
                restore_error_handler();
            }
        }
    }

    public static function formatBytes(int $bytes): string
    {
        if ($bytes < 1024) {
            return $bytes.' B';
        }

        if ($bytes < 1048576) {
            return round($bytes / 1024, 1).' KB';
        }

        return round($bytes / 1048576, 2).' MB';
    }
}
