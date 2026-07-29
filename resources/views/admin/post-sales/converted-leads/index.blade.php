@extends('layouts.mantis')

@section('title', 'Post-sales Converted Students')

@section('content')
<!-- [ breadcrumb ] start -->
<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-6">
                <div class="page-header-title">
                    <h5 class="m-b-10">Post-sales Converted Students</h5>
                    <p class="m-b-0 text-muted">Review converted students with quick access to their full history.</p>
                </div>
            </div>
            <div class="col-md-6">
                <ul class="breadcrumb d-flex justify-content-end">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item">Post-sales Converted Students</li>
                </ul>
            </div>
        </div>
    </div>
</div>
<!-- [ breadcrumb ] end -->

<!-- [ Filter Section ] start -->
<div class="row mb-3">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form method="GET" action="{{ route('admin.post-sales.converted-leads.index') }}" id="dateFilterForm">
                    <div class="row g-3 align-items-end">
                        <!-- Search -->
                        <div class="col-6 col-md-4 col-lg-3">
                            <label for="search" class="form-label">Search</label>
                            <input type="text" class="form-control form-control-sm" name="search" id="search"
                                value="{{ request('search') }}" placeholder="Name, phone or register no.">
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

                        <!-- Batch -->
                        <div class="col-6 col-md-4 col-lg-2">
                            <label for="batch_id" class="form-label">Batch</label>
                            <select class="form-select form-select-sm" name="batch_id" id="batch_id" data-selected="{{ request('batch_id') }}">
                                <option value="">All Batches</option>
                                @foreach($batches as $batch)
                                <option value="{{ $batch->id }}" {{ request('batch_id') == $batch->id ? 'selected' : '' }}>
                                    {{ $batch->title }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- BDE -->
                        <div class="col-6 col-md-4 col-lg-2">
                            <label for="telecaller_id" class="form-label">BDE</label>
                            <select class="form-select form-select-sm" name="telecaller_id" id="telecaller_id">
                                <option value="">All BDEs</option>
                                @foreach($telecallers as $telecaller)
                                <option value="{{ $telecaller->id }}" {{ request('telecaller_id') == $telecaller->id ? 'selected' : '' }}>
                                    {{ $telecaller->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- From Date -->
                        <div class="col-6 col-md-4 col-lg-2">
                            <label for="date_from" class="form-label">From Date</label>
                            <input type="date" class="form-control form-control-sm" name="date_from" id="date_from"
                                value="{{ request('date_from') }}">
                        </div>

                        <!-- To Date -->
                        <div class="col-6 col-md-4 col-lg-2">
                            <label for="date_to" class="form-label">To Date</label>
                            <input type="date" class="form-control form-control-sm" name="date_to" id="date_to"
                                value="{{ request('date_to') }}">
                        </div>

                        <!-- Action Buttons -->
                        <div class="col-12 col-lg-3">
                            <div class="d-flex gap-2 flex-wrap">
                                <button type="submit" class="btn btn-primary btn-sm flex-fill flex-lg-grow-0">
                                    <i class="ti ti-filter me-1"></i> Filter
                                </button>
                                <a href="{{ route('admin.post-sales.converted-leads.index') }}" class="btn btn-outline-secondary btn-sm flex-fill flex-lg-grow-0" id="clearFiltersBtn">
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
            <div class="card-header d-flex justify-content-between align-items-center">
                <!-- Desktop Header -->
                <div class="d-none d-md-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Converted Students</h5>
                    @include('admin.converted-leads.partials.export-buttons', ['exportPage' => 'post-sales'])
                    <div class="d-flex gap-2">
                        @if(isset($canAssignPostSales) && $canAssignPostSales)
                        <button type="button" class="btn btn-primary btn-sm" onclick="show_large_modal('{{ route('admin.post-sales.converted-leads.bulk-assign') }}', 'Bulk Assign to Post-Sales')">
                            <i class="ti ti-user-plus me-1"></i> Bulk Assign
                        </button>
                        @endif
                        <button type="button" class="btn btn-warning btn-sm" onclick="show_large_modal('{{ route('admin.post-sales.postponed-batches') }}', 'Postponed Batches')">
                            <i class="ti ti-calendar-time me-1"></i> Postponed Batches
                        </button>
                    </div>
                </div>

                <!-- Mobile Header -->
                <div class="d-md-none">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0">Converted Students</h5>
                        <div class="d-flex gap-2">
                            @if(isset($canAssignPostSales) && $canAssignPostSales)
                            <button type="button" class="btn btn-primary btn-sm" onclick="show_large_modal('{{ route('admin.post-sales.converted-leads.bulk-assign') }}', 'Bulk Assign')">
                                <i class="ti ti-user-plus me-1"></i> Bulk Assign
                            </button>
                            @endif
                            <button type="button" class="btn btn-warning btn-sm" onclick="show_large_modal('{{ route('admin.post-sales.postponed-batches') }}', 'Postponed Batches')">
                                <i class="ti ti-calendar-time me-1"></i> Postponed
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body">

                <!-- Desktop Table View -->
                <div class="d-none d-lg-block">
                    <div class="table-responsive" style="overflow-x: auto;">
                        <table class="table table-hover" id="postSalesConvertedTable" style="min-width: 2000px;">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Name</th>
                                    <th>Phone</th>
                                    <th>WhatsApp</th>
                                    @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor())
                                    <th>Parent Phone</th>
                                    @endif
                                    <th>BDE Name</th>
                                    <th>Converted Date</th>
                                    <th>Course</th>
                                    <th>Batch</th>
                                    <th>Admission Batch</th>
                                    <th>Subject</th>
                                    <th>Status</th>
                                    <th>Paid Status</th>
                                    <th>Call Status</th>
                                    <th>Called Date</th>
                                    <th>Call Time</th>
                                    <th>Followup Date</th>
                                    <th>Remark</th>
                                    <th>Has Pending Payment</th>
                                    <th>Paid Amount</th>
                                    <th>Pending Amount</th>
                                    <th>Post-Sales</th>
                                    <th class="text-center">Actions</th>
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
                <div class="d-lg-none" id="mobileConvertedStudentsContainer">
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
    ['data' => 'name', 'name' => 'name'],
    ['data' => 'phone', 'name' => 'phone'],
    ['data' => 'whatsapp', 'name' => 'whatsapp', 'orderable' => false, 'searchable' => false],
];

if (\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor()) {
    $columns[] = ['data' => 'parent_phone', 'name' => 'parent_phone', 'orderable' => false, 'searchable' => false];
}

$columns = array_merge($columns, [
    ['data' => 'bde_name', 'name' => 'bde_name', 'orderable' => false, 'searchable' => false],
    ['data' => 'created_at', 'name' => 'created_at'],
    ['data' => 'course', 'name' => 'course', 'orderable' => false, 'searchable' => false],
    ['data' => 'batch', 'name' => 'batch', 'orderable' => false, 'searchable' => false],
    ['data' => 'admission_batch', 'name' => 'admission_batch', 'orderable' => false, 'searchable' => false],
    ['data' => 'subject', 'name' => 'subject', 'orderable' => false, 'searchable' => false],
    ['data' => 'status', 'name' => 'status', 'orderable' => false, 'searchable' => false],
    ['data' => 'paid_status', 'name' => 'paid_status', 'orderable' => false, 'searchable' => false],
    ['data' => 'call_status', 'name' => 'call_status', 'orderable' => false, 'searchable' => false],
    ['data' => 'called_date', 'name' => 'called_date', 'orderable' => false, 'searchable' => false],
    ['data' => 'called_time', 'name' => 'called_time', 'orderable' => false, 'searchable' => false],
    ['data' => 'postsale_followup', 'name' => 'postsale_followup', 'orderable' => false, 'searchable' => false],
    ['data' => 'post_sales_remarks', 'name' => 'post_sales_remarks', 'orderable' => false, 'searchable' => false],
    ['data' => 'pending_payment', 'name' => 'pending_payment', 'orderable' => false, 'searchable' => false],
    ['data' => 'paid_amount', 'name' => 'paid_amount', 'orderable' => false, 'searchable' => false],
    ['data' => 'pending_amount', 'name' => 'pending_amount', 'orderable' => false, 'searchable' => false],
    ['data' => 'post_sales_user', 'name' => 'post_sales_user', 'orderable' => false, 'searchable' => false],
    ['data' => 'actions', 'name' => 'actions', 'orderable' => false, 'searchable' => false],
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

    /* Improve table responsiveness */
    .table-responsive {
        border: none;
    }

    #postSalesConvertedTable {
        margin-bottom: 0;
    }

    #postSalesConvertedTable thead th {
        border-top: none;
        font-weight: 600;
        background-color: #f8f9fa;
        white-space: nowrap;
    }

    #postSalesConvertedTable tbody td {
        vertical-align: middle;
        white-space: nowrap;
    }

    .cancelled-row > td {
        background-color: #f8d7da !important;
    }

    .cancelled-card {
        border: 1px solid #f5c2c7;
        background-color: #fff5f5;
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
</style>
<div id="postSalesConfig" data-data-url="{{ route('admin.post-sales.converted-leads.data') }}" style="display: none;"></div>
@if(isset($canAssignPostSales) && $canAssignPostSales)
<div id="bulkAssignConfig" style="display: none;"
     data-data-url="{{ route('admin.post-sales.converted-leads.bulk-assign.data') }}"
     data-submit-url="{{ route('admin.post-sales.converted-leads.bulk-assign.submit') }}"></div>
@endif
<script type="application/json" id="postSalesConvertedColumnsData">
{!! json_encode($columns) !!}
</script>
<script>
    const postSalesConfigEl = document.getElementById('postSalesConfig');
    const convertedLeadsDataUrl = postSalesConfigEl ? postSalesConfigEl.dataset.dataUrl : '';
    const postSalesConvertedColumns = JSON.parse(document.getElementById('postSalesConvertedColumnsData').textContent || '[]');
    // Initialize DataTables asynchronously to prevent blocking
    $(document).ready(function() {
        // ULTRA-OPTIMIZED DataTables - Performance Critical
        // Prevent global initialization for this table
        $('#postSalesConvertedTable').removeClass('data_table_basic');
        
        // Use setTimeout to defer initialization and allow page to render first
        setTimeout(function() {
            // Destroy existing instance if any
            if ($.fn.DataTable.isDataTable('#postSalesConvertedTable')) {
                $('#postSalesConvertedTable').DataTable().destroy();
            }
            
            // Get filter values from form
            function getFilterParams() {
                return {
                    filter_search: $('#search').val() || '', // Renamed to avoid conflict with DataTable's search
                    course_id: $('#course_id').val() || '',
                    batch_id: $('#batch_id').val() || '',
                    telecaller_id: $('#telecaller_id').val() || '',
                    date_from: $('#date_from').val() || '',
                    date_to: $('#date_to').val() || ''
                };
            }

            // Update URL with filter parameters
            function updateUrlWithFilters() {
                const filters = getFilterParams();
                const params = new URLSearchParams();
                
                Object.keys(filters).forEach(function(key) {
                    if (filters[key]) {
                        params.append(key, filters[key]);
                    }
                });
                
                const newUrl = params.toString()
                    ? `${window.location.pathname}?${params.toString()}`
                    : window.location.pathname;
                
                // Update URL without reloading page
                window.history.pushState({path: newUrl}, '', newUrl);
            }

            // Load filters from URL on page load
            function loadFiltersFromUrl() {
                const urlParams = new URLSearchParams(window.location.search);
                
                if (urlParams.get('search')) {
                    $('#search').val(urlParams.get('search'));
                }
                if (urlParams.get('course_id')) {
                    $('#course_id').val(urlParams.get('course_id'));
                }
                if (urlParams.get('batch_id')) {
                    $('#batch_id').data('selected', urlParams.get('batch_id'));
                }
                if (urlParams.get('telecaller_id')) {
                    $('#telecaller_id').val(urlParams.get('telecaller_id'));
                }
                if (urlParams.get('date_from')) {
                    $('#date_from').val(urlParams.get('date_from'));
                }
                if (urlParams.get('date_to')) {
                    $('#date_to').val(urlParams.get('date_to'));
                }
            }
            
            // Store last JSON response for mobile view
            var lastJsonResponse = null;
            
            // Initialize with AJAX - maximum performance optimizations
            var convertedTable = $('#postSalesConvertedTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: convertedLeadsDataUrl,
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
                        showToast('Error loading data. Please try again.', 'error');
                    }
                },
                pageLength: 25,
                lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
                order: [[0, 'desc']], // Sort by id (column 0) descending
                dom: "Bfrtip",
                buttons: ["csv", "excel", "print", "pdf"],
                stateSave: true,
                stateDuration: -1, // Persist state indefinitely (like leads page)
                scrollCollapse: true,
                // Performance optimizations
                autoWidth: false,
                scrollX: true,
                searchHighlight: false,
                columns: postSalesConvertedColumns,
                // Optimize rendering
                drawCallback: function(settings) {
                    // Initialize tooltips for visible rows
                    var api = this.api();
                    $(api.rows({page: 'current'}).nodes()).find('[data-bs-toggle="tooltip"]').tooltip();
                    
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
            
            // Load batches by course
            function loadBatchesByCourse(courseId, selectedBatchId, callback) {
                const $batch = $('#batch_id');
                $batch.html('<option value="">Loading...</option>');
                if (!courseId) {
                    $batch.html('<option value="">All Batches</option>');
                    if (callback) callback();
                    return;
                }
                $.get(`/api/batches/by-course/${courseId}`).done(function(response) {
                    let opts = '<option value="">All Batches</option>';
                    if (response.success && response.batches) {
                        response.batches.forEach(function(b) {
                            const sel = String(selectedBatchId) === String(b.id) ? 'selected' : '';
                            opts += `<option value="${b.id}" ${sel}>${b.title}</option>`;
                        });
                    }
                    $batch.html(opts);
                    // Set the value after populating options
                    if (selectedBatchId) {
                        $batch.val(selectedBatchId);
                    }
                    if (callback) callback();
                }).fail(function() {
                    $batch.html('<option value="">All Batches</option>');
                    if (callback) callback();
                });
            }

            // Load filters from URL on page load
            loadFiltersFromUrl();

            // Initialize batches on page load if course is selected
            const initialCourseId = $('#course_id').val();
            const initialBatchId = $('#batch_id').data('selected');
            if (initialCourseId) {
                loadBatchesByCourse(initialCourseId, initialBatchId, function() {
                    // After batches are loaded, ensure URL is updated
                    updateUrlWithFilters();
                });
            } else {
                // Update URL on initial load even if no filters
                updateUrlWithFilters();
            }

            // Handle clear button
            $('#clearFiltersBtn').on('click', function(e) {
                e.preventDefault();
                // Clear all filters
                $('#search').val('');
                $('#course_id').val('');
                $('#batch_id').html('<option value="">All Batches</option>').val('');
                $('#telecaller_id').val('');
                $('#date_from').val('');
                $('#date_to').val('');
                // Update URL to base path
                window.history.pushState({path: window.location.pathname}, '', window.location.pathname);
                // Reset mobile view state
                mobileViewState.allData = [];
                mobileViewState.currentPage = 1;
                mobileViewState.hasMore = true;
                convertedTable.ajax.reload();
            });

            // Reload table when filters change
            $('#dateFilterForm').on('submit', function(e) {
                e.preventDefault();
                updateUrlWithFilters();
                // Reset mobile view state
                mobileViewState.allData = [];
                mobileViewState.currentPage = 1;
                mobileViewState.hasMore = true;
                convertedTable.ajax.reload();
            });

            // On course change → reload batches
            $('#course_id').on('change', function() {
                const courseId = $(this).val();
                loadBatchesByCourse(courseId, '');
                // Clear batch selection when course changes
                $('#batch_id').val('');
                updateUrlWithFilters();
                // Reset mobile view state
                mobileViewState.allData = [];
                mobileViewState.currentPage = 1;
                mobileViewState.hasMore = true;
                convertedTable.ajax.reload();
            });

            // Reload on filter change
            $('#batch_id, #telecaller_id, #date_from, #date_to').on('change', function() {
                updateUrlWithFilters();
                // Reset mobile view state
                mobileViewState.allData = [];
                mobileViewState.currentPage = 1;
                mobileViewState.hasMore = true;
                convertedTable.ajax.reload();
            });

            // Handle search input with debounce
            let searchTimeout;
            $('#search').on('input', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(function() {
                    updateUrlWithFilters();
                    // Reset mobile view state
                    mobileViewState.allData = [];
                    mobileViewState.currentPage = 1;
                    mobileViewState.hasMore = true;
                    convertedTable.ajax.reload();
                }, 500); // Wait 500ms after user stops typing
            });

            // Handle browser back/forward buttons
            window.addEventListener('popstate', function(event) {
                // Reload filters from URL
                const urlParams = new URLSearchParams(window.location.search);
                $('#search').val(urlParams.get('search') || '');
                $('#course_id').val(urlParams.get('course_id') || '');
                $('#telecaller_id').val(urlParams.get('telecaller_id') || '');
                $('#date_from').val(urlParams.get('date_from') || '');
                $('#date_to').val(urlParams.get('date_to') || '');
                
                // Reload batches if course changed
                const courseId = $('#course_id').val();
                const batchId = urlParams.get('batch_id') || '';
                if (courseId) {
                    loadBatchesByCourse(courseId, batchId);
                } else {
                    $('#batch_id').html('<option value="">All Batches</option>');
                }
                
                // Batch value will be set by loadBatchesByCourse function
                
                // Reset mobile view state
                mobileViewState.allData = [];
                mobileViewState.currentPage = 1;
                mobileViewState.hasMore = true;
                convertedTable.ajax.reload();
            });
            
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
                const container = $('#mobileConvertedStudentsContainer');
                
                if (!append) {
                    container.empty();
                    mobileViewState.allData = [];
                    mobileViewState.currentPage = 1;
                }
                
                // Show loading indicator only on first load
                if (!append) {
                    container.html('<div class="text-center py-4"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div><p class="mt-2 text-muted">Loading converted students...</p></div>');
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
                    order: [{column: 0, dir: 'desc'}],
                    search: {value: '', regex: false}
                };
                
                // Merge with filter parameters
                const filters = getFilterParams();
                $.extend(requestData, filters);
                
                // Make AJAX request to load all data
                $.ajax({
                    url: convertedLeadsDataUrl,
                    type: 'GET',
                    data: requestData,
                    success: function(response) {
                        mobileViewState.isLoading = false;
                        
                        if (!response || !response.data) {
                            if (!append && mobileViewState.allData.length === 0) {
                                container.html('<div class="text-center py-4"><div class="text-muted"><i class="ti ti-inbox f-48 mb-3 d-block"></i><h5>No converted students found</h5><p>Try adjusting your filters.</p></div></div>');
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
                            container.html('<div class="text-center py-4"><div class="alert alert-danger"><i class="ti ti-alert-circle me-2"></i>Error loading data. Please try again.</div></div>');
                        }
                    }
                });
            }
            
            // Render all mobile view cards
            function renderMobileViewCards() {
                const container = $('#mobileConvertedStudentsContainer');
                
                // Only clear on first page load
                if (mobileViewState.currentPage === 1) {
                    container.empty();
                }
                
                if (mobileViewState.allData.length === 0 && !mobileViewState.isLoading) {
                    container.html('<div class="text-center py-4"><div class="text-muted"><i class="ti ti-inbox f-48 mb-3 d-block"></i><h5>No converted students found</h5><p>Try adjusting your filters.</p></div></div>');
                    return;
                }
                
                // Remove existing info before rendering (but keep load more button)
                container.find('.mobile-view-info').remove();
                
                // Clear existing cards only on first page
                if (mobileViewState.currentPage === 1) {
                    container.find('.card[data-student-id]').remove();
                }
                
                // Render all cards (avoid duplicates by checking data-student-id)
                mobileViewState.allData.forEach(function(item) {
                    // Check if card already exists to avoid duplicates
                    const existingCard = container.find('[data-student-id="' + item.data.id + '"]');
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
                
                // Initialize tooltips for mobile cards
                container.find('[data-bs-toggle="tooltip"]').tooltip();
                
                // Show record count
                updateMobileViewInfo();
            }
            
            // Show load more button
            function showLoadMoreButton() {
                const container = $('#mobileConvertedStudentsContainer');
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
                const infoHtml = '<div class="alert alert-info mb-3 mobile-view-info"><small><i class="ti ti-info-circle me-1"></i>Showing ' + mobileViewState.allData.length + ' of ' + mobileViewState.totalRecords + ' converted students</small></div>';
                const container = $('#mobileConvertedStudentsContainer');
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
                        const container = $('#mobileConvertedStudentsContainer');
                        container.html('<div class="text-center py-4"><div class="text-muted"><i class="ti ti-inbox f-48 mb-3 d-block"></i><h5>No converted students found</h5><p>Try adjusting your filters.</p></div></div>');
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

                const statusValue = (data.status || '').toString().toLowerCase();
                const isCancelledFlag = Boolean(data.is_cancelled);
                
                // Add data attribute to track student ID and avoid duplicates
                const cardClasses = ['card', 'mb-2'];
                if (isCancelledFlag) {
                    cardClasses.push('cancelled-card');
                }
                let cardHtml = '<div class="' + cardClasses.join(' ') + '" data-student-id="' + (data.id || '') + '">';
                
                cardHtml += '<div class="card-body p-3">';
                
                // Header
                cardHtml += '<div class="d-flex align-items-start justify-content-between mb-2">';
                cardHtml += '<div class="d-flex align-items-center flex-grow-1">';
                cardHtml += '<div class="avtar avtar-s rounded-circle bg-light-primary me-2 d-flex align-items-center justify-content-center">';
                const name = data.name || 'N/A';
                const firstChar = name && name.length > 0 ? name.charAt(0).toUpperCase() : '?';
                cardHtml += '<span class="f-14 fw-bold text-primary">' + firstChar + '</span>';
                cardHtml += '</div>';
                cardHtml += '<div class="flex-grow-1">';
                cardHtml += '<small class="text-muted d-block f-10 mb-1">' + (data.created_at || '') + '</small>';
                cardHtml += '<h6 class="mb-0 fw-bold f-14">' + escapeHtml(name) + '</h6>';
                cardHtml += '<small class="text-muted f-11">#' + (index || '') + ' - ' + escapeHtml(data.register_number || 'No register #') + '</small>';
                cardHtml += '</div></div>';
                
                // Action buttons
                cardHtml += '<div class="d-flex gap-1">';
                const viewRoute = (data.routes && data.routes.view) ? data.routes.view : '#';
                const statusUpdateRoute = (data.routes && data.routes.status_update) ? data.routes.status_update : '#';
                const invoiceRoute = (data.routes && data.routes.invoice) ? data.routes.invoice : null;
                const cancelFlagRoute = (data.routes && data.routes.cancel_flag) ? data.routes.cancel_flag : null;
                cardHtml += '<a href="' + viewRoute + '" class="btn btn-sm btn-outline-primary" title="View Details"><i class="ti ti-eye f-12"></i></a>';
                if (invoiceRoute) {
                    cardHtml += '<a href="' + invoiceRoute + '" class="btn btn-sm btn-success" title="View Invoice"><i class="ti ti-receipt f-12"></i></a>';
                }
                cardHtml += '<button type="button" class="btn btn-sm btn-outline-success" title="Status Update" onclick="show_ajax_modal(\'' + statusUpdateRoute + '\', \'Status Update\')"><i class="ti ti-edit f-12"></i></button>';
                if (cancelFlagRoute && statusValue === 'cancel') {
                    const cancelBtnClass = isCancelledFlag ? 'btn-danger' : 'btn-outline-danger';
                    const cancelBtnTitle = isCancelledFlag ? 'Update cancellation confirmation' : 'Confirm cancellation';
                    cardHtml += '<button type="button" class="btn btn-sm ' + cancelBtnClass + '" title="' + cancelBtnTitle + '" onclick="show_ajax_modal(\'' + cancelFlagRoute + '\', \'Cancellation Confirmation\')"><i class="ti ti-ban f-12"></i></button>';
                }
                cardHtml += '</div></div>';
                
                // Student details
                cardHtml += '<div class="row g-1 mb-2">';
                cardHtml += '<div class="col-6"><div class="d-flex align-items-center"><i class="ti ti-phone f-12 text-muted me-1"></i><small class="text-muted f-11">' + escapeHtml(data.phone || '-') + '</small></div></div>';
                cardHtml += '<div class="col-6"><div class="d-flex align-items-center"><i class="ti ti-brand-whatsapp f-12 text-muted me-1"></i><small class="text-muted f-11">' + escapeHtml(data.whatsapp || 'N/A') + '</small></div></div>';
                if (data.show_parent_phone && data.parent_phone) {
                    cardHtml += '<div class="col-6"><div class="d-flex align-items-center"><i class="ti ti-phone-call f-12 text-muted me-1"></i><small class="text-muted f-11">Parent: ' + escapeHtml(data.parent_phone || 'N/A') + '</small></div></div>';
                }
                cardHtml += '<div class="col-6"><div class="d-flex align-items-center"><i class="ti ti-user f-12 text-muted me-1"></i><small class="text-muted f-11">BDE: ' + escapeHtml(data.bde_name || 'Unassigned') + '</small></div></div>';
                cardHtml += '<div class="col-6"><div class="d-flex align-items-center"><i class="ti ti-user-plus f-12 text-muted me-1"></i><small class="text-muted f-11">Post-Sales: ' + escapeHtml(data.post_sales_user || 'Unassigned') + '</small></div></div>';
                cardHtml += '<div class="col-6"><div class="d-flex align-items-center"><i class="ti ti-book f-12 text-muted me-1"></i><small class="text-muted f-11">' + escapeHtml(data.course || '-') + '</small></div></div>';
                if (data.batch && data.batch !== 'N/A') {
                    cardHtml += '<div class="col-6"><div class="d-flex align-items-center"><i class="ti ti-calendar f-12 text-muted me-1"></i><small class="text-muted f-11">Batch: ' + escapeHtml(data.batch) + '</small></div></div>';
                }
                if (data.admission_batch && data.admission_batch !== 'N/A') {
                    cardHtml += '<div class="col-6"><div class="d-flex align-items-center"><i class="ti ti-calendar-check f-12 text-muted me-1"></i><small class="text-muted f-11">Admission: ' + escapeHtml(data.admission_batch) + '</small></div></div>';
                }
                if (data.subject && data.subject !== 'N/A') {
                    cardHtml += '<div class="col-6"><div class="d-flex align-items-center"><i class="ti ti-bookmark f-12 text-muted me-1"></i><small class="text-muted f-11">Subject: ' + escapeHtml(data.subject) + '</small></div></div>';
                }
                // Pending Payment
                const pendingPaymentBadge = (data.pending_payment === true) 
                    ? '<span class="badge bg-warning">Pending</span>' 
                    : '<span class="text-muted">No</span>';
                cardHtml += '<div class="col-6"><div class="d-flex align-items-center"><i class="ti ti-currency-rupee f-12 text-muted me-1"></i><small class="text-muted f-11">Pending Payment: ' + pendingPaymentBadge + '</small></div></div>';
                // Paid Amount
                const paidAmount = data.paid_amount || 0;
                const paidAmountFormatted = '₹' + parseFloat(paidAmount).toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                cardHtml += '<div class="col-6"><div class="d-flex align-items-center"><i class="ti ti-currency-rupee f-12 text-success me-1"></i><small class="fw-bold text-success f-11">Paid: ' + paidAmountFormatted + '</small></div></div>';
                // Pending Amount
                const pendingAmount = data.pending_amount || 0;
                const pendingAmountFormatted = '₹' + parseFloat(pendingAmount).toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                const pendingAmountClass = pendingAmount > 0 ? 'text-dark' : 'text-muted';
                cardHtml += '<div class="col-6"><div class="d-flex align-items-center"><i class="ti ti-currency-rupee f-12 text-dark me-1"></i><small class="fw-semibold ' + pendingAmountClass + ' f-11">Pending: ' + pendingAmountFormatted + '</small></div></div>';
                if (statusValue === 'cancel') {
                    const cancelStateLabel = isCancelledFlag ? 'Confirmed' : 'Cancelled';
                    const cancelStateClass = isCancelledFlag ? 'bg-danger' : 'bg-secondary';
                    cardHtml += '<div class="col-12">';
                    cardHtml += '<span class="badge bg-danger me-1">Cancel</span>';
                    cardHtml += '<span class="badge ' + cancelStateClass + '">Flag: ' + cancelStateLabel + '</span>';
                    if (data.cancelled_by) {
                        cardHtml += '<br><small class="text-muted f-11 mt-1 d-block"><i class="ti ti-user f-12 me-1"></i>By: ' + escapeHtml(data.cancelled_by);
                        if (data.cancelled_at) {
                            cardHtml += '<br>' + escapeHtml(data.cancelled_at);
                        }
                        if (data.cancel_remark) {
                            cardHtml += '<br><strong>Remark:</strong> ' + escapeHtml(data.cancel_remark);
                        }
                        cardHtml += '</small>';
                    }
                    cardHtml += '</div>';
                }
                if (data.called_date) {
                    cardHtml += '<div class="col-6"><div class="d-flex align-items-center"><i class="ti ti-calendar-time f-12 text-muted me-1"></i><small class="text-muted f-11">Called: ' + escapeHtml(data.called_date) + '</small></div></div>';
                }
                if (data.called_time) {
                    cardHtml += '<div class="col-6"><div class="d-flex align-items-center"><i class="ti ti-clock f-12 text-muted me-1"></i><small class="text-muted f-11">Call Time: ' + escapeHtml(data.called_time) + '</small></div></div>';
                }
                cardHtml += '</div>';
                
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
    });

    // Bulk Assign modal: use event delegation so handlers work when modal content is loaded via AJAX (scripts in injected HTML do not run)
    (function() {
        var $config = $('#bulkAssignConfig');
        if (!$config.length) return;
        var dataUrl = $config.data('data-url');
        var submitUrl = $config.data('submit-url');
        var csrfToken = $('meta[name="csrf-token"]').attr('content');

        function escapeHtmlBulk(t) {
            if (!t) return '';
            var m = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' };
            return String(t).replace(/[&<>"']/g, function(c) { return m[c] || c; });
        }
        function getBulkFilters() {
            return {
                date_from: $('#bulk_date_from').val(),
                date_to: $('#bulk_date_to').val(),
                course_id: $('#bulk_course_id').val(),
                batch_id: $('#bulk_batch_id').val() || ''
            };
        }
        var bulkAssignLoadInProgress = false;
        function loadBulkAssignList() {
            var f = getBulkFilters();
            if (!f.date_from || !f.date_to || !f.course_id) {
                $('#bulkAssignTableBody').empty();
                $('#bulkAssignEmpty').show();
                $('#bulkAssignTableWrap').hide();
                return;
            }
            if (bulkAssignLoadInProgress) return;
            bulkAssignLoadInProgress = true;
            $('#bulkAssignEmpty').html('<div class="py-4"><span class="spinner-border spinner-border-sm me-2"></span>Loading students...</div>');
            $('#bulkAssignTableWrap').hide();
            var ajaxData = { date_from: f.date_from, date_to: f.date_to, course_id: f.course_id };
            if (f.batch_id) ajaxData.batch_id = f.batch_id;
            $.ajax({
                url: dataUrl,
                type: 'GET',
                data: ajaxData,
                success: function(res) {
                    bulkAssignLoadInProgress = false;
                    $('#bulkAssignEmpty').html('<i class="ti ti-filter-off d-block mb-2" style="font-size: 2rem;"></i><p class="mb-0">Select From Date, To Date and Course to load students.</p>');
                    if (!res.success || !res.data) {
                        $('#bulkAssignTableBody').empty();
                        $('#bulkAssignEmpty').show();
                        $('#bulkAssignTableWrap').hide();
                        return;
                    }
                    var html = '';
                    res.data.forEach(function(row) {
                        html += '<tr>';
                        html += '<td><input type="checkbox" class="form-check-input bulk-row-cb" value="' + row.id + '" name="ids[]"></td>';
                        html += '<td>' + row.index + '</td>';
                        html += '<td>' + escapeHtmlBulk(row.name) + '</td>';
                        html += '<td>' + escapeHtmlBulk(row.register_number) + '</td>';
                        html += '<td>' + escapeHtmlBulk(row.phone) + '</td>';
                        html += '<td>' + escapeHtmlBulk(row.course) + '</td>';
                        html += '<td>' + escapeHtmlBulk(row.batch) + '</td>';
                        html += '<td>' + escapeHtmlBulk(row.post_sales_user) + '</td>';
                        html += '<td>' + escapeHtmlBulk(row.created_at) + '</td>';
                        html += '</tr>';
                    });
                    $('#bulkAssignTableBody').html(html);
                    $('#bulkAssignCount').text('Total: ' + res.data.length + ' student(s). Select rows and choose Assign to Post-Sales, then click Bulk Assign.');
                    $('#bulkAssignEmpty').hide();
                    $('#bulkAssignTableWrap').show();
                    $('#bulk_check_all').prop('checked', false).prop('indeterminate', false);
                    updateBulkCheckAll();
                },
                error: function(xhr) {
                    bulkAssignLoadInProgress = false;
                    var msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Failed to load data.';
                    if (xhr.responseJSON && xhr.responseJSON.errors) {
                        msg = Object.values(xhr.responseJSON.errors).flat().join(' ');
                    }
                    if (typeof showToast === 'function') showToast(msg, 'error');
                    else alert(msg);
                    $('#bulkAssignTableBody').empty();
                    $('#bulkAssignEmpty').html('<i class="ti ti-filter-off d-block mb-2" style="font-size: 2rem;"></i><p class="mb-0">Select From Date, To Date and Course to load students.</p>').show();
                    $('#bulkAssignTableWrap').hide();
                }
            });
        }
        function toggleBulkSubmitButton() {
            var anyChecked = $('#bulkAssignTableBody input.bulk-row-cb:checked').length > 0;
            var userSelected = $('#bulk_assign_to').val() !== '';
            $('#bulkAssignSubmitBtn').prop('disabled', !(anyChecked && userSelected));
        }
        function updateBulkCheckAll() {
            var total = $('#bulkAssignTableBody input.bulk-row-cb').length;
            var checked = $('#bulkAssignTableBody input.bulk-row-cb:checked').length;
            $('#bulk_check_all').prop('checked', total > 0 && total === checked);
            $('#bulk_check_all').prop('indeterminate', checked > 0 && checked < total);
            toggleBulkSubmitButton();
        }

        $(document).on('change', '#bulk_batch_id', function() {
            loadBulkAssignList();
        });
        $(document).on('change', '#bulk_date_from, #bulk_date_to, #bulk_course_id', function() {
            var courseId = $('#bulk_course_id').val();
            var $batchSelect = $('#bulk_batch_id');
            $batchSelect.find('option:not(:first)').remove();
            $batchSelect.val('');
            if (courseId) {
                $.get('/api/batches/by-course/' + courseId).done(function(res) {
                    if (res.success && res.batches && res.batches.length) {
                        res.batches.forEach(function(b) {
                            $batchSelect.append($('<option></option>').val(b.id).text(b.title));
                        });
                    }
                    loadBulkAssignList();
                }).fail(function() {
                    loadBulkAssignList();
                });
            } else {
                loadBulkAssignList();
            }
        });
        $(document).on('change', '#bulk_check_all', function() {
            $('#bulkAssignTableBody input.bulk-row-cb').prop('checked', $(this).is(':checked'));
            updateBulkCheckAll();
        });
        $(document).on('change', '#bulkAssignTableBody input.bulk-row-cb', updateBulkCheckAll);
        $(document).on('change', '#bulk_assign_to', toggleBulkSubmitButton);
        $(document).on('input', '#bulk_select_count', function() {
            var count = parseInt($(this).val(), 10) || 0;
            var $checkboxes = $('#bulkAssignTableBody input.bulk-row-cb');
            $checkboxes.prop('checked', false);
            if (count > 0) {
                $checkboxes.slice(0, count).prop('checked', true);
            }
            updateBulkCheckAll();
        });
        $(document).on('click', '#bulkAssignSubmitBtn', function() {
            var ids = [];
            $('#bulkAssignTableBody input.bulk-row-cb:checked').each(function() { ids.push($(this).val()); });
            var userId = $('#bulk_assign_to').val();
            if (!ids.length || !userId) {
                if (typeof showToast === 'function') showToast('Select at least one student and a Post-Sales user.', 'error');
                else alert('Select at least one student and a Post-Sales user.');
                return;
            }
            var btn = $(this);
            btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span>Saving...');
            $.ajax({
                url: submitUrl,
                type: 'POST',
                data: {
                    _token: csrfToken,
                    post_sales_user_id: userId,
                    ids: ids
                },
                success: function(res) {
                    if (res.success) {
                        if (typeof showToast === 'function') showToast(res.message, 'success');
                        else alert(res.message);
                        $('#large_modal').modal('hide');
                        if ($.fn.DataTable.isDataTable('#postSalesConvertedTable')) {
                            $('#postSalesConvertedTable').DataTable().ajax.reload(null, false);
                        }
                        var container = document.getElementById('mobileConvertedStudentsContainer');
                        if (container && typeof loadAllMobileViewData === 'function') {
                            loadAllMobileViewData(1, false);
                        } else if (container && typeof loadMobileView === 'function' && typeof lastJsonResponse !== 'undefined' && lastJsonResponse) {
                            loadMobileView(lastJsonResponse);
                        }
                    } else {
                        if (typeof showToast === 'function') showToast(res.message || 'Error', 'error');
                        else alert(res.message || 'Error');
                    }
                    btn.prop('disabled', false).html('<i class="ti ti-user-plus me-1"></i> Bulk Assign');
                    toggleBulkSubmitButton();
                },
                error: function(xhr) {
                    var msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Request failed.';
                    if (xhr.responseJSON && xhr.responseJSON.errors) {
                        msg = Object.values(xhr.responseJSON.errors).flat().join(' ');
                    }
                    if (typeof showToast === 'function') showToast(msg, 'error');
                    else alert(msg);
                    btn.prop('disabled', false).html('<i class="ti ti-user-plus me-1"></i> Bulk Assign');
                    toggleBulkSubmitButton();
                }
            });
        });
    })();

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
</script>
@endpush
