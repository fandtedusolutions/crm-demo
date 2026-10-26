@php
    $supportNavBtn = function (string $route): string {
        return request()->routeIs($route) ? 'btn btn-primary active' : 'btn btn-outline-primary';
    };
@endphp
<!-- [ Support List ] start -->
@if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_support_team())
<div class="row mb-3">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h6 class="mb-3">Support List</h6>
                <div class="d-flex gap-2 flex-wrap">
                    @if(\App\Helpers\RoleHelper::is_support_team())
                    <a href="{{ route('admin.converted-leads.index') }}" class="{{ $supportNavBtn('admin.converted-leads.index') }}">
                        <i class="ti ti-list"></i> All Converted Leads
                    </a>
                    @endif
                    <a href="{{ route('admin.support-bosse-converted-leads.index') }}" class="{{ $supportNavBtn('admin.support-bosse-converted-leads.index') }}">
                        <i class="ti ti-headphones"></i> {{ course_title(2) }} Converted Support List
                    </a>
                    <a href="{{ route('admin.support-nios-converted-leads.index') }}" class="{{ $supportNavBtn('admin.support-nios-converted-leads.index') }}">
                        <i class="ti ti-headphones"></i> {{ course_title(1) }} Converted Support List
                    </a>
                    <a href="{{ route('admin.support-ugpg-converted-leads.index') }}" class="{{ $supportNavBtn('admin.support-ugpg-converted-leads.index') }}">
                        <i class="ti ti-headphones"></i> {{ course_title(9) }} Converted Support List
                    </a>
                    <a href="{{ route('admin.support-edumaster-converted-leads.index') }}" class="{{ $supportNavBtn('admin.support-edumaster-converted-leads.index') }}">
                        <i class="ti ti-headphones"></i> {{ course_title(23) }} Converted Support List
                    </a>
                    <a href="{{ route('admin.support-hotel-management-converted-leads.index') }}" class="{{ $supportNavBtn('admin.support-hotel-management-converted-leads.index') }}">
                        <i class="ti ti-headphones"></i> {{ course_title(8) }} Converted Support List
                    </a>
                    <a href="{{ route('admin.support-medical-coding-converted-leads.index') }}" class="{{ $supportNavBtn('admin.support-medical-coding-converted-leads.index') }}">
                        <i class="ti ti-headphones"></i> {{ course_title(3) }} Converted Support List
                    </a>
                    <a href="{{ route('admin.support-hospital-administration-converted-leads.index') }}" class="{{ $supportNavBtn('admin.support-hospital-administration-converted-leads.index') }}">
                        <i class="ti ti-headphones"></i> {{ course_title(4) }} Converted Support List
                    </a>
                    <a href="{{ route('admin.support-gmvss-converted-leads.index') }}" class="{{ $supportNavBtn('admin.support-gmvss-converted-leads.index') }}">
                        <i class="ti ti-headphones"></i> {{ course_title(16) }} Converted Support List
                    </a>
                    <a href="{{ route('admin.support-digital-marketing-converted-leads.index') }}" class="{{ $supportNavBtn('admin.support-digital-marketing-converted-leads.index') }}">
                        <i class="ti ti-headphones"></i> {{ course_title(11) }} Converted Support List
                    </a>
                    <a href="{{ route('admin.support-diploma-in-data-science-converted-leads.index') }}" class="{{ $supportNavBtn('admin.support-diploma-in-data-science-converted-leads.index') }}">
                        <i class="ti ti-headphones"></i> {{ course_title(12) }} Converted Support List
                    </a>
                    <a href="{{ route('admin.support-data-analytics-with-gen-ai-converted-leads.index') }}" class="{{ $supportNavBtn('admin.support-data-analytics-with-gen-ai-converted-leads.index') }}">
                        <i class="ti ti-headphones"></i> {{ course_title(37) }} Converted Support List
                    </a>
                    <a href="{{ route('admin.support-web-development-converted-leads.index') }}" class="{{ $supportNavBtn('admin.support-web-development-converted-leads.index') }}">
                        <i class="ti ti-headphones"></i> {{ course_title(13) }} Converted Support List
                    </a>
                    <a href="{{ route('admin.support-vibe-coding-converted-leads.index') }}" class="{{ $supportNavBtn('admin.support-vibe-coding-converted-leads.index') }}">
                        <i class="ti ti-headphones"></i> {{ course_title(14) }} Converted Support List
                    </a>
                    <a href="{{ route('admin.support-graphic-designing-converted-leads.index') }}" class="{{ $supportNavBtn('admin.support-graphic-designing-converted-leads.index') }}">
                        <i class="ti ti-headphones"></i> {{ course_title(15) }} Converted Support List
                    </a>
                    <a href="{{ route('admin.support-ai-integrated-video-editing-converted-leads.index') }}" class="{{ $supportNavBtn('admin.support-ai-integrated-video-editing-converted-leads.index') }}">
                        <i class="ti ti-headphones"></i> {{ course_title(30) }} Converted Support List
                    </a>
                    <a href="{{ route('admin.support-ai-integrated-videography-converted-leads.index') }}" class="{{ $supportNavBtn('admin.support-ai-integrated-videography-converted-leads.index') }}">
                        <i class="ti ti-headphones"></i> {{ course_title(31) }} Converted Support List
                    </a>
                    <a href="{{ route('admin.support-ai-integrated-photography-converted-leads.index') }}" class="{{ $supportNavBtn('admin.support-ai-integrated-photography-converted-leads.index') }}">
                        <i class="ti ti-headphones"></i> {{ course_title(32) }} Converted Support List
                    </a>
                    <a href="{{ route('admin.support-machine-learning-converted-leads.index') }}" class="{{ $supportNavBtn('admin.support-machine-learning-converted-leads.index') }}">
                        <i class="ti ti-headphones"></i> {{ course_title(20) }} Converted Support List
                    </a>
                    <a href="{{ route('admin.support-flutter-converted-leads.index') }}" class="{{ $supportNavBtn('admin.support-flutter-converted-leads.index') }}">
                        <i class="ti ti-headphones"></i> {{ course_title(21) }} Converted Support List
                    </a>
                    <a href="{{ route('admin.support-eduthanzeel-converted-leads.index') }}" class="{{ $supportNavBtn('admin.support-eduthanzeel-converted-leads.index') }}">
                        <i class="ti ti-headphones"></i> {{ course_title(6) }} Converted Support List
                    </a>
                    <a href="{{ route('admin.support-e-school-converted-leads.index') }}" class="{{ $supportNavBtn('admin.support-e-school-converted-leads.index') }}">
                        <i class="ti ti-headphones"></i> {{ course_title(5) }} Converted Support List
                    </a>
                    <a href="{{ route('admin.support-junior-vlogger-converted-leads.index') }}" class="{{ $supportNavBtn('admin.support-junior-vlogger-converted-leads.index') }}">
                        <i class="ti ti-headphones"></i> {{ course_title(25) }} Converted Support List
                    </a>
                    <a href="{{ route('admin.support-robo-vibe-converted-leads.index') }}" class="{{ $supportNavBtn('admin.support-robo-vibe-converted-leads.index') }}">
                        <i class="ti ti-headphones"></i> {{ course_title(33) }} Converted Support List
                    </a>
                    <a href="{{ route('admin.support-prompt-engineering-converted-leads.index') }}" class="{{ $supportNavBtn('admin.support-prompt-engineering-converted-leads.index') }}">
                        <i class="ti ti-headphones"></i> {{ course_title(34) }} Converted Support List
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endif
<!-- [ Support List ] end -->
