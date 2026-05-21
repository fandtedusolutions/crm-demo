@extends('layouts.mantis')

@section('title', 'Digital Marketing Course Mentor List')

@section('content')
@php
$canEditRestricted = \App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_hod() || \App\Helpers\RoleHelper::is_admission_counsellor();
$restrictedFieldsServer = [
'phone',
'batch_id',
'admission_batch_id',
'internship_id',
'email',
'call_status',
'orientation_class_date',
'class_start_date',
'class_end_date',
'whatsapp_group_status',
'class_time_id',
'programme_type',
'location',
'total_class',
'total_present',
'total_absent',
'final_certificate_examination_date',
'certificate_examination_marks',
'final_interview_date',
'interview_marks',
'certificate_distribution_date',
'experience_certificate_distribution_date',
'completed_cancelled_date',
'remarks',
];
@endphp
<style>
    .table td {
        white-space: nowrap;
        vertical-align: middle;
    }

    .table td .btn-group {
        white-space: nowrap;
    }

    .table td .inline-edit {
        white-space: nowrap;
    }

    .table td .display-value {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 150px;
        display: inline-block;
    }

    .cancelled-row>td {
        background-color: #fff1f0 !important;
    }

    .cancelled-card {
        border: 1px solid #f5c2c7;
        background-color: #fff5f5;
    }
</style>

<div id="can-edit-restricted-flag" data-value="{{ $canEditRestricted ? 'true' : 'false' }}" style="display:none;"></div>
@php
$restrictedSelectorCss = collect($restrictedFieldsServer)
->map(function ($field) {
return ".inline-edit[data-field='{$field}'] .edit-btn";
})
->implode(",\n");
@endphp
@if(!$canEditRestricted && $restrictedSelectorCss)
@push('scripts')
<script>
    (function() {
        const css = `{!! addslashes($restrictedSelectorCss) !!} { display: none !important; }`;
        const style = document.createElement('style');
        style.textContent = css;
        document.head.appendChild(style);
    })();
</script>
@endpush
@endif
<!-- [ breadcrumb ] start -->
<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-6">
                <div class="page-header-title">
                    <h5 class="m-b-10">Digital Marketing Course Mentor List</h5>
                </div>
            </div>
            <div class="col-md-6">
                <ul class="breadcrumb d-flex justify-content-end">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.converted-leads.index') }}">Converted Leads</a></li>
                    <li class="breadcrumb-item">Digital Marketing Mentor</li>
                </ul>
            </div>
        </div>
    </div>
</div>
<!-- [ breadcrumb ] end -->

<!-- [ Course Filter Buttons ] start -->
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
                        <i class="ti ti-school"></i> NIOS Converted Leads
                    </a>
                    <a href="{{ route('admin.bosse-converted-leads.index') }}" class="btn btn-outline-warning">
                        <i class="ti ti-school-2"></i> BOSSE Converted Leads
                    </a>
                    <a href="{{ route('admin.ugpg-converted-leads.index') }}" class="btn btn-outline-warning">
                        <i class="ti ti-graduation"></i> UG/PG Converted Leads
                    </a>
                    <a href="{{ route('admin.hotel-management-converted-leads.index') }}" class="btn btn-outline-info">
                        <i class="ti ti-building"></i> Hotel Management Converted Leads
                    </a>
                    <a href="{{ route('admin.gmvss-converted-leads.index') }}" class="btn btn-outline-info">
                        <i class="ti ti-certificate"></i> GMVSS Converted Leads
                    </a>
                    <a href="{{ route('admin.digital-marketing-converted-leads.index') }}" class="btn btn-outline-primary">
                        <i class="ti ti-marketing"></i> Digital Marketing Converted Leads
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
                        <i class="ti ti-palette"></i> Graphic Designing Converted Leads
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
                        <i class="ti ti-video"></i> Junior Vlogger Converted Leads
                    </a>
                    <a href="{{ route('admin.junior-vlogger-mentor-converted-leads.index') }}" class="btn btn-outline-primary">
                        <i class="ti ti-user-star"></i> Junior Vlogger Converted Mentor List
                    </a>
                    <a href="{{ route('admin.support-junior-vlogger-converted-leads.index') }}" class="btn btn-outline-primary">
                        <i class="ti ti-headphones"></i> Junior Vlogger â€“ Course Support List
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endif
<!-- [ Course Filter Buttons ] end -->

<!-- [ Mentor List ] start -->
@if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_mentor() || \App\Helpers\RoleHelper::is_telecaller() || \App\Helpers\RoleHelper::is_team_lead() || \App\Helpers\RoleHelper::is_senior_manager() || \App\Helpers\RoleHelper::is_hod())
<div class="row mb-3">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h6 class="mb-3">Mentor List</h6>
                <div class="d-flex gap-2 flex-wrap">
                    @if(\App\Helpers\RoleHelper::is_mentor() || \App\Helpers\RoleHelper::is_telecaller() || \App\Helpers\RoleHelper::is_team_lead() || \App\Helpers\RoleHelper::is_senior_manager() || \App\Helpers\RoleHelper::is_hod())
                    <a href="{{ route('admin.converted-leads.index') }}" class="btn btn-outline-primary">
                        <i class="ti ti-list"></i> All Converted Leads
                    </a>
                    @endif
                    <a href="{{ route('admin.mentor-bosse-converted-leads.index') }}" class="btn btn-outline-primary">
                        <i class="ti ti-user-star"></i> Bosse Converted Mentor List
                    </a>
                    <a href="{{ route('admin.mentor-nios-converted-leads.index') }}" class="btn btn-outline-primary">
                        <i class="ti ti-user-star"></i> NIOS Converted Mentor List
                    </a>
                    <a href="{{ route('admin.mentor-ugpg-converted-leads.index') }}" class="btn btn-outline-primary">
                        <i class="ti ti-user-star"></i> UG/PG Mentor Converted List
                    </a>
                    <a href="{{ route('admin.mentor-edumaster-converted-leads.index') }}" class="btn btn-outline-primary">
                        <i class="ti ti-user-star"></i> EduMaster Mentor Converted List
                    </a>
                    <a href="{{ route('admin.mentor-eschool-converted-leads.index') }}" class="btn btn-outline-primary">
                        <i class="ti ti-user-star"></i> E-School Converted Mentor List
                    </a>
                    <a href="{{ route('admin.mentor-eduthanzeel-converted-leads.index') }}" class="btn btn-outline-primary">
                        <i class="ti ti-user-star"></i> Eduthanzeel Converted Mentor List
                    </a>
                    <a href="{{ route('admin.gmvss-mentor-converted-leads.index') }}" class="btn btn-outline-primary">
                        <i class="ti ti-user-star"></i> GMVSS Mentor List
                    </a>
                    <a href="{{ route('admin.digital-marketing-mentor-converted-leads.index') }}" class="btn btn-outline-primary active">
                        <i class="ti ti-user-star"></i> Digital Marketing Mentor List
                    </a>
                    <a href="{{ route('admin.data-science-mentor-converted-leads.index') }}" class="btn btn-outline-primary">
                        <i class="ti ti-user-star"></i> Data Science Course Mentor List
                    </a>
                    <a href="{{ route('admin.graphic-designing-mentor-converted-leads.index') }}" class="btn btn-outline-primary">
                        <i class="ti ti-user-star"></i> Graphic Designing Mentor List
                    </a>
                    <a href="{{ route('admin.machine-learning-mentor-converted-leads.index') }}" class="btn btn-outline-primary">
                        <i class="ti ti-user-star"></i> Machine Learning Mentor List
                    </a>
                    <a href="{{ route('admin.medical-coding-mentor-converted-leads.index') }}" class="btn btn-outline-primary">
                        <i class="ti ti-user-star"></i> Medical Coding Mentor List
                    </a>
                    <a href="{{ route('admin.python-mentor-converted-leads.index') }}" class="btn btn-outline-primary">
                        <i class="ti ti-user-star"></i> Python Mentor List
                    </a>
                    <a href="{{ route('admin.flutter-mentor-converted-leads.index') }}" class="btn btn-outline-primary">
                        <i class="ti ti-user-star"></i> Flutter Mentor List
                    </a>
                    <a href="{{ route('admin.rpa-mentor-converted-leads.index') }}" class="btn btn-outline-primary">
                        <i class="ti ti-user-star"></i> RPA Mentor List
                    </a>
                    <a href="{{ route('admin.junior-vlogger-mentor-converted-leads.index') }}" class="btn btn-outline-primary">
                        <i class="ti ti-user-star"></i> Junior Vlogger Converted Mentor List
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endif
<!-- [ Mentor List ] end -->

<!-- [ Support List ] start -->
@if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_support_team())
<div class="row mb-3">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h6 class="mb-3">Support List</h6>
                <div class="d-flex gap-2 flex-wrap">
                    <a href="{{ route('admin.support-bosse-converted-leads.index') }}" class="btn btn-outline-primary">
                        <i class="ti ti-headphones"></i> Bosse Converted Support List
                    </a>
                    <a href="{{ route('admin.support-nios-converted-leads.index') }}" class="btn btn-outline-primary">
                        <i class="ti ti-headphones"></i> NIOS Converted Support List
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
                        <i class="ti ti-headphones"></i> GMVSS Converted Support List
                    </a>
                    <a href="{{ route('admin.support-digital-marketing-converted-leads.index') }}" class="btn btn-outline-primary">
                        <i class="ti ti-headphones"></i> Digital Marketing Converted Support List
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
                        <i class="ti ti-headphones"></i> Graphic Designing Converted Support List
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
                        <i class="ti ti-headphones"></i> Junior Vlogger â€“ Course Support List
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endif
<!-- [ Support List ] end -->

<!-- [ Filter Section ] start -->
<div class="row mb-3">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form method="GET" action="{{ route('admin.digital-marketing-mentor-converted-leads.index') }}" id="filterForm">
                    <div class="row g-3 align-items-end">
                        <div class="col-12 col-sm-6 col-md-2">
                            <label for="search" class="form-label">Search</label>
                            <input type="text" class="form-control" id="search" name="search"
                                value="{{ request('search') }}" placeholder="Name, Phone, Email, Register Number">
                        </div>
                        <div class="col-12 col-sm-6 col-md-2">
                            <label for="batch_id" class="form-label">Batch</label>
                            <select class="form-select" id="batch_id" name="batch_id">
                                <option value="">All Batches</option>
                                @foreach($batches as $batch)
                                <option value="{{ $batch->id }}" {{ request('batch_id') == $batch->id ? 'selected' : '' }}>
                                    {{ $batch->title }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-sm-6 col-md-2">
                            <label for="admission_batch_id" class="form-label">Admission Batch</label>
                            <select class="form-select" id="admission_batch_id" name="admission_batch_id" data-selected="{{ request('admission_batch_id') }}">
                                <option value="">All Admission Batches</option>
                            </select>
                        </div>
                        @include('admin.converted-leads.partials.mentor-flag-filter-field')
                        
                        <div class="col-12 col-sm-6 col-md-2">
                            <label for="date_from" class="form-label">From Date</label>
                            <input type="date" class="form-control" id="date_from" name="date_from"
                                value="{{ request('date_from') }}">
                        </div>
                        <div class="col-12 col-sm-6 col-md-2">
                            <label for="date_to" class="form-label">To Date</label>
                            <input type="date" class="form-control" id="date_to" name="date_to"
                                value="{{ request('date_to') }}">
                        </div>
                        <div class="col-12 col-sm-6 col-md-2">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" id="status" name="status">
                                <option value="">All</option>
                                <option value="Paid" {{ request('status')==='Paid' ? 'selected' : '' }}>Paid</option>
                                <option value="Admission cancel" {{ request('status')==='Admission cancel' ? 'selected' : '' }}>Admission cancel</option>
                                <option value="Active" {{ request('status')==='Active' ? 'selected' : '' }}>Active</option>
                                <option value="Inactive" {{ request('status')==='Inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>
                        <div class="col-12 col-sm-6 col-md-2">
                            <label for="call_status" class="form-label">Call Status</label>
                            <select class="form-select" id="call_status" name="call_status">
                                <option value="">All</option>
                                <option value="Call Not Answered" {{ request('call_status')==='Call Not Answered' ? 'selected' : '' }}>Call Not Answered</option>
                                <option value="Switched Off" {{ request('call_status')==='Switched Off' ? 'selected' : '' }}>Switched Off</option>
                                <option value="Line Busy" {{ request('call_status')==='Line Busy' ? 'selected' : '' }}>Line Busy</option>
                                <option value="Student Asks to Call Later" {{ request('call_status')==='Student Asks to Call Later' ? 'selected' : '' }}>Student Asks to Call Later</option>
                                <option value="Lack of Interest in Conversation" {{ request('call_status')==='Lack of Interest in Conversation' ? 'selected' : '' }}>Lack of Interest in Conversation</option>
                                <option value="Wrong Contact" {{ request('call_status')==='Wrong Contact' ? 'selected' : '' }}>Wrong Contact</option>
                                <option value="Inconsistent Responses" {{ request('call_status')==='Inconsistent Responses' ? 'selected' : '' }}>Inconsistent Responses</option>
                                <option value="Task Complete" {{ request('call_status')==='Task Complete' ? 'selected' : '' }}>Task Complete</option>
                                <option value="Admission cancel" {{ request('call_status')==='Admission cancel' ? 'selected' : '' }}>Admission cancel</option>
                            </select>
                        </div>
                        <div class="col-12 col-sm-6 col-md-2">
                            <label for="class_information" class="form-label">Class Information</label>
                            <select class="form-select" id="class_information" name="class_information">
                                <option value="">All</option>
                                <option value="phone call" {{ request('class_information')==='phone call' ? 'selected' : '' }}>Phone Call</option>
                                <option value="whatsapp" {{ request('class_information')==='whatsapp' ? 'selected' : '' }}>WhatsApp</option>
                            </select>
                        </div>
                        <div class="col-12 col-sm-6 col-md-2">
                            <label for="orientation_class_status" class="form-label">Orientation Class Status</label>
                            <select class="form-select" id="orientation_class_status" name="orientation_class_status">
                                <option value="">All</option>
                                <option value="Participated" {{ request('orientation_class_status')==='Participated' ? 'selected' : '' }}>Participated</option>
                                <option value="Did not participated" {{ request('orientation_class_status')==='Did not participated' ? 'selected' : '' }}>Did not participated</option>
                            </select>
                        </div>
                        <div class="col-12 col-sm-6 col-md-2">
                            <label for="whatsapp_group_status" class="form-label">WhatsApp Group Status</label>
                            <select class="form-select" id="whatsapp_group_status" name="whatsapp_group_status">
                                <option value="">All</option>
                                <option value="sent link" {{ request('whatsapp_group_status')==='sent link' ? 'selected' : '' }}>Sent Link</option>
                                <option value="task complete" {{ request('whatsapp_group_status')==='task complete' ? 'selected' : '' }}>Task Complete</option>
                            </select>
                        </div>
                        <div class="col-12 col-sm-6 col-md-2">
                            <label for="class_status" class="form-label">Class Status</label>
                            <select class="form-select" id="class_status" name="class_status">
                                <option value="">All</option>
                                <option value="Running" {{ request('class_status')==='Running' ? 'selected' : '' }}>Running</option>
                                <option value="Cancel" {{ request('class_status')==='Cancel' ? 'selected' : '' }}>Cancel</option>
                                <option value="complete" {{ request('class_status')==='complete' ? 'selected' : '' }}>Complete</option>
                            </select>
                        </div>
                        <div class="col-12 col-sm-6 col-md-2">
                            <label for="programme_type" class="form-label">Course Type</label>
                            <select class="form-select" id="programme_type" name="programme_type">
                                <option value="">All</option>
                                <option value="online" {{ request('programme_type')==='online' ? 'selected' : '' }}>Online</option>
                                <option value="offline" {{ request('programme_type')==='offline' ? 'selected' : '' }}>Offline</option>
                            </select>
                        </div>
                        <div class="col-12 col-sm-6 col-md-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="ti ti-search"></i> Filter
                            </button>
                            <a href="{{ route('admin.data-science-mentor-converted-leads.index') }}" class="btn btn-secondary">
                                <i class="ti ti-refresh"></i> Clear
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- [ Filter Section ] end -->

<!-- [ Main Content ] start -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5>Digital Marketing Course Mentor List</h5>
            </div>
            <div class="card-body">
                <!-- Desktop Table View -->
                <div class="d-none d-lg-block">
                    <div class="table-responsive">
                        <table class="table table-hover data_table_basic" id="dataScienceMentorTable">
                            <thead>
                                <tr>
                                    <th>SL No</th>
                                    <th>Academic</th>
                                    <th>Support</th>
                                    <th>Converted Date</th>
                                    <th>Register Number</th>
                                    <th>Flag</th>
                                    <th>Name</th>
                                    <th>Type</th>
                                    <th>Phone</th>
                                    <th>WhatsApp</th>
                                    @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor())
                                    <th>Parent Phone</th>
                                    @endif
                                    <th>Course Type</th>
                                    <th>Location</th>
                                    <th>Batch</th>
                                    <th>Admission Batch</th>
                                    <th>Internship Id</th>
                                    <th>Email Id</th>
                                    <th>Call Status</th>
                                    <th>Orientation Class Date</th>
                                    <th>Class Start Date</th>
                                    <th>Class End Date</th>
                                    <th>Whatsapp Group Status</th>
                                    <th>Platform</th>
                                    <th>Class Time</th>
                                    <th>Place</th>
                                    <th>1st Month Exam Date</th>
                                    <th>1st Month Marks</th>
                                    <th>1st Month Feedback</th>
                                    <th>2nd Month Exam Date</th>
                                    <th>2nd Month Marks</th>
                                    <th>2nd Month Feedback</th>
                                    <th>AI Workshop Attendance</th>
                                    <th>3rd Month Exam Date</th>
                                    <th>3rd Month Marks</th>
                                    <th>3rd Month Feedback</th>
                                    <th>Initial Project Start Date</th>
                                    <th>Initial Project End Date</th>
                                    <th>Initial Project Marks</th>
                                    <th>Initial Project Feedback</th>
                                    <th>Mock Test Date</th>
                                    <th>Mock Test Marks</th>
                                    <th>Mock Test Feedback</th>
                                    <th>Mock Interview Date</th>
                                    <th>Mock Interview Marks</th>
                                    <th>Mock Interview Feedback</th>
                                    <th>Final Project Start Date</th>
                                    <th>Final Project End Date</th>
                                    <th>Final Project Marks</th>
                                    <th>Final Project Feedback</th>
                                    <th>Total Class</th>
                                    <th>Total Present</th>
                                    <th>Total Absent</th>
                                    <th>Final Certificate Examination Date</th>
                                    <th>Certificate Examination Marks</th>
                                    <th>Final Interview Date</th>
                                    <th>Interview Marks</th>
                                    <th>Certificate Distribution Date</th>
                                    <th>Experience Certificate Distribution Date</th>
                                    <th>Cancelled Date</th>
                                    <th>Remarks</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($convertedLeads as $index => $convertedLead)
                                <tr class="{{ $convertedLead->is_cancelled ? 'cancelled-row' : '' }}">
                                    @php
                                    $canToggleAcademic = \App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_academic_assistant() || \App\Helpers\RoleHelper::is_admission_counsellor();
                                    $canToggleSupport = \App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_support_team();
                                    @endphp
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        @include('admin.converted-leads.partials.status-badge', [
                                        'convertedLead' => $convertedLead,
                                        'type' => 'academic',
                                        'showToggle' => $canToggleAcademic,
                                        'toggleUrl' => $canToggleAcademic ? route('admin.converted-leads.toggle-academic-verify', $convertedLead->id) : null,
                                        'title' => 'academic',
                                        'useModal' => true
                                        ])
                                    </td>
                                    <td>
                                        @include('admin.converted-leads.partials.status-badge', [
                                        'convertedLead' => $convertedLead,
                                        'type' => 'support',
                                        'showToggle' => $canToggleSupport,
                                        'toggleUrl' => $canToggleSupport ? route('admin.support-converted-leads.toggle-support-verify', $convertedLead->id) : null,
                                        'title' => 'support',
                                        'useModal' => true
                                        ])
                                    </td>
                                    <td>{{ $convertedLead->created_at->format('d-m-Y') }}</td>
                                    <td>{{ $convertedLead->register_number ?? '-' }}</td>
                                    @include('admin.converted-leads.partials.inline-flag-cell', ['convertedLead' => $convertedLead])
                                    <td>
                                        {{ $convertedLead->name }}
                                        @if($convertedLead->is_cancelled)
                                        <div>
                                            <span class="badge bg-danger ms-2">Cancelled</span>
                                            @if($convertedLead->cancelledBy)
                                                <br><small class="text-muted ms-2">By: {{ $convertedLead->cancelledBy->name }}
                                                @if($convertedLead->cancelled_at)
                                                    ({{ $convertedLead->cancelled_at->format('d-m-Y h:i A') }})
                                                @endif
                                                </small>
                                            @endif
                                        </div>
                                        @endif
                                    </td>
                                    <td>{{ $convertedLead->is_b2b == 1 ? ('B2B' . ($convertedLead->lead?->team?->name ? ' (' . $convertedLead->lead->team->name . ')' : '')) : 'In House' }}</td>
                                    <td>
                                        <div class="inline-edit" data-field="phone" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->phone }}">
                                            <span class="display-value">{{ \App\Helpers\PhoneNumberHelper::display($convertedLead->code, $convertedLead->phone) }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_academic_assistant() || \App\Helpers\RoleHelper::is_hod() || \App\Helpers\RoleHelper::is_mentor())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                        <div class="d-none inline-code-value" data-field="code" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->code }}"></div>
                                    </td>
                                    <td>
                                        @if($convertedLead->leadDetail && $convertedLead->leadDetail->whatsapp_number)
                                            {{ \App\Helpers\PhoneNumberHelper::display($convertedLead->leadDetail->whatsapp_code, $convertedLead->leadDetail->whatsapp_number) }}
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                    @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor())
                                    <td>
                                        @if($convertedLead->leadDetail && $convertedLead->leadDetail->parents_number)
                                            {{ \App\Helpers\PhoneNumberHelper::display($convertedLead->leadDetail->parents_code, $convertedLead->leadDetail->parents_number) }}
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                    @endif
                                    <td>{{ $convertedLead->leadDetail?->programme_type ? ucfirst($convertedLead->leadDetail->programme_type) : '-' }}</td>
                                    <td>
                                        @if($convertedLead->leadDetail?->programme_type === 'offline')
                                            {{ $convertedLead->leadDetail?->location ?? '-' }}
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="inline-edit" data-field="batch_id" data-id="{{ $convertedLead->id }}" data-course-id="{{ $convertedLead->course_id }}" data-current-id="{{ $convertedLead->batch_id }}">
                                            <span class="display-value">{{ $convertedLead->batch ? $convertedLead->batch->title : 'N/A' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_academic_assistant() || \App\Helpers\RoleHelper::is_hod() || \App\Helpers\RoleHelper::is_mentor())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="inline-edit" data-field="admission_batch_id" data-id="{{ $convertedLead->id }}" data-batch-id="{{ $convertedLead->batch_id }}" data-current-id="{{ $convertedLead->admission_batch_id }}">
                                            <span class="display-value">{{ $convertedLead->admissionBatch ? $convertedLead->admissionBatch->title : 'N/A' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_academic_assistant() || \App\Helpers\RoleHelper::is_hod() || \App\Helpers\RoleHelper::is_mentor())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="inline-edit" data-field="internship_id" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->studentDetails?->internship_id }}">
                                            <span class="display-value">{{ $convertedLead->studentDetails?->internship_id ?? 'N/A' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_academic_assistant() || \App\Helpers\RoleHelper::is_hod() || \App\Helpers\RoleHelper::is_mentor())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="inline-edit" data-field="email" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->email }}">
                                            <span class="display-value">{{ $convertedLead->email ?? '-' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_academic_assistant() || \App\Helpers\RoleHelper::is_hod() || \App\Helpers\RoleHelper::is_mentor())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="inline-edit" data-field="call_status" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->studentDetails?->call_status }}">
                                            <span class="display-value">{{ $convertedLead->studentDetails?->call_status ?? '-' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_academic_assistant() || \App\Helpers\RoleHelper::is_hod() || \App\Helpers\RoleHelper::is_mentor())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="inline-edit" data-field="orientation_class_date" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->mentorDetails?->orientation_class_date ? \Carbon\Carbon::parse($convertedLead->mentorDetails->orientation_class_date)->format('d-m-Y') : '' }}">
                                            <span class="display-value">{{ $convertedLead->mentorDetails?->orientation_class_date ? \Carbon\Carbon::parse($convertedLead->mentorDetails->orientation_class_date)->format('d-m-Y') : '-' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_academic_assistant() || \App\Helpers\RoleHelper::is_hod() || \App\Helpers\RoleHelper::is_mentor())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="inline-edit" data-field="class_start_date" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->mentorDetails?->class_start_date ? \Carbon\Carbon::parse($convertedLead->mentorDetails->class_start_date)->format('d-m-Y') : '' }}">
                                            <span class="display-value">{{ $convertedLead->mentorDetails?->class_start_date ? \Carbon\Carbon::parse($convertedLead->mentorDetails->class_start_date)->format('d-m-Y') : '-' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_academic_assistant() || \App\Helpers\RoleHelper::is_hod() || \App\Helpers\RoleHelper::is_mentor())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="inline-edit" data-field="class_end_date" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->mentorDetails?->class_end_date ? \Carbon\Carbon::parse($convertedLead->mentorDetails->class_end_date)->format('d-m-Y') : '' }}">
                                            <span class="display-value">{{ $convertedLead->mentorDetails?->class_end_date ? \Carbon\Carbon::parse($convertedLead->mentorDetails->class_end_date)->format('d-m-Y') : '-' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_academic_assistant() || \App\Helpers\RoleHelper::is_hod() || \App\Helpers\RoleHelper::is_mentor())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="inline-edit" data-field="whatsapp_group_status" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->mentorDetails?->whatsapp_group_status }}">
                                            <span class="display-value">{{ $convertedLead->mentorDetails?->whatsapp_group_status ?? '-' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_academic_assistant() || \App\Helpers\RoleHelper::is_hod() || \App\Helpers\RoleHelper::is_mentor())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="inline-edit" data-field="programme_type" data-id="{{ $convertedLead->id }}" data-field-type="select" data-options='{"online":"Online","offline":"Offline"}' data-current="{{ $convertedLead->leadDetail?->programme_type }}">
                                            <span class="display-value">{{ $convertedLead->leadDetail?->programme_type ? ucfirst($convertedLead->leadDetail->programme_type) : '-' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_academic_assistant() || \App\Helpers\RoleHelper::is_hod() || \App\Helpers\RoleHelper::is_mentor())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        @if($course && $course->needs_time)
                                        <div class="inline-edit" data-field="class_time_id" data-id="{{ $convertedLead->id }}" data-course-id="{{ $convertedLead->course_id }}" data-programme-type="{{ $convertedLead->leadDetail?->programme_type }}" data-current-id="{{ $convertedLead->leadDetail?->class_time_id }}">
                                            <span class="display-value">
                                                @if($convertedLead->leadDetail?->classTime)
                                                @php
                                                $fromTime = \Carbon\Carbon::parse($convertedLead->leadDetail->classTime->from_time)->format('h:i A');
                                                $toTime = \Carbon\Carbon::parse($convertedLead->leadDetail->classTime->to_time)->format('h:i A');
                                                @endphp
                                                {{ $fromTime }} - {{ $toTime }}
                                                @else
                                                -
                                                @endif
                                            </span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_academic_assistant() || \App\Helpers\RoleHelper::is_hod() || \App\Helpers\RoleHelper::is_mentor())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                        @else
                                        <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="inline-edit" data-field="location" data-id="{{ $convertedLead->id }}" data-field-type="select" data-options='{!! json_encode($offlinePlaces->pluck(' name', 'name' )->toArray()) !!}' data-current="{{ $convertedLead->leadDetail?->location }}">
                                            <span class="display-value">{{ $convertedLead->leadDetail?->location ?? '-' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_academic_assistant() || \App\Helpers\RoleHelper::is_hod() || \App\Helpers\RoleHelper::is_mentor())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="inline-edit" data-field="first_month_exam_date" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->mentorDetails?->first_month_exam_date ? \Carbon\Carbon::parse($convertedLead->mentorDetails->first_month_exam_date)->format('d-m-Y') : '' }}">
                                            <span class="display-value">{{ $convertedLead->mentorDetails?->first_month_exam_date ? \Carbon\Carbon::parse($convertedLead->mentorDetails->first_month_exam_date)->format('d-m-Y') : '-' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_academic_assistant() || \App\Helpers\RoleHelper::is_hod() || \App\Helpers\RoleHelper::is_mentor())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="inline-edit" data-field="first_month_marks" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->mentorDetails?->first_month_marks }}">
                                            <span class="display-value">{{ $convertedLead->mentorDetails?->first_month_marks ?? '-' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_academic_assistant() || \App\Helpers\RoleHelper::is_hod() || \App\Helpers\RoleHelper::is_mentor())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="inline-edit" data-field="first_month_feedback" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->mentorDetails?->first_month_feedback }}">
                                            <span class="display-value">{{ $convertedLead->mentorDetails?->first_month_feedback ?? '-' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_academic_assistant() || \App\Helpers\RoleHelper::is_hod() || \App\Helpers\RoleHelper::is_mentor())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="inline-edit" data-field="second_month_exam_date" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->mentorDetails?->second_month_exam_date ? \Carbon\Carbon::parse($convertedLead->mentorDetails->second_month_exam_date)->format('d-m-Y') : '' }}">
                                            <span class="display-value">{{ $convertedLead->mentorDetails?->second_month_exam_date ? \Carbon\Carbon::parse($convertedLead->mentorDetails->second_month_exam_date)->format('d-m-Y') : '-' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_academic_assistant() || \App\Helpers\RoleHelper::is_hod() || \App\Helpers\RoleHelper::is_mentor())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="inline-edit" data-field="second_month_marks" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->mentorDetails?->second_month_marks }}">
                                            <span class="display-value">{{ $convertedLead->mentorDetails?->second_month_marks ?? '-' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_academic_assistant() || \App\Helpers\RoleHelper::is_hod() || \App\Helpers\RoleHelper::is_mentor())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="inline-edit" data-field="second_month_feedback" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->mentorDetails?->second_month_feedback }}">
                                            <span class="display-value">{{ $convertedLead->mentorDetails?->second_month_feedback ?? '-' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_academic_assistant() || \App\Helpers\RoleHelper::is_hod() || \App\Helpers\RoleHelper::is_mentor())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="inline-edit" data-field="ai_workshop_attendance" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->mentorDetails?->ai_workshop_attendance }}">
                                            <span class="display-value">{{ $convertedLead->mentorDetails?->ai_workshop_attendance ?? '-' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_academic_assistant() || \App\Helpers\RoleHelper::is_hod() || \App\Helpers\RoleHelper::is_mentor())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="inline-edit" data-field="third_month_exam_date" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->mentorDetails?->third_month_exam_date ? \Carbon\Carbon::parse($convertedLead->mentorDetails->third_month_exam_date)->format('d-m-Y') : '' }}">
                                            <span class="display-value">{{ $convertedLead->mentorDetails?->third_month_exam_date ? \Carbon\Carbon::parse($convertedLead->mentorDetails->third_month_exam_date)->format('d-m-Y') : '-' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_academic_assistant() || \App\Helpers\RoleHelper::is_hod() || \App\Helpers\RoleHelper::is_mentor())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="inline-edit" data-field="third_month_marks" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->mentorDetails?->third_month_marks }}">
                                            <span class="display-value">{{ $convertedLead->mentorDetails?->third_month_marks ?? '-' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_academic_assistant() || \App\Helpers\RoleHelper::is_hod() || \App\Helpers\RoleHelper::is_mentor())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="inline-edit" data-field="third_month_feedback" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->mentorDetails?->third_month_feedback }}">
                                            <span class="display-value">{{ $convertedLead->mentorDetails?->third_month_feedback ?? '-' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_academic_assistant() || \App\Helpers\RoleHelper::is_hod() || \App\Helpers\RoleHelper::is_mentor())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="inline-edit" data-field="initial_project_start_date" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->mentorDetails?->initial_project_start_date ? \Carbon\Carbon::parse($convertedLead->mentorDetails->initial_project_start_date)->format('d-m-Y') : '' }}">
                                            <span class="display-value">{{ $convertedLead->mentorDetails?->initial_project_start_date ? \Carbon\Carbon::parse($convertedLead->mentorDetails->initial_project_start_date)->format('d-m-Y') : '-' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_academic_assistant() || \App\Helpers\RoleHelper::is_hod() || \App\Helpers\RoleHelper::is_mentor())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="inline-edit" data-field="initial_project_end_date" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->mentorDetails?->initial_project_end_date ? \Carbon\Carbon::parse($convertedLead->mentorDetails->initial_project_end_date)->format('d-m-Y') : '' }}">
                                            <span class="display-value">{{ $convertedLead->mentorDetails?->initial_project_end_date ? \Carbon\Carbon::parse($convertedLead->mentorDetails->initial_project_end_date)->format('d-m-Y') : '-' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_academic_assistant() || \App\Helpers\RoleHelper::is_hod() || \App\Helpers\RoleHelper::is_mentor())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="inline-edit" data-field="initial_project_marks" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->mentorDetails?->initial_project_marks }}">
                                            <span class="display-value">{{ $convertedLead->mentorDetails?->initial_project_marks ?? '-' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_academic_assistant() || \App\Helpers\RoleHelper::is_hod() || \App\Helpers\RoleHelper::is_mentor())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="inline-edit" data-field="initial_project_feedback" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->mentorDetails?->initial_project_feedback }}">
                                            <span class="display-value">{{ $convertedLead->mentorDetails?->initial_project_feedback ?? '-' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_academic_assistant() || \App\Helpers\RoleHelper::is_hod() || \App\Helpers\RoleHelper::is_mentor())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="inline-edit" data-field="mock_test_date" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->mentorDetails?->mock_test_date ? \Carbon\Carbon::parse($convertedLead->mentorDetails->mock_test_date)->format('d-m-Y') : '' }}">
                                            <span class="display-value">{{ $convertedLead->mentorDetails?->mock_test_date ? \Carbon\Carbon::parse($convertedLead->mentorDetails->mock_test_date)->format('d-m-Y') : '-' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_academic_assistant() || \App\Helpers\RoleHelper::is_hod() || \App\Helpers\RoleHelper::is_mentor())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="inline-edit" data-field="mock_test_marks" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->mentorDetails?->mock_test_marks }}">
                                            <span class="display-value">{{ $convertedLead->mentorDetails?->mock_test_marks ?? '-' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_academic_assistant() || \App\Helpers\RoleHelper::is_hod() || \App\Helpers\RoleHelper::is_mentor())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="inline-edit" data-field="mock_test_feedback" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->mentorDetails?->mock_test_feedback }}">
                                            <span class="display-value">{{ $convertedLead->mentorDetails?->mock_test_feedback ?? '-' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_academic_assistant() || \App\Helpers\RoleHelper::is_hod() || \App\Helpers\RoleHelper::is_mentor())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="inline-edit" data-field="mock_interview_date" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->mentorDetails?->mock_interview_date ? \Carbon\Carbon::parse($convertedLead->mentorDetails->mock_interview_date)->format('d-m-Y') : '' }}">
                                            <span class="display-value">{{ $convertedLead->mentorDetails?->mock_interview_date ? \Carbon\Carbon::parse($convertedLead->mentorDetails->mock_interview_date)->format('d-m-Y') : '-' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_academic_assistant() || \App\Helpers\RoleHelper::is_hod() || \App\Helpers\RoleHelper::is_mentor())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="inline-edit" data-field="mock_interview_marks" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->mentorDetails?->mock_interview_marks }}">
                                            <span class="display-value">{{ $convertedLead->mentorDetails?->mock_interview_marks ?? '-' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_academic_assistant() || \App\Helpers\RoleHelper::is_hod() || \App\Helpers\RoleHelper::is_mentor())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="inline-edit" data-field="mock_interview_feedback" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->mentorDetails?->mock_interview_feedback }}">
                                            <span class="display-value">{{ $convertedLead->mentorDetails?->mock_interview_feedback ?? '-' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_academic_assistant() || \App\Helpers\RoleHelper::is_hod() || \App\Helpers\RoleHelper::is_mentor())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="inline-edit" data-field="final_project_start_date" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->mentorDetails?->final_project_start_date ? \Carbon\Carbon::parse($convertedLead->mentorDetails->final_project_start_date)->format('d-m-Y') : '' }}">
                                            <span class="display-value">{{ $convertedLead->mentorDetails?->final_project_start_date ? \Carbon\Carbon::parse($convertedLead->mentorDetails->final_project_start_date)->format('d-m-Y') : '-' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_academic_assistant() || \App\Helpers\RoleHelper::is_hod() || \App\Helpers\RoleHelper::is_mentor())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="inline-edit" data-field="final_project_end_date" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->mentorDetails?->final_project_end_date ? \Carbon\Carbon::parse($convertedLead->mentorDetails->final_project_end_date)->format('d-m-Y') : '' }}">
                                            <span class="display-value">{{ $convertedLead->mentorDetails?->final_project_end_date ? \Carbon\Carbon::parse($convertedLead->mentorDetails->final_project_end_date)->format('d-m-Y') : '-' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_academic_assistant() || \App\Helpers\RoleHelper::is_hod() || \App\Helpers\RoleHelper::is_mentor())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="inline-edit" data-field="final_project_marks" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->mentorDetails?->final_project_marks }}">
                                            <span class="display-value">{{ $convertedLead->mentorDetails?->final_project_marks ?? '-' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_academic_assistant() || \App\Helpers\RoleHelper::is_hod() || \App\Helpers\RoleHelper::is_mentor())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="inline-edit" data-field="final_project_feedback" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->mentorDetails?->final_project_feedback }}">
                                            <span class="display-value">{{ $convertedLead->mentorDetails?->final_project_feedback ?? '-' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_academic_assistant() || \App\Helpers\RoleHelper::is_hod() || \App\Helpers\RoleHelper::is_mentor())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="inline-edit" data-field="total_class" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->mentorDetails?->total_class }}">
                                            <span class="display-value">{{ $convertedLead->mentorDetails?->total_class ?? '-' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_academic_assistant() || \App\Helpers\RoleHelper::is_hod() || \App\Helpers\RoleHelper::is_mentor())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="inline-edit" data-field="total_present" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->mentorDetails?->total_present }}">
                                            <span class="display-value">{{ $convertedLead->mentorDetails?->total_present ?? '-' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_academic_assistant() || \App\Helpers\RoleHelper::is_hod() || \App\Helpers\RoleHelper::is_mentor())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="inline-edit" data-field="total_absent" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->mentorDetails?->total_absent }}">
                                            <span class="display-value">{{ $convertedLead->mentorDetails?->total_absent ?? '-' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_academic_assistant() || \App\Helpers\RoleHelper::is_hod() || \App\Helpers\RoleHelper::is_mentor())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="inline-edit" data-field="final_certificate_examination_date" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->mentorDetails?->final_certificate_examination_date ? \Carbon\Carbon::parse($convertedLead->mentorDetails->final_certificate_examination_date)->format('d-m-Y') : '' }}">
                                            <span class="display-value">{{ $convertedLead->mentorDetails?->final_certificate_examination_date ? \Carbon\Carbon::parse($convertedLead->mentorDetails->final_certificate_examination_date)->format('d-m-Y') : '-' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_academic_assistant() || \App\Helpers\RoleHelper::is_hod() || \App\Helpers\RoleHelper::is_mentor())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="inline-edit" data-field="certificate_examination_marks" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->mentorDetails?->certificate_examination_marks }}">
                                            <span class="display-value">{{ $convertedLead->mentorDetails?->certificate_examination_marks ?? '-' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_academic_assistant() || \App\Helpers\RoleHelper::is_hod() || \App\Helpers\RoleHelper::is_mentor())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="inline-edit" data-field="final_interview_date" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->mentorDetails?->final_interview_date ? \Carbon\Carbon::parse($convertedLead->mentorDetails->final_interview_date)->format('d-m-Y') : '' }}">
                                            <span class="display-value">{{ $convertedLead->mentorDetails?->final_interview_date ? \Carbon\Carbon::parse($convertedLead->mentorDetails->final_interview_date)->format('d-m-Y') : '-' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_academic_assistant() || \App\Helpers\RoleHelper::is_hod() || \App\Helpers\RoleHelper::is_mentor())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="inline-edit" data-field="interview_marks" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->mentorDetails?->interview_marks }}">
                                            <span class="display-value">{{ $convertedLead->mentorDetails?->interview_marks ?? '-' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_academic_assistant() || \App\Helpers\RoleHelper::is_hod() || \App\Helpers\RoleHelper::is_mentor())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="inline-edit" data-field="certificate_distribution_date" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->mentorDetails?->certificate_distribution_date ? \Carbon\Carbon::parse($convertedLead->mentorDetails->certificate_distribution_date)->format('d-m-Y') : '' }}">
                                            <span class="display-value">{{ $convertedLead->mentorDetails?->certificate_distribution_date ? \Carbon\Carbon::parse($convertedLead->mentorDetails->certificate_distribution_date)->format('d-m-Y') : '-' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_academic_assistant() || \App\Helpers\RoleHelper::is_hod() || \App\Helpers\RoleHelper::is_mentor())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="inline-edit" data-field="experience_certificate_distribution_date" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->mentorDetails?->experience_certificate_distribution_date ? \Carbon\Carbon::parse($convertedLead->mentorDetails->experience_certificate_distribution_date)->format('d-m-Y') : '' }}">
                                            <span class="display-value">{{ $convertedLead->mentorDetails?->experience_certificate_distribution_date ? \Carbon\Carbon::parse($convertedLead->mentorDetails->experience_certificate_distribution_date)->format('d-m-Y') : '-' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_academic_assistant() || \App\Helpers\RoleHelper::is_hod() || \App\Helpers\RoleHelper::is_mentor())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="inline-edit" data-field="cancelled_date" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->mentorDetails?->cancelled_date ? \Carbon\Carbon::parse($convertedLead->mentorDetails->cancelled_date)->format('d-m-Y') : '' }}">
                                            <span class="display-value">{{ $convertedLead->mentorDetails?->cancelled_date ? \Carbon\Carbon::parse($convertedLead->mentorDetails->cancelled_date)->format('d-m-Y') : '-' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_academic_assistant() || \App\Helpers\RoleHelper::is_hod() || \App\Helpers\RoleHelper::is_mentor())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="inline-edit" data-field="remarks" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->mentorDetails?->remarks }}">
                                            <span class="display-value">{{ $convertedLead->mentorDetails?->remarks ?? '-' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_academic_assistant() || \App\Helpers\RoleHelper::is_hod() || \App\Helpers\RoleHelper::is_mentor())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        @if($convertedLead->mentorDetails?->is_placement_passed)
                                            <span class="badge bg-success">Placement Passed</span>
                                            @if($convertedLead->mentorDetails?->is_placement_passed_at)
                                                <br><small class="text-muted">{{ $convertedLead->mentorDetails->is_placement_passed_at->format('d-m-Y h:i A') }}</small>
                                            @endif
                                            @if($convertedLead->mentorDetails?->placement_resume)
                                                <br><a href="{{ asset('storage/' . $convertedLead->mentorDetails->placement_resume) }}" target="_blank" class="btn btn-sm btn-link p-0 small"><i class="ti ti-file-text"></i> View Resume</a>
                                                @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor())
                                                    <br><a href="javascript:void(0);" class="btn btn-sm {{ $convertedLead->mentorDetails->is_resume_verified ? 'btn-success' : 'btn-outline-success' }} px-2 py-0"
                                                        onclick="show_small_modal('{{ route('admin.converted-leads.verify-resume-modal', $convertedLead->id) }}', 'Resume Verification')"
                                                        title="Resume Verification">
                                                        <i class="ti ti-circle-check"></i> {{ $convertedLead->mentorDetails->is_resume_verified ? 'Resume Verified' : 'Verify Resume' }}@if($convertedLead->mentorDetails->is_resume_verified && $convertedLead->mentorDetails->resume_verified_at) ({{ $convertedLead->mentorDetails->resume_verified_at->format('d M Y') }})@endif
                                                    </a>
                                                    <br><a href="javascript:void(0);" class="btn btn-sm btn-outline-primary px-2 py-0"
                                                        onclick="show_small_modal('{{ route('admin.converted-leads.move-to-placement', $convertedLead->id) }}', 'Change Resume')"
                                                        title="Change Resume">
                                                        <i class="ti ti-upload"></i> Change Resume
                                                    </a>
                                                @endif
                                            @endif
                                        @else
                                            <a href="javascript:void(0);" class="btn btn-outline-primary btn-sm px-2"
                                                onclick="show_small_modal('{{ route('admin.converted-leads.move-to-placement', $convertedLead->id) }}', 'Move to Placement')"
                                                title="Move to Placement">
                                                <i class="ti ti-user-check"></i> Move to Placement
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="58" class="text-center">No Data Science Course mentor records found</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Mobile Card View -->
                <div class="d-lg-none">
                    @forelse($convertedLeads as $index => $convertedLead)
                    <div class="card mb-3 {{ $convertedLead->is_cancelled ? 'cancelled-card' : '' }}">
                        <div class="card-body">
                            @php
                            $canToggleAcademic = \App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_academic_assistant() || \App\Helpers\RoleHelper::is_admission_counsellor();
                            $canToggleSupport = \App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_support_team();
                            @endphp
                            <!-- Lead Header -->
                            <div class="d-flex align-items-center mb-3">
                                <div class="avtar avtar-s rounded-circle bg-light-primary me-3 d-flex align-items-center justify-content-center">
                                    <span class="f-16 fw-bold text-primary">{{ strtoupper(substr($convertedLead->name, 0, 1)) }}</span>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-1 fw-bold">{{ $convertedLead->name }}</h6>
                                    <small class="text-muted">ID: {{ $convertedLead->lead_id }}</small>
                                    @if($convertedLead->is_cancelled)
                                    <span class="badge bg-danger ms-2">Cancelled</span>
                                    @endif
                                </div>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                        <i class="ti ti-dots-vertical"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li>
                                            <a class="dropdown-item" href="{{ route('admin.converted-leads.show', $convertedLead->id) }}">
                                                <i class="ti ti-eye me-2"></i>View Details
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item" href="{{ route('admin.invoices.index', $convertedLead->id) }}">
                                                <i class="ti ti-receipt me-2"></i>View Invoice
                                            </a>
                                        </li>
                                        <li>
                                        </li>
                                    </ul>
                                </div>
                            </div>

                            <!-- Lead Details -->
                            <div class="row g-2 mb-3">
                                <div class="col-6">
                                    <small class="text-muted d-block">Support</small>
                                    @include('admin.converted-leads.partials.status-badge', [
                                    'convertedLead' => $convertedLead,
                                    'type' => 'support',
                                    'showToggle' => $canToggleSupport,
                                    'toggleUrl' => $canToggleSupport ? route('admin.support-converted-leads.toggle-support-verify', $convertedLead->id) : null
                                    ])
                                </div>
                                <div class="col-6">
                                    <small class="text-muted d-block">Converted Date</small>
                                    <span class="fw-medium">{{ $convertedLead->created_at->format('d-m-Y') }}</span>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted d-block">Register Number</small>
                                    <span class="fw-medium">{{ $convertedLead->register_number ?? 'N/A' }}</span>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted d-block">Name</small>
                                    <span class="fw-medium">{{ $convertedLead->name }}</span>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted d-block">Phone</small>
                                    <span class="fw-medium">{{ \App\Helpers\PhoneNumberHelper::display($convertedLead->code, $convertedLead->phone) }}</span>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted d-block">WhatsApp</small>
                                    <span class="fw-medium">
                                        @if($convertedLead->leadDetail && $convertedLead->leadDetail->whatsapp_number)
                                            {{ \App\Helpers\PhoneNumberHelper::display($convertedLead->leadDetail->whatsapp_code, $convertedLead->leadDetail->whatsapp_number) }}
                                        @else
                                            N/A
                                        @endif
                                    </span>
                                </div>
                                @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor())
                                <div class="col-6">
                                    <small class="text-muted d-block">Parent Phone</small>
                                    <span class="fw-medium">
                                        @if($convertedLead->leadDetail && $convertedLead->leadDetail->parents_number)
                                            {{ \App\Helpers\PhoneNumberHelper::display($convertedLead->leadDetail->parents_code, $convertedLead->leadDetail->parents_number) }}
                                        @else
                                            N/A
                                        @endif
                                    </span>
                                </div>
                                @endif
                                <div class="col-6">
                                    <small class="text-muted d-block">Course Type</small>
                                    <span class="fw-medium">{{ $convertedLead->leadDetail?->programme_type ? ucfirst($convertedLead->leadDetail->programme_type) : 'N/A' }}</span>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted d-block">Location</small>
                                    <span class="fw-medium">
                                        @if($convertedLead->leadDetail?->programme_type === 'offline')
                                            {{ $convertedLead->leadDetail?->location ?? 'N/A' }}
                                        @else
                                            -
                                        @endif
                                    </span>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted d-block">Class Time</small>
                                    <span class="fw-medium">
                                        @if($course && $course->needs_time && $convertedLead->leadDetail?->classTime)
                                            @php
                                            $fromTime = \Carbon\Carbon::parse($convertedLead->leadDetail->classTime->from_time)->format('h:i A');
                                            $toTime = \Carbon\Carbon::parse($convertedLead->leadDetail->classTime->to_time)->format('h:i A');
                                            @endphp
                                            {{ $fromTime }} - {{ $toTime }}
                                        @else
                                            -
                                        @endif
                                    </span>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted d-block">Batch</small>
                                    <span class="fw-medium">{{ $convertedLead->batch ? $convertedLead->batch->title : 'N/A' }}</span>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted d-block">Admission Batch</small>
                                    <span class="fw-medium">{{ $convertedLead->admissionBatch ? $convertedLead->admissionBatch->title : 'N/A' }}</span>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted d-block">Internship ID</small>
                                    <span class="fw-medium">{{ $convertedLead->studentDetails?->internship_id ?? 'N/A' }}</span>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted d-block">Email</small>
                                    <span class="fw-medium">{{ $convertedLead->email ?? 'N/A' }}</span>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted d-block">Call Status</small>
                                    <span class="fw-medium">{{ $convertedLead->studentDetails?->call_status ?? 'N/A' }}</span>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted d-block">Class Status</small>
                                    <span class="fw-medium">{{ $convertedLead->studentDetails?->class_status ?? 'N/A' }}</span>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted d-block">Academic</small>
                                    @include('admin.converted-leads.partials.status-badge', [
                                    'convertedLead' => $convertedLead,
                                    'type' => 'academic',
                                    'showToggle' => $canToggleAcademic,
                                    'toggleUrl' => $canToggleAcademic ? route('admin.converted-leads.toggle-academic-verify', $convertedLead->id) : null
                                    ])
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="d-flex gap-2 flex-wrap">
                                <a href="{{ route('admin.converted-leads.show', $convertedLead->id) }}"
                                    class="btn btn-sm btn-primary">
                                    <i class="ti ti-eye me-1"></i>View Details
                                </a>
                                <a href="{{ route('admin.invoices.index', $convertedLead->id) }}"
                                    class="btn btn-sm btn-success">
                                    <i class="ti ti-receipt me-1"></i>View Invoice
                                </a>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-5">
                        <div class="text-muted">
                            <i class="ti ti-marketing f-48 mb-3 d-block"></i>
                            <h5>No Data Science Course mentor records found</h5>
                            <p>Try adjusting your filters or check back later.</p>
                        </div>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
<!-- [ Main Content ] end -->

@endsection

<script id="country-codes-json" type="application/json">
    {
        !!json_encode($country_codes ?? [], JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) !!
    }
</script>

@push('styles')
<style>
    .spin {
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        0% {
            transform: rotate(0deg);
        }

        100% {
            transform: rotate(360deg);
        }
    }

    .inline-edit {
        position: relative;
        overflow: visible;
    }

    .inline-edit .edit-form {
        display: none;
        position: absolute;
        top: 0;
        left: -8px;
        z-index: 10;
        background: white;
        border: 1px solid #ddd;
        border-radius: 4px;
        padding: 10px;
        min-width: 320px;
        max-width: 440px;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
    }

    .inline-edit.editing .edit-form {
        display: block;
    }

    .inline-edit.editing .display-value {
        display: none;
    }

    .inline-edit .edit-form input,
    .inline-edit .edit-form select {
        width: 100%;
        padding: 4px 8px;
        border: 1px solid #ccc;
        border-radius: 3px;
        font-size: 12px;
    }

    .inline-edit .edit-form input:focus,
    .inline-edit .edit-form select:focus {
        border-color: #7366ff;
        outline: none;
        box-shadow: 0 0 0 2px rgba(115, 102, 255, 0.15);
    }

    .inline-edit .edit-form .btn-group {
        margin-top: 5px;
    }

    .inline-edit .edit-form .btn {
        padding: 2px 8px;
        font-size: 11px;
    }

    #dataScienceMentorTable thead th,
    #dataScienceMentorTable tbody td {
        white-space: nowrap;
    }

    #dataScienceMentorTable thead th {
        position: sticky;
        top: 0;
        z-index: 5;
        background: #fff;
        box-shadow: inset 0 -1px 0 #e9ecef;
    }

    #dataScienceMentorTable tbody tr:hover {
        background: #fafbff;
    }

    #dataScienceMentorTable td .display-value {
        display: inline-block;
        max-width: 220px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        vertical-align: middle;
    }

    #dataScienceMentorTable .btn-group .btn {
        margin-right: 4px;
    }

    #dataScienceMentorTable .btn-group .btn:last-child {
        margin-right: 0;
    }
</style>
@endpush

@push('scripts')
@include('admin.converted-leads.partials.placement-modal-reopen-script')
<script>
    $(document).ready(function() {
        const canEditRestricted = document.getElementById('can-edit-restricted-flag')?.dataset.value === 'true';
        const restrictedFields = [
            'phone',
            'batch_id',
            'admission_batch_id',
            'internship_id',
            'email',
            'call_status',
            'orientation_class_date',
            'class_start_date',
            'class_end_date',
            'whatsapp_group_status',
            'class_time_id',
            'programme_type',
            'location',
            'total_class',
            'total_present',
            'total_absent',
            'final_certificate_examination_date',
            'certificate_examination_marks',
            'final_interview_date',
            'interview_marks',
            'certificate_distribution_date',
            'experience_certificate_distribution_date',
            'completed_cancelled_date',
            'remarks',
        ];

        // Remove edit buttons for restricted fields when user is not permitted
        if (!canEditRestricted) {
            $('.inline-edit').each(function() {
                const field = $(this).data('field');
                if (restrictedFields.includes(field)) {
                    $(this).find('.edit-btn').remove();
                }
            });
        }

        // DataTable is automatically initialized by layout for tables with 'data_table_basic' class

        // Dependent filters: load admission batches by batch
        function loadAdmissionBatchesByBatch(batchId, selectedId) {
            const $admission = $('#admission_batch_id');
            $admission.html('<option value="">Loading...</option>');
            if (!batchId) {
                $admission.html('<option value="">All Admission Batches</option>');
                return;
            }
            $.get(`/api/admission-batches/by-batch/${batchId}`).done(function(list) {
                let opts = '<option value="">All Admission Batches</option>';
                list.forEach(function(i) {
                    const sel = String(selectedId) === String(i.id) ? 'selected' : '';
                    opts += `<option value="${i.id}" ${sel}>${i.title}</option>`;
                });
                $admission.html(opts);
            }).fail(function() {
                $admission.html('<option value="">All Admission Batches</option>');
            });
        }

        // Initialize dependent dropdowns on load
        const initialBatchId = $('#batch_id').val();
        const initialAdmissionBatchId = $('#admission_batch_id').data('selected');
        if (initialBatchId) {
            loadAdmissionBatchesByBatch(initialBatchId, initialAdmissionBatchId);
        }

        // On batch change â†’ reload admission batches and optionally submit form
        $('#batch_id').on('change', function() {
            const bid = $(this).val();
            loadAdmissionBatchesByBatch(bid, '');
            // Auto-submit form when batch is changed for better UX
            setTimeout(() => {
                $('#filterForm').submit();
            }, 100);
        });

        // Inline editing functionality
        $(document).on('click', '.edit-btn', function(e) {
            e.preventDefault();
            e.stopPropagation();

            const container = $(this).closest('.inline-edit');
            const field = container.data('field');
            if (restrictedFields.includes(field) && !canEditRestricted) {
                toast_error('You do not have permission to edit this field');
                return;
            }
            const id = container.data('id');
            const currentValue = container.data('current') !== undefined ? String(container.data('current')).trim() : container.find('.display-value').text().trim();

            if (container.hasClass('editing')) {
                return;
            }

            $('.inline-edit.editing').not(container).each(function() {
                $(this).removeClass('editing');
                $(this).find('.edit-form').remove();
            });

            let editForm = '';

            if (field === 'phone') {
                const currentCode = container.siblings('.inline-code-value').data('current') || '';
                editForm = createPhoneField(currentCode, currentValue);
            } else if (container.data('field-type') === 'select') {
                // Handle fields with data-field-type="select" using data-options
                const options = container.data('options');
                editForm = createSelectFieldFromOptions(field, currentValue, options);
            } else if (['call_status', 'class_information', 'orientation_class_status', 'whatsapp_group_status', 'class_status', 'programme_type', 'whatsapp_group_status', 'ai_workshop_attendance'].includes(field)) {
                editForm = createSelectField(field, currentValue);
            } else if (['class_starting_date', 'class_ending_date', 'complete_cancel_date', 'orientation_class_date', 'class_start_date', 'class_end_date', 'first_month_exam_date', 'second_month_exam_date', 'third_month_exam_date', 'initial_project_start_date', 'initial_project_end_date', 'mock_test_date', 'mock_interview_date', 'final_project_start_date', 'final_project_end_date', 'final_certificate_examination_date', 'final_interview_date', 'certificate_distribution_date', 'experience_certificate_distribution_date', 'cancelled_date'].includes(field)) {
                editForm = createDateField(field, currentValue);
            } else if (field === 'class_time') {
                editForm = createTimeField(field, currentValue);
            } else if (field === 'batch_id') {
                const courseId = container.data('course-id');
                const currentId = container.data('current-id');
                editForm = createBatchSelect(courseId, currentId);
            } else if (field === 'admission_batch_id') {
                const batchId = container.data('batch-id');
                const currentId = container.data('current-id');
                editForm = createAdmissionBatchField(batchId, currentId);
            } else if (field === 'class_time_id') {
                const courseId = container.data('course-id');
                const programmeType = container.data('programme-type');
                const currentId = container.data('current-id');
                editForm = createClassTimeSelect(courseId, programmeType, currentId);
            } else {
                editForm = createInputField(field, currentValue);
            }

            container.addClass('editing');
            container.append(editForm);

            // Load options for select fields that need dynamic loading
            if (field === 'batch_id') {
                const courseId = container.data('course-id');
                const currentId = container.data('current-id');
                const $select = container.find('select');
                loadBatchesForEdit($select, courseId, currentId);
            } else if (field === 'admission_batch_id') {
                const batchId = container.data('batch-id');
                const currentId = container.data('current-id');
                const $select = container.find('select');
                loadAdmissionBatchesForEdit($select, batchId, currentId);
            } else if (field === 'class_time_id') {
                const courseId = container.data('course-id');
                const programmeType = container.data('programme-type');
                const currentId = container.data('current-id');
                const $select = container.find('select');
                loadClassTimesForEdit($select, courseId, programmeType, currentId);
            } else {
                container.find('input, select').first().focus();
            }
        });

        // Save inline edit
        $(document).off('click.saveInline').on('click.saveInline', '.save-edit', function(e) {
            e.preventDefault();
            e.stopPropagation();

            const container = $(this).closest('.inline-edit');
            const field = container.data('field');
            if (restrictedFields.includes(field) && !canEditRestricted) {
                toast_error('You do not have permission to edit this field');
                return;
            }
            const id = container.data('id');
            let value;
            if (field === 'phone') {
                value = container.find('input[type="text"]').val();
            } else {
                value = container.find('input, select').val();
            }
            let extra = {};
            if (field === 'phone') {
                const codeVal = container.find('select[name="code"]').val();
                extra = {
                    code: codeVal
                };
            }

            const btn = $(this);
            if (btn.data('busy')) return;
            btn.data('busy', true);
            btn.prop('disabled', true).html('<i class="ti ti-loader-2 spin"></i>');

            // Use converted-leads endpoint for register number, phone, email, batch_id, admission_batch_id, internship_id, call_status
            // Use mentor endpoint for all other mentor detail fields
            const isConvertedLeadField = ['phone', 'register_number', 'email', 'batch_id', 'admission_batch_id', 'internship_id', 'call_status', 'class_time_id', 'programme_type', 'location'].includes(field);
            const updateUrl = isConvertedLeadField ?
                `/admin/converted-leads/${id}/inline-update` :
                `/admin/data-science-mentor-converted-leads/${id}/update-mentor-details`;

            $.ajax({
                url: updateUrl,
                method: 'POST',
                data: $.extend({
                    field: field,
                    value: value,
                    _token: $('meta[name="csrf-token"]').attr('content')
                }, extra),
                success: function(response) {
                    if (response.success) {
                        // Update display value with the response value (which should be the title, not ID)
                        let displayValue = response.value || 'N/A';
                        container.find('.display-value').text(displayValue);
                        // Update the data-current attribute with the new display value
                        container.data('current', displayValue);
                        // Update data-current-id for fields that use it (store the ID, not the display value)
                        if (field === 'batch_id' || field === 'admission_batch_id' || field === 'class_time_id') {
                            container.data('current-id', value || '');
                        }
                        // If batch_id changed, update admission_batch_id field's data-batch-id
                        if (field === 'batch_id') {
                            const row = container.closest('tr');
                            const admissionBatchContainer = row.find('[data-field="admission_batch_id"]');
                            if (admissionBatchContainer.length) {
                                admissionBatchContainer.data('batch-id', value || '');
                                // Clear admission batch if batch changed
                                admissionBatchContainer.data('current-id', '');
                                admissionBatchContainer.find('.display-value').text('N/A');
                            }
                        }
                        // If programme_type changed, reload page to update location and class_time_id visibility
                        if (field === 'programme_type') {
                            setTimeout(() => {
                                location.reload();
                            }, 500);
                        }
                        if (field === 'phone') {
                            const codeVal = extra.code || '';
                            container.siblings('.inline-code-value').data('current', codeVal);
                        }
                        // Handle class_time_id display update
                        if (field === 'class_time_id' && response.value) {
                            // The response.value should contain the formatted time string
                            displayValue = response.value;
                        }
                        toast_success(response.message);
                    } else {
                        toast_error(response.error || 'Update failed');
                    }
                },
                error: function(xhr) {
                    let errorMessage = 'Update failed';
                    if (xhr.responseJSON) {
                        if (xhr.responseJSON.error) {
                            errorMessage = xhr.responseJSON.error;
                        } else if (xhr.responseJSON.errors) {
                            // Handle validation errors
                            const errors = xhr.responseJSON.errors;
                            const fieldErrors = Object.values(errors).flat();
                            errorMessage = fieldErrors.join(', ');
                        }
                    }
                    toast_error(errorMessage);
                },
                complete: function() {
                    btn.data('busy', false);
                    container.removeClass('editing');
                    container.find('.edit-form').remove();
                }
            });
        });

        // Cancel edit
        $(document).on('click', '.cancel-edit', function(e) {
            e.preventDefault();
            e.stopPropagation();

            const container = $(this).closest('.inline-edit');
            container.removeClass('editing');
            container.find('.edit-form').remove();
            container.find('.display-value').show();
            container.find('.edit-btn').show();
        });

        // Helper functions for creating form elements
        function createInputField(field, currentValue) {
            const displayValue = currentValue === '-' ? '' : currentValue;
            return `
                <div class="edit-form">
                    <input type="text" value="${displayValue}" class="form-control form-control-sm" autocomplete="off" autocapitalize="off" spellcheck="false">
                    <div class="btn-group mt-1">
                        <button type="button" class="btn btn-success btn-sm save-edit">Save</button>
                        <button type="button" class="btn btn-secondary btn-sm cancel-edit">Cancel</button>
                    </div>
                </div>
            `;
        }

        function createDateField(field, currentValue) {
            const value = (currentValue && currentValue !== '-') ? currentValue : '';
            // For class starting and ending dates, allow future dates
            return `
                <div class="edit-form">
                    <input type="date" value="${value}" class="form-control form-control-sm">
                    <div class="btn-group mt-1">
                        <button class="btn btn-success btn-sm save-edit">Save</button>
                        <button class="btn btn-secondary btn-sm cancel-edit">Cancel</button>
                    </div>
                </div>
            `;
        }

        function createTimeField(field, currentValue) {
            const value = (currentValue && currentValue !== '-') ? currentValue : '';
            return `
                <div class="edit-form">
                    <input type="time" value="${value}" class="form-control form-control-sm">
                    <div class="btn-group mt-1">
                        <button class="btn btn-success btn-sm save-edit">Save</button>
                        <button class="btn btn-secondary btn-sm cancel-edit">Cancel</button>
                    </div>
                </div>
            `;
        }

        function createBatchSelect(courseId, currentId) {
            return `
                <div class="edit-form">
                    <select class="form-select form-select-sm">
                        <option value="">Loading...</option>
                    </select>
                    <div class="btn-group mt-1">
                        <button type="button" class="btn btn-success btn-sm save-edit">Save</button>
                        <button type="button" class="btn btn-secondary btn-sm cancel-edit">Cancel</button>
                    </div>
                </div>
            `;
        }

        function createAdmissionBatchField(batchId, currentId) {
            return `
                <div class="edit-form">
                    <select class="form-select form-select-sm" data-batch-id="${batchId}">
                        <option value="">Loading...</option>
                    </select>
                    <div class="btn-group mt-1">
                        <button type="button" class="btn btn-success btn-sm save-edit">Save</button>
                        <button type="button" class="btn btn-secondary btn-sm cancel-edit">Cancel</button>
                    </div>
                </div>
            `;
        }

        function loadBatchesForEdit($select, courseId, currentId) {
            if (!courseId) {
                $select.html('<option value="">No course selected</option>');
                return;
            }

            $.get(`/api/batches/by-course/${courseId}`)
                .done(function(response) {
                    let options = '<option value="">Select Batch</option>';
                    if (response.success && response.batches) {
                        response.batches.forEach(function(batch) {
                            const isSelected = (currentId && String(currentId) === String(batch.id)) ? 'selected' : '';
                            options += `<option value="${batch.id}" ${isSelected}>${batch.title}</option>`;
                        });
                    }
                    $select.html(options);
                    $select.focus();
                })
                .fail(function() {
                    $select.html('<option value="">Error loading batches</option>');
                });
        }

        function loadAdmissionBatchesForEdit($select, batchId, currentId) {
            if (!batchId) {
                $select.html('<option value="">No batch selected</option>');
                return;
            }

            $.get(`/api/admission-batches/by-batch/${batchId}`)
                .done(function(list) {
                    let options = '<option value="">Select Admission Batch</option>';
                    list.forEach(function(item) {
                        const isSelected = (currentId && String(currentId) === String(item.id)) ? 'selected' : '';
                        options += `<option value="${item.id}" ${isSelected}>${item.title}</option>`;
                    });
                    $select.html(options);
                    $select.focus();
                })
                .fail(function() {
                    $select.html('<option value="">Error loading admission batches</option>');
                });
        }

        function createClassTimeSelect(courseId, programmeType, currentId) {
            return `
                <div class="edit-form">
                    <select class="form-select form-select-sm" data-course-id="${courseId}" data-programme-type="${programmeType}">
                        <option value="">Loading...</option>
                    </select>
                    <div class="btn-group mt-1">
                        <button type="button" class="btn btn-success btn-sm save-edit">Save</button>
                        <button type="button" class="btn btn-secondary btn-sm cancel-edit">Cancel</button>
                    </div>
                </div>
            `;
        }

        function loadClassTimesForEdit($select, courseId, programmeType, currentId) {
            if (!courseId || !programmeType) {
                $select.html('<option value="">No course or programme type selected</option>');
                return;
            }

            $.get(`/api/class-times/by-course/${courseId}?class_type=${programmeType}`)
                .done(function(list) {
                    let options = '<option value="">Select Class Time</option>';
                    if (list && list.length > 0) {
                        list.forEach(function(classTime) {
                            const fromTime = new Date('2000-01-01 ' + classTime.from_time).toLocaleTimeString('en-US', {
                                hour: '2-digit',
                                minute: '2-digit',
                                hour12: true
                            });
                            const toTime = new Date('2000-01-01 ' + classTime.to_time).toLocaleTimeString('en-US', {
                                hour: '2-digit',
                                minute: '2-digit',
                                hour12: true
                            });
                            const isSelected = (currentId && String(currentId) === String(classTime.id)) ? 'selected' : '';
                            options += `<option value="${classTime.id}" ${isSelected}>${fromTime} - ${toTime}</option>`;
                        });
                    }
                    $select.html(options);
                    $select.focus();
                })
                .fail(function() {
                    $select.html('<option value="">Error loading class times</option>');
                });
        }

        function createPhoneField(currentCode, currentPhone) {
            const codeOptionsEl = document.getElementById('country-codes-json');
            let codeOptions = {};
            try {
                codeOptions = codeOptionsEl ? JSON.parse(codeOptionsEl.textContent || '{}') : {};
            } catch (e) {
                codeOptions = {};
            }
            const buildOptions = (selected) => {
                let opts = '<option value="">Select Country</option>';
                for (const c in codeOptions) {
                    const isSel = String(selected) === String(c) ? 'selected' : '';
                    opts += `<option value="${c}" ${isSel}>${c} - ${codeOptions[c]}</option>`;
                }
                return opts;
            };
            const safePhone = (currentPhone && currentPhone !== 'N/A') ? currentPhone : '';
            return `
                <div class="edit-form">
                    <div class="row g-1">
                        <div class="col-5">
                            <select class="form-select form-select-sm" name="code">
                                ${buildOptions(currentCode)}
                            </select>
                        </div>
                        <div class="col-7">
                            <input type="text" value="${safePhone}" class="form-control form-control-sm" placeholder="Phone number">
                        </div>
                    </div>
                    <div class="btn-group mt-1">
                        <button type="button" class="btn btn-success btn-sm save-edit">Save</button>
                        <button type="button" class="btn btn-secondary btn-sm cancel-edit">Cancel</button>
                    </div>
                </div>
            `;
        }

        function createSelectField(field, currentValue) {
            let options = '';

            if (field === 'call_status') {
                options = `
                    <option value="">Select Call Status</option>
                    <option value="Call Not Answered" ${currentValue === 'Call Not Answered' ? 'selected' : ''}>Call Not Answered</option>
                    <option value="Switched Off" ${currentValue === 'Switched Off' ? 'selected' : ''}>Switched Off</option>
                    <option value="Line Busy" ${currentValue === 'Line Busy' ? 'selected' : ''}>Line Busy</option>
                    <option value="Student Asks to Call Later" ${currentValue === 'Student Asks to Call Later' ? 'selected' : ''}>Student Asks to Call Later</option>
                    <option value="Lack of Interest in Conversation" ${currentValue === 'Lack of Interest in Conversation' ? 'selected' : ''}>Lack of Interest in Conversation</option>
                    <option value="Wrong Contact" ${currentValue === 'Wrong Contact' ? 'selected' : ''}>Wrong Contact</option>
                    <option value="Inconsistent Responses" ${currentValue === 'Inconsistent Responses' ? 'selected' : ''}>Inconsistent Responses</option>
                    <option value="Task Complete" ${currentValue === 'Task Complete' ? 'selected' : ''}>Task Complete</option>
                    <option value="Admission cancel" ${currentValue === 'Admission cancel' ? 'selected' : ''}>Admission cancel</option>
                `;
            } else if (field === 'class_information') {
                options = `
                    <option value="">Select Class Information</option>
                    <option value="phone call" ${currentValue === 'phone call' ? 'selected' : ''}>Phone Call</option>
                    <option value="whatsapp" ${currentValue === 'whatsapp' ? 'selected' : ''}>WhatsApp</option>
                `;
            } else if (field === 'orientation_class_status') {
                options = `
                    <option value="">Select Orientation Class Status</option>
                    <option value="Participated" ${currentValue === 'Participated' ? 'selected' : ''}>Participated</option>
                    <option value="Did not participated" ${currentValue === 'Did not participated' ? 'selected' : ''}>Did not participated</option>
                `;
            } else if (field === 'whatsapp_group_status') {
                options = `
                    <option value="">Select WhatsApp Group Status</option>
                    <option value="sent link" ${currentValue === 'sent link' ? 'selected' : ''}>Sent Link</option>
                    <option value="task complete" ${currentValue === 'task complete' ? 'selected' : ''}>Task Complete</option>
                `;
            } else if (field === 'class_status') {
                options = `
                    <option value="">Select Class Status</option>
                    <option value="Running" ${currentValue === 'Running' ? 'selected' : ''}>Running</option>
                    <option value="Cancel" ${currentValue === 'Cancel' ? 'selected' : ''}>Cancel</option>
                    <option value="complete" ${currentValue === 'complete' ? 'selected' : ''}>Complete</option>
                `;
            } else if (field === 'programme_type') {
                options = `
                    <option value="">Select Course Type</option>
                    <option value="online" ${currentValue === 'online' ? 'selected' : ''}>Online</option>
                    <option value="offline" ${currentValue === 'offline' ? 'selected' : ''}>Offline</option>
                `;
            } else if (field === 'whatsapp_group_status') {
                options = `
                    <option value="">Select WhatsApp Group Status</option>
                    <option value="sent link" ${currentValue === 'sent link' ? 'selected' : ''}>Sent Link</option>
                    <option value="task complete" ${currentValue === 'task complete' ? 'selected' : ''}>Task Complete</option>
                `;
            } else if (field === 'ai_workshop_attendance') {
                options = `
                    <option value="">Select AI Workshop Attendance</option>
                    <option value="Attended" ${currentValue === 'Attended' ? 'selected' : ''}>Attended</option>
                    <option value="Not Attended" ${currentValue === 'Not Attended' ? 'selected' : ''}>Not Attended</option>
                `;
            }

            return `
                <div class="edit-form">
                    <select class="form-select form-select-sm">
                        ${options}
                    </select>
                    <div class="btn-group mt-1">
                        <button type="button" class="btn btn-success btn-sm save-edit">Save</button>
                        <button type="button" class="btn btn-secondary btn-sm cancel-edit">Cancel</button>
                    </div>
                </div>
            `;
        }

        function createSelectFieldFromOptions(field, currentValue, options) {
            let optionsHtml = '<option value="">Select ' + field.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase()) + '</option>';

            if (options && typeof options === 'object') {
                for (const [value, label] of Object.entries(options)) {
                    const selected = (currentValue && String(currentValue).toLowerCase() === String(value).toLowerCase()) ? 'selected' : '';
                    optionsHtml += `<option value="${value}" ${selected}>${label}</option>`;
                }
            }

            return `
                <div class="edit-form">
                    <select class="form-select form-select-sm">
                        ${optionsHtml}
                    </select>
                    <div class="btn-group mt-1">
                        <button type="button" class="btn btn-success btn-sm save-edit">Save</button>
                        <button type="button" class="btn btn-secondary btn-sm cancel-edit">Cancel</button>
                    </div>
                </div>
            `;
        }

        // Handle ID card generation form submission
        $(document).off('submit', '.id-card-generate-form').on('submit', '.id-card-generate-form', function(e) {
            e.preventDefault();
            e.stopImmediatePropagation();

            const $form = $(this);
            const $button = $form.find('button[type="submit"]');
            const originalText = $button.text();
            const loadingText = $button.data('loading-text') || 'Generating...';

            $button.prop('disabled', true).text(loadingText);

            $.ajax({
                url: $form.attr('action'),
                method: 'POST',
                data: $form.serialize(),
                success: function(response) {
                    if (response.success) {
                        show_alert('success', response.message || 'ID Card generated successfully!');
                        // Reload the page to show updated buttons
                        setTimeout(() => {
                            location.reload();
                        }, 1000);
                    } else {
                        show_alert('error', response.message || 'Failed to generate ID Card');
                    }
                },
                error: function(xhr) {
                    let errorMessage = 'Failed to generate ID Card';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    show_alert('error', errorMessage);
                },
                complete: function() {
                    $button.prop('disabled', false).text(originalText);
                }
            });
        });
    });

    // Toggle Academic Verification with confirmation modal
    let academicVerifyUrl = null;
    $(document).off('click', '.toggle-academic-verify-btn').on('click', '.toggle-academic-verify-btn', function(e) {
        e.preventDefault();
        const $btn = $(this);
        const url = $btn.data('url');
        const name = $btn.data('name') || 'this student';
        const isVerified = String($btn.data('verified')) === '1';

        academicVerifyUrl = url;

        const actionText = isVerified ? 'unverify' : 'verify';
        const modalText = `Are you sure you want to ${actionText} academic status for <strong>${name}</strong>?`;
        $('#academicVerifyModalText').html(modalText);
        const $confirmBtn = $('#confirmAcademicVerifyBtn');
        $confirmBtn.removeClass('btn-danger btn-success').addClass(isVerified ? 'btn-danger' : 'btn-success');
        $('#academicVerifyModal').modal('show');
    });

    $('#confirmAcademicVerifyBtn').on('click', function() {
        if (!academicVerifyUrl) return;
        const $confirmBtn = $(this);
        const originalHtml = $confirmBtn.html();
        $confirmBtn.prop('disabled', true).addClass('disabled');
        $.post(academicVerifyUrl, {
                _token: '{{ csrf_token() }}'
            })
            .done(function(res) {
                if (res && res.success) {
                    show_alert('success', res.message || 'Updated');
                    $('#academicVerifyModal').modal('hide');
                    setTimeout(() => {
                        location.reload();
                    }, 600);
                } else {
                    show_alert('error', (res && res.message) ? res.message : 'Failed to update');
                }
            })
            .fail(function(xhr) {
                let msg = 'Failed to update';
                if (xhr && xhr.responseJSON && xhr.responseJSON.message) {
                    msg = xhr.responseJSON.message;
                }
                show_alert('error', msg);
            })
            .always(function() {
                $confirmBtn.prop('disabled', false).removeClass('disabled').html(originalHtml);
                academicVerifyUrl = null;
            });
    });

    // Toggle Support Verification with confirmation modal
    let supportVerifyUrl = null;
    $(document).off('click', '.toggle-support-verify-btn').on('click', '.toggle-support-verify-btn', function(e) {
        e.preventDefault();
        const $btn = $(this);
        const url = $btn.data('url');
        const name = $btn.data('name') || 'this student';
        const isVerified = String($btn.data('verified')) === '1';

        supportVerifyUrl = url;

        const actionText = isVerified ? 'unverify' : 'verify';
        const modalText = `Are you sure you want to ${actionText} support status for <strong>${name}</strong>?`;
        $('#supportVerifyModalText').html(modalText);
        const $confirmBtn = $('#confirmSupportVerifyBtn');
        $confirmBtn.removeClass('btn-danger btn-success').addClass(isVerified ? 'btn-danger' : 'btn-success');
        $('#supportVerifyModal').modal('show');
    });

    $('#confirmSupportVerifyBtn').on('click', function() {
        if (!supportVerifyUrl) return;
        const $confirmBtn = $(this);
        const originalHtml = $confirmBtn.html();
        $confirmBtn.prop('disabled', true).addClass('disabled');
        $.post(supportVerifyUrl, {
                _token: '{{ csrf_token() }}'
            })
            .done(function(res) {
                if (res && res.success) {
                    show_alert('success', res.message || 'Updated');
                    $('#supportVerifyModal').modal('hide');
                    setTimeout(() => {
                        location.reload();
                    }, 600);
                } else {
                    show_alert('error', (res && res.message) ? res.message : 'Failed to update');
                }
            })
            .fail(function(xhr) {
                let msg = 'Failed to update';
                if (xhr && xhr.responseJSON && xhr.responseJSON.message) {
                    msg = xhr.responseJSON.message;
                }
                show_alert('error', msg);
            })
            .always(function() {
                $confirmBtn.prop('disabled', false).removeClass('disabled').html(originalHtml);
                supportVerifyUrl = null;
            });
    });
</script>
@include('admin.converted-leads.partials.mentor-flag-inline-scripts')
@endpush

<!-- Support Verify Modal -->
<div class="modal fade" id="supportVerifyModal" tabindex="-1" aria-labelledby="supportVerifyModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="supportVerifyModalLabel">Confirm Action</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p id="supportVerifyModalText" class="mb-0"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="confirmSupportVerifyBtn">
                    <span class="confirm-text">Confirm</span>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Academic Verify Modal -->
<div class="modal fade" id="academicVerifyModal" tabindex="-1" aria-labelledby="academicVerifyModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="academicVerifyModalLabel">Confirm Action</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p id="academicVerifyModalText" class="mb-0"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="confirmAcademicVerifyBtn">
                    <span class="confirm-text">Confirm</span>
                </button>
            </div>
        </div>
    </div>
</div>
