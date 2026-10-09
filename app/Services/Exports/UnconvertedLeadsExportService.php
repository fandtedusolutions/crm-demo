<?php

namespace App\Services\Exports;

use App\Helpers\PhoneNumberHelper;
use App\Models\Lead;
use App\Support\StreamingXlsxWriter;
use Throwable;

class UnconvertedLeadsExportService
{
    public function run(): void
    {
        $key = ExportProgress::UNCONVERTED_LEADS;
        $this->guardAlreadyRunning($key);

        ExportProgress::update($key, [
            'status' => 'processing',
            'percent' => 0,
            'processed' => 0,
            'message' => 'Counting unconverted leads…',
            'pid' => getmypid(),
            'started_at' => now()->toDateTimeString(),
            'finished_at' => null,
            'error' => null,
        ]);

        $this->registerFailureHandler($key);

        try {
            @ini_set('memory_limit', '1024M');
            set_time_limit(0);

            $total = Lead::query()->where('is_converted', 0)->count();
            ExportProgress::update($key, [
                'total' => $total,
                'percent' => $total === 0 ? 90 : 1,
                'message' => $total === 0
                    ? 'No unconverted leads found. Saving the workbook…'
                    : 'Writing '.$total.' unconverted leads…',
            ]);

            $writer = new StreamingXlsxWriter(ExportProgress::filePath($key));
            $writer->open();
            $writer->addRow(['Unconverted leads (is_converted = 0)']);
            $writer->addRow(['Period', 'From project start to '.now()->format('d-m-Y h:i A'), 'Total leads', (string) $total]);
            $writer->addRow([]);
            $writer->addRow($this->headings(), true);

            $processed = 0;
            $serial = 0;

            Lead::query()
                ->select([
                    'id', 'title', 'code', 'phone', 'email', 'lead_status_id', 'lead_source_id',
                    'course_id', 'telecaller_id', 'team_id', 'place', 'rating', 'interest_status',
                    'followup_date', 'remarks', 'marketing_remarks', 'is_converted', 'created_at',
                    'gender', 'age', 'whatsapp', 'whatsapp_code', 'qualification', 'country_id',
                    'address', 'first_created_at', 'is_b2b',
                ])
                ->where('is_converted', 0)
                ->with([
                    'leadStatus:id,title',
                    'leadSource:id,title',
                    'course:id,title',
                    'telecaller:id,name,team_id',
                    'telecaller.team:id,name',
                    'team:id,name',
                    'country:id,title',
                    'studentDetails.sslcCertificates:id,lead_detail_id,verification_status',
                    'plusTwoFollowUpQuestionnaire:id,lead_id,created_at',
                    'latestReasonActivity',
                ])
                ->chunkById(250, function ($leads) use ($writer, &$processed, &$serial, $total, $key) {
                    foreach ($leads as $lead) {
                        $serial++;
                        $writer->addRow($this->mapLead($lead, $serial));
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
    private function headings(): array
    {
        return [
            'S.No',
            'Lead ID',
            'Created At',
            'First Created At',
            'Type',
            'Team',
            'Name',
            'Profile %',
            'Phone',
            'WhatsApp',
            'Email',
            'Gender',
            'Age',
            'Qualification',
            'Country',
            'Address',
            'Status',
            'Interest',
            'Rating',
            'Source',
            'Course',
            'Telecaller',
            'Place',
            'Followup Date',
            'Last Reason',
            'Remarks',
            'Marketing Remarks',
            'Registration',
            'Registration Status',
            'Documents',
            'Date',
            'Time',
            'Converted',
        ];
    }

    /**
     * @return array<int, string>
     */
    private function mapLead(Lead $lead, int $serial): array
    {
        $team = $lead->team?->name ?: $lead->telecaller?->team?->name;
        $registration = $this->registrationLabel($lead);

        return [
            (string) $serial,
            (string) $lead->id,
            $lead->created_at ? $lead->created_at->format('d-m-Y h:i A') : '-',
            $lead->first_created_at ? $lead->first_created_at->format('d-m-Y h:i A') : '-',
            ((int) ($lead->is_b2b ?? 0) === 1) ? 'B2B' : 'In House',
            $team ?: '-',
            $lead->title ?: '-',
            $this->profilePercent($lead).'%',
            PhoneNumberHelper::display($lead->code, $lead->phone),
            PhoneNumberHelper::display($lead->whatsapp_code, $lead->whatsapp),
            $lead->email ?: '-',
            $lead->gender ?: '-',
            $lead->age !== null && $lead->age !== '' ? (string) $lead->age : '-',
            $lead->qualification ?: '-',
            $lead->country?->title ?: '-',
            $lead->address ?: '-',
            $lead->leadStatus?->title ?: '-',
            $this->interestLabel($lead->interest_status),
            $lead->rating ? ($lead->rating.'/10') : 'Not Rated',
            $lead->leadSource?->title ?: '-',
            $lead->course?->title ?: '-',
            $lead->telecaller?->name ?: 'Unassigned',
            $lead->place ?: '-',
            $lead->followup_date ? $lead->followup_date->format('d-m-Y') : '-',
            $this->lastReason($lead),
            $lead->remarks ?: '-',
            $lead->marketing_remarks ?: '-',
            $registration['form'],
            $registration['status'],
            $registration['documents'],
            $lead->created_at ? $lead->created_at->format('d-m-Y') : '-',
            $lead->created_at ? $lead->created_at->format('h:i A') : '-',
            'No',
        ];
    }

    private function interestLabel(mixed $interest): string
    {
        return match ((int) $interest) {
            1 => 'Hot',
            2 => 'Warm',
            3 => 'Cold',
            default => $interest ? 'Cold' : 'Not Set',
        };
    }

    private function profilePercent(Lead $lead): int
    {
        $fields = [
            'title', 'gender', 'age', 'phone', 'code', 'whatsapp', 'whatsapp_code',
            'email', 'qualification', 'country_id', 'interest_status', 'lead_status_id',
            'lead_source_id', 'address', 'telecaller_id', 'team_id', 'place',
        ];
        $completed = 0;
        foreach ($fields as $field) {
            if (! empty($lead->{$field})) {
                $completed++;
            }
        }

        return (int) round(($completed / count($fields)) * 100);
    }

    private function lastReason(Lead $lead): string
    {
        $reason = trim((string) ($lead->latestReasonActivity?->reason ?? ''));

        return $reason !== '' ? $reason : '-';
    }

    /**
     * @return array{form: string, status: string, documents: string}
     */
    private function registrationLabel(Lead $lead): array
    {
        if ($lead->studentDetails) {
            $documents = $lead->studentDetails->getDocumentVerificationStatus();

            return [
                'form' => 'Form Submitted',
                'status' => $lead->studentDetails->status ? ucfirst((string) $lead->studentDetails->status) : '-',
                'documents' => $documents === 'verified' ? 'Documents Verified' : ($documents === 'pending' ? 'Documents Pending' : '-'),
            ];
        }

        if ($lead->plusTwoFollowUpQuestionnaire) {
            return [
                'form' => 'Questionnaire Submitted',
                'status' => 'Plus Two Follow-Up',
                'documents' => '-',
            ];
        }

        return [
            'form' => 'Not Submitted',
            'status' => '-',
            'documents' => '-',
        ];
    }

    private function guardAlreadyRunning(string $key): void
    {
        $current = ExportProgress::read($key);
        $pid = (int) ($current['pid'] ?? 0);
        if (($current['status'] ?? '') === 'processing' && $pid > 0 && $pid !== getmypid() && ExportProgress::pidIsRunning($pid)) {
            throw new \RuntimeException('This export is already running.');
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
