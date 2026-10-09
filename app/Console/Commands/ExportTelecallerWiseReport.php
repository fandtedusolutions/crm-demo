<?php

namespace App\Console\Commands;

use App\Services\Exports\TelecallerWiseReportExportService;
use Illuminate\Console\Command;
use Throwable;

class ExportTelecallerWiseReport extends Command
{
    protected $signature = 'exports:telecaller-wise-report';

    protected $description = 'Export the telecaller-wise call report from project start through today to an Excel file';

    public function handle(TelecallerWiseReportExportService $export): int
    {
        $this->info('Exporting the telecaller-wise call report. Progress is written for the Excel Exports page.');

        try {
            $export->run();
        } catch (Throwable $exception) {
            $this->error($exception->getMessage());

            return self::FAILURE;
        }

        $this->info('Telecaller-wise report Excel file is ready.');

        return self::SUCCESS;
    }
}
