@extends('layouts.mantis')

@section('title', 'NatX Analytics')

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/call-analytics.css') }}">
@endpush

@section('content')
@php
    use App\Helpers\DateRangeHelper;
@endphp

<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-6">
                <div class="page-header-title">
                    <h5 class="m-b-10">NatX Analytics</h5>
                    <p class="m-b-0 text-muted">All users · call log and recording details</p>
                </div>
            </div>
            <div class="col-md-6">
                <ul class="breadcrumb d-flex justify-content-end">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item">NatX Analytics</li>
                </ul>
            </div>
        </div>
    </div>
</div>

@include('admin.natx-analytics.partials.nav-tabs', ['activeTab' => 'index'])

<div class="row mb-3 no-print">
    <div class="col-12">
        <div class="card">
            <div class="card-header py-3">
                <h5 class="mb-0"><i class="ti ti-filter me-2"></i>Search &amp; Filters</h5>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('admin.natx-analytics.index') }}">
                    <div class="row g-3 align-items-end">
                        @include('admin.natx-analytics.partials.date-range-filter')
                        <div class="col-md-2">
                            <label class="form-label">Role</label>
                            <select name="role_id" id="natxRoleFilter" class="form-select form-select-sm">
                                <option value="">All Roles</option>
                                @foreach($roles as $role)
                                    <option value="{{ $role->id }}" {{ (string) ($filters['role_id'] ?? '') === (string) $role->id ? 'selected' : '' }}>
                                        {{ $role->title }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">User</label>
                            <select name="user_id" id="natxUserFilter" class="form-select form-select-sm">
                                <option value="">All Users</option>
                                @foreach($users as $filterUser)
                                    <option value="{{ $filterUser->id }}" data-role="{{ $filterUser->role_id }}" {{ (string) ($filters['user_id'] ?? '') === (string) $filterUser->id ? 'selected' : '' }}>
                                        {{ $filterUser->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Call Type</label>
                            <select name="call_type" class="form-select form-select-sm">
                                <option value="">All Types</option>
                                @foreach(['incoming', 'outgoing', 'not_picked', 'missed', 'rejected', 'unknown'] as $type)
                                    <option value="{{ $type }}" {{ ($filters['call_type'] ?? '') === $type ? 'selected' : '' }}>{{ $type === 'not_picked' ? 'Not Picked' : ucfirst($type) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Search</label>
                            <input type="text" name="search" class="form-control form-control-sm" value="{{ $filters['search'] ?? '' }}" placeholder="Phone, contact, or device_call_id">
                        </div>
                        <div class="col-md-auto">
                            <button type="submit" class="btn btn-primary btn-sm"><i class="ti ti-search me-1"></i> Apply</button>
                            <a href="{{ route('admin.natx-analytics.index') }}" class="btn btn-outline-secondary btn-sm"><i class="ti ti-refresh me-1"></i> Reset</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@include('admin.natx-analytics.partials.active-filters')

@include('admin.natx-analytics.partials.stats-cards')

<div class="row">
    <div class="col-12">
        <div class="card ca-table-card">
            <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2 py-3">
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <h5 class="mb-0">NatX Call Logs</h5>
                    @if(!empty($activeUser))
                        <span class="badge bg-light text-dark border">{{ $activeUser->name }}</span>
                    @elseif(!empty($activeRole))
                        <span class="badge bg-light text-dark border">{{ $activeRole->title }}</span>
                    @else
                        <span class="badge bg-light text-dark border">All users</span>
                    @endif
                    <span class="badge bg-light-primary border border-primary ca-period-badge">
                        {{ DateRangeHelper::displayPeriod($filters) }}
                    </span>
                    <span class="badge bg-light-primary border border-primary">{{ $calls->total() }} {{ $calls->total() === 1 ? 'record' : 'records' }}</span>
                </div>
            </div>
            <div class="card-body">
                @include('admin.natx-analytics.partials.full-logs-table', ['calls' => $calls])
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
@include('admin.natx-analytics.partials.recording-scripts')
<script>
    function filterNatxUsersByRole() {
        const roleId = document.getElementById('natxRoleFilter')?.value || '';
        const userSelect = document.getElementById('natxUserFilter');
        if (!userSelect) {
            return;
        }

        let hasVisibleSelection = false;

        Array.from(userSelect.options).forEach((option) => {
            if (option.value === '') {
                option.hidden = false;
                return;
            }

            const matchesRole = !roleId || option.dataset.role === roleId;
            option.hidden = !matchesRole;

            if (matchesRole && option.selected) {
                hasVisibleSelection = true;
            }
        });

        if (!hasVisibleSelection) {
            userSelect.value = '';
        }
    }

    document.getElementById('natxRoleFilter')?.addEventListener('change', filterNatxUsersByRole);
    filterNatxUsersByRole();
</script>
@endpush
