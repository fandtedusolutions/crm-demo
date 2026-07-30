@extends('layouts.mantis')

@section('title', 'Certificate Course in Medical Coding Mentor List')

@section('content')
@php
$canEdit = \App\Helpers\RoleHelper::is_admin_or_super_admin()
    || \App\Helpers\RoleHelper::is_admission_counsellor()
    || \App\Helpers\RoleHelper::is_academic_assistant()
    || \App\Helpers\RoleHelper::is_hod()
    || \App\Helpers\RoleHelper::is_mentor();
@endphp
<style>
    .table td { white-space: nowrap; vertical-align: middle; }
    .table td .btn-group { white-space: nowrap; }
    .table td .inline-edit { white-space: nowrap; }
    .table td .display-value {
        white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
        max-width: 150px; display: inline-block;
    }
    .cancelled-row > td { background-color: #fff1f0 !important; }
    .cancelled-card { border: 1px solid #f5c2c7; background-color: #fff5f5; }
    .inline-edit .edit-form { display: none; }
    .inline-edit.editing .edit-form { display: block; }
    .inline-edit.editing .display-value { display: none !important; }
    .inline-edit.editing .edit-btn { display: none !important; }
    .inline-edit .edit-form input,
    .inline-edit .edit-form select { min-width: 120px; }
    .table-responsive { overflow-x: auto; -webkit-overflow-scrolling: touch; }
    #mcMentorTable thead th,
    #mcMentorTable tbody td { white-space: nowrap; }
    #mcMentorTable thead th {
        position: sticky; top: 0; background: #f8f9fa; z-index: 1;
        box-shadow: 0 1px 0 #dee2e6;
    }
    #mcMentorTable tbody tr:hover { background: #fafbff; }
    #mcMentorTable td .display-value {
        display: inline-block; max-width: 220px; overflow: hidden; text-overflow: ellipsis;
    }
</style>

<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-6">
                <div class="page-header-title">
                    <h5 class="m-b-10">Certificate Course in Medical Coding Mentor List</h5>
                </div>
            </div>
            <div class="col-md-6">
                <ul class="breadcrumb d-flex justify-content-end">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.converted-leads.index') }}">Converted Leads</a></li>
                    <li class="breadcrumb-item">Medical Coding Mentor</li>
                </ul>
            </div>
        </div>
    </div>
</div>

@include('admin.converted-leads.partials.converted-leads-course-nav')
@include('admin.converted-leads.partials.mentor-list-nav', ['activeMentorRoute' => $activeMentorRoute ?? null])
@include('admin.converted-leads.partials.faculty-list-nav', ['activeFacultyRoute' => $activeFacultyRoute ?? null])
@include('admin.converted-leads.partials.converted-leads-support-nav')

<div class="row mb-3">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form method="GET" action="{{ route('admin.medical-coding-mentor-converted-leads.index') }}" id="filterForm">
                    <div class="row g-3 align-items-end">
                        <div class="col-12 col-sm-6 col-md-2">
                            <label for="search" class="form-label">Search</label>
                            <input type="text" class="form-control" id="search" name="search" value="{{ request('search') }}" placeholder="Name, Phone, Register No">
                        </div>
                        <div class="col-12 col-sm-6 col-md-2">
                            <label for="batch_id" class="form-label">Batch</label>
                            <select class="form-select" id="batch_id" name="batch_id">
                                <option value="">All</option>
                                @foreach($batches as $b)
                                <option value="{{ $b->id }}" {{ request('batch_id') == $b->id ? 'selected' : '' }}>{{ $b->title }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-sm-6 col-md-2">
                            <label for="admission_batch_id" class="form-label">Academic Batch</label>
                            <select class="form-select" id="admission_batch_id" name="admission_batch_id" data-selected="{{ request('admission_batch_id') }}">
                                <option value="">All</option>
                            </select>
                        </div>
                        @include('admin.converted-leads.partials.mentor-flag-filter-field')
                        <div class="col-12 col-sm-6 col-md-2">
                            <label for="date_from" class="form-label">From Date</label>
                            <input type="date" class="form-control" id="date_from" name="date_from" value="{{ request('date_from') }}">
                        </div>
                        <div class="col-12 col-sm-6 col-md-2">
                            <label for="date_to" class="form-label">To Date</label>
                            <input type="date" class="form-control" id="date_to" name="date_to" value="{{ request('date_to') }}">
                        </div>
                        <div class="col-12 col-sm-6 col-md-2">
                            <button type="submit" class="btn btn-primary"><i class="ti ti-search"></i> Filter</button>
                            <a href="{{ route('admin.medical-coding-mentor-converted-leads.index') }}" class="btn btn-secondary"><i class="ti ti-x"></i> Clear</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card" data-mentor-update-url="{{ route('admin.medical-coding-mentor-converted-leads.update-mentor-details', ['id' => '__ID__']) }}">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Certificate Course in Medical Coding Mentor List</h5>
                @include('admin.converted-leads.partials.export-buttons', ['exportPage' => 'mentor-medical-coding'])
            </div>
            <div class="card-body">
                <div class="d-none d-lg-block">
                    <div class="table-responsive">
                        <table class="table table-hover data_table_basic" id="mcMentorTable">
                            @include('admin.converted-leads.partials.medical-coding-mentor-table', [
                                'canEdit' => $canEdit,
                                'convertedLeads' => $convertedLeads,
                                'course' => $course,
                            ])
                        </table>
                    </div>
                    <div class="mt-3">
                        {{ $convertedLeads->withQueryString()->links() }}
                    </div>
                </div>

                <div class="d-lg-none">
                    @forelse($convertedLeads as $index => $lead)
                    <div class="card mb-3 {{ $lead->is_cancelled ? 'cancelled-card' : '' }}">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h6 class="mb-0">{{ $lead->name }}</h6>
                                @if($lead->is_cancelled)<span class="badge bg-danger">Cancelled</span>@endif
                            </div>
                            <div class="row g-2 mb-2 small">
                                <div class="col-6"><span class="text-muted">Reg. No</span><br>{{ $lead->register_number ?? '-' }}</div>
                                <div class="col-6"><span class="text-muted">Batch</span><br>{{ $lead->batch?->title ?: '-' }}</div>
                                <div class="col-6"><span class="text-muted">Phone</span><br>{{ \App\Helpers\PhoneNumberHelper::display($lead->code, $lead->phone) }}</div>
                                <div class="col-6"><span class="text-muted">Conversion</span><br>{{ $lead->created_at ? $lead->created_at->format('d-m-Y') : '-' }}</div>
                            </div>
                            <div class="d-flex gap-2">
                                <a href="{{ route('admin.converted-leads.show', $lead->id) }}" class="btn btn-sm btn-outline-primary"><i class="ti ti-eye"></i> View</a>
                                <a href="{{ route('admin.invoices.index', $lead->id) }}" class="btn btn-sm btn-success"><i class="ti ti-receipt"></i> Invoice</a>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-4 text-muted">No records found.</div>
                    @endforelse
                    <div class="mt-3">
                        {{ $convertedLeads->withQueryString()->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    var updateUrlBase = '{{ route("admin.medical-coding-mentor-converted-leads.update-mentor-details", ":id") }}';
    var dateFields = @json(\App\Support\MedicalCodingMentorTrackColumns::dateFields());

    function loadAdmissionBatches(batchId, selectedId) {
        var $sel = $('#admission_batch_id');
        $sel.html('<option value="">Loading...</option>');
        if (!batchId) { $sel.html('<option value="">All</option>'); return; }
        $.get('/api/admission-batches/by-batch/' + batchId).done(function(list) {
            var opts = '<option value="">All</option>';
            (list || []).forEach(function(i) {
                opts += '<option value="' + i.id + '"' + (String(selectedId) === String(i.id) ? ' selected' : '') + '>' + i.title + '</option>';
            });
            $sel.html(opts);
        }).fail(function() { $sel.html('<option value="">All</option>'); });
    }
    loadAdmissionBatches($('#batch_id').val(), $('#admission_batch_id').data('selected'));
    $('#batch_id').on('change', function() { loadAdmissionBatches($(this).val(), ''); });

    function createInput(currentVal) {
        var s = (currentVal === undefined || currentVal === null) ? '' : String(currentVal);
        var v = (s === '-' || s === '') ? '' : s;
        return '<div class="edit-form"><input type="text" class="form-control form-control-sm" value="' + v.replace(/"/g, '&quot;') + '"><div class="btn-group mt-1"><button type="button" class="btn btn-success btn-sm save-edit">Save</button><button type="button" class="btn btn-secondary btn-sm cancel-edit">Cancel</button></div></div>';
    }
    function createDateInput(currentVal) {
        var s = (currentVal === undefined || currentVal === null) ? '' : String(currentVal);
        var v = (s && s !== '-') ? s : '';
        return '<div class="edit-form"><input type="date" class="form-control form-control-sm" value="' + v + '"><div class="btn-group mt-1"><button type="button" class="btn btn-success btn-sm save-edit">Save</button><button type="button" class="btn btn-secondary btn-sm cancel-edit">Cancel</button></div></div>';
    }
    function createSelect(options, currentVal) {
        var opts = '<option value="">--</option>';
        if (typeof options === 'string') options = JSON.parse(options);
        for (var k in options) {
            opts += '<option value="' + k + '"' + (String(currentVal) === String(k) ? ' selected' : '') + '>' + options[k] + '</option>';
        }
        return '<div class="edit-form"><select class="form-select form-select-sm">' + opts + '</select><div class="btn-group mt-1"><button type="button" class="btn btn-success btn-sm save-edit">Save</button><button type="button" class="btn btn-secondary btn-sm cancel-edit">Cancel</button></div></div>';
    }

    $(document).on('click', '#mcMentorTable .edit-btn', function(e) {
        e.preventDefault();
        e.stopPropagation();
        var $c = $(this).closest('.inline-edit');
        if ($c.hasClass('mentor-flag-field')) return;
        var field = $c.data('field');
        var rawCurrent = $c.data('current');
        var current = (rawCurrent === undefined || rawCurrent === null) ? '' : rawCurrent;
        $('#mcMentorTable .inline-edit').not('.mentor-flag-field').removeClass('editing').find('.edit-form').remove();
        var html = '';
        if ($c.data('field-type') === 'select' && $c.data('options')) {
            html = createSelect($c.data('options'), current);
        } else if ($c.data('field-type') === 'date' || dateFields.indexOf(field) !== -1) {
            html = createDateInput(current);
        } else {
            html = createInput(current);
        }
        $c.addClass('editing').append(html);
        $c.find('input, select').first().focus();
    });

    $(document).on('click', '#mcMentorTable .save-edit', function(e) {
        e.preventDefault();
        var $c = $(this).closest('.inline-edit');
        if ($c.hasClass('mentor-flag-field')) return;
        var field = $c.data('field');
        var id = $c.data('id');
        var value = $c.find('input, select').val() || '';
        var $btn = $(this);
        if ($btn.data('busy')) return;
        $btn.data('busy', true).prop('disabled', true).html('<i class="ti ti-loader-2"></i>');
        $.ajax({
            url: updateUrlBase.replace(':id', id),
            method: 'POST',
            data: { field: field, value: value, _token: $('meta[name="csrf-token"]').attr('content') },
            success: function(res) {
                if (res.success) {
                    $c.find('.display-value').text(res.value !== undefined && res.value !== null && res.value !== '' ? res.value : '-');
                    $c.data('current', value);
                    if (typeof toast_success === 'function') toast_success(res.message);
                } else if (typeof toast_error === 'function') {
                    toast_error(res.error || 'Update failed');
                }
            },
            error: function(xhr) {
                var msg = (xhr.responseJSON && xhr.responseJSON.error) || 'Update failed';
                if (typeof toast_error === 'function') toast_error(msg);
            },
            complete: function() {
                $btn.data('busy', false).prop('disabled', false).html('Save');
                $c.removeClass('editing').find('.edit-form').remove();
            }
        });
    });

    $(document).on('click', '#mcMentorTable .cancel-edit', function(e) {
        e.preventDefault();
        $(this).closest('.inline-edit').removeClass('editing').find('.edit-form').remove();
    });
});
</script>
@include('admin.converted-leads.partials.mentor-flag-inline-scripts')
@endpush
