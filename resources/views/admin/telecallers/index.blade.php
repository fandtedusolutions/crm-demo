@extends('layouts.mantis')

@section('title', 'Telecaller Management')

@section('content')
<!-- [ breadcrumb ] start -->
<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-6">
                <div class="page-header-title">
                    <h5 class="m-b-10">Telecaller Management</h5>
                </div>
            </div>
            <div class="col-md-6">
                <ul class="breadcrumb d-flex justify-content-end">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item">User Management</li>
                    <li class="breadcrumb-item">Telecallers</li>
                </ul>
            </div>
        </div>
    </div>
</div>
<!-- [ breadcrumb ] end -->

<!-- [ Main Content ] start -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Telecaller List</h5>
                    <a href="javascript:void(0);" class="btn btn-primary btn-sm px-3"
                        onclick="show_small_modal('{{ route('admin.telecallers.add') }}', 'Add Telecaller')">
                        <i class="ti ti-plus"></i> Add New
                    </a>
                </div>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('admin.telecallers.index') }}" class="mb-4">
                    <div class="row g-2 align-items-end">
                        <div class="col-md-4 col-lg-3">
                            <label for="search" class="form-label">Search</label>
                            <input type="text" class="form-control form-control-sm" name="search" id="search"
                                value="{{ $search ?? request('search') }}"
                                placeholder="Name, phone or email">
                        </div>
                        <div class="col-md-3 col-lg-3">
                            <label for="filter_team_id" class="form-label">Team</label>
                            <select class="form-select form-select-sm" name="team_id" id="filter_team_id">
                                <option value="">All Teams</option>
                                @foreach($teams as $team)
                                    <option value="{{ $team->id }}" {{ (int) ($selectedTeamId ?? 0) === (int) $team->id ? 'selected' : '' }}>
                                        {{ $team->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-auto">
                            <button type="submit" class="btn btn-primary btn-sm">
                                <i class="ti ti-search me-1"></i> Search
                            </button>
                        </div>
                        @if($hasActiveFilters ?? false)
                        <div class="col-md-auto">
                            <a href="{{ route('admin.telecallers.index') }}" class="btn btn-outline-secondary btn-sm">
                                <i class="ti ti-x me-1"></i> Clear
                            </a>
                        </div>
                        @endif
                    </div>
                </form>

                @if($hasActiveFilters ?? false)
                    <p class="text-muted small mb-3">
                        Showing {{ $telecallers->count() }} {{ $telecallers->count() === 1 ? 'telecaller' : 'telecallers' }} matching your criteria.
                    </p>
                @endif

                <div class="table-responsive">
                    <table class="table table-striped datatable">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Extension</th>
                                <th>Team</th>
                                <th>Joining Date</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($telecallers as $index => $telecaller)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <span>{{ $telecaller->name }}</span>
                                        @if($telecaller->is_team_lead)
                                            <span class="badge bg-warning ms-2">
                                                <i class="ti ti-crown me-1"></i>Team Lead
                                            </span>
                                        @endif
                                        @if($telecaller->is_senior_manager)
                                            <span class="badge bg-info ms-2">
                                                <i class="ti ti-user-star me-1"></i>Senior Manager
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td>{{ $telecaller->email }}</td>
                                <td>{{ $telecaller->phone ?? '-' }}</td>
                                <td>{{ $telecaller->ext_no ?? '-' }}</td>
                                <td>{{ $telecaller->team ? $telecaller->team->name : '-' }}</td>
                                <td>
                                    @if($telecaller->joining_date)
                                        {{ $telecaller->joining_date->format('M d, Y') }}
                                    @else
                                        <span class="text-muted">Not set</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-success">Active</span>
                                </td>
                                <td>
                                    <a href="javascript:void(0);" class="btn btn-warning btn-sm shadow-sm px-3"
                                        onclick="show_small_modal('{{ route('admin.telecallers.edit', $telecaller->id) }}', 'Edit Telecaller')"
                                        title="Edit">
                                        <i class="ti ti-edit"></i> Edit
                                    </a>
                                    <a href="javascript:void(0);" class="btn btn-info btn-sm shadow-sm px-3"
                                        onclick="show_small_modal('{{ route('admin.telecallers.change-password', $telecaller->id) }}', 'Change Password')" title="Change Password">
                                        <i class="ti ti-key"></i> Password
                                    </a>
                                    <a href="javascript:void(0);" class="btn btn-danger btn-sm shadow-sm px-3"
                                        onclick="delete_modal('{{ route('admin.telecallers.delete', $telecaller->id) }}')" title="Delete">
                                        <i class="ti ti-trash"></i> Delete
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="9" class="text-center text-muted py-4">
                                    @if($hasActiveFilters ?? false)
                                        No telecallers found matching your search or filters.
                                    @else
                                        No telecallers found.
                                    @endif
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- [ Main Content ] end -->

@endsection

@push('scripts')
<script>
// DataTable is now initialized globally in footer-scripts.blade.php
// No need for duplicate initialization here
</script>
@endpush
