                                <tr class="{{ $convertedLead->is_cancelled ? 'cancelled-row' : '' }}">
                                    @php
                                    $academicVerifiedAt = $convertedLead->academic_verified_at
                                    ? $convertedLead->academic_verified_at->copy()->timezone($appTimezone)->format('d-m-Y h:i A')
                                    : null;
                                    $selectedCourses = [];
                                    if ($convertedLead->leadDetail && $convertedLead->leadDetail->selected_courses) {
                                        try {
                                            $selectedCourses = json_decode($convertedLead->leadDetail->selected_courses, true);
                                            if (!is_array($selectedCourses)) {
                                                $selectedCourses = [];
                                            }
                                        } catch (\Exception $e) {
                                            $selectedCourses = [];
                                        }
                                    }
                                    @endphp
                                    <td>{{ $displayIndex }}</td>
                                    <td>
                                        @if($academicVerifiedAt)
                                        <span class="badge bg-success">Verified</span><br>
                                        <small class="text-muted">{{ $academicVerifiedAt }}</small>
                                        @else
                                        <span class="badge bg-warning">Not Verified</span>
                                        @endif
                                    </td>
                                    <td>{{ $convertedLead->created_at->format('d-m-Y') }}</td>
                                    <td>{{ $convertedLead->register_number ?? '-' }}</td>
                                    @include('admin.converted-leads.partials.inline-mentor-flag-cell', ['convertedLead' => $convertedLead])
                                    @include('admin.converted-leads.partials.inline-call-time-cell', ['convertedLead' => $convertedLead])
                                    <td>
                                        {{ $convertedLead->name }}
                                        @if($convertedLead->is_cancelled)
                                        <div>
                                            <span class="badge bg-danger ms-2">Cancelled</span>
                                        </div>
                                        @endif
                                    </td>
                                    <td>{{ $convertedLead->is_b2b == 1 ? ('B2B' . ($convertedLead->lead?->team?->name ? ' (' . $convertedLead->lead->team->name . ')' : '')) : 'In House' }}</td>
                                    <td>
                                        @php
                                        $dobDisplay = '-';
                                        if ($convertedLead->leadDetail && $convertedLead->leadDetail->date_of_birth) {
                                            $dobDisplay = $convertedLead->leadDetail->date_of_birth->format('d-m-Y');
                                        } elseif ($convertedLead->dob) {
                                            $dobDisplay = strtotime($convertedLead->dob) ? date('d-m-Y', strtotime($convertedLead->dob)) : $convertedLead->dob;
                                        }
                                        @endphp
                                        {{ $dobDisplay }}
                                    </td>
                                    <td>{{ \App\Helpers\PhoneNumberHelper::display($convertedLead->code, $convertedLead->phone) }}</td>
                                    <td>
                                        @if($convertedLead->leadDetail && $convertedLead->leadDetail->whatsapp_number)
                                            {{ \App\Helpers\PhoneNumberHelper::display($convertedLead->leadDetail->whatsapp_code, $convertedLead->leadDetail->whatsapp_number) }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor())
                                    <td>
                                        @if($convertedLead->leadDetail && $convertedLead->leadDetail->parents_number)
                                            {{ \App\Helpers\PhoneNumberHelper::display($convertedLead->leadDetail->parents_code, $convertedLead->leadDetail->parents_number) }}
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    @endif
                                    <td>{{ $convertedLead->email ?? '-' }}</td>
                                    <td>{{ $convertedLead->batch ? $convertedLead->batch->title : '-' }}</td>
                                    <td>{{ $convertedLead->admissionBatch ? $convertedLead->admissionBatch->title : '-' }}</td>
                                    <td>
                                        @if(!empty($selectedCourses))
                                            {{ implode(', ', $selectedCourses) }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <!-- SSLC Back Year Fields -->
                                    <td>{{ $convertedLead->leadDetail?->sslc_back_year ?? '-' }}</td>
                                    <td>
                                        <div class="inline-edit" data-field="sslc_enrollment_number" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->mentorDetails?->sslc_enrollment_number }}">
                                            <span class="display-value">{{ $convertedLead->mentorDetails?->sslc_enrollment_number ?? '-' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_mentor())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="inline-edit" data-field="sslc_registration_link_id" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->mentorDetails?->sslc_registration_link_id }}">
                                            <span class="display-value">{{ $convertedLead->mentorDetails?->sslcRegistrationLink?->title ?? '-' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_mentor())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="inline-edit" data-field="sslc_online_result_publication_date" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->mentorDetails?->sslc_online_result_publication_date ? $convertedLead->mentorDetails->sslc_online_result_publication_date->format('Y-m-d') : '' }}">
                                            <span class="display-value">{{ $convertedLead->mentorDetails?->sslc_online_result_publication_date ? $convertedLead->mentorDetails->sslc_online_result_publication_date->format('d-m-Y') : '-' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_mentor())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="inline-edit" data-field="sslc_certificate_publication_date" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->mentorDetails?->sslc_certificate_publication_date ? $convertedLead->mentorDetails->sslc_certificate_publication_date->format('Y-m-d') : '' }}">
                                            <span class="display-value">{{ $convertedLead->mentorDetails?->sslc_certificate_publication_date ? $convertedLead->mentorDetails->sslc_certificate_publication_date->format('d-m-Y') : '-' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_mentor())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="inline-edit" data-field="sslc_certificate_issued_date" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->mentorDetails?->sslc_certificate_issued_date ? $convertedLead->mentorDetails->sslc_certificate_issued_date->format('Y-m-d') : '' }}">
                                            <span class="display-value">{{ $convertedLead->mentorDetails?->sslc_certificate_issued_date ? $convertedLead->mentorDetails->sslc_certificate_issued_date->format('d-m-Y') : '-' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_mentor())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="inline-edit" data-field="sslc_certificate_distribution_date" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->mentorDetails?->sslc_certificate_distribution_date ? $convertedLead->mentorDetails->sslc_certificate_distribution_date->format('Y-m-d') : '' }}">
                                            <span class="display-value">{{ $convertedLead->mentorDetails?->sslc_certificate_distribution_date ? $convertedLead->mentorDetails->sslc_certificate_distribution_date->format('d-m-Y') : '-' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_mentor())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="inline-edit" data-field="sslc_courier_tracking_number" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->mentorDetails?->sslc_courier_tracking_number }}">
                                            <span class="display-value">{{ $convertedLead->mentorDetails?->sslc_courier_tracking_number ?? '-' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_mentor())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="inline-edit" data-field="sslc_remarks" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->mentorDetails?->sslc_remarks }}">
                                            <span class="display-value">{{ $convertedLead->mentorDetails?->sslc_remarks ?? '-' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_mentor())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <!-- Plus Two Back Year Fields -->
                                    <td>{{ $convertedLead->leadDetail?->plustwo_back_year ?? '-' }}</td>
                                    <td>
                                        <div class="inline-edit" data-field="plustwo_subject_no" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->mentorDetails?->plustwo_subject_no }}">
                                            <span class="display-value">{{ $convertedLead->mentorDetails?->plustwo_subject_no ?? '-' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_mentor())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="inline-edit" data-field="plustwo_enrollment_number" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->mentorDetails?->plustwo_enrollment_number }}">
                                            <span class="display-value">{{ $convertedLead->mentorDetails?->plustwo_enrollment_number ?? '-' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_mentor())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="inline-edit" data-field="plustwo_registration_link_id" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->mentorDetails?->plustwo_registration_link_id }}">
                                            <span class="display-value">{{ $convertedLead->mentorDetails?->plustwoRegistrationLink?->title ?? '-' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_mentor())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="inline-edit" data-field="plustwo_online_result_publication_date" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->mentorDetails?->plustwo_online_result_publication_date ? $convertedLead->mentorDetails->plustwo_online_result_publication_date->format('Y-m-d') : '' }}">
                                            <span class="display-value">{{ $convertedLead->mentorDetails?->plustwo_online_result_publication_date ? $convertedLead->mentorDetails->plustwo_online_result_publication_date->format('d-m-Y') : '-' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_mentor())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="inline-edit" data-field="plustwo_certificate_publication_date" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->mentorDetails?->plustwo_certificate_publication_date ? $convertedLead->mentorDetails->plustwo_certificate_publication_date->format('Y-m-d') : '' }}">
                                            <span class="display-value">{{ $convertedLead->mentorDetails?->plustwo_certificate_publication_date ? $convertedLead->mentorDetails->plustwo_certificate_publication_date->format('d-m-Y') : '-' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_mentor())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="inline-edit" data-field="plustwo_certificate_issued_date" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->mentorDetails?->plustwo_certificate_issued_date ? $convertedLead->mentorDetails->plustwo_certificate_issued_date->format('Y-m-d') : '' }}">
                                            <span class="display-value">{{ $convertedLead->mentorDetails?->plustwo_certificate_issued_date ? $convertedLead->mentorDetails->plustwo_certificate_issued_date->format('d-m-Y') : '-' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_mentor())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="inline-edit" data-field="plustwo_certificate_distribution_date" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->mentorDetails?->plustwo_certificate_distribution_date ? $convertedLead->mentorDetails->plustwo_certificate_distribution_date->format('Y-m-d') : '' }}">
                                            <span class="display-value">{{ $convertedLead->mentorDetails?->plustwo_certificate_distribution_date ? $convertedLead->mentorDetails->plustwo_certificate_distribution_date->format('d-m-Y') : '-' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_mentor())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="inline-edit" data-field="plustwo_courier_tracking_number" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->mentorDetails?->plustwo_courier_tracking_number }}">
                                            <span class="display-value">{{ $convertedLead->mentorDetails?->plustwo_courier_tracking_number ?? '-' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_mentor())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="inline-edit" data-field="plustwo_remarks" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->mentorDetails?->plustwo_remarks }}">
                                            <span class="display-value">{{ $convertedLead->mentorDetails?->plustwo_remarks ?? '-' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_mentor())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <!-- Degree Back Year Fields -->
                                    <td>{{ $convertedLead->leadDetail?->university?->title ?? '-' }}</td>
                                    <td>{{ $convertedLead->leadDetail?->course_type ?? '-' }}</td>
                                    <td>{{ $convertedLead->leadDetail?->edumaster_course_name ?? '-' }}</td>
                                    <td>{{ $convertedLead->leadDetail?->back_year ?? '-' }}</td>
                                    <td>
                                        <div class="inline-edit" data-field="degree_registration_start_date" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->mentorDetails?->degree_registration_start_date ? $convertedLead->mentorDetails->degree_registration_start_date->format('Y-m-d') : '' }}">
                                            <span class="display-value">{{ $convertedLead->mentorDetails?->degree_registration_start_date ? $convertedLead->mentorDetails->degree_registration_start_date->format('d-m-Y') : '-' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_mentor())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="inline-edit" data-field="degree_registration_form_summary_distribution_date" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->mentorDetails?->degree_registration_form_summary_distribution_date ? $convertedLead->mentorDetails->degree_registration_form_summary_distribution_date->format('Y-m-d') : '' }}">
                                            <span class="display-value">{{ $convertedLead->mentorDetails?->degree_registration_form_summary_distribution_date ? $convertedLead->mentorDetails->degree_registration_form_summary_distribution_date->format('d-m-Y') : '-' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_mentor())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="inline-edit" data-field="degree_registration_form_summary_submission_date" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->mentorDetails?->degree_registration_form_summary_submission_date ? $convertedLead->mentorDetails->degree_registration_form_summary_submission_date->format('Y-m-d') : '' }}">
                                            <span class="display-value">{{ $convertedLead->mentorDetails?->degree_registration_form_summary_submission_date ? $convertedLead->mentorDetails->degree_registration_form_summary_submission_date->format('d-m-Y') : '-' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_mentor())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="inline-edit" data-field="degree_id_card_issued_date" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->mentorDetails?->degree_id_card_issued_date ? $convertedLead->mentorDetails->degree_id_card_issued_date->format('Y-m-d') : '' }}">
                                            <span class="display-value">{{ $convertedLead->mentorDetails?->degree_id_card_issued_date ? $convertedLead->mentorDetails->degree_id_card_issued_date->format('d-m-Y') : '-' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_mentor())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="inline-edit" data-field="degree_first_year_result_date" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->mentorDetails?->degree_first_year_result_date ? $convertedLead->mentorDetails->degree_first_year_result_date->format('Y-m-d') : '' }}">
                                            <span class="display-value">{{ $convertedLead->mentorDetails?->degree_first_year_result_date ? $convertedLead->mentorDetails->degree_first_year_result_date->format('d-m-Y') : '-' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_mentor())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="inline-edit" data-field="degree_second_year_result_date" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->mentorDetails?->degree_second_year_result_date ? $convertedLead->mentorDetails->degree_second_year_result_date->format('Y-m-d') : '' }}">
                                            <span class="display-value">{{ $convertedLead->mentorDetails?->degree_second_year_result_date ? $convertedLead->mentorDetails->degree_second_year_result_date->format('d-m-Y') : '-' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_mentor())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="inline-edit" data-field="degree_third_year_result_date" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->mentorDetails?->degree_third_year_result_date ? $convertedLead->mentorDetails->degree_third_year_result_date->format('Y-m-d') : '' }}">
                                            <span class="display-value">{{ $convertedLead->mentorDetails?->degree_third_year_result_date ? $convertedLead->mentorDetails->degree_third_year_result_date->format('d-m-Y') : '-' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_mentor())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="inline-edit" data-field="degree_online_result_publication_date" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->mentorDetails?->degree_online_result_publication_date ? $convertedLead->mentorDetails->degree_online_result_publication_date->format('Y-m-d') : '' }}">
                                            <span class="display-value">{{ $convertedLead->mentorDetails?->degree_online_result_publication_date ? $convertedLead->mentorDetails->degree_online_result_publication_date->format('d-m-Y') : '-' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_mentor())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="inline-edit" data-field="degree_certificate_publication_date" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->mentorDetails?->degree_certificate_publication_date ? $convertedLead->mentorDetails->degree_certificate_publication_date->format('Y-m-d') : '' }}">
                                            <span class="display-value">{{ $convertedLead->mentorDetails?->degree_certificate_publication_date ? $convertedLead->mentorDetails->degree_certificate_publication_date->format('d-m-Y') : '-' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_mentor())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="inline-edit" data-field="degree_certificate_issued_date" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->mentorDetails?->degree_certificate_issued_date ? $convertedLead->mentorDetails->degree_certificate_issued_date->format('Y-m-d') : '' }}">
                                            <span class="display-value">{{ $convertedLead->mentorDetails?->degree_certificate_issued_date ? $convertedLead->mentorDetails->degree_certificate_issued_date->format('d-m-Y') : '-' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_mentor())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="inline-edit" data-field="degree_certificate_distribution_date" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->mentorDetails?->degree_certificate_distribution_date ? $convertedLead->mentorDetails->degree_certificate_distribution_date->format('Y-m-d') : '' }}">
                                            <span class="display-value">{{ $convertedLead->mentorDetails?->degree_certificate_distribution_date ? $convertedLead->mentorDetails->degree_certificate_distribution_date->format('d-m-Y') : '-' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_mentor())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="inline-edit" data-field="degree_courier_tracking_number" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->mentorDetails?->degree_courier_tracking_number }}">
                                            <span class="display-value">{{ $convertedLead->mentorDetails?->degree_courier_tracking_number ?? '-' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_mentor())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="inline-edit" data-field="degree_remarks" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->mentorDetails?->degree_remarks }}">
                                            <span class="display-value">{{ $convertedLead->mentorDetails?->degree_remarks ?? '-' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_mentor())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        @if($convertedLead->mentorDetails?->is_placement_passed)
                                            <span class="badge bg-success">Placement Passed</span>
                                            @if($convertedLead->mentorDetails?->is_placement_passed_at)
                                                <br><small class="text-muted">{{ $convertedLead->mentorDetails->is_placement_passed_at->format('d-m-Y h:i A') }}</small>
                                            @endif
                                            @if($convertedLead->mentorDetails?->placement_resume)
                                                <br><a href="{{ asset('storage/' . $convertedLead->mentorDetails->placement_resume) }}" target="_blank" class="btn btn-sm btn-link p-0 small"><i class="ti ti-file-text"></i> View Resume</a>
                                                @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor())
                                                    <br><a href="javascript:void(0);" class="btn btn-sm {{ $convertedLead->mentorDetails->is_resume_verified ? 'btn-success' : 'btn-outline-success' }} px-2 py-0"
                                                        onclick="show_small_modal('{{ route('admin.converted-leads.verify-resume-modal', $convertedLead->id) }}', 'Resume Verification')"
                                                        title="Resume Verification">
                                                        <i class="ti ti-circle-check"></i> {{ $convertedLead->mentorDetails->is_resume_verified ? 'Resume Verified' : 'Verify Resume' }}@if($convertedLead->mentorDetails->is_resume_verified && $convertedLead->mentorDetails->resume_verified_at) ({{ $convertedLead->mentorDetails->resume_verified_at->format('d M Y') }})@endif
                                                    </a>
                                                    <br><a href="javascript:void(0);" class="btn btn-sm btn-outline-primary px-2 py-0"
                                                        onclick="show_small_modal('{{ route('admin.converted-leads.move-to-placement', $convertedLead->id) }}', 'Change Resume')"
                                                        title="Change Resume">
                                                        <i class="ti ti-upload"></i> Change Resume
                                                    </a>
                                                @endif
                                            @endif
                                        @else
                                            <a href="javascript:void(0);" class="btn btn-outline-primary btn-sm px-2"
                                                onclick="show_small_modal('{{ route('admin.converted-leads.move-to-placement', $convertedLead->id) }}', 'Move to Placement')"
                                                title="Move to Placement">
                                                <i class="ti ti-user-check"></i> Move to Placement
                                            </a>
                                        @endif
                                    </td>
                                </tr>
