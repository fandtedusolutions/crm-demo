@php
    $activeFacultyRoute = $activeFacultyRoute ?? null;
@endphp
<!-- [ Faculty List ] start -->
@if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_faculty() || \App\Helpers\RoleHelper::is_telecaller() || \App\Helpers\RoleHelper::is_team_lead() || \App\Helpers\RoleHelper::is_senior_manager() || \App\Helpers\RoleHelper::is_hod())
<div class="row mb-3">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h6 class="mb-3">Faculty List</h6>
                <div class="d-flex gap-2 flex-wrap">
                    @if(\App\Helpers\RoleHelper::is_faculty() || \App\Helpers\RoleHelper::is_telecaller() || \App\Helpers\RoleHelper::is_team_lead() || \App\Helpers\RoleHelper::is_senior_manager() || \App\Helpers\RoleHelper::is_hod())
                    <a href="{{ route('admin.converted-leads.index') }}" class="btn btn-outline-primary {{ $activeFacultyRoute === 'admin.converted-leads.index' ? 'active' : '' }}">
                        <i class="ti ti-list"></i> All Converted Leads
                    </a>
                    @endif
                    <a href="{{ route('admin.faculty-bosse-converted-leads.index') }}" class="btn btn-outline-primary {{ $activeFacultyRoute === 'admin.faculty-bosse-converted-leads.index' ? 'active' : '' }}">
                        <i class="ti ti-user-star"></i> Bosse Converted Faculty List
                    </a>
                    <a href="{{ route('admin.faculty-nios-converted-leads.index') }}" class="btn btn-outline-primary {{ $activeFacultyRoute === 'admin.faculty-nios-converted-leads.index' ? 'active' : '' }}">
                        <i class="ti ti-user-star"></i> NIOS Converted Faculty List
                    </a>
                    <a href="{{ route('admin.faculty-ugpg-converted-leads.index') }}" class="btn btn-outline-primary {{ $activeFacultyRoute === 'admin.faculty-ugpg-converted-leads.index' ? 'active' : '' }}">
                        <i class="ti ti-user-star"></i> UG/PG Faculty Converted List
                    </a>
                    <a href="{{ route('admin.faculty-edumaster-converted-leads.index') }}" class="btn btn-outline-primary {{ $activeFacultyRoute === 'admin.faculty-edumaster-converted-leads.index' ? 'active' : '' }}">
                        <i class="ti ti-user-star"></i> EduMaster Faculty Converted List
                    </a>
                    <a href="{{ route('admin.faculty-eschool-converted-leads.index') }}" class="btn btn-outline-primary {{ $activeFacultyRoute === 'admin.faculty-eschool-converted-leads.index' ? 'active' : '' }}">
                        <i class="ti ti-user-star"></i> E-School Converted Faculty List
                    </a>
                    <a href="{{ route('admin.faculty-eduthanzeel-converted-leads.index') }}" class="btn btn-outline-primary {{ $activeFacultyRoute === 'admin.faculty-eduthanzeel-converted-leads.index' ? 'active' : '' }}">
                        <i class="ti ti-user-star"></i> Eduthanzeel Converted Faculty List
                    </a>
                    <a href="{{ route('admin.gmvss-faculty-converted-leads.index') }}" class="btn btn-outline-primary {{ $activeFacultyRoute === 'admin.gmvss-faculty-converted-leads.index' ? 'active' : '' }}">
                        <i class="ti ti-user-star"></i> GMVSS Faculty List
                    </a>
                    <a href="{{ route('admin.digital-marketing-faculty-converted-leads.index') }}" class="btn btn-outline-primary {{ $activeFacultyRoute === 'admin.digital-marketing-faculty-converted-leads.index' ? 'active' : '' }}">
                        <i class="ti ti-user-star"></i> Digital Marketing Faculty List
                    </a>
                    <a href="{{ route('admin.data-science-faculty-converted-leads.index') }}" class="btn btn-outline-primary {{ $activeFacultyRoute === 'admin.data-science-faculty-converted-leads.index' ? 'active' : '' }}">
                        <i class="ti ti-user-star"></i> Data Science Course Faculty List
                    </a>
                    <a href="{{ route('admin.graphic-designing-faculty-converted-leads.index') }}" class="btn btn-outline-primary {{ $activeFacultyRoute === 'admin.graphic-designing-faculty-converted-leads.index' ? 'active' : '' }}">
                        <i class="ti ti-user-star"></i> Graphic Designing Faculty List
                    </a>
                    <a href="{{ route('admin.machine-learning-faculty-converted-leads.index') }}" class="btn btn-outline-primary {{ $activeFacultyRoute === 'admin.machine-learning-faculty-converted-leads.index' ? 'active' : '' }}">
                        <i class="ti ti-user-star"></i> Machine Learning Faculty List
                    </a>
                    <a href="{{ route('admin.medical-coding-faculty-converted-leads.index') }}" class="btn btn-outline-primary {{ $activeFacultyRoute === 'admin.medical-coding-faculty-converted-leads.index' ? 'active' : '' }}">
                        <i class="ti ti-user-star"></i> Medical Coding Faculty List
                    </a>
                    <a href="{{ route('admin.python-faculty-converted-leads.index') }}" class="btn btn-outline-primary {{ $activeFacultyRoute === 'admin.python-faculty-converted-leads.index' ? 'active' : '' }}">
                        <i class="ti ti-user-star"></i> Python Faculty List
                    </a>
                    <a href="{{ route('admin.flutter-faculty-converted-leads.index') }}" class="btn btn-outline-primary {{ $activeFacultyRoute === 'admin.flutter-faculty-converted-leads.index' ? 'active' : '' }}">
                        <i class="ti ti-user-star"></i> Flutter Faculty List
                    </a>
                    <a href="{{ route('admin.rpa-faculty-converted-leads.index') }}" class="btn btn-outline-primary {{ $activeFacultyRoute === 'admin.rpa-faculty-converted-leads.index' ? 'active' : '' }}">
                        <i class="ti ti-user-star"></i> RPA Faculty List
                    </a>
                    <a href="{{ route('admin.junior-vlogger-faculty-converted-leads.index') }}" class="btn btn-outline-primary {{ $activeFacultyRoute === 'admin.junior-vlogger-faculty-converted-leads.index' ? 'active' : '' }}">
                        <i class="ti ti-user-star"></i> Junior Vlogger Converted Faculty List
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endif
<!-- [ Faculty List ] end -->
