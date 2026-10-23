<?php

namespace App\Services\Exports;

use App\Helpers\PhoneNumberHelper;
use App\Models\ConvertedLead;
use App\Support\StreamingXlsxWriter;
use Carbon\Carbon;
use RuntimeException;
use Throwable;

class ConvertedLeadsExportService
{
    public function run(): void
    {
        $key = ExportProgress::CONVERTED_LEADS;
        ExportProgress::ensureWritableDirectory(ExportProgress::directory());
        ExportProgress::ensureWritableDirectory(dirname(ExportProgress::statusPath($key)));
        $this->guardAlreadyRunning($key);

        ExportProgress::update($key, [
            'status' => 'processing',
            'percent' => 0,
            'processed' => 0,
            'message' => 'Counting converted leads…',
            'pid' => getmypid(),
            'started_at' => now()->toDateTimeString(),
            'finished_at' => null,
            'error' => null,
        ]);

        $this->registerFailureHandler($key);

        try {
            @ini_set('memory_limit', '1024M');
            set_time_limit(0);

            $total = ConvertedLead::query()->count();
            ExportProgress::update($key, [
                'total' => $total,
                'percent' => $total === 0 ? 90 : 1,
                'message' => $total === 0
                    ? 'No converted leads found. Saving the workbook…'
                    : 'Writing '.$total.' converted leads…',
            ]);

            $writer = new StreamingXlsxWriter(ExportProgress::filePath($key));
            $writer->open();
            $writer->addRow(['Converted Leads Full Details Export']);
            $writer->addRow(['Generated', now()->format('d-m-Y h:i A'), 'Total Converted Leads', (string) $total]);
            $writer->addRow([]);
            $writer->addRow($this->headings(), true);

            $processed = 0;
            $serial = 0;

            ConvertedLead::query()
                ->with([
                    'lead.country:id,title',
                    'lead.team:id,name',
                    'lead.telecaller:id,name,team_id',
                    'lead.telecaller.team:id,name',
                    'lead.createdBy:id,name',
                    'lead.leadSource:id,title',
                    'course:id,title',
                    'subCourse:id,title',
                    'university:id,title',
                    'board:id,title',
                    'batch:id,title',
                    'admissionBatch:id,title',
                    'addonBatch:id,title',
                    'addonAdmissionBatch:id,title',
                    'faculty:id,name',
                    'academicAssistant:id,name',
                    'academicVerifiedBy:id,name',
                    'supportVerifiedBy:id,name',
                    'cancelledBy:id,name',
                    'studentDetails',
                    'leadDetail.addonCourse:id,title',
                    'leadDetail',
                    'invoices.payments',
                ])
                ->chunkById(250, function ($convertedLeads) use ($writer, &$processed, &$serial, $total, $key) {
                    foreach ($convertedLeads as $convertedLead) {
                        $serial++;
                        $writer->addRow($this->mapConvertedLead($convertedLead, $serial));
                        $processed++;
                    }

                    $percent = $total > 0 ? (int) floor(($processed / $total) * 99) : 99;
                    ExportProgress::update($key, [
                        'processed' => $processed,
                        'total' => $total,
                        'percent' => max(1, min(99, $percent)),
                        'message' => 'Writing leads '.$processed.' of '.$total,
                    ]);
                });

            $writer->close();

            ExportProgress::update($key, [
                'status' => 'completed',
                'percent' => 100,
                'processed' => $processed,
                'total' => $total,
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
    public function headings(): array
    {
        return [
            'S.No',
            'Lead ID',
            'Converted Date',
            'Register Number',
            'Student Name',
            'Phone Number',
            'Alternate / Personal Phone',
            'WhatsApp Number',
            'Parent Phone',
            'Father Contact',
            'Mother Contact',
            'Email',
            'Date of Birth',
            'Gender',
            'Father Name',
            'Mother Name',
            'Residential Address',
            'Place',
            'District',
            'State',
            'Pin Code',
            'Country',
            'Lead Type',
            'Team',
            'BDE / Telecaller',
            'Course',
            'Sub Course',
            'University / Board',
            'Batch',
            'Admission Batch',
            'Addon Course',
            'Addon Batch',
            'Addon Admission Batch',
            'Faculty',
            'Academic Assistant',
            'Academic Verification Status',
            'Academic Verified At',
            'Academic Verified By',
            'Academic Document Approved At',
            'Support Verification Status',
            'Support Verified At',
            'Support Verified By',
            'Finance Approval',
            'Status',
            'REG. FEE',
            'EXAM FEE',
            'ID CARD',
            'TMA',
            'RE-Mode',
            'Enrollment Number',
            'Total Invoiced Amount',
            'Total Paid Amount',
            'Pending Balance',
            'Payment Approval Status',
            'LMS Status',
            'LMS Shared At',
            'Admission Cancelled',
            'Cancelled By',
            'Cancelled At',
            'Cancel Remark',
            'Lead Created By',
            'Lead Source',
            'Remarks',
            'Record Created At',
        ];
    }

    /**
     * @return array<int, string>
     */
    private function mapConvertedLead(ConvertedLead $convertedLead, int $serial): array
    {
        $lead = $convertedLead->lead;
        $leadDetail = $convertedLead->leadDetail;
        $studentDetails = $convertedLead->studentDetails;

        // Converted date
        $convertedDate = '-';
        if ($studentDetails && $studentDetails->converted_date) {
            try {
                $convertedDate = Carbon::parse($studentDetails->converted_date)->format('d-m-Y');
            } catch (Throwable) {
                $convertedDate = (string) $studentDetails->converted_date;
            }
        } elseif ($convertedLead->created_at) {
            $convertedDate = $convertedLead->created_at->format('d-m-Y');
        }

        // DOB
        $dobDisplay = '-';
        $dobVal = $convertedLead->dob ?: ($leadDetail?->date_of_birth);
        if ($dobVal) {
            $ts = strtotime((string) $dobVal);
            $dobDisplay = $ts ? date('d-m-Y', $ts) : (string) $dobVal;
        }

        // Phone numbers
        $phone = PhoneNumberHelper::display($convertedLead->code, $convertedLead->phone);
        $personalPhone = ($leadDetail && $leadDetail->personal_number)
            ? PhoneNumberHelper::display($leadDetail->personal_code, $leadDetail->personal_number)
            : '-';
        $whatsapp = ($leadDetail && $leadDetail->whatsapp_number)
            ? PhoneNumberHelper::display($leadDetail->whatsapp_code, $leadDetail->whatsapp_number)
            : ($lead && $lead->whatsapp
                ? PhoneNumberHelper::display($lead->whatsapp_code, $lead->whatsapp)
                : '-');
        $parentPhone = ($leadDetail && $leadDetail->parents_number)
            ? PhoneNumberHelper::display($leadDetail->parents_code, $leadDetail->parents_number)
            : '-';
        $fatherContact = ($leadDetail && $leadDetail->father_contact_number)
            ? PhoneNumberHelper::display($leadDetail->father_contact_code, $leadDetail->father_contact_number)
            : '-';
        $motherContact = ($leadDetail && $leadDetail->mother_contact_number)
            ? PhoneNumberHelper::display($leadDetail->mother_contact_code, $leadDetail->mother_contact_number)
            : '-';

        // Type & Team
        $team = $lead?->team?->name ?: ($lead?->telecaller?->team?->name ?: '-');
        $leadType = ((int) ($convertedLead->is_b2b ?? 0) === 1)
            ? ('B2B'.($lead?->team?->name ? ' ('.$lead->team->name.')' : ''))
            : 'In House';

        // Invoices calculation
        $totalInvoiced = 0.0;
        $totalPaid = 0.0;
        $hasPendingApproval = false;
        if ($convertedLead->invoices) {
            foreach ($convertedLead->invoices as $invoice) {
                $totalInvoiced += (float) ($invoice->total_amount ?? 0);
                if ($invoice->payments) {
                    foreach ($invoice->payments as $payment) {
                        $totalPaid += (float) ($payment->amount ?? 0);
                        if (($payment->status ?? '') === 'Pending Approval') {
                            $hasPendingApproval = true;
                        }
                    }
                }
            }
        }
        $pendingBalance = max(0.0, $totalInvoiced - $totalPaid);

        // Academic document approved timestamp
        $academicDocApproved = '-';
        if ($leadDetail && $leadDetail->reviewed_at) {
            try {
                $academicDocApproved = Carbon::parse($leadDetail->reviewed_at)->format('d-m-Y h:i A');
            } catch (Throwable) {
                $academicDocApproved = (string) $leadDetail->reviewed_at;
            }
        }

        return [
            (string) $serial,
            $convertedLead->lead_id ? (string) $convertedLead->lead_id : ($lead ? (string) $lead->id : '-'),
            $convertedDate,
            $convertedLead->register_number ?: ($studentDetails?->registration_number ?: '-'),
            $convertedLead->name ?: ($leadDetail?->student_name ?: ($lead?->title ?: '-')),
            $phone,
            $personalPhone,
            $whatsapp,
            $parentPhone,
            $fatherContact,
            $motherContact,
            $convertedLead->email ?: ($leadDetail?->email ?: ($lead?->email ?: '-')),
            $dobDisplay,
            $leadDetail?->gender ?: ($lead?->gender ?: '-'),
            $leadDetail?->father_name ?: '-',
            $leadDetail?->mother_name ?: '-',
            $leadDetail?->residential_address ?: ($lead?->address ?: '-'),
            $lead?->place ?: ($leadDetail?->location ?: '-'),
            $leadDetail?->district ?: '-',
            $leadDetail?->state ?: '-',
            $leadDetail?->pin_code ?: '-',
            $lead?->country?->title ?: '-',
            $leadType,
            $team,
            $lead?->telecaller?->name ?: 'Unassigned',
            $convertedLead->course?->title ?: ($lead?->course?->title ?: '-'),
            $convertedLead->subCourse?->title ?: '-',
            $convertedLead->university?->title ?: ($convertedLead->board?->title ?: '-'),
            $convertedLead->batch?->title ?: '-',
            $convertedLead->admissionBatch?->title ?: '-',
            $leadDetail?->addonCourse?->title ?: '-',
            $convertedLead->addonBatch?->title ?: '-',
            $convertedLead->addonAdmissionBatch?->title ?: '-',
            $convertedLead->faculty?->name ?: '-',
            $convertedLead->academicAssistant?->name ?: '-',
            $convertedLead->is_academic_verified ? 'Verified' : 'Pending',
            $convertedLead->academic_verified_at ? $convertedLead->academic_verified_at->format('d-m-Y h:i A') : '-',
            $convertedLead->academicVerifiedBy?->name ?: '-',
            $academicDocApproved,
            $convertedLead->is_support_verified ? 'Verified' : 'Pending',
            $convertedLead->support_verified_at ? $convertedLead->support_verified_at->format('d-m-Y h:i A') : '-',
            $convertedLead->supportVerifiedBy?->name ?: '-',
            $convertedLead->finance_approval ?? 'Pending',
            $convertedLead->status ?? '-',
            $studentDetails?->reg_fee ?? '-',
            $studentDetails?->exam_fee ?? '-',
            $studentDetails?->id_card ?? '-',
            $studentDetails?->tma ?? '-',
            $studentDetails?->re_mode ?: ($leadDetail?->re_mode ?: '-'),
            $studentDetails?->enroll_no ?: ($studentDetails?->enrollment_number ?: '-'),
            number_format($totalInvoiced, 2),
            number_format($totalPaid, 2),
            number_format($pendingBalance, 2),
            $hasPendingApproval ? 'Pending Approval' : 'No Pending Approval',
            $convertedLead->is_shared_to_lms ? 'Already sent to LMS' : 'Not sent',
            $convertedLead->shared_to_lms_at ? $convertedLead->shared_to_lms_at->format('d-m-Y h:i A') : '-',
            $convertedLead->is_cancelled ? 'Yes' : 'No',
            $convertedLead->cancelledBy?->name ?: '-',
            $convertedLead->cancelled_at ? $convertedLead->cancelled_at->format('d-m-Y h:i A') : '-',
            $convertedLead->cancel_remark ?: '-',
            $lead?->createdBy?->name ?: '-',
            $lead?->leadSource?->title ?: '-',
            $convertedLead->remarks ?: ($lead?->remarks ?: '-'),
            $convertedLead->created_at ? $convertedLead->created_at->format('d-m-Y h:i A') : '-',
        ];
    }

    private function guardAlreadyRunning(string $key): void
    {
        $current = ExportProgress::read($key);
        $pid = (int) ($current['pid'] ?? 0);
        if (($current['status'] ?? '') === 'processing' && $pid > 0 && $pid !== getmypid() && ExportProgress::pidIsRunning($pid)) {
            throw new RuntimeException('This export is already running.');
        }
    }

    private function registerFailureHandler(string $key): void
    {
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
    }
}
