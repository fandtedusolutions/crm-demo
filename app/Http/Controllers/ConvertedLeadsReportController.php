<?php

namespace App\Http\Controllers;

use App\Helpers\PhoneNumberHelper;
use App\Helpers\RoleHelper;
use App\Models\ConvertedLead;
use App\Models\Course;
use App\Models\LeadSource;
use App\Models\LeadStatus;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ConvertedLeadsReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('custom.auth');
    }

    public function index(Request $request)
    {
        if (! $this->canAccess()) {
            abort(403, 'Access denied.');
        }

        $fromDate = $request->get('date_from', Carbon::now()->subDays(30)->format('Y-m-d'));
        $toDate = $request->get('date_to', Carbon::now()->format('Y-m-d'));

        $filters = [
            'lead_source_id' => $request->get('lead_source_id'),
            'course_id' => $request->get('course_id'),
            'lead_status_id' => $request->get('lead_status_id'),
            'first_lead_source_id' => $request->get('first_lead_source_id'),
            'first_lead_course_id' => $request->get('first_lead_course_id'),
            'first_lead_status_id' => $request->get('first_lead_status_id'),
            'is_b2b' => $request->get('is_b2b'),
        ];

        $leadSources = LeadSource::where('is_active', true)->orderBy('title')->get(['id', 'title']);
        $courses = Course::where('is_active', true)->orderBy('title')->get(['id', 'title']);
        $leadStatuses = LeadStatus::where('is_active', true)->orderBy('title')->get(['id', 'title', 'color']);

        return view('admin.reports.converted-leads-report', compact(
            'fromDate',
            'toDate',
            'filters',
            'leadSources',
            'courses',
            'leadStatuses'
        ));
    }

    public function getData(Request $request): JsonResponse
    {
        if (! $this->canAccess()) {
            return response()->json(['message' => 'Access denied.'], 403);
        }

        $fromDate = $request->get('date_from', Carbon::now()->subDays(30)->format('Y-m-d'));
        $toDate = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        $filters = $this->extractFilters($request);

        $emptyFilters = [
            'lead_source_id' => null,
            'course_id' => null,
            'lead_status_id' => null,
            'first_lead_source_id' => null,
            'first_lead_course_id' => null,
            'first_lead_status_id' => null,
            'is_b2b' => null,
        ];

        $recordsTotal = $this->buildQuery($fromDate, $toDate, $emptyFilters)->count();

        $query = $this->buildQuery($fromDate, $toDate, $filters);

        if ($request->filled('search.value')) {
            $search = trim((string) $request->input('search.value'));
            if ($search !== '') {
                $query->where(function ($q) use ($search) {
                    $q->where('converted_leads.name', 'like', "%{$search}%")
                        ->orWhere('converted_leads.phone', 'like', "%{$search}%")
                        ->orWhereHas('lead', function ($leadQuery) use ($search) {
                            $leadQuery->withoutGlobalScope('exclude_pullbacked')
                                ->where(function ($inner) use ($search) {
                                    $inner->where('title', 'like', "%{$search}%")
                                        ->orWhere('phone', 'like', "%{$search}%")
                                        ->orWhereHas('telecaller', function ($telecallerQuery) use ($search) {
                                            $telecallerQuery->where('name', 'like', "%{$search}%");
                                        })
                                        ->orWhereHas('createdBy', function ($createdByQuery) use ($search) {
                                            $createdByQuery->where('name', 'like', "%{$search}%");
                                        });
                                });
                        });
                });
            }
        }

        $recordsFiltered = (clone $query)->count();

        $orderColumns = [
            0 => 'converted_leads.id',
            1 => 'converted_leads.created_at',
            2 => 'converted_leads.name',
            3 => 'converted_leads.id',
            4 => 'converted_leads.phone',
            5 => 'converted_leads.id',
            6 => 'converted_leads.course_id',
            7 => 'converted_leads.id',
            8 => 'converted_leads.id',
            9 => 'converted_leads.id',
            10 => 'converted_leads.id',
            11 => 'converted_leads.id',
            12 => 'converted_leads.id',
            13 => 'converted_leads.id',
            14 => 'converted_leads.id',
        ];

        $orderColumnIndex = (int) $request->input('order.0.column', 1);
        $orderDir = strtolower((string) $request->input('order.0.dir', 'desc')) === 'asc' ? 'asc' : 'desc';
        $orderColumn = $orderColumns[$orderColumnIndex] ?? 'converted_leads.created_at';
        $query->orderBy($orderColumn, $orderDir);

        $start = (int) $request->input('start', 0);
        $length = (int) $request->input('length', 25);
        if ($length === -1) {
            $length = max($recordsFiltered, 1);
        }

        $rows = $query->skip($start)->take($length)->get();

        $data = [];
        foreach ($rows as $index => $convertedLead) {
            $lead = $convertedLead->lead;

            $typeHtml = ($lead && $lead->is_b2b)
                ? '<span class="badge bg-info">B2B</span>'
                : '<span class="badge bg-secondary">In House</span>';

            $statusHtml = 'N/A';
            if ($lead && $lead->leadStatus) {
                $color = e($lead->leadStatus->color ?? '#6c757d');
                $title = e($lead->leadStatus->title);
                $statusHtml = '<span class="badge" style="background-color: ' . $color . '">' . $title . '</span>';
            }

            $firstStatusHtml = 'N/A';
            if ($lead && $lead->firstLeadStatus) {
                $color = e($lead->firstLeadStatus->color ?? '#6c757d');
                $title = e($lead->firstLeadStatus->title);
                $firstStatusHtml = '<span class="badge" style="background-color: ' . $color . '">' . $title . '</span>';
            }

            $data[] = [
                $start + $index + 1,
                optional($convertedLead->created_at)->format('d M Y H:i') ?: 'N/A',
                e($convertedLead->name ?: optional($lead)->title ?: 'N/A'),
                e(optional(optional($lead)->telecaller)->name ?: 'N/A'),
                e(PhoneNumberHelper::display($convertedLead->code, $convertedLead->phone)),
                $typeHtml,
                e(optional(optional($lead)->course)->title ?: optional($convertedLead->course)->title ?: 'N/A'),
                e(optional(optional($lead)->createdBy)->name ?: 'N/A'),
                optional(optional($lead)->first_created_at)->format('d M Y H:i') ?: 'N/A',
                optional(optional($lead)->created_at)->format('d M Y H:i') ?: 'N/A',
                $statusHtml,
                e(optional(optional($lead)->leadSource)->title ?: 'N/A'),
                e(optional(optional($lead)->firstLeadSource)->title ?: 'N/A'),
                e(optional(optional($lead)->firstLeadCourse)->title ?: 'N/A'),
                $firstStatusHtml,
            ];
        }

        return response()->json([
            'draw' => (int) $request->input('draw'),
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data,
        ]);
    }

    public function exportExcel(Request $request)
    {
        if (! $this->canAccess()) {
            abort(403, 'Access denied.');
        }

        $fromDate = $request->get('date_from', Carbon::now()->subDays(30)->format('Y-m-d'));
        $toDate = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        $filters = $this->extractFilters($request);

        $rows = $this->buildQuery($fromDate, $toDate, $filters)
            ->orderByDesc('converted_leads.created_at')
            ->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Converted Leads Report');

        $sheet->setCellValue('A1', 'Converted Leads Report');
        $sheet->setCellValue('A2', 'Date Range: ' . $fromDate . ' to ' . $toDate);
        $sheet->setCellValue('A3', 'Generated: ' . now()->format('Y-m-d H:i:s'));

        $headers = [
            'A5' => 'Converted Date',
            'B5' => 'Name',
            'C5' => 'BDE Name',
            'D5' => 'Phone',
            'E5' => 'Type',
            'F5' => 'Course',
            'G5' => 'Lead Created By',
            'H5' => 'Lead First Created At',
            'I5' => 'Lead Created At',
            'J5' => 'Lead Status',
            'K5' => 'Lead Source',
            'L5' => 'First Lead Source',
            'M5' => 'First Lead Course',
            'N5' => 'First Lead Status',
        ];

        foreach ($headers as $cell => $label) {
            $sheet->setCellValue($cell, $label);
        }

        $row = 6;
        foreach ($rows as $convertedLead) {
            $lead = $convertedLead->lead;
            $sheet->setCellValue('A' . $row, optional($convertedLead->created_at)->format('Y-m-d H:i:s'));
            $sheet->setCellValue('B' . $row, $convertedLead->name ?: optional($lead)->title);
            $sheet->setCellValue('C' . $row, optional(optional($lead)->telecaller)->name);
            $sheet->setCellValue('D' . $row, PhoneNumberHelper::display($convertedLead->code, $convertedLead->phone));
            $sheet->setCellValue('E' . $row, ($lead && $lead->is_b2b) ? 'B2B' : 'In House');
            $sheet->setCellValue('F' . $row, optional(optional($lead)->course)->title ?: optional($convertedLead->course)->title);
            $sheet->setCellValue('G' . $row, optional(optional($lead)->createdBy)->name);
            $sheet->setCellValue('H' . $row, optional(optional($lead)->first_created_at)->format('Y-m-d H:i:s'));
            $sheet->setCellValue('I' . $row, optional(optional($lead)->created_at)->format('Y-m-d H:i:s'));
            $sheet->setCellValue('J' . $row, optional(optional($lead)->leadStatus)->title);
            $sheet->setCellValue('K' . $row, optional(optional($lead)->leadSource)->title);
            $sheet->setCellValue('L' . $row, optional(optional($lead)->firstLeadSource)->title);
            $sheet->setCellValue('M' . $row, optional(optional($lead)->firstLeadCourse)->title);
            $sheet->setCellValue('N' . $row, optional(optional($lead)->firstLeadStatus)->title);
            $row++;
        }

        foreach (range('A', 'N') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);
        $filename = 'converted-leads-report-' . $fromDate . '-to-' . $toDate . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $writer->save('php://output');
        exit;
    }

    private function extractFilters(Request $request): array
    {
        return [
            'lead_source_id' => $request->get('lead_source_id'),
            'course_id' => $request->get('course_id'),
            'lead_status_id' => $request->get('lead_status_id'),
            'first_lead_source_id' => $request->get('first_lead_source_id'),
            'first_lead_course_id' => $request->get('first_lead_course_id'),
            'first_lead_status_id' => $request->get('first_lead_status_id'),
            'is_b2b' => $request->get('is_b2b'),
        ];
    }

    private function buildQuery(string $fromDate, string $toDate, array $filters)
    {
        $query = ConvertedLead::query()
            ->select('converted_leads.*')
            ->with([
                'course:id,title',
                'lead' => function ($q) {
                    $q->withoutGlobalScope('exclude_pullbacked')
                        ->with([
                            'telecaller:id,name',
                            'createdBy:id,name',
                            'course:id,title',
                            'leadStatus:id,title,color',
                            'leadSource:id,title',
                            'firstLeadSource:id,title',
                            'firstLeadCourse:id,title',
                            'firstLeadStatus:id,title,color',
                        ]);
                },
            ])
            ->whereBetween('converted_leads.created_at', [$fromDate . ' 00:00:00', $toDate . ' 23:59:59']);

        $needsLeadJoin = $filters['lead_source_id']
            || $filters['course_id']
            || $filters['lead_status_id']
            || $filters['first_lead_source_id']
            || $filters['first_lead_course_id']
            || $filters['first_lead_status_id']
            || ($filters['is_b2b'] !== null && $filters['is_b2b'] !== '');

        if ($needsLeadJoin) {
            $query->whereHas('lead', function ($q) use ($filters) {
                $q->withoutGlobalScope('exclude_pullbacked');

                if (! empty($filters['lead_source_id'])) {
                    $q->where('lead_source_id', $filters['lead_source_id']);
                }
                if (! empty($filters['course_id'])) {
                    $q->where('course_id', $filters['course_id']);
                }
                if (! empty($filters['lead_status_id'])) {
                    $q->where('lead_status_id', $filters['lead_status_id']);
                }
                if (! empty($filters['first_lead_source_id'])) {
                    $q->where('first_lead_source_id', $filters['first_lead_source_id']);
                }
                if (! empty($filters['first_lead_course_id'])) {
                    $q->where('first_lead_course_id', $filters['first_lead_course_id']);
                }
                if (! empty($filters['first_lead_status_id'])) {
                    $q->where('first_lead_status_id', $filters['first_lead_status_id']);
                }
                if ($filters['is_b2b'] !== null && $filters['is_b2b'] !== '') {
                    $q->where('is_b2b', (int) $filters['is_b2b']);
                }
            });
        }

        return $query;
    }

    private function canAccess(): bool
    {
        return RoleHelper::is_admin_or_super_admin()
            || RoleHelper::is_general_manager()
            || RoleHelper::is_auditor()
            || RoleHelper::is_team_lead()
            || RoleHelper::is_senior_manager();
    }
}
