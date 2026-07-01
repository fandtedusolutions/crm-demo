<?php

namespace App\Exports;

use App\Models\ConvertedLead;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class ConvertedLeadsExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths
{
    protected $convertedLeads;

    public function __construct($convertedLeads)
    {
        $this->convertedLeads = $convertedLeads;
    }

    public function collection()
    {
        return $this->convertedLeads;
    }

    public function headings(): array
    {
        return [
            'S.No',
            'Academic',
            'Support',
            'Converted Date',
            'Register Number',
            'Name',
            'BDE Name',
            'Phone',
            'DOB',
            'WhatsApp',
            'Parent Phone',
            'Course',
            'Batch',
            'Admission Batch',
            'Status',
            'Cancelled By',
            'REG. FEE',
            'Mail',
            'Academic Document Approved',
            'Academic Verified At',
            'Support Verified At',
            'Lead Created By',
            'Pending Payment',
        ];
    }

    public function map($convertedLead): array
    {
        static $serialNumber = 0;
        $serialNumber++;

        // Calculate pending payment
        $pendingPayment = 0;
        if ($convertedLead->invoices) {
            foreach ($convertedLead->invoices as $invoice) {
                $totalPaid = $invoice->payments ? $invoice->payments->sum('amount') : 0;
                $pendingPayment += ($invoice->total_amount - $totalPaid);
            }
        }

        return [
            $serialNumber,
            $convertedLead->is_academic_verified ? 'Yes' : 'No',
            $convertedLead->is_support_verified ? 'Yes' : 'No',
            $convertedLead->created_at ? $convertedLead->created_at->format('d-m-Y') : '-',
            $convertedLead->register_number ?? '-',
            $convertedLead->name ?? '-',
            $convertedLead->lead && $convertedLead->lead->telecaller ? $convertedLead->lead->telecaller->name : '-',
            $convertedLead->code && $convertedLead->phone ? ($convertedLead->code . $convertedLead->phone) : ($convertedLead->phone ?? '-'),
            $convertedLead->dob ? \Carbon\Carbon::parse($convertedLead->dob)->format('d-m-Y') : '-',
            ($convertedLead->leadDetail && $convertedLead->leadDetail->whatsapp_number)
                ? ($convertedLead->leadDetail->whatsapp_code . $convertedLead->leadDetail->whatsapp_number)
                : '-',
            ($convertedLead->leadDetail && $convertedLead->leadDetail->parents_number)
                ? ($convertedLead->leadDetail->parents_code . $convertedLead->leadDetail->parents_number)
                : '-',
            $convertedLead->course ? $convertedLead->course->title : '-',
            $convertedLead->batch ? $convertedLead->batch->title : '-',
            $convertedLead->admissionBatch ? $convertedLead->admissionBatch->title : '-',
            $convertedLead->status ?? '-',
            $convertedLead->cancelledBy ? $convertedLead->cancelledBy->name : '-',
            $convertedLead->studentDetails ? ($convertedLead->studentDetails->reg_fee ?? '-') : '-',
            $convertedLead->email ?? '-',
            $convertedLead->leadDetail && $convertedLead->leadDetail->reviewed_at
                ? \Carbon\Carbon::parse($convertedLead->leadDetail->reviewed_at)->format('d-m-Y h:i A')
                : '-',
            $convertedLead->academic_verified_at ? \Carbon\Carbon::parse($convertedLead->academic_verified_at)->format('d-m-Y h:i A') : '-',
            $convertedLead->support_verified_at ? \Carbon\Carbon::parse($convertedLead->support_verified_at)->format('d-m-Y h:i A') : '-',
            $convertedLead->lead && $convertedLead->lead->createdBy ? $convertedLead->lead->createdBy->name : '-',
            number_format($pendingPayment, 2),
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold with background color
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4472C4']
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER
                ]
            ],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 8,  // S.No
            'B' => 12, // Academic
            'C' => 12, // Support
            'D' => 22, // Academic Document Approved
            'E' => 15, // Converted Date
            'F' => 20, // Academic Verified At
            'G' => 20, // Support Verified At
            'H' => 18, // Register Number
            'I' => 12, // DOB
            'J' => 25, // Name
            'K' => 15, // Phone
            'L' => 15, // WhatsApp
            'M' => 15, // Parent Phone
            'N' => 25, // Course
            'O' => 20, // Batch
            'P' => 20, // Admission Batch
            'Q' => 15, // Status
            'R' => 20, // Cancelled By
            'S' => 12, // REG. FEE
            'T' => 25, // Mail
            'U' => 15, // Pending Payment
        ];
    }
}

