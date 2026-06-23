@extends('layouts.mantis')

@section('title', $courseTitle . ' Faculty List')

@section('content')
<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-6">
                <div class="page-header-title">
                    <h5 class="m-b-10">{{ $courseTitle }} Faculty List</h5>
                </div>
            </div>
            <div class="col-md-6">
                <ul class="breadcrumb d-flex justify-content-end">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.converted-leads.index') }}">Converted Leads</a></li>
                    <li class="breadcrumb-item">{{ $courseTitle }} Faculty</li>
                </ul>
            </div>
        </div>
    </div>
</div>

@if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_academic_assistant() || \App\Helpers\RoleHelper::is_finance())
<div class="row mb-3">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h6 class="mb-3">Filter by Course</h6>
                <div class="d-flex gap-2 flex-wrap">
                    <a href="{{ route('admin.converted-leads.index') }}" class="btn btn-outline-primary">
                        <i class="ti ti-list"></i> All Converted Leads
                    </a>
                    <a href="{{ route('admin.nios-converted-leads.index') }}" class="btn btn-outline-success">
                        <i class="ti ti-school"></i> National Institute of Open Schooling Converted Leads
                    </a>
                    <a href="{{ route('admin.bosse-converted-leads.index') }}" class="btn btn-outline-warning">
                        <i class="ti ti-school-2"></i> Board of Open Schooling and Skill Education Converted Leads
                    </a>
                    <a href="{{ route('admin.ugpg-converted-leads.index') }}" class="btn btn-outline-warning">
                        <i class="ti ti-graduation"></i> UG/PG Converted Leads
                    </a>
                    <a href="{{ route('admin.edumaster-converted-leads.index') }}" class="btn btn-outline-warning">
                        <i class="ti ti-graduation"></i> EduMaster Converted Leads
                    </a>
                    <a href="{{ route('admin.hotel-management-converted-leads.index') }}" class="btn btn-outline-info">
                        <i class="ti ti-building"></i> Hotel Management Converted Leads
                    </a>
                    <a href="{{ route('admin.gmvss-converted-leads.index') }}" class="btn btn-outline-info">
                        <i class="ti ti-certificate"></i> Grameen Mukt Vidhyalayi Shiksha Sansthan Converted Leads
                    </a>
                    <a href="{{ route('admin.digital-marketing-converted-leads.index') }}" class="btn btn-outline-primary">
                        <i class="ti ti-marketing"></i> AI Integrated Digital Marketing Converted Leads
                    </a>
                    <a href="{{ route('admin.diploma-in-data-science-converted-leads.index') }}" class="btn btn-outline-primary">
                        <i class="ti ti-database"></i> Diploma in Data Science Converted Leads
                    </a>
                    <a href="{{ route('admin.web-development-converted-leads.index') }}" class="btn btn-outline-primary">
                        <i class="ti ti-world"></i> Web Development & Designing Converted Leads
                    </a>
                    <a href="{{ route('admin.vibe-coding-converted-leads.index') }}" class="btn btn-outline-primary">
                        <i class="ti ti-device-desktop"></i> Vibe Coding Converted Leads
                    </a>
                    <a href="{{ route('admin.graphic-designing-converted-leads.index') }}" class="btn btn-outline-primary">
                        <i class="ti ti-palette"></i> Diploma in Graphic Designing Converted Leads
                    </a>
                    <a href="{{ route('admin.machine-learning-converted-leads.index') }}" class="btn btn-outline-primary">
                        <i class="ti ti-brain"></i> Diploma in Machine Learning Converted Leads
                    </a>
                    <a href="{{ route('admin.flutter-converted-leads.index') }}" class="btn btn-outline-primary">
                        <i class="ti ti-device-mobile"></i> Flutter Converted Leads
                    </a>
                    <a href="{{ route('admin.eduthanzeel-converted-leads.index') }}" class="btn btn-outline-primary">
                        <i class="ti ti-school"></i> Eduthanzeel Converted Leads
                    </a>
                    <a href="{{ route('admin.e-school-converted-leads.index') }}" class="btn btn-outline-primary">
                        <i class="ti ti-device-laptop"></i> E-School Converted Leads
                    </a>
                    <a href="{{ route('admin.junior-vlogger-converted-leads.index') }}" class="btn btn-outline-primary">
                        <i class="ti ti-video"></i> CreateX AI Converted Leads
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

@include('admin.converted-leads.partials.mentor-list-nav', ['activeMentorRoute' => $activeMentorRoute ?? null])

@include('admin.converted-leads.partials.faculty-list-nav', ['activeFacultyRoute' => $activeFacultyRoute ?? $routeName ?? null])

@if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_support_team())
<div class="row mb-3">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h6 class="mb-3">Support List</h6>
                <div class="d-flex gap-2 flex-wrap">
                    <a href="{{ route('admin.support-bosse-converted-leads.index') }}" class="btn btn-outline-primary">
                        <i class="ti ti-headphones"></i> Board of Open Schooling and Skill Education Converted Support List
                    </a>
                    <a href="{{ route('admin.support-nios-converted-leads.index') }}" class="btn btn-outline-primary">
                        <i class="ti ti-headphones"></i> National Institute of Open Schooling Converted Support List
                    </a>
                    <a href="{{ route('admin.support-ugpg-converted-leads.index') }}" class="btn btn-outline-primary">
                        <i class="ti ti-headphones"></i> UG/PG Converted Support List
                    </a>
                    <a href="{{ route('admin.support-edumaster-converted-leads.index') }}" class="btn btn-outline-primary">
                        <i class="ti ti-headphones"></i> EduMaster Converted Support List
                    </a>
                    <a href="{{ route('admin.support-hotel-management-converted-leads.index') }}" class="btn btn-outline-primary">
                        <i class="ti ti-headphones"></i> Hotel Management Converted Support List
                    </a>
                    <a href="{{ route('admin.support-gmvss-converted-leads.index') }}" class="btn btn-outline-primary">
                        <i class="ti ti-headphones"></i> Grameen Mukt Vidhyalayi Shiksha Sansthan Converted Support List
                    </a>
                    <a href="{{ route('admin.support-digital-marketing-converted-leads.index') }}" class="btn btn-outline-primary">
                        <i class="ti ti-headphones"></i> AI Integrated Digital Marketing Converted Support List
                    </a>
                    <a href="{{ route('admin.support-diploma-in-data-science-converted-leads.index') }}" class="btn btn-outline-primary">
                        <i class="ti ti-headphones"></i> Diploma in Data Science Converted Support List
                    </a>
                    <a href="{{ route('admin.support-web-development-converted-leads.index') }}" class="btn btn-outline-primary">
                        <i class="ti ti-headphones"></i> Web Development & Designing Converted Support List
                    </a>
                    <a href="{{ route('admin.support-vibe-coding-converted-leads.index') }}" class="btn btn-outline-primary">
                        <i class="ti ti-headphones"></i> Vibe Coding Converted Support List
                    </a>
                    <a href="{{ route('admin.support-graphic-designing-converted-leads.index') }}" class="btn btn-outline-primary">
                        <i class="ti ti-headphones"></i> Diploma in Graphic Designing Converted Support List
                    </a>
                    <a href="{{ route('admin.support-machine-learning-converted-leads.index') }}" class="btn btn-outline-primary">
                        <i class="ti ti-headphones"></i> Diploma in Machine Learning Converted Support List
                    </a>
                    <a href="{{ route('admin.support-flutter-converted-leads.index') }}" class="btn btn-outline-primary">
                        <i class="ti ti-headphones"></i> Flutter Converted Support List
                    </a>
                    <a href="{{ route('admin.support-eduthanzeel-converted-leads.index') }}" class="btn btn-outline-primary">
                        <i class="ti ti-headphones"></i> Eduthanzeel Converted Support List
                    </a>
                    <a href="{{ route('admin.support-e-school-converted-leads.index') }}" class="btn btn-outline-primary">
                        <i class="ti ti-headphones"></i> E-School Converted Support List
                    </a>
                    <a href="{{ route('admin.support-junior-vlogger-converted-leads.index') }}" class="btn btn-outline-primary">
                        <i class="ti ti-headphones"></i> CreateX AI - Course Support List
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

<div class="row mb-3">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form method="GET" action="{{ route($routeName) }}" class="row g-3 align-items-end">
                    <div class="col-12 col-sm-6 col-md-3">
                        <label for="search" class="form-label">Search</label>
                        <input type="text" class="form-control" id="search" name="search"
                            value="{{ request('search') }}" placeholder="Name, Phone, Register Number">
                    </div>
                    @include('admin.converted-leads.partials.course-flag-filter-field')
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
                        <a href="{{ route($routeName) }}" class="btn btn-secondary"><i class="ti ti-x"></i> Clear</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card" data-mentor-update-url="{{ route('admin.converted-leads.inline-update', ['id' => '__ID__']) }}">
            <div class="card-header">
                <h5 class="mb-0">{{ $courseTitle }} Faculty List</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Academic</th>
                                <th>Support</th>
                                <th>Register Number</th>
                                <th>Converted Date</th>
                                <th>Course Flag</th>
                                    <th>Call Time</th>
                                <th>Name</th>
                                <th>Date of Birth</th>
                                <th>Type</th>
                                <th>Mobile</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($convertedLeads as $index => $convertedLead)
                            <tr>
                                <td>{{ $convertedLeads->firstItem() + $index }}</td>
                                <td>
                                    @include('admin.converted-leads.partials.status-badge', [
                                        'verified' => (bool) $convertedLead->is_academic_verified,
                                        'label' => 'Academic',
                                        'date' => $convertedLead->academic_verified_at,
                                        'toggleUrl' => null
                                    ])
                                </td>
                                <td>
                                    @include('admin.converted-leads.partials.status-badge', [
                                        'verified' => (bool) $convertedLead->is_support_verified,
                                        'label' => 'Support',
                                        'date' => $convertedLead->support_verified_at,
                                        'toggleUrl' => null
                                    ])
                                </td>
                                <td>{{ $convertedLead->register_number ?: '-' }}</td>
                                <td>{{ $convertedLead->created_at ? $convertedLead->created_at->format('d-m-Y') : '-' }}</td>
                                @include('admin.converted-leads.partials.inline-course-flag-cell', ['convertedLead' => $convertedLead])
                                    @include('admin.converted-leads.partials.inline-call-time-cell', ['convertedLead' => $convertedLead])
                                <td>{{ $convertedLead->name }}</td>
                                <td>{{ $convertedLead->dob ? \Carbon\Carbon::parse($convertedLead->dob)->format('d-m-Y') : '-' }}</td>
                                <td>{{ $convertedLead->is_b2b ? 'B2B' : 'In House' }}</td>
                                <td>{{ \App\Helpers\PhoneNumberHelper::display($convertedLead->code, $convertedLead->phone) }}</td>
                                <td>
                                    @if($convertedLead->mentorDetails?->is_placement_passed)
                                        <span class="badge bg-success">Placement Passed</span>
                                        @if($convertedLead->mentorDetails?->is_placement_passed_at)
                                            <br><small class="text-muted">{{ $convertedLead->mentorDetails->is_placement_passed_at->format('d-m-Y h:i A') }}</small>
                                        @endif

                                        @if($convertedLead->mentorDetails?->placement_resume)
                                            <br><a href="{{ asset('storage/' . $convertedLead->mentorDetails->placement_resume) }}" target="_blank" class="btn btn-sm btn-link p-0 small">
                                                <i class="ti ti-file-text"></i> View Resume
                                            </a>

                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_academic_assistant())
                                                <br><a href="javascript:void(0);" class="btn btn-sm {{ $convertedLead->mentorDetails->is_resume_verified ? 'btn-success' : 'btn-outline-success' }} px-2 py-0"
                                                    data-verify-url="{{ route('admin.converted-leads.verify-resume-modal', $convertedLead->id) }}"
                                                    onclick="show_small_modal(this.dataset.verifyUrl, 'Resume Verification')"
                                                    title="Resume Verification">
                                                    <i class="ti ti-circle-check"></i> {{ $convertedLead->mentorDetails->is_resume_verified ? 'Resume Verified' : 'Verify Resume' }}@if($convertedLead->mentorDetails->is_resume_verified && $convertedLead->mentorDetails->resume_verified_at) ({{ $convertedLead->mentorDetails->resume_verified_at->format('d M Y') }})@endif
                                                </a>
                                            @endif
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_academic_assistant() || \App\Helpers\RoleHelper::is_faculty() || \App\Helpers\RoleHelper::is_hod())
                                                <br><a href="javascript:void(0);" class="btn btn-sm btn-outline-primary px-2 py-0"
                                                    data-change-resume-url="{{ route('admin.converted-leads.move-to-placement', $convertedLead->id) }}"
                                                    onclick="show_small_modal(this.dataset.changeResumeUrl, 'Change Resume')"
                                                    title="Change Resume">
                                                    <i class="ti ti-upload"></i> Change Resume
                                                </a>
                                            @endif
                                        @endif
                                    @else
                                        <a href="javascript:void(0);" class="btn btn-outline-primary btn-sm px-2"
                                            data-placement-url="{{ route('admin.converted-leads.move-to-placement', $convertedLead->id) }}"
                                            onclick="show_small_modal(this.dataset.placementUrl, 'Move to Placement')"
                                            title="Move to Placement">
                                            <i class="ti ti-user-check"></i> Move to Placement
                                        </a>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="11" class="text-center">No converted leads found</td>
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
@include('admin.converted-leads.partials.placement-modal-reopen-script')
@push('scripts')
@include('admin.converted-leads.partials.course-flag-inline-scripts', ['courseUpdateUrl' => route('admin.converted-leads.inline-update', ['id' => '__ID__'])])
@endpush
@endsection

