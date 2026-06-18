<!-- [ Sidebar Menu ] start -->
<nav class="pc-sidebar">
    <div class="navbar-wrapper">
        <div class="m-header">
            <a href="{{ route('dashboard') }}" class="b-brand text-primary">
                <!-- ========   Change your logo from here   ============ -->
                <img src="{{ asset('storage/logo.png') }}" class="img-fluid logo-lg" alt="logo" 
                     style="height: 200px !important; width: 100px !important; object-fit: contain; padding: 10px !important;">
            </a>
        </div>
        <div class="navbar-content">
            <ul class="pc-navbar">
                <li class="pc-item pc-caption">
                    <label>Navigation</label>
                </li>
                @if(has_permission('dashboard/index'))
                <li class="pc-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <a href="{{ route('dashboard') }}" class="pc-link">
                        <span class="pc-micon">
                            <i class="ti ti-dashboard"></i>
                        </span>
                        <span class="pc-mtext">Dashboard</span>
                    </a>
                </li>
                @endif
                
                @if(has_permission('leads/index'))
                <li class="pc-item {{ request()->routeIs('leads.index') ? 'active' : '' }}">
                    <a href="{{ route('leads.index') }}" class="pc-link">
                        <span class="pc-micon">
                            <i class="ti ti-users"></i>
                        </span>
                        <span class="pc-mtext">Leads</span>
                    </a>
                </li>
                @endif

                @if(has_permission('leads/pullbacked'))
                <li class="pc-item {{ request()->routeIs('admin.leads.pullbacked') ? 'active' : '' }}">
                    <a href="{{ route('admin.leads.pullbacked') }}" class="pc-link">
                        <span class="pc-micon">
                            <i class="ti ti-arrow-back-up"></i>
                        </span>
                        <span class="pc-mtext">Pullbacked Leads</span>
                    </a>
                </li>
                @endif

                @if(has_permission('leads/followup'))
                <li class="pc-item {{ request()->routeIs('leads.followup') ? 'active' : '' }}">
                    <a href="{{ route('leads.followup') }}" class="pc-link">
                        <span class="pc-micon">
                            <i class="ti ti-clock"></i>
                        </span>
                        <span class="pc-mtext">Follow-up Leads</span>
                    </a>
                </li>
                @endif

                @if(has_permission('leads/registration-form-submitted'))
                <li class="pc-item {{ request()->routeIs('leads.registration-form-submitted') ? 'active' : '' }}">
                    <a href="{{ route('leads.registration-form-submitted') }}" class="pc-link">
                        <span class="pc-micon">
                            <i class="ti ti-file-text"></i>
                        </span>
                        <span class="pc-mtext">Registration Form Submitted</span>
                    </a>
                </li>
                @endif

                {{-- Converted Leads Section --}}
                @if(
                    has_permission('admin/converted-leads/index')
                    || \App\Helpers\RoleHelper::is_support_team()
                    || \App\Helpers\RoleHelper::is_hod()
                )
                <li class="pc-item {{ (request()->routeIs('admin.converted-leads.*') || request()->routeIs('admin.support-*-converted-leads.*')) && !request()->routeIs('admin.support-ajax-converted-leads.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.converted-leads.index') }}" class="pc-link">
                        <span class="pc-micon">
                            <i class="ti ti-user-check"></i>
                        </span>
                        <span class="pc-mtext">Converted Leads</span>
                    </a>
                </li>
                <li class="pc-item {{ request()->routeIs('admin.support-ajax-converted-leads.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.support-ajax-converted-leads.index') }}" class="pc-link">
                        <span class="pc-micon">
                            <i class="ti ti-headphones"></i>
                        </span>
                        <span class="pc-mtext">Support List</span>
                    </a>
                </li>
                @endif
                @if(has_permission('admin/placement-list/index'))
                    <li class="pc-item {{ request()->routeIs('admin.placement-list.*') ? 'active' : '' }}">
                        <a href="{{ route('admin.placement-list.index') }}" class="pc-link">
                            <span class="pc-micon">
                                <i class="ti ti-briefcase"></i>
                            </span>
                            <span class="pc-mtext">Placement List</span>
                        </a>
                    </li>
                @endif

                @if(has_permission('admin/post-sales-converted-leads/index'))
                <li class="pc-item {{ request()->routeIs('admin.post-sales.converted-leads.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.post-sales.converted-leads.index') }}" class="pc-link">
                        <span class="pc-micon">
                            <i class="ti ti-headset"></i>
                        </span>
                        <span class="pc-mtext">Post-sales Converted Students</span>
                    </a>
                </li>
                @endif

                {{-- Payments Overview (hidden for B2B telecallers) --}}
                @php
                    $currentUser = \App\Helpers\AuthHelper::getCurrentUser();
                    $isB2BTelecaller = \App\Helpers\RoleHelper::is_telecaller() && $currentUser && $currentUser->is_b2b;
                @endphp
                @if(has_permission('admin/payments/list') && !$isB2BTelecaller)
                <li class="pc-item {{ request()->routeIs('admin.payments.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.payments.list') }}" class="pc-link">
                        <span class="pc-micon">
                            <i class="ti ti-cash"></i>
                        </span>
                        <span class="pc-mtext">Payments</span>
                    </a>
                </li>
                @endif

                {{-- D2D Form - Above User Management --}}
                @if(has_permission('admin/marketing/d2d-form'))
                <li class="pc-item {{ request()->routeIs('admin.marketing.d2d-form') || request()->routeIs('admin.marketing.d2d-submit') ? 'active' : '' }}">
                    <a href="{{ route('admin.marketing.d2d-form') }}" class="pc-link">
                        <span class="pc-micon">
                            <i class="ti ti-clipboard"></i>
                        </span>
                        <span class="pc-mtext">D2D Form</span>
                    </a>
                </li>
                @endif
                {{-- Marketing Leads - Above User Management --}}
                @if(has_permission('admin/marketing/marketing-leads'))
                <li class="pc-item {{ request()->routeIs('admin.marketing.marketing-leads') ? 'active' : '' }}">
                    <a href="{{ route('admin.marketing.marketing-leads') }}" class="pc-link">
                        <span class="pc-micon">
                            <i class="ti ti-list"></i>
                        </span>
                        <span class="pc-mtext">Marketing Leads</span>
                    </a>
                </li>
                @endif

                @if(has_permission('admin/online-teaching-faculties/index'))
                <li class="pc-item {{ request()->routeIs('admin.online-teaching-faculties.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.online-teaching-faculties.index') }}" class="pc-link">
                        <span class="pc-micon">
                            <i class="ti ti-school"></i>
                        </span>
                        <span class="pc-mtext">Online Teaching Faculty</span>
                    </a>
                </li>
                @endif

                @if(has_permission('admin/call-analytics/index'))
                <li class="pc-item {{ request()->routeIs('admin.call-analytics.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.call-analytics.index') }}" class="pc-link">
                        <span class="pc-micon">
                            <i class="ti ti-phone-call"></i>
                        </span>
                        <span class="pc-mtext">Call Analytics</span>
                    </a>
                </li>
                @endif

                {{-- User Management Section --}}
                @if(has_permission('admin/telecallers/index') || has_permission('admin/marketing/index') || has_permission('admin/admins/index') || has_permission('admin/admission-counsellors/index') || has_permission('admin/academic-assistants/index') || has_permission('admin/teachers/index') || has_permission('admin/support-team/index') || has_permission('admin/mentor/index') || has_permission('admin/faculty/index') || has_permission('admin/finance/index') || has_permission('admin/hod/index') || has_permission('admin/placement-officers/index'))
                <li class="pc-item pc-caption">
                    <label>User Management</label>
                </li>
                @if(has_permission('admin/telecallers/index'))
                <li class="pc-item {{ request()->routeIs('admin.telecallers.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.telecallers.index') }}" class="pc-link">
                        <span class="pc-micon">
                            <i class="ti ti-phone"></i>
                        </span>
                        <span class="pc-mtext">Telecallers</span>
                    </a>
                </li>
                @endif
                @if(has_permission('admin/marketing/index'))
                <li class="pc-item {{ (request()->routeIs('admin.marketing.index') || request()->routeIs('admin.marketing.add') || request()->routeIs('admin.marketing.edit') || request()->routeIs('admin.marketing.submit') || request()->routeIs('admin.marketing.update') || request()->routeIs('admin.marketing.delete') || request()->routeIs('admin.marketing.change-password') || request()->routeIs('admin.marketing.update-password')) && !request()->routeIs('admin.marketing.d2d-form') && !request()->routeIs('admin.marketing.d2d-submit') && !request()->routeIs('admin.marketing.marketing-leads') && !request()->routeIs('admin.marketing.marketing-leads.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.marketing.index') }}" class="pc-link">
                        <span class="pc-micon">
                            <i class="ti ti-briefcase"></i>
                        </span>
                        <span class="pc-mtext">Marketing</span>
                    </a>
                </li>
                @endif
                @if(has_permission('admin/teachers/index'))
                <li class="pc-item {{ request()->routeIs('admin.teachers.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.teachers.index') }}" class="pc-link">
                        <span class="pc-micon">
                            <i class="ti ti-school"></i>
                        </span>
                        <span class="pc-mtext">Teachers</span>
                    </a>
                </li>
                @endif
                @if(has_permission('admin/general-managers/index'))
                <li class="pc-item {{ request()->routeIs('admin.general-managers.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.general-managers.index') }}" class="pc-link">
                        <span class="pc-micon">
                            <i class="ti ti-users"></i>
                        </span>
                        <span class="pc-mtext">General Managers</span>
                    </a>
                </li>
                @endif
                @if(has_permission('admin/auditors/index'))
                <li class="pc-item {{ request()->routeIs('admin.auditors.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.auditors.index') }}" class="pc-link">
                        <span class="pc-micon">
                            <i class="ti ti-clipboard-list"></i>
                        </span>
                        <span class="pc-mtext">Auditors</span>
                    </a>
                </li>
                @endif
                @if(has_permission('admin/placement-officers/index'))
                <li class="pc-item {{ request()->routeIs('admin.placement-officers.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.placement-officers.index') }}" class="pc-link">
                        <span class="pc-micon">
                            <i class="ti ti-briefcase"></i>
                        </span>
                        <span class="pc-mtext">Placement Officers</span>
                    </a>
                </li>
                @endif
                @if(has_permission('admin/admission-counsellors/index'))
                <li class="pc-item {{ request()->routeIs('admin.admission-counsellors.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.admission-counsellors.index') }}" class="pc-link">
                        <span class="pc-micon">
                            <i class="ti ti-user-plus"></i>
                        </span>
                        <span class="pc-mtext">Admission Counsellors</span>
                    </a>
                </li>
                @endif
                @if(has_permission('admin/academic-assistants/index'))
                <li class="pc-item {{ request()->routeIs('admin.academic-assistants.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.academic-assistants.index') }}" class="pc-link">
                        <span class="pc-micon">
                            <i class="ti ti-user-check"></i>
                        </span>
                        <span class="pc-mtext">Academic Assistants</span>
                    </a>
                </li>
                @endif
                @if(has_permission('admin/finance/index'))
                <li class="pc-item {{ request()->routeIs('admin.finance.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.finance.index') }}" class="pc-link">
                        <span class="pc-micon">
                            <i class="ti ti-currency-dollar"></i>
                        </span>
                        <span class="pc-mtext">Finance</span>
                    </a>
                </li>
                @endif
                @if(has_permission('admin/hod/index'))
                <li class="pc-item {{ request()->routeIs('admin.hod.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.hod.index') }}" class="pc-link">
                        <span class="pc-micon">
                            <i class="ti ti-user-star"></i>
                        </span>
                        <span class="pc-mtext">HOD</span>
                    </a>
                </li>
                @endif
                @if(has_permission('admin/support-team/index'))
                <li class="pc-item {{ request()->routeIs('admin.support-team.*') || request()->routeIs('admin.support-team-*-converted-leads.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.support-team.index') }}" class="pc-link">
                        <span class="pc-micon">
                            <i class="ti ti-headset"></i>
                        </span>
                        <span class="pc-mtext">Support Team</span>
                    </a>
                </li>
                @endif
                @if(has_permission('admin/mentor/index'))
                <li class="pc-item {{ request()->routeIs('admin.mentor.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.mentor.index') }}" class="pc-link">
                        <span class="pc-micon">
                            <i class="ti ti-user-check"></i>
                        </span>
                        <span class="pc-mtext">Mentor</span>
                    </a>
                </li>
                @endif
                @if(has_permission('admin/faculty/index'))
                <li class="pc-item {{ request()->routeIs('admin.faculty.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.faculty.index') }}" class="pc-link">
                        <span class="pc-micon">
                            <i class="ti ti-school"></i>
                        </span>
                        <span class="pc-mtext">Faculty</span>
                    </a>
                </li>
                @endif
                @if(has_permission('admin/post-sales/index'))
                <li class="pc-item {{ request()->routeIs('admin.post-sales.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.post-sales.index') }}" class="pc-link">
                        <span class="pc-micon">
                            <i class="ti ti-headset"></i>
                        </span>
                        <span class="pc-mtext">Post-sales</span>
                    </a>
                </li>
                @endif
                @if(has_permission('admin/admins/index'))
                <li class="pc-item {{ request()->routeIs('admin.admins.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.admins.index') }}" class="pc-link">
                        <span class="pc-micon">
                            <i class="ti ti-shield"></i>
                        </span>
                        <span class="pc-mtext">Admin Users</span>
                    </a>
                </li>
                @endif
                @endif
                
                
                {{-- Lead Management Section --}}
                @if(has_permission('admin/lead-statuses/index') || has_permission('admin/lead-sources/index'))
                <li class="pc-item pc-caption">
                    <label>Lead Management</label>
                </li>
                @if(has_permission('admin/lead-statuses/index'))
                <li class="pc-item {{ request()->routeIs('admin.lead-statuses.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.lead-statuses.index') }}" class="pc-link">
                        <span class="pc-micon">
                            <i class="ti ti-flag"></i>
                        </span>
                        <span class="pc-mtext">Lead Status</span>
                    </a>
                </li>
                @endif
                @if(has_permission('admin/lead-sources/index'))
                <li class="pc-item {{ request()->routeIs('admin.lead-sources.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.lead-sources.index') }}" class="pc-link">
                        <span class="pc-micon">
                            <i class="ti ti-tag"></i>
                        </span>
                        <span class="pc-mtext">Lead Source</span>
                    </a>
                </li>
                @endif
                @endif

                
                {{-- Reports Section --}}
                @if(has_permission('admin/reports/leads'))
                <li class="pc-item pc-caption">
                    <label>Reports</label>
                </li>
                <li class="pc-item {{ request()->routeIs('admin.reports.leads') || request()->routeIs('admin.reports.lead-status') || request()->routeIs('admin.reports.lead-source') || request()->routeIs('admin.reports.team') || request()->routeIs('admin.reports.telecaller') ? 'active' : '' }}">
                    <a href="{{ route('admin.reports.leads') }}" class="pc-link">
                        <span class="pc-micon">
                            <i class="ti ti-chart-pie"></i>
                        </span>
                        <span class="pc-mtext">Lead Reports</span>
                    </a>
                </li>
                @endif
                
                {{-- Advanced Reports Section --}}
                @if(has_permission('admin/reports/lead-stage-movement') || has_permission('admin/reports/lead-efficiency') || has_permission('admin/reports/lead-aging') || has_permission('admin/reports/team-wise') || has_permission('admin/reports/course-summary'))
                <li class="pc-item pc-caption">
                    <label>Advanced Reports</label>
                </li>
                
                {{-- Stage Movement - Available to Admin, Super Admin, and Auditor --}}
                @if(has_permission('admin/reports/lead-stage-movement'))
                <li class="pc-item {{ request()->routeIs('admin.reports.lead-stage-movement*') ? 'active' : '' }}">
                    <a href="{{ route('admin.reports.lead-stage-movement') }}" class="pc-link">
                        <span class="pc-micon">
                            <i class="ti ti-arrow-right"></i>
                        </span>
                        <span class="pc-mtext">Stage Movement</span>
                    </a>
                </li>
                @endif
                
                {{-- Other Advanced Reports - Only for Super Admin and Auditor --}}
                @if(has_permission('admin/reports/lead-efficiency'))
                <li class="pc-item {{ request()->routeIs('admin.reports.lead-efficiency*') ? 'active' : '' }}">
                    <a href="{{ route('admin.reports.lead-efficiency') }}" class="pc-link">
                        <span class="pc-micon">
                            <i class="ti ti-chart-line"></i>
                        </span>
                        <span class="pc-mtext">Source Efficiency</span>
                    </a>
                </li>
                @endif
                @if(has_permission('admin/reports/lead-aging'))
                <li class="pc-item {{ request()->routeIs('admin.reports.lead-aging*') ? 'active' : '' }}">
                    <a href="{{ route('admin.reports.lead-aging') }}" class="pc-link">
                        <span class="pc-micon">
                            <i class="ti ti-clock"></i>
                        </span>
                        <span class="pc-mtext">Lead Aging</span>
                    </a>
                </li>
                @endif
                @if(has_permission('admin/reports/team-wise'))
                <li class="pc-item {{ request()->routeIs('admin.reports.team-wise*') ? 'active' : '' }}">
                    <a href="{{ route('admin.reports.team-wise') }}" class="pc-link">
                        <span class="pc-micon">
                            <i class="ti ti-users"></i>
                        </span>
                        <span class="pc-mtext">Team-Wise Report</span>
                    </a>
                </li>
                @endif
                @if(has_permission('admin/reports/course-summary'))
                <li class="pc-item {{ request()->routeIs('admin.reports.course-summary*') || request()->routeIs('admin.reports.course-leads*') || request()->routeIs('admin.reports.course-converted-leads*') ? 'active' : '' }}">
                    <a href="{{ route('admin.reports.course-summary') }}" class="pc-link">
                        <span class="pc-micon">
                            <i class="ti ti-book"></i>
                        </span>
                        <span class="pc-mtext">Course Reports</span>
                    </a>
                </li>
                @endif
                @endif
                
                {{-- Post Sales Reports Section --}}
                @php
                    $canAccessPostSalesReports = \App\Helpers\RoleHelper::is_finance() || 
                                                  \App\Helpers\RoleHelper::is_admin_or_super_admin() || 
                                                  \App\Helpers\RoleHelper::is_post_sales_head();
                @endphp
                @if($canAccessPostSalesReports)
                <li class="pc-item pc-caption">
                    <label>Post Sales Reports</label>
                </li>
                <li class="pc-item {{ request()->routeIs('admin.reports.post-sales-month-ways*') ? 'active' : '' }}">
                    <a href="{{ route('admin.reports.post-sales-month-ways') }}" class="pc-link">
                        <span class="pc-micon">
                            <i class="ti ti-calendar-month"></i>
                        </span>
                        <span class="pc-mtext">Post Sales Month Ways</span>
                    </a>
                </li>
                <li class="pc-item {{ request()->routeIs('admin.reports.total-monthly*') ? 'active' : '' }}">
                    <a href="{{ route('admin.reports.total-monthly') }}" class="pc-link">
                        <span class="pc-micon">
                            <i class="ti ti-chart-bar"></i>
                        </span>
                        <span class="pc-mtext">Total Monthly Report</span>
                    </a>
                </li>
                <li class="pc-item {{ request()->routeIs('admin.reports.bde-collected-amount-course-ways*') ? 'active' : '' }}">
                    <a href="{{ route('admin.reports.bde-collected-amount-course-ways') }}" class="pc-link">
                        <span class="pc-micon">
                            <i class="ti ti-currency-rupee"></i>
                        </span>
                        <span class="pc-mtext">BDE Collected Amount</span>
                    </a>
                </li>
                @endif
                
                {{-- Finance Reports Section --}}
                @php
                    $canAccessFinanceReports = \App\Helpers\RoleHelper::is_finance() || 
                                                \App\Helpers\RoleHelper::is_admin_or_super_admin();
                @endphp
                @if($canAccessFinanceReports)
                <li class="pc-item pc-caption">
                    <label>Finance Reports</label>
                </li>
                <li class="pc-item {{ request()->routeIs('admin.reports.telecallers-sales*') ? 'active' : '' }}">
                    <a href="{{ route('admin.reports.telecallers-sales') }}" class="pc-link">
                        <span class="pc-micon">
                            <i class="ti ti-report-analytics"></i>
                        </span>
                        <span class="pc-mtext">Telecallers Sales Report</span>
                    </a>
                </li>
                <li class="pc-item {{ request()->routeIs('admin.reports.thanzeels-eschool-sales*') ? 'active' : '' }}">
                    <a href="{{ route('admin.reports.thanzeels-eschool-sales') }}" class="pc-link">
                        <span class="pc-micon">
                            <i class="ti ti-chart-bar"></i>
                        </span>
                        <span class="pc-mtext">Thanzeels and Eschool Report</span>
                    </a>
                </li>
                <li class="pc-item {{ request()->routeIs('admin.reports.course-wise-sales*') ? 'active' : '' }}">
                    <a href="{{ route('admin.reports.course-wise-sales') }}" class="pc-link">
                        <span class="pc-micon">
                            <i class="ti ti-book"></i>
                        </span>
                        <span class="pc-mtext">Course Wise Sales Report</span>
                    </a>
                </li>
                @endif
                
                {{-- Notifications Section --}}
                @if(has_permission('admin/notifications/index'))
                <li class="pc-item pc-caption">
                    <label>Notifications</label>
                </li>
                <li class="pc-item {{ request()->routeIs('admin.notifications.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.notifications.index') }}" class="pc-link">
                        <span class="pc-micon">
                            <i class="ti ti-bell"></i>
                        </span>
                        <span class="pc-mtext">Manage Notifications</span>
                    </a>
                </li>
                @endif
                
                {{-- Call Management Section --}}
                @if(has_permission('admin/call-logs/index'))
                <li class="pc-item pc-caption">
                    <label>Call Management</label>
                </li>
                <li class="pc-item {{ request()->routeIs('admin.call-logs.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.call-logs.index') }}" class="pc-link">
                        <span class="pc-micon">
                            <i class="ti ti-phone"></i>
                        </span>
                        <span class="pc-mtext">Call Logs</span>
                    </a>
                </li>
                @endif
                
                {{-- Telecaller Tracking Section --}}
                @if(is_super_admin() || is_auditor())
                <li class="pc-item pc-caption">
                    <label>Telecaller Tracking</label>
                </li>
                <li class="pc-item {{ request()->routeIs('admin.telecaller-tracking.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.telecaller-tracking.dashboard') }}" class="pc-link">
                        <span class="pc-micon">
                            <i class="ti ti-chart-line"></i>
                        </span>
                        <span class="pc-mtext">Tracking Dashboard</span>
                    </a>
                </li>
                <li class="pc-item {{ request()->routeIs('admin.telecaller-tasks.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.telecaller-tasks.index') }}" class="pc-link">
                        <span class="pc-micon">
                            <i class="ti ti-notes"></i>
                        </span>
                        <span class="pc-mtext">Task Management</span>
                    </a>
                </li>
                @endif
                
                {{-- Master Data Section --}}
                @if(has_permission('admin/courses/index') || has_permission('admin/countries/index') || has_permission('admin/teams/index') || has_permission('admin/subjects/index') || has_permission('admin/subject-areas/index') || has_permission('admin/mails/index') || has_permission('admin/flags/index') || has_permission('admin/support-flags/index') || has_permission('admin/course-flags/index') || has_permission('admin/class-times/index') || has_permission('admin/offline-places/index') || has_permission('admin/course-documents/index') || has_permission('admin/universities/index') || has_permission('admin/university-courses/index') || has_permission('admin/registration-links/index'))
                <li class="pc-item pc-caption">
                    <label>Master Data</label>
                </li>
                @if(has_permission('admin/courses/index'))
                <li class="pc-item {{ request()->routeIs('admin.courses.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.courses.index') }}" class="pc-link">
                        <span class="pc-micon">
                            <i class="ti ti-book"></i>
                        </span>
                        <span class="pc-mtext">Courses</span>
                    </a>
                </li>
                @endif
                @if(has_permission('admin/subjects/index'))
                <li class="pc-item {{ request()->routeIs('admin.subjects.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.subjects.index') }}" class="pc-link">
                        <span class="pc-micon">
                            <i class="ti ti-bookmark"></i>
                        </span>
                        <span class="pc-mtext">Subjects</span>
                    </a>
                </li>
                @endif
                @if(has_permission('admin/subject-areas/index'))
                <li class="pc-item {{ request()->routeIs('admin.subject-areas.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.subject-areas.index') }}" class="pc-link">
                        <span class="pc-micon">
                            <i class="ti ti-category"></i>
                        </span>
                        <span class="pc-mtext">Subject Areas</span>
                    </a>
                </li>
                @endif
                @if(has_permission('admin/mails/index'))
                <li class="pc-item {{ request()->routeIs('admin.mails.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.mails.index') }}" class="pc-link">
                        <span class="pc-micon">
                            <i class="ti ti-mail"></i>
                        </span>
                        <span class="pc-mtext">Mail</span>
                    </a>
                </li>
                @endif
                @if(has_permission('admin/flags/index'))
                <li class="pc-item {{ request()->routeIs('admin.flags.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.flags.index') }}" class="pc-link">
                        <span class="pc-micon">
                            <i class="ti ti-flag"></i>
                        </span>
                        <span class="pc-mtext">Flag</span>
                    </a>
                </li>
                @endif
                @if(has_permission('admin/support-flags/index'))
                <li class="pc-item {{ request()->routeIs('admin.support-flags.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.support-flags.index') }}" class="pc-link">
                        <span class="pc-micon">
                            <i class="ti ti-flag-2"></i>
                        </span>
                        <span class="pc-mtext">Support Flag</span>
                    </a>
                </li>
                @endif
                @if(has_permission('admin/course-flags/index'))
                <li class="pc-item {{ request()->routeIs('admin.course-flags.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.course-flags.index') }}" class="pc-link">
                        <span class="pc-micon">
                            <i class="ti ti-flag-3"></i>
                        </span>
                        <span class="pc-mtext">Course Flag</span>
                    </a>
                </li>
                @endif
                @if(has_permission('admin/class-times/index'))
                <li class="pc-item {{ request()->routeIs('admin.class-times.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.class-times.index') }}" class="pc-link">
                        <span class="pc-micon">
                            <i class="ti ti-clock"></i>
                        </span>
                        <span class="pc-mtext">Class Times</span>
                    </a>
                </li>
                @endif
                @if(has_permission('admin/offline-places/index'))
                <li class="pc-item {{ request()->routeIs('admin.offline-places.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.offline-places.index') }}" class="pc-link">
                        <span class="pc-micon">
                            <i class="ti ti-map-pin"></i>
                        </span>
                        <span class="pc-mtext">Offline Places</span>
                    </a>
                </li>
                @endif
                @if(has_permission('admin/sub-courses/index'))
                <li class="pc-item {{ request()->routeIs('admin.sub-courses.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.sub-courses.index') }}" class="pc-link">
                        <span class="pc-micon">
                            <i class="ti ti-book"></i>
                        </span>
                        <span class="pc-mtext">Sub Courses</span>
                    </a>
                </li>
                @endif
                @if(has_permission('admin/countries/index'))
                <li class="pc-item {{ request()->routeIs('admin.countries.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.countries.index') }}" class="pc-link">
                        <span class="pc-micon">
                            <i class="ti ti-world"></i>
                        </span>
                        <span class="pc-mtext">Countries</span>
                    </a>
                </li>
                @endif
                @if(has_permission('admin/boards/index'))
                <li class="pc-item {{ request()->routeIs('admin.boards.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.boards.index') }}" class="pc-link">
                        <span class="pc-micon">
                            <i class="ti ti-school"></i>
                        </span>
                        <span class="pc-mtext">Boards</span>
                    </a>
                </li>
                @endif
                @if(has_permission('admin/batches/index'))
                <li class="pc-item {{ request()->routeIs('admin.batches.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.batches.index') }}" class="pc-link">
                        <span class="pc-micon">
                            <i class="ti ti-calendar"></i>
                        </span>
                        <span class="pc-mtext">Batches</span>
                    </a>
                </li>
                @endif
                @if(has_permission('admin/admission-batches/index'))
                <li class="pc-item {{ request()->routeIs('admin.admission-batches.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.admission-batches.index') }}" class="pc-link">
                        <span class="pc-micon">
                            <i class="ti ti-calendar-plus"></i>
                        </span>
                        <span class="pc-mtext">Admission Batches</span>
                    </a>
                </li>
                @endif
                @if(has_permission('admin/teams/index') || \App\Helpers\RoleHelper::is_finance())
                <li class="pc-item {{ request()->routeIs('admin.teams.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.teams.index') }}" class="pc-link">
                        <span class="pc-micon">
                            <i class="ti ti-users"></i>
                        </span>
                        <span class="pc-mtext">Teams</span>
                    </a>
                </li>
                @endif
                @if(has_permission('admin/b2b-services/index'))
                <li class="pc-item {{ request()->routeIs('admin.b2b-services.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.b2b-services.index') }}" class="pc-link">
                        <span class="pc-micon">
                            <i class="ti ti-briefcase"></i>
                        </span>
                        <span class="pc-mtext">B2B Services</span>
                    </a>
                </li>
                @endif
                @if(has_permission('admin/departments/index'))
                <li class="pc-item {{ request()->routeIs('admin.departments.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.departments.index') }}" class="pc-link">
                        <span class="pc-micon">
                            <i class="ti ti-building"></i>
                        </span>
                        <span class="pc-mtext">Departments</span>
                    </a>
                </li>
                @endif
                @if(has_permission('admin/universities/index'))
                <li class="pc-item {{ request()->routeIs('admin.universities.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.universities.index') }}" class="pc-link">
                        <span class="pc-micon">
                            <i class="ti ti-school"></i>
                        </span>
                        <span class="pc-mtext">Universities</span>
                    </a>
                </li>
                @endif
                @if(has_permission('admin/university-courses/index'))
                <li class="pc-item {{ request()->routeIs('admin.university-courses.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.university-courses.index') }}" class="pc-link">
                        <span class="pc-micon">
                            <i class="ti ti-graduation-cap"></i>
                        </span>
                        <span class="pc-mtext">University Courses</span>
                    </a>
                </li>
                @endif
                @if(has_permission('admin/registration-links/index'))
                <li class="pc-item {{ request()->routeIs('admin.registration-links.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.registration-links.index') }}" class="pc-link">
                        <span class="pc-micon">
                            <i class="ti ti-link"></i>
                        </span>
                        <span class="pc-mtext">Registration Links</span>
                    </a>
                </li>
                @endif
                @if(has_permission('admin/academic-delivery-structures/index') || \App\Helpers\RoleHelper::is_academic_counselor())
                <li class="pc-item {{ request()->routeIs('admin.academic-delivery-structures.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.academic-delivery-structures.index') }}" class="pc-link">
                        <span class="pc-micon">
                            <i class="ti ti-book"></i>
                        </span>
                        <span class="pc-mtext">Academic Delivery Structure</span>
                    </a>
                </li>
                @endif
                @endif
                
                {{-- Settings Section --}}
                @if(has_permission('admin/website/settings') || has_permission('profile/index'))
                <li class="pc-item pc-caption">
                    <label>Settings</label>
                </li>
                @if(has_permission('admin/website/settings'))
                <li class="pc-item {{ request()->routeIs('admin.website.settings.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.website.settings') }}" class="pc-link">
                        <span class="pc-micon">
                            <i class="ti ti-settings"></i>
                        </span>
                        <span class="pc-mtext">Website Settings</span>
                    </a>
                </li>
                @endif
                @if(has_permission('profile/index'))
                <li class="pc-item {{ request()->routeIs('profile*') ? 'active' : '' }}">
                    <a href="{{ route('profile') }}" class="pc-link">
                        <span class="pc-micon">
                            <i class="ti ti-user"></i>
                        </span>
                        <span class="pc-mtext">Profile</span>
                    </a>
                </li>
                @endif
                @endif
            </ul>
        </div>
    </div>
</nav>
<!-- [ Sidebar Menu ] end -->
