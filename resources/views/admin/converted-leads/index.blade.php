@extends('layouts.mantis')

@section('title', 'Converted Leads')

@section('content')
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
<!-- [ breadcrumb ] start -->
<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-6">
                <div class="page-header-title">
                    <h5 class="m-b-10">Converted Leads Management</h5>
                </div>
            </div>
            <div class="col-md-6">
                <ul class="breadcrumb d-flex justify-content-end">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item">Converted Leads</li>
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
                    <a href="{{ route('admin.converted-leads.index') }}" class="btn btn-outline-primary {{ request()->routeIs('admin.converted-leads.index') && !request('course_id') ? 'active' : '' }}">
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
                    <a href="{{ route('admin.edumaster-converted-leads.index') }}" class="btn btn-outline-warning">
                        <i class="ti ti-graduation"></i> EduMaster Converted Leads
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
                    <a href="{{ route('admin.converted-leads.index') }}" class="btn btn-outline-primary active">
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
                    <a href="{{ route('admin.digital-marketing-mentor-converted-leads.index') }}" class="btn btn-outline-primary">
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

@include('admin.converted-leads.partials.faculty-list-nav', ['activeFacultyRoute' => $activeFacultyRoute ?? null])

<!-- [ Support List ] start -->
@if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_support_team())
<div class="row mb-3">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h6 class="mb-3">Support List</h6>
                <div class="d-flex gap-2 flex-wrap">
                    @if(\App\Helpers\RoleHelper::is_support_team())
                    <a href="{{ route('admin.converted-leads.index') }}" class="btn btn-outline-primary">
                        <i class="ti ti-list"></i> All Converted Leads
                    </a>
                    @endif
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
                        <i class="ti ti-headphones"></i> Junior Vlogger - Course Support List
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
                <form method="GET" action="{{ route('admin.converted-leads.index') }}" id="filterForm">
                    <div class="row g-3 align-items-end">
                        <div class="col-12 col-sm-6 col-md-2">
                            <label for="search" class="form-label">Search</label>
                            <input type="text" class="form-control" id="search" name="search"
                                value="{{ request('search') }}" placeholder="Name, Phone, Email, Register Number">
                        </div>
                        @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_academic_assistant() || \App\Helpers\RoleHelper::is_finance() || \App\Helpers\RoleHelper::is_mentor())
                        <div class="col-12 col-sm-6 col-md-2">
                            <label for="course_id" class="form-label">Course</label>
                            <select class="form-select" id="course_id" name="course_id">
                                <option value="">All Courses</option>
                                @foreach($courses as $course)
                                <option value="{{ $course->id }}" {{ request('course_id') == $course->id ? 'selected' : '' }}>
                                    {{ $course->title }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        @endif
                        <div class="col-12 col-sm-6 col-md-2">
                            <label for="batch_id" class="form-label">Batch</label>
                            <select class="form-select" id="batch_id" name="batch_id" data-selected="{{ request('batch_id') }}">
                                <option value="">All Batches</option>
                            </select>
                        </div>
                        <div class="col-12 col-sm-6 col-md-2">
                            <label for="admission_batch_id" class="form-label">Admission Batch</label>
                            <select class="form-select" id="admission_batch_id" name="admission_batch_id" data-selected="{{ request('admission_batch_id') }}">
                                <option value="">All Admission Batches</option>
                            </select>
                        </div>
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
                        <!-- <div class="col-12 col-sm-6 col-md-2">
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
                            <label for="reg_fee" class="form-label">REG. FEE</label>
                            <select class="form-select" id="reg_fee" name="reg_fee">
                                <option value="">All</option>
                                <option value="Received" {{ request('reg_fee')==='Received' ? 'selected' : '' }}>Received</option>
                                <option value="Not Received" {{ request('reg_fee')==='Not Received' ? 'selected' : '' }}>Not Received</option>
                            </select>
                        </div>  -->

                        <div class="col-12 col-sm-6 col-md-2">
                            <label for="status" class="form-label">REG. FEE</label>
                            <select class="form-select" id="status" name="status">
                                <option value="">All</option>
                                <option value="Paid" {{ request('status')==='Paid' ? 'selected' : '' }}>Paid</option>
                                <option value="Received" {{ request('status')==='Received' ? 'selected' : '' }}>Received</option>
                                <option value="Admission cancel" {{ request('status')==='Admission cancel' ? 'selected' : '' }}>Admission cancel</option>
                                <option value="Active" {{ request('status')==='Active' ? 'selected' : '' }}>Active</option>
                                <option value="Inactive" {{ request('status')==='Inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>

                        <div class="col-12 col-sm-6 col-md-2">
                            <label for="reg_fee" class="form-label">Status</label>
                            <select class="form-select" id="reg_fee" name="reg_fee">
                                <option value="">All</option>
                                <option value="Handover -1" {{ request('reg_fee')==='Handover -1' ? 'selected' : '' }}>Handover -1</option>
                                <option value="Handover - 2" {{ request('reg_fee')==='Handover - 2' ? 'selected' : '' }}>Handover - 2</option>
                                <option value="Handover - 3" {{ request('reg_fee')==='Handover - 3' ? 'selected' : '' }}>Handover - 3</option>
                                <option value="Handover - 4" {{ request('reg_fee')==='Handover - 4' ? 'selected' : '' }}>Handover - 4</option>
                                <option value="Handover - 5" {{ request('reg_fee')==='Handover - 5' ? 'selected' : '' }}>Handover - 5</option>
                                <option value="Paid" {{ request('reg_fee')==='Paid' ? 'selected' : '' }}>Paid</option>
                                <option value="Admission cancel" {{ request('reg_fee')==='Admission cancel' ? 'selected' : '' }}>Admission cancel</option>
                            </select>
                        </div>

                        <div class="col-12 col-sm-6 col-md-2">
                            <label for="exam_fee" class="form-label">EXAM FEE</label>
                            <select class="form-select" id="exam_fee" name="exam_fee">
                                <option value="">All</option>
                                <option value="Pending" {{ request('exam_fee')==='Pending' ? 'selected' : '' }}>Pending</option>
                                <option value="Not Paid" {{ request('exam_fee')==='Not Paid' ? 'selected' : '' }}>Not Paid</option>
                                <option value="Paid" {{ request('exam_fee')==='Paid' ? 'selected' : '' }}>Paid</option>
                            </select>
                        </div>

                        <div class="col-12 col-sm-6 col-md-2">
                            <label for="id_card" class="form-label">ID CARD</label>
                            <select class="form-select" id="id_card" name="id_card">
                                <option value="">All</option>
                                <option value="processing" {{ request('id_card')==='processing' ? 'selected' : '' }}>processing</option>
                                <option value="download" {{ request('id_card')==='download' ? 'selected' : '' }}>download</option>
                                <option value="not downloaded" {{ request('id_card')==='not downloaded' ? 'selected' : '' }}>not downloaded</option>
                            </select>
                        </div>

                        <div class="col-12 col-sm-6 col-md-2">
                            <label for="tma" class="form-label">TMA</label>
                            <select class="form-select" id="tma" name="tma">
                                <option value="">All</option>
                                <option value="Uploaded" {{ request('tma')==='Uploaded' ? 'selected' : '' }}>Uploaded</option>
                                <option value="Not Upload" {{ request('tma')==='Not Upload' ? 'selected' : '' }}>Not Upload</option>
                            </select>
                        </div>

                        <div class="col-12 col-sm-6 col-md-2">
                            <label for="is_b2b" class="form-label">Type</label>
                            <select class="form-select" id="is_b2b" name="is_b2b">
                                <option value="">All Types</option>
                                <option value="b2b" {{ request('is_b2b') === 'b2b' ? 'selected' : '' }}>B2B</option>
                                <option value="in_house" {{ request('is_b2b') === 'in_house' ? 'selected' : '' }}>In House</option>
                            </select>
                        </div>

                        <div class="col-12 col-md-4">
                            <div class="d-flex gap-2 flex-wrap">
                                <button type="submit" class="btn btn-primary">
                                    <i class="ti ti-search"></i> <span class="d-none d-sm-inline">Filter</span>
                                </button>
                                <a href="{{ route('admin.converted-leads.index') }}" class="btn btn-secondary" id="convertedLeadsClearFilters">
                                    <i class="ti ti-x"></i> <span class="d-none d-sm-inline">Clear</span>
                                </a>
                            </div>
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
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Converted Leads List</h5>
                <a href="{{ route('admin.converted-leads.export', request()->all()) }}" class="btn btn-success">
                    <i class="ti ti-download"></i> Export to Excel
                </a>
            </div>
            <div class="card-body">
                <!-- Table View -->
                <div class="table-responsive">
                    <table class="table table-hover" id="convertedLeadsTable" style="min-width: 2400px;">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Academic</th>
                                    <th>Support</th>
                                    <th>Academic Document Approved</th>
                                    <th>Converted Date</th>
                                    <th>Academic Verified At</th>
                                    <th>Support Verified At</th>
                                    <th>Register Number</th>
                                    <th>DOB</th>
                                    <th>Type</th>
                                    <th>Name</th>
                                    <th>Phone</th>
                                    <th>WhatsApp</th>
                                    @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_academic_assistant())
                                    <th>Parent Phone</th>
                                    @endif
                                    <th>Course</th>
                                    <th>Batch</th>
                                    <th>Admission Batch</th>
                                    <th>Status</th>
                                    <th>Cancelled By</th>
                                    <th>REG. FEE</th>
                                    <th>Mail</th>
                                    <th>Lead Created By</th>
                                    <th>Pending Payment</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
            </div>
        </div>
    </div>
</div>
<!-- [ Main Content ] end -->

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

<script id="country-codes-json" type="application/json">{!! json_encode($country_codes ?? [], JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) !!}</script>

@php
$convertedLeadsColumns = [
    ['data' => 'index', 'name' => 'index', 'orderable' => false, 'searchable' => false],
    ['data' => 'academic', 'name' => 'academic', 'orderable' => false, 'searchable' => false],
    ['data' => 'support', 'name' => 'support', 'orderable' => false, 'searchable' => false],
    ['data' => 'academic_doc_approved', 'name' => 'academic_doc_approved', 'orderable' => false, 'searchable' => false],
    ['data' => 'converted_date', 'name' => 'converted_date', 'orderable' => false, 'searchable' => false],
    ['data' => 'academic_verified_at', 'name' => 'academic_verified_at', 'orderable' => false, 'searchable' => false],
    ['data' => 'support_verified_at', 'name' => 'support_verified_at', 'orderable' => false, 'searchable' => false],
    ['data' => 'register_number', 'name' => 'register_number', 'orderable' => false, 'searchable' => false],
    ['data' => 'dob', 'name' => 'dob', 'orderable' => false, 'searchable' => false],
    ['data' => 'type', 'name' => 'type', 'orderable' => false, 'searchable' => false],
    ['data' => 'name', 'name' => 'name', 'orderable' => false, 'searchable' => false],
    ['data' => 'phone', 'name' => 'phone', 'orderable' => false, 'searchable' => false],
    ['data' => 'whatsapp', 'name' => 'whatsapp', 'orderable' => false, 'searchable' => false],
];
if (\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_academic_assistant()) {
    $convertedLeadsColumns[] = ['data' => 'parent_phone', 'name' => 'parent_phone', 'orderable' => false, 'searchable' => false];
}
$convertedLeadsColumns = array_merge($convertedLeadsColumns, [
    ['data' => 'course', 'name' => 'course', 'orderable' => false, 'searchable' => false],
    ['data' => 'batch', 'name' => 'batch', 'orderable' => false, 'searchable' => false],
    ['data' => 'admission_batch', 'name' => 'admission_batch', 'orderable' => false, 'searchable' => false],
    ['data' => 'status', 'name' => 'status', 'orderable' => false, 'searchable' => false],
    ['data' => 'cancelled_by', 'name' => 'cancelled_by', 'orderable' => false, 'searchable' => false],
    ['data' => 'reg_fee', 'name' => 'reg_fee', 'orderable' => false, 'searchable' => false],
    ['data' => 'email', 'name' => 'email', 'orderable' => false, 'searchable' => false],
    ['data' => 'lead_created_by', 'name' => 'lead_created_by', 'orderable' => false, 'searchable' => false],
    ['data' => 'pending_payment', 'name' => 'pending_payment', 'orderable' => false, 'searchable' => false],
    ['data' => 'actions', 'name' => 'actions', 'orderable' => false, 'searchable' => false],
]);
@endphp

<div id="convertedLeadsConfig" data-data-url="{{ route('admin.converted-leads.data') }}" style="display:none"></div>
<script type="application/json" id="convertedLeadsColumnsData">{!! json_encode($convertedLeadsColumns) !!}</script>

@endsection

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
        /* allow a bit wider than the cell */
        z-index: 10;
        background: white;
        border: 1px solid #ddd;
        border-radius: 4px;
        padding: 10px;
        min-width: 320px;
        /* wider edit area */
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

    /* Increase table column widths for readability */
    #convertedLeadsTable thead th,
    #convertedLeadsTable tbody td {
        white-space: nowrap;
    }

    /* Sticky header */
    #convertedLeadsTable thead th {
        position: sticky;
        top: 0;
        z-index: 5;
        background: #fff;
        box-shadow: inset 0 -1px 0 #e9ecef;
    }

    /* Hover state */
    #convertedLeadsTable tbody tr:hover {
        background: #fafbff;
    }

    /* Truncate long text */
    #convertedLeadsTable td .display-value {
        display: inline-block;
        max-width: 220px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        vertical-align: middle;
    }

    /* Action buttons spacing */
    #convertedLeadsTable .btn-group .btn {
        margin-right: 4px;
    }

    #convertedLeadsTable .btn-group .btn:last-child {
        margin-right: 0;
    }

    /* Filter form separation */
    .card .card-body #filterForm {
        border-bottom: 1px dashed #e9ecef;
        padding-bottom: 8px;
    }

    /* Column-specific min-widths by position */
    #convertedLeadsTable thead th:nth-child(1),
    #convertedLeadsTable tbody td:nth-child(1) {
        min-width: 60px;
    }

    #convertedLeadsTable thead th:nth-child(2),
    #convertedLeadsTable tbody td:nth-child(2) {
        min-width: 140px;
    }

    #convertedLeadsTable thead th:nth-child(3),
    #convertedLeadsTable tbody td:nth-child(3) {
        min-width: 140px;
    }

    #convertedLeadsTable thead th:nth-child(4),
    #convertedLeadsTable tbody td:nth-child(4) {
        min-width: 160px;
    }

    #convertedLeadsTable thead th:nth-child(5),
    #convertedLeadsTable tbody td:nth-child(5) {
        min-width: 140px;
    }

    #convertedLeadsTable thead th:nth-child(6),
    #convertedLeadsTable tbody td:nth-child(6) {
        min-width: 140px;
    }

    #convertedLeadsTable thead th:nth-child(7),
    #convertedLeadsTable tbody td:nth-child(7) {
        min-width: 220px;
    }

    #convertedLeadsTable thead th:nth-child(8),
    #convertedLeadsTable tbody td:nth-child(8) {
        min-width: 160px;
    }

    #convertedLeadsTable thead th:nth-child(9),
    #convertedLeadsTable tbody td:nth-child(9) {
        min-width: 200px;
    }

    #convertedLeadsTable thead th:nth-child(10),
    #convertedLeadsTable tbody td:nth-child(10) {
        min-width: 200px;
    }

    #convertedLeadsTable thead th:nth-child(11),
    #convertedLeadsTable tbody td:nth-child(11) {
        min-width: 220px;
    }

    #convertedLeadsTable thead th:nth-child(12),
    #convertedLeadsTable tbody td:nth-child(12) {
        min-width: 220px;
    }

    #convertedLeadsTable thead th:nth-child(13),
    #convertedLeadsTable tbody td:nth-child(13) {
        min-width: 200px;
    }
</style>
@endpush

@push('scripts')
<script>
    $(document).ready(function() {
        const configEl = document.getElementById('convertedLeadsConfig');
        const convertedLeadsDataUrl = configEl ? configEl.dataset.dataUrl : '';
        const columnsEl = document.getElementById('convertedLeadsColumnsData');
        const convertedLeadsColumns = columnsEl ? JSON.parse(columnsEl.textContent || '[]') : [];
        let convertedLeadsTable = null;

        function getFilterParams() {
            return {
                filter_search: ($('#search').val() || '').trim(),
                course_id: $('#course_id').val() || '',
                batch_id: $('#batch_id').val() || '',
                admission_batch_id: $('#admission_batch_id').val() || '',
                date_from: $('#date_from').val() || '',
                date_to: $('#date_to').val() || '',
                status: $('#status').val() || '',
                reg_fee: $('#reg_fee').val() || '',
                exam_fee: $('#exam_fee').val() || '',
                id_card: $('#id_card').val() || '',
                tma: $('#tma').val() || '',
                is_b2b: $('#is_b2b').val() || ''
            };
        }

        function updateUrlWithFilters() {
            const f = getFilterParams();
            const params = new URLSearchParams();
            if (f.filter_search) params.append('search', f.filter_search);
            if (f.course_id) params.append('course_id', f.course_id);
            if (f.batch_id) params.append('batch_id', f.batch_id);
            if (f.admission_batch_id) params.append('admission_batch_id', f.admission_batch_id);
            if (f.date_from) params.append('date_from', f.date_from);
            if (f.date_to) params.append('date_to', f.date_to);
            if (f.status) params.append('status', f.status);
            if (f.reg_fee) params.append('reg_fee', f.reg_fee);
            if (f.exam_fee) params.append('exam_fee', f.exam_fee);
            if (f.id_card) params.append('id_card', f.id_card);
            if (f.tma) params.append('tma', f.tma);
            if (f.is_b2b) params.append('is_b2b', f.is_b2b);
            const newUrl = params.toString() ? `${window.location.pathname}?${params.toString()}` : window.location.pathname;
            window.history.replaceState({ path: newUrl }, '', newUrl);
        }

        function loadFiltersFromUrl() {
            const p = new URLSearchParams(window.location.search);
            if (p.get('search')) $('#search').val(p.get('search'));
            if (p.get('course_id')) $('#course_id').val(p.get('course_id'));
            if (p.get('batch_id')) $('#batch_id').data('selected', p.get('batch_id'));
            if (p.get('admission_batch_id')) $('#admission_batch_id').data('selected', p.get('admission_batch_id'));
            if (p.get('date_from')) $('#date_from').val(p.get('date_from'));
            if (p.get('date_to')) $('#date_to').val(p.get('date_to'));
            if (p.get('status')) $('#status').val(p.get('status'));
            if (p.get('reg_fee')) $('#reg_fee').val(p.get('reg_fee'));
            if (p.get('exam_fee')) $('#exam_fee').val(p.get('exam_fee'));
            if (p.get('id_card')) $('#id_card').val(p.get('id_card'));
            if (p.get('tma')) $('#tma').val(p.get('tma'));
            if (p.get('is_b2b')) $('#is_b2b').val(p.get('is_b2b'));
        }

        function reloadConvertedLeadsTable() {
            if (convertedLeadsTable) {
                convertedLeadsTable.ajax.reload(null, false);
            }
        }

        function loadBatchesByCourse(courseId, selectedId, done) {
            const $batch = $('#batch_id');
            $batch.html('<option value="">Loading...</option>');
            if (!courseId) {
                $batch.html('<option value="">All Batches</option>');
                if (typeof done === 'function') done();
                return;
            }
            $.get(`/api/batches/by-course/${courseId}`).done(function(response) {
                let opts = '<option value="">All Batches</option>';
                if (response.success && response.batches) {
                    response.batches.forEach(function(b) {
                        const sel = String(selectedId) === String(b.id) ? 'selected' : '';
                        opts += `<option value="${b.id}" ${sel}>${b.title}</option>`;
                    });
                }
                $batch.html(opts);
            }).fail(function() {
                $batch.html('<option value="">All Batches</option>');
            }).always(function() {
                if (typeof done === 'function') done();
            });
        }

        function loadAdmissionBatchesByBatch(batchId, selectedId, done) {
            const $admission = $('#admission_batch_id');
            $admission.html('<option value="">Loading...</option>');
            if (!batchId) {
                $admission.html('<option value="">All Admission Batches</option>');
                if (typeof done === 'function') done();
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
            }).always(function() {
                if (typeof done === 'function') done();
            });
        }

        loadFiltersFromUrl();
        const initialCourse = $('#course_id').val();
        const batchDataSelected = $('#batch_id').data('selected');
        const admissionDataSelected = $('#admission_batch_id').data('selected');

        function initConvertedLeadsDataTable() {
            $('#convertedLeadsTable').removeClass('data_table_basic');
            if ($.fn.DataTable.isDataTable('#convertedLeadsTable')) {
                $('#convertedLeadsTable').DataTable().destroy();
            }
            convertedLeadsTable = $('#convertedLeadsTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: convertedLeadsDataUrl,
                    type: 'GET',
                    data: function(d) {
                        $.extend(d, getFilterParams());
                    },
                    error: function() {
                        if (typeof showToast === 'function') {
                            showToast('Error loading converted leads.', 'error');
                        }
                    }
                },
                pageLength: 25,
                lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
                order: [],
                ordering: false,
                dom: 'Bfrtip',
                buttons: ['csv', 'excel', 'print', 'pdf'],
                scrollX: true,
                autoWidth: false,
                columns: convertedLeadsColumns,
                stateSave: true,
                stateDuration: -1
            });
        }

        setTimeout(function() {
            if (initialCourse) {
                loadBatchesByCourse(initialCourse, batchDataSelected, function() {
                    const bid = $('#batch_id').val() || batchDataSelected;
                    loadAdmissionBatchesByBatch(bid, admissionDataSelected, initConvertedLeadsDataTable);
                });
            } else {
                loadBatchesByCourse('', '', function() {
                    loadAdmissionBatchesByBatch(batchDataSelected, admissionDataSelected, initConvertedLeadsDataTable);
                });
            }
        }, 0);

        $('#filterForm').on('submit', function(e) {
            e.preventDefault();
            updateUrlWithFilters();
            reloadConvertedLeadsTable();
        });

        $('#convertedLeadsClearFilters').on('click', function(e) {
            e.preventDefault();
            const form = document.getElementById('filterForm');
            if (form) form.reset();
            $('#batch_id').html('<option value="">All Batches</option>');
            $('#admission_batch_id').html('<option value="">All Admission Batches</option>');
            window.history.replaceState({}, '', window.location.pathname);
            reloadConvertedLeadsTable();
        });

        // Handle Change Course modal buttons
        $(document).on('click', '.js-change-course-modal', function(e) {
            e.preventDefault();
            const url = $(this).data('modal-url');
            const title = $(this).data('modal-title') || 'Change Course';
            if (typeof show_ajax_modal === 'function' && url) {
                show_ajax_modal(url, title);
            }
        });

        // Handle cancellation flag modal
        $(document).on('click', '.js-cancel-flag', function(e) {
            e.preventDefault();
            const url = $(this).data('cancel-url');
            const title = $(this).data('modal-title') || 'Cancellation Confirmation';
            if (typeof show_ajax_modal === 'function' && url) {
                show_ajax_modal(url, title);
            }
        });

        // Delegated submit handler for cancellation flag modal (ensures AJAX even when inline scripts are not executed)
        $(document).on('submit', '#cancelFlagForm', function(e) {
            e.preventDefault();
            const form = $(this);
            const submitUrl = form.data('submit-url');
            if (!submitUrl) {
                return form.off('submit').submit(); // fallback to default submit if url missing
            }

            const submitBtn = form.find('button[type="submit"]');
            const originalText = submitBtn.html();
            submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Saving...');

            $.ajax({
                url: submitUrl,
                method: 'POST',
                data: form.serialize(),
                success: function (response) {
                    $('#ajax_modal').modal('hide');
                    if (typeof showToast === 'function') {
                        showToast(response.message, 'success');
                    } else if (typeof toast_success === 'function') {
                        toast_success(response.message);
                    } else {
                        alert(response.message);
                    }
                    if ($.fn.DataTable.isDataTable('#convertedLeadsTable')) {
                        const dt = $('#convertedLeadsTable').DataTable();
                        if (dt.ajax && dt.ajax.url()) {
                            dt.ajax.reload();
                        } else {
                            // Static table: redraw to clear warnings, then fallback reload
                            dt.rows().invalidate().draw(false);
                            location.reload();
                        }
                    } else {
                        location.reload();
                    }
                },
                error: function (xhr) {
                    let message = 'Unable to update cancellation flag.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        message = xhr.responseJSON.message;
                    } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                        message = Object.values(xhr.responseJSON.errors).flat().join('\n');
                    }
                    if (typeof showToast === 'function') {
                        showToast(message, 'error');
                    } else if (typeof toast_error === 'function') {
                        toast_error(message);
                    } else {
                        alert(message);
                    }
                },
                complete: function () {
                    submitBtn.prop('disabled', false).html(originalText);
                }
            });
        });

        // On course change â†’ reload batches, clear admission batch
        $('#course_id').on('change', function() {
            const cid = $(this).val();
            $('#admission_batch_id').html('<option value="">All Admission Batches</option>');
            loadBatchesByCourse(cid, '');
        });

        // On batch change â†’ reload admission batches
        $('#batch_id').on('change', function() {
            const bid = $(this).val();
            loadAdmissionBatchesByBatch(bid, '');
        });

        $(document).on('click', '.update-register-btn', function(e) {
            e.preventDefault();
            const url = $(this).data('url');
            const title = $(this).data('title');
            show_small_modal(url, title);
        });

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
                        setTimeout(function() {
                            if ($.fn.DataTable.isDataTable('#convertedLeadsTable')) {
                                $('#convertedLeadsTable').DataTable().ajax.reload(null, false);
                            } else {
                                location.reload();
                            }
                        }, 600);
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
                        setTimeout(function() {
                            if ($.fn.DataTable.isDataTable('#convertedLeadsTable')) {
                                $('#convertedLeadsTable').DataTable().ajax.reload(null, false);
                            } else {
                                location.reload();
                            }
                        }, 400);
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
                        setTimeout(function() {
                            if ($.fn.DataTable.isDataTable('#convertedLeadsTable')) {
                                $('#convertedLeadsTable').DataTable().ajax.reload(null, false);
                            } else {
                                location.reload();
                            }
                        }, 400);
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

        // Inline editing functionality
        const inlinePhoneFields = ['phone', 'whatsapp_number', 'parents_number'];

        $(document).on('click', '.edit-btn', function(e) {
            e.preventDefault();
            e.stopPropagation();

            const container = $(this).closest('.inline-edit');
            const field = container.data('field');
            const id = container.data('id');
            const currentValue = container.data('current') !== undefined ? String(container.data('current')).trim() : container.find('.display-value').text().trim();
            const currentId = container.data('current-id') !== undefined ? String(container.data('current-id')).trim() : '';

            if (container.hasClass('editing')) {
                return;
            }

            $('.inline-edit.editing').not(container).each(function() {
                $(this).removeClass('editing');
                $(this).find('.edit-form').remove();
            });

            let editForm = '';

            if (field === 'batch_id') {
                const courseId = container.data('course-id');
                editForm = createBatchSelect(courseId, currentId);
            } else if (field === 'admission_batch_id') {
                const batchId = container.data('batch-id');
                editForm = createAdmissionBatchSelect(batchId, currentId);
            } else if (field === 'status') {
                editForm = createStatusSelect(currentValue);
            } else if (field === 'reg_fee') {
                editForm = createRegFeeSelect(currentValue);
            } else if (inlinePhoneFields.includes(field)) {
                const currentCode = container.data('code') || '';
                editForm = createPhoneField(currentCode, currentValue);
            } else {
                editForm = createInputField(field, currentValue);
            }

            container.addClass('editing');
            container.append(editForm);

            // Load options for select fields that need dynamic loading
            if (field === 'batch_id') {
                const courseId = container.data('course-id');
                const select = container.find('select');
                loadBatches(courseId, select, currentId);
            } else if (field === 'admission_batch_id') {
                const batchId = container.data('batch-id');
                const select = container.find('select');
                loadAdmissionBatches(batchId, select, currentId);
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
            const id = container.data('id');
            let value = container.find('input, select').val();
            let extra = {};

            if (inlinePhoneFields.includes(field)) {
                value = container.find('input[name="phone"]').val();
                const codeField = container.data('code-field') || 'code';
                extra[codeField] = container.find('select[name="code"]').val();
            }

            const btn = $(this);
            if (btn.data('busy')) return;
            btn.data('busy', true);
            btn.prop('disabled', true).html('<i class="ti ti-loader-2 spin"></i>');

            $.ajax({
                url: `/admin/converted-leads/${id}/inline-update`,
                method: 'POST',
                data: $.extend({
                    field: field,
                    value: value,
                    _token: $('meta[name="csrf-token"]').attr('content')
                }, extra),
                success: function(response) {
                    if (response.success) {
                        let displayValue = response.value || 'N/A';
                        container.find('.display-value').text(displayValue);
                        if (inlinePhoneFields.includes(field)) {
                            const num = container.find('input[name="phone"]').val();
                            const code = container.find('select[name="code"]').val();
                            container.data('current', num || '');
                            container.data('code', code || '');
                        } else {
                            container.data('current', displayValue);
                        }
                        // Update avatar initial when name changes
                        if (field === 'name') {
                            const row = container.closest('tr');
                            const initialEl = row.find('.js-name-initial').first();
                            if (initialEl.length) {
                                const initial = (displayValue && String(displayValue).trim().length)
                                    ? String(displayValue).trim().charAt(0).toUpperCase()
                                    : '?';
                                initialEl.text(initial);
                            }
                        }
                        // Update data-current-id for fields that use it (store the ID, not the display value)
                        if (field === 'batch_id' || field === 'admission_batch_id') {
                            container.data('current-id', value || '');
                        }

                        // If batch_id changed, update the admission_batch_id container's data-batch-id
                        if (field === 'batch_id') {
                            const row = container.closest('tr');
                            const admissionBatchContainer = row.find('.inline-edit[data-field="admission_batch_id"]');
                            if (admissionBatchContainer.length) {
                                admissionBatchContainer.data('batch-id', value || '');
                                // Clear admission batch if batch changed
                                admissionBatchContainer.find('.display-value').text('N/A');
                                admissionBatchContainer.data('current-id', '');
                            }
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

        // Cancel inline edit
        $(document).on('click', '.cancel-edit', function(e) {
            e.preventDefault();
            e.stopPropagation();

            const container = $(this).closest('.inline-edit');
            container.removeClass('editing');
            container.find('.edit-form').remove();
        });

        function createInputField(field, currentValue) {
            const inputType = 'text';
            const displayValue = currentValue === 'N/A' ? '' : currentValue;
            const commonAttrs = 'autocomplete="off" autocapitalize="off" spellcheck="false" name="inline-temp"';
            const valueAttr = `value="${displayValue}"`;
            return `
                <div class="edit-form">
                    <input type="${inputType}" ${valueAttr} ${commonAttrs} class="form-control form-control-sm">
                    <div class="btn-group mt-1">
                        <button type="button" class="btn btn-success btn-sm save-edit">Save</button>
                        <button type="button" class="btn btn-secondary btn-sm cancel-edit">Cancel</button>
                    </div>
                </div>
            `;
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
                            <select name="code" class="form-select form-select-sm">
                                ${buildOptions(currentCode)}
                            </select>
                        </div>
                        <div class="col-7">
                            <input type="text" name="phone" value="${safePhone}" class="form-control form-control-sm" placeholder="Phone number" autocomplete="off">
                        </div>
                    </div>
                    <div class="btn-group mt-1">
                        <button type="button" class="btn btn-success btn-sm save-edit">Save</button>
                        <button type="button" class="btn btn-secondary btn-sm cancel-edit">Cancel</button>
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

        function createAdmissionBatchSelect(batchId, currentId) {
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

        function createRegFeeSelect(currentValue) {
            const options = [{
                    value: 'Received',
                    label: 'Received'
                },
                {
                    value: 'Not Received',
                    label: 'Not Received'
                }
            ];
            const selected = currentValue === 'N/A' ? '' : currentValue;
            const optionTags = ['<option value="">Select Reg. Fee</option>']
                .concat(options.map(opt => {
                    const isSel = selected === opt.value ? 'selected' : '';
                    return `<option value="${opt.value}" ${isSel}>${opt.label}</option>`;
                }))
                .join('');

            return `
                <div class="edit-form">
                    <select class="form-select form-select-sm">
                        ${optionTags}
                    </select>
                    <div class="btn-group mt-1">
                        <button type="button" class="btn btn-success btn-sm save-edit">Save</button>
                        <button type="button" class="btn btn-secondary btn-sm cancel-edit">Cancel</button>
                    </div>
                </div>
            `;
        }

        function createStatusSelect(currentValue) {
            const options = [{
                    value: 'Paid',
                    label: 'Paid'
                },
                {
                    value: 'Admission cancel',
                    label: 'Admission cancel'
                },
                {
                    value: 'Active',
                    label: 'Active'
                },
                {
                    value: 'Inactive',
                    label: 'Inactive'
                },
            ];
            const selected = currentValue === 'N/A' ? '' : currentValue;
            const optionTags = ['<option value="">Select Status</option>']
                .concat(options.map(opt => {
                    const isSel = selected === opt.value ? 'selected' : '';
                    return `<option value="${opt.value}" ${isSel}>${opt.label}</option>`;
                }))
                .join('');

            return `
                <div class="edit-form">
                    <select class="form-select form-select-sm">
                        ${optionTags}
                    </select>
                    <div class="btn-group mt-1">
                        <button type="button" class="btn btn-success btn-sm save-edit">Save</button>
                        <button type="button" class="btn btn-secondary btn-sm cancel-edit">Cancel</button>
                    </div>
                </div>
            `;
        }

        function loadBatches(courseId, select, currentId) {
            if (!courseId) {
                select.html('<option value="">No course selected</option>');
                return;
            }

            $.get(`/api/batches/by-course/${courseId}`)
                .done(function(response) {
                    let options = '<option value="">Select Batch</option>';
                    if (response.success && response.batches) {
                        response.batches.forEach(function(batch) {
                            // Only select if currentId is not empty and matches
                            const isSelected = (currentId && String(currentId) === String(batch.id)) ? 'selected' : '';
                            options += `<option value="${batch.id}" ${isSelected}>${batch.title}</option>`;
                        });
                    }
                    select.html(options);
                    select.focus();
                })
                .fail(function() {
                    select.html('<option value="">Error loading batches</option>');
                });
        }

        function loadAdmissionBatches(batchId, select, currentId) {
            if (!batchId) {
                select.html('<option value="">No batch selected</option>');
                return;
            }

            $.get(`/api/admission-batches/by-batch/${batchId}`)
                .done(function(batches) {
                    let options = '<option value="">Select Admission Batch</option>';
                    batches.forEach(function(batch) {
                        // Only select if currentId is not empty and matches
                        const isSelected = (currentId && String(currentId) === String(batch.id)) ? 'selected' : '';
                        options += `<option value="${batch.id}" ${isSelected}>${batch.title}</option>`;
                    });
                    select.html(options);
                    select.focus();
                })
                .fail(function() {
                    select.html('<option value="">Error loading admission batches</option>');
                });
        }
    });
</script>
@endpush
