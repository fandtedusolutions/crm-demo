@extends('layouts.mantis')

@section('title', 'National Institute of Open Schooling Converted Leads')

@section('content')
<style>
    .table td {
        white-space: nowrap;
        vertical-align: middle;
    }

    .table td .btn-group {
        white-space: nowrap;
    }

    .table td .inline-edit {
        white-space: nowrap;
    }

    .table td .display-value {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 150px;
        display: inline-block;
    }

    .cancelled-row>td {
        background-color: #fff1f0 !important;
    }

    .cancelled-card {
        border: 1px solid #f5c2c7;
        background-color: #fff5f5;
    }
</style>
<!-- [ breadcrumb ] start -->
<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-6">
                <div class="page-header-title">
                    <h5 class="m-b-10">National Institute of Open Schooling Converted Leads Management</h5>
                </div>
            </div>
            <div class="col-md-6">
                <ul class="breadcrumb d-flex justify-content-end">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.converted-leads.index') }}">Converted Leads</a></li>
                    <li class="breadcrumb-item">National Institute of Open Schooling</li>
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
<div class="row mb-3">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form method="GET" action="{{ route('admin.nios-converted-leads.index') }}" id="filterForm">
                    <div class="row g-3 align-items-end">
                        <div class="col-12 col-sm-6 col-md-2">
                            <label for="search" class="form-label">Search</label>
                            <input type="text" class="form-control" id="search" name="search"
                                value="{{ request('search') }}" placeholder="Name, Phone, Email, Register Number">
                        </div>
                        <div class="col-12 col-sm-6 col-md-2">
                            <label for="batch_id" class="form-label">Batch</label>
                            <select class="form-select" id="batch_id" name="batch_id" data-selected="{{ request('batch_id') }}">
                                <option value="">All Batches</option>
                                @foreach($batches as $batch)
                                <option value="{{ $batch->id }}" {{ request('batch_id') == $batch->id ? 'selected' : '' }}>
                                    {{ $batch->title }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-sm-6 col-md-2">
                            <label for="admission_batch_id" class="form-label">Admission Batch</label>
                            <select class="form-select" id="admission_batch_id" name="admission_batch_id" data-selected="{{ request('admission_batch_id') }}">
                                <option value="">All Admission Batches</option>
                            </select>
                        </div>
                        @include('admin.converted-leads.partials.course-flag-filter-field')

                        <div class="col-12 col-sm-6 col-md-2">
                            <label for="date_from" class="form-label">From Date</label>
                            <input type="date" class="form-control" id="date_from" name="date_from"
                                value="{{ request('date_from') }}">
                        </div>
                        <div class="col-12 col-sm-6 col-md-2">
                            <label for="date_to" class="form-label">To Date</label>
                            <input type="date" class="form-control" id="date_to" name="date_to"
                                value="{{ request('date_to') }}">
                        </div>
                        <div class="col-12 col-sm-6 col-md-2">
                            <label for="status" class="form-label">REG. FEE</label>
                            <select class="form-select" id="status" name="status">
                                <option value="">All</option>
                                <option value="Paid" {{ request('status')==='Paid' ? 'selected' : '' }}>Paid</option>
                                <option value="Received" {{ request('status')==='Received' ? 'selected' : '' }}>Received</option>
                                <option value="Admission cancel" {{ request('status')==='Admission cancel' ? 'selected' : '' }}>Admission cancel</option>
                                <option value="Active" {{ request('status')==='Active' ? 'selected' : '' }}>Active</option>
                                <option value="Inactive" {{ request('status')==='Inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>

                        <div class="col-12 col-sm-6 col-md-2">
                            <label for="reg_fee" class="form-label">Status</label>
                            <select class="form-select" id="reg_fee" name="reg_fee">
                                <option value="">All</option>
                                <option value="Handover -1" {{ request('reg_fee')==='Handover -1' ? 'selected' : '' }}>Handover -1</option>
                                <option value="Handover - 2" {{ request('reg_fee')==='Handover - 2' ? 'selected' : '' }}>Handover - 2</option>
                                <option value="Handover - 3" {{ request('reg_fee')==='Handover - 3' ? 'selected' : '' }}>Handover - 3</option>
                                <option value="Handover - 4" {{ request('reg_fee')==='Handover - 4' ? 'selected' : '' }}>Handover - 4</option>
                                <option value="Handover - 5" {{ request('reg_fee')==='Handover - 5' ? 'selected' : '' }}>Handover - 5</option>
                                <option value="Paid" {{ request('reg_fee')==='Paid' ? 'selected' : '' }}>Paid</option>
                                <option value="Admission cancel" {{ request('reg_fee')==='Admission cancel' ? 'selected' : '' }}>Admission cancel</option>
                            </select>
                        </div>

                        <div class="col-12 col-sm-6 col-md-2">
                            <label for="exam_fee" class="form-label">EXAM FEE</label>
                            <select class="form-select" id="exam_fee" name="exam_fee">
                                <option value="">All</option>
                                <option value="Pending" {{ request('exam_fee')==='Pending' ? 'selected' : '' }}>Pending</option>
                                <option value="Not Paid" {{ request('exam_fee')==='Not Paid' ? 'selected' : '' }}>Not Paid</option>
                                <option value="Paid" {{ request('exam_fee')==='Paid' ? 'selected' : '' }}>Paid</option>
                            </select>
                        </div>

                        <div class="col-12 col-sm-6 col-md-2">
                            <label for="id_card" class="form-label">ID CARD</label>
                            <select class="form-select" id="id_card" name="id_card">
                                <option value="">All</option>
                                <option value="processing" {{ request('id_card')==='processing' ? 'selected' : '' }}>processing</option>
                                <option value="download" {{ request('id_card')==='download' ? 'selected' : '' }}>download</option>
                                <option value="not downloaded" {{ request('id_card')==='not downloaded' ? 'selected' : '' }}>not downloaded</option>
                            </select>
                        </div>

                        <div class="col-12 col-sm-6 col-md-2">
                            <label for="tma" class="form-label">TMA</label>
                            <select class="form-select" id="tma" name="tma">
                                <option value="">All</option>
                                <option value="Uploaded" {{ request('tma')==='Uploaded' ? 'selected' : '' }}>Uploaded</option>
                                <option value="Not Upload" {{ request('tma')==='Not Upload' ? 'selected' : '' }}>Not Upload</option>
                            </select>
                        </div>

                        <div class="col-12 col-md-4">
                            <div class="d-flex gap-2 flex-wrap">
                                <button type="submit" class="btn btn-primary">
                                    <i class="ti ti-search"></i> <span class="d-none d-sm-inline">Filter</span>
                                </button>
                                <a href="{{ route('admin.nios-converted-leads.index') }}" class="btn btn-secondary" id="niosClearFilters">
                                    <i class="ti ti-x"></i> <span class="d-none d-sm-inline">Clear</span>
                                </a>
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
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">National Institute of Open Schooling Converted Leads List</h5>
                @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_academic_assistant())
                <div class="batch-actions-toolbar" style="display: none;">
                    <span class="me-2 text-muted selected-count">0 selected</span>
                    <button type="button" class="btn btn-sm btn-primary" id="batchUpdateBtn">
                        <i class="ti ti-edit"></i> Batch Update
                    </button>
                </div>
                @endif
            </div>
            <div class="card-body">
                <!-- Desktop Table View -->
                <div class="d-none d-lg-block">
                    <div class="table-responsive">
                        <table class="table table-hover" id="convertedLeadsTable">
                            <thead>
                                <tr>
                                    @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_academic_assistant())
                                    <th style="width: 40px;">
                                        <input type="checkbox" id="selectAll" title="Select All">
                                    </th>
                                    @endif
                                    <th>#</th>
                                    <th>Academic</th>
                                    <th>Support</th>
                                    <th>Register Number</th>
                                    <th>Course Flag</th>
                                    <th>Converted Date</th>
                                    <th>DOB</th>
                                    <th>Type</th>
                                    <th>Name</th>
                                    <th>Subject</th>
                                    <th>Subject Area</th>
                                    <th>Mobile</th>
                                    <th>WhatsApp</th>
                                    @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor())
                                    <th>Parent Phone</th>
                                    @endif
                                    <th>Batch</th>
                                    <th>Course</th>
                                    <th>Admission Batch</th>
                                    <th>Registered Person</th>
                                    <th>Username</th>
                                    <th>Password</th>
                                    <th>REG. FEE</th>
                                    <th>REG. STATUS</th>
                                    <th>EXAM FEE</th>
                                    <th>Ref No</th>
                                    <th>Enroll No</th>
                                    <th>MAIL</th>
                                    <th>ID CARD</th>
                                    <th>TMA</th>
                                    <th>Remarks</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>

                <!-- Mobile Card View (filled from AJAX response) -->
                <div class="d-lg-none" id="niosMobileCardsWrap">
                    <div id="niosMobileCards"></div>
                    <div id="niosMobileEmpty" class="text-center py-5 d-none">
                        <div class="text-muted">
                            <i class="ti ti-check-circle f-48 mb-3 d-block"></i>
                            <h5>No National Institute of Open Schooling converted leads found</h5>
                            <p>Try adjusting your filters or check back later.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- [ Main Content ] end -->

<!-- Support Verify Modal -->
<div class="modal fade" id="supportVerifyModal" tabindex="-1" aria-labelledby="supportVerifyModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="supportVerifyModalLabel">Confirm Action</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p id="supportVerifyModalText" class="mb-0"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="confirmSupportVerifyBtn">
                    <span class="confirm-text">Confirm</span>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Academic Verify Modal -->
<div class="modal fade" id="academicVerifyModal" tabindex="-1" aria-labelledby="academicVerifyModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="academicVerifyModalLabel">Confirm Action</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p id="academicVerifyModalText" class="mb-0"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="confirmAcademicVerifyBtn">
                    <span class="confirm-text">Confirm</span>
                </button>
            </div>
        </div>
    </div>
</div>

<script id="country-codes-json" type="application/json">{!! json_encode($country_codes ?? [], JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) !!}</script>

@php
$niosConvertedLeadsColumns = [];
if (\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_academic_assistant()) {
    $niosConvertedLeadsColumns[] = ['data' => 'batch_cb', 'name' => 'batch_cb', 'orderable' => false, 'searchable' => false];
}
$niosConvertedLeadsColumns = array_merge($niosConvertedLeadsColumns, [
    ['data' => 'index', 'name' => 'index', 'orderable' => false, 'searchable' => false],
    ['data' => 'academic', 'name' => 'academic', 'orderable' => false, 'searchable' => false],
    ['data' => 'support', 'name' => 'support', 'orderable' => false, 'searchable' => false],
    ['data' => 'register_number', 'name' => 'register_number', 'orderable' => false, 'searchable' => false],
    ['data' => 'course_flag', 'name' => 'course_flag', 'orderable' => false, 'searchable' => false],
    ['data' => 'converted_date', 'name' => 'converted_date', 'orderable' => false, 'searchable' => false],
    ['data' => 'dob', 'name' => 'dob', 'orderable' => false, 'searchable' => false],
    ['data' => 'type', 'name' => 'type', 'orderable' => false, 'searchable' => false],
    ['data' => 'name_col', 'name' => 'name_col', 'orderable' => false, 'searchable' => false],
    ['data' => 'subject', 'name' => 'subject', 'orderable' => false, 'searchable' => false],
    ['data' => 'subject_area', 'name' => 'subject_area', 'orderable' => false, 'searchable' => false],
    ['data' => 'mobile', 'name' => 'mobile', 'orderable' => false, 'searchable' => false],
    ['data' => 'whatsapp', 'name' => 'whatsapp', 'orderable' => false, 'searchable' => false],
]);
if (\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor()) {
    $niosConvertedLeadsColumns[] = ['data' => 'parent_phone', 'name' => 'parent_phone', 'orderable' => false, 'searchable' => false];
}
$niosConvertedLeadsColumns = array_merge($niosConvertedLeadsColumns, [
    ['data' => 'batch', 'name' => 'batch', 'orderable' => false, 'searchable' => false],
    ['data' => 'course', 'name' => 'course', 'orderable' => false, 'searchable' => false],
    ['data' => 'admission_batch', 'name' => 'admission_batch', 'orderable' => false, 'searchable' => false],
    ['data' => 'registered_person', 'name' => 'registered_person', 'orderable' => false, 'searchable' => false],
    ['data' => 'username', 'name' => 'username', 'orderable' => false, 'searchable' => false],
    ['data' => 'password', 'name' => 'password', 'orderable' => false, 'searchable' => false],
    ['data' => 'admission_status', 'name' => 'admission_status', 'orderable' => false, 'searchable' => false],
    ['data' => 'student_reg_fee', 'name' => 'student_reg_fee', 'orderable' => false, 'searchable' => false],
    ['data' => 'exam_fee', 'name' => 'exam_fee', 'orderable' => false, 'searchable' => false],
    ['data' => 'ref_no', 'name' => 'ref_no', 'orderable' => false, 'searchable' => false],
    ['data' => 'enroll_no', 'name' => 'enroll_no', 'orderable' => false, 'searchable' => false],
    ['data' => 'mail', 'name' => 'mail', 'orderable' => false, 'searchable' => false],
    ['data' => 'id_card', 'name' => 'id_card', 'orderable' => false, 'searchable' => false],
    ['data' => 'tma', 'name' => 'tma', 'orderable' => false, 'searchable' => false],
    ['data' => 'remarks', 'name' => 'remarks', 'orderable' => false, 'searchable' => false],
    ['data' => 'actions', 'name' => 'actions', 'orderable' => false, 'searchable' => false],
]);
@endphp
<div id="niosConvertedLeadsConfig" data-data-url="{{ route('admin.nios-converted-leads.data') }}" style="display:none"></div>
<script type="application/json" id="niosConvertedLeadsColumnsData">{!! json_encode($niosConvertedLeadsColumns) !!}</script>

@include('admin.converted-leads.partials.course-flag-inline-scripts')
@endsection

@push('styles')
<style>
    .spin {
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        0% {
            transform: rotate(0deg);
        }

        100% {
            transform: rotate(360deg);
        }
    }

    .inline-edit {
        position: relative;
        overflow: visible;
    }

    .inline-edit .edit-form {
        display: none;
        position: absolute;
        top: 0;
        left: -8px;
        z-index: 10;
        background: white;
        border: 1px solid #ddd;
        border-radius: 4px;
        padding: 10px;
        min-width: 320px;
        max-width: 440px;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
    }

    .inline-edit.editing .edit-form {
        display: block;
    }

    .inline-edit.editing .display-value {
        display: none;
    }

    .inline-edit .edit-form input,
    .inline-edit .edit-form select {
        width: 100%;
        padding: 4px 8px;
        border: 1px solid #ccc;
        border-radius: 3px;
        font-size: 12px;
    }

    .inline-edit .edit-form input:focus,
    .inline-edit .edit-form select:focus {
        border-color: #7366ff;
        outline: none;
        box-shadow: 0 0 0 2px rgba(115, 102, 255, 0.15);
    }

    .inline-edit .edit-form .btn-group {
        margin-top: 5px;
    }

    .inline-edit .edit-form .btn {
        padding: 2px 8px;
        font-size: 11px;
    }

    #convertedLeadsTable thead th,
    #convertedLeadsTable tbody td {
        white-space: nowrap;
    }

    #convertedLeadsTable thead th {
        position: sticky;
        top: 0;
        z-index: 5;
        background: #fff;
        box-shadow: inset 0 -1px 0 #e9ecef;
    }

    #convertedLeadsTable tbody tr:hover {
        background: #fafbff;
    }

    #convertedLeadsTable td .display-value {
        display: inline-block;
        max-width: 220px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        vertical-align: middle;
    }

    #convertedLeadsTable .btn-group .btn {
        margin-right: 4px;
    }

    #convertedLeadsTable .btn-group .btn:last-child {
        margin-right: 0;
    }

    .card .card-body #filterForm {
        border-bottom: 1px dashed #e9ecef;
        padding-bottom: 8px;
    }

    /* Column-specific min-widths by position */
    #convertedLeadsTable thead th:nth-child(1),
    #convertedLeadsTable tbody td:nth-child(1) {
        min-width: 60px;
    }

    #convertedLeadsTable thead th:nth-child(2),
    #convertedLeadsTable tbody td:nth-child(2) {
        min-width: 140px;
    }

    #convertedLeadsTable thead th:nth-child(3),
    #convertedLeadsTable tbody td:nth-child(3) {
        min-width: 120px;
    }

    #convertedLeadsTable thead th:nth-child(4),
    #convertedLeadsTable tbody td:nth-child(4) {
        min-width: 120px;
    }

    #convertedLeadsTable thead th:nth-child(5),
    #convertedLeadsTable tbody td:nth-child(5) {
        min-width: 220px;
    }

    #convertedLeadsTable thead th:nth-child(6),
    #convertedLeadsTable tbody td:nth-child(6) {
        min-width: 180px;
    }

    #convertedLeadsTable thead th:nth-child(7),
    #convertedLeadsTable tbody td:nth-child(7) {
        min-width: 140px;
    }

    #convertedLeadsTable thead th:nth-child(8),
    #convertedLeadsTable tbody td:nth-child(8) {
        min-width: 180px;
    }

    #convertedLeadsTable thead th:nth-child(9),
    #convertedLeadsTable tbody td:nth-child(9) {
        min-width: 180px;
    }

    #convertedLeadsTable thead th:nth-child(10),
    #convertedLeadsTable tbody td:nth-child(10) {
        min-width: 160px;
    }

    #convertedLeadsTable thead th:nth-child(11),
    #convertedLeadsTable tbody td:nth-child(11) {
        min-width: 140px;
    }

    #convertedLeadsTable thead th:nth-child(12),
    #convertedLeadsTable tbody td:nth-child(12) {
        min-width: 140px;
    }

    #convertedLeadsTable thead th:nth-child(13),
    #convertedLeadsTable tbody td:nth-child(13) {
        min-width: 140px;
    }

    #convertedLeadsTable thead th:nth-child(14),
    #convertedLeadsTable tbody td:nth-child(14) {
        min-width: 140px;
    }

    #convertedLeadsTable thead th:nth-child(15),
    #convertedLeadsTable tbody td:nth-child(15) {
        min-width: 140px;
    }

    #convertedLeadsTable thead th:nth-child(16),
    #convertedLeadsTable tbody td:nth-child(16) {
        min-width: 140px;
    }

    #convertedLeadsTable thead th:nth-child(17),
    #convertedLeadsTable tbody td:nth-child(17) {
        min-width: 200px;
    }

    #convertedLeadsTable thead th:nth-child(18),
    #convertedLeadsTable tbody td:nth-child(18) {
        min-width: 120px;
    }

    #convertedLeadsTable thead th:nth-child(19),
    #convertedLeadsTable tbody td:nth-child(19) {
        min-width: 120px;
    }

    #convertedLeadsTable thead th:nth-child(20),
    #convertedLeadsTable tbody td:nth-child(20) {
        min-width: 140px;
    }
</style>
@endpush

@push('scripts')
@include('admin.converted-leads.partials.converted-lead-subject-area-scripts')
<script>
    $(document).ready(function() {
        const configEl = document.getElementById('niosConvertedLeadsConfig');
        const niosDataUrl = configEl ? configEl.dataset.dataUrl : '';
        const columnsEl = document.getElementById('niosConvertedLeadsColumnsData');
        const niosColumns = columnsEl ? JSON.parse(columnsEl.textContent || '[]') : [];
        let niosConvertedLeadsTable = null;

        function getNiosFilterParams() {
            return {
                filter_search: ($('#search').val() || '').trim(),
                batch_id: $('#batch_id').val() || '',
                admission_batch_id: $('#admission_batch_id').val() || '',
                course_flag_id: $('#course_flag_id').val() || '',

                date_from: $('#date_from').val() || '',
                date_to: $('#date_to').val() || '',
                status: $('#status').val() || '',
                reg_fee: $('#reg_fee').val() || '',
                exam_fee: $('#exam_fee').val() || '',
                id_card: $('#id_card').val() || '',
                tma: $('#tma').val() || ''
            };
        }

        function updateNiosUrlWithFilters() {
            const f = getNiosFilterParams();
            const params = new URLSearchParams();
            if (f.search) params.append('search', f.search);
            if (f.batch_id) params.append('batch_id', f.batch_id);
            if (f.admission_batch_id) params.append('admission_batch_id', f.admission_batch_id);
            if (f.course_flag_id) params.append('course_flag_id', f.course_flag_id);
            if (f.date_from) params.append('date_from', f.date_from);
            if (f.date_to) params.append('date_to', f.date_to);
            if (f.status) params.append('status', f.status);
            if (f.reg_fee) params.append('reg_fee', f.reg_fee);
            if (f.exam_fee) params.append('exam_fee', f.exam_fee);
            if (f.id_card) params.append('id_card', f.id_card);
            if (f.tma) params.append('tma', f.tma);
            const newUrl = params.toString() ? `${window.location.pathname}?${params.toString()}` : window.location.pathname;
            window.history.replaceState({ path: newUrl }, '', newUrl);
        }

        function loadNiosFiltersFromUrl() {
            const p = new URLSearchParams(window.location.search);
            if (p.get('search')) $('#search').val(p.get('search'));
            if (p.get('batch_id')) {
                $('#batch_id').val(p.get('batch_id'));
                $('#batch_id').data('selected', p.get('batch_id'));
            }
            if (p.get('admission_batch_id')) $('#admission_batch_id').data('selected', p.get('admission_batch_id'));
            if (p.get('course_flag_id')) $('#course_flag_id').val(p.get('course_flag_id'));
            if (p.get('date_from')) $('#date_from').val(p.get('date_from'));
            if (p.get('date_to')) $('#date_to').val(p.get('date_to'));
            if (p.get('status')) $('#status').val(p.get('status'));
            if (p.get('reg_fee')) $('#reg_fee').val(p.get('reg_fee'));
            if (p.get('exam_fee')) $('#exam_fee').val(p.get('exam_fee'));
            if (p.get('id_card')) $('#id_card').val(p.get('id_card'));
            if (p.get('tma')) $('#tma').val(p.get('tma'));
        }

        function reloadNiosTable() {
            if (niosConvertedLeadsTable) {
                niosConvertedLeadsTable.ajax.reload(null, false);
            }
        }

        function syncNiosMobileCards(json) {
            const $cards = $('#niosMobileCards');
            const $empty = $('#niosMobileEmpty');
            if (!json || !Array.isArray(json.data)) {
                $cards.empty();
                $empty.addClass('d-none');
                return;
            }
            if (json.data.length === 0) {
                $cards.empty();
                $empty.removeClass('d-none');
                return;
            }
            $empty.addClass('d-none');
            let html = '';
            json.data.forEach(function(row) {
                html += row.mobile_card || '';
            });
            $cards.html(html);
        }

        function loadAdmissionBatchesByBatch(batchId, selectedId, done) {
            const $admission = $('#admission_batch_id');
            $admission.html('<option value="">Loading...</option>');
            if (!batchId) {
                $admission.html('<option value="">All Admission Batches</option>');
                if (typeof done === 'function') done();
                return;
            }
            $.get(`/api/admission-batches/by-batch/${batchId}`).done(function(list) {
                let opts = '<option value="">All Admission Batches</option>';
                list.forEach(function(i) {
                    const sel = String(selectedId) === String(i.id) ? 'selected' : '';
                    opts += `<option value="${i.id}" ${sel}>${i.title}</option>`;
                });
                $admission.html(opts);
            }).fail(function() {
                $admission.html('<option value="">All Admission Batches</option>');
            }).always(function() {
                if (typeof done === 'function') done();
            });
        }

        function initNiosDataTable() {
            $('#convertedLeadsTable').removeClass('data_table_basic');
            if ($.fn.DataTable.isDataTable('#convertedLeadsTable')) {
                $('#convertedLeadsTable').DataTable().destroy();
            }
            niosConvertedLeadsTable = $('#convertedLeadsTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: niosDataUrl,
                    type: 'GET',
                    data: function(d) {
                        $.extend(d, getNiosFilterParams());
                    },
                    dataSrc: function(json) {
                        syncNiosMobileCards(json);
                        return json.data;
                    },
                    error: function() {
                        if (typeof showToast === 'function') {
                            showToast('Error loading National Institute of Open Schooling converted leads.', 'error');
                        }
                    }
                },
                pageLength: 25,
                lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
                order: [],
                ordering: false,
                dom: 'Bfrtip',
                buttons: ['csv', 'excel', 'print', 'pdf'],
                scrollX: true,
                autoWidth: false,
                columns: niosColumns,
                drawCallback: function() {
                    const api = this.api();
                    const json = api.ajax.json();
                    if (json) syncNiosMobileCards(json);
                    $('#selectAll').prop('checked', false);
                    $('.batch-actions-toolbar').hide();
                    $('.selected-count').text('0 selected');
                }
            });
        }

        loadNiosFiltersFromUrl();
        const batchDataSelected = $('#batch_id').data('selected');
        const admissionDataSelected = $('#admission_batch_id').data('selected');
        if (batchDataSelected) {
            $('#batch_id').val(String(batchDataSelected));
        }
        loadAdmissionBatchesByBatch($('#batch_id').val() || batchDataSelected, admissionDataSelected, initNiosDataTable);

        $('#filterForm').on('submit', function(e) {
            e.preventDefault();
            updateNiosUrlWithFilters();
            reloadNiosTable();
        });

        $('#niosClearFilters').on('click', function(e) {
            e.preventDefault();
            const form = document.getElementById('filterForm');
            if (form) form.reset();
            $('#batch_id').data('selected', '');
            $('#admission_batch_id').data('selected', '');
            loadAdmissionBatchesByBatch('', '', function() {
                window.history.replaceState({}, '', window.location.pathname);
                reloadNiosTable();
            });
        });

        $('#batch_id').on('change', function() {
            const bid = $(this).val();
            loadAdmissionBatchesByBatch(bid, '', null);
        });

        $(document).on('click', '.update-register-btn', function(e) {
            e.preventDefault();
            const url = $(this).data('url');
            const title = $(this).data('title');
            show_small_modal(url, title);
        });

        $(document).on('change', '#selectAll', function() {
            $('#convertedLeadsTable tbody .row-checkbox').prop('checked', $(this).prop('checked'));
        });

        // Handle ID card generation form submission
        $(document).off('submit', '.id-card-generate-form').on('submit', '.id-card-generate-form', function(e) {
            e.preventDefault();
            e.stopImmediatePropagation();

            const form = $(this);
            const button = form.find('button[type="submit"]');

            if (button.prop('disabled')) {
                return false;
            }

            const originalText = button.html();
            const loadingText = button.data('loading-text');

            button.prop('disabled', true).html('<i class="ti ti-loader-2 spin"></i> ' + loadingText);

            $.ajax({
                url: form.attr('action'),
                type: 'POST',
                data: form.serialize(),
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        toast_success(response.message);
                        setTimeout(function() {
                            if ($.fn.DataTable.isDataTable('#convertedLeadsTable')) {
                                $('#convertedLeadsTable').DataTable().ajax.reload(null, false);
                            } else {
                                location.reload();
                            }
                        }, 600);
                    }
                },
                error: function(xhr) {
                    console.error('Error generating ID card:', xhr);
                    toast_error('Error generating ID card. Please try again.');
                    button.prop('disabled', false).html(originalText);
                }
            });

            return false;
        });

        // Inline editing functionality
        $(document).on('click', '.edit-btn', function(e) {
            e.preventDefault();
            e.stopPropagation();

            const container = $(this).closest('.inline-edit');
            if (container.hasClass('converted-lead-subject-area-field')) {
                return;
            }

            const field = container.data('field');
            const id = container.data('id');
            const currentValue = container.data('current') !== undefined ? String(container.data('current')).trim() : container.find('.display-value').text().trim();
            const currentId = container.data('current-id') !== undefined ? String(container.data('current-id')).trim() : '';

            if (container.hasClass('editing')) {
                return;
            }

            $('.inline-edit.editing').not(container).each(function() {
                $(this).removeClass('editing');
                $(this).find('.edit-form').remove();
            });

            let editForm = '';

            if (field === 'subject_id') {
                const courseId = container.data('course-id');
                editForm = createSubjectSelect(courseId, currentId);
            } else if (field === 'batch_id') {
                const courseId = container.data('course-id');
                editForm = createBatchSelect(courseId, currentId);
            } else if (field === 'admission_batch_id') {
                const batchId = container.data('batch-id');
                editForm = createAdmissionBatchSelect(batchId, currentId);
            } else if (field === 'academic_assistant_id') {
                editForm = createAcademicAssistantSelect(currentId);
            } else if (['status', 'reg_fee', 'exam_fee', 'id_card', 'tma'].includes(field)) {
                editForm = createSelectField(field, currentValue);
            } else if (field === 'phone') {
                const currentCode = container.siblings('.inline-code-value').data('current') || '';
                editForm = createPhoneField(currentCode, currentValue);
            } else {
                editForm = createInputField(field, currentValue);
            }

            container.addClass('editing');
            container.append(editForm);

            // Load options for select fields that need dynamic loading
            if (field === 'subject_id') {
                const courseId = container.data('course-id');
                const select = container.find('select');
                loadSubjects(courseId, select, currentId);
            } else if (field === 'batch_id') {
                const courseId = container.data('course-id');
                const select = container.find('select');
                loadBatches(courseId, select, currentId);
            } else if (field === 'admission_batch_id') {
                const batchId = container.data('batch-id');
                const select = container.find('select');
                loadAdmissionBatches(batchId, select, currentId);
            } else if (field === 'academic_assistant_id') {
                const select = container.find('select');
                loadAcademicAssistants(select, currentId);
            } else {
                container.find('input, select').first().focus();
            }
        });

        // Save inline edit
        $(document).off('click.saveInline').on('click.saveInline', '.save-edit', function(e) {
            e.preventDefault();
            e.stopPropagation();

            const container = $(this).closest('.inline-edit');
            if (container.hasClass('converted-lead-subject-area-field')) {
                return;
            }

            const field = container.data('field');
            const id = container.data('id');
            let value;
            if (field === 'phone') {
                value = container.find('input[type="text"]').val();
            } else {
                value = container.find('input, select').val();
            }
            let extra = {};
            if (field === 'phone') {
                const codeVal = container.find('select[name="code"]').val();
                extra = {
                    code: codeVal
                };
            }

            const btn = $(this);
            if (btn.data('busy')) return;
            btn.data('busy', true);
            btn.prop('disabled', true).html('<i class="ti ti-loader-2 spin"></i>');

            $.ajax({
                url: `/admin/converted-leads/${id}/inline-update`,
                method: 'POST',
                data: $.extend({
                    field: field,
                    value: value,
                    _token: $('meta[name="csrf-token"]').attr('content')
                }, extra),
                success: function(response) {
                    if (response.success) {
                        // Update display value with the response value (which should be the title, not ID)
                        let displayValue = response.value || 'N/A';
                        container.find('.display-value').text(displayValue);
                        // Update the data-current attribute with the new display value
                        container.data('current', displayValue);
                        // Update data-current-id for fields that use it (store the ID, not the display value)
                        if (field === 'batch_id' || field === 'subject_id' || field === 'admission_batch_id' || field === 'academic_assistant_id') {
                            container.data('current-id', value || '');
                        }
                        if (field === 'phone') {
                            const codeVal = extra.code || '';
                            container.siblings('.inline-code-value').data('current', codeVal);
                        }
                        toast_success(response.message);
                    } else {
                        toast_error(response.error || 'Update failed');
                    }
                },
                error: function(xhr) {
                    let errorMessage = 'Update failed';
                    if (xhr.responseJSON) {
                        if (xhr.responseJSON.error) {
                            errorMessage = xhr.responseJSON.error;
                        } else if (xhr.responseJSON.errors) {
                            // Handle validation errors
                            const errors = xhr.responseJSON.errors;
                            const fieldErrors = Object.values(errors).flat();
                            errorMessage = fieldErrors.join(', ');
                        }
                    }
                    toast_error(errorMessage);
                },
                complete: function() {
                    btn.data('busy', false);
                    container.removeClass('editing');
                    container.find('.edit-form').remove();
                }
            });
        });

        // Cancel inline edit
        $(document).on('click', '.cancel-edit', function(e) {
            e.preventDefault();
            e.stopPropagation();

            const container = $(this).closest('.inline-edit');
            container.removeClass('editing');
            container.find('.edit-form').remove();
        });

        // Helper functions for creating form elements
        function createInputField(field, currentValue) {
            if (field === 'dob') {
                const today = new Date().toISOString().split('T')[0];
                const value = (currentValue && currentValue !== 'N/A') ? currentValue : '';
                return `
                    <div class="edit-form">
                        <input type="date" max="${today}" value="${value}" class="form-control form-control-sm">
                        <div class="btn-group mt-1">
                            <button class="btn btn-success btn-sm save-edit">Save</button>
                            <button class="btn btn-secondary btn-sm cancel-edit">Cancel</button>
                        </div>
                    </div>
                `;
            }

            const inputType = 'text';
            const displayValue = currentValue === 'N/A' ? '' : currentValue;
            const commonAttrs = 'autocomplete="off" autocapitalize="off" spellcheck="false" name="inline-temp"';
            const valueAttr = `value="${displayValue}"`;
            return `
                <div class="edit-form">
                    <input type="${inputType}" ${valueAttr} ${commonAttrs} class="form-control form-control-sm">
                    <div class="btn-group mt-1">
                        <button type="button" class="btn btn-success btn-sm save-edit">Save</button>
                        <button type="button" class="btn btn-secondary btn-sm cancel-edit">Cancel</button>
                    </div>
                </div>
            `;
        }

        function createPhoneField(currentCode, currentPhone) {
            const codeOptionsEl = document.getElementById('country-codes-json');
            let codeOptions = {};
            try {
                codeOptions = codeOptionsEl ? JSON.parse(codeOptionsEl.textContent || '{}') : {};
            } catch (e) {
                codeOptions = {};
            }
            const buildOptions = (selected) => {
                let opts = '<option value="">Select Country</option>';
                for (const c in codeOptions) {
                    const isSel = String(selected) === String(c) ? 'selected' : '';
                    opts += `<option value="${c}" ${isSel}>${c} - ${codeOptions[c]}</option>`;
                }
                return opts;
            };
            const safePhone = (currentPhone && currentPhone !== 'N/A') ? currentPhone : '';
            return `
                <div class="edit-form">
                    <div class="row g-1">
                        <div class="col-5">
                            <select class="form-select form-select-sm" name="code">
                                ${buildOptions(currentCode)}
                            </select>
                        </div>
                        <div class="col-7">
                            <input type="text" value="${safePhone}" class="form-control form-control-sm" placeholder="Phone number">
                        </div>
                    </div>
                    <div class="btn-group mt-1">
                        <button type="button" class="btn btn-success btn-sm save-edit">Save</button>
                        <button type="button" class="btn btn-secondary btn-sm cancel-edit">Cancel</button>
                    </div>
                </div>
            `;
        }

        function createSelectField(field, currentValue) {
            let options = '';
            const selectedValue = currentValue === 'N/A' ? '' : currentValue;

            switch (field) {
                case 'status':
                    options = '<option value="">Select Status</option>';
                    options += `<option value="Paid" ${selectedValue === 'Paid' ? 'selected' : ''}>Paid</option>`;
                    options += `<option value="Received" ${selectedValue === 'Received' ? 'selected' : ''}>Received</option>`;
                    options += `<option value="Admission cancel" ${selectedValue === 'Admission cancel' ? 'selected' : ''}>Admission cancel</option>`;
                    options += `<option value="Active" ${selectedValue === 'Active' ? 'selected' : ''}>Active</option>`;
                    options += `<option value="Inactive" ${selectedValue === 'Inactive' ? 'selected' : ''}>Inactive</option>`;
                    break;
                case 'reg_fee':
                    options = '<option value="">Select Registration Fee</option>';
                    options += `<option value="Handover -1" ${selectedValue === 'Handover -1' ? 'selected' : ''}>Handover -1</option>`;
                    options += `<option value="Handover - 2" ${selectedValue === 'Handover - 2' ? 'selected' : ''}>Handover - 2</option>`;
                    options += `<option value="Handover - 3" ${selectedValue === 'Handover - 3' ? 'selected' : ''}>Handover - 3</option>`;
                    options += `<option value="Handover - 4" ${selectedValue === 'Handover - 4' ? 'selected' : ''}>Handover - 4</option>`;
                    options += `<option value="Handover - 5" ${selectedValue === 'Handover - 5' ? 'selected' : ''}>Handover - 5</option>`;
                    options += `<option value="Paid" ${selectedValue === 'Paid' ? 'selected' : ''}>Paid</option>`;
                    options += `<option value="Admission cancel" ${selectedValue === 'Admission cancel' ? 'selected' : ''}>Admission cancel</option>`;
                    break;
                case 'exam_fee':
                    options = '<option value="">Select EXAM FEE</option>';
                    options += `<option value="Pending" ${selectedValue === 'Pending' ? 'selected' : ''}>Pending</option>`;
                    options += `<option value="Not Paid" ${selectedValue === 'Not Paid' ? 'selected' : ''}>Not Paid</option>`;
                    options += `<option value="Paid" ${selectedValue === 'Paid' ? 'selected' : ''}>Paid</option>`;
                    break;
                case 'id_card':
                    options = '<option value="">Select ID CARD</option>';
                    options += `<option value="processing" ${selectedValue === 'processing' ? 'selected' : ''}>processing</option>`;
                    options += `<option value="download" ${selectedValue === 'download' ? 'selected' : ''}>download</option>`;
                    options += `<option value="not downloaded" ${selectedValue === 'not downloaded' ? 'selected' : ''}>not downloaded</option>`;
                    break;
                case 'tma':
                    options = '<option value="">Select TMA</option>';
                    options += `<option value="Uploaded" ${selectedValue === 'Uploaded' ? 'selected' : ''}>Uploaded</option>`;
                    options += `<option value="Not Upload" ${selectedValue === 'Not Upload' ? 'selected' : ''}>Not Upload</option>`;
                    break;
            }

            return `
                <div class="edit-form">
                    <select class="form-select form-select-sm">
                        ${options}
                    </select>
                    <div class="btn-group mt-1">
                        <button type="button" class="btn btn-success btn-sm save-edit">Save</button>
                        <button type="button" class="btn btn-secondary btn-sm cancel-edit">Cancel</button>
                    </div>
                </div>
            `;
        }

        function createSubjectSelect(courseId, currentId) {
            return `
                <div class="edit-form">
                    <select class="form-select form-select-sm">
                        <option value="">Loading...</option>
                    </select>
                    <div class="btn-group mt-1">
                        <button type="button" class="btn btn-success btn-sm save-edit">Save</button>
                        <button type="button" class="btn btn-secondary btn-sm cancel-edit">Cancel</button>
                    </div>
                </div>
            `;
        }

        function createBatchSelect(courseId, currentId) {
            return `
                <div class="edit-form">
                    <select class="form-select form-select-sm">
                        <option value="">Loading...</option>
                    </select>
                    <div class="btn-group mt-1">
                        <button type="button" class="btn btn-success btn-sm save-edit">Save</button>
                        <button type="button" class="btn btn-secondary btn-sm cancel-edit">Cancel</button>
                    </div>
                </div>
            `;
        }

        function createAdmissionBatchSelect(batchId, currentId) {
            return `
                <div class="edit-form">
                    <select class="form-select form-select-sm">
                        <option value="">Loading...</option>
                    </select>
                    <div class="btn-group mt-1">
                        <button type="button" class="btn btn-success btn-sm save-edit">Save</button>
                        <button type="button" class="btn btn-secondary btn-sm cancel-edit">Cancel</button>
                    </div>
                </div>
            `;
        }

        function createAcademicAssistantSelect(currentId) {
            return `
                <div class="edit-form">
                    <select class="form-select form-select-sm">
                        <option value="">Loading...</option>
                    </select>
                    <div class="btn-group mt-1">
                        <button type="button" class="btn btn-success btn-sm save-edit">Save</button>
                        <button type="button" class="btn btn-secondary btn-sm cancel-edit">Cancel</button>
                    </div>
                </div>
            `;
        }


        function loadSubjects(courseId, select, currentId) {
            if (!courseId) {
                select.html('<option value="">No course selected</option>');
                return;
            }

            $.get(`/api/subjects/by-course/${courseId}`)
                .done(function(subjects) {
                    let options = '<option value="">Select Subject</option>';
                    subjects.forEach(function(subject) {
                        // Only select if currentId is not empty and matches
                        const isSelected = (currentId && String(currentId) === String(subject.id)) ? 'selected' : '';
                        options += `<option value="${subject.id}" ${isSelected}>${subject.title}</option>`;
                    });
                    select.html(options);
                    select.focus();
                })
                .fail(function() {
                    select.html('<option value="">Error loading subjects</option>');
                });
        }

        function loadBatches(courseId, select, currentId) {
            if (!courseId) {
                select.html('<option value="">No course selected</option>');
                return;
            }

            $.get(`/api/batches/by-course/${courseId}`)
                .done(function(response) {
                    let options = '<option value="">Select Batch</option>';
                    if (response.success && response.batches) {
                        response.batches.forEach(function(batch) {
                            // Only select if currentId is not empty and matches
                            const isSelected = (currentId && String(currentId) === String(batch.id)) ? 'selected' : '';
                            options += `<option value="${batch.id}" ${isSelected}>${batch.title}</option>`;
                        });
                    }
                    select.html(options);
                    select.focus();
                })
                .fail(function() {
                    select.html('<option value="">Error loading batches</option>');
                });
        }

        function loadAdmissionBatches(batchId, select, currentId) {
            if (!batchId) {
                select.html('<option value="">No batch selected</option>');
                return;
            }

            $.get(`/api/admission-batches/by-batch/${batchId}`)
                .done(function(batches) {
                    let options = '<option value="">Select Admission Batch</option>';
                    batches.forEach(function(batch) {
                        // Only select if currentId is not empty and matches
                        const isSelected = (currentId && String(currentId) === String(batch.id)) ? 'selected' : '';
                        options += `<option value="${batch.id}" ${isSelected}>${batch.title}</option>`;
                    });
                    select.html(options);
                    select.focus();
                })
                .fail(function() {
                    select.html('<option value="">Error loading admission batches</option>');
                });
        }

        function loadAcademicAssistants(select, currentId) {
            $.get('/api/academic-assistants')
                .done(function(assistants) {
                    let options = '<option value="">Select Academic Assistant</option>';
                    assistants.forEach(function(assistant) {
                        // Only select if currentId is not empty and matches
                        const isSelected = (currentId && String(currentId) === String(assistant.id)) ? 'selected' : '';
                        options += `<option value="${assistant.id}" ${isSelected}>${assistant.name}</option>`;
                    });
                    select.html(options);
                    select.focus();
                })
                .fail(function() {
                    select.html('<option value="">Error loading academic assistants</option>');
                });
        }

        // Toggle Academic Verification with confirmation modal
        let academicVerifyUrl = null;
        $(document).off('click', '.toggle-academic-verify-btn').on('click', '.toggle-academic-verify-btn', function(e) {
            e.preventDefault();
            const $btn = $(this);
            const url = $btn.data('url');
            const name = $btn.data('name') || 'this student';
            const isVerified = String($btn.data('verified')) === '1';

            academicVerifyUrl = url;

            const actionText = isVerified ? 'unverify' : 'verify';
            const modalText = `Are you sure you want to ${actionText} academic status for <strong>${name}</strong>?`;
            $('#academicVerifyModalText').html(modalText);
            const $confirmBtn = $('#confirmAcademicVerifyBtn');
            $confirmBtn.removeClass('btn-danger btn-success').addClass(isVerified ? 'btn-danger' : 'btn-success');
            $('#academicVerifyModal').modal('show');
        });

        $('#confirmAcademicVerifyBtn').on('click', function() {
            if (!academicVerifyUrl) return;
            const $confirmBtn = $(this);
            const originalHtml = $confirmBtn.html();
            $confirmBtn.prop('disabled', true).addClass('disabled');
            $.post(academicVerifyUrl, {
                    _token: '{{ csrf_token() }}'
                })
                .done(function(res) {
                    if (res && res.success) {
                        show_alert('success', res.message || 'Updated');
                        $('#academicVerifyModal').modal('hide');
                        setTimeout(function() {
                            if ($.fn.DataTable.isDataTable('#convertedLeadsTable')) {
                                $('#convertedLeadsTable').DataTable().ajax.reload(null, false);
                            } else {
                                location.reload();
                            }
                        }, 400);
                    } else {
                        show_alert('error', (res && res.message) ? res.message : 'Failed to update');
                    }
                })
                .fail(function(xhr) {
                    let msg = 'Failed to update';
                    if (xhr && xhr.responseJSON && xhr.responseJSON.message) {
                        msg = xhr.responseJSON.message;
                    }
                    show_alert('error', msg);
                })
                .always(function() {
                    $confirmBtn.prop('disabled', false).removeClass('disabled').html(originalHtml);
                    academicVerifyUrl = null;
                });
        });

        // Toggle Support Verification with confirmation modal
        let supportVerifyUrl = null;
        $(document).off('click', '.toggle-support-verify-btn').on('click', '.toggle-support-verify-btn', function(e) {
            e.preventDefault();
            const $btn = $(this);
            const url = $btn.data('url');
            const name = $btn.data('name') || 'this student';
            const isVerified = String($btn.data('verified')) === '1';

            supportVerifyUrl = url;

            const actionText = isVerified ? 'unverify' : 'verify';
            const modalText = `Are you sure you want to ${actionText} support status for <strong>${name}</strong>?`;
            $('#supportVerifyModalText').html(modalText);
            const $confirmBtn = $('#confirmSupportVerifyBtn');
            $confirmBtn.removeClass('btn-danger btn-success').addClass(isVerified ? 'btn-danger' : 'btn-success');
            $('#supportVerifyModal').modal('show');
        });

        $('#confirmSupportVerifyBtn').on('click', function() {
            if (!supportVerifyUrl) return;
            const $confirmBtn = $(this);
            const originalHtml = $confirmBtn.html();
            $confirmBtn.prop('disabled', true).addClass('disabled');
            $.post(supportVerifyUrl, {
                    _token: '{{ csrf_token() }}'
                })
                .done(function(res) {
                    if (res && res.success) {
                        show_alert('success', res.message || 'Updated');
                        $('#supportVerifyModal').modal('hide');
                        setTimeout(function() {
                            if ($.fn.DataTable.isDataTable('#convertedLeadsTable')) {
                                $('#convertedLeadsTable').DataTable().ajax.reload(null, false);
                            } else {
                                location.reload();
                            }
                        }, 400);
                    } else {
                        show_alert('error', (res && res.message) ? res.message : 'Failed to update');
                    }
                })
                .fail(function(xhr) {
                    let msg = 'Failed to update';
                    if (xhr && xhr.responseJSON && xhr.responseJSON.message) {
                        msg = xhr.responseJSON.message;
                    }
                    show_alert('error', msg);
                })
                .always(function() {
                    $confirmBtn.prop('disabled', false).removeClass('disabled').html(originalHtml);
                    supportVerifyUrl = null;
                });
        });

        // Handle Change Course modal buttons
        $(document).on('click', '.js-change-course-modal', function(e) {
            e.preventDefault();
            const url = $(this).data('modal-url');
            const title = $(this).data('modal-title') || 'Change Course';
            if (typeof show_ajax_modal === 'function' && url) {
                show_ajax_modal(url, title);
            }
        });

        // Handle cancellation flag modal
        $(document).on('click', '.js-cancel-flag', function(e) {
            e.preventDefault();
            const url = $(this).data('cancel-url');
            const title = $(this).data('modal-title') || 'Cancellation Confirmation';
            if (typeof show_ajax_modal === 'function' && url) {
                show_ajax_modal(url, title);
            }
        });

        // Delegated submit handler for cancellation flag modal
        $(document).on('submit', '#cancelFlagForm', function(e) {
            e.preventDefault();
            const form = $(this);
            const submitUrl = form.data('submit-url');
            if (!submitUrl) {
                return form.off('submit').submit();
            }

            const submitBtn = form.find('button[type="submit"]');
            const originalText = submitBtn.html();
            submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Saving...');

            $.ajax({
                url: submitUrl,
                method: 'POST',
                data: form.serialize(),
                success: function (response) {
                    $('#ajax_modal').modal('hide');
                    if (typeof showToast === 'function') {
                        showToast(response.message, 'success');
                    } else if (typeof toast_success === 'function') {
                        toast_success(response.message);
                    } else {
                        alert(response.message);
                    }
                    if ($.fn.DataTable.isDataTable('#convertedLeadsTable')) {
                        const dt = $('#convertedLeadsTable').DataTable();
                        if (dt.ajax && dt.ajax.url()) {
                            dt.ajax.reload();
                        } else {
                            dt.rows().invalidate().draw(false);
                            location.reload();
                        }
                    } else {
                        location.reload();
                    }
                },
                error: function (xhr) {
                    let message = 'Unable to update cancellation flag.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        message = xhr.responseJSON.message;
                    } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                        message = Object.values(xhr.responseJSON.errors).flat().join('\n');
                    }
                    if (typeof showToast === 'function') {
                        showToast(message, 'error');
                    } else if (typeof toast_error === 'function') {
                        toast_error(message);
                    } else {
                        alert(message);
                    }
                },
                complete: function () {
                    submitBtn.prop('disabled', false).html(originalText);
                }
            });
        });
    });
</script>
@endpush
