<?php

namespace App\Services\Exports;

use RuntimeException;
use Symfony\Component\Process\Process;
use Throwable;

class BackgroundExportLauncher
{
    /**
     * @return array<string, mixed>
     */
    public static function start(string $key, array $arguments = []): array
    {
        if (! ExportProgress::isValidKey($key)) {
            throw new RuntimeException('Unknown export.');
        }

        if (ExportProgress::isRunning($key)) {
            $status = ExportProgress::present($key);
            $status['already_running'] = true;

            return $status;
        }

        ExportProgress::update($key, [
            'status' => 'queued',
            'percent' => 0,
            'processed' => 0,
            'total' => 0,
            'message' => 'Queued. Waiting for the background process to start.',
            'pid' => null,
            'started_at' => now()->toDateTimeString(),
            'finished_at' => null,
            'error' => null,
            'filters' => [
                'start_date' => self::argumentValue($arguments, 'start') ?: '2024-01-01',
                'end_date' => self::argumentValue($arguments, 'end') ?: now()->format('Y-m-d'),
            ],
        ]);

        try {
            self::spawn(ExportProgress::command($key), $arguments);
        } catch (Throwable $exception) {
            ExportProgress::update($key, [
                'status' => 'failed',
                'percent' => 0,
                'message' => 'Could not start the background process.',
                'error' => $exception->getMessage(),
                'finished_at' => now()->toDateTimeString(),
            ]);
        }

        $status = ExportProgress::present($key);
        $status['already_running'] = false;

        return $status;
    }

    private static function phpBinary(): string
    {
        $cli = PHP_BINDIR.DIRECTORY_SEPARATOR.'php'.(PHP_OS_FAMILY === 'Windows' ? '.exe' : '');
        if (is_file($cli)) {
            return $cli;
        }

        return PHP_BINARY;
    }

    private static function argumentValue(array $arguments, string $name): ?string
    {
        foreach ($arguments as $argument) {
            $prefix = '--'.$name.'=';
            if (str_starts_with($argument, $prefix)) {
                return substr($argument, strlen($prefix));
            }
        }

        return null;
    }

    private static function spawn(string $artisanCommand, array $arguments = []): void
    {
        $php = self::phpBinary();
        $artisan = base_path('artisan');
        $logDirectory = storage_path('logs');
        if (! is_dir($logDirectory)) {
            mkdir($logDirectory, 0755, true);
        }

        $log = $logDirectory.'/'.str_replace(':', '-', $artisanCommand).'.log';

        if (PHP_OS_FAMILY === 'Windows') {
            $process = new Process(array_merge([$php, $artisan, $artisanCommand], $arguments), base_path());
            $process->setOptions(['create_new_console' => true]);
            $process->setTimeout(null);
            $process->start();

            return;
        }

        $parts = array_merge(
            [escapeshellarg($php), escapeshellarg($artisan), escapeshellarg($artisanCommand)],
            array_map(static fn (string $argument) => escapeshellarg($argument), $arguments)
        );

        // setsid detaches from php-fpm so the page does not wait for the export or get killed with it.
        $command = 'setsid nohup '.implode(' ', $parts).' >> '.escapeshellarg($log).' 2>&1 < /dev/null &';

        $started = false;
        if (function_exists('shell_exec')) {
            shell_exec($command);
            $started = true;
        } elseif (function_exists('popen')) {
            $handle = popen($command, 'r');
            if ($handle !== false) {
                pclose($handle);
                $started = true;
            }
        }

        if (! $started) {
            throw new RuntimeException('Unable to start the background export. Run: php artisan '.$artisanCommand);
        }
    }
}
