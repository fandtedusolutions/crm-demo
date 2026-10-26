@php
    $activeFacultyRoute = $activeFacultyRoute ?? null;
    $facultyNavBtn = function (string $route) use ($activeFacultyRoute): string {
        $active = $activeFacultyRoute !== null
            ? $activeFacultyRoute === $route
            : request()->routeIs($route);

        return $active ? 'btn btn-primary active' : 'btn btn-outline-primary';
    };
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
                    <a href="{{ route('admin.converted-leads.index') }}" class="{{ $facultyNavBtn('admin.converted-leads.index') }}">
                        <i class="ti ti-list"></i> All Converted Leads
                    </a>
                    @endif
                    <a href="{{ route('admin.faculty-bosse-converted-leads.index') }}" class="{{ $facultyNavBtn('admin.faculty-bosse-converted-leads.index') }}">
                        <i class="ti ti-user-star"></i> {{ course_title(2) }} Faculty List
                    </a>
                    <a href="{{ route('admin.faculty-nios-converted-leads.index') }}" class="{{ $facultyNavBtn('admin.faculty-nios-converted-leads.index') }}">
                        <i class="ti ti-user-star"></i> {{ course_title(1) }} Faculty List
                    </a>
                    <a href="{{ route('admin.faculty-ugpg-converted-leads.index') }}" class="{{ $facultyNavBtn('admin.faculty-ugpg-converted-leads.index') }}">
                        <i class="ti ti-user-star"></i> {{ course_title(9) }} Faculty List
                    </a>
                    <a href="{{ route('admin.faculty-edumaster-converted-leads.index') }}" class="{{ $facultyNavBtn('admin.faculty-edumaster-converted-leads.index') }}">
                        <i class="ti ti-user-star"></i> {{ course_title(23) }} Faculty List
                    </a>
                    <a href="{{ route('admin.faculty-eschool-converted-leads.index') }}" class="{{ $facultyNavBtn('admin.faculty-eschool-converted-leads.index') }}">
                        <i class="ti ti-user-star"></i> {{ course_title(5) }} Faculty List
                    </a>
                    <a href="{{ route('admin.faculty-eduthanzeel-converted-leads.index') }}" class="{{ $facultyNavBtn('admin.faculty-eduthanzeel-converted-leads.index') }}">
                        <i class="ti ti-user-star"></i> {{ course_title(6) }} Faculty List
                    </a>
                    <a href="{{ route('admin.gmvss-faculty-converted-leads.index') }}" class="{{ $facultyNavBtn('admin.gmvss-faculty-converted-leads.index') }}">
                        <i class="ti ti-user-star"></i> {{ course_title(16) }} Faculty List
                    </a>
                    <a href="{{ route('admin.digital-marketing-faculty-converted-leads.index') }}" class="{{ $facultyNavBtn('admin.digital-marketing-faculty-converted-leads.index') }}">
                        <i class="ti ti-user-star"></i> {{ course_title(11) }} Faculty List
                    </a>
                    <a href="{{ route('admin.data-science-faculty-converted-leads.index') }}" class="{{ $facultyNavBtn('admin.data-science-faculty-converted-leads.index') }}">
                        <i class="ti ti-user-star"></i> {{ course_title(12) }} Faculty List
                    </a>
                    <a href="{{ route('admin.data-analytics-with-gen-ai-faculty-converted-leads.index') }}" class="{{ $facultyNavBtn('admin.data-analytics-with-gen-ai-faculty-converted-leads.index') }}">
                        <i class="ti ti-user-star"></i> {{ course_title(37) }} Faculty List
                    </a>
                    <a href="{{ route('admin.graphic-designing-faculty-converted-leads.index') }}" class="{{ $facultyNavBtn('admin.graphic-designing-faculty-converted-leads.index') }}">
                        <i class="ti ti-user-star"></i> {{ course_title(15) }} Faculty List
                    </a>
                    <a href="{{ route('admin.ai-integrated-video-editing-faculty-converted-leads.index') }}" class="{{ $facultyNavBtn('admin.ai-integrated-video-editing-faculty-converted-leads.index') }}">
                        <i class="ti ti-user-star"></i> {{ course_title(30) }} Faculty List
                    </a>
                    <a href="{{ route('admin.ai-integrated-videography-faculty-converted-leads.index') }}" class="{{ $facultyNavBtn('admin.ai-integrated-videography-faculty-converted-leads.index') }}">
                        <i class="ti ti-user-star"></i> {{ course_title(31) }} Faculty List
                    </a>
                    <a href="{{ route('admin.ai-integrated-photography-faculty-converted-leads.index') }}" class="{{ $facultyNavBtn('admin.ai-integrated-photography-faculty-converted-leads.index') }}">
                        <i class="ti ti-user-star"></i> {{ course_title(32) }} Faculty List
                    </a>
                    <a href="{{ route('admin.machine-learning-faculty-converted-leads.index') }}" class="{{ $facultyNavBtn('admin.machine-learning-faculty-converted-leads.index') }}">
                        <i class="ti ti-user-star"></i> {{ course_title(20) }} Faculty List
                    </a>
                    <a href="{{ route('admin.medical-coding-faculty-converted-leads.index') }}" class="{{ $facultyNavBtn('admin.medical-coding-faculty-converted-leads.index') }}">
                        <i class="ti ti-user-star"></i> {{ course_title(3) }} Faculty List
                    </a>
                    <a href="{{ route('admin.hospital-administration-faculty-converted-leads.index') }}" class="{{ $facultyNavBtn('admin.hospital-administration-faculty-converted-leads.index') }}">
                        <i class="ti ti-user-star"></i> {{ course_title(4) }} Faculty List
                    </a>
                    <a href="{{ route('admin.python-faculty-converted-leads.index') }}" class="{{ $facultyNavBtn('admin.python-faculty-converted-leads.index') }}">
                        <i class="ti ti-user-star"></i> {{ course_title(10) }} Faculty List
                    </a>
                    <a href="{{ route('admin.flutter-faculty-converted-leads.index') }}" class="{{ $facultyNavBtn('admin.flutter-faculty-converted-leads.index') }}">
                        <i class="ti ti-user-star"></i> {{ course_title(21) }} Faculty List
                    </a>
                    <a href="{{ route('admin.rpa-faculty-converted-leads.index') }}" class="{{ $facultyNavBtn('admin.rpa-faculty-converted-leads.index') }}">
                        <i class="ti ti-user-star"></i> {{ course_title(27) }} Faculty List
                    </a>
                    <a href="{{ route('admin.junior-vlogger-faculty-converted-leads.index') }}" class="{{ $facultyNavBtn('admin.junior-vlogger-faculty-converted-leads.index') }}">
                        <i class="ti ti-user-star"></i> {{ course_title(25) }} Faculty List
                    </a>
                    <a href="{{ route('admin.robo-vibe-faculty-converted-leads.index') }}" class="{{ $facultyNavBtn('admin.robo-vibe-faculty-converted-leads.index') }}">
                        <i class="ti ti-user-star"></i> {{ course_title(33) }} Faculty List
                    </a>
                    <a href="{{ route('admin.prompt-engineering-faculty-converted-leads.index') }}" class="{{ $facultyNavBtn('admin.prompt-engineering-faculty-converted-leads.index') }}">
                        <i class="ti ti-user-star"></i> {{ course_title(34) }} Faculty List
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endif
<!-- [ Faculty List ] end -->
