@extends('layouts.mantis')

@section('title', 'Bulk Reassign Leads')

@section('content')
<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center g-2">
            <div class="col-md-7">
                <div class="d-flex align-items-center gap-3">
                    <div class="bulk-reassign-page-icon">
                        <i class="ti ti-exchange"></i>
                    </div>
                    <div>
                        <h5 class="m-b-5 mb-1">Bulk Reassign Leads</h5>
                        <p class="text-muted mb-0 small">Move leads between telecallers by team, source, status, and date range.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-5">
                <div class="d-flex justify-content-md-end align-items-center gap-2 flex-wrap">
                    <a href="{{ route('leads.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="ti ti-arrow-left me-1"></i> Back to Leads
                    </a>
                    <ul class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('leads.index') }}">Leads</a></li>
                        <li class="breadcrumb-item">Bulk Reassign</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

@if ($todayReassignSummary->isNotEmpty())
@php $todayTotal = $todayReassignSummary->sum('total_leads'); @endphp
<div class="row mb-3">
    <div class="col-12">
        <div class="today-reassign-summary">
            <div class="today-reassign-summary__icon">
                <i class="ti ti-chart-bar"></i>
            </div>
            <div class="today-reassign-summary__content">
                <span class="today-reassign-summary__label">Today · {{ now()->format('d M Y') }}</span>
                <span class="today-reassign-summary__total">{{ number_format($todayTotal) }} leads reassigned</span>
            </div>
            <div class="today-reassign-summary__badges">
                @foreach ($todayReassignSummary as $summary)
                <span class="today-reassign-summary__badge" title="Reassigned to {{ $summary->toTelecaller?->name ?? 'Unknown' }}">
                    <i class="ti ti-user me-1"></i>{{ $summary->toTelecaller?->name ?? 'Unknown' }}
                    <strong>{{ number_format($summary->total_leads) }}</strong>
                </span>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endif

<div class="row">
    <div class="col-12">
        @if ($errors->any())
        <div class="alert alert-danger border-0 shadow-sm mb-3">
            <div class="d-flex align-items-start gap-2">
                <i class="ti ti-alert-circle fs-5 mt-1"></i>
                <ul class="mb-0 ps-3">
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
        @endif

        @if (session('error'))
        <div class="alert alert-danger border-0 shadow-sm mb-3">
            <i class="ti ti-alert-circle me-1"></i>{{ session('error') }}
        </div>
        @endif

        @include('admin.leads.partials.bulk-reassign-form')
    </div>
</div>
@endsection

@push('styles')
<style>
    .bulk-reassign-page-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        background: linear-gradient(135deg, #198754 0%, #20c997 100%);
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.35rem;
        flex-shrink: 0;
        box-shadow: 0 4px 14px rgba(25, 135, 84, 0.25);
    }

    .bulk-reassign-page-icon .ti {
        font-size: 1.35rem;
        line-height: 1;
        display: block;
    }

    .today-reassign-summary {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 0.75rem 1rem;
        padding: 0.65rem 1rem;
        background: linear-gradient(90deg, #edf7f1 0%, #f8fffb 100%);
        border: 1px solid #b8dfc8;
        border-radius: 10px;
        box-shadow: 0 1px 3px rgba(25, 135, 84, 0.08);
    }

    .today-reassign-summary__icon {
        width: 34px;
        height: 34px;
        border-radius: 8px;
        background: #198754;
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
        flex-shrink: 0;
    }

    .today-reassign-summary__content {
        display: flex;
        flex-direction: column;
        gap: 0.1rem;
        min-width: 140px;
    }

    .today-reassign-summary__label {
        font-size: 0.7rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        color: #146c43;
    }

    .today-reassign-summary__total {
        font-size: 0.85rem;
        font-weight: 600;
        color: #0f5132;
    }

    .today-reassign-summary__badges {
        display: flex;
        flex-wrap: wrap;
        gap: 0.4rem;
        flex: 1;
    }

    .today-reassign-summary__badge {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.28rem 0.65rem;
        font-size: 0.75rem;
        color: #fff;
        background: #198754;
        border-radius: 50rem;
        white-space: nowrap;
    }

    .today-reassign-summary__badge strong {
        background: rgba(255, 255, 255, 0.22);
        padding: 0.05rem 0.4rem;
        border-radius: 50rem;
        font-weight: 700;
    }

    .bulk-reassign-section {
        background: #fff;
        border: 1px solid #e9ecef;
        border-radius: 12px;
        margin-bottom: 1.25rem;
        overflow: hidden;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
    }

    .bulk-reassign-section__header {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.85rem 1.15rem;
        border-bottom: 1px solid #eef1f4;
        background: #f8f9fa;
    }

    .bulk-reassign-section__step {
        width: 28px;
        height: 28px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.8rem;
        font-weight: 700;
        color: #fff;
        flex-shrink: 0;
    }

    .bulk-reassign-section--transfer .bulk-reassign-section__step { background: #198754; }
    .bulk-reassign-section--transfer .bulk-reassign-section__header { border-left: 4px solid #198754; }

    .bulk-reassign-transfer-block {
        height: 100%;
        padding: 1rem;
        border-radius: 10px;
        border: 1px solid #e9ecef;
        background: #fafbfc;
    }

    .bulk-reassign-transfer-block--to {
        border-top: 3px solid #198754;
    }

    .bulk-reassign-transfer-block--from {
        border-top: 3px solid #fd7e14;
    }

    .bulk-reassign-transfer-block__title {
        font-size: 0.88rem;
        font-weight: 700;
        margin-bottom: 0.85rem;
        display: flex;
        align-items: center;
        gap: 0.4rem;
    }

    .bulk-reassign-transfer-block--to .bulk-reassign-transfer-block__title { color: #198754; }
    .bulk-reassign-transfer-block--from .bulk-reassign-transfer-block__title { color: #fd7e14; }

    .bulk-reassign-transfer-arrow {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: #e9ecef;
        color: #495057;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        flex-shrink: 0;
    }

    .bulk-reassign-section--filter .bulk-reassign-section__step { background: #0d6efd; }
    .bulk-reassign-section--filter .bulk-reassign-section__header { border-left: 4px solid #0d6efd; }

    .bulk-reassign-section--leads .bulk-reassign-section__step { background: #6f42c1; }
    .bulk-reassign-section--leads .bulk-reassign-section__header { border-left: 4px solid #6f42c1; }

    .bulk-reassign-section__title {
        font-size: 0.95rem;
        font-weight: 600;
        margin: 0;
        color: #212529;
    }

    .bulk-reassign-section__hint {
        font-size: 0.78rem;
        color: #6c757d;
        margin: 0;
    }

    .bulk-reassign-section__body {
        padding: 1.15rem;
    }

    .bulk-reassign-field label {
        font-size: 0.82rem;
        font-weight: 600;
        color: #495057;
        margin-bottom: 0.35rem;
    }

    .bulk-reassign-field label .ti {
        font-size: 0.9rem;
        opacity: 0.7;
    }

    .bulk-reassign-leads-toolbar {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 0.75rem;
        padding: 0.75rem 1rem;
        background: #f8f9fa;
        border: 1px solid #e9ecef;
        border-radius: 10px;
        margin-bottom: 0.75rem;
    }

    .bulk-reassign-leads-toolbar__meta {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        flex-wrap: wrap;
    }

    .bulk-reassign-count-pill {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.3rem 0.7rem;
        border-radius: 50rem;
        font-size: 0.78rem;
        font-weight: 600;
        background: #e7f1ff;
        color: #0d6efd;
        border: 1px solid #cfe2ff;
    }

    .bulk-reassign-count-pill--selected {
        background: #e8f5e9;
        color: #198754;
        border-color: #c8e6c9;
    }

    .bulk-reassign-quick-select {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .bulk-reassign-quick-select label {
        font-size: 0.78rem;
        font-weight: 600;
        color: #6c757d;
        margin: 0;
        white-space: nowrap;
    }

    .bulk-reassign-quick-select input {
        width: 90px;
        font-size: 0.85rem;
    }

    .bulk-operations-table {
        max-height: calc(100vh - 480px);
        min-height: 180px;
        overflow-y: auto;
        border: 1px solid #e9ecef;
        border-radius: 10px;
        position: relative;
    }

    .bulk-operations-table.is-loading::after {
        content: '';
        position: absolute;
        inset: 0;
        background: rgba(255, 255, 255, 0.75);
        z-index: 2;
    }

    .bulk-operations-table__loader {
        display: none;
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        z-index: 3;
        text-align: center;
        color: #6c757d;
        font-size: 0.85rem;
    }

    .bulk-operations-table.is-loading .bulk-operations-table__loader {
        display: block;
    }

    .bulk-table {
        margin-bottom: 0;
    }

    .bulk-table thead th {
        background: #f1f3f5;
        position: sticky;
        top: 0;
        z-index: 1;
        font-size: 0.78rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.03em;
        color: #495057;
        border-bottom: 2px solid #dee2e6;
        white-space: nowrap;
        vertical-align: middle;
    }

    .bulk-table tbody td {
        font-size: 0.85rem;
        vertical-align: middle;
    }

    .bulk-table tbody tr:hover {
        background-color: #f8f9fa;
    }

    .bulk-table .bulk-empty-row td {
        padding: 2.5rem 1rem;
        text-align: center;
        color: #6c757d;
        background: #fafbfc;
    }

    .bulk-table .bulk-empty-row .ti {
        font-size: 2rem;
        opacity: 0.35;
        display: block;
        margin-bottom: 0.5rem;
    }

    .bulk-checkbox {
        width: 18px;
        height: 18px;
        cursor: pointer;
    }

    .bulk-reassign-actions {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 0.75rem;
        padding: 1rem 1.15rem;
        background: #f8f9fa;
        border: 1px solid #e9ecef;
        border-radius: 12px;
        margin-top: 0.5rem;
    }

    .bulk-reassign-actions__hint {
        font-size: 0.8rem;
        color: #6c757d;
        margin: 0;
    }

  .bulk-reassign-select2 + .select2-container,
    .bulk-reassign-filter-select + .select2-container {
        width: 100% !important;
    }

    @media (max-width: 767.98px) {
        .bulk-reassign-page-icon {
            width: 40px;
            height: 40px;
            font-size: 1.1rem;
        }

        .today-reassign-summary__content {
            width: 100%;
        }

        .bulk-reassign-leads-toolbar {
            flex-direction: column;
            align-items: stretch;
        }

        .bulk-reassign-quick-select {
            justify-content: space-between;
        }

        .bulk-reassign-transfer-arrow {
            transform: rotate(90deg);
            margin: 0.5rem auto;
        }
    }
</style>
@endpush
