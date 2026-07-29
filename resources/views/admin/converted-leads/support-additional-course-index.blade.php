@extends('layouts.mantis')

@section('title', $pageTitle)

@section('content')
@php
    $indexRoute = $indexRoute ?? request()->route()->getName();
    $exportPage = $exportPage ?? 'support-additional';
@endphp
<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-6">
                <div class="page-header-title">
                    <h5 class="m-b-10">{{ $pageTitle }}</h5>
                </div>
            </div>
            <div class="col-md-6">
                <ul class="breadcrumb d-flex justify-content-end">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.converted-leads.index') }}">Converted Leads</a></li>
                    <li class="breadcrumb-item">{{ $pageTitle }}</li>
                </ul>
            </div>
        </div>
    </div>
</div>

@include('admin.converted-leads.partials.converted-leads-course-nav')
@include('admin.converted-leads.partials.mentor-list-nav', ['activeMentorRoute' => $activeMentorRoute ?? null])
@include('admin.converted-leads.partials.faculty-list-nav', ['activeFacultyRoute' => $activeFacultyRoute ?? null])
@include('admin.converted-leads.partials.converted-leads-support-nav')

<div class="row mb-3">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form method="GET" action="{{ route($indexRoute) }}" class="row g-3 align-items-end">
                    <div class="col-12 col-sm-6 col-md-3">
                        <label for="search" class="form-label">Search</label>
                        <input type="text" class="form-control" id="search" name="search"
                            value="{{ request('search') }}" placeholder="Name, Phone, Email, Register Number">
                    </div>
                    <div class="col-12 col-sm-6 col-md-2">
                        <label for="batch_id" class="form-label">Batch</label>
                        <select class="form-select" id="batch_id" name="batch_id">
                            <option value="">All Batches</option>
                            @foreach($batches as $batch)
                                <option value="{{ $batch->id }}" {{ (string) request('batch_id') === (string) $batch->id ? 'selected' : '' }}>
                                    {{ $batch->title }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @include('admin.converted-leads.partials.support-flag-filter-field')
                    <div class="col-12 col-sm-6 col-md-2">
                        <label for="date_from" class="form-label">From Date</label>
                        <input type="date" class="form-control" id="date_from" name="date_from" value="{{ request('date_from') }}">
                    </div>
                    <div class="col-12 col-sm-6 col-md-2">
                        <label for="date_to" class="form-label">To Date</label>
                        <input type="date" class="form-control" id="date_to" name="date_to" value="{{ request('date_to') }}">
                    </div>
                    <div class="col-12 col-md-3 d-flex gap-2">
                        <button type="submit" class="btn btn-primary"><i class="ti ti-search"></i> Filter</button>
                        <a href="{{ route($indexRoute) }}" class="btn btn-secondary"><i class="ti ti-x"></i> Clear</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">{{ $pageTitle }}</h5>
                @include('admin.converted-leads.partials.export-buttons', ['exportPage' => $exportPage])
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Support</th>
                                <th>Converted Date</th>
                                <th>Register Number</th>
                                <th>Name</th>
                                <th>Phone</th>
                                <th>Batch</th>
                                <th>Admission Batch</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($convertedLeads as $index => $convertedLead)
                            <tr>
                                <td>{{ $convertedLeads->firstItem() + $index }}</td>
                                <td>
                                    @include('admin.converted-leads.partials.status-badge', [
                                        'verified' => (bool) $convertedLead->is_support_verified,
                                        'label' => 'Support',
                                        'date' => $convertedLead->support_verified_at,
                                        'toggleUrl' => null
                                    ])
                                </td>
                                <td>{{ $convertedLead->created_at ? $convertedLead->created_at->format('d-m-Y') : '-' }}</td>
                                <td>{{ $convertedLead->register_number ?: '-' }}</td>
                                <td>{{ $convertedLead->name }}</td>
                                <td>{{ \App\Helpers\PhoneNumberHelper::display($convertedLead->code, $convertedLead->phone) }}</td>
                                <td>{{ $convertedLead->batch?->title ?: '-' }}</td>
                                <td>{{ $convertedLead->admissionBatch?->title ?: '-' }}</td>
                                <td>
                                    <a href="{{ route('admin.support-converted-leads.details', $convertedLead->id) }}" class="btn btn-sm btn-outline-primary" title="View">
                                        <i class="ti ti-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="9" class="text-center">No converted leads found</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">
                    {{ $convertedLeads->withQueryString()->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
