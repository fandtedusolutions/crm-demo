@php
    $canToggleAcademic = \App\Helpers\RoleHelper::is_admin_or_super_admin()
        || \App\Helpers\RoleHelper::is_academic_assistant()
        || \App\Helpers\RoleHelper::is_admission_counsellor();
@endphp
@include('admin.converted-leads.partials.status-badge', [
    'convertedLead' => $convertedLead,
    'type' => 'academic',
    'showToggle' => $canToggleAcademic,
    'toggleUrl' => $canToggleAcademic ? route('admin.converted-leads.toggle-academic-verify', $convertedLead->id) : null,
    'title' => 'academic',
    'useModal' => true,
    'compact' => true,
])
