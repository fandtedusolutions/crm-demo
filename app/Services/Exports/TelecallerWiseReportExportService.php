<?php

namespace App\Services\Exports;

use App\Models\CallAppLog;
use App\Models\User;
use App\Support\StreamingXlsxWriter;
use Illuminate\Support\Facades\DB;
use Throwable;

class TelecallerWiseReportExportService
{
    public function run(): void
    {
        $key = ExportProgress::TELECALLER_WISE;
        $current = ExportProgress::read($key);
        $pid = (int) ($current['pid'] ?? 0);
        if (($current['status'] ?? '') === 'processing' && $pid > 0 && $pid !== getmypid() && ExportProgress::pidIsRunning($pid)) {
            throw new \RuntimeException('This export is already running.');
        }

        ExportProgress::update($key, [
            'status' => 'processing',
            'percent' => 5,
            'processed' => 0,
            'total' => 0,
            'message' => 'Reading call logs from the start of the project…',
            'pid' => getmypid(),
            'started_at' => now()->toDateTimeString(),
            'finished_at' => null,
            'error' => null,
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

            $bounds = CallAppLog::query()
                ->selectRaw('MIN(started_at_ms) as min_ms, MAX(started_at_ms) as max_ms, COUNT(*) as total_calls')
                ->first();

            $fromLabel = CallAppLog::dateTimeFromMilliseconds(isset($bounds->min_ms) ? (int) $bounds->min_ms : null)?->format('d-m-Y') ?? 'Project start';
            $toLabel = now()->format('d-m-Y');

            ExportProgress::update($key, [
                'percent' => 35,
                'message' => 'Building the telecaller-wise summary…',
            ]);

            $query = CallAppLog::query();
            $rows = (clone $query)
                ->select(CallAppLog::telecallerAggregateColumns())
                ->groupBy('telecaller_id')
                ->orderByDesc('total_calls')
                ->get();

            $telecallers = User::query()
                ->whereIn('id', $rows->pluck('telecaller_id')->filter()->all())
                ->get(['id', 'name', 'email', 'phone'])
                ->keyBy('id');

            $connectedTotal = (int) (clone $query)
                ->select(DB::raw("COUNT(DISTINCT REGEXP_REPLACE(phone_number, '[^0-9]', '')) as connected_count"))
                ->value('connected_count');

            ExportProgress::update($key, [
                'percent' => 75,
                'processed' => $rows->count(),
                'total' => $rows->count(),
                'message' => 'Writing the Excel file…',
            ]);

            $writer = new StreamingXlsxWriter(ExportProgress::filePath($key));
            $writer->open();
            $writer->addRow(['Telecaller-wise call report']);
            $writer->addRow([
                'Period',
                $fromLabel.' to '.$toLabel,
                'Call logs',
                (string) ((int) ($bounds->total_calls ?? 0)),
            ]);
            $writer->addRow([]);
            $writer->addRow($this->headings(), true);

            $serial = 0;
            $incomingTotal = 0;
            $outgoingTotal = 0;

            foreach ($rows as $row) {
                $serial++;
                $telecaller = $telecallers->get($row->telecaller_id);
                $incomingTotal += (int) $row->incoming_calls;
                $outgoingTotal += (int) $row->outgoing_calls;
                $writer->addRow([
                    (string) $serial,
                    $telecaller?->name ?: 'Unknown',
                    $telecaller?->email ?: '-',
                    $telecaller?->phone ?: '-',
                    (string) (int) $row->total_calls,
                    (string) (int) $row->connected_calls,
                    (string) CallAppLog::attendedCallCount((int) $row->incoming_calls, (int) $row->outgoing_calls),
                    (string) (int) $row->incoming_calls,
                    (string) (int) $row->outgoing_calls,
                    (string) (int) $row->not_picked_calls,
                    (string) (int) $row->missed_calls,
                    (string) (int) $row->rejected_calls,
                    CallAppLog::formatDuration((int) $row->total_duration_seconds),
                    (string) (int) $row->total_duration_seconds,
                    (string) (int) $row->with_recording,
                    (string) (int) $row->recordings_uploaded,
                ]);
            }

            $writer->addRow([
                '',
                'Total',
                '',
                '',
                (string) (int) $rows->sum('total_calls'),
                (string) $connectedTotal,
                (string) CallAppLog::attendedCallCount($incomingTotal, $outgoingTotal),
                (string) $incomingTotal,
                (string) $outgoingTotal,
                (string) (int) $rows->sum('not_picked_calls'),
                (string) (int) $rows->sum('missed_calls'),
                (string) (int) $rows->sum('rejected_calls'),
                CallAppLog::formatDuration((int) $rows->sum('total_duration_seconds')),
                (string) (int) $rows->sum('total_duration_seconds'),
                (string) (int) $rows->sum('with_recording'),
                (string) (int) $rows->sum('recordings_uploaded'),
            ]);
            $writer->close();

            ExportProgress::update($key, [
                'status' => 'completed',
                'percent' => 100,
                'processed' => $rows->count(),
                'total' => $rows->count(),
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

    /**
     * @return array<int, string>
     */
    private function headings(): array
    {
        return [
            'S.No',
            'Telecaller',
            'Email',
            'Phone',
            'Total',
            'Connected (unique)',
            'Attended',
            'Incoming',
            'Outgoing',
            'Not Picked',
            'Missed',
            'Rejected',
            'Talk Time',
            'Talk Time (seconds)',
            'Recording',
            'Uploaded',
        ];
    }
}
