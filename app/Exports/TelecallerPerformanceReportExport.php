<?php

namespace App\Exports;

use App\Models\CallAppLog;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class TelecallerPerformanceReportExport
{
    public function __construct(
        protected $rows,
        protected array $summary,
        protected string $fromDate,
        protected string $toDate
    ) {
    }

    public function export(): Spreadsheet
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Telecaller Report');

        $row = 1;
        $sheet->setCellValue('A' . $row, 'Telecaller Performance Report');
        $sheet->getStyle('A' . $row)->getFont()->setBold(true)->setSize(16);
        $sheet->mergeCells('A' . $row . ':Q' . $row);
        $row += 2;

        $sheet->setCellValue('A' . $row, 'Report Period: ' . \Carbon\Carbon::parse($this->fromDate)->format('M d, Y') . ' to ' . \Carbon\Carbon::parse($this->toDate)->format('M d, Y'));
        $row += 2;

        $calls = $this->summary['call_grand_totals'] ?? [];
        $summaryLines = [
            'Total Leads' => $this->summary['total_leads'] ?? 0,
            'Active Leads' => $this->summary['active_leads'] ?? 0,
            'Converted Leads' => $this->summary['converted_leads'] ?? 0,
            'Conversion Rate' => ($this->summary['conversion_rate'] ?? 0) . '%',
            'Total Calls' => $calls['total_calls'] ?? 0,
            'Connected (Unique)' => $calls['connected_calls'] ?? 0,
            'Attended (In + Out)' => $calls['attended_calls'] ?? 0,
            'Incoming Calls' => $calls['incoming_calls'] ?? 0,
            'Outgoing Calls' => $calls['outgoing_calls'] ?? 0,
        ];

        foreach ($summaryLines as $label => $value) {
            $sheet->setCellValue('A' . $row, $label);
            $sheet->setCellValue('B' . $row, $value);
            $sheet->getStyle('A' . $row)->getFont()->setBold(true);
            $row++;
        }

        $row += 2;

        $headers = [
            'S.No', 'Telecaller', 'Phone', 'Team', 'Total Leads', 'Active Leads', 'Converted Leads', 'Conv. %',
            'Total Calls', 'Connected (Unique)', 'Attended', 'Incoming', 'Outgoing', 'Not Picked', 'Missed', 'Rejected', 'Talk Time',
        ];
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . $row, $header);
            $sheet->getStyle($col . $row)->getFont()->setBold(true);
            $col++;
        }
        $row++;

        $serial = 1;
        foreach ($this->rows as $telecaller) {
            $sheet->setCellValue('A' . $row, $serial++);
            $sheet->setCellValue('B' . $row, $telecaller->name);
            $sheet->setCellValue('C' . $row, $telecaller->phone ?? 'N/A');
            $sheet->setCellValue('D' . $row, $telecaller->team_name ?? 'No Team');
            $sheet->setCellValue('E' . $row, $telecaller->total_leads);
            $sheet->setCellValue('F' . $row, $telecaller->active_leads);
            $sheet->setCellValue('G' . $row, $telecaller->converted_leads);
            $sheet->setCellValue('H' . $row, $telecaller->conversion_rate . '%');
            $sheet->setCellValue('I' . $row, $telecaller->total_calls);
            $sheet->setCellValue('J' . $row, $telecaller->connected_calls);
            $sheet->setCellValue('K' . $row, $telecaller->attended_calls);
            $sheet->setCellValue('L' . $row, $telecaller->incoming_calls);
            $sheet->setCellValue('M' . $row, $telecaller->outgoing_calls);
            $sheet->setCellValue('N' . $row, $telecaller->not_picked_calls);
            $sheet->setCellValue('O' . $row, $telecaller->missed_calls);
            $sheet->setCellValue('P' . $row, $telecaller->rejected_calls);
            $sheet->setCellValue('Q' . $row, CallAppLog::formatDuration((int) $telecaller->total_duration_seconds));
            $row++;
        }

        foreach (range('A', 'Q') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        return $spreadsheet;
    }
}
