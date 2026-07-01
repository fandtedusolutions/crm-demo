@extends('layouts.mantis')

@section('title', 'Lead Source Report')

@section('content')
<!-- [ breadcrumb ] start -->
<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-6">
                <div class="page-header-title">
                    <h5 class="m-b-10">Lead Source Report</h5>
                </div>
            </div>
            <div class="col-md-6">
                <div class="d-flex justify-content-end align-items-center gap-3">
                    <ul class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.reports.leads') }}">Reports</a></li>
                        <li class="breadcrumb-item">Lead Source</li>
                    </ul>
                    <a href="{{ route('admin.reports.leads') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="ti ti-arrow-left me-1"></i>Back to Reports
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- [ breadcrumb ] end -->

<!-- [ Printable Report Content ] start -->
<div class="printable-report">
    <div class="header text-center mb-4" style="display: none;">
        <h1>Lead Source Report</h1>
        <p>Report Period: {{ \Carbon\Carbon::parse($fromDate)->format('M d, Y') }} to {{ \Carbon\Carbon::parse($toDate)->format('M d, Y') }}</p>
        <p>Generated on: {{ now()->format('M d, Y H:i:s') }}</p>
    </div>
</div>
<!-- [ Printable Report Content ] end -->

<!-- [ Filter Form ] start -->
<div class="row mb-3 no-print">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form method="GET" action="{{ route('admin.reports.lead-source') }}" id="reportFilterForm">
                    <div class="row align-items-end">
                        <div class="col-md-2">
                            <label for="date_from" class="form-label">From Date</label>
                            <input type="date" class="form-control" id="date_from" name="date_from" 
                                   value="{{ $fromDate }}">
                        </div>
                        <div class="col-md-2">
                            <label for="date_to" class="form-label">To Date</label>
                            <input type="date" class="form-control" id="date_to" name="date_to" 
                                   value="{{ $toDate }}">
                        </div>
                        <div class="col-md-2">
                            <label for="lead_source_id" class="form-label">Lead Source</label>
                            <select class="form-select" id="lead_source_id" name="lead_source_id">
                                <option value="">All Sources</option>
                                @foreach($leadSources as $source)
                                    <option value="{{ $source->id }}" {{ $leadSourceId == $source->id ? 'selected' : '' }}>
                                        {{ $source->title }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex gap-2 flex-wrap align-items-end">
                                <button type="submit" class="btn btn-primary">
                                    <i class="ti ti-filter"></i> Generate Report
                                </button>
                                <a href="{{ route('admin.reports.lead-source') }}" class="btn btn-outline-secondary">
                                    <i class="ti ti-refresh"></i> Reset
                                </a>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.reports.lead-source.excel', request()->query()) }}" class="btn btn-success">
                                        <i class="ti ti-file-excel"></i> Excel
                                    </a>
                                    <a href="{{ route('admin.reports.lead-source.pdf', request()->query()) }}" class="btn btn-danger">
                                        <i class="ti ti-file-pdf"></i> PDF
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- [ Filter Form ] end -->

<!-- [ Report Summary ] start -->
<div class="printable-report">
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Lead Source Summary</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-lg-3 col-md-6 col-sm-6">
                        <div class="text-center p-3 border rounded">
                            <h3 class="text-primary mb-2">{{ $reports['lead_source']->sum('count') }}</h3>
                            <p class="text-muted mb-0 fw-medium">Total Leads</p>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 col-sm-6">
                        <div class="text-center p-3 border rounded">
                            <h3 class="text-info mb-2">{{ $reports['lead_source']->count() }}</h3>
                            <p class="text-muted mb-0 fw-medium">Lead Sources</p>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 col-sm-6">
                        <div class="text-center p-3 border rounded">
                            <h3 class="text-success mb-2">{{ $reports['lead_source']->max('count') ?? 0 }}</h3>
                            <p class="text-muted mb-0 fw-medium">Highest Source</p>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 col-sm-6">
                        <div class="text-center p-3 border rounded">
                            <h3 class="text-warning mb-2">{{ $reports['lead_source']->isNotEmpty() ? round($reports['lead_source']->avg('count'), 1) : 0 }}</h3>
                            <p class="text-muted mb-0 fw-medium">Average per Source</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- [ Report Summary ] end -->

<!-- [ Lead Source Report ] start -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="ti ti-tag me-2"></i>Lead Source Report
                </h5>
            </div>
            <div class="card-body">
                @if($reports['lead_source']->count() > 0)
                    <div class="table-responsive">
                        <table id="leadSourceReportTable" class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Source</th>
                                    <th class="text-end">Count</th>
                                    <th class="text-end">Percentage</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($reports['lead_source'] as $source)
                                    @php
                                        $total = $reports['lead_source']->sum('count');
                                        $percentage = $total > 0 ? round(($source->count / $total) * 100, 1) : 0;
                                    @endphp
                                    <tr>
                                        <td>
                                            <i class="ti ti-tag me-2 text-primary"></i>
                                            {{ $source->title }}
                                        </td>
                                        <td class="text-end fw-bold">{{ $source->count }}</td>
                                        <td class="text-end">{{ $percentage }}%</td>
                                        <td class="text-end">
                                            <a href="{{ route('leads.index', ['lead_source_id' => $source->id, 'date_from' => $fromDate, 'date_to' => $toDate]) }}" 
                                               class="btn btn-sm btn-outline-primary">
                                                <i class="ti ti-eye"></i> View Leads
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center text-muted py-4">
                        <i class="ti ti-tag f-48 mb-3"></i>
                        <p>No data available for the selected date range</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
<!-- [ Lead Source Report ] end -->

<!-- [ Monthly Trend ] start -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="ti ti-chart-line me-2"></i>Monthly Trend
                </h5>
            </div>
            <div class="card-body">
                @if($reports['monthly']->count() > 0)
                    <div class="table-responsive">
                        <table id="monthlyTrendTable" class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Month</th>
                                    <th class="text-end">Total Leads</th>
                                    <th class="text-end">Converted</th>
                                    <th class="text-end">Conversion Rate</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($reports['monthly'] as $month)
                                    <tr>
                                        <td>{{ $month->month }}</td>
                                        <td class="text-end fw-bold">{{ $month->total_leads > 0 ? $month->total_leads : '-' }}</td>
                                        <td class="text-end text-success">{{ $month->converted > 0 ? $month->converted : '-' }}</td>
                                        <td class="text-end">{{ $month->total_leads > 0 ? $month->conversion_rate . '%' : '0%' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center text-muted py-4">
                        <i class="ti ti-chart-line f-48 mb-3"></i>
                        <p>No monthly data available</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
<!-- [ Monthly Trend ] end -->

<!-- [ Leads Data ] start -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="ti ti-users me-2"></i>Leads Data
                </h5>
            </div>
            <div class="card-body">
                @if($leads->count() > 0)
                    <div class="table-responsive">
                        <table id="leadsDataTable" class="table table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Name</th>
                                    <th>Type</th>
                                    <th>Phone</th>
                                    <th>Email</th>
                                    <th>Status</th>
                                    <th>Source</th>
                                    <th>Telecaller</th>
                                    <th>Created</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($leads as $lead)
                                    <tr>
                                        <td>{{ $leads->firstItem() + $loop->index }}</td>
                                        <td>{{ $lead->title }}</td>
                                        <td>
                                            @if($lead->is_b2b)
                                                <span class="badge bg-info text-dark">B2B</span>
                                            @else
                                                <span class="badge bg-secondary text-white">In House</span>
                                            @endif
                                        </td>
                                        <td>{{ $lead->phone }}</td>
                                        <td>{{ $lead->email ?? '-' }}</td>
                                        <td>
                                            <span class="badge {{ \App\Helpers\StatusHelper::getLeadStatusColorClass($lead->leadStatus?->id) }}">
                                                {{ $lead->leadStatus->title ?? 'Unknown' }}
                                            </span>
                                        </td>
                                        <td>
                                            <i class="ti ti-tag me-1"></i>
                                            {{ $lead->leadSource->title ?? 'Unknown' }}
                                        </td>
                                        <td>{{ $lead->telecaller->name ?? '-' }}</td>
                                        <td>{{ $lead->created_at->format('M d, Y') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @if($leads->hasPages())
                        <div class="mt-3">
                            {{ $leads->links('pagination::bootstrap-5') }}
                        </div>
                    @endif
                @else
                    <div class="text-center text-muted py-4">
                        <i class="ti ti-users f-48 mb-3"></i>
                        <p>No leads found for the selected date range</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
<!-- [ Leads Data ] end -->
</div>
<!-- [ Printable Report Content ] end -->

@endsection

@push('styles')
<style>
@media print {
    body * {
        visibility: hidden;
    }
    .printable-report, .printable-report * {
        visibility: visible;
    }
    .printable-report {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
    }
    .no-print {
        display: none !important;
    }
    .card {
        border: none !important;
        box-shadow: none !important;
    }
    .btn {
        display: none !important;
    }
    .breadcrumb {
        display: none !important;
    }
    .page-header {
        display: none !important;
    }
}
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    // Initialize DataTable for Lead Source Report
    if ($.fn.DataTable.isDataTable('#leadSourceReportTable')) {
        $('#leadSourceReportTable').DataTable().destroy();
    }
    $('#leadSourceReportTable').DataTable({
        responsive: true,
        pageLength: 10,
        lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
        order: [[1, 'desc']], // Sort by count descending
        columnDefs: [
            { orderable: false, targets: [3] } // Disable sorting on Actions column
        ],
        language: {
            search: "Search sources:",
            lengthMenu: "Show _MENU_ sources per page",
            info: "Showing _START_ to _END_ of _TOTAL_ sources",
            paginate: {
                first: "First",
                last: "Last",
                next: "Next",
                previous: "Previous"
            }
        }
    });

    // Initialize DataTable for Monthly Trend
    if ($.fn.DataTable.isDataTable('#monthlyTrendTable')) {
        $('#monthlyTrendTable').DataTable().destroy();
    }
    $('#monthlyTrendTable').DataTable({
        responsive: true,
        pageLength: 12,
        lengthMenu: [[6, 12, 24, -1], [6, 12, 24, "All"]],
        order: [[0, 'asc']], // Sort by month ascending
        language: {
            search: "Search months:",
            lengthMenu: "Show _MENU_ months per page",
            info: "Showing _START_ to _END_ of _TOTAL_ months",
            paginate: {
                first: "First",
                last: "Last",
                next: "Next",
                previous: "Previous"
            }
        }
    });

    // Initialize DataTable for Leads Data
    if ($.fn.DataTable.isDataTable('#leadsDataTable')) {
        $('#leadsDataTable').DataTable().destroy();
    }
    $('#leadsDataTable').DataTable({
        responsive: true,
        pageLength: 25,
        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
        order: [[8, 'desc']], // Sort by created date descending
        columnDefs: [
            { orderable: false, targets: [0] } // Disable sorting on # column
        ],
        language: {
            search: "Search leads:",
            lengthMenu: "Show _MENU_ leads per page",
            info: "Showing _START_ to _END_ of _TOTAL_ leads",
            paginate: {
                first: "First",
                last: "Last",
                next: "Next",
                previous: "Previous"
            }
        }
    });
});
</script>
@endpush
