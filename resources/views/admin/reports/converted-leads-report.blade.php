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
            <div class="card-body">
                <form method="GET" action="{{ route('admin.reports.converted-leads-report') }}" id="convertedLeadsReportFilter">
                    <div class="row g-3 align-items-end">
                        <div class="col-md-2">
                            <label for="date_from" class="form-label">Converted From</label>
                            <input type="date" class="form-control" id="date_from" name="date_from" value="{{ $fromDate }}">
                        </div>
                        <div class="col-md-2">
                            <label for="date_to" class="form-label">Converted To</label>
                            <input type="date" class="form-control" id="date_to" name="date_to" value="{{ $toDate }}">
                        </div>
                        <div class="col-md-2">
                            <label for="is_b2b" class="form-label">Type</label>
                            <select class="form-select" id="is_b2b" name="is_b2b">
                                <option value="">All Types</option>
                                <option value="0" {{ (string) $filters['is_b2b'] === '0' ? 'selected' : '' }}>In House</option>
                                <option value="1" {{ (string) $filters['is_b2b'] === '1' ? 'selected' : '' }}>B2B</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex gap-2 flex-wrap">
                                <button type="submit" class="btn btn-primary">
                                    <i class="ti ti-filter"></i> Filter
                                </button>
                                <a href="{{ route('admin.reports.converted-leads-report') }}" class="btn btn-outline-secondary">
                                    <i class="ti ti-refresh"></i> Reset
                                </a>
                                <a href="{{ route('admin.reports.converted-leads-report.excel', request()->query()) }}" class="btn btn-success">
                                    <i class="ti ti-file-excel"></i> Excel
                                </a>
                            </div>
                        </div>
                    </div>

                    <hr class="my-3">

                    <div class="row g-3">
                        <div class="col-12">
                            <h6 class="mb-0 text-muted">Current Lead Filters</h6>
                        </div>
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

                    <div class="row g-3 mt-1">
                        <div class="col-12">
                            <h6 class="mb-0 text-muted">First Lead Filters</h6>
                        </div>
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
                </form>
            </div>
        </div>
    </div>
</div>

<div class="row mb-3">
    <div class="col-12">
        <div class="card">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="mb-1">Total Converted</h6>
                    <h3 class="mb-0 text-success">{{ $convertedLeads->total() }}</h3>
                </div>
                <div class="text-muted">
                    {{ \Carbon\Carbon::parse($fromDate)->format('d M Y') }}
                    –
                    {{ \Carbon\Carbon::parse($toDate)->format('d M Y') }}
                </div>
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
                    <table class="table table-bordered table-hover align-middle">
                        <thead class="table-light">
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
                        <tbody>
                            @forelse($convertedLeads as $index => $convertedLead)
                                @php $lead = $convertedLead->lead; @endphp
                                <tr>
                                    <td>{{ $convertedLeads->firstItem() + $index }}</td>
                                    <td>{{ optional($convertedLead->created_at)->format('d M Y H:i') }}</td>
                                    <td>{{ $convertedLead->name ?: optional($lead)->title ?: 'N/A' }}</td>
                                    <td>{{ optional(optional($lead)->telecaller)->name ?: 'N/A' }}</td>
                                    <td>{{ \App\Helpers\PhoneNumberHelper::display($convertedLead->code, $convertedLead->phone) }}</td>
                                    <td>
                                        @if($lead && $lead->is_b2b)
                                            <span class="badge bg-info">B2B</span>
                                        @else
                                            <span class="badge bg-secondary">In House</span>
                                        @endif
                                    </td>
                                    <td>{{ optional(optional($lead)->course)->title ?: optional($convertedLead->course)->title ?: 'N/A' }}</td>
                                    <td>{{ optional(optional($lead)->createdBy)->name ?: 'N/A' }}</td>
                                    <td>{{ optional(optional($lead)->first_created_at)->format('d M Y H:i') ?: 'N/A' }}</td>
                                    <td>{{ optional(optional($lead)->created_at)->format('d M Y H:i') ?: 'N/A' }}</td>
                                    <td>
                                        @if(optional($lead)->leadStatus)
                                            <span class="badge" style="background-color: {{ $lead->leadStatus->color ?? '#6c757d' }}">
                                                {{ $lead->leadStatus->title }}
                                            </span>
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                    <td>{{ optional(optional($lead)->leadSource)->title ?: 'N/A' }}</td>
                                    <td>{{ optional(optional($lead)->firstLeadSource)->title ?: 'N/A' }}</td>
                                    <td>{{ optional(optional($lead)->firstLeadCourse)->title ?: 'N/A' }}</td>
                                    <td>
                                        @if(optional($lead)->firstLeadStatus)
                                            <span class="badge" style="background-color: {{ $lead->firstLeadStatus->color ?? '#6c757d' }}">
                                                {{ $lead->firstLeadStatus->title }}
                                            </span>
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="15" class="text-center py-5 text-muted">
                                        No converted leads found for the selected filters.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($convertedLeads->hasPages())
                    <div class="d-flex justify-content-end mt-3">
                        {{ $convertedLeads->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
