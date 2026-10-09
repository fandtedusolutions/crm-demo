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
    public static function start(string $key): array
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
        ]);

        try {
            self::spawn(ExportProgress::command($key));
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

    private static function spawn(string $artisanCommand): void
    {
        $php = self::phpBinary();
        $artisan = base_path('artisan');
        $logDirectory = storage_path('logs');
        if (! is_dir($logDirectory)) {
            mkdir($logDirectory, 0755, true);
        }

        $log = $logDirectory.'/'.str_replace(':', '-', $artisanCommand).'.log';

        if (PHP_OS_FAMILY === 'Windows') {
            $process = new Process([$php, $artisan, $artisanCommand], base_path());
            $process->setOptions(['create_new_console' => true]);
            $process->setTimeout(null);
            $process->start();

            return;
        }

        $command = 'nohup '
            .escapeshellarg($php).' '
            .escapeshellarg($artisan).' '
            .escapeshellarg($artisanCommand)
            .' >> '.escapeshellarg($log).' 2>&1 &';

        $process = Process::fromShellCommandline($command, base_path());
        $process->setTimeout(15);
        $process->run();

        if (! $process->isSuccessful()) {
            throw new RuntimeException(trim($process->getErrorOutput()) ?: 'Unable to start the background export. Run: php artisan '.$artisanCommand);
        }
    }
}
