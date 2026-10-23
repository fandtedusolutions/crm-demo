<?php

namespace App\Console\Commands;

use App\Services\Exports\ConvertedLeadsExportService;
use Illuminate\Console\Command;
use Throwable;

class ExportConvertedLeads extends Command
{
    protected $signature = 'exports:converted-leads';

    protected $description = 'Export full converted leads details from project start to an Excel file stored on the server';

    public function handle(ConvertedLeadsExportService $export): int
    {
        $this->info('Exporting converted leads. Progress is written to server storage.');

        try {
            $export->run();
        } catch (Throwable $exception) {
            $this->error($exception->getMessage());

            return self::FAILURE;
        }

        $this->info('Converted leads Excel file is ready.');

        return self::SUCCESS;
    }
}
