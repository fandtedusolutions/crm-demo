@php
    $convertedLead = $convertedLead ?? ($lead ?? null);
    $addonCourse = $convertedLead?->leadDetail?->addonCourse;
    $addonCourseId = (int) ($convertedLead?->leadDetail?->addon_course_id ?? 0);
    $showAddonBadge = $convertedLead
        && $addonCourse
        && $addonCourseId > 0
        && $addonCourseId !== (int) $convertedLead->course_id;
@endphp
@if($showAddonBadge)
    <div class="mt-1">
        <span class="badge cl-addon-badge">Addon: {{ $addonCourse->title }}</span>
        @if($convertedLead->course && (int) $convertedLead->course_id !== $addonCourseId)
            <small class="text-muted d-block">Primary: {{ $convertedLead->course->title }}</small>
        @endif
    </div>
@endif
