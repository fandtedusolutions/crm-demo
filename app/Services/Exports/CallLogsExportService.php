<?php

namespace App\Services\Exports;

use App\Models\CallAppLog;
use App\Models\User;
use App\Support\StreamingXlsxWriter;
use Illuminate\Support\Facades\DB;
use Throwable;

class CallLogsExportService
{
    public function run(?string $startDate = null, ?string $endDate = null): void
    {
        $key = ExportProgress::CALL_LOGS;
        $current = ExportProgress::read($key);
        $pid = (int) ($current['pid'] ?? 0);
        if (($current['status'] ?? '') === 'processing' && $pid > 0 && $pid !== getmypid() && ExportProgress::pidIsRunning($pid)) {
            throw new \RuntimeException('This export is already running.');
        }

        $startDate = $startDate ?: '2024-01-01';
        $endDate = $endDate ?: now()->format('Y-m-d');

        ExportProgress::ensureWritableDirectory(ExportProgress::directory());
        ExportProgress::ensureWritableDirectory(dirname(ExportProgress::statusPath($key)));
        ExportProgress::update($key, [
            'status' => 'processing',
            'percent' => 0,
            'processed' => 0,
            'total' => 0,
            'message' => 'Counting call logs from '.$startDate.' to '.$endDate.'…',
            'pid' => getmypid(),
            'started_at' => now()->toDateTimeString(),
            'finished_at' => null,
            'error' => null,
            'filters' => [
                'start_date' => $startDate,
                'end_date' => $endDate,
            ],
        ]);

        register_shutdown_function(function () use ($key) {
            $status = ExportProgress::read($key);
            if (($status['status'] ?? '') !== 'processing' || (int) ($status['pid'] ?? 0) !== getmypid()) {
                return;
            }

            $error = error_get_last();
            ExportProgress::update($key, [
                'status' => 'failed',
                'message' => 'Export stopped unexpectedly.',
                'error' => $error['message'] ?? 'The process ended before the file was saved.',
                'finished_at' => now()->toDateTimeString(),
            ]);
        });

        try {
            @ini_set('memory_limit', '1024M');
            set_time_limit(0);
            DB::disableQueryLog();

            $path = ExportProgress::filePath($key);
            $processed = $this->writeFile($path, $startDate, $endDate, function (int $done, int $total) use ($key, $startDate, $endDate) {
                $percent = $total > 0 ? (int) floor(($done / $total) * 99) : 99;
                ExportProgress::update($key, [
                    'processed' => $done,
                    'total' => $total,
                    'percent' => max(1, min(99, $percent)),
                    'message' => 'Writing call logs '.$done.' of '.$total.' ('.$startDate.' to '.$endDate.')',
                ]);
            });

            ExportProgress::update($key, [
                'status' => 'completed',
                'percent' => 100,
                'processed' => $processed,
                'message' => 'Excel file is ready to download.',
                'finished_at' => now()->toDateTimeString(),
                'error' => null,
            ]);
        } catch (Throwable $exception) {
            ExportProgress::update($key, [
                'status' => 'failed',
                'message' => 'Export failed.',
                'error' => $exception->getMessage(),
                'finished_at' => now()->toDateTimeString(),
            ]);

            throw $exception;
        }
    }

    public function writeFile(string $path, ?string $startDate, ?string $endDate, ?callable $onProgress = null): int
    {
        $query = CallAppLog::query()->select([
            'id',
            'telecaller_id',
            'phone_number',
            'contact_name',
            'call_type',
            'remarks',
            'duration_seconds',
            'started_at_ms',
            'end_at_ms',
            'has_recording',
            'recording_uploaded',
            'device_id',
            'app_version',
        ]);

        if ($startDate && $endDate) {
            [$startMs, $endMs] = CallAppLog::millisecondRangeForDates($startDate, $endDate);
            $query->whereBetween('started_at_ms', [$startMs, $endMs]);
        }

        $total = (clone $query)->count();
        if ($onProgress) {
            $onProgress(0, $total);
        }

        $users = User::query()->get(['id', 'name', 'email'])->keyBy('id');
        $writer = new StreamingXlsxWriter($path);
        $writer->open();
        $writer->addRow([
            'Call logs',
            ($startDate ?: 'Project start').' to '.($endDate ?: now()->format('Y-m-d')),
            'Total',
            (string) $total,
        ]);
        $writer->addRow([]);
        $writer->addRow([
            'S.No', 'Telecaller', 'Email', 'Phone', 'Contact', 'Type', 'Remarks',
            'Duration', 'Duration (seconds)', 'Call Date', 'Call Time', 'End Date', 'End Time',
            'Recording', 'Uploaded', 'Device ID', 'App Version',
        ], true);

        $serial = 0;
        $query->reorder()->chunkById(2000, function ($calls) use ($writer, $users, &$serial, $total, $onProgress) {
            foreach ($calls as $call) {
                $serial++;
                $user = $users->get($call->telecaller_id);
                $started = CallAppLog::dateTimeFromMilliseconds((int) $call->started_at_ms);
                $ended = CallAppLog::dateTimeFromMilliseconds($call->end_at_ms ? (int) $call->end_at_ms : null);
                $writer->addRow([
                    (string) $serial,
                    $user?->name ?: 'N/A',
                    $user?->email ?: '-',
                    $call->phone_number ?: '-',
                    $call->contact_name ?: '-',
                    $call->call_type_label,
                    $call->remarks ?: '-',
                    CallAppLog::formatDuration((int) $call->duration_seconds),
                    (string) (int) $call->duration_seconds,
                    $started ? $started->format('d M Y') : '-',
                    $started ? $started->format('h:i A') : '-',
                    $ended ? $ended->format('d M Y') : '-',
                    $ended ? $ended->format('h:i A') : '-',
                    $call->has_recording ? 'Yes' : 'No',
                    $call->recording_uploaded ? 'Yes' : 'No',
                    $call->device_id ?: '-',
                    $call->app_version ?: '-',
                ]);
            }

            if ($onProgress) {
                $onProgress($serial, $total);
            }
        }, 'id');

        $writer->close();

        return $serial;
    }
}
