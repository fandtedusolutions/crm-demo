@extends('layouts.mantis')

@section('title', 'Converted Leads Report')

@section('content')
<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-6">
                <div class="page-header-title">
                    <h5 class="m-b-10">Converted Leads Report</h5>
                    <p class="m-b-0">Converted leads with first and current source, course, and status</p>
                </div>
            </div>
            <div class="col-md-6">
                <ul class="breadcrumb d-flex justify-content-end">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item">Converted Leads Report</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<div class="row mb-3">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
                <div>
                    <h5 class="mb-0">
                        <i class="ti ti-filter me-1"></i>Report Filters
                    </h5>
                    <small class="text-muted">Set date range, then filter by current or first lead values</small>
                </div>
                <div class="d-flex align-items-center gap-3">
                    <div class="text-end">
                        <div class="text-muted small">Total Converted</div>
                        <div class="fw-bold text-success fs-4 lh-1" id="convertedLeadsTotalCount">0</div>
                    </div>
                    <div class="vr d-none d-sm-block"></div>
                    <div class="text-muted small" id="convertedLeadsDateRange">
                        {{ \Carbon\Carbon::parse($fromDate)->format('d M Y') }}
                        –
                        {{ \Carbon\Carbon::parse($toDate)->format('d M Y') }}
                    </div>
                </div>
            </div>
            <div class="card-body">
                <form id="convertedLeadsReportFilter" onsubmit="return false;">
                    <div class="mb-4">
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <span class="avtar avtar-xs bg-light-primary">
                                <i class="ti ti-calendar text-primary"></i>
                            </span>
                            <h6 class="mb-0">Conversion Period</h6>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-4 col-lg-3">
                                <label for="date_from" class="form-label">Converted From</label>
                                <input type="date" class="form-control" id="date_from" name="date_from" value="{{ $fromDate }}">
                            </div>
                            <div class="col-md-4 col-lg-3">
                                <label for="date_to" class="form-label">Converted To</label>
                                <input type="date" class="form-control" id="date_to" name="date_to" value="{{ $toDate }}">
                            </div>
                            <div class="col-md-4 col-lg-3">
                                <label for="is_b2b" class="form-label">Type</label>
                                <select class="form-select" id="is_b2b" name="is_b2b">
                                    <option value="">All Types</option>
                                    <option value="0" {{ (string) $filters['is_b2b'] === '0' ? 'selected' : '' }}>In House</option>
                                    <option value="1" {{ (string) $filters['is_b2b'] === '1' ? 'selected' : '' }}>B2B</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <span class="avtar avtar-xs bg-light-info">
                                <i class="ti ti-user-check text-info"></i>
                            </span>
                            <div>
                                <h6 class="mb-0">Current Lead Filters</h6>
                                <small class="text-muted">Filter by the lead’s current source, course, and status</small>
                            </div>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label for="lead_source_id" class="form-label">Lead Source</label>
                                <select class="form-select" id="lead_source_id" name="lead_source_id">
                                    <option value="">All Sources</option>
                                    @foreach($leadSources as $source)
                                        <option value="{{ $source->id }}" {{ (string) $filters['lead_source_id'] === (string) $source->id ? 'selected' : '' }}>
                                            {{ $source->title }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="course_id" class="form-label">Course</label>
                                <select class="form-select" id="course_id" name="course_id">
                                    <option value="">All Courses</option>
                                    @foreach($courses as $course)
                                        <option value="{{ $course->id }}" {{ (string) $filters['course_id'] === (string) $course->id ? 'selected' : '' }}>
                                            {{ $course->title }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="lead_status_id" class="form-label">Lead Status</label>
                                <select class="form-select" id="lead_status_id" name="lead_status_id">
                                    <option value="">All Statuses</option>
                                    @foreach($leadStatuses as $status)
                                        <option value="{{ $status->id }}" {{ (string) $filters['lead_status_id'] === (string) $status->id ? 'selected' : '' }}>
                                            {{ $status->title }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <span class="avtar avtar-xs bg-light-warning">
                                <i class="ti ti-flag text-warning"></i>
                            </span>
                            <div>
                                <h6 class="mb-0">First Lead Filters</h6>
                                <small class="text-muted">Filter by the values captured when the lead was first created</small>
                            </div>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label for="first_lead_source_id" class="form-label">First Lead Source</label>
                                <select class="form-select" id="first_lead_source_id" name="first_lead_source_id">
                                    <option value="">All Sources</option>
                                    @foreach($leadSources as $source)
                                        <option value="{{ $source->id }}" {{ (string) $filters['first_lead_source_id'] === (string) $source->id ? 'selected' : '' }}>
                                            {{ $source->title }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="first_lead_course_id" class="form-label">First Lead Course</label>
                                <select class="form-select" id="first_lead_course_id" name="first_lead_course_id">
                                    <option value="">All Courses</option>
                                    @foreach($courses as $course)
                                        <option value="{{ $course->id }}" {{ (string) $filters['first_lead_course_id'] === (string) $course->id ? 'selected' : '' }}>
                                            {{ $course->title }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="first_lead_status_id" class="form-label">First Lead Status</label>
                                <select class="form-select" id="first_lead_status_id" name="first_lead_status_id">
                                    <option value="">All Statuses</option>
                                    @foreach($leadStatuses as $status)
                                        <option value="{{ $status->id }}" {{ (string) $filters['first_lead_status_id'] === (string) $status->id ? 'selected' : '' }}>
                                            {{ $status->title }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex flex-wrap gap-2 justify-content-end pt-2 border-top">
                        <button type="button" class="btn btn-primary" id="applyConvertedLeadsFilters">
                            <i class="ti ti-filter me-1"></i>Filter
                        </button>
                        <button type="button" class="btn btn-outline-secondary" id="resetConvertedLeadsFilters">
                            <i class="ti ti-refresh me-1"></i>Reset
                        </button>
                        <a href="{{ route('admin.reports.converted-leads-report.excel') }}" class="btn btn-success" id="exportConvertedLeadsExcel">
                            <i class="ti ti-file-excel me-1"></i>Excel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Converted Leads</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="convertedLeadsReportTable" class="table table-hover nowrap w-100">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Converted Date</th>
                                <th>Name</th>
                                <th>BDE Name</th>
                                <th>Phone</th>
                                <th>Type</th>
                                <th>Course</th>
                                <th>Lead Created By</th>
                                <th>Lead First Created At</th>
                                <th>Lead Created At</th>
                                <th>Lead Status</th>
                                <th>Lead Source</th>
                                <th>First Lead Source</th>
                                <th>First Lead Course</th>
                                <th>First Lead Status</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    const exportBaseUrl = @json(route('admin.reports.converted-leads-report.excel'));
    const dataUrl = @json(route('admin.reports.converted-leads-report.data'));
    const defaultFromDate = @json($fromDate);
    const defaultToDate = @json($toDate);

    $('#convertedLeadsReportTable').removeClass('data_table_basic datatable');

    function getFilterParams() {
        return {
            date_from: $('#date_from').val() || '',
            date_to: $('#date_to').val() || '',
            is_b2b: $('#is_b2b').val() || '',
            lead_source_id: $('#lead_source_id').val() || '',
            course_id: $('#course_id').val() || '',
            lead_status_id: $('#lead_status_id').val() || '',
            first_lead_source_id: $('#first_lead_source_id').val() || '',
            first_lead_course_id: $('#first_lead_course_id').val() || '',
            first_lead_status_id: $('#first_lead_status_id').val() || ''
        };
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

    function updateExportButton() {
        const queryString = buildQueryString(getFilterParams());
        $('#exportConvertedLeadsExcel').attr('href', queryString ? `${exportBaseUrl}?${queryString}` : exportBaseUrl);
    }

    function updateDateRangeLabel() {
        const fromDate = $('#date_from').val() || defaultFromDate;
        const toDate = $('#date_to').val() || defaultToDate;
        $('#convertedLeadsDateRange').text(formatDisplayDate(fromDate) + ' – ' + formatDisplayDate(toDate));
    }

    function formatDisplayDate(dateStr) {
        if (!dateStr) {
            return '';
        }
        const date = new Date(dateStr + 'T00:00:00');
        if (Number.isNaN(date.getTime())) {
            return dateStr;
        }
        return date.toLocaleDateString('en-GB', {
            day: '2-digit',
            month: 'short',
            year: 'numeric'
        });
    }

    function updateUrlWithFilters() {
        const queryString = buildQueryString(getFilterParams());
        const newUrl = queryString
            ? `${window.location.pathname}?${queryString}`
            : window.location.pathname;
        window.history.replaceState({}, '', newUrl);
    }

    if ($.fn.DataTable.isDataTable('#convertedLeadsReportTable')) {
        $('#convertedLeadsReportTable').DataTable().destroy();
    }

    const reportTable = $('#convertedLeadsReportTable').DataTable({
        processing: true,
        serverSide: true,
        searching: true,
        scrollX: true,
        autoWidth: false,
        pageLength: 25,
        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, 'All']],
        order: [[1, 'desc']],
        ajax: {
            url: dataUrl,
            type: 'GET',
            data: function(d) {
                $.extend(d, getFilterParams());
            },
            error: function() {
                if (typeof showToast === 'function') {
                    showToast('Error loading converted leads report. Please try again.', 'error');
                }
            }
        },
        columns: [
            { data: 0, orderable: false, searchable: false },
            { data: 1 },
            { data: 2 },
            { data: 3, orderable: false },
            { data: 4 },
            { data: 5, orderable: false },
            { data: 6 },
            { data: 7, orderable: false },
            { data: 8, orderable: false },
            { data: 9, orderable: false },
            { data: 10, orderable: false },
            { data: 11, orderable: false },
            { data: 12, orderable: false },
            { data: 13, orderable: false },
            { data: 14, orderable: false }
        ],
        language: {
            processing: 'Loading...',
            search: 'Search:',
            lengthMenu: 'Show _MENU_ entries',
            info: 'Showing _START_ to _END_ of _TOTAL_ entries',
            infoEmpty: 'Showing 0 to 0 of 0 entries',
            zeroRecords: 'No converted leads found for the selected filters',
            emptyTable: 'No converted leads found for the selected filters',
            paginate: {
                first: 'First',
                last: 'Last',
                next: 'Next',
                previous: 'Previous'
            }
        },
        drawCallback: function(settings) {
            const api = this.api();
            const json = api.ajax.json();
            if (json && typeof json.recordsFiltered !== 'undefined') {
                $('#convertedLeadsTotalCount').text(json.recordsFiltered);
            }
        }
    });

    updateExportButton();
    updateDateRangeLabel();

    $('#applyConvertedLeadsFilters').on('click', function() {
        updateExportButton();
        updateDateRangeLabel();
        updateUrlWithFilters();
        reportTable.ajax.reload();
    });

    $('#resetConvertedLeadsFilters').on('click', function() {
        $('#date_from').val(defaultFromDate);
        $('#date_to').val(defaultToDate);
        $('#is_b2b').val('');
        $('#lead_source_id').val('');
        $('#course_id').val('');
        $('#lead_status_id').val('');
        $('#first_lead_source_id').val('');
        $('#first_lead_course_id').val('');
        $('#first_lead_status_id').val('');
        updateExportButton();
        updateDateRangeLabel();
        updateUrlWithFilters();
        reportTable.ajax.reload();
    });
});
</script>
@endpush
