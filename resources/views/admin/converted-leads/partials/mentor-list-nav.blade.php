@php
    $activeMentorRoute = $activeMentorRoute ?? null;
    $mentorNavBtn = function (string $route) use ($activeMentorRoute): string {
        $active = $activeMentorRoute !== null
            ? $activeMentorRoute === $route
            : request()->routeIs($route);

        if ($route === 'admin.converted-leads.index' && $activeMentorRoute === 'admin.converted-leads.index') {
            $active = true;
        }

        return $active ? 'btn btn-primary active' : 'btn btn-outline-primary';
    };
@endphp
<!-- [ Mentor List ] start -->
@if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_mentor() || \App\Helpers\RoleHelper::is_telecaller() || \App\Helpers\RoleHelper::is_team_lead() || \App\Helpers\RoleHelper::is_senior_manager() || \App\Helpers\RoleHelper::is_hod())
<div class="row mb-3">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h6 class="mb-3">Mentor List</h6>
                <div class="d-flex gap-2 flex-wrap">
                    @if(\App\Helpers\RoleHelper::is_mentor() || \App\Helpers\RoleHelper::is_telecaller() || \App\Helpers\RoleHelper::is_team_lead() || \App\Helpers\RoleHelper::is_senior_manager() || \App\Helpers\RoleHelper::is_hod())
                    <a href="{{ route('admin.converted-leads.index') }}" class="{{ $mentorNavBtn('admin.converted-leads.index') }}">
                        <i class="ti ti-list"></i> All Converted Leads
                    </a>
                    @endif
                    <a href="{{ route('admin.mentor-bosse-converted-leads.index') }}" class="{{ $mentorNavBtn('admin.mentor-bosse-converted-leads.index') }}">
                        <i class="ti ti-user-star"></i> Board of Open Schooling and Skill Education Converted Mentor List
                    </a>
                    <a href="{{ route('admin.mentor-nios-converted-leads.index') }}" class="{{ $mentorNavBtn('admin.mentor-nios-converted-leads.index') }}">
                        <i class="ti ti-user-star"></i> National Institute of Open Schooling Converted Mentor List
                    </a>
                    <a href="{{ route('admin.mentor-ugpg-converted-leads.index') }}" class="{{ $mentorNavBtn('admin.mentor-ugpg-converted-leads.index') }}">
                        <i class="ti ti-user-star"></i> UG/PG Mentor Converted List
                    </a>
                    <a href="{{ route('admin.mentor-edumaster-converted-leads.index') }}" class="{{ $mentorNavBtn('admin.mentor-edumaster-converted-leads.index') }}">
                        <i class="ti ti-user-star"></i> EduMaster Mentor Converted List
                    </a>
                    <a href="{{ route('admin.mentor-eschool-converted-leads.index') }}" class="{{ $mentorNavBtn('admin.mentor-eschool-converted-leads.index') }}">
                        <i class="ti ti-user-star"></i> E-School Converted Mentor List
                    </a>
                    <a href="{{ route('admin.mentor-eduthanzeel-converted-leads.index') }}" class="{{ $mentorNavBtn('admin.mentor-eduthanzeel-converted-leads.index') }}">
                        <i class="ti ti-user-star"></i> Eduthanzeel Converted Mentor List
                    </a>
                    <a href="{{ route('admin.gmvss-mentor-converted-leads.index') }}" class="{{ $mentorNavBtn('admin.gmvss-mentor-converted-leads.index') }}">
                        <i class="ti ti-user-star"></i> Grameen Mukt Vidhyalayi Shiksha Sansthan Mentor List
                    </a>
                    <a href="{{ route('admin.digital-marketing-mentor-converted-leads.index') }}" class="{{ $mentorNavBtn('admin.digital-marketing-mentor-converted-leads.index') }}">
                        <i class="ti ti-user-star"></i> AI Integrated Digital Marketing Mentor List
                    </a>
                    <a href="{{ route('admin.data-science-mentor-converted-leads.index') }}" class="{{ $mentorNavBtn('admin.data-science-mentor-converted-leads.index') }}">
                        <i class="ti ti-user-star"></i> Data Science Course Mentor List
                    </a>
                    <a href="{{ route('admin.graphic-designing-mentor-converted-leads.index') }}" class="{{ $mentorNavBtn('admin.graphic-designing-mentor-converted-leads.index') }}">
                        <i class="ti ti-user-star"></i> Diploma in Graphic Designing Mentor List
                    </a>
                    <a href="{{ route('admin.ai-integrated-video-editing-mentor-converted-leads.index') }}" class="{{ $mentorNavBtn('admin.ai-integrated-video-editing-mentor-converted-leads.index') }}">
                        <i class="ti ti-user-star"></i> AI-Integrated Video Editing Mentor List
                    </a>
                    <a href="{{ route('admin.ai-integrated-videography-mentor-converted-leads.index') }}" class="{{ $mentorNavBtn('admin.ai-integrated-videography-mentor-converted-leads.index') }}">
                        <i class="ti ti-user-star"></i> AI-Integrated Videography Mentor List
                    </a>
                    <a href="{{ route('admin.ai-integrated-photography-mentor-converted-leads.index') }}" class="{{ $mentorNavBtn('admin.ai-integrated-photography-mentor-converted-leads.index') }}">
                        <i class="ti ti-user-star"></i> AI-Integrated Photography Mentor List
                    </a>
                    <a href="{{ route('admin.machine-learning-mentor-converted-leads.index') }}" class="{{ $mentorNavBtn('admin.machine-learning-mentor-converted-leads.index') }}">
                        <i class="ti ti-user-star"></i> Machine Learning Mentor List
                    </a>
                    <a href="{{ route('admin.medical-coding-mentor-converted-leads.index') }}" class="{{ $mentorNavBtn('admin.medical-coding-mentor-converted-leads.index') }}">
                        <i class="ti ti-user-star"></i> Certificate Course in Medical Coding Mentor List
                    </a>
                    <a href="{{ route('admin.python-mentor-converted-leads.index') }}" class="{{ $mentorNavBtn('admin.python-mentor-converted-leads.index') }}">
                        <i class="ti ti-user-star"></i> Python Mentor List
                    </a>
                    <a href="{{ route('admin.flutter-mentor-converted-leads.index') }}" class="{{ $mentorNavBtn('admin.flutter-mentor-converted-leads.index') }}">
                        <i class="ti ti-user-star"></i> Flutter Mentor List
                    </a>
                    <a href="{{ route('admin.rpa-mentor-converted-leads.index') }}" class="{{ $mentorNavBtn('admin.rpa-mentor-converted-leads.index') }}">
                        <i class="ti ti-user-star"></i> RPA Mentor List
                    </a>
                    <a href="{{ route('admin.junior-vlogger-mentor-converted-leads.index') }}" class="{{ $mentorNavBtn('admin.junior-vlogger-mentor-converted-leads.index') }}">
                        <i class="ti ti-user-star"></i> CreateX AI Converted Mentor List
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endif
<!-- [ Mentor List ] end -->
