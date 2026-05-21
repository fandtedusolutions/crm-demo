<tr class="{{ $convertedLead->is_cancelled ? 'cancelled-row' : '' }}">
    @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_academic_assistant())
    <td>
        <input type="checkbox" class="row-checkbox" value="{{ $convertedLead->id }}">
    </td>
    @endif
    <td>{{ $displayIndex }}</td>
    <td>
        @php $isVerified = (bool) ($convertedLead->is_academic_verified ?? false); @endphp
        <span class="badge {{ $isVerified ? 'bg-success' : 'bg-secondary' }} me-1">
            {{ $isVerified ? 'Verified' : 'Not Verified' }}
        </span>
        @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_academic_assistant() || \App\Helpers\RoleHelper::is_admission_counsellor())
        <button type="button" class="btn btn-sm {{ $isVerified ? 'btn-outline-danger' : 'btn-outline-success' }} toggle-academic-verify-btn"
            data-use-modal="1"
            data-id="{{ $convertedLead->id }}"
            data-name="{{ $convertedLead->name }}"
            data-verified="{{ $isVerified ? 1 : 0 }}"
            data-url="{{ route('admin.converted-leads.toggle-academic-verify', $convertedLead->id) }}"
            title="{{ $isVerified ? 'Unverify' : 'Verify' }} academic">
            <i class="ti {{ $isVerified ? 'ti-x' : 'ti-check' }}"></i>
        </button>
        @endif
    </td>
    <td>
        @php $isSupportVerified = (bool) ($convertedLead->is_support_verified ?? false); @endphp
        <span class="badge {{ $isSupportVerified ? 'bg-success' : 'bg-secondary' }} me-1">
            {{ $isSupportVerified ? 'Verified' : 'Not Verified' }}
        </span>
        @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_support_team())
        <button type="button" class="btn btn-sm {{ $isSupportVerified ? 'btn-outline-danger' : 'btn-outline-success' }} toggle-support-verify-btn"
            data-use-modal="1"
            data-id="{{ $convertedLead->id }}"
            data-name="{{ $convertedLead->name }}"
            data-verified="{{ $isSupportVerified ? 1 : 0 }}"
            data-url="{{ route('admin.support-converted-leads.toggle-support-verify', $convertedLead->id) }}"
            title="{{ $isSupportVerified ? 'Unverify' : 'Verify' }} support">
            <i class="ti {{ $isSupportVerified ? 'ti-x' : 'ti-check' }}"></i>
        </button>
        @endif
    </td>
    <td>
        <div class="inline-edit" data-field="register_number" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->register_number }}">
            @if($convertedLead->register_number)
            <span class="badge bg-success"><span class="display-value">{{ $convertedLead->register_number }}</span></span>
            @else
            <span class="display-value text-muted">Not Set</span>
            @endif
            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_academic_assistant())
            <button type="button" class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                <i class="ti ti-edit"></i>
            </button>
            @endif
        </div>
    </td>
    <td>{{ $convertedLead->created_at->format('d-m-Y') }}</td>
    <td>
        <div class="inline-edit" data-field="dob" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->dob }}">
            @php
            $dobDisplay = $convertedLead->dob ? (strtotime($convertedLead->dob) ? date('d-m-Y', strtotime($convertedLead->dob)) : $convertedLead->dob) : 'N/A';
            @endphp
            <span class="display-value">{{ $dobDisplay }}</span>
            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_academic_assistant())
            <button type="button" class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                <i class="ti ti-edit"></i>
            </button>
            @endif
        </div>
    </td>
    <td>{{ $convertedLead->is_b2b == 1 ? ('B2B' . ($convertedLead->lead?->team?->name ? ' (' . $convertedLead->lead->team->name . ')' : '')) : 'In house' }}</td>
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
    <td>
        <div class="inline-edit" data-field="subject_id" data-id="{{ $convertedLead->id }}" data-course-id="{{ $convertedLead->course_id }}" data-current-id="{{ $convertedLead->subject_id }}">
            <span class="display-value">{{ $convertedLead->subject ? $convertedLead->subject->title : 'N/A' }}</span>
            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_academic_assistant())
            <button type="button" class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                <i class="ti ti-edit"></i>
            </button>
            @endif
        </div>
    </td>
    <td>
        <div class="inline-edit" data-field="subject_area_id" data-id="{{ $convertedLead->id }}" data-current-id="{{ $convertedLead->subject_area_id }}">
            <span class="display-value">{{ $convertedLead->subjectArea ? $convertedLead->subjectArea->title : 'N/A' }}</span>
            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_academic_assistant())
            <button type="button" class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                <i class="ti ti-edit"></i>
            </button>
            @endif
        </div>
    </td>
    <td>
        <div class="inline-edit" data-field="phone" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->phone }}">
            <span class="display-value">{{ \App\Helpers\PhoneNumberHelper::display($convertedLead->code, $convertedLead->phone) }}</span>
            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_academic_assistant())
            <button type="button" class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                <i class="ti ti-edit"></i>
            </button>
            @endif
        </div>
        <div class="d-none inline-code-value" data-field="code" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->code }}"></div>
    </td>
    <td>
        @if($convertedLead->leadDetail && $convertedLead->leadDetail->whatsapp_number)
            {{ \App\Helpers\PhoneNumberHelper::display($convertedLead->leadDetail->whatsapp_code, $convertedLead->leadDetail->whatsapp_number) }}
        @else
            <span class="text-muted">N/A</span>
        @endif
    </td>
    @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor())
    <td>
        @if($convertedLead->leadDetail && $convertedLead->leadDetail->parents_number)
            {{ \App\Helpers\PhoneNumberHelper::display($convertedLead->leadDetail->parents_code, $convertedLead->leadDetail->parents_number) }}
        @else
            <span class="text-muted">N/A</span>
        @endif
    </td>
    @endif
    <td>
        <div class="inline-edit" data-field="batch_id" data-id="{{ $convertedLead->id }}" data-course-id="{{ $convertedLead->course_id }}" data-current-id="{{ $convertedLead->batch_id }}">
            <span class="display-value">{{ $convertedLead->batch ? $convertedLead->batch->title : 'N/A' }}</span>
            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_academic_assistant())
            <button type="button" class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                <i class="ti ti-edit"></i>
            </button>
            @endif
        </div>
    </td>
    <td>{{ $convertedLead->course ? $convertedLead->course->title : 'N/A' }}</td>
    <td>
        <div class="inline-edit" data-field="admission_batch_id" data-id="{{ $convertedLead->id }}" data-batch-id="{{ $convertedLead->batch_id }}" data-current-id="{{ $convertedLead->admission_batch_id }}">
            <span class="display-value">{{ $convertedLead->admissionBatch ? $convertedLead->admissionBatch->title : 'N/A' }}</span>
            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_academic_assistant())
            <button type="button" class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                <i class="ti ti-edit"></i>
            </button>
            @endif
        </div>
    </td>
    <td>
        <div class="inline-edit" data-field="academic_assistant_id" data-id="{{ $convertedLead->id }}" data-current-id="{{ $convertedLead->academic_assistant_id }}">
            <span class="display-value">{{ $convertedLead->academicAssistant ? $convertedLead->academicAssistant->name : 'N/A' }}</span>
            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_academic_assistant())
            <button type="button" class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                <i class="ti ti-edit"></i>
            </button>
            @endif
        </div>
    </td>
    <td>
        <div class="inline-edit" data-field="username" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->username }}">
            <span class="display-value">{{ $convertedLead->username ?? 'N/A' }}</span>
            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_academic_assistant())
            <button type="button" class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                <i class="ti ti-edit"></i>
            </button>
            @endif
        </div>
    </td>
    <td>
        <div class="inline-edit" data-field="password" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->password }}">
            <span class="display-value">{{ $convertedLead->password ?? 'N/A' }}</span>
            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_academic_assistant())
            <button type="button" class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                <i class="ti ti-edit"></i>
            </button>
            @endif
        </div>
    </td>
    <td>
        <div class="inline-edit" data-field="status" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->status }}">
            <span class="display-value">{{ $convertedLead->status ?? 'N/A' }}</span>
            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_academic_assistant() || \App\Helpers\RoleHelper::is_finance())
            <button type="button" class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                <i class="ti ti-edit"></i>
            </button>
            @endif
        </div>
    </td>
    <td>
        <div class="inline-edit" data-field="reg_fee" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->reg_fee }}">
            <span class="display-value">{{ $convertedLead->reg_fee ?? 'N/A' }}</span>
            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_academic_assistant())
            <button type="button" class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                <i class="ti ti-edit"></i>
            </button>
            @endif
        </div>
    </td>
    <td>
        <div class="inline-edit" data-field="exam_fee" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->exam_fee }}">
            <span class="display-value">{{ $convertedLead->exam_fee ?? 'N/A' }}</span>
            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_academic_assistant() || \App\Helpers\RoleHelper::is_finance())
            <button type="button" class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                <i class="ti ti-edit"></i>
            </button>
            @endif
        </div>
    </td>
    <td>
        <div class="inline-edit" data-field="ref_no" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->ref_no }}">
            <span class="display-value">{{ $convertedLead->ref_no ?? 'N/A' }}</span>
            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_academic_assistant())
            <button type="button" class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                <i class="ti ti-edit"></i>
            </button>
            @endif
        </div>
    </td>
    <td>
        <div class="inline-edit" data-field="enroll_no" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->enroll_no }}">
            <span class="display-value">{{ $convertedLead->enroll_no ?? 'N/A' }}</span>
            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_academic_assistant())
            <button type="button" class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                <i class="ti ti-edit"></i>
            </button>
            @endif
        </div>
    </td>
    <td>{{ $convertedLead->email ?? 'N/A' }}</td>
    <td>
        <div class="inline-edit" data-field="id_card" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->id_card }}">
            <span class="display-value">{{ $convertedLead->id_card ?? 'N/A' }}</span>
            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_academic_assistant())
            <button type="button" class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                <i class="ti ti-edit"></i>
            </button>
            @endif
        </div>
    </td>
    <td>
        <div class="inline-edit" data-field="tma" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->tma }}">
            <span class="display-value">{{ $convertedLead->tma ?? 'NOT SET' }}</span>
            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_academic_assistant())
            <button type="button" class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                <i class="ti ti-edit"></i>
            </button>
            @endif
        </div>
    </td>
    <td>
        <div class="inline-edit" data-field="remarks" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->studentDetails?->remarks }}">
            <span class="display-value">{{ $convertedLead->studentDetails?->remarks ?? 'N/A' }}</span>
            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_academic_assistant())
            <button type="button" class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                <i class="ti ti-edit"></i>
            </button>
            @endif
        </div>
    </td>
    <td>
        <div class="" role="group">
            <a href="{{ route('admin.converted-leads.show', $convertedLead->id) }}" class="btn btn-sm btn-outline-primary" title="View Details">
                <i class="ti ti-eye"></i>
            </a>
            <a href="{{ route('admin.invoices.index', $convertedLead->id) }}" class="btn btn-sm btn-success" title="View Invoice">
                <i class="ti ti-receipt"></i>
            </a>
            @php
            $canManageCancelFlag = \App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor();
            @endphp
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
            @php $courseChanged = (bool) ($convertedLead->is_course_changed ?? false); @endphp
            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor())
            <button type="button" class="btn btn-sm {{ $courseChanged ? 'btn-success' : 'btn-danger' }} js-change-course-modal"
                title="Change Course"
                data-modal-url="{{ route('admin.converted-leads.change-course-modal', $convertedLead->id) }}"
                data-modal-title="Change Course">
                <i class="ti ti-exchange"></i>
            </button>
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
