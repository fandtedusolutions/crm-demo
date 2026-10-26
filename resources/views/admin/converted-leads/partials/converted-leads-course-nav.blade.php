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
                        <i class="ti ti-school"></i> {{ course_title(1) }} Converted Leads
                    </a>
                    <a href="{{ route('admin.bosse-converted-leads.index') }}" class="{{ $convertedNavBtn('admin.bosse-converted-leads.index') }}">
                        <i class="ti ti-school-2"></i> {{ course_title(2) }} Converted Leads
                    </a>
                    <a href="{{ route('admin.ugpg-converted-leads.index') }}" class="{{ $convertedNavBtn('admin.ugpg-converted-leads.index') }}">
                        <i class="ti ti-graduation"></i> {{ course_title(9) }} Converted Leads
                    </a>
                    <a href="{{ route('admin.edumaster-converted-leads.index') }}" class="{{ $convertedNavBtn('admin.edumaster-converted-leads.index') }}">
                        <i class="ti ti-graduation"></i> {{ course_title(23) }} Converted Leads
                    </a>
                    <a href="{{ route('admin.hotel-management-converted-leads.index') }}" class="{{ $convertedNavBtn('admin.hotel-management-converted-leads.index') }}">
                        <i class="ti ti-building"></i> {{ course_title(8) }} Converted Leads
                    </a>
                    <a href="{{ route('admin.medical-coding-converted-leads.index') }}" class="{{ $convertedNavBtn('admin.medical-coding-converted-leads.index') }}">
                        <i class="ti ti-stethoscope"></i> {{ course_title(3) }} Converted Leads
                    </a>
                    <a href="{{ route('admin.hospital-administration-converted-leads.index') }}" class="{{ $convertedNavBtn('admin.hospital-administration-converted-leads.index') }}">
                        <i class="ti ti-building-hospital"></i> {{ course_title(4) }} Converted Leads
                    </a>
                    <a href="{{ route('admin.ai-integrated-sales-marketing-converted-leads.index') }}" class="{{ $convertedNavBtn('admin.ai-integrated-sales-marketing-converted-leads.index') }}">
                        <i class="ti ti-chart-arrows"></i> {{ course_title(29) }} Converted Leads
                    </a>
                    <a href="{{ route('admin.gmvss-converted-leads.index') }}" class="{{ $convertedNavBtn('admin.gmvss-converted-leads.index') }}">
                        <i class="ti ti-certificate"></i> {{ course_title(16) }} Converted Leads
                    </a>
                    <a href="{{ route('admin.digital-marketing-converted-leads.index') }}" class="{{ $convertedNavBtn('admin.digital-marketing-converted-leads.index') }}">
                        <i class="ti ti-marketing"></i> {{ course_title(11) }} Converted Leads
                    </a>
                    <a href="{{ route('admin.diploma-in-data-science-converted-leads.index') }}" class="{{ $convertedNavBtn('admin.diploma-in-data-science-converted-leads.index') }}">
                        <i class="ti ti-database"></i> {{ course_title(12) }} Converted Leads
                    </a>
                    <a href="{{ route('admin.data-analytics-with-gen-ai-converted-leads.index') }}" class="{{ $convertedNavBtn('admin.data-analytics-with-gen-ai-converted-leads.index') }}">
                        <i class="ti ti-database"></i> {{ course_title(37) }} Converted Leads
                    </a>
                    <a href="{{ route('admin.web-development-converted-leads.index') }}" class="{{ $convertedNavBtn('admin.web-development-converted-leads.index') }}">
                        <i class="ti ti-world"></i> {{ course_title(13) }} Converted Leads
                    </a>
                    <a href="{{ route('admin.vibe-coding-converted-leads.index') }}" class="{{ $convertedNavBtn('admin.vibe-coding-converted-leads.index') }}">
                        <i class="ti ti-device-desktop"></i> {{ course_title(14) }} Converted Leads
                    </a>
                    <a href="{{ route('admin.graphic-designing-converted-leads.index') }}" class="{{ $convertedNavBtn('admin.graphic-designing-converted-leads.index') }}">
                        <i class="ti ti-palette"></i> {{ course_title(15) }} Converted Leads
                    </a>
                    <a href="{{ route('admin.ai-integrated-video-editing-converted-leads.index') }}" class="{{ $convertedNavBtn('admin.ai-integrated-video-editing-converted-leads.index') }}">
                        <i class="ti ti-video"></i> {{ course_title(30) }} Converted Leads
                    </a>
                    <a href="{{ route('admin.ai-integrated-videography-converted-leads.index') }}" class="{{ $convertedNavBtn('admin.ai-integrated-videography-converted-leads.index') }}">
                        <i class="ti ti-video-plus"></i> {{ course_title(31) }} Converted Leads
                    </a>
                    <a href="{{ route('admin.ai-integrated-photography-converted-leads.index') }}" class="{{ $convertedNavBtn('admin.ai-integrated-photography-converted-leads.index') }}">
                        <i class="ti ti-camera"></i> {{ course_title(32) }} Converted Leads
                    </a>
                    <a href="{{ route('admin.machine-learning-converted-leads.index') }}" class="{{ $convertedNavBtn('admin.machine-learning-converted-leads.index') }}">
                        <i class="ti ti-brain"></i> {{ course_title(20) }} Converted Leads
                    </a>
                    <a href="{{ route('admin.flutter-converted-leads.index') }}" class="{{ $convertedNavBtn('admin.flutter-converted-leads.index') }}">
                        <i class="ti ti-device-mobile"></i> {{ course_title(21) }} Converted Leads
                    </a>
                    <a href="{{ route('admin.eduthanzeel-converted-leads.index') }}" class="{{ $convertedNavBtn('admin.eduthanzeel-converted-leads.index') }}">
                        <i class="ti ti-school"></i> {{ course_title(6) }} Converted Leads
                    </a>
                    <a href="{{ route('admin.e-school-converted-leads.index') }}" class="{{ $convertedNavBtn('admin.e-school-converted-leads.index') }}">
                        <i class="ti ti-device-laptop"></i> {{ course_title(5) }} Converted Leads
                    </a>
                    <a href="{{ route('admin.junior-vlogger-converted-leads.index') }}" class="{{ $convertedNavBtn('admin.junior-vlogger-converted-leads.index') }}">
                        <i class="ti ti-video"></i> {{ course_title(25) }} Converted Leads
                    </a>
                    <a href="{{ route('admin.robo-vibe-converted-leads.index') }}" class="{{ $convertedNavBtn('admin.robo-vibe-converted-leads.index') }}">
                        <i class="ti ti-robot"></i> {{ course_title(33) }} Converted Leads
                    </a>
                    <a href="{{ route('admin.prompt-engineering-converted-leads.index') }}" class="{{ $convertedNavBtn('admin.prompt-engineering-converted-leads.index') }}">
                        <i class="ti ti-brain"></i> {{ course_title(34) }} Converted Leads
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endif
<!-- [ Course Filter Buttons ] end -->
