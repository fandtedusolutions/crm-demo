<?php

namespace App\Console\Commands;

use App\Services\Exports\CallLogsExportService;
use Illuminate\Console\Command;
use Throwable;

class ExportCallLogs extends Command
{
    protected $signature = 'exports:call-logs {--start=2024-01-01} {--end=}';

    protected $description = 'Export call logs to Excel. Defaults to 2024-01-01 through today.';

    public function handle(CallLogsExportService $export): int
    {
        $start = (string) $this->option('start');
        $end = (string) ($this->option('end') ?: now()->format('Y-m-d'));

        $this->info('Exporting call logs from '.$start.' to '.$end.'. Progress is written for the Excel Exports page.');

        try {
            $export->run($start, $end);
        } catch (Throwable $exception) {
            $this->error($exception->getMessage());

            return self::FAILURE;
        }

        $this->info('Call logs Excel file is ready.');

        return self::SUCCESS;
    }
}
