@php
    $orientationOptions = ['Participated' => 'Participated', 'Did not participated' => 'Did not participated'];
    $peFacultyTrackColumns = [
        ['type' => 'number', 'field' => 'pe_attendance_days_1_10', 'min' => 1, 'max' => 10],
        ['type' => 'number', 'field' => 'pe_practical_work_1_5', 'min' => 1, 'max' => 5],
        ['type' => 'text', 'field' => 'pe_first_periodical_test'],
        ['type' => 'number', 'field' => 'pe_attendance_days_11_20', 'min' => 11, 'max' => 20],
        ['type' => 'text', 'field' => 'pe_second_periodical_test'],
        ['type' => 'number', 'field' => 'pe_attendance_days_21_30', 'min' => 21, 'max' => 30],
        ['type' => 'number', 'field' => 'pe_practical_work_11_15', 'min' => 11, 'max' => 15],
        ['type' => 'text', 'field' => 'pe_final_examination'],
    ];
@endphp
<thead>
    <tr>
        <th>SL</th>
        <th>Conversation Date</th>
        <th>Team</th>
        <th>Registration Number</th>
        <th>Full Name</th>
        <th>Age</th>
        <th>Primary Mobile Number</th>
        <th>WhatsApp Number</th>
        <th>Medium of Study</th>
        <th>Previous Qualification</th>
        <th>Technology Proficiency</th>
        <th>Batch</th>
        <th>Academic Batch</th>
        <th>Class Timing</th>
        <th>Faculty Flag</th>
        <th>Total Class Days</th>
        <th>Orientation Status</th>
        <th>Attendance (Days 1–10)</th>
        <th>Practical Work (1–5)</th>
        <th>First Periodical Test</th>
        <th>Attendance (Days 11–20)</th>
        <th>Second Periodical Test</th>
        <th>Attendance (Days 21–30)</th>
        <th>Practical Work (11–15)</th>
        <th>Final Examination</th>
        <th>Course Completion Date</th>
        <th>Course Status</th>
        <th>Student Feedback</th>
        <th>Actions</th>
    </tr>
</thead>
<tbody>
    @forelse($convertedLeads as $index => $lead)
    @php
        $peLead = $lead->lead ? $lead->lead->promptEngineeringStudentDetails : null;
        $md = $lead->mentorDetails;
        $sd = $lead->studentDetails;
        $age = $lead->dob ? \Carbon\Carbon::parse($lead->dob)->age : null;
        $conversationDate = $lead->lead && $lead->lead->first_created_at
            ? $lead->lead->first_created_at->format('d-m-Y')
            : ($lead->lead && $lead->lead->created_at ? $lead->lead->created_at->format('d-m-Y') : '-');
        $teamLabel = $lead->is_b2b == 1 && $lead->lead && $lead->lead->team
            ? $lead->lead->team->name
            : ($lead->is_b2b == 1 ? 'B2B' : 'In House');
        $fmt = function ($d) { return $d ? \Carbon\Carbon::parse($d)->format('d-m-Y') : '-'; };
        $fmtYmd = function ($d) { return $d ? \Carbon\Carbon::parse($d)->format('Y-m-d') : ''; };
        $completionYmd = $sd && $sd->class_ending_date ? $fmtYmd($sd->class_ending_date) : '';
    @endphp
    <tr class="{{ $lead->is_cancelled ? 'cancelled-row' : '' }}">
        <td>{{ $index + 1 }}</td>
        <td>{{ $conversationDate }}</td>
        <td>{{ $teamLabel }}</td>
        <td>
            @if($canEdit)
            <div class="inline-edit" data-field="register_number" data-id="{{ $lead->id }}" data-current="{{ $lead->register_number }}">
                <span class="display-value">{{ $lead->register_number ?? '-' }}</span>
                <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit"><i class="ti ti-edit"></i></button>
            </div>
            @else
            {{ $lead->register_number ?? '-' }}
            @endif
        </td>
        <td>
            @if($canEdit)
            <div class="inline-edit" data-field="name" data-id="{{ $lead->id }}" data-current="{{ $lead->name }}">
                <span class="display-value">{{ $lead->name }}</span>
                <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit"><i class="ti ti-edit"></i></button>
            </div>
            @else
            {{ $lead->name }}
            @endif
        </td>
        <td>{{ $age !== null ? $age : '-' }}</td>
        <td>
            @if($canEdit)
            <div class="inline-edit" data-field="phone" data-id="{{ $lead->id }}" data-current="{{ $lead->phone }}">
                <span class="display-value">{{ \App\Helpers\PhoneNumberHelper::display($lead->code, $lead->phone) }}</span>
                <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit"><i class="ti ti-edit"></i></button>
            </div>
            <div class="d-none inline-code-value" data-field="code" data-id="{{ $lead->id }}" data-current="{{ $lead->code }}"></div>
            @else
            {{ \App\Helpers\PhoneNumberHelper::display($lead->code, $lead->phone) }}
            @endif
        </td>
        <td>
            @if($canEdit)
            <div class="inline-edit" data-field="whatsapp_number" data-id="{{ $lead->id }}" data-current="{{ $peLead?->whatsapp_number }}">
                <span class="display-value">{{ $peLead && $peLead->whatsapp_number ? \App\Helpers\PhoneNumberHelper::display($peLead->whatsapp_code, $peLead->whatsapp_number) : '-' }}</span>
                <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit"><i class="ti ti-edit"></i></button>
            </div>
            @else
            {{ $peLead && $peLead->whatsapp_number ? \App\Helpers\PhoneNumberHelper::display($peLead->whatsapp_code, $peLead->whatsapp_number) : '-' }}
            @endif
        </td>
        <td>
            @if($canEdit)
            <div class="inline-edit" data-field="medium_of_study" data-id="{{ $lead->id }}" data-current="{{ $peLead?->medium_of_study }}">
                <span class="display-value">{{ $peLead && $peLead->medium_of_study ? ucfirst(str_replace('_', ' ', $peLead->medium_of_study)) : '-' }}</span>
                <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit"><i class="ti ti-edit"></i></button>
            </div>
            @else
            {{ $peLead && $peLead->medium_of_study ? ucfirst(str_replace('_', ' ', $peLead->medium_of_study)) : '-' }}
            @endif
        </td>
        <td>
            @if($canEdit)
            <div class="inline-edit" data-field="previous_qualification" data-id="{{ $lead->id }}" data-current="{{ $peLead?->previous_qualification }}">
                <span class="display-value">{{ $peLead && $peLead->previous_qualification ? ucfirst(str_replace('_', ' ', $peLead->previous_qualification)) : '-' }}</span>
                <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit"><i class="ti ti-edit"></i></button>
            </div>
            @else
            {{ $peLead && $peLead->previous_qualification ? ucfirst(str_replace('_', ' ', $peLead->previous_qualification)) : '-' }}
            @endif
        </td>
        <td>
            @if($canEdit)
            <div class="inline-edit" data-field="technology_performance_category" data-id="{{ $lead->id }}" data-current="{{ $peLead?->technology_performance_category }}">
                <span class="display-value">{{ $peLead && $peLead->technology_performance_category ? ucfirst(str_replace('_', ' ', $peLead->technology_performance_category)) : '-' }}</span>
                <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit"><i class="ti ti-edit"></i></button>
            </div>
            @else
            {{ $peLead && $peLead->technology_performance_category ? ucfirst(str_replace('_', ' ', $peLead->technology_performance_category)) : '-' }}
            @endif
        </td>
        <td>
            @if($canEdit)
            <div class="inline-edit" data-field="batch_id" data-id="{{ $lead->id }}" data-course-id="{{ $lead->course_id }}" data-current-id="{{ $lead->batch_id }}">
                <span class="display-value">{{ $lead->batch ? $lead->batch->title : 'N/A' }}</span>
                <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit"><i class="ti ti-edit"></i></button>
            </div>
            @else
            {{ $lead->batch ? $lead->batch->title : '-' }}
            @endif
        </td>
        <td>
            @if($canEdit)
            <div class="inline-edit" data-field="admission_batch_id" data-id="{{ $lead->id }}" data-batch-id="{{ $lead->batch_id }}" data-current-id="{{ $lead->admission_batch_id }}">
                <span class="display-value">{{ $lead->admissionBatch ? $lead->admissionBatch->title : '-' }}</span>
                <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit"><i class="ti ti-edit"></i></button>
            </div>
            @else
            {{ $lead->admissionBatch ? $lead->admissionBatch->title : '-' }}
            @endif
        </td>
        <td>
            @if($canEdit && $course && $course->needs_time)
            <div class="inline-edit" data-field="class_time_id" data-id="{{ $lead->id }}" data-course-id="{{ $lead->course_id }}" data-programme-type="{{ $peLead?->programme_type }}" data-current-id="{{ $peLead?->class_time_id }}">
                <span class="display-value">
                    @if($peLead && $peLead->classTime)
                    {{ \Carbon\Carbon::parse($peLead->classTime->from_time)->format('h:i A') }} - {{ \Carbon\Carbon::parse($peLead->classTime->to_time)->format('h:i A') }}
                    @else - @endif
                </span>
                <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit"><i class="ti ti-edit"></i></button>
            </div>
            @else
            @if($peLead && $peLead->classTime)
            {{ \Carbon\Carbon::parse($peLead->classTime->from_time)->format('h:i A') }} - {{ \Carbon\Carbon::parse($peLead->classTime->to_time)->format('h:i A') }}
            @else - @endif
            @endif
        </td>
        @include('admin.converted-leads.partials.inline-course-flag-cell', ['convertedLead' => $lead])
        <td>
            @if($canEdit)
            <div class="inline-edit" data-field="total_class_days" data-id="{{ $lead->id }}" data-current="{{ $md?->total_class_days ?? '' }}">
                <span class="display-value">{{ $md?->total_class_days ?? '-' }}</span>
                <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit"><i class="ti ti-edit"></i></button>
            </div>
            @else
            {{ $md?->total_class_days ?? '-' }}
            @endif
        </td>
        <td>
            @if($canEdit)
            <div class="inline-edit" data-field="orientation_class_status" data-id="{{ $lead->id }}" data-field-type="select" data-options='@json($orientationOptions)' data-current="{{ $sd?->orientation_class_status }}">
                <span class="display-value">{{ $sd?->orientation_class_status ?? '-' }}</span>
                <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit"><i class="ti ti-edit"></i></button>
            </div>
            @else
            {{ $sd?->orientation_class_status ?? '-' }}
            @endif
        </td>
        @foreach($peFacultyTrackColumns as $col)
        @php $fieldKey = $col['field']; @endphp
        <td>
            @if($canEdit)
            <div class="inline-edit"
                data-field="{{ $fieldKey }}"
                data-id="{{ $lead->id }}"
                @if($col['type'] === 'number') data-field-type="number" data-min="{{ $col['min'] }}" data-max="{{ $col['max'] }}" @endif
                data-current="{{ $md?->$fieldKey ?? '' }}">
                <span class="display-value">{{ $md?->$fieldKey ?? '-' }}</span>
                <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit"><i class="ti ti-edit"></i></button>
            </div>
            @else
            {{ $md?->$fieldKey ?? '-' }}
            @endif
        </td>
        @endforeach
        <td>
            @if($canEdit)
            <div class="inline-edit" data-field="class_ending_date" data-id="{{ $lead->id }}" data-current="{{ $completionYmd }}">
                <span class="display-value">{{ $fmt($sd?->class_ending_date) }}</span>
                <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit"><i class="ti ti-edit"></i></button>
            </div>
            @else
            {{ $fmt($sd?->class_ending_date) }}
            @endif
        </td>
        <td>
            @if($canEdit)
            <div class="inline-edit" data-field="pe_course_status" data-id="{{ $lead->id }}" data-current="{{ $md?->pe_course_status }}">
                <span class="display-value">{{ $md?->pe_course_status ?? '-' }}</span>
                <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit"><i class="ti ti-edit"></i></button>
            </div>
            @else
            {{ $md?->pe_course_status ?? '-' }}
            @endif
        </td>
        <td>
            @if($canEdit)
            <div class="inline-edit" data-field="pe_student_feedback" data-id="{{ $lead->id }}" data-current="{{ $md?->pe_student_feedback }}">
                <span class="display-value">{{ $md?->pe_student_feedback ?? '-' }}</span>
                <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit"><i class="ti ti-edit"></i></button>
            </div>
            @else
            {{ $md?->pe_student_feedback ?? '-' }}
            @endif
        </td>
        <td>
            <a href="{{ route('admin.converted-leads.show', $lead->id) }}" class="btn btn-sm btn-outline-primary" title="View"><i class="ti ti-eye"></i></a>
            <a href="{{ route('admin.invoices.index', $lead->id) }}" class="btn btn-sm btn-success" title="Invoice"><i class="ti ti-receipt"></i></a>
        </td>
    </tr>
    @empty
    <tr>
        <td colspan="29" class="text-center">No records found.</td>
    </tr>
    @endforelse
</tbody>
