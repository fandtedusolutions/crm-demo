@php
    $areas = ($convertedLead->subjectAreas ?? collect())->sortBy('title', SORT_NATURAL | SORT_FLAG_CASE);
@endphp
@if($areas->isNotEmpty())
    <span class="converted-lead-subject-areas-display">{{ $areas->pluck('title')->filter()->implode(', ') }}</span>
@else
    <span class="text-muted">N/A</span>
@endif
