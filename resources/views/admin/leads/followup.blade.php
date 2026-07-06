@extends('layouts.mantis')

@section('title', 'Follow-up Leads')

@section('content')
<!-- [ breadcrumb ] start -->
<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-6">
                <div class="page-header-title">
                    <h5 class="m-b-10">Follow-up Leads Management</h5>
                </div>
            </div>
            <div class="col-md-6">
                <ul class="breadcrumb d-flex justify-content-end">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('leads.index') }}">Leads</a></li>
                    <li class="breadcrumb-item">Follow-up Leads</li>
                </ul>
            </div>
        </div>
    </div>
</div>
<!-- [ breadcrumb ] end -->

@if(request('search_key'))
<div class="alert alert-info alert-dismissible fade show" role="alert">
    <div class="d-flex align-items-center">
        <i class="ti ti-search me-2"></i>
        <div class="flex-grow-1">
            <strong>Search Results:</strong> Showing follow-up leads matching "{{ request('search_key') }}"
        </div>
        <a href="{{ route('leads.followup') }}" class="btn btn-sm btn-outline-info">
            <i class="ti ti-x"></i> Clear Search
        </a>
    </div>
</div>
@endif

<!-- [ Filter ] start -->
<div class="row mb-3">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form method="GET" action="{{ route('leads.followup') }}" id="followupFilterForm">
                    <div class="d-none d-lg-block">
                        <div class="row align-items-end">
                            <div class="col-md-2">
                                <label for="search_key" class="form-label">Search</label>
                                <input type="text" class="form-control" name="search_key" id="search_key"
                                    value="{{ request('search_key') }}" placeholder="Search leads...">
                            </div>
                            <div class="col-md-2">
                                <label for="lead_source_id" class="form-label">Lead Source</label>
                                <select class="form-select" name="lead_source_id" id="lead_source_id">
                                    <option value="">All Sources</option>
                                    @foreach($leadSources as $source)
                                    <option value="{{ $source->id }}" {{ request('lead_source_id') == $source->id ? 'selected' : '' }}>
                                        {{ $source->title }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="course_id" class="form-label">Course</label>
                                <select class="form-select" name="course_id" id="course_id">
                                    <option value="">All Courses</option>
                                    @foreach($courses as $course)
                                    <option value="{{ $course->id }}" {{ request('course_id') == $course->id ? 'selected' : '' }}>
                                        {{ $course->title }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="country_id" class="form-label">Country</label>
                                <select class="form-select" name="country_id" id="country_id">
                                    <option value="">All Countries</option>
                                    @foreach($countries as $country)
                                    <option value="{{ $country->id }}" {{ request('country_id') == $country->id ? 'selected' : '' }}>
                                        {{ $country->title }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            @if(!$isTelecaller || $isTeamLead)
                            <div class="col-md-2">
                                <label for="telecaller_id" class="form-label">Telecaller</label>
                                <select class="form-select" name="telecaller_id" id="telecaller_id">
                                    <option value="">All Telecallers</option>
                                    @foreach($telecallers as $telecaller)
                                    <option value="{{ $telecaller->id }}" {{ request('telecaller_id') == $telecaller->id ? 'selected' : '' }}>
                                        {{ $telecaller->name }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            @endif
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="ti ti-search"></i> Filter
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="d-lg-none">
                        <div class="row g-2">
                            <div class="col-12">
                                <label for="search_key_mobile" class="form-label f-12">Search</label>
                                <input type="text" class="form-control" name="search_key" id="search_key_mobile"
                                    value="{{ request('search_key') }}" placeholder="Search leads...">
                            </div>
                            <div class="col-6">
                                <label for="lead_source_id_mobile" class="form-label f-12">Lead Source</label>
                                <select class="form-select" name="lead_source_id" id="lead_source_id_mobile">
                                    <option value="">All Sources</option>
                                    @foreach($leadSources as $source)
                                    <option value="{{ $source->id }}" {{ request('lead_source_id') == $source->id ? 'selected' : '' }}>
                                        {{ $source->title }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-6">
                                <label for="course_id_mobile" class="form-label f-12">Course</label>
                                <select class="form-select" name="course_id" id="course_id_mobile">
                                    <option value="">All Courses</option>
                                    @foreach($courses as $course)
                                    <option value="{{ $course->id }}" {{ request('course_id') == $course->id ? 'selected' : '' }}>
                                        {{ $course->title }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-6">
                                <label for="country_id_mobile" class="form-label f-12">Country</label>
                                <select class="form-select" name="country_id" id="country_id_mobile">
                                    <option value="">All Countries</option>
                                    @foreach($countries as $country)
                                    <option value="{{ $country->id }}" {{ request('country_id') == $country->id ? 'selected' : '' }}>
                                        {{ $country->title }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            @if(!$isTelecaller || $isTeamLead)
                            <div class="col-6">
                                <label for="telecaller_id_mobile" class="form-label f-12">Telecaller</label>
                                <select class="form-select" name="telecaller_id" id="telecaller_id_mobile">
                                    <option value="">All Telecallers</option>
                                    @foreach($telecallers as $telecaller)
                                    <option value="{{ $telecaller->id }}" {{ request('telecaller_id') == $telecaller->id ? 'selected' : '' }}>
                                        {{ $telecaller->name }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            @endif
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="ti ti-search"></i> Filter
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- [ Filter ] end -->

<!-- [ Main Content ] start -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Follow-up Leads <small class="text-muted" id="followupLeadCount"></small></h5>
                    <div class="d-flex gap-2">
                        <a href="{{ route('leads.index') }}" class="btn btn-secondary btn-sm px-3">
                            <i class="ti ti-arrow-left"></i> All Leads
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="d-none d-lg-block">
                    <div class="table-responsive" style="overflow-x: auto;">
                        <table class="table table-hover" id="followupLeadsAjaxTable" style="min-width: 1700px;">
                            <thead>
                                @php
                                $hasRegistrationDetails = $isAdminOrSuperAdmin || $isTelecallerRole || $isAcademicAssistant || $isAdmissionCounsellor || $isTeamLeadRole || $isGeneralManager;
                                $followupDateColumnIndex = 2 + ($hasRegistrationDetails ? 1 : 0) + 3;
                                @endphp
                                <tr>
                                    <th>#</th>
                                    <th>Actions</th>
                                    @if($hasRegistrationDetails)
                                    <th>Registration Details</th>
                                    @endif
                                    <th>Name</th>
                                    <th>Profile</th>
                                    <th>Phone</th>
                                    <th>Followup Date</th>
                                    <th>Email</th>
                                    <th>Status</th>
                                    <th>Interest</th>
                                    <th>Rating</th>
                                    <th>Source</th>
                                    <th>Course</th>
                                    <th>Telecaller</th>
                                    <th>Place</th>
                                    <th>Last Reason</th>
                                    <th>Remarks</th>
                                    <th>Date</th>
                                    <th>Time</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>

                <br class="d-lg-none">
                <hr class="d-lg-none">
                <br class="d-lg-none">

                <div class="d-lg-none" id="mobileFollowupContainer"></div>
            </div>
        </div>
    </div>
</div>
<!-- [ Main Content ] end -->

@php
$columns = [
    ['data' => 'index', 'name' => 'index', 'orderable' => false, 'searchable' => false],
    ['data' => 'actions', 'name' => 'actions', 'orderable' => false, 'searchable' => false],
];

if ($hasRegistrationDetails) {
    $columns[] = ['data' => 'registration_details', 'name' => 'registration_details', 'orderable' => false, 'searchable' => false];
}

$columns = array_merge($columns, [
    ['data' => 'name', 'name' => 'name'],
    ['data' => 'profile', 'name' => 'profile', 'orderable' => false, 'searchable' => false],
    ['data' => 'phone', 'name' => 'phone'],
    ['data' => 'followup_date', 'name' => 'followup_date'],
    ['data' => 'email', 'name' => 'email'],
    ['data' => 'status', 'name' => 'status', 'orderable' => false, 'searchable' => false],
    ['data' => 'interest', 'name' => 'interest', 'orderable' => false, 'searchable' => false],
    ['data' => 'rating', 'name' => 'rating', 'orderable' => false, 'searchable' => false],
    ['data' => 'source', 'name' => 'source'],
    ['data' => 'course', 'name' => 'course'],
    ['data' => 'telecaller', 'name' => 'telecaller'],
    ['data' => 'place', 'name' => 'place'],
    ['data' => 'last_reason', 'name' => 'last_reason', 'orderable' => false, 'searchable' => false],
    ['data' => 'remarks', 'name' => 'remarks'],
    ['data' => 'date', 'name' => 'date'],
    ['data' => 'time', 'name' => 'time'],
]);
@endphp

@endsection

@push('scripts')
<style>
    #followupLeadsAjaxTable thead th {
        border-top: none;
        font-weight: 600;
        background-color: #f8f9fa;
        white-space: nowrap;
    }

    #followupLeadsAjaxTable tbody td {
        vertical-align: middle;
        white-space: nowrap;
    }

    .table-responsive {
        border: none;
    }
</style>
<script>
    $(document).ready(function() {
        $('#followupLeadsAjaxTable').removeClass('data_table_basic');

        setTimeout(function() {
            if ($.fn.DataTable.isDataTable('#followupLeadsAjaxTable')) {
                $('#followupLeadsAjaxTable').DataTable().destroy();
            }

            const followupDateColumnIndex = @json($followupDateColumnIndex);
            let lastJsonResponse = null;

            function getUrlParameter(name) {
                name = name.replace(/[\[]/, '\\[').replace(/[\]]/, '\\]');
                const regex = new RegExp('[\\?&]' + name + '=([^&#]*)');
                const results = regex.exec(location.search);
                return results === null ? '' : decodeURIComponent(results[1].replace(/\+/g, ' '));
            }

            function getFilterParams() {
                const isMobile = window.innerWidth < 992;
                return {
                    search_key: isMobile
                        ? ($('#search_key_mobile').val() || getUrlParameter('search_key') || '')
                        : ($('#search_key').val() || getUrlParameter('search_key') || '{{ request('search_key') }}' || ''),
                    lead_source_id: isMobile
                        ? ($('#lead_source_id_mobile').val() || '')
                        : ($('#lead_source_id').val() || ''),
                    course_id: isMobile
                        ? ($('#course_id_mobile').val() || '')
                        : ($('#course_id').val() || ''),
                    country_id: isMobile
                        ? ($('#country_id_mobile').val() || '')
                        : ($('#country_id').val() || ''),
                    telecaller_id: isMobile
                        ? ($('#telecaller_id_mobile').val() || $('#telecaller_id').val() || '')
                        : ($('#telecaller_id').val() || '')
                };
            }

            function updateUrlWithFilters() {
                const filters = getFilterParams();
                const params = new URLSearchParams();

                Object.keys(filters).forEach(function(key) {
                    if (filters[key]) {
                        params.append(key, filters[key]);
                    }
                });

                let newUrl = window.location.pathname;
                if (params.toString()) {
                    newUrl += '?' + params.toString();
                }

                window.history.pushState({ path: newUrl }, '', newUrl);
            }

            function loadFiltersFromUrl() {
                const urlParams = new URLSearchParams(window.location.search);

                if (urlParams.get('search_key')) {
                    $('#search_key, #search_key_mobile').val(urlParams.get('search_key'));
                }
                if (urlParams.get('lead_source_id')) {
                    $('#lead_source_id, #lead_source_id_mobile').val(urlParams.get('lead_source_id'));
                }
                if (urlParams.get('course_id')) {
                    $('#course_id, #course_id_mobile').val(urlParams.get('course_id'));
                }
                if (urlParams.get('country_id')) {
                    $('#country_id, #country_id_mobile').val(urlParams.get('country_id'));
                }
                if (urlParams.get('telecaller_id')) {
                    $('#telecaller_id, #telecaller_id_mobile').val(urlParams.get('telecaller_id'));
                }
            }

            loadFiltersFromUrl();

            const mobileViewState = {
                currentPage: 1,
                pageSize: 25,
                totalRecords: 0,
                allData: [],
                isLoading: false,
                hasMore: true
            };

            function updateLeadCount(info) {
                if (info) {
                    $('#followupLeadCount').text('(' + info.recordsDisplay + ' leads)');
                }
            }

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
                    showToast('Failed to copy link. Please try again.', 'error');
                    $(this).removeClass('processing');
                }

                document.body.removeChild(tempInput);
            }

            const followupTable = window.innerWidth >= 992 ? $('#followupLeadsAjaxTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('leads.followup.data') }}',
                    type: 'GET',
                    data: function(d) {
                        $.extend(d, getFilterParams());
                    },
                    dataSrc: function(json) {
                        lastJsonResponse = json;
                        return json.data;
                    },
                    error: function() {
                        showToast('Error loading follow-up leads. Please try again.', 'error');
                    }
                },
                pageLength: 25,
                lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, 'All']],
                order: [[followupDateColumnIndex, 'asc']],
                dom: 'Bfrtip',
                buttons: ['csv', 'excel', 'print', 'pdf'],
                stateSave: true,
                scrollCollapse: true,
                autoWidth: false,
                scrollX: true,
                columns: @json($columns),
                columnDefs: [
                    {
                        targets: followupDateColumnIndex,
                        type: 'html',
                        render: function(data, type) {
                            if (type === 'sort' || type === 'type') {
                                return data === '-' ? '' : data;
                            }
                            if (!data || data === '-') {
                                return '-';
                            }
                            return '<span class="badge bg-warning">' + data + '</span>';
                        }
                    }
                ],
                drawCallback: function(settings) {
                    const api = this.api();
                    const info = api.page.info();
                    updateLeadCount(info);

                    $(api.rows({ page: 'current' }).nodes()).find('[data-bs-toggle="tooltip"]').tooltip();
                    $(api.rows({ page: 'current' }).nodes()).find('.copy-link-btn').off('click').on('click', handleCopyLink);

                    if (lastJsonResponse && settings.iDraw === 1) {
                        loadMobileView(lastJsonResponse);
                    }
                },
                language: {
                    processing: 'Loading...',
                    emptyTable: 'No follow-up leads found',
                    zeroRecords: 'No matching follow-up leads found',
                    lengthMenu: 'Show _MENU_ entries',
                    info: 'Showing _START_ to _END_ of _TOTAL_ entries',
                    infoEmpty: 'Showing 0 to 0 of 0 entries',
                    infoFiltered: '(filtered from _MAX_ total entries)',
                    search: 'Search:',
                    paginate: {
                        first: 'First',
                        last: 'Last',
                        next: 'Next',
                        previous: 'Previous'
                    }
                }
            }) : null;

            if (followupTable) {
                $('#followupFilterForm').on('submit', function(e) {
                    e.preventDefault();
                    updateUrlWithFilters();
                    mobileViewState.allData = [];
                    mobileViewState.currentPage = 1;
                    mobileViewState.hasMore = true;
                    followupTable.ajax.reload();
                });

                $('#lead_source_id, #course_id, #country_id, #telecaller_id, #lead_source_id_mobile, #course_id_mobile, #country_id_mobile, #telecaller_id_mobile').on('change', function() {
                    updateUrlWithFilters();
                    mobileViewState.allData = [];
                    mobileViewState.currentPage = 1;
                    mobileViewState.hasMore = true;
                    followupTable.ajax.reload();
                });
            } else {
                $('#followupFilterForm').on('submit', function(e) {
                    e.preventDefault();
                    updateUrlWithFilters();
                    mobileViewState.allData = [];
                    mobileViewState.currentPage = 1;
                    mobileViewState.hasMore = true;
                    loadAllMobileViewData(1, false);
                });

                $('#lead_source_id, #course_id, #country_id, #telecaller_id, #lead_source_id_mobile, #course_id_mobile, #country_id_mobile, #telecaller_id_mobile').on('change', function() {
                    updateUrlWithFilters();
                    mobileViewState.allData = [];
                    mobileViewState.currentPage = 1;
                    mobileViewState.hasMore = true;
                    loadAllMobileViewData(1, false);
                });

                loadAllMobileViewData(1, false);
            }

            function loadAllMobileViewData(page = 1, append = false) {
                if (mobileViewState.isLoading) {
                    return;
                }

                mobileViewState.isLoading = true;
                mobileViewState.currentPage = page;
                const container = $('#mobileFollowupContainer');

                if (!append) {
                    container.html('<div class="text-center py-4"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div><p class="mt-2 text-muted">Loading follow-up leads...</p></div>');
                    mobileViewState.allData = [];
                }

                const requestData = {
                    draw: page,
                    start: (page - 1) * mobileViewState.pageSize,
                    length: mobileViewState.pageSize,
                    order: [{ column: followupDateColumnIndex, dir: 'asc' }],
                    search: { value: '', regex: false }
                };

                $.extend(requestData, getFilterParams());

                $.ajax({
                    url: '{{ route('leads.followup.data') }}',
                    type: 'GET',
                    data: requestData,
                    success: function(response) {
                        mobileViewState.isLoading = false;

                        if (!response || !response.data) {
                            if (!append) {
                                container.html('<div class="text-center py-4 text-muted"><i class="ti ti-inbox f-48 mb-3 d-block"></i><h5>No follow-up leads found</h5></div>');
                            }
                            return;
                        }

                        mobileViewState.totalRecords = response.recordsFiltered || response.recordsTotal || 0;
                        $('#followupLeadCount').text('(' + mobileViewState.totalRecords + ' leads)');

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
                                    console.error('Error parsing mobile view data:', e);
                                }
                            }
                        });

                        mobileViewState.hasMore = mobileViewState.allData.length < mobileViewState.totalRecords;
                        renderMobileViewCards();

                        if (mobileViewState.hasMore) {
                            showLoadMoreButton();
                        } else {
                            $('.load-more-mobile-btn').parent().remove();
                        }
                    },
                    error: function() {
                        mobileViewState.isLoading = false;
                        if (!append) {
                            container.html('<div class="text-center py-4"><div class="alert alert-danger">Error loading follow-up leads.</div></div>');
                        }
                    }
                });
            }

            function renderMobileViewCards() {
                const container = $('#mobileFollowupContainer');
                container.empty();

                if (mobileViewState.allData.length === 0) {
                    container.html('<div class="text-center py-4 text-muted"><i class="ti ti-inbox f-48 mb-3 d-block"></i><h5>No follow-up leads found</h5></div>');
                    return;
                }

                mobileViewState.allData.forEach(function(item) {
                    container.append(renderMobileCard(item.data, item.index));
                });

                container.find('[data-bs-toggle="tooltip"]').tooltip();
                container.find('.copy-link-btn').off('click').on('click', handleCopyLink);
            }

            function showLoadMoreButton() {
                const container = $('#mobileFollowupContainer');
                const remaining = mobileViewState.totalRecords - mobileViewState.allData.length;
                if (remaining <= 0) {
                    return;
                }

                const loadMoreHtml = '<div class="text-center py-3"><button class="btn btn-outline-primary load-more-mobile-btn" type="button">Load More (' + remaining + ' remaining)</button></div>';
                container.append(loadMoreHtml);
                container.find('.load-more-mobile-btn').off('click').on('click', function() {
                    const nextPage = Math.floor(mobileViewState.allData.length / mobileViewState.pageSize) + 1;
                    loadAllMobileViewData(nextPage, true);
                });
            }

            function loadMobileView(jsonData) {
                if (!jsonData || !jsonData.data || window.innerWidth >= 992) {
                    return;
                }

                mobileViewState.totalRecords = jsonData.recordsFiltered || jsonData.recordsTotal || 0;
                mobileViewState.allData = [];
                mobileViewState.currentPage = 1;
                mobileViewState.hasMore = true;

                if (mobileViewState.totalRecords > 0) {
                    loadAllMobileViewData(1, false);
                } else {
                    $('#mobileFollowupContainer').html('<div class="text-center py-4 text-muted"><i class="ti ti-inbox f-48 mb-3 d-block"></i><h5>No follow-up leads found</h5></div>');
                }
            }

            function escapeHtml(text) {
                if (!text) return '';
                const map = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' };
                return String(text).replace(/[&<>"']/g, function(m) { return map[m]; });
            }

            function renderMobileCard(data, index) {
                if (!data || !data.id) {
                    return '';
                }

                let cardHtml = '<div class="card mb-2" data-lead-id="' + data.id + '"><div class="card-body p-3">';
                cardHtml += '<div class="d-flex justify-content-between align-items-start mb-2">';
                cardHtml += '<div><small class="text-muted d-block">' + escapeHtml(data.created_at || '') + '</small>';
                cardHtml += '<h6 class="mb-0 fw-bold">' + escapeHtml(data.title || 'N/A') + '</h6>';
                cardHtml += '<small class="text-muted">#' + (index || '') + '</small></div>';
                cardHtml += '<div class="d-flex gap-1">';
                if (data.routes && data.routes.view) {
                    cardHtml += '<a href="javascript:void(0);" class="btn btn-sm btn-outline-primary" onclick="show_large_modal(\'' + data.routes.view + '\', \'View Lead\')"><i class="ti ti-eye f-12"></i></a>';
                }
                if (data.permissions && data.permissions.can_edit && data.routes && data.routes.edit) {
                    cardHtml += '<a href="javascript:void(0);" class="btn btn-sm btn-outline-secondary" onclick="show_ajax_modal(\'' + data.routes.edit + '\', \'Edit Lead\')"><i class="ti ti-edit f-12"></i></a>';
                }
                cardHtml += '</div></div>';

                cardHtml += '<div class="row g-2 f-12">';
                cardHtml += '<div class="col-6"><strong>Phone:</strong> ' + escapeHtml(data.phone || '-') + '</div>';
                if (data.followup_date) {
                    cardHtml += '<div class="col-6"><strong>Follow-up:</strong> <span class="badge bg-warning">' + escapeHtml(data.followup_date) + '</span></div>';
                }
                cardHtml += '<div class="col-6"><strong>Email:</strong> ' + escapeHtml(data.email || '-') + '</div>';
                if (data.status) {
                    cardHtml += '<div class="col-6"><strong>Status:</strong> <span class="badge ' + (data.status.color_class || 'bg-secondary') + '">' + escapeHtml(data.status.title || '-') + '</span></div>';
                }
                if (data.course) {
                    cardHtml += '<div class="col-6"><strong>Course:</strong> ' + escapeHtml(data.course) + '</div>';
                }
                cardHtml += '</div></div></div>';

                return cardHtml;
            }
        }, 50);
    });
</script>
@endpush
