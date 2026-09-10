@php
    $approvalStatus = $convertedLead->finance_approval ?? 'Pending';
    $badgeClass = $approvalStatus === 'Approved' ? 'bg-success' : 'bg-warning text-dark';
    $canEditFinanceApproval = \App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_finance();
@endphp
<div class="inline-edit" data-field="finance_approval" data-id="{{ $convertedLead->id }}" data-current="{{ $approvalStatus }}">
    <span class="display-value"><span class="badge {{ $badgeClass }}">{{ $approvalStatus }}</span></span>
    @if($canEditFinanceApproval)
    <button type="button" class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit Finance Approval">
        <i class="ti ti-edit"></i>
    </button>
    @endif
</div>
