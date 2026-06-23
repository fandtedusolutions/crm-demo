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
                        <i class="ti ti-headphones"></i> Board of Open Schooling and Skill Education Converted Support List
                    </a>
                    <a href="{{ route('admin.support-nios-converted-leads.index') }}" class="{{ $supportNavBtn('admin.support-nios-converted-leads.index') }}">
                        <i class="ti ti-headphones"></i> National Institute of Open Schooling Converted Support List
                    </a>
                    <a href="{{ route('admin.support-ugpg-converted-leads.index') }}" class="{{ $supportNavBtn('admin.support-ugpg-converted-leads.index') }}">
                        <i class="ti ti-headphones"></i> UG/PG Converted Support List
                    </a>
                    <a href="{{ route('admin.support-edumaster-converted-leads.index') }}" class="{{ $supportNavBtn('admin.support-edumaster-converted-leads.index') }}">
                        <i class="ti ti-headphones"></i> EduMaster Converted Support List
                    </a>
                    <a href="{{ route('admin.support-hotel-management-converted-leads.index') }}" class="{{ $supportNavBtn('admin.support-hotel-management-converted-leads.index') }}">
                        <i class="ti ti-headphones"></i> Hotel Management Converted Support List
                    </a>
                    <a href="{{ route('admin.support-gmvss-converted-leads.index') }}" class="{{ $supportNavBtn('admin.support-gmvss-converted-leads.index') }}">
                        <i class="ti ti-headphones"></i> Grameen Mukt Vidhyalayi Shiksha Sansthan Converted Support List
                    </a>
                    <a href="{{ route('admin.support-digital-marketing-converted-leads.index') }}" class="{{ $supportNavBtn('admin.support-digital-marketing-converted-leads.index') }}">
                        <i class="ti ti-headphones"></i> AI Integrated Digital Marketing Converted Support List
                    </a>
                    <a href="{{ route('admin.support-diploma-in-data-science-converted-leads.index') }}" class="{{ $supportNavBtn('admin.support-diploma-in-data-science-converted-leads.index') }}">
                        <i class="ti ti-headphones"></i> Diploma in Data Science Converted Support List
                    </a>
                    <a href="{{ route('admin.support-web-development-converted-leads.index') }}" class="{{ $supportNavBtn('admin.support-web-development-converted-leads.index') }}">
                        <i class="ti ti-headphones"></i> Web Development & Designing Converted Support List
                    </a>
                    <a href="{{ route('admin.support-vibe-coding-converted-leads.index') }}" class="{{ $supportNavBtn('admin.support-vibe-coding-converted-leads.index') }}">
                        <i class="ti ti-headphones"></i> Vibe Coding Converted Support List
                    </a>
                    <a href="{{ route('admin.support-graphic-designing-converted-leads.index') }}" class="{{ $supportNavBtn('admin.support-graphic-designing-converted-leads.index') }}">
                        <i class="ti ti-headphones"></i> Diploma in Graphic Designing Converted Support List
                    </a>
                    <a href="{{ route('admin.support-ai-integrated-video-editing-converted-leads.index') }}" class="{{ $supportNavBtn('admin.support-ai-integrated-video-editing-converted-leads.index') }}">
                        <i class="ti ti-headphones"></i> AI-Integrated Video Editing Converted Support List
                    </a>
                    <a href="{{ route('admin.support-ai-integrated-videography-converted-leads.index') }}" class="{{ $supportNavBtn('admin.support-ai-integrated-videography-converted-leads.index') }}">
                        <i class="ti ti-headphones"></i> AI-Integrated Videography Converted Support List
                    </a>
                    <a href="{{ route('admin.support-ai-integrated-photography-converted-leads.index') }}" class="{{ $supportNavBtn('admin.support-ai-integrated-photography-converted-leads.index') }}">
                        <i class="ti ti-headphones"></i> AI-Integrated Photography Converted Support List
                    </a>
                    <a href="{{ route('admin.support-machine-learning-converted-leads.index') }}" class="{{ $supportNavBtn('admin.support-machine-learning-converted-leads.index') }}">
                        <i class="ti ti-headphones"></i> Diploma in Machine Learning Converted Support List
                    </a>
                    <a href="{{ route('admin.support-flutter-converted-leads.index') }}" class="{{ $supportNavBtn('admin.support-flutter-converted-leads.index') }}">
                        <i class="ti ti-headphones"></i> Flutter Converted Support List
                    </a>
                    <a href="{{ route('admin.support-eduthanzeel-converted-leads.index') }}" class="{{ $supportNavBtn('admin.support-eduthanzeel-converted-leads.index') }}">
                        <i class="ti ti-headphones"></i> Eduthanzeel Converted Support List
                    </a>
                    <a href="{{ route('admin.support-e-school-converted-leads.index') }}" class="{{ $supportNavBtn('admin.support-e-school-converted-leads.index') }}">
                        <i class="ti ti-headphones"></i> E-School Converted Support List
                    </a>
                    <a href="{{ route('admin.support-junior-vlogger-converted-leads.index') }}" class="{{ $supportNavBtn('admin.support-junior-vlogger-converted-leads.index') }}">
                        <i class="ti ti-headphones"></i> CreateX AI - Course Support List
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endif
<!-- [ Support List ] end -->
