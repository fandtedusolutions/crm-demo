@php
    $canToggleSupport = \App\Helpers\RoleHelper::is_admin_or_super_admin()
        || \App\Helpers\RoleHelper::is_support_team();
@endphp
@include('admin.converted-leads.partials.status-badge', [
    'convertedLead' => $convertedLead,
    'type' => 'support',
    'showToggle' => $canToggleSupport,
    'toggleUrl' => $canToggleSupport ? route('admin.support-converted-leads.toggle-support-verify', $convertedLead->id) : null,
    'title' => 'support',
    'useModal' => true,
    'compact' => true,
])
