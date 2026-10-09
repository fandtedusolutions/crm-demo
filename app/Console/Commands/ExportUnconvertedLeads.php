<?php

namespace App\Console\Commands;

use App\Services\Exports\UnconvertedLeadsExportService;
use Illuminate\Console\Command;
use Throwable;

class ExportUnconvertedLeads extends Command
{
    protected $signature = 'exports:unconverted-leads';

    protected $description = 'Export every unconverted lead (is_converted = 0) from project start to an Excel file';

    public function handle(UnconvertedLeadsExportService $export): int
    {
        $this->info('Exporting unconverted leads. Progress is written for the Excel Exports page.');

        try {
            $export->run();
        } catch (Throwable $exception) {
            $this->error($exception->getMessage());

            return self::FAILURE;
        }

        $this->info('Unconverted leads Excel file is ready.');

        return self::SUCCESS;
    }
}
