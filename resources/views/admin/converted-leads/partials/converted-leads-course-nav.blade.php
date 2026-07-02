@php
    $convertedNavBtn = function (string $route, bool $allLeads = false): string {
        $active = request()->routeIs($route);
        if ($allLeads && request()->routeIs('admin.converted-leads.index') && !request('course_id')) {
            $active = true;
        }

        return $active ? 'btn btn-primary active' : 'btn btn-outline-primary';
    };
@endphp
<!-- [ Course Filter Buttons ] start -->
@if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_academic_assistant() || \App\Helpers\RoleHelper::is_finance())
<div class="row mb-3">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h6 class="mb-3">Filter by Course</h6>
                <div class="d-flex gap-2 flex-wrap">
                    <a href="{{ route('admin.converted-leads.index') }}" class="{{ $convertedNavBtn('admin.converted-leads.index', true) }}">
                        <i class="ti ti-list"></i> All Converted Leads
                    </a>
                    <a href="{{ route('admin.nios-converted-leads.index') }}" class="{{ $convertedNavBtn('admin.nios-converted-leads.index') }}">
                        <i class="ti ti-school"></i> National Institute of Open Schooling Converted Leads
                    </a>
                    <a href="{{ route('admin.bosse-converted-leads.index') }}" class="{{ $convertedNavBtn('admin.bosse-converted-leads.index') }}">
                        <i class="ti ti-school-2"></i> Board of Open Schooling and Skill Education Converted Leads
                    </a>
                    <a href="{{ route('admin.ugpg-converted-leads.index') }}" class="{{ $convertedNavBtn('admin.ugpg-converted-leads.index') }}">
                        <i class="ti ti-graduation"></i> UG/PG Converted Leads
                    </a>
                    <a href="{{ route('admin.edumaster-converted-leads.index') }}" class="{{ $convertedNavBtn('admin.edumaster-converted-leads.index') }}">
                        <i class="ti ti-graduation"></i> EduMaster Converted Leads
                    </a>
                    <a href="{{ route('admin.hotel-management-converted-leads.index') }}" class="{{ $convertedNavBtn('admin.hotel-management-converted-leads.index') }}">
                        <i class="ti ti-building"></i> Hotel Management Converted Leads
                    </a>
                    <a href="{{ route('admin.gmvss-converted-leads.index') }}" class="{{ $convertedNavBtn('admin.gmvss-converted-leads.index') }}">
                        <i class="ti ti-certificate"></i> Grameen Mukt Vidhyalayi Shiksha Sansthan Converted Leads
                    </a>
                    <a href="{{ route('admin.digital-marketing-converted-leads.index') }}" class="{{ $convertedNavBtn('admin.digital-marketing-converted-leads.index') }}">
                        <i class="ti ti-marketing"></i> AI Integrated Digital Marketing Converted Leads
                    </a>
                    <a href="{{ route('admin.diploma-in-data-science-converted-leads.index') }}" class="{{ $convertedNavBtn('admin.diploma-in-data-science-converted-leads.index') }}">
                        <i class="ti ti-database"></i> Diploma in Data Science Converted Leads
                    </a>
                    <a href="{{ route('admin.web-development-converted-leads.index') }}" class="{{ $convertedNavBtn('admin.web-development-converted-leads.index') }}">
                        <i class="ti ti-world"></i> Web Development & Designing Converted Leads
                    </a>
                    <a href="{{ route('admin.vibe-coding-converted-leads.index') }}" class="{{ $convertedNavBtn('admin.vibe-coding-converted-leads.index') }}">
                        <i class="ti ti-device-desktop"></i> Vibe Coding Converted Leads
                    </a>
                    <a href="{{ route('admin.graphic-designing-converted-leads.index') }}" class="{{ $convertedNavBtn('admin.graphic-designing-converted-leads.index') }}">
                        <i class="ti ti-palette"></i> Diploma in Graphic Designing Converted Leads
                    </a>
                    <a href="{{ route('admin.ai-integrated-video-editing-converted-leads.index') }}" class="{{ $convertedNavBtn('admin.ai-integrated-video-editing-converted-leads.index') }}">
                        <i class="ti ti-video"></i> AI-Integrated Video Editing Converted Leads
                    </a>
                    <a href="{{ route('admin.ai-integrated-videography-converted-leads.index') }}" class="{{ $convertedNavBtn('admin.ai-integrated-videography-converted-leads.index') }}">
                        <i class="ti ti-video-plus"></i> AI-Integrated Videography Converted Leads
                    </a>
                    <a href="{{ route('admin.ai-integrated-photography-converted-leads.index') }}" class="{{ $convertedNavBtn('admin.ai-integrated-photography-converted-leads.index') }}">
                        <i class="ti ti-camera"></i> AI-Integrated Photography Converted Leads
                    </a>
                    <a href="{{ route('admin.machine-learning-converted-leads.index') }}" class="{{ $convertedNavBtn('admin.machine-learning-converted-leads.index') }}">
                        <i class="ti ti-brain"></i> Diploma in Machine Learning Converted Leads
                    </a>
                    <a href="{{ route('admin.flutter-converted-leads.index') }}" class="{{ $convertedNavBtn('admin.flutter-converted-leads.index') }}">
                        <i class="ti ti-device-mobile"></i> Flutter Converted Leads
                    </a>
                    <a href="{{ route('admin.eduthanzeel-converted-leads.index') }}" class="{{ $convertedNavBtn('admin.eduthanzeel-converted-leads.index') }}">
                        <i class="ti ti-school"></i> Eduthanzeel Converted Leads
                    </a>
                    <a href="{{ route('admin.e-school-converted-leads.index') }}" class="{{ $convertedNavBtn('admin.e-school-converted-leads.index') }}">
                        <i class="ti ti-device-laptop"></i> E-School Converted Leads
                    </a>
                    <a href="{{ route('admin.junior-vlogger-converted-leads.index') }}" class="{{ $convertedNavBtn('admin.junior-vlogger-converted-leads.index') }}">
                        <i class="ti ti-video"></i> CreateX AI Converted Leads
                    </a>
                    <a href="{{ route('admin.robo-vibe-converted-leads.index') }}" class="{{ $convertedNavBtn('admin.robo-vibe-converted-leads.index') }}">
                        <i class="ti ti-robot"></i> Robo Vibe Converted Leads
                    </a>
                    <a href="{{ route('admin.prompt-engineering-converted-leads.index') }}" class="{{ $convertedNavBtn('admin.prompt-engineering-converted-leads.index') }}">
                        <i class="ti ti-brain"></i> Prompt Engineering Converted Leads
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endif
<!-- [ Course Filter Buttons ] end -->
