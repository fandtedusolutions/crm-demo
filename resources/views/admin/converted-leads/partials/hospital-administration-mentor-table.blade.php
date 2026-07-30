@php
    $haTrackColumns = \App\Support\HospitalAdministrationMentorTrackColumns::trackColumns();
    $tableColspan = 15 + count($haTrackColumns) + 1;
@endphp
<thead>
    <tr>
        <th>SL</th>
        <th>Conversation Date</th>
        <th>Team</th>
        <th>Registration Number</th>
        <th>Full Name</th>
        <th>Age</th>
        <th>Mobile Number</th>
        <th>WhatsApp Number</th>
        <th>Medium of Study</th>
        <th>Previous Qualification</th>
        <th>Technology Proficiency</th>
        <th>Batch</th>
        <th>Academic Batch</th>
        <th>Class Timing</th>
        <th>Mentor Flag</th>
        @foreach($haTrackColumns as $col)
        <th>{{ $col['label'] }}</th>
        @endforeach
        <th>Actions</th>
    </tr>
</thead>
<tbody>
    @forelse($convertedLeads as $index => $lead)
    @php
        $md = $lead->mentorDetails;
        $ld = $lead->leadDetail;
        $age = $lead->dob ? \Carbon\Carbon::parse($lead->dob)->age : null;
        $conversationDate = $lead->created_at ? $lead->created_at->format('d-m-Y') : '-';
        $teamLabel = $lead->is_b2b == 1 && $lead->lead && $lead->lead->team
            ? $lead->lead->team->name
            : ($lead->is_b2b == 1 ? 'B2B' : 'In House');
        $fmt = function ($d) { return $d ? \Carbon\Carbon::parse($d)->format('d-m-Y') : '-'; };
        $fmtYmd = function ($d) { return $d ? \Carbon\Carbon::parse($d)->format('Y-m-d') : ''; };
        $classTimeLabel = '-';
        if ($ld && $ld->classTime) {
            $classTimeLabel = \Carbon\Carbon::parse($ld->classTime->from_time)->format('h:i A')
                .' - '.\Carbon\Carbon::parse($ld->classTime->to_time)->format('h:i A');
        }
    @endphp
    <tr class="{{ $lead->is_cancelled ? 'cancelled-row' : '' }}">
        <td>{{ $convertedLeads->firstItem() + $index }}</td>
        <td>{{ $conversationDate }}</td>
        <td>{{ $teamLabel }}</td>
        <td>{{ $lead->register_number ?: '-' }}</td>
        <td>{{ $lead->name }}</td>
        <td>{{ $age !== null ? $age : '-' }}</td>
        <td>{{ \App\Helpers\PhoneNumberHelper::display($lead->code, $lead->phone) }}</td>
        <td>
            @if($ld && $ld->whatsapp_number)
                {{ \App\Helpers\PhoneNumberHelper::display($ld->whatsapp_code, $ld->whatsapp_number) }}
            @else
                -
            @endif
        </td>
        <td>{{ $ld && $ld->medium_of_study ? ucfirst(str_replace('_', ' ', $ld->medium_of_study)) : '-' }}</td>
        <td>{{ $ld && $ld->previous_qualification ? ucfirst(str_replace('_', ' ', $ld->previous_qualification)) : '-' }}</td>
        <td>{{ $ld && $ld->technology_performance_category ? ucfirst(str_replace('_', ' ', $ld->technology_performance_category)) : '-' }}</td>
        <td>{{ $lead->batch?->title ?: '-' }}</td>
        <td>{{ $lead->admissionBatch?->title ?: '-' }}</td>
        <td>{{ $classTimeLabel }}</td>
        @include('admin.converted-leads.partials.inline-mentor-flag-cell', ['convertedLead' => $lead])
        @foreach($haTrackColumns as $col)
        @php
            $fieldKey = $col['field'];
            $rawValue = \App\Support\HospitalAdministrationMentorTrackColumns::trackValue($md, $fieldKey);
            if (($col['type'] ?? '') === 'date') {
                $displayValue = $rawValue ? $fmt($rawValue) : '-';
                $currentValue = $rawValue ? $fmtYmd($rawValue) : '';
            } else {
                $displayValue = ($rawValue !== null && $rawValue !== '') ? $rawValue : '-';
                $currentValue = $rawValue ?? '';
            }
            $optionsJson = !empty($col['options']) ? htmlspecialchars(json_encode($col['options']), ENT_QUOTES, 'UTF-8') : null;
        @endphp
        <td>
            @if($canEdit)
            <div class="inline-edit"
                data-field="{{ $fieldKey }}"
                data-id="{{ $lead->id }}"
                @if(($col['type'] ?? '') === 'date') data-field-type="date"
                @elseif(($col['type'] ?? '') === 'select') data-field-type="select" data-options="{{ $optionsJson }}"
                @endif
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
