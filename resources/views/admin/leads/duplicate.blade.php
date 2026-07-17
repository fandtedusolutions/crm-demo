@extends('layouts.mantis')

@section('title', 'Duplicate Leads')

@section('content')
<!-- [ breadcrumb ] start -->
<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-6">
                <div class="page-header-title">
                    <h5 class="m-b-10">Duplicate Leads Management</h5>
                </div>
            </div>
            <div class="col-md-6">
                <ul class="breadcrumb d-flex justify-content-end">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('leads.index') }}">Leads</a></li>
                    <li class="breadcrumb-item">Duplicate Leads</li>
                </ul>
            </div>
        </div>
    </div>
</div>
<!-- [ breadcrumb ] end -->

@if(request('search_key'))
<!-- [ Search Results Indicator ] start -->
<div class="alert alert-info alert-dismissible fade show" role="alert">
    <div class="d-flex align-items-center">
        <i class="ti ti-search me-2"></i>
        <div class="flex-grow-1">
            <strong>Search Results:</strong> Showing duplicate leads matching "{{ request('search_key') }}"
        </div>
        <a href="{{ route('leads.duplicate') }}" class="btn btn-sm btn-outline-info">
            <i class="ti ti-x"></i> Clear Search
        </a>
    </div>
</div>
<!-- [ Search Results Indicator ] end -->
@endif

<!-- [ Filter Section ] start -->
<div class="row mb-3">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form method="GET" action="{{ route('leads.duplicate') }}" id="dateFilterForm">
                    <div class="row g-3 align-items-end">
                        <!-- From Date -->
                        <div class="col-6 col-md-4 col-lg-2">
                            <label for="date_from" class="form-label">From Date</label>
                            <input type="date" class="form-control form-control-sm" name="date_from" id="date_from"
                                value="{{ $fromDate }}">
                        </div>

                        <!-- To Date -->
                        <div class="col-6 col-md-4 col-lg-2">
                            <label for="date_to" class="form-label">To Date</label>
                            <input type="date" class="form-control form-control-sm" name="date_to" id="date_to"
                                value="{{ $toDate }}">
                        </div>

                        <!-- Status -->
                        <div class="col-6 col-md-4 col-lg-2">
                            <label for="filter_lead_status_id" class="form-label">Status</label>
                            <select class="form-select form-select-sm" name="lead_status_id" id="filter_lead_status_id">
                                <option value="">All Statuses</option>
                                @foreach($leadStatuses as $status)
                                <option value="{{ $status->id }}" {{ request('lead_status_id') == $status->id ? 'selected' : '' }}>
                                    {{ $status->title }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Source -->
                        <div class="col-6 col-md-4 col-lg-2">
                            <label for="filter_lead_source_id" class="form-label">Source</label>
                            <select class="form-select form-select-sm" name="lead_source_id" id="filter_lead_source_id">
                                <option value="">All Sources</option>
                                @foreach($leadSources as $source)
                                <option value="{{ $source->id }}" {{ request('lead_source_id') == $source->id ? 'selected' : '' }}>
                                    {{ $source->title }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Course -->
                        <div class="col-6 col-md-4 col-lg-2">
                            <label for="course_id" class="form-label">Course</label>
                            <select class="form-select form-select-sm" name="course_id" id="course_id">
                                <option value="">All Courses</option>
                                @foreach($courses as $course)
                                <option value="{{ $course->id }}" {{ request('course_id') == $course->id ? 'selected' : '' }}>
                                    {{ $course->title }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Rating -->
                        <div class="col-6 col-md-4 col-lg-2">
                            <label for="rating" class="form-label">Rating</label>
                            <select class="form-select form-select-sm" name="rating" id="rating">
                                <option value="">All Ratings</option>
                                @for($i = 1; $i <= 10; $i++)
                                    <option value="{{ $i }}" {{ request('rating') == $i ? 'selected' : '' }}>
                                    {{ $i }}/10
                                    </option>
                                    @endfor
                            </select>
                        </div>

                        <!-- Telecaller (conditional) -->
                        @if(!$isTelecaller || $isTeamLead)
                        <div class="col-6 col-md-4 col-lg-2">
                            <label for="telecaller_id" class="form-label">Telecaller</label>
                            <select class="form-select form-select-sm" name="telecaller_id" id="telecaller_id_filter">
                                <option value="">All Telecallers</option>
                                @foreach($telecallers as $telecaller)
                                <option value="{{ $telecaller->id }}" {{ request('telecaller_id') == $telecaller->id ? 'selected' : '' }}>
                                    {{ $telecaller->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        @endif

                        <!-- Lead Type (only for Admin/Super Admin, Senior Manager, General Manager) -->
                        @if($isAdminOrSuperAdmin || $isSeniorManager || $isGeneralManager)
                        <div class="col-6 col-md-4 col-lg-2">
                            <label for="lead_type" class="form-label">Lead Type</label>
                            <select class="form-select form-select-sm" name="lead_type" id="lead_type">
                                <option value="">All Leads</option>
                                <option value="normal" {{ request('lead_type') == 'normal' ? 'selected' : '' }}>Normal Leads</option>
                                <option value="pullback" {{ request('lead_type') == 'pullback' ? 'selected' : '' }}>Pullback Lead</option>
                            </select>
                        </div>
                        @endif

                        <!-- Action Buttons -->
                        <div class="col-12 col-lg-2">
                            <div class="d-flex gap-2 flex-wrap">
                                <button type="submit" class="btn btn-primary btn-sm flex-fill flex-lg-grow-0">
                                    <i class="ti ti-filter me-1"></i> Filter
                                </button>
                                <a href="{{ route('leads.duplicate') }}" class="btn btn-outline-secondary btn-sm flex-fill flex-lg-grow-0">
                                    <i class="ti ti-x me-1"></i> Clear
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
            <div class="card-header">
                <!-- Desktop Header -->
                <div class="d-none d-md-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Duplicate Leads (Same Code & Phone)</h5>
                    <div class="d-flex gap-2">
                        <a href="{{ route('leads.export', request()->query()) }}" class="btn btn-outline-info btn-sm px-3 js-export-excel"
                            title="Export to Excel">
                            <i class="ti ti-download"></i> Export Excel
                        </a>
                        @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_team_lead() || \App\Helpers\RoleHelper::is_general_manager() || \App\Helpers\RoleHelper::is_senior_manager())
                        <a href="javascript:void(0);" class="btn btn-primary btn-sm px-3"
                            onclick="show_ajax_modal('{{ route('leads.add') }}', 'Add New Lead')">
                            <i class="ti ti-plus"></i> Add Lead
                        </a>
                        <a href="javascript:void(0);" class="btn btn-outline-primary btn-sm px-3"
                            onclick="show_ajax_modal('{{ route('leads.bulk-upload.test') }}', 'Bulk Upload Leads')">
                            <i class="ti ti-upload"></i> Bulk Upload
                        </a>
                        <a href="{{ route('admin.leads.bulk-reassign') }}" class="btn btn-outline-success btn-sm px-3">
                            <i class="ti ti-users"></i> Bulk Reassign
                        </a>
                        @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_general_manager())
                        <a href="javascript:void(0);" class="btn btn-outline-danger btn-sm px-3"
                            onclick="show_large_modal('{{ route('admin.leads.pullback') }}', 'Pullback Lead')">
                            <i class="ti ti-arrow-back-up"></i> Pullback Lead
                        </a>
                        @endif
                        @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_general_manager() || \App\Helpers\RoleHelper::is_senior_manager() || \App\Helpers\RoleHelper::is_team_lead())
                        <a href="javascript:void(0);" class="btn btn-outline-warning btn-sm px-3"
                            onclick="show_large_modal('{{ route('admin.leads.followup') }}', 'Followup Leads')">
                            <i class="ti ti-calendar-event"></i> Followup Leads
                        </a>
                        <a href="javascript:void(0);" class="btn btn-outline-danger btn-sm px-3"
                            onclick="show_ajax_modal('{{ route('admin.leads.bulk-delete') }}', 'Bulk Delete Leads')">
                            <i class="ti ti-trash"></i> Bulk Delete
                        </a>
                        @endif
                        @endif
                    </div>
                </div>

                <!-- Mobile Header -->
                <div class="d-md-none">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0">Duplicate Leads</h5>
                        @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_team_lead() || \App\Helpers\RoleHelper::is_general_manager() || \App\Helpers\RoleHelper::is_senior_manager())
                        <a href="javascript:void(0);" class="btn btn-primary btn-sm"
                            onclick="show_ajax_modal('{{ route('leads.add') }}', 'Add New Lead')">
                            <i class="ti ti-plus"></i> Add
                        </a>
                        @endif
                    </div>

                    <div class="row g-2 mb-2">
                        <div class="col-12">
                            <a href="{{ route('leads.export', request()->query()) }}" class="btn btn-outline-info btn-sm w-100 js-export-excel"
                                title="Export to Excel">
                                <i class="ti ti-download me-1"></i> Export Excel
                            </a>
                        </div>
                    </div>

                    @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_team_lead() || \App\Helpers\RoleHelper::is_general_manager() || \App\Helpers\RoleHelper::is_senior_manager())
                    <div class="row g-2">
                        <div class="col-6">
                            <a href="javascript:void(0);" class="btn btn-outline-primary btn-sm w-100"
                                onclick="show_ajax_modal('{{ route('leads.bulk-upload.test') }}', 'Bulk Upload Leads')">
                                <i class="ti ti-upload me-1"></i> Upload
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="{{ route('admin.leads.bulk-reassign') }}" class="btn btn-outline-success btn-sm w-100">
                                <i class="ti ti-users me-1"></i> Reassign
                            </a>
                        </div>
                        @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_general_manager())
                        <div class="col-6">
                            <a href="javascript:void(0);" class="btn btn-outline-danger btn-sm w-100"
                                onclick="show_large_modal('{{ route('admin.leads.pullback') }}', 'Pullback Lead')">
                                <i class="ti ti-arrow-back-up me-1"></i> Pullback
                            </a>
                        </div>
                        @endif
                        @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_general_manager() || \App\Helpers\RoleHelper::is_senior_manager() || \App\Helpers\RoleHelper::is_team_lead())
                        <div class="col-6">
                            <a href="javascript:void(0);" class="btn btn-outline-warning btn-sm w-100"
                                onclick="show_large_modal('{{ route('admin.leads.followup') }}', 'Followup Leads')">
                                <i class="ti ti-calendar-event me-1"></i> Followup
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="javascript:void(0);" class="btn btn-outline-danger btn-sm w-100"
                                onclick="show_ajax_modal('{{ route('admin.leads.bulk-delete') }}', 'Bulk Delete Leads')">
                                <i class="ti ti-trash me-1"></i> Delete
                            </a>
                        </div>
                        @endif
                    </div>
                    @endif
                </div>
            </div>
            <div class="card-body">

                <!-- Desktop Table View -->
                <div class="d-none d-lg-block">
                    <div class="table-responsive" style="overflow-x: auto;">
                        <table class="table table-hover" id="duplicateLeadsTable" style="min-width: 1900px;">
                            <thead>
                                @php
                                $canViewFirstCreated = $isAdminOrSuperAdmin || $isGeneralManager;
                                @endphp
                                <tr>
                                    <th>#</th>
                                    <th>Actions</th>
                                    @if($isAdminOrSuperAdmin || $isTelecallerRole || $isAcademicAssistant || $isAdmissionCounsellor || $isGeneralManager)
                                    <th>Registration Details</th>
                                    @endif
                                    <th>Created At</th>
                                    @if($canViewFirstCreated)
                                    <th>First Created At</th>
                                    @endif
                                    <th>Name</th>
                                    <th>Profile</th>
                                    <th>Phone</th>
                                    <th>Email</th>
                                    <th>Status</th>
                                    <th>Interest</th>
                                    <th>Rating</th>
                                    <th>Converted</th>
                                    <th>Source</th>
                                    <th>Course</th>
                                    <th>Telecaller</th>
                                    <th>Place</th>
                                    <th>Followup Date</th>
                                    <th>Last Reason</th>
                                    <th>Remarks</th>
                                    <th>Marketing Remarks</th>
                                    <th>Date</th>
                                    <th>Time</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Data will be loaded via AJAX -->
                            </tbody>
                        </table>
                    </div>
                </div>

                <br>
                <hr>
                <br>

                <!-- Mobile Card View -->
                <div class="d-lg-none" id="mobileLeadsContainer">
                    <!-- Data will be loaded via AJAX with lazy loading -->
                </div>

            </div>
        </div>
    </div>
</div>
<!-- [ Main Content ] end -->

@php
// Build columns array for DataTables
$columns = [
['data' => 'index', 'name' => 'index', 'orderable' => false, 'searchable' => false],
['data' => 'actions', 'name' => 'actions', 'orderable' => false, 'searchable' => false],
];

if ($isAdminOrSuperAdmin || $isTelecallerRole || $isAcademicAssistant || $isAdmissionCounsellor || $isGeneralManager) {
$columns[] = ['data' => 'registration_details', 'name' => 'registration_details', 'orderable' => false, 'searchable' => false];
}

$columns = array_merge($columns, [
['data' => 'created_at', 'name' => 'created_at'],
]);

if ($canViewFirstCreated) {
$columns[] = ['data' => 'first_created_at', 'name' => 'first_created_at'];
}

$columns = array_merge($columns, [
['data' => 'name', 'name' => 'name'],
['data' => 'profile', 'name' => 'profile', 'orderable' => false, 'searchable' => false],
['data' => 'phone', 'name' => 'phone'],
['data' => 'email', 'name' => 'email'],
['data' => 'status', 'name' => 'status', 'orderable' => false, 'searchable' => false],
['data' => 'interest', 'name' => 'interest', 'orderable' => false, 'searchable' => false],
['data' => 'rating', 'name' => 'rating', 'orderable' => false, 'searchable' => false],
['data' => 'converted', 'name' => 'converted', 'orderable' => false, 'searchable' => false],
['data' => 'source', 'name' => 'source'],
['data' => 'course', 'name' => 'course'],
['data' => 'telecaller', 'name' => 'telecaller'],
['data' => 'place', 'name' => 'place'],
['data' => 'followup_date', 'name' => 'followup_date'],
['data' => 'last_reason', 'name' => 'last_reason', 'orderable' => false, 'searchable' => false],
['data' => 'remarks', 'name' => 'remarks'],
['data' => 'marketing_remarks', 'name' => 'marketing_remarks'],
['data' => 'date', 'name' => 'date'],
['data' => 'time', 'name' => 'time']
]);
@endphp

@endsection

@push('scripts')
<style>
    /* Fix DataTables responsive dropdown icon issue */
    .dtr-control {
        position: relative;
        cursor: pointer;
    }

    .dtr-control:before {
        content: '+';
        display: inline-block;
        width: 20px;
        height: 20px;
        line-height: 18px;
        text-align: center;
        border: 1px solid #ddd;
        border-radius: 3px;
        background-color: #f8f9fa;
        color: #666;
        font-weight: bold;
        margin-right: 8px;
    }

    .dtr-control.dtr-expanded:before {
        content: '-';
        background-color: #007bff;
        color: white;
        border-color: #007bff;
    }

    /* Remove the problematic sorting_1 class styling */
    .dtr-control.sorting_1:before {
        content: '+';
    }

    /* Improve table responsiveness */
    .table-responsive {
        border: none;
    }

    #leadsTable,
    #duplicateLeadsTable {
        margin-bottom: 0;
    }

    #leadsTable thead th,
    #duplicateLeadsTable thead th {
        border-top: none;
        font-weight: 600;
        background-color: #f8f9fa;
        white-space: nowrap;
    }

    #leadsTable tbody td,
    #duplicateLeadsTable tbody td {
        vertical-align: middle;
        white-space: nowrap;
    }

    /* Fix action buttons in responsive mode */
    .dtr-details {
        background-color: #f8f9fa;
        padding: 10px;
        border-left: 3px solid #007bff;
    }

    .dtr-details li {
        margin-bottom: 5px;
    }

    /* Improve mobile card layout */
    @media (max-width: 991.98px) {
        .card-body {
            padding: 0.75rem;
        }

        .mobile-card {
            margin-bottom: 0.5rem;
        }
    }

    /* Additional responsive improvements */
    @media (max-width: 1200px) {
        .table-responsive {
            font-size: 0.875rem;
        }

        #leadsTable th,
        #leadsTable td {
            padding: 0.5rem 0.25rem;
        }
    }

    /* Enhanced action button styling */
    .btn-sm {
        min-width: 32px;
        height: 32px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .btn-sm i {
        font-size: 12px;
    }

    /* Mobile action buttons layout */
    @media (max-width: 991.98px) {
        .d-flex.gap-1 {
            gap: 0.25rem !important;
        }

        .btn-sm {
            min-width: 28px;
            height: 28px;
            padding: 0.25rem;
        }
    }

    @media (max-width: 768px) {
        .table-responsive {
            font-size: 0.8rem;
        }

        #leadsTable th,
        #leadsTable td {
            padding: 0.375rem 0.125rem;
        }

        .btn-sm {
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
        }
    }

    /* Fix DataTables info and pagination on mobile */
    .dataTables_info,
    .dataTables_paginate {
        font-size: 0.875rem;
    }

    @media (max-width: 576px) {

        .dataTables_info,
        .dataTables_paginate {
            font-size: 0.75rem;
        }

        .dataTables_length,
        .dataTables_filter {
            margin-bottom: 0.5rem;
        }
    }

    /* Copy link button styling */
    .copy-link-btn {
        transition: all 0.3s ease;
    }

    .copy-link-btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
    }

    .copy-link-btn.btn-success {
        animation: pulse 0.6s ease-in-out;
    }

    .copy-link-btn.processing {
        pointer-events: none;
        opacity: 0.7;
    }

    @keyframes pulse {
        0% {
            transform: scale(1);
        }

        50% {
            transform: scale(1.1);
        }

        100% {
            transform: scale(1);
        }
    }
</style>
<script>
    // Initialize DataTables asynchronously to prevent blocking
    $(document).ready(function() {
        const exportBaseUrl = @json(route('leads.export')); // Note: Export may need to be updated for duplicates

        // ULTRA-OPTIMIZED DataTables for duplicate leads - Performance Critical
        // Prevent global initialization for this table
        $('#duplicateLeadsTable').removeClass('data_table_basic');

        // Use setTimeout to defer initialization and allow page to render first
        setTimeout(function() {
            // Destroy existing instance if any
            if ($.fn.DataTable.isDataTable('#duplicateLeadsTable')) {
                $('#duplicateLeadsTable').DataTable().destroy();
            }

            // Get filter values from form
            function getFilterParams() {
                var params = {
                    date_from: $('#date_from').val() || '',
                    date_to: $('#date_to').val() || '',
                    lead_status_id: $('#filter_lead_status_id').val() || '',
                    lead_source_id: $('#filter_lead_source_id').val() || '',
                    course_id: $('#course_id').val() || '',
                    rating: $('#rating').val() || '',
                    telecaller_id: $('#telecaller_id_filter').val() || '',
                    search_key: getUrlParameter('search_key') || @json(request('search_key', ''))
                };

                // Add lead_type if the field exists (only for admin/super admin, senior manager, general manager)
                if ($('#lead_type').length > 0) {
                    params.lead_type = $('#lead_type').val() || '';
                }

                return params;
            }

            function buildQueryString(params) {
                const searchParams = new URLSearchParams();
                Object.keys(params).forEach(function(key) {
                    const value = params[key];
                    if (value !== undefined && value !== null && String(value).trim() !== '') {
                        searchParams.append(key, value);
                    }
                });
                return searchParams.toString();
            }

            function updateExportButtons(filters) {
                const queryString = buildQueryString(filters);
                const exportUrl = queryString ? `${exportBaseUrl}?${queryString}` : exportBaseUrl;
                $('.js-export-excel').attr('href', exportUrl);
            }

            // Get URL parameter
            function getUrlParameter(name) {
                name = name.replace(/[\[]/, '\\[').replace(/[\]]/, '\\]');
                var regex = new RegExp('[\\?&]' + name + '=([^&#]*)');
                var results = regex.exec(location.search);
                return results === null ? '' : decodeURIComponent(results[1].replace(/\+/g, ' '));
            }

            // Update URL with filter parameters
            function updateUrlWithFilters() {
                var filters = getFilterParams();
                var params = new URLSearchParams();

                Object.keys(filters).forEach(function(key) {
                    if (filters[key]) {
                        params.append(key, filters[key]);
                    }
                });

                updateExportButtons(filters);

                var newUrl = window.location.pathname;
                if (params.toString()) {
                    newUrl += '?' + params.toString();
                }

                // Update URL without reloading page
                window.history.pushState({
                    path: newUrl
                }, '', newUrl);
            }

            // Load filters from URL on page load
            function loadFiltersFromUrl() {
                var urlParams = new URLSearchParams(window.location.search);

                if (urlParams.get('date_from')) {
                    $('#date_from').val(urlParams.get('date_from'));
                }
                if (urlParams.get('date_to')) {
                    $('#date_to').val(urlParams.get('date_to'));
                }
                if (urlParams.get('lead_status_id')) {
                    $('#filter_lead_status_id').val(urlParams.get('lead_status_id'));
                }
                if (urlParams.get('lead_source_id')) {
                    $('#filter_lead_source_id').val(urlParams.get('lead_source_id'));
                }
                if (urlParams.get('course_id')) {
                    $('#course_id').val(urlParams.get('course_id'));
                }
                if (urlParams.get('rating')) {
                    $('#rating').val(urlParams.get('rating'));
                }
                if (urlParams.get('telecaller_id')) {
                    $('#telecaller_id_filter').val(urlParams.get('telecaller_id'));
                }
            }

            // Load filters from URL on page load
            loadFiltersFromUrl();
            updateExportButtons(getFilterParams());

            // Store last JSON response for mobile view
            var lastJsonResponse = null;

            // Initialize with AJAX - maximum performance optimizations
            var leadsTable = $('#duplicateLeadsTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('leads.duplicate-data') }}',
                    type: 'GET',
                    data: function(d) {
                        // Merge DataTables parameters with filter parameters
                        var filters = getFilterParams();
                        $.extend(d, filters);
                    },
                    dataSrc: function(json) {
                        // Store JSON response for mobile view
                        lastJsonResponse = json;
                        return json.data;
                    },
                    error: function(xhr, error, thrown) {
                        console.error('DataTables error:', error);
                        showToast('Error loading leads data. Please try again.', 'error');
                    }
                },
                pageLength: 25,
                lengthMenu: [
                    [10, 25, 50, 100, -1],
                    [10, 25, 50, 100, "All"]
                ],
                order: [
                    [3, 'desc']
                ], // Sort by created_at (column 3)
                dom: "Bfrtip",
                buttons: ["csv", "excel", "print", "pdf"],
                stateSave: true,
                scrollCollapse: true,
                // Performance optimizations
                autoWidth: false,
                scrollX: true,
                searchHighlight: false,
                columns: @json($columns),
                // Optimize rendering
                drawCallback: function(settings) {
                    // Initialize tooltips for visible rows
                    var api = this.api();
                    $(api.rows({
                        page: 'current'
                    }).nodes()).find('[data-bs-toggle="tooltip"]').tooltip();

                    // Re-initialize copy link buttons
                    $(api.rows({
                        page: 'current'
                    }).nodes()).find('.copy-link-btn').off('click').on('click', handleCopyLink);

                    // Re-initialize voxbay call buttons
                    $(api.rows({
                        page: 'current'
                    }).nodes()).find('.voxbay-call-btn').off('click').on('click', function() {
                        // Existing voxbay call handler
                    });

                    // Load mobile view data on first draw only
                    if (lastJsonResponse && settings.iDraw === 1) {
                        loadMobileView(lastJsonResponse);
                    }
                },
                language: {
                    processing: "Loading...",
                    emptyTable: "No data available",
                    zeroRecords: "No matching records found",
                    lengthMenu: "Show _MENU_ entries",
                    info: "Showing _START_ to _END_ of _TOTAL_ entries",
                    infoEmpty: "Showing 0 to 0 of 0 entries",
                    infoFiltered: "(filtered from _MAX_ total entries)",
                    search: "Search:",
                    paginate: {
                        first: "First",
                        last: "Last",
                        next: "Next",
                        previous: "Previous"
                    }
                }
            });

            // Reload table when filters change
            $('#dateFilterForm').on('submit', function(e) {
                e.preventDefault();
                updateUrlWithFilters();
                // Reset mobile view state
                mobileViewState.allData = [];
                mobileViewState.currentPage = 1;
                mobileViewState.hasMore = true;
                leadsTable.ajax.reload();
            });

            // Reload on filter change
            $('#filter_lead_status_id, #filter_lead_source_id, #course_id, #rating, #telecaller_id_filter, #lead_type').on('change', function() {
                updateUrlWithFilters();
                // Reset mobile view state
                mobileViewState.allData = [];
                mobileViewState.currentPage = 1;
                mobileViewState.hasMore = true;
                leadsTable.ajax.reload();
            });

            // Handle copy link functionality
            function handleCopyLink(e) {
                e.preventDefault();
                e.stopPropagation();

                if ($(this).hasClass('processing')) {
                    return false;
                }

                $(this).addClass('processing');
                const url = $(this).data('url');
                const fullUrl = url.startsWith('http') ? url : window.location.origin + url;

                const tempInput = document.createElement('input');
                tempInput.value = fullUrl;
                document.body.appendChild(tempInput);
                tempInput.select();
                tempInput.setSelectionRange(0, 99999);

                try {
                    document.execCommand('copy');
                    const originalIcon = $(this).find('i').attr('class');
                    $(this).find('i').removeClass().addClass('ti ti-check');
                    $(this).removeClass('btn-outline-info btn-info').addClass('btn-success');
                    showToast('Registration link copied to clipboard!', 'success');

                    setTimeout(() => {
                        $(this).find('i').removeClass().addClass(originalIcon);
                        $(this).removeClass('btn-success').addClass('btn-outline-info');
                        $(this).removeClass('processing');
                    }, 2000);
                } catch (err) {
                    console.error('Failed to copy: ', err);
                    showToast('Failed to copy link. Please try again.', 'error');
                    $(this).removeClass('processing');
                }

                document.body.removeChild(tempInput);
            }

            // Mobile view pagination state
            var mobileViewState = {
                currentPage: 1,
                pageSize: 25,
                totalRecords: 0,
                allData: [],
                isLoading: false,
                hasMore: true
            };

            // Load all mobile view data from server
            function loadAllMobileViewData(page = 1, append = false) {
                if (mobileViewState.isLoading) return;

                mobileViewState.isLoading = true;
                mobileViewState.currentPage = page;
                const container = $('#mobileLeadsContainer');

                if (!append) {
                    container.empty();
                    mobileViewState.allData = [];
                    mobileViewState.currentPage = 1;
                }

                // Show loading indicator only on first load
                if (!append) {
                    container.html('<div class="text-center py-4"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div><p class="mt-2 text-muted">Loading leads...</p></div>');
                } else {
                    // Show loading on button when appending
                    const btn = $('.load-more-mobile-btn');
                    if (btn.length > 0) {
                        btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Loading...');
                    }
                }

                // Prepare request data
                const requestData = {
                    draw: page,
                    start: (page - 1) * mobileViewState.pageSize,
                    length: mobileViewState.pageSize,
                    order: [{
                        column: 3,
                        dir: 'desc'
                    }],
                    search: {
                        value: '',
                        regex: false
                    }
                };

                // Merge with filter parameters
                const filters = getFilterParams();
                $.extend(requestData, filters);

                // Make AJAX request to load all data
                $.ajax({
                    url: '{{ route('leads.duplicate-data') }}',
                    type: 'GET',
                    data: requestData,
                    success: function(response) {
                        mobileViewState.isLoading = false;

                        if (!response || !response.data) {
                            if (!append && mobileViewState.allData.length === 0) {
                                container.html('<div class="text-center py-4"><div class="text-muted"><i class="ti ti-inbox f-48 mb-3 d-block"></i><h5>No leads found</h5><p>Try adjusting your filters or add a new lead.</p></div></div>');
                            }
                            return;
                        }

                        // Update total records
                        mobileViewState.totalRecords = response.recordsFiltered || response.recordsTotal || 0;

                        // Process and store data
                        if (response.data && Array.isArray(response.data)) {
                            response.data.forEach(function(row) {
                                if (row && row.mobile_view) {
                                    try {
                                        const mobileData = typeof row.mobile_view === 'string' ? JSON.parse(row.mobile_view) : row.mobile_view;
                                        if (mobileData && mobileData.id) {
                                            mobileViewState.allData.push({
                                                data: mobileData,
                                                index: row.index || mobileViewState.allData.length + 1
                                            });
                                        }
                                    } catch (e) {
                                        console.error('Error parsing mobile view data:', e, row);
                                    }
                                }
                            });
                        }

                        // Check if there's more data to load
                        mobileViewState.hasMore = mobileViewState.allData.length < mobileViewState.totalRecords;

                        // Render all loaded data
                        renderMobileViewCards();

                        // Always show load more button if there's more data
                        if (mobileViewState.hasMore && mobileViewState.totalRecords > mobileViewState.allData.length) {
                            // Small delay to ensure rendering is complete
                            setTimeout(function() {
                                showLoadMoreButton();
                            }, 100);
                        } else {
                            // Remove load more button if all data is loaded
                            $('.load-more-mobile-btn').parent().remove();
                        }
                    },
                    error: function(xhr, status, error) {
                        mobileViewState.isLoading = false;
                        console.error('Error loading mobile view data:', error);
                        if (!append && mobileViewState.allData.length === 0) {
                            container.html('<div class="text-center py-4"><div class="alert alert-danger"><i class="ti ti-alert-circle me-2"></i>Error loading leads. Please try again.</div></div>');
                        }
                    }
                });
            }

            // Render all mobile view cards
            function renderMobileViewCards() {
                const container = $('#mobileLeadsContainer');

                // Only clear on first page load
                if (mobileViewState.currentPage === 1) {
                    container.empty();
                }

                if (mobileViewState.allData.length === 0 && !mobileViewState.isLoading) {
                    container.html('<div class="text-center py-4"><div class="text-muted"><i class="ti ti-inbox f-48 mb-3 d-block"></i><h5>No leads found</h5><p>Try adjusting your filters or add a new lead.</p></div></div>');
                    return;
                }

                // Remove existing info before rendering (but keep load more button)
                container.find('.mobile-view-info').remove();

                // Clear existing cards only on first page
                if (mobileViewState.currentPage === 1) {
                    container.find('.card[data-lead-id]').remove();
                }

                // Render all cards (avoid duplicates by checking data-lead-id)
                mobileViewState.allData.forEach(function(item) {
                    // Check if card already exists to avoid duplicates
                    const existingCard = container.find('[data-lead-id="' + item.data.id + '"]');
                    if (existingCard.length === 0) {
                        const cardHtml = renderMobileCard(item.data, item.index);
                        // Insert before load more button if it exists
                        const loadMoreBtn = container.find('.load-more-mobile-btn').parent();
                        if (loadMoreBtn.length > 0) {
                            loadMoreBtn.before(cardHtml);
                        } else {
                            container.append(cardHtml);
                        }
                    }
                });

                // Initialize tooltips and event handlers for mobile cards
                container.find('[data-bs-toggle="tooltip"]').tooltip();
                container.find('.copy-link-btn').off('click').on('click', handleCopyLink);
                // Re-bind voxbay call buttons for mobile cards
                container.find('.voxbay-call-btn').off('click').on('click', function() {
                    // Voxbay handler will be triggered via document delegation
                });

                // Show record count
                updateMobileViewInfo();
            }

            // Show load more button
            function showLoadMoreButton() {
                const container = $('#mobileLeadsContainer');
                if (!container || container.length === 0) {
                    console.error('Mobile container not found');
                    return;
                }

                // Calculate remaining records
                const remaining = mobileViewState.totalRecords - mobileViewState.allData.length;

                if (remaining <= 0) {
                    // Remove button if no more records
                    $('.load-more-mobile-btn').parent().remove();
                    return;
                }

                const existingButton = container.find('.load-more-mobile-btn');

                if (existingButton.length > 0) {
                    // Update existing button
                    existingButton.html('<i class="ti ti-arrow-down me-2"></i>Load More (' + remaining + ' remaining)');
                    existingButton.prop('disabled', false).show();
                } else {
                    // Create new button - make it prominent and visible
                    const loadMoreHtml = '<div class="text-center py-4" style="clear: both; border-top: 1px solid #dee2e6; margin-top: 20px;"><button class="btn btn-outline-primary btn-lg load-more-mobile-btn" onclick="loadMoreMobileData()" style="min-width: 250px; padding: 12px 24px; font-size: 16px;"><i class="ti ti-arrow-down me-2"></i>Load More (' + remaining + ' remaining)</button></div>';
                    container.append(loadMoreHtml);
                }
            }

            // Load more mobile data
            window.loadMoreMobileData = function() {
                if (mobileViewState.hasMore && !mobileViewState.isLoading) {
                    const nextPage = Math.floor(mobileViewState.allData.length / mobileViewState.pageSize) + 1;
                    const btn = $('.load-more-mobile-btn');
                    btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Loading...');
                    loadAllMobileViewData(nextPage, true);
                }
            };

            // Update mobile view info
            function updateMobileViewInfo() {
                const infoHtml = '<div class="alert alert-info mb-3 mobile-view-info"><small><i class="ti ti-info-circle me-1"></i>Showing ' + mobileViewState.allData.length + ' of ' + mobileViewState.totalRecords + ' leads</small></div>';
                const container = $('#mobileLeadsContainer');
                const existingInfo = container.find('.mobile-view-info');
                if (existingInfo.length > 0) {
                    existingInfo.replaceWith(infoHtml);
                } else {
                    container.prepend(infoHtml);
                }
            }

            // Load mobile view with current page data (for initial display)
            function loadMobileView(jsonData) {
                if (!jsonData || !jsonData.data) return;

                // Update total records
                const newTotalRecords = jsonData.recordsFiltered || jsonData.recordsTotal || 0;

                // Always reload if total records changed or if we haven't loaded anything yet
                if (mobileViewState.totalRecords !== newTotalRecords || mobileViewState.allData.length === 0) {
                    mobileViewState.totalRecords = newTotalRecords;
                    mobileViewState.allData = [];
                    mobileViewState.currentPage = 1;
                    mobileViewState.hasMore = true;
                    mobileViewState.isLoading = false;

                    // Load all data for mobile view - start with first page
                    if (mobileViewState.totalRecords > 0) {
                        loadAllMobileViewData(1, false);
                    } else {
                        const container = $('#mobileLeadsContainer');
                        container.html('<div class="text-center py-4"><div class="text-muted"><i class="ti ti-inbox f-48 mb-3 d-block"></i><h5>No leads found</h5><p>Try adjusting your filters or add a new lead.</p></div></div>');
                    }
                }
            }

            // Render mobile card HTML
            function renderMobileCard(data, index) {
                // Validate data
                if (!data || !data.id) {
                    console.error('Invalid data in renderMobileCard:', data);
                    return '';
                }

                // Add data attribute to track lead ID and avoid duplicates
                let cardHtml = '<div class="card mb-2" data-lead-id="' + (data.id || '') + '">';

                const profileStatusClass = (data.profile && data.profile.status === 'incomplete') ? 'bg-danger' :
                    ((data.profile && data.profile.status === 'partial') ? 'bg-warning' :
                        ((data.profile && data.profile.status === 'almost_complete') ? 'bg-info' : 'bg-success'));

                cardHtml += '<div class="card-body p-3">';

                // Header
                cardHtml += '<div class="d-flex align-items-start justify-content-between mb-2">';
                cardHtml += '<div class="d-flex align-items-center flex-grow-1">';
                cardHtml += '<div class="avtar avtar-s rounded-circle bg-light-primary me-2 d-flex align-items-center justify-content-center">';
                const title = data.title || 'N/A';
                const firstChar = title && title.length > 0 ? title.charAt(0).toUpperCase() : '?';
                cardHtml += '<span class="f-14 fw-bold text-primary">' + firstChar + '</span>';
                cardHtml += '</div>';
                cardHtml += '<div class="flex-grow-1">';
                cardHtml += '<small class="text-muted d-block f-10 mb-1">' + (data.created_at || '') + '</small>';
                cardHtml += '<h6 class="mb-0 fw-bold f-14">' + escapeHtml(title) + '</h6>';
                cardHtml += '<small class="text-muted f-11">#' + (index || '') + '</small>';
                cardHtml += '</div></div>';

                // Action buttons
                cardHtml += '<div class="d-flex gap-1">';
                const viewRoute = (data.routes && data.routes.view) ? data.routes.view : '#';
                const editRoute = (data.routes && data.routes.edit) ? data.routes.edit : '#';
                const deleteRoute = (data.routes && data.routes.delete) ? data.routes.delete : '#';
                cardHtml += '<a href="javascript:void(0);" class="btn btn-sm btn-outline-primary" onclick="show_large_modal(\'' + viewRoute + '\', \'View Lead\')" title="View Lead"><i class="ti ti-eye f-12"></i></a>';
                if (data.permissions && data.permissions.can_edit) {
                    cardHtml += '<a href="javascript:void(0);" class="btn btn-sm btn-outline-secondary" onclick="show_ajax_modal(\'' + editRoute + '\', \'Edit Lead\')" title="Edit Lead"><i class="ti ti-edit f-12"></i></a>';
                }
                if (data.permissions && data.permissions.can_delete) {
                    cardHtml += '<a href="javascript:void(0);" class="btn btn-sm btn-outline-danger" onclick="delete_modal(\'' + deleteRoute + '\')" title="Delete Lead"><i class="ti ti-trash f-12"></i></a>';
                }
                cardHtml += '</div></div>';

                // Profile completeness
                cardHtml += '<div class="mb-2">';
                const profileCompleteness = (data.profile && data.profile.completeness) ? data.profile.completeness : 0;
                if (profileCompleteness < 100) {
                    cardHtml += '<div class="d-flex align-items-center">';
                    cardHtml += '<small class="text-muted me-2 f-11">Profile:</small>';
                    cardHtml += '<div class="progress me-2" style="width: 80px; height: 6px;">';
                    cardHtml += '<div class="progress-bar ' + profileStatusClass + '" role="progressbar" style="width: ' + profileCompleteness + '%"></div>';
                    cardHtml += '</div>';
                    const missingFields = (data.profile && data.profile.missing_fields) ? data.profile.missing_fields : '';
                    cardHtml += '<span class="badge ' + profileStatusClass + ' f-10" title="Missing: ' + escapeHtml(missingFields) + '">' + profileCompleteness + '%</span>';
                    cardHtml += '</div>';
                    const missingCount = (data.profile && data.profile.missing_count) ? data.profile.missing_count : 0;
                    if (missingCount > 0) {
                        cardHtml += '<div class="mt-1"><small class="text-muted f-10">Missing: ' + escapeHtml(missingFields) + (missingCount > 5 ? '...' : '') + '</small></div>';
                    }
                } else {
                    cardHtml += '<div class="d-flex align-items-center">';
                    cardHtml += '<small class="text-muted me-2 f-11">Profile:</small>';
                    cardHtml += '<span class="badge bg-success f-10"><i class="ti ti-check me-1"></i> Complete</span>';
                    cardHtml += '</div>';
                }
                cardHtml += '</div>';

                // Lead details
                cardHtml += '<div class="row g-1 mb-2">';
                cardHtml += '<div class="col-6"><div class="d-flex align-items-center"><i class="ti ti-phone f-12 text-muted me-1"></i><small class="text-muted f-11">' + escapeHtml(data.phone || '-') + '</small></div></div>';
                cardHtml += '<div class="col-6"><div class="d-flex align-items-center"><i class="ti ti-mail f-12 text-muted me-1"></i><small class="text-muted f-11">' + escapeHtml(data.email || '-') + '</small></div></div>';
                const statusColor = (data.status && data.status.color_class) ? data.status.color_class : 'bg-secondary';
                const statusTitle = (data.status && data.status.title) ? data.status.title : 'Unknown';
                cardHtml += '<div class="col-6"><div class="d-flex align-items-center"><i class="ti ti-circle f-12 text-muted me-1"></i><span class="badge ' + statusColor + ' f-11">' + escapeHtml(statusTitle) + '</span></div></div>';
                const interestColor = (data.interest && data.interest.color) ? data.interest.color : 'secondary';
                const interestLabel = (data.interest && data.interest.label) ? data.interest.label : 'Not Set';
                cardHtml += '<div class="col-6"><div class="d-flex align-items-center"><i class="ti ti-flame f-12 text-muted me-1"></i><span class="badge bg-' + interestColor + ' f-10">' + escapeHtml(interestLabel) + '</span></div></div>';
                cardHtml += '<div class="col-6"><div class="d-flex align-items-center"><i class="ti ti-star f-12 text-muted me-1"></i><span class="badge bg-primary f-10">' + escapeHtml(data.rating || 'Not Rated') + '</span></div></div>';
                cardHtml += '<div class="col-6"><div class="d-flex align-items-center"><i class="ti ti-user f-12 text-muted me-1"></i><small class="text-muted f-11">' + escapeHtml(data.telecaller || 'Unassigned') + '</small></div></div>';
                cardHtml += '<div class="col-6"><div class="d-flex align-items-center"><i class="ti ti-book f-12 text-muted me-1"></i><small class="text-muted f-11">' + escapeHtml(data.course || '-') + '</small></div></div>';
                const createdDate = data.created_at ? (data.created_at.split(' ')[0] || '') : '';
                cardHtml += '<div class="col-6"><div class="d-flex align-items-center"><i class="ti ti-calendar f-12 text-muted me-1"></i><small class="text-muted f-11">' + createdDate + '</small></div></div>';
                if (data.followup_date) {
                    cardHtml += '<div class="col-6"><div class="d-flex align-items-center"><i class="ti ti-clock f-12 text-muted me-1"></i><span class="badge bg-warning f-10">' + escapeHtml(data.followup_date) + '</span></div></div>';
                }
                if (data.remarks) {
                    const remarksText = data.remarks.length > 50 ? data.remarks.substring(0, 50) + '...' : data.remarks;
                    cardHtml += '<div class="col-12"><div class="d-flex align-items-start"><i class="ti ti-note f-12 text-muted me-1 mt-1"></i><small class="text-muted f-11" title="' + escapeHtml(data.remarks) + '">' + escapeHtml(remarksText) + '</small></div></div>';
                }

                // Registration details
                if (data.student_details) {
                    cardHtml += '<div class="col-12 mt-2"><div class="border-top pt-2">';
                    cardHtml += '<div class="d-flex align-items-center justify-content-between mb-2">';
                    cardHtml += '<small class="text-muted f-11 fw-bold">Registration Details:</small>';
                    cardHtml += '<span class="badge bg-success f-10">Form Submitted</span>';
                    cardHtml += '</div>';
                    cardHtml += '<div class="row g-1">';
                    const courseTitle = (data.student_details.course_title) ? data.student_details.course_title : 'Unknown Course';
                    cardHtml += '<div class="col-6"><small class="text-muted f-10">Course:</small><div class="fw-medium f-11">' + escapeHtml(courseTitle) + '</div></div>';
                    const studentStatus = data.student_details.status || 'pending';
                    const statusBadge = (studentStatus === 'approved') ? 'bg-success' : ((studentStatus === 'rejected') ? 'bg-danger' : 'bg-warning');
                    const statusText = studentStatus ? (studentStatus.charAt(0).toUpperCase() + studentStatus.slice(1)) : 'Pending';
                    cardHtml += '<div class="col-6"><small class="text-muted f-10">Status:</small><div><span class="badge ' + statusBadge + ' f-10">' + escapeHtml(statusText) + '</span></div></div>';
                    if (data.student_details.reviewed_at && (studentStatus === 'approved' || studentStatus === 'rejected')) {
                        const label = studentStatus === 'approved' ? 'Approved on' : 'Rejected on';
                        cardHtml += '<div class="col-12"><small class="text-muted f-10">' + label + ':</small><div class="fw-medium f-11">' + escapeHtml(data.student_details.reviewed_at) + '</div></div>';
                    }
                    // Document verification status
                    const docVerificationStatus = data.student_details.document_verification_status;
                    if (docVerificationStatus !== null && docVerificationStatus !== undefined) {
                        const docBadge = docVerificationStatus === 'verified' ? 'bg-success' : 'bg-warning';
                        const docText = docVerificationStatus === 'verified' ? 'Documents Verified' : 'Documents Pending';
                        cardHtml += '<div class="col-12"><small class="text-muted f-10">Documents:</small><div><span class="badge ' + docBadge + ' f-10">' + escapeHtml(docText) + '</span></div></div>';
                    }
                    cardHtml += '</div>';
                    const regDetailsRoute = (data.routes && data.routes.registration_details) ? data.routes.registration_details : '#';
                    if (data.permissions && data.permissions.can_view_registration) {
                        cardHtml += '<div class="mt-2"><a href="' + regDetailsRoute + '" class="btn btn-sm btn-outline-primary" title="View Registration Details"><i class="ti ti-eye me-1"></i>View Details</a></div>';
                    }
                    cardHtml += '</div></div>';
                } else if (data.routes && data.routes.registration_link && data.permissions && data.permissions.can_view_registration) {
                    // Show registration link and copy link buttons when form hasn't been submitted
                    cardHtml += '<div class="col-12 mt-2"><div class="border-top pt-2">';
                    cardHtml += '<small class="text-muted f-11 fw-bold mb-2 d-block">Registration Details:</small>';
                    cardHtml += '<div class="d-flex gap-1">';
                    cardHtml += '<a href="' + data.routes.registration_link + '" target="_blank" class="btn btn-sm btn-outline-warning" title="Open Registration Form"><i class="ti ti-external-link f-12"></i></a>';
                    cardHtml += '<button type="button" class="btn btn-sm btn-outline-info copy-link-btn" data-url="' + data.routes.registration_link + '" title="Copy Registration Link"><i class="ti ti-copy f-12"></i></button>';
                    cardHtml += '</div>';
                    cardHtml += '</div></div>';
                }

                cardHtml += '</div>';

                // Action buttons
                cardHtml += '<div class="d-flex gap-1 flex-wrap justify-content-between">';
                cardHtml += '<div class="d-flex gap-1">';
                const statusUpdateRoute = (data.routes && data.routes.status_update) ? data.routes.status_update : '#';
                const convertRoute = (data.routes && data.routes.convert) ? data.routes.convert : '#';
                const callLogsRoute = (data.routes && data.routes.call_logs) ? data.routes.call_logs : '#';
                if (data.permissions && data.permissions.can_update_status) {
                    cardHtml += '<a href="javascript:void(0);" class="btn btn-sm btn-outline-warning" onclick="show_ajax_modal(\'' + statusUpdateRoute + '\', \'Update Status\')" title="Update Status"><i class="ti ti-arrow-up f-12"></i></a>';
                }
                if (data.permissions && data.permissions.can_convert) {
                    cardHtml += '<a href="javascript:void(0);" class="btn btn-sm btn-outline-success" onclick="show_ajax_modal(\'' + convertRoute + '\', \'Convert Lead\')" title="Convert Lead"><i class="ti ti-refresh f-12"></i></a>';
                }
                if (data.lead_status_id == 6) {
                    cardHtml += '<a href="https://docs.google.com/forms/d/e/1FAIpQLSchtc8xlKUJehZNmzoKTkRvwLwk4-SGjzKSHM2UFToAhgdTlQ/viewform?usp=sf_link" target="_blank" class="btn btn-sm btn-outline-info" title="Demo Conduction Form"><i class="ti ti-file-text f-12"></i></a>';
                }
                cardHtml += '</div>';
                cardHtml += '<div class="d-flex gap-1">';
                // Call button
                if (data.permissions && data.permissions.can_call && data.permissions.telecaller_id > 0) {
                    cardHtml += '<button class="btn btn-sm btn-outline-success voxbay-call-btn" data-lead-id="' + data.id + '" data-telecaller-id="' + data.permissions.telecaller_id + '" title="Call Lead"><i class="ti ti-phone f-12"></i></button>';
                }
                // WhatsApp button
                if (data.code && data.phone_number) {
                    const phoneNumber = (data.code + data.phone_number).replace(/\D/g, '');
                    const leadName = data.title || 'there';
                    const message = encodeURIComponent('Hi ' + leadName);
                    const whatsappUrl = 'https://wa.me/' + phoneNumber + '?text=' + message;
                    cardHtml += '<a href="' + whatsappUrl + '" target="_blank" class="btn btn-sm btn-success" title="WhatsApp"><i class="ti ti-brand-whatsapp f-12"></i></a>';
                }
                // Call Logs button - show to everyone, including admission counsellor and post sales
                if (data.permissions && data.permissions.can_view_call_logs) {
                    cardHtml += '<a href="' + callLogsRoute + '" class="btn btn-sm btn-info" title="View Call Logs"><i class="ti ti-phone-call f-12"></i></a>';
                }
                cardHtml += '</div></div>';

                cardHtml += '</div></div>';

                return cardHtml;
            }

            function escapeHtml(text) {
                if (!text) return '';
                const map = {
                    '&': '&amp;',
                    '<': '&lt;',
                    '>': '&gt;',
                    '"': '&quot;',
                    "'": '&#039;'
                };
                return String(text).replace(/[&<>"']/g, m => map[m]);
            }
        }, 50); // Small delay to allow page to render first

        // Handle global search form submission
        $('.header-search form, .drp-search form').on('submit', function(e) {
            e.preventDefault();
            const searchValue = $(this).find('input[name="search_key"]').val().trim();
            if (searchValue) {
                window.location.href = '{{ route("leads.duplicate") }}?search_key=' + encodeURIComponent(searchValue);
            } else {
                window.location.href = '{{ route("leads.duplicate") }}';
            }
        });

        // Handle search input enter key
        $('.header-search input, .drp-search input').on('keypress', function(e) {
            if (e.which === 13) { // Enter key
                $(this).closest('form').submit();
            }
        });

        // Action buttons are now directly accessible without dropdown
        // All functionality is handled by onclick attributes on the buttons

        // Copy link functionality for all registration forms
        // Remove any existing event listeners first to prevent double execution
        $('.copy-link-btn').off('click').on('click', function(e) {
            e.preventDefault();
            e.stopPropagation();

            // Prevent double execution by checking if already processing
            if ($(this).hasClass('processing')) {
                return false;
            }

            // Mark as processing
            $(this).addClass('processing');

            const url = $(this).data('url');
            // Check if URL already contains protocol (http/https)
            const fullUrl = url.startsWith('http') ? url : window.location.origin + url;

            // Create a temporary input element
            const tempInput = document.createElement('input');
            tempInput.value = fullUrl;
            document.body.appendChild(tempInput);

            // Select and copy the text
            tempInput.select();
            tempInput.setSelectionRange(0, 99999); // For mobile devices

            try {
                document.execCommand('copy');

                // Show success feedback
                const originalIcon = $(this).find('i').attr('class');
                $(this).find('i').removeClass().addClass('ti ti-check');
                $(this).removeClass('btn-outline-info btn-info').addClass('btn-success');

                // Show toast notification
                showToast('Registration link copied to clipboard!', 'success');

                // Reset button after 2 seconds
                setTimeout(() => {
                    $(this).find('i').removeClass().addClass(originalIcon);
                    $(this).removeClass('btn-success').addClass('btn-outline-info');
                    $(this).removeClass('processing'); // Remove processing flag
                }, 2000);

            } catch (err) {
                console.error('Failed to copy: ', err);
                showToast('Failed to copy link. Please try again.', 'error');
                $(this).removeClass('processing'); // Remove processing flag on error
            }

            // Remove the temporary input
            document.body.removeChild(tempInput);
        });
    });

    // Function to show toast notifications
    function showToast(message, type = 'info') {
        // Create toast element
        const toast = $(`
            <div class="toast align-items-center text-white bg-${type === 'success' ? 'success' : 'danger'} border-0" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body">
                        <i class="ti ti-${type === 'success' ? 'check' : 'alert-circle'} me-2"></i>
                        ${message}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            </div>
        `);

        // Add to toast container or create one
        let toastContainer = $('.toast-container');
        if (toastContainer.length === 0) {
            toastContainer = $('<div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 9999;"></div>');
            $('body').append(toastContainer);
        }

        toastContainer.append(toast);

        // Initialize and show toast
        const bsToast = new bootstrap.Toast(toast[0]);
        bsToast.show();

        // Remove toast element after it's hidden
        toast.on('hidden.bs.toast', function() {
            $(this).remove();
        });
    }

    // Function to show registration details in a modal
</script>
@endpush