@php
    $canToggleAcademic = \App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_academic_assistant() || \App\Helpers\RoleHelper::is_admission_counsellor();
    $canToggleSupport = \App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_support_team();
    $dobDisplay = '-';
    if ($convertedLead->leadDetail && $convertedLead->leadDetail->date_of_birth) {
        $dobDisplay = $convertedLead->leadDetail->date_of_birth->format('d-m-Y');
    } elseif ($convertedLead->dob) {
        $dobDisplay = strtotime($convertedLead->dob) ? date('d-m-Y', strtotime($convertedLead->dob)) : $convertedLead->dob;
    }

    $courseChanged = (bool) ($convertedLead->is_course_changed ?? false);
    $canManageCancelFlag = \App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor();
    $showParentPhone = \App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor();

    $selectedCourses = $convertedLead->leadDetail?->selected_courses ? json_decode($convertedLead->leadDetail->selected_courses, true) : [];
    $hasUG = in_array('UG', $selectedCourses);
    $hasPG = in_array('PG', $selectedCourses);

    $courseTeamName = $convertedLead->lead?->team?->name;
    $isB2bType = $convertedLead->is_b2b == 1;
    $typeDisplay = $isB2bType ? ('B2B' . ($courseTeamName ? ' (' . $courseTeamName . ')' : '')) : 'In House';
@endphp

<tr class="{{ $convertedLead->is_cancelled ? 'cancelled-row' : '' }}">
    <td>{{ $displayIndex }}</td>

    <td>
        @include('admin.converted-leads.partials.status-badge', [
            'convertedLead' => $convertedLead,
            'type' => 'academic',
            'showToggle' => $canToggleAcademic,
            'toggleUrl' => $canToggleAcademic ? route('admin.converted-leads.toggle-academic-verify', $convertedLead->id) : null,
            'title' => 'academic',
            'useModal' => true,
        ])
    </td>

    <td>
        @include('admin.converted-leads.partials.status-badge', [
            'convertedLead' => $convertedLead,
            'type' => 'support',
            'showToggle' => $canToggleSupport,
            'toggleUrl' => $canToggleSupport ? route('admin.support-converted-leads.toggle-support-verify', $convertedLead->id) : null,
            'title' => 'support',
            'useModal' => true,
        ])
    </td>

    <td>{{ $convertedLead->created_at->format('d-m-Y') }}</td>

    <td>
        <div class="inline-edit" data-field="register_number" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->register_number }}">
            @if($convertedLead->register_number)
                <span class="badge bg-success"><span class="display-value">{{ $convertedLead->register_number }}</span></span>
            @else
                <span class="display-value text-muted">Not Set</span>
            @endif
            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_academic_assistant())
                <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                    <i class="ti ti-edit"></i>
                </button>
            @endif
        </div>
    </td>
    @include('admin.converted-leads.partials.inline-course-flag-cell', ['convertedLead' => $convertedLead])

    <td>
        <div class="d-flex align-items-center">
            <div class="avtar avtar-s rounded-circle bg-light-success me-2 d-flex align-items-center justify-content-center">
                <span class="f-16 fw-bold text-success">{{ strtoupper(substr($convertedLead->name, 0, 1)) }}</span>
            </div>
            <div>
                <h6 class="mb-0">{{ $convertedLead->name }}</h6>
                <small class="text-muted">ID: {{ $convertedLead->lead_id }}</small>
                @if($convertedLead->is_cancelled)
                    <div>
                        <span class="badge bg-danger mt-1">Cancelled</span>
                        @if($convertedLead->cancelledBy)
                            <br><small class="text-muted mt-1 d-block">By: {{ $convertedLead->cancelledBy->name }}
                                @if($convertedLead->cancelled_at)
                                    <br>{{ $convertedLead->cancelled_at->format('d-m-Y h:i A') }}
                                @endif
                            </small>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </td>

    <td>{{ $typeDisplay }}</td>

    <td>
        <div class="inline-edit" data-field="dob" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->leadDetail?->date_of_birth ? $convertedLead->leadDetail->date_of_birth->format('Y-m-d') : ($convertedLead->dob ?: '') }}">
            <span class="display-value">{{ $dobDisplay }}</span>
            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_academic_assistant())
                <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                    <i class="ti ti-edit"></i>
                </button>
            @endif
        </div>
    </td>

    <td>
        <div class="inline-edit" data-field="phone" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->phone }}">
            <span class="display-value">{{ \App\Helpers\PhoneNumberHelper::display($convertedLead->code, $convertedLead->phone) }}</span>
            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_academic_assistant())
                <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                    <i class="ti ti-edit"></i>
                </button>
            @endif
        </div>
        <div class="d-none inline-code-value" data-field="code" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->code }}"></div>
    </td>

    <td>
        <div class="inline-edit" data-field="whatsapp_number" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->leadDetail?->whatsapp_number }}">
            <span class="display-value">{{ $convertedLead->leadDetail ? \App\Helpers\PhoneNumberHelper::display($convertedLead->leadDetail->whatsapp_code, $convertedLead->leadDetail->whatsapp_number) : '-' }}</span>
            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_academic_assistant())
                <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                    <i class="ti ti-edit"></i>
                </button>
            @endif
        </div>
        <div class="d-none inline-code-value" data-field="whatsapp_code" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->leadDetail?->whatsapp_code }}"></div>
    </td>

    @if($showParentPhone)
        <td>
            @if($convertedLead->leadDetail && $convertedLead->leadDetail->parents_number)
                {{ \App\Helpers\PhoneNumberHelper::display($convertedLead->leadDetail->parents_code, $convertedLead->leadDetail->parents_number) }}
            @else
                <span class="text-muted">-</span>
            @endif
        </td>
    @endif

    <td>{{ $convertedLead->email ?? '-' }}</td>

    <td>{{ $convertedLead->batch ? $convertedLead->batch->title : 'N/A' }}</td>

    <td>{{ $convertedLead->admissionBatch ? $convertedLead->admissionBatch->title : 'N/A' }}</td>

    @include('admin.converted-leads.partials.inline-finance-approval-cell', ['convertedLead' => $convertedLead])
    @include('admin.converted-leads.partials.inline-faculty-cell', ['convertedLead' => $convertedLead])

    <td>
        <div class="inline-edit" data-field="selected_courses" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->leadDetail?->selected_courses }}">
            <span class="display-value">
                @if(!empty($selectedCourses))
                    {{ implode(', ', $selectedCourses) }}
                @else
                    -
                @endif
            </span>
            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_academic_assistant())
                <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                    <i class="ti ti-edit"></i>
                </button>
            @endif
        </div>
    </td>

    <td>
        <div class="inline-edit" data-field="university_id" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->leadDetail?->university_id }}">
            <span class="display-value">{{ $convertedLead->leadDetail?->university?->title ?? '-' }}</span>
            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_academic_assistant())
                <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                    <i class="ti ti-edit"></i>
                </button>
            @endif
        </div>
    </td>

    <td>
        <div class="inline-edit" data-field="course_type" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->leadDetail?->course_type }}">
            <span class="display-value">{{ $convertedLead->leadDetail?->course_type ?? '-' }}</span>
            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_academic_assistant())
                <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                    <i class="ti ti-edit"></i>
                </button>
            @endif
        </div>
    </td>

    <td>
        @if($hasUG || $hasPG)
            <div class="inline-edit" data-field="edumaster_course_name" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->leadDetail?->edumaster_course_name }}">
                <span class="display-value">{{ $convertedLead->leadDetail?->edumaster_course_name ?? '-' }}</span>
                @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_academic_assistant())
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
        <div class="inline-edit" data-field="sslc_back_year" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->leadDetail?->sslc_back_year }}">
            <span class="display-value">{{ $convertedLead->leadDetail?->sslc_back_year ?? '-' }}</span>
            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_academic_assistant())
                <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                    <i class="ti ti-edit"></i>
                </button>
            @endif
        </div>
    </td>

    <td>
        <div class="inline-edit" data-field="plustwo_back_year" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->leadDetail?->plustwo_back_year }}">
            <span class="display-value">{{ $convertedLead->leadDetail?->plustwo_back_year ?? '-' }}</span>
            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_academic_assistant())
                <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                    <i class="ti ti-edit"></i>
                </button>
            @endif
        </div>
    </td>

    <td>
        @if($hasUG || $hasPG)
            <div class="inline-edit" data-field="degree_back_year" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->leadDetail?->degree_back_year }}">
                <span class="display-value">{{ $convertedLead->leadDetail?->degree_back_year ?? '-' }}</span>
                @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_academic_assistant())
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
        <div class="" role="group">
            <a href="{{ route('admin.converted-leads.show', $convertedLead->id) }}" class="btn btn-sm btn-outline-primary" title="View Details">
                <i class="ti ti-eye"></i>
            </a>
            <a href="{{ route('admin.invoices.index', $convertedLead->id) }}" class="btn btn-sm btn-success" title="View Invoice">
                <i class="ti ti-receipt"></i>
            </a>

            @if($canManageCancelFlag)
                @php
                    $cancelBtnClass = $convertedLead->is_cancelled ? 'btn-danger' : 'btn-outline-danger';
                    $cancelBtnTitle = $convertedLead->is_cancelled ? 'Update cancellation confirmation' : 'Confirm cancellation';
                @endphp
                <button type="button" class="btn btn-sm {{ $cancelBtnClass }} js-cancel-flag" title="{{ $cancelBtnTitle }}"
                    data-cancel-url="{{ route('admin.converted-leads.cancel-flag', $convertedLead->id) }}"
                    data-modal-title="Cancellation Confirmation">
                    <i class="ti ti-ban"></i>
                </button>
            @endif

            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_academic_assistant() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_support_team())
                <button type="button" class="btn btn-sm btn-info update-register-btn" title="Update Register Number"
                    data-url="{{ route('admin.converted-leads.update-register-number-modal', $convertedLead->id) }}"
                    data-title="Update Register Number">
                    <i class="ti ti-edit"></i>
                </button>

                @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor())
                    @if($courseChanged)
                        <button type="button" class="btn btn-sm btn-success js-change-course-modal"
                            title="Change Course"
                            data-modal-url="{{ route('admin.converted-leads.change-course-modal', $convertedLead->id) }}"
                            data-modal-title="Change Course">
                            <i class="ti ti-exchange"></i>
                        </button>
                    @else
                        <button type="button" class="btn btn-sm btn-danger js-change-course-modal"
                            title="Change Course"
                            data-modal-url="{{ route('admin.converted-leads.change-course-modal', $convertedLead->id) }}"
                            data-modal-title="Change Course">
                            <i class="ti ti-exchange"></i>
                        </button>
                    @endif
                @endif

                @if($convertedLead->register_number)
                    @if($hasIdCard)
                        <a href="{{ route('admin.converted-leads.id-card-view', $convertedLead->id) }}" class="btn btn-sm btn-success" title="View ID Card" target="_blank">
                            <i class="ti ti-id"></i>
                        </a>
                    @else
                        <form action="{{ route('admin.converted-leads.id-card-generate', $convertedLead->id) }}" method="post" style="display:inline-block" class="id-card-generate-form">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-warning" title="Generate ID Card" data-loading-text="Generating...">
                                <i class="ti ti-id"></i>
                            </button>
                        </form>
                    @endif
                @endif
            @endif
        </div>
    </td>
</tr>

