@extends('layouts.mantis')

@section('title', 'Team Management')

@section('content')
<!-- [ breadcrumb ] start -->
<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-6">
                <div class="page-header-title">
                    <h5 class="m-b-10">Team Management</h5>
                </div>
            </div>
            <div class="col-md-6">
                <ul class="breadcrumb d-flex justify-content-end">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item">Settings</li>
                    <li class="breadcrumb-item">Teams</li>
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
                    <div>
                        <h5 class="mb-0">Team List</h5>
                        @if(\App\Helpers\RoleHelper::is_finance())
                        <small class="text-muted">B2B teams only (read-only)</small>
                        @endif
                    </div>
                    @if(!\App\Helpers\RoleHelper::is_finance())
                    <a href="javascript:void(0);" class="btn btn-primary btn-sm px-3"
                        onclick="show_small_modal('{{ route('admin.teams.add') }}', 'Add Team')">
                        <i class="ti ti-plus"></i> Add New
                    </a>
                    @endif
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped datatable">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Department</th>
                                <th>Type</th>
                                <th>B2B Partner ID</th>
                                <th>B2B Code</th>
                                <th>Date of Joining</th>
                                <th>Partner Status</th>
                                <th>Description</th>
                                <th>Team Lead</th>
                                <th>Members</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($teams as $index => $team)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 35px; height: 35px;">
                                            <i class="ti ti-users"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-0 fw-semibold">{{ $team->name }}</h6>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @if($team->marketing_team)
                                        <span class="badge bg-warning text-dark">
                                            <i class="ti ti-marketing me-1"></i>Marketing
                                        </span>
                                    @else
                                        <span class="badge bg-success text-white">
                                            <i class="ti ti-shopping-cart me-1"></i>Sales
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    @if($team->is_b2b)
                                        <span class="badge bg-info text-dark">B2B</span>
                                    @else
                                        <span class="badge bg-secondary text-white">In house</span>
                                    @endif
                                    @if($team->is_postsale)
                                        <span class="badge bg-dark">Post-sale</span>
                                    @endif
                                </td>
                                <td>{{ $team->detail->b2b_partner_id ?? 'N/A' }}</td>
                                <td>{{ $team->detail->b2b_code ?? 'N/A' }}</td>
                                <td>{{ $team->detail->date_of_joining ?? 'N/A' }}</td>
                                <td>
                                    @php
                                        $status = $team->detail->partner_status ?? 'N/A';
                                        $badgeClass = 'bg-light text-muted';
                                        if($status == 'Active') $badgeClass = 'bg-success text-white';
                                        elseif($status == 'Inactive') $badgeClass = 'bg-warning text-dark';
                                        elseif($status == 'Suspended') $badgeClass = 'bg-danger text-white';
                                    @endphp
                                    <span class="badge {{ $badgeClass }}">{{ $status }}</span>
                                </td>
                                <td>
                                    <span class="text-muted">{{ $team->description ?? 'No description' }}</span>
                                </td>
                                <td>
                                    @if($team->teamLead)
                                        <div class="d-flex align-items-center">
                                            <div class="bg-warning text-dark rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 30px; height: 30px;">
                                                <i class="ti ti-crown"></i>
                                            </div>
                                            <div>
                                                <h6 class="mb-0 fw-semibold">{{ $team->teamLead->name }}</h6>
                                                <small class="text-muted">{{ $team->teamLead->email }}</small>
                                            </div>
                                        </div>
                                    @else
                                        <span class="badge bg-light text-muted">
                                            <i class="ti ti-user-x me-1"></i>No Team Lead
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <span class="badge bg-info text-white me-2">{{ $team->users->count() }}</span>
                                        <span class="text-muted">members</span>
                                    </div>
                                </td>
                                <td>
                                    @if($team->is_b2b)
                                    <a href="javascript:void(0);" class="btn btn-outline-secondary btn-sm shadow-sm px-3 me-1" title="Update Terms and Conditions"
                                        onclick="show_large_modal('{{ route('admin.teams.terms-and-conditions', $team->id) }}', 'Terms and Conditions - {{ addslashes(str_replace(["\r","\n"], '', $team->name)) }}')">
                                        <i class="ti ti-file-text"></i> Terms
                                    </a>
                                    @if(!\App\Helpers\RoleHelper::is_finance())
                                    <a href="{{ route('public.team.register', $team->id) }}" target="_blank" class="btn btn-primary btn-sm shadow-sm px-3 me-1" title="Public Registration Form">
                                        <i class="ti ti-external-link"></i> Public Form
                                    </a>
                                    @endif
                                    @if($team->detail)
                                    <a href="{{ route('admin.teams.details', $team->id) }}" class="btn btn-dark btn-sm shadow-sm px-3 me-1" title="View Submitted Details">
                                        <i class="ti ti-file-text"></i> Details
                                    </a>
                                    @endif
                                    @endif
                                    <a href="javascript:void(0);" class="btn btn-info btn-sm shadow-sm px-3 me-1"
                                        onclick="show_ajax_modal('{{ route('admin.teams.members', $team->id) }}', 'Team Members - {{ $team->name }}')"
                                        title="View Members">
                                        <i class="ti ti-users"></i> Members
                                    </a>
                                    @if(!\App\Helpers\RoleHelper::is_finance())
                                    <a href="javascript:void(0);" class="btn btn-warning btn-sm shadow-sm px-3 me-1"
                                        onclick="show_small_modal('{{ route('admin.teams.edit', $team->id) }}', 'Edit Team')"
                                        title="Edit">
                                        <i class="ti ti-edit"></i> Edit
                                    </a>
                                    <a href="javascript:void(0);" class="btn btn-danger btn-sm shadow-sm px-3"
                                        onclick="delete_modal('{{ route('admin.teams.delete', $team->id) }}')" title="Delete">
                                        <i class="ti ti-trash"></i> Delete
                                    </a>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- [ Main Content ] end -->

@endsection

