<?php

namespace App\Http\Controllers\Concerns;

use App\Helpers\AuthHelper;
use App\Helpers\RoleHelper;
use App\Models\ConvertedLead;
use App\Models\ConvertedLeadIdCard;
use App\Models\Course;
use App\Models\OfflinePlace;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

trait ConvertedLeadScopedDataTables
{
    /**
     * Course pages that share the digital-marketing style table (programme / internship columns).
     */
    protected function digitalProgrammeStyleCourseIds(): array
    {
        return [11, 12, 13, 14, 15, 20, 21, 27, 30, 31, 32];
    }

    protected function aiPythonStyleCourseId(): int
    {
        return 10;
    }

    protected function ajaxScopedConvertedLeadCourseIds(): array
    {
        return array_merge([$this->aiPythonStyleCourseId()], $this->digitalProgrammeStyleCourseIds());
    }

    protected function isAjaxScopedConvertedLeadCourse(int $courseId): bool
    {
        return in_array($courseId, $this->ajaxScopedConvertedLeadCourseIds(), true);
    }

    protected function scopedConvertedLeadLayout(int $courseId): ?string
    {
        if ($courseId === $this->aiPythonStyleCourseId()) {
            return 'ai_python';
        }
        if (in_array($courseId, $this->digitalProgrammeStyleCourseIds(), true)) {
            return 'digital_programme';
        }

        return null;
    }

    protected function applyProgrammeCoursePageRoleScope(Builder $query): void
    {
        $currentUser = AuthHelper::getCurrentUser();
        if (! $currentUser) {
            return;
        }

        if (RoleHelper::is_senior_manager()) {
            return;
        }
        if (RoleHelper::is_team_lead()) {
            $teamId = $currentUser->team_id;
            if ($teamId) {
                $teamMemberIds = \App\Models\User::where('team_id', $teamId)->pluck('id')->toArray();
                $query->whereHas('lead', function ($q) use ($teamMemberIds) {
                    $q->whereIn('telecaller_id', $teamMemberIds);
                });
            } else {
                $query->whereHas('lead', function ($q) {
                    $q->where('telecaller_id', AuthHelper::getCurrentUserId());
                });
            }

            return;
        }
        if (RoleHelper::is_admission_counsellor()) {
            return;
        }
        if (RoleHelper::is_academic_assistant()) {
            return;
        }
        if (RoleHelper::is_telecaller()) {
            $query->whereHas('lead', function ($q) {
                $q->where('telecaller_id', AuthHelper::getCurrentUserId());
            });
        }
    }

    protected function applyProgrammeCoursePageFilters(Builder $query, Request $request, int $courseId): void
    {
        $search = $this->resolvedConvertedLeadsSearchTerm($request);
        if ($search !== null) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('register_number', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('call_status')) {
            $query->whereHas('studentDetails', function ($q) use ($request) {
                $q->where('call_status', $request->call_status);
            });
        }

        if ($request->filled('class_information')) {
            $query->whereHas('studentDetails', function ($q) use ($request) {
                $q->where('class_information', $request->class_information);
            });
        }

        if ($request->filled('orientation_class_status')) {
            $query->whereHas('studentDetails', function ($q) use ($request) {
                $q->where('orientation_class_status', $request->orientation_class_status);
            });
        }

        if ($request->filled('whatsapp_group_status')) {
            $query->whereHas('studentDetails', function ($q) use ($request) {
                $q->where('whatsapp_group_status', $request->whatsapp_group_status);
            });
        }

        if ($request->filled('class_status')) {
            $query->whereHas('studentDetails', function ($q) use ($request) {
                $q->where('class_status', $request->class_status);
            });
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->filled('batch_id')) {
            $query->where('batch_id', $request->batch_id);
        }

        if ($courseId !== $this->aiPythonStyleCourseId() && $request->filled('programme_type')) {
            $query->whereHas('leadDetail', function ($q) use ($request) {
                $q->where('programme_type', $request->programme_type);
            });
        }

        if ($this->programmeCourseUsesAdmissionBatchFilter($courseId) && $request->filled('admission_batch_id')) {
            $query->where('admission_batch_id', $request->admission_batch_id);
        }

        \App\Support\CourseFlagFieldSupport::applyListingFilter($query, $request);
    }

    /**
     * Matches legacy index methods: AI Integrated Digital Marketing (11) and Diploma in Data Science (12) did not filter by admission_batch_id.
     */
    protected function programmeCourseUsesAdmissionBatchFilter(int $courseId): bool
    {
        return ! in_array($courseId, [10, 11, 12], true);
    }

    protected function getScopedConvertedLeadsDataResponse(Request $request, int $scopedCourseId): JsonResponse
    {
        $layout = $this->scopedConvertedLeadLayout($scopedCourseId);
        if ($layout === null) {
            return response()->json([
                'draw' => (int) $request->input('draw'),
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
                'error' => 'Unsupported course.',
            ], 422);
        }

        $recordsTotalQuery = ConvertedLead::query()->where('course_id', $scopedCourseId);
        $this->applyProgrammeCoursePageRoleScope($recordsTotalQuery);
        $recordsTotal = (clone $recordsTotalQuery)->count();

        $filteredQuery = ConvertedLead::query()->where('course_id', $scopedCourseId);
        $this->applyProgrammeCoursePageRoleScope($filteredQuery);
        $this->applyProgrammeCoursePageFilters($filteredQuery, $request, $scopedCourseId);
        $recordsFiltered = (clone $filteredQuery)->count();

        $length = (int) $request->input('length', 25);
        if ($length < 1 || $length > 500) {
            $length = 25;
        }
        $start = max(0, (int) $request->input('start', 0));

        $with = [
            'lead.team',
            'cancelledBy',
            'studentDetails',
            'leadDetail.classTime',
            'batch',
            'admissionBatch',
            'courseFlag',
        ];

        $convertedLeads = (clone $filteredQuery)->with($with)
            ->orderBy('created_at', 'desc')
            ->skip($start)
            ->take($length)
            ->get();

        $leadIds = $convertedLeads->pluck('id');
        $idCardLeadIds = ConvertedLeadIdCard::whereIn('converted_lead_id', $leadIds)
            ->pluck('converted_lead_id')
            ->unique()
            ->flip();

        $courseModel = Course::find($scopedCourseId);
        $offlinePlaceOptions = OfflinePlace::active()->pluck('name', 'name')->toArray();
        $showParentPhone = RoleHelper::is_admin_or_super_admin() || RoleHelper::is_admission_counsellor();
        $canEditInline = RoleHelper::is_admin_or_super_admin() || RoleHelper::is_admission_counsellor() || RoleHelper::is_academic_assistant();
        $offlineOptionsJson = htmlspecialchars(json_encode($offlinePlaceOptions), ENT_QUOTES, 'UTF-8');
        $programmeOptionsJson = htmlspecialchars(json_encode(['online' => 'Online', 'offline' => 'Offline']), ENT_QUOTES, 'UTF-8');

        $data = [];
        foreach ($convertedLeads as $i => $convertedLead) {
            $displayIndex = $start + $i + 1;
            if ($layout === 'digital_programme') {
                $data[] = $this->formatDigitalProgrammeCourseDataTableRow(
                    $convertedLead,
                    $displayIndex,
                    $courseModel,
                    $idCardLeadIds,
                    $showParentPhone,
                    $canEditInline,
                    $offlineOptionsJson,
                    $programmeOptionsJson
                );
            } else {
                $data[] = $this->formatAiPythonCourseDataTableRow(
                    $convertedLead,
                    $displayIndex,
                    $idCardLeadIds,
                    $showParentPhone,
                    $canEditInline
                );
            }
        }

        return response()->json([
            'draw' => (int) $request->input('draw'),
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data,
        ]);
    }

    protected function formatDigitalProgrammeCourseDataTableRow(
        ConvertedLead $cl,
        int $displayIndex,
        ?Course $courseModel,
        $idCardLeadIds,
        bool $showParentPhone,
        bool $canEditInline,
        string $offlineOptionsJson,
        string $programmeOptionsJson
    ): array {
        $canToggleAcademic = RoleHelper::is_admin_or_super_admin() || RoleHelper::is_academic_assistant() || RoleHelper::is_admission_counsellor();
        $canToggleSupport = RoleHelper::is_admin_or_super_admin() || RoleHelper::is_support_team();

        $academicHtml = view('admin.converted-leads.partials.status-badge', [
            'convertedLead' => $cl,
            'type' => 'academic',
            'showToggle' => $canToggleAcademic,
            'toggleUrl' => $canToggleAcademic ? route('admin.converted-leads.toggle-academic-verify', $cl->id) : null,
            'title' => 'academic',
            'useModal' => true,
        ])->render();

        $supportHtml = view('admin.converted-leads.partials.status-badge', [
            'convertedLead' => $cl,
            'type' => 'support',
            'showToggle' => $canToggleSupport,
            'toggleUrl' => $canToggleSupport ? route('admin.support-converted-leads.toggle-support-verify', $cl->id) : null,
            'title' => 'support',
            'useModal' => true,
        ])->render();

        $editBtn = $canEditInline
            ? '<button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit"><i class="ti ti-edit"></i></button>'
            : '';

        $regCurrent = $cl->register_number ?? '';
        $registerCell = '<div class="inline-edit" data-field="register_number" data-id="'.$cl->id.'" data-current="'.e($regCurrent).'">'
            .'<span class="display-value">'.e($regCurrent !== '' ? $regCurrent : '-').'</span>'.$editBtn.'</div>';

        $courseFlagCell = view('admin.converted-leads.partials.inline-course-flag-cell', ['convertedLead' => $cl])->render();

        $nameHtml = view('admin.converted-leads.partials.dt-programme-name', ['convertedLead' => $cl])->render();

        $typeLabel = $cl->is_b2b == 1
            ? ('B2B'.($cl->lead?->team?->name ? ' ('.$cl->lead->team->name.')' : ''))
            : 'In House';

        $phoneCell = '<div class="inline-edit" data-field="phone" data-id="'.$cl->id.'" data-current="'.e($cl->phone).'">'
            .'<span class="display-value">'.e(\App\Helpers\PhoneNumberHelper::display($cl->code, $cl->phone)).'</span>'.$editBtn.'</div>'
            .'<div class="d-none inline-code-value" data-field="code" data-id="'.$cl->id.'" data-current="'.e($cl->code).'"></div>';

        $whatsappCell = ($cl->leadDetail && $cl->leadDetail->whatsapp_number)
            ? e(\App\Helpers\PhoneNumberHelper::display($cl->leadDetail->whatsapp_code, $cl->leadDetail->whatsapp_number))
            : '<span class="text-muted">N/A</span>';

        $parentCell = ($cl->leadDetail && $cl->leadDetail->parents_number)
            ? e(\App\Helpers\PhoneNumberHelper::display($cl->leadDetail->parents_code, $cl->leadDetail->parents_number))
            : '<span class="text-muted">N/A</span>';

        $progVal = $cl->leadDetail?->programme_type ?? '';
        $programmeCell = '<div class="inline-edit" data-field="programme_type" data-id="'.$cl->id.'" data-field-type="select" data-options="'.$programmeOptionsJson.'" data-current="'.e($progVal).'">'
            .'<span class="display-value">'.e($progVal ? ucfirst($progVal) : '-').'</span>'.$editBtn.'</div>';

        if ($cl->leadDetail?->programme_type === 'offline') {
            $locVal = $cl->leadDetail?->location ?? '';
            $locationCell = '<div class="inline-edit" data-field="location" data-id="'.$cl->id.'" data-field-type="select" data-options="'.$offlineOptionsJson.'" data-current="'.e($locVal).'">'
                .'<span class="display-value">'.e($locVal !== '' ? $locVal : '-').'</span>'.$editBtn.'</div>';
        } else {
            $locationCell = '<span class="text-muted">-</span>';
        }

        if ($courseModel && $courseModel->needs_time) {
            $ct = $cl->leadDetail?->classTime;
            $fromTo = '-';
            if ($ct) {
                $fromTime = Carbon::parse($ct->from_time)->format('h:i A');
                $toTime = Carbon::parse($ct->to_time)->format('h:i A');
                $fromTo = $fromTime.' - '.$toTime;
            }
            $classTimeCell = '<div class="inline-edit" data-field="class_time_id" data-id="'.$cl->id.'" data-course-id="'.$cl->course_id
                .'" data-programme-type="'.e($cl->leadDetail?->programme_type ?? '').'" data-current-id="'.e((string) ($cl->leadDetail?->class_time_id ?? '')).'">'
                .'<span class="display-value">'.e($fromTo).'</span>'.$editBtn.'</div>';
        } else {
            $classTimeCell = '<span class="text-muted">-</span>';
        }

        $batchCell = '<div class="inline-edit" data-field="batch_id" data-id="'.$cl->id.'" data-course-id="'.$cl->course_id.'" data-current-id="'.e((string) ($cl->batch_id ?? '')).'">'
            .'<span class="display-value">'.e($cl->batch ? $cl->batch->title : 'N/A').'</span>'.$editBtn.'</div>';

        $admCell = '<div class="inline-edit" data-field="admission_batch_id" data-id="'.$cl->id.'" data-batch-id="'.e((string) ($cl->batch_id ?? '')).'" data-current-id="'.e((string) ($cl->admission_batch_id ?? '')).'">'
            .'<span class="display-value">'.e($cl->admissionBatch ? $cl->admissionBatch->title : 'N/A').'</span>'.$editBtn.'</div>';

        $intern = $cl->studentDetails?->internship_id ?? '';
        $internCell = '<div class="inline-edit" data-field="internship_id" data-id="'.$cl->id.'" data-current="'.e($intern).'">'
            .'<span class="display-value">'.e($intern !== '' ? $intern : 'N/A').'</span>'.$editBtn.'</div>';

        $sd = $cl->studentDetails;

        $callStatusCell = $this->programmeInlineTextField('call_status', $cl->id, $sd?->call_status, $canEditInline);
        $classInfoCell = $this->programmeInlineTextField('class_information', $cl->id, $sd?->class_information, $canEditInline);
        $orientCell = $this->programmeInlineTextField('orientation_class_status', $cl->id, $sd?->orientation_class_status, $canEditInline);
        $startCell = $this->programmeInlineDateField('class_starting_date', $cl->id, $sd?->class_starting_date, $canEditInline);
        $endCell = $this->programmeInlineDateField('class_ending_date', $cl->id, $sd?->class_ending_date, $canEditInline);
        $waGroupCell = $this->programmeInlineTextField('whatsapp_group_status', $cl->id, $sd?->whatsapp_group_status, $canEditInline);
        $classStatCell = $this->programmeInlineTextField('class_status', $cl->id, $sd?->class_status, $canEditInline);
        $ccDateCell = $this->programmeInlineDateField('complete_cancel_date', $cl->id, $sd?->complete_cancel_date, $canEditInline);
        $remarksCell = $this->programmeInlineTextField('remarks', $cl->id, $sd?->remarks, $canEditInline);

        $hasIdCard = $idCardLeadIds->has($cl->id);
        $actionsHtml = view('admin.converted-leads.partials.dt-cell-actions', [
            'convertedLead' => $cl,
            'hasIdCard' => $hasIdCard,
        ])->render();

        $row = [
            'DT_RowId' => 'converted_lead_'.$cl->id,
            'DT_RowClass' => $cl->is_cancelled ? 'cancelled-row' : '',
            'sl_no' => (string) $displayIndex,
            'academic' => $academicHtml,
            'support' => $supportHtml,
            'converted_date' => e($cl->created_at->format('d-m-Y')),
            'register_number' => $registerCell,
            'course_flag' => $courseFlagCell,
            'name' => $nameHtml,
            'type' => e($typeLabel),
            'phone' => $phoneCell,
            'whatsapp' => $whatsappCell,
            'programme_type' => $programmeCell,
            'location' => $locationCell,
            'class_time' => $classTimeCell,
            'batch' => $batchCell,
            'admission_batch' => $admCell,
            'internship_id' => $internCell,
            'email' => e($cl->email ?? '-'),
            'call_status' => $callStatusCell,
            'class_information' => $classInfoCell,
            'orientation_class_status' => $orientCell,
            'class_starting_date' => $startCell,
            'class_ending_date' => $endCell,
            'whatsapp_group_status' => $waGroupCell,
            'class_status' => $classStatCell,
            'complete_cancel_date' => $ccDateCell,
            'remarks' => $remarksCell,
            'actions' => $actionsHtml,
        ];

        if ($showParentPhone) {
            $row['parent_phone'] = $parentCell;
        }

        return $row;
    }

    protected function formatAiPythonCourseDataTableRow(
        ConvertedLead $cl,
        int $displayIndex,
        $idCardLeadIds,
        bool $showParentPhone,
        bool $canEditInline
    ): array {
        $canToggleAcademic = RoleHelper::is_admin_or_super_admin() || RoleHelper::is_academic_assistant() || RoleHelper::is_admission_counsellor();
        $canToggleSupport = RoleHelper::is_admin_or_super_admin() || RoleHelper::is_support_team();

        $academicHtml = view('admin.converted-leads.partials.status-badge', [
            'convertedLead' => $cl,
            'type' => 'academic',
            'showToggle' => $canToggleAcademic,
            'toggleUrl' => $canToggleAcademic ? route('admin.converted-leads.toggle-academic-verify', $cl->id) : null,
            'title' => 'academic',
            'useModal' => true,
        ])->render();

        $supportHtml = view('admin.converted-leads.partials.status-badge', [
            'convertedLead' => $cl,
            'type' => 'support',
            'showToggle' => $canToggleSupport,
            'toggleUrl' => $canToggleSupport ? route('admin.support-converted-leads.toggle-support-verify', $cl->id) : null,
            'title' => 'support',
            'useModal' => true,
        ])->render();

        $editBtn = $canEditInline
            ? '<button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit"><i class="ti ti-edit"></i></button>'
            : '';

        $regNum = $cl->studentDetails?->registration_number ?? '';
        $registrationCell = '<div class="inline-edit" data-field="registration_number" data-id="'.$cl->id.'" data-current="'.e($regNum).'">'
            .'<span class="display-value">'.e($regNum !== '' ? $regNum : '-').'</span>'.$editBtn.'</div>';

        $courseFlagCell = view('admin.converted-leads.partials.inline-course-flag-cell', ['convertedLead' => $cl])->render();

        $nameHtml = view('admin.converted-leads.partials.dt-programme-name', ['convertedLead' => $cl])->render();

        $typeLabel = $cl->is_b2b == 1
            ? ('B2B'.($cl->lead?->team?->name ? ' ('.$cl->lead->team->name.')' : ''))
            : 'In House';

        $phoneCell = '<div class="inline-edit" data-field="phone" data-id="'.$cl->id.'" data-current="'.e($cl->phone).'">'
            .'<span class="display-value">'.e(\App\Helpers\PhoneNumberHelper::display($cl->code, $cl->phone)).'</span>'.$editBtn.'</div>'
            .'<div class="d-none inline-code-value" data-field="code" data-id="'.$cl->id.'" data-current="'.e($cl->code).'"></div>';

        $whatsappCell = ($cl->leadDetail && $cl->leadDetail->whatsapp_number)
            ? e(\App\Helpers\PhoneNumberHelper::display($cl->leadDetail->whatsapp_code, $cl->leadDetail->whatsapp_number))
            : '<span class="text-muted">N/A</span>';

        $parentCell = ($cl->leadDetail && $cl->leadDetail->parents_number)
            ? e(\App\Helpers\PhoneNumberHelper::display($cl->leadDetail->parents_code, $cl->leadDetail->parents_number))
            : '<span class="text-muted">N/A</span>';

        $batchCell = '<div class="inline-edit" data-field="batch_id" data-id="'.$cl->id.'" data-course-id="'.$cl->course_id.'" data-current-id="'.e((string) ($cl->batch_id ?? '')).'">'
            .'<span class="display-value">'.e($cl->batch ? $cl->batch->title : 'N/A').'</span>'.$editBtn.'</div>';

        $admCell = '<div class="inline-edit" data-field="admission_batch_id" data-id="'.$cl->id.'" data-batch-id="'.e((string) ($cl->batch_id ?? '')).'" data-current-id="'.e((string) ($cl->admission_batch_id ?? '')).'">'
            .'<span class="display-value">'.e($cl->admissionBatch ? $cl->admissionBatch->title : 'N/A').'</span>'.$editBtn.'</div>';

        $intern = $cl->studentDetails?->internship_id ?? '';
        $internCell = '<div class="inline-edit" data-field="internship_id" data-id="'.$cl->id.'" data-current="'.e($intern).'">'
            .'<span class="display-value">'.e($intern !== '' ? $intern : 'N/A').'</span>'.$editBtn.'</div>';

        $sd = $cl->studentDetails;

        $callStatusCell = $this->programmeInlineTextField('call_status', $cl->id, $sd?->call_status, $canEditInline);
        $classInfoCell = $this->programmeInlineTextField('class_information', $cl->id, $sd?->class_information, $canEditInline);
        $orientCell = $this->programmeInlineTextField('orientation_class_status', $cl->id, $sd?->orientation_class_status, $canEditInline);
        $startCell = $this->programmeInlineDateField('class_starting_date', $cl->id, $sd?->class_starting_date, $canEditInline);
        $endCell = $this->programmeInlineDateField('class_ending_date', $cl->id, $sd?->class_ending_date, $canEditInline);
        $waGroupCell = $this->programmeInlineTextField('whatsapp_group_status', $cl->id, $sd?->whatsapp_group_status, $canEditInline);

        $ctRaw = $sd?->class_time;
        $ctDisplay = '-';
        if ($ctRaw) {
            try {
                $ctDisplay = Carbon::parse($ctRaw)->format('h:i A');
            } catch (\Throwable $e) {
                $ctDisplay = (string) $ctRaw;
            }
        }
        $classTimeCell = '<div class="inline-edit" data-field="class_time" data-id="'.$cl->id.'" data-current="'.e((string) $ctRaw).'">'
            .'<span class="display-value">'.e($ctDisplay).'</span>'.$editBtn.'</div>';

        $classStatCell = $this->programmeInlineTextField('class_status', $cl->id, $sd?->class_status, $canEditInline);
        $ccDateCell = $this->programmeInlineDateField('complete_cancel_date', $cl->id, $sd?->complete_cancel_date, $canEditInline);
        $remarksCell = $this->programmeInlineTextField('remarks', $cl->id, $sd?->remarks, $canEditInline);

        $hasIdCard = $idCardLeadIds->has($cl->id);
        $actionsHtml = view('admin.converted-leads.partials.dt-cell-actions', [
            'convertedLead' => $cl,
            'hasIdCard' => $hasIdCard,
        ])->render();

        $row = [
            'DT_RowId' => 'converted_lead_'.$cl->id,
            'DT_RowClass' => $cl->is_cancelled ? 'cancelled-row' : '',
            'sl_no' => (string) $displayIndex,
            'academic' => $academicHtml,
            'support' => $supportHtml,
            'converted_date' => e($cl->created_at->format('d-m-Y')),
            'registration_number' => $registrationCell,
            'course_flag' => $courseFlagCell,
            'name' => $nameHtml,
            'type' => e($typeLabel),
            'phone' => $phoneCell,
            'whatsapp' => $whatsappCell,
            'batch' => $batchCell,
            'admission_batch' => $admCell,
            'internship_id' => $internCell,
            'email' => e($cl->email ?? '-'),
            'call_status' => $callStatusCell,
            'class_information' => $classInfoCell,
            'orientation_class_status' => $orientCell,
            'class_starting_date' => $startCell,
            'class_ending_date' => $endCell,
            'whatsapp_group_status' => $waGroupCell,
            'class_time' => $classTimeCell,
            'class_status' => $classStatCell,
            'complete_cancel_date' => $ccDateCell,
            'remarks' => $remarksCell,
            'actions' => $actionsHtml,
        ];

        if ($showParentPhone) {
            $row['parent_phone'] = $parentCell;
        }

        return $row;
    }

    protected function programmeInlineTextField(string $field, int $id, ?string $current, bool $canEdit): string
    {
        $v = $current ?? '';
        $editBtn = $canEdit
            ? '<button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit"><i class="ti ti-edit"></i></button>'
            : '';

        return '<div class="inline-edit" data-field="'.e($field).'" data-id="'.$id.'" data-current="'.e($v).'">'
            .'<span class="display-value">'.e($v !== '' ? $v : '-').'</span>'.$editBtn.'</div>';
    }

    protected function programmeInlineDateField(string $field, int $id, $current, bool $canEdit): string
    {
        $raw = $current ? (string) $current : '';
        $display = '-';
        if ($current) {
            try {
                $display = Carbon::parse($current)->format('d-m-Y');
            } catch (\Throwable $e) {
                $display = $raw;
            }
        }
        $editBtn = $canEdit
            ? '<button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit"><i class="ti ti-edit"></i></button>'
            : '';

        return '<div class="inline-edit" data-field="'.e($field).'" data-id="'.$id.'" data-current="'.e($raw).'">'
            .'<span class="display-value">'.e($display).'</span>'.$editBtn.'</div>';
    }
}
