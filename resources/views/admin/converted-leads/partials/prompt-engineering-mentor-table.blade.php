@php
    $peMentorTrackColumns = \App\Support\PromptEngineeringMentorTrackColumns::trackColumns();
    $tableColspan = 1 + 13 + 1 + count($peMentorTrackColumns) + 1;
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
        <th>Mentor Flag</th>
        @foreach($peMentorTrackColumns as $col)
        <th>{{ $col['label'] }}</th>
        @endforeach
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
        @include('admin.converted-leads.partials.inline-mentor-flag-cell', ['convertedLead' => $lead])
        @foreach($peMentorTrackColumns as $col)
        @php
            $fieldKey = $col['field'];
            $storage = $col['storage'] ?? 'mentor';
            if ($storage === 'student') {
                $rawValue = $sd?->$fieldKey;
                $displayValue = $col['type'] === 'date' ? $fmt($rawValue) : ($rawValue ?? '-');
                $currentValue = $col['type'] === 'date' ? $fmtYmd($rawValue) : ($rawValue ?? '');
            } else {
                $rawValue = \App\Support\PromptEngineeringMentorTrackColumns::mentorColumnValue($md, $fieldKey);
                $displayValue = $rawValue ?? '-';
                $currentValue = $rawValue ?? '';
            }
        @endphp
        <td>
            @if($canEdit)
            <div class="inline-edit"
                data-field="{{ $fieldKey }}"
                data-id="{{ $lead->id }}"
                @if($col['type'] === 'date') data-field-type="date" @endif
                data-current="{{ $currentValue }}">
                <span class="display-value">{{ $displayValue }}</span>
                <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit"><i class="ti ti-edit"></i></button>
            </div>
            @else
            {{ $displayValue }}
            @endif
        </td>
        @endforeach
        <td>
            <a href="{{ route('admin.converted-leads.show', $lead->id) }}" class="btn btn-sm btn-outline-primary" title="View"><i class="ti ti-eye"></i></a>
            <a href="{{ route('admin.invoices.index', $lead->id) }}" class="btn btn-sm btn-success" title="Invoice"><i class="ti ti-receipt"></i></a>
        </td>
    </tr>
    @empty
    <tr>
        <td colspan="{{ $tableColspan }}" class="text-center">No records found.</td>
    </tr>
    @endforelse
</tbody>
