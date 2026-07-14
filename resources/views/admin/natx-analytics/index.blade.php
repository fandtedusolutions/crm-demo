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
                            <select name="role_id" class="form-select form-select-sm js-analytics-role-filter">
                                <option value="">All Roles</option>
                                @foreach($roles as $role)
                                    <option value="{{ $role->id }}" {{ (string)($filters['role_id'] ?? '') === (string)$role->id ? 'selected' : '' }}>
                                        {{ $role->title }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2 js-analytics-team-filter-container" style="display: none;">
                            <label class="form-label">Team</label>
                            <select name="team_id" class="form-select form-select-sm js-analytics-team-filter">
                                <option value="">All Teams</option>
                                @foreach($teams as $team)
                                    <option value="{{ $team->id }}" {{ (string)($filters['team_id'] ?? '') === (string)$team->id ? 'selected' : '' }}>
                                        {{ $team->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">User</label>
                            <select name="user_id" class="form-select form-select-sm js-analytics-user-filter">
                                <option value="">All Users</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" data-role-id="{{ $user->role_id }}" data-team-id="{{ $user->team_id }}" {{ (string)($filters['user_id'] ?? '') === (string)$user->id ? 'selected' : '' }}>
                                        {{ $user->name }}
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
                    <span class="badge bg-light text-dark border">All users</span>
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
    $(function () {
        const $roleSelect = $('.js-analytics-role-filter');
        const $teamSelect = $('.js-analytics-team-filter');
        const $teamContainer = $('.js-analytics-team-filter-container');
        const $userSelect = $('.js-analytics-user-filter');

        function filterUsersAndTeams() {
            const selectedRole = $roleSelect.val();

            // Toggle Team filter visibility (only for role_id = 3)
            if (selectedRole === '3') {
                $teamContainer.show().find('select').prop('disabled', false);
            } else {
                $teamContainer.hide().find('select').prop('disabled', true).val('');
            }

            const currentSelectedRole = $roleSelect.val();
            const currentSelectedTeam = $teamSelect.is(':visible') ? $teamSelect.val() : '';

            $userSelect.find('option').each(function () {
                const $option = $(this);
                const userRole = $option.attr('data-role-id');
                const userTeam = $option.attr('data-team-id');

                let match = true;
                if (currentSelectedRole && userRole !== currentSelectedRole) {
                    match = false;
                }
                if (currentSelectedRole === '3' && currentSelectedTeam && userTeam !== currentSelectedTeam) {
                    match = false;
                }

                if (match || !$option.val()) {
                    $option.show().prop('disabled', false);
                } else {
                    $option.hide().prop('disabled', true);
                }
            });

            const $selectedOption = $userSelect.find('option:selected');
            if ($selectedOption.is(':disabled')) {
                $userSelect.val('');
            }
        }

        $roleSelect.on('change', function () {
            $teamSelect.val('');
            filterUsersAndTeams();
        });
        $teamSelect.on('change', filterUsersAndTeams);

        filterUsersAndTeams();
    });
</script>
@endpush
