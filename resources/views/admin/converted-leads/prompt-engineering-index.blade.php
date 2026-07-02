@extends('layouts.mantis')

@section('title', 'Prompt Engineering Converted Leads')

@section('content')
@php $appTimezone = config('app.timezone'); @endphp
<style>
    .table td {
        white-space: nowrap;
        vertical-align: middle;
    }
    .table td .btn-group { white-space: nowrap; }
    .table td .inline-edit { white-space: nowrap; }
    .table td .display-value {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 150px;
        display: inline-block;
    }
    .cancelled-row>td { background-color: #fff1f0 !important; }
    .cancelled-card { border: 1px solid #f5c2c7; background-color: #fff5f5; }
</style>
<!-- [ breadcrumb ] start -->
<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-6">
                <div class="page-header-title">
                    <h5 class="m-b-10">Prompt Engineering Converted Leads Management</h5>
                </div>
            </div>
            <div class="col-md-6">
                <ul class="breadcrumb d-flex justify-content-end">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.converted-leads.index') }}">Converted Leads</a></li>
                    <li class="breadcrumb-item">Prompt Engineering</li>
                </ul>
            </div>
        </div>
    </div>
</div>
<!-- [ breadcrumb ] end -->

@include('admin.converted-leads.partials.converted-leads-course-nav')

@include('admin.converted-leads.partials.mentor-list-nav', ['activeMentorRoute' => $activeMentorRoute ?? null])

@include('admin.converted-leads.partials.faculty-list-nav', ['activeFacultyRoute' => $activeFacultyRoute ?? null])

@include('admin.converted-leads.partials.converted-leads-support-nav')

<!-- [ Filter Section ] start -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Filter Prompt Engineering Converted Leads</h5>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('admin.prompt-engineering-converted-leads.index') }}" id="filterForm">
                    <div class="row g-3">
                        <div class="col-12 col-sm-6 col-md-2">
                            <label for="search" class="form-label">Search</label>
                            <input type="text" class="form-control" id="search" name="search"
                                value="{{ request('search') }}" placeholder="Name, Phone, Email, Register Number">
                        </div>
                        <div class="col-12 col-sm-6 col-md-2">
                            <label for="batch_id" class="form-label">Batch</label>
                            <select class="form-select" id="batch_id" name="batch_id">
                                <option value="">All Batches</option>
                                @foreach($batches as $batch)
                                <option value="{{ $batch->id }}" {{ request('batch_id') == $batch->id ? 'selected' : '' }}>{{ $batch->title }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-sm-6 col-md-2">
                            <label for="admission_batch_id" class="form-label">Academic Batch</label>
                            <select class="form-select" id="admission_batch_id" name="admission_batch_id" data-selected="{{ request('admission_batch_id') }}">
                                <option value="">All Admission Batches</option>
                            </select>
                        </div>
                        @include('admin.converted-leads.partials.course-flag-filter-field')

                        <div class="col-12 col-sm-6 col-md-2">
                            <label for="date_from" class="form-label">From Date</label>
                            <input type="date" class="form-control" id="date_from" name="date_from" value="{{ request('date_from') }}">
                        </div>
                        <div class="col-12 col-sm-6 col-md-2">
                            <label for="date_to" class="form-label">To Date</label>
                            <input type="date" class="form-control" id="date_to" name="date_to" value="{{ request('date_to') }}">
                        </div>
                        <div class="col-12 col-sm-6 col-md-2">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" id="status" name="status">
                                <option value="">All</option>
                                <option value="Active" {{ request('status') === 'Active' ? 'selected' : '' }}>Active</option>
                                <option value="Inactive" {{ request('status') === 'Inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>
                        <div class="col-12 col-sm-6 col-md-2">
                            <label for="is_b2b" class="form-label">B2B / In House</label>
                            <select class="form-select" id="is_b2b" name="is_b2b">
                                <option value="">All</option>
                                <option value="b2b" {{ request('is_b2b') === 'b2b' ? 'selected' : '' }}>B2B</option>
                                <option value="in_house" {{ request('is_b2b') === 'in_house' ? 'selected' : '' }}>In House</option>
                            </select>
                        </div>
                        <div class="col-12 col-sm-6 col-md-2">
                            <div class="d-flex gap-2 flex-wrap align-items-end">
                                <button type="submit" class="btn btn-primary"><i class="ti ti-search"></i> Filter</button>
                                <a href="{{ route('admin.prompt-engineering-converted-leads.index') }}" class="btn btn-secondary"><i class="ti ti-x"></i> Clear</a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- [ Filter Section ] end -->

<!-- [ Main Content ] start -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Prompt Engineering Converted Leads List</h5>
            </div>
            <div class="card-body">
                <div class="d-none d-lg-block">
                    <div class="table-responsive">
                        <table class="table table-hover data_table_basic" id="promptEngineeringTable">
                            <thead>
                                <tr>
                                    <th>SL No</th>
                                    <th>Academic</th>
                                    <th>Support</th>
                                    <th>Registration Number</th>
                                    <th>Course Flag</th>
                                    <th>Conversion Date</th>
                                    <th>B2B Team</th>
                                    <th>Batch</th>
                                    <th>Class Time</th>
                                    <th>Academic Batch</th>
                                    <th>Full Name</th>
                                    <th>Date of Birth</th>
                                    <th>Age</th>
                                    <th>Email ID</th>
                                    <th>Primary Mobile Number</th>
                                    <th>Medium of Study</th>
                                    <th>Previous Qualification</th>
                                    <th>Technology Performance Level</th>
                                    <th>Course Completion Date</th>
                                    <th>Certificate Issued Date</th>
                                    <th>B2B Partner Id</th>
                                    <th>B2B Code</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($convertedLeads as $index => $convertedLead)
                                @php
                                    $leadDetailPromptEngineering = $convertedLead->lead ? $convertedLead->lead->promptEngineeringStudentDetails : null;
                                    $canToggleAcademic = \App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_academic_assistant() || \App\Helpers\RoleHelper::is_admission_counsellor();
                                    $canToggleSupport = \App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_support_team();
                                    $age = $convertedLead->dob ? \Carbon\Carbon::parse($convertedLead->dob)->age : null;
                                @endphp
                                <tr class="{{ $convertedLead->is_cancelled ? 'cancelled-row' : '' }}">
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        @include('admin.converted-leads.partials.status-badge', [
                                            'convertedLead' => $convertedLead,
                                            'type' => 'academic',
                                            'showToggle' => $canToggleAcademic,
                                            'toggleUrl' => $canToggleAcademic ? route('admin.converted-leads.toggle-academic-verify', $convertedLead->id) : null,
                                            'title' => 'academic',
                                            'useModal' => true
                                        ])
                                    </td>
                                    <td>
                                        @include('admin.converted-leads.partials.status-badge', [
                                            'convertedLead' => $convertedLead,
                                            'type' => 'support',
                                            'showToggle' => $canToggleSupport,
                                            'toggleUrl' => $canToggleSupport ? route('admin.support-converted-leads.toggle-support-verify', $convertedLead->id) : null,
                                            'title' => 'support',
                                            'useModal' => true
                                        ])
                                    </td>
                                    <td>
                                        <div class="inline-edit" data-field="register_number" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->register_number }}">
                                            <span class="display-value">{{ $convertedLead->register_number ?? '-' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_academic_assistant())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit"><i class="ti ti-edit"></i></button>
                                            @endif
                                        </div>
                                    </td>
    @include('admin.converted-leads.partials.inline-course-flag-cell', ['convertedLead' => $convertedLead])
                                    <td>{{ $convertedLead->created_at ? $convertedLead->created_at->format('d-m-Y') : '-' }}</td>
                                    <td>{{ $convertedLead->is_b2b == 1 && $convertedLead->lead && $convertedLead->lead->team ? $convertedLead->lead->team->name : ($convertedLead->is_b2b == 1 ? 'B2B' : 'In House') }}</td>
                                    <td>
                                        <div class="inline-edit"
                                             data-field="batch_id"
                                             data-id="{{ $convertedLead->id }}"
                                             data-course-id="{{ $convertedLead->course_id }}"
                                             data-current-id="{{ $convertedLead->batch_id }}">
                                            <span class="display-value">{{ $convertedLead->batch ? $convertedLead->batch->title : '-' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_academic_assistant())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        @if($leadDetailPromptEngineering && $leadDetailPromptEngineering->classTime)
                                            @php
                                                $fromTime = \Carbon\Carbon::parse($leadDetailPromptEngineering->classTime->from_time)->format('h:i A');
                                                $toTime = \Carbon\Carbon::parse($leadDetailPromptEngineering->classTime->to_time)->format('h:i A');
                                            @endphp
                                            {{ $fromTime }} - {{ $toTime }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>
                                        <div class="inline-edit"
                                             data-field="admission_batch_id"
                                             data-id="{{ $convertedLead->id }}"
                                             data-batch-id="{{ $convertedLead->batch_id }}"
                                             data-current-id="{{ $convertedLead->admission_batch_id }}">
                                            <span class="display-value">{{ $convertedLead->admissionBatch ? $convertedLead->admissionBatch->title : '-' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_academic_assistant())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        {{ $convertedLead->name }}
                                        @if($convertedLead->is_cancelled)
                                        <div><span class="badge bg-danger ms-2">Cancelled</span></div>
                                        @endif
                                    </td>
                                    <td>{{ $convertedLead->dob ? \Carbon\Carbon::parse($convertedLead->dob)->format('d-m-Y') : '-' }}</td>
                                    <td>{{ $age !== null ? $age : '-' }}</td>
                                    <td>{{ $convertedLead->email ?? '-' }}</td>
                                    <td>{{ \App\Helpers\PhoneNumberHelper::display($convertedLead->code, $convertedLead->phone) }}</td>
                                    <td>{{ $leadDetailPromptEngineering && $leadDetailPromptEngineering->medium_of_study ? ucfirst(str_replace('_', ' ', $leadDetailPromptEngineering->medium_of_study)) : '-' }}</td>
                                    <td>{{ $leadDetailPromptEngineering && $leadDetailPromptEngineering->previous_qualification ? ucfirst(str_replace('_', ' ', $leadDetailPromptEngineering->previous_qualification)) : '-' }}</td>
                                    <td>{{ $leadDetailPromptEngineering && $leadDetailPromptEngineering->technology_performance_category ? ucfirst(str_replace('_', ' ', $leadDetailPromptEngineering->technology_performance_category)) : '-' }}</td>
                                    <td>
                                        <div class="inline-edit"
                                             data-field="class_ending_date"
                                             data-id="{{ $convertedLead->id }}"
                                             data-current="{{ $convertedLead->studentDetails && $convertedLead->studentDetails->class_ending_date ? ( $convertedLead->studentDetails->class_ending_date instanceof \Carbon\Carbon ? $convertedLead->studentDetails->class_ending_date->format('Y-m-d') : $convertedLead->studentDetails->class_ending_date ) : '' }}">
                                            @php
                                                $completionDate = $convertedLead->studentDetails && $convertedLead->studentDetails->class_ending_date
                                                    ? \Carbon\Carbon::parse($convertedLead->studentDetails->class_ending_date)->format('d-m-Y')
                                                    : '-';
                                            @endphp
                                            <span class="display-value">{{ $completionDate }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_academic_assistant())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="inline-edit"
                                             data-field="certificate_issued_date"
                                             data-id="{{ $convertedLead->id }}"
                                             data-current="{{ $convertedLead->studentDetails && $convertedLead->studentDetails->certificate_issued_date ? ( $convertedLead->studentDetails->certificate_issued_date instanceof \Carbon\Carbon ? $convertedLead->studentDetails->certificate_issued_date->format('Y-m-d') : $convertedLead->studentDetails->certificate_issued_date ) : '' }}">
                                            @php
                                                $issuedDate = $convertedLead->studentDetails && $convertedLead->studentDetails->certificate_issued_date
                                                    ? \Carbon\Carbon::parse($convertedLead->studentDetails->certificate_issued_date)->format('d-m-Y')
                                                    : '-';
                                            @endphp
                                            <span class="display-value">{{ $issuedDate }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_academic_assistant())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>{{ $convertedLead->lead && $convertedLead->lead->team && $convertedLead->lead->team->detail ? ($convertedLead->lead->team->detail->b2b_partner_id ?? '-') : '-' }}</td>
                                    <td>{{ $convertedLead->lead && $convertedLead->lead->team && $convertedLead->lead->team->detail ? ($convertedLead->lead->team->detail->b2b_code ?? '-') : '-' }}</td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('admin.converted-leads.show', $convertedLead->id) }}" class="btn btn-sm btn-outline-primary" title="View Details"><i class="ti ti-eye"></i></a>
                                            <a href="{{ route('admin.invoices.index', $convertedLead->id) }}" class="btn btn-sm btn-success" title="View Invoice"><i class="ti ti-receipt"></i></a>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor())
                                            <button type="button" class="btn btn-sm {{ $convertedLead->is_cancelled ? 'btn-danger' : 'btn-outline-danger' }} js-cancel-flag" title="Cancellation"
                                                data-cancel-url="{{ route('admin.converted-leads.cancel-flag', $convertedLead->id) }}"
                                                data-modal-title="Cancellation Confirmation"><i class="ti ti-ban"></i></button>
                                            @endif
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_academic_assistant() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_support_team())
                                            <button type="button" class="btn btn-sm btn-info update-register-btn" title="Update Register Number"
                                                data-url="{{ route('admin.converted-leads.update-register-number-modal', $convertedLead->id) }}"
                                                data-title="Update Register Number"><i class="ti ti-edit"></i></button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="23" class="text-center">No Prompt Engineering converted leads found</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Mobile Card View -->
                <div class="d-lg-none">
                    @forelse($convertedLeads as $index => $convertedLead)
                    @php
                        $leadDetailPromptEngineering = $convertedLead->lead ? $convertedLead->lead->promptEngineeringStudentDetails : null;
                        $canToggleAcademic = \App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_academic_assistant() || \App\Helpers\RoleHelper::is_admission_counsellor();
                        $canToggleSupport = \App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_support_team();
                        $age = $convertedLead->dob ? \Carbon\Carbon::parse($convertedLead->dob)->age : null;
                    @endphp
                    <div class="card mb-3 {{ $convertedLead->is_cancelled ? 'cancelled-card' : '' }}">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <h6 class="mb-0">{{ $convertedLead->name }}</h6>
                                    @if($convertedLead->is_cancelled)<span class="badge bg-danger ms-2">Cancelled</span>@endif
                                </div>
                                <span class="badge bg-primary">#{{ $index + 1 }}</span>
                            </div>
                            <div class="row g-2 mb-3">
                                <div class="col-6"><small class="text-muted d-block">Registration Number</small><span class="fw-medium">{{ $convertedLead->register_number ?? '-' }}</span></div>
                                <div class="col-6"><small class="text-muted d-block">Conversion Date</small><span class="fw-medium">{{ $convertedLead->created_at ? $convertedLead->created_at->format('d-m-Y') : '-' }}</span></div>
                                <div class="col-6"><small class="text-muted d-block">Batch</small><span class="fw-medium">{{ $convertedLead->batch ? $convertedLead->batch->title : '-' }}</span></div>
                                <div class="col-6"><small class="text-muted d-block">Academic Batch</small><span class="fw-medium">{{ $convertedLead->admissionBatch ? $convertedLead->admissionBatch->title : '-' }}</span></div>
                                <div class="col-6"><small class="text-muted d-block">Email</small><span class="fw-medium">{{ $convertedLead->email ?? '-' }}</span></div>
                                <div class="col-6"><small class="text-muted d-block">Phone</small><span class="fw-medium">{{ \App\Helpers\PhoneNumberHelper::display($convertedLead->code, $convertedLead->phone) }}</span></div>
                                <div class="col-6"><small class="text-muted d-block">Academic</small>
                                    @include('admin.converted-leads.partials.status-badge', ['convertedLead' => $convertedLead, 'type' => 'academic', 'showToggle' => $canToggleAcademic, 'toggleUrl' => $canToggleAcademic ? route('admin.converted-leads.toggle-academic-verify', $convertedLead->id) : null])
                                </div>
                                <div class="col-6"><small class="text-muted d-block">Support</small>
                                    @include('admin.converted-leads.partials.status-badge', ['convertedLead' => $convertedLead, 'type' => 'support', 'showToggle' => $canToggleSupport, 'toggleUrl' => $canToggleSupport ? route('admin.support-converted-leads.toggle-support-verify', $convertedLead->id) : null])
                                </div>
                            </div>
                            <div class="d-flex gap-2">
                                <a href="{{ route('admin.converted-leads.show', $convertedLead->id) }}" class="btn btn-sm btn-outline-primary"><i class="ti ti-eye"></i> View</a>
                                <a href="{{ route('admin.invoices.index', $convertedLead->id) }}" class="btn btn-sm btn-success"><i class="ti ti-receipt"></i> Invoice</a>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-4">
                        <i class="ti ti-inbox fs-1 text-muted"></i>
                        <p class="text-muted mt-2">No Prompt Engineering converted leads found</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
<!-- [ Main Content ] end -->

<script type="application/json" id="country-codes-json">@json($country_codes)</script>
@include('admin.converted-leads.partials.course-flag-inline-scripts')
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    function loadAdmissionBatchesByBatch(batchId, selectedId) {
        var $admission = $('#admission_batch_id');
        $admission.html('<option value="">Loading...</option>');
        if (!batchId) {
            $admission.html('<option value="">All Admission Batches</option>');
            return;
        }
        $.get('/api/admission-batches/by-batch/' + batchId).done(function(list) {
            var opts = '<option value="">All Admission Batches</option>';
            if (list && list.length) {
                list.forEach(function(i) {
                    var sel = String(selectedId) === String(i.id) ? 'selected' : '';
                    opts += '<option value="' + i.id + '" ' + sel + '>' + i.title + '</option>';
                });
            }
            $admission.html(opts);
        }).fail(function() {
            $admission.html('<option value="">All Admission Batches</option>');
        });
    }
    var initialBatchId = $('#batch_id').val();
    var initialAdmissionBatchId = $('#admission_batch_id').data('selected');
    if (initialBatchId) loadAdmissionBatchesByBatch(initialBatchId, initialAdmissionBatchId);
    $('#batch_id').on('change', function() {
        loadAdmissionBatchesByBatch($(this).val(), '');
        setTimeout(function() { $('#filterForm').submit(); }, 100);
    });

    // Inline edit handlers (batch, academic batch, course completion & certificate issued date, register number, etc.)
    var inlineDateFields = ['class_ending_date', 'certificate_issued_date'];

    function createInlineInputField(field, currentValue) {
        var displayValue = (currentValue && currentValue !== '-' && currentValue !== 'N/A') ? currentValue : '';
        return '' +
            '<div class="edit-form">' +
                '<input type="text" value="' + displayValue + '" class="form-control form-control-sm" autocomplete="off" autocapitalize="off" spellcheck="false">' +
                '<div class="btn-group mt-1">' +
                    '<button type="button" class="btn btn-success btn-sm save-edit">Save</button>' +
                    '<button type="button" class="btn btn-secondary btn-sm cancel-edit">Cancel</button>' +
                '</div>' +
            '</div>';
    }

    function createInlineDateField(currentValue) {
        var value = (currentValue && currentValue !== '-' && currentValue !== 'N/A') ? currentValue : '';
        return '' +
            '<div class="edit-form">' +
                '<input type="date" value="' + value + '" class="form-control form-control-sm">' +
                '<div class="btn-group mt-1">' +
                    '<button type="button" class="btn btn-success btn-sm save-edit">Save</button>' +
                    '<button type="button" class="btn btn-secondary btn-sm cancel-edit">Cancel</button>' +
                '</div>' +
            '</div>';
    }

    function createInlineBatchSelect() {
        return '' +
            '<div class="edit-form">' +
                '<select class="form-select form-select-sm">' +
                    '<option value="">Loading...</option>' +
                '</select>' +
                '<div class="btn-group mt-1">' +
                    '<button type="button" class="btn btn-success btn-sm save-edit">Save</button>' +
                    '<button type="button" class="btn btn-secondary btn-sm cancel-edit">Cancel</button>' +
                '</div>' +
            '</div>';
    }

    function createInlineAdmissionBatchSelect() {
        return '' +
            '<div class="edit-form">' +
                '<select class="form-select form-select-sm">' +
                    '<option value="">Loading...</option>' +
                '</select>' +
                '<div class="btn-group mt-1">' +
                    '<button type="button" class="btn btn-success btn-sm save-edit">Save</button>' +
                    '<button type="button" class="btn btn-secondary btn-sm cancel-edit">Cancel</button>' +
                '</div>' +
            '</div>';
    }

    function loadInlineBatches(courseId, $select, currentId) {
        if (!courseId) {
            $select.html('<option value="">No course selected</option>');
            return;
        }
        $.get('/api/batches/by-course/' + courseId)
            .done(function (response) {
                var options = '<option value="">Select Batch</option>';
                if (response.success && response.batches) {
                    response.batches.forEach(function (batch) {
                        var isSelected = (currentId && String(currentId) === String(batch.id)) ? 'selected' : '';
                        options += '<option value="' + batch.id + '" ' + isSelected + '>' + batch.title + '</option>';
                    });
                }
                $select.html(options).focus();
            })
            .fail(function () {
                $select.html('<option value="">Error loading batches</option>');
            });
    }

    function loadInlineAdmissionBatches(batchId, $select, currentId) {
        if (!batchId) {
            $select.html('<option value="">No batch selected</option>');
            return;
        }
        $.get('/api/admission-batches/by-batch/' + batchId)
            .done(function (list) {
                var options = '<option value="">Select Admission Batch</option>';
                if (list && list.length) {
                    list.forEach(function (item) {
                        var selected = String(currentId) === String(item.id) ? 'selected' : '';
                        options += '<option value="' + item.id + '" ' + selected + '>' + item.title + '</option>';
                    });
                }
                $select.html(options).focus();
            })
            .fail(function () {
                $select.html('<option value="">Error loading admission batches</option>');
            });
    }

    $(document).on('click', '.edit-btn', function(e) {
        e.preventDefault();
        e.stopPropagation();

        var container = $(this).closest('.inline-edit');
        var field = container.data('field');
        var currentValue = container.data('current') !== undefined
            ? String(container.data('current')).trim()
            : container.find('.display-value').text().trim();
        var currentId = container.data('current-id') !== undefined
            ? String(container.data('current-id')).trim()
            : '';

        if (container.hasClass('editing')) {
            return;
        }

        $('.inline-edit.editing').each(function () {
            $(this).removeClass('editing');
            $(this).find('.edit-form').remove();
        });

        var editForm = '';
        if (field === 'batch_id') {
            editForm = createInlineBatchSelect();
        } else if (field === 'admission_batch_id') {
            editForm = createInlineAdmissionBatchSelect();
        } else if (inlineDateFields.indexOf(field) !== -1) {
            editForm = createInlineDateField(currentValue);
        } else {
            editForm = createInlineInputField(field, currentValue);
        }

        container.addClass('editing');
        container.append(editForm);

        if (field === 'batch_id') {
            var courseId = container.data('course-id');
            var select = container.find('select');
            loadInlineBatches(courseId, select, currentId);
        } else if (field === 'admission_batch_id') {
            var batchId = container.data('batch-id');
            var selectAb = container.find('select');
            loadInlineAdmissionBatches(batchId, selectAb, currentId);
        } else {
            container.find('input, select').first().focus();
        }
    });

    $(document).off('click.saveInline').on('click.saveInline', '.save-edit', function(e) {
        e.preventDefault();
        e.stopPropagation();

        var container = $(this).closest('.inline-edit');
        var field = container.data('field');
        var id = container.data('id');
        var value = container.find('input, select').val();
        var btn = $(this);

        if (btn.data('busy')) return;
        btn.data('busy', true).prop('disabled', true).html('<i class="ti ti-loader-2 spin"></i>');

        $.ajax({
            url: '{{ route("admin.converted-leads.inline-update", ":id") }}'.replace(':id', id),
            method: 'POST',
            data: {
                field: field,
                value: value,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    var displayValue = response.value || 'N/A';
                    container.find('.display-value').text(displayValue);
                    container.data('current', (inlineDateFields.indexOf(field) !== -1) ? value : displayValue);
                    if (field === 'batch_id' || field === 'admission_batch_id') {
                        container.data('current-id', value || '');
                    }
                    if (typeof toast_success === 'function') toast_success(response.message);
                } else {
                    if (typeof toast_error === 'function') toast_error(response.error || 'Update failed');
                }
            },
            error: function(xhr) {
                var msg = (xhr.responseJSON && xhr.responseJSON.error) || (xhr.responseJSON && xhr.responseJSON.message) || 'Update failed';
                if (typeof toast_error === 'function') toast_error(msg);
            },
            complete: function() {
                btn.data('busy', false).prop('disabled', false).html('Save');
                container.removeClass('editing').find('.edit-form').remove();
            }
        });
    });

    $(document).off('click.cancelInline').on('click.cancelInline', '.cancel-edit', function(e) {
        e.preventDefault();
        e.stopPropagation();
        var container = $(this).closest('.inline-edit');
        container.removeClass('editing').find('.edit-form').remove();
    });

    $('.update-register-btn').on('click', function(e) {
        e.preventDefault();
        var url = $(this).data('url');
        var title = $(this).data('title');
        if (typeof show_small_modal === 'function' && url) show_small_modal(url, title);
    });
    $(document).on('click', '.js-cancel-flag', function(e) {
        e.preventDefault();
        var url = $(this).data('cancel-url');
        var title = $(this).data('modal-title') || 'Cancellation';
        if (typeof show_ajax_modal === 'function' && url) show_ajax_modal(url, title);
    });
});
</script>
@endpush

