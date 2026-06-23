@extends('layouts.mantis')

@php
    $pageCourseName = $pageCourseName ?? 'Flutter';
    $pageCourseId = $pageCourseId ?? 21;
    $pageRouteName = $pageRouteName ?? 'admin.flutter-converted-leads.index';
@endphp

@section('title', $pageCourseName . ' Converted Leads')

@section('content')
@include('admin.converted-leads.partials.programme-course-server-datatable')
<style>
    .table td {
        white-space: nowrap;
        vertical-align: middle;
    }

    .table td .btn-group {
        white-space: nowrap;
    }

    .table td .inline-edit {
        white-space: nowrap;
    }

    .table td .display-value {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 150px;
        display: inline-block;
    }

    .cancelled-row>td {
        background-color: #fff1f0 !important;
    }

    .cancelled-card {
        border: 1px solid #f5c2c7;
        background-color: #fff5f5;
    }
</style>
<!-- [ breadcrumb ] start -->
<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-6">
                <div class="page-header-title">
                    <h5 class="m-b-10">{{ $pageCourseName }} Converted Leads Management</h5>
                </div>
            </div>
            <div class="col-md-6">
                <ul class="breadcrumb d-flex justify-content-end">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.converted-leads.index') }}">Converted Leads</a></li>
                    <li class="breadcrumb-item">{{ $pageCourseName }}</li>
                </ul>
            </div>
        </div>
    </div>
</div>
<!-- [ breadcrumb ] end -->

@include('admin.converted-leads.partials.converted-leads-course-nav')

@include('admin.converted-leads.partials.mentor-list-nav', ['activeMentorRoute' => $activeMentorRoute ?? null])

@include('admin.converted-leads.partials.faculty-list-nav', ['activeFacultyRoute' => $activeFacultyRoute ?? null])

@include('admin.converted-leads.partials.converted-leads-support-nav')

<!-- [ Filter Section ] start -->
<div class="row mb-3">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form method="GET" action="{{ route($pageRouteName) }}" id="filterForm" class="js-programme-scoped-dt-form">
                    <div class="row g-3 align-items-end">
                        <div class="col-12 col-sm-6 col-md-2">
                            <label for="search" class="form-label">Search</label>
                            <input type="text" class="form-control" id="search" name="search"
                                value="{{ request('search') }}" placeholder="Name, Phone, Email, Register Number">
                        </div>
                        <div class="col-12 col-sm-6 col-md-2">
                            <label for="batch_id" class="form-label">Batch</label>
                            <select class="form-select" id="batch_id" name="batch_id">
                                <option value="">All Batches</option>
                                @foreach($batches as $batch)
                                <option value="{{ $batch->id }}" {{ request('batch_id') == $batch->id ? 'selected' : '' }}>
                                    {{ $batch->title }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-sm-6 col-md-2">
                            <label for="admission_batch_id" class="form-label">Admission Batch</label>
                            <select class="form-select" id="admission_batch_id" name="admission_batch_id" data-selected="{{ request('admission_batch_id') }}">
                                <option value="">All Admission Batches</option>
                            </select>
                        </div>
                        @include('admin.converted-leads.partials.course-flag-filter-field')

                        <div class="col-12 col-sm-6 col-md-2">
                            <label for="date_from" class="form-label">From Date</label>
                            <input type="date" class="form-control" id="date_from" name="date_from"
                                value="{{ request('date_from') }}">
                        </div>
                        <div class="col-12 col-sm-6 col-md-2">
                            <label for="date_to" class="form-label">To Date</label>
                            <input type="date" class="form-control" id="date_to" name="date_to"
                                value="{{ request('date_to') }}">
                        </div>
                        <div class="col-12 col-sm-6 col-md-2">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" id="status" name="status">
                                <option value="">All</option>
                                <option value="Paid" {{ request('status')==='Paid' ? 'selected' : '' }}>Paid</option>
                                <option value="Admission cancel" {{ request('status')==='Admission cancel' ? 'selected' : '' }}>Admission cancel</option>
                                <option value="Active" {{ request('status')==='Active' ? 'selected' : '' }}>Active</option>
                                <option value="Inactive" {{ request('status')==='Inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>
                        <div class="col-12 col-sm-6 col-md-2">
                            <label for="call_status" class="form-label">Call Status</label>
                            <select class="form-select" id="call_status" name="call_status">
                                <option value="">All</option>
                                <option value="Call Not Answered" {{ request('call_status')==='Call Not Answered' ? 'selected' : '' }}>Call Not Answered</option>
                                <option value="Switched Off" {{ request('call_status')==='Switched Off' ? 'selected' : '' }}>Switched Off</option>
                                <option value="Line Busy" {{ request('call_status')==='Line Busy' ? 'selected' : '' }}>Line Busy</option>
                                <option value="Student Asks to Call Later" {{ request('call_status')==='Student Asks to Call Later' ? 'selected' : '' }}>Student Asks to Call Later</option>
                                <option value="Lack of Interest in Conversation" {{ request('call_status')==='Lack of Interest in Conversation' ? 'selected' : '' }}>Lack of Interest in Conversation</option>
                                <option value="Wrong Contact" {{ request('call_status')==='Wrong Contact' ? 'selected' : '' }}>Wrong Contact</option>
                                <option value="Inconsistent Responses" {{ request('call_status')==='Inconsistent Responses' ? 'selected' : '' }}>Inconsistent Responses</option>
                                <option value="Task Complete" {{ request('call_status')==='Task Complete' ? 'selected' : '' }}>Task Complete</option>
                                <option value="Admission cancel" {{ request('call_status')==='Admission cancel' ? 'selected' : '' }}>Admission cancel</option>
                            </select>
                        </div>
                        <div class="col-12 col-sm-6 col-md-2">
                            <label for="class_information" class="form-label">Class Information</label>
                            <select class="form-select" id="class_information" name="class_information">
                                <option value="">All</option>
                                <option value="phone call" {{ request('class_information')==='phone call' ? 'selected' : '' }}>Phone Call</option>
                                <option value="whatsapp" {{ request('class_information')==='whatsapp' ? 'selected' : '' }}>WhatsApp</option>
                            </select>
                        </div>
                        <div class="col-12 col-sm-6 col-md-2">
                            <label for="orientation_class_status" class="form-label">Orientation Class Status</label>
                            <select class="form-select" id="orientation_class_status" name="orientation_class_status">
                                <option value="">All</option>
                                <option value="Participated" {{ request('orientation_class_status')==='Participated' ? 'selected' : '' }}>Participated</option>
                                <option value="Did not participated" {{ request('orientation_class_status')==='Did not participated' ? 'selected' : '' }}>Did not participated</option>
                            </select>
                        </div>
                        <div class="col-12 col-sm-6 col-md-2">
                            <label for="whatsapp_group_status" class="form-label">WhatsApp Group Status</label>
                            <select class="form-select" id="whatsapp_group_status" name="whatsapp_group_status">
                                <option value="">All</option>
                                <option value="sent link" {{ request('whatsapp_group_status')==='sent link' ? 'selected' : '' }}>Sent Link</option>
                                <option value="task complete" {{ request('whatsapp_group_status')==='task complete' ? 'selected' : '' }}>Task Complete</option>
                            </select>
                        </div>
                        <div class="col-12 col-sm-6 col-md-2">
                            <label for="class_status" class="form-label">Class Status</label>
                            <select class="form-select" id="class_status" name="class_status">
                                <option value="">All</option>
                                <option value="Running" {{ request('class_status')==='Running' ? 'selected' : '' }}>Running</option>
                                <option value="Cancel" {{ request('class_status')==='Cancel' ? 'selected' : '' }}>Cancel</option>
                                <option value="complete" {{ request('class_status')==='complete' ? 'selected' : '' }}>Complete</option>
                            </select>
                        </div>
                        <div class="col-12 col-sm-6 col-md-2">
                            <label for="programme_type" class="form-label">Course Type</label>
                            <select class="form-select" id="programme_type" name="programme_type">
                                <option value="">All</option>
                                <option value="online" {{ request('programme_type')==='online' ? 'selected' : '' }}>Online</option>
                                <option value="offline" {{ request('programme_type')==='offline' ? 'selected' : '' }}>Offline</option>
                            </select>
                        </div>
                        <div class="col-12 col-sm-6 col-md-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="ti ti-search"></i> Filter
                            </button>
                            <a href="{{ route($pageRouteName) }}" class="btn btn-secondary">
                                <i class="ti ti-refresh"></i> Clear
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- [ Filter Section ] end -->

<!-- [ Main Content ] start -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5>{{ $pageCourseName }} Converted Leads</h5>
            </div>
            <div class="card-body">
                <p class="d-lg-none text-muted small mb-2">Tip: scroll sideways to see all columns.</p>
                <div class="table-responsive">
                        <table class="table table-hover" id="{{ $courseDataTableId }}">
                            <thead>
                                <tr>
                                    <th>SL No</th>
                                    <th>Academic</th>
                                    <th>Support</th>
                                    <th>Converted Date</th>
                                    <th>Register Number</th>
                                    <th>Course Flag</th>
                                    <th>Name</th>
                                    <th>Type</th>
                                    <th>Phone</th>
                                    <th>WhatsApp</th>
                                    @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor())
                                    <th>Parent Phone</th>
                                    @endif
                                    <th>Course Type</th>
                                    <th>Location</th>
                                    <th>Class Time</th>
                                    <th>Batch</th>
                                    <th>Admission Batch</th>
                                    <th>Internship ID</th>
                                    <th>Email</th>
                                    <th>Call Status</th>
                                    <th>Class Information</th>
                                    <th>Orientation Class Status</th>
                                    <th>Class Starting Date</th>
                                    <th>Class Ending Date</th>
                                    <th>WhatsApp Group Status</th>
                                    <th>Class Status</th>
                                    <th>Complete/Cancel Date</th>
                                    <th>Remarks</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>

            </div>
        </div>
    </div>
</div>
<!-- [ Main Content ] end -->

@include('admin.converted-leads.partials.course-flag-inline-scripts')
@endsection

@push('styles')
<style>
    .spin {
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        0% {
            transform: rotate(0deg);
        }

        100% {
            transform: rotate(360deg);
        }
    }

    .inline-edit {
        position: relative;
        overflow: visible;
    }

    .inline-edit .edit-form {
        display: none;
        position: absolute;
        top: 0;
        left: -8px;
        z-index: 10;
        background: white;
        border: 1px solid #ddd;
        border-radius: 4px;
        padding: 10px;
        min-width: 320px;
        max-width: 440px;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
    }

    .inline-edit.editing .edit-form {
        display: block;
    }

    .inline-edit.editing .display-value {
        display: none;
    }

    .inline-edit .edit-form input,
    .inline-edit .edit-form select {
        width: 100%;
        padding: 4px 8px;
        border: 1px solid #ccc;
        border-radius: 3px;
        font-size: 12px;
    }

    .inline-edit .edit-form input:focus,
    .inline-edit .edit-form select:focus {
        border-color: #7366ff;
        outline: none;
        box-shadow: 0 0 0 2px rgba(115, 102, 255, 0.15);
    }

    .inline-edit .edit-form .btn-group {
        margin-top: 5px;
    }

    .inline-edit .edit-form .btn {
        padding: 2px 8px;
        font-size: 11px;
    }

    #aiPythonTable thead th,
    #aiPythonTable tbody td {
        white-space: nowrap;
    }

    #aiPythonTable thead th {
        position: sticky;
        top: 0;
        z-index: 5;
        background: #fff;
        box-shadow: inset 0 -1px 0 #e9ecef;
    }

    #aiPythonTable tbody tr:hover {
        background: #fafbff;
    }

    #aiPythonTable td .display-value {
        display: inline-block;
        max-width: 220px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        vertical-align: middle;
    }

    #aiPythonTable .btn-group .btn {
        margin-right: 4px;
    }

    #aiPythonTable .btn-group .btn:last-child {
        margin-right: 0;
    }
</style>
@endpush

@push('scripts')
<script id="country-codes-json" type="application/json">{!! json_encode($country_codes ?? [], JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) !!}</script>
<script>
    $(document).ready(function() {
        // Inline editing functionality
        $(document).on('click', '.edit-btn', function(e) {
            e.preventDefault();
            e.stopPropagation();

            const container = $(this).closest('.inline-edit');
            const field = container.data('field');
            const id = container.data('id');
            const currentValue = container.data('current') !== undefined ? String(container.data('current')).trim() : container.find('.display-value').text().trim();

            if (container.hasClass('editing')) {
                return;
            }

            $('.inline-edit.editing').not(container).each(function() {
                $(this).removeClass('editing');
                $(this).find('.edit-form').remove();
            });

            let editForm = '';

            if (field === 'phone') {
                const currentCode = container.siblings('.inline-code-value').data('current') || '';
                editForm = createPhoneField(currentCode, currentValue);
            } else if (['call_status', 'class_information', 'orientation_class_status', 'whatsapp_group_status', 'class_status'].includes(field)) {
                editForm = createSelectField(field, currentValue);
            } else if (['class_starting_date', 'class_ending_date', 'complete_cancel_date'].includes(field)) {
                editForm = createDateField(field, currentValue);
            } else if (field === 'class_time') {
                editForm = createTimeField(field, currentValue);
            } else if (field === 'batch_id') {
                const courseId = container.data('course-id');
                const currentId = container.data('current-id');
                editForm = createBatchSelect(courseId, currentId);
            } else if (field === 'admission_batch_id') {
                const batchId = container.data('batch-id');
                const currentId = container.data('current-id');
                editForm = createAdmissionBatchField(batchId, currentId);
            } else if (field === 'class_time_id') {
                const courseId = container.data('course-id');
                const programmeType = container.data('programme-type');
                const currentId = container.data('current-id');
                editForm = createClassTimeSelect(courseId, programmeType, currentId);
            } else if (container.data('field-type') === 'select') {
                // Handle fields with data-field-type="select" using data-options
                const options = container.data('options');
                editForm = createSelectFieldFromOptions(field, currentValue, options);
            } else {
                editForm = createInputField(field, currentValue);
            }

            container.addClass('editing');
            container.append(editForm);

            // Load options for select fields that need dynamic loading
            if (field === 'batch_id') {
                const courseId = container.data('course-id');
                const currentId = container.data('current-id');
                const $select = container.find('select');
                loadBatchesForEdit($select, courseId, currentId);
            } else if (field === 'admission_batch_id') {
                const batchId = container.data('batch-id');
                const currentId = container.data('current-id');
                const $select = container.find('select');
                loadAdmissionBatchesForEdit($select, batchId, currentId);
            } else if (field === 'class_time_id') {
                const courseId = container.data('course-id');
                const programmeType = container.data('programme-type');
                const currentId = container.data('current-id');
                const $select = container.find('select');
                loadClassTimesForEdit($select, courseId, programmeType, currentId);
            } else {
                container.find('input, select').first().focus();
            }
        });

        // Save inline edit
        $(document).off('click.saveInline').on('click.saveInline', '.save-edit', function(e) {
            e.preventDefault();
            e.stopPropagation();

            const container = $(this).closest('.inline-edit');
            const field = container.data('field');
            const id = container.data('id');
            let value;
            if (field === 'phone') {
                value = container.find('input[type="text"]').val();
            } else {
                value = container.find('input, select').val();
            }
            let extra = {};
            if (field === 'phone') {
                const codeVal = container.find('select[name="code"]').val();
                extra = {
                    code: codeVal
                };
            }

            const btn = $(this);
            if (btn.data('busy')) return;
            btn.data('busy', true);
            btn.prop('disabled', true).html('<i class="ti ti-loader-2 spin"></i>');

            $.ajax({
                url: `/admin/converted-leads/${id}/inline-update`,
                method: 'POST',
                data: $.extend({
                    field: field,
                    value: value,
                    _token: $('meta[name="csrf-token"]').attr('content')
                }, extra),
                success: function(response) {
                    if (response.success) {
                        // Update display value with the response value (which should be the title, not ID)
                        let displayValue = response.value || 'N/A';
                        container.find('.display-value').text(displayValue);
                        // Update the data-current attribute with the new display value
                        container.data('current', displayValue);
                        // Update data-current-id for fields that use it (store the ID, not the display value)
                        if (field === 'batch_id' || field === 'admission_batch_id' || field === 'class_time_id') {
                            container.data('current-id', value || '');
                        }
                        // Handle class_time_id display update
                        if (field === 'class_time_id' && response.value) {
                            // The response.value should contain the formatted time string
                            displayValue = response.value;
                        }
                        // If batch_id changed, update admission_batch_id field's data-batch-id
                        if (field === 'batch_id') {
                            const row = container.closest('tr');
                            const admissionBatchContainer = row.find('[data-field="admission_batch_id"]');
                            if (admissionBatchContainer.length) {
                                admissionBatchContainer.data('batch-id', value || '');
                                // Clear admission batch if batch changed
                                admissionBatchContainer.data('current-id', '');
                                admissionBatchContainer.find('.display-value').text('N/A');
                            }
                        }
                        // If programme_type changed, reload page to update location and class_time_id visibility
                        if (field === 'programme_type') {
                            setTimeout(() => {
                                (typeof window.reloadProgrammeCourseDataTable === 'function' ? window.reloadProgrammeCourseDataTable() : location.reload());
                            }, 500);
                        }
                        if (field === 'phone') {
                            const codeVal = extra.code || '';
                            container.siblings('.inline-code-value').data('current', codeVal);
                        }
                        toast_success(response.message);
                    } else {
                        toast_error(response.error || 'Update failed');
                    }
                },
                error: function(xhr) {
                    let errorMessage = 'Update failed';
                    if (xhr.responseJSON) {
                        if (xhr.responseJSON.error) {
                            errorMessage = xhr.responseJSON.error;
                        } else if (xhr.responseJSON.errors) {
                            // Handle validation errors
                            const errors = xhr.responseJSON.errors;
                            const fieldErrors = Object.values(errors).flat();
                            errorMessage = fieldErrors.join(', ');
                        }
                    }
                    toast_error(errorMessage);
                },
                complete: function() {
                    btn.data('busy', false);
                    container.removeClass('editing');
                    container.find('.edit-form').remove();
                }
            });
        });

        // Cancel edit
        $(document).on('click', '.cancel-edit', function(e) {
            e.preventDefault();
            e.stopPropagation();

            const container = $(this).closest('.inline-edit');
            container.removeClass('editing');
            container.find('.edit-form').remove();
            container.find('.display-value').show();
            container.find('.edit-btn').show();
        });

        // Helper functions for creating form elements
        function createInputField(field, currentValue) {
            const displayValue = currentValue === '-' ? '' : currentValue;
            return `
                <div class="edit-form">
                    <input type="text" value="${displayValue}" class="form-control form-control-sm" autocomplete="off" autocapitalize="off" spellcheck="false">
                    <div class="btn-group mt-1">
                        <button type="button" class="btn btn-success btn-sm save-edit">Save</button>
                        <button type="button" class="btn btn-secondary btn-sm cancel-edit">Cancel</button>
                    </div>
                </div>
            `;
        }

        function createDateField(field, currentValue) {
            const value = (currentValue && currentValue !== '-') ? currentValue : '';
            // For class starting and ending dates, allow future dates
            const maxDate = (field === 'class_starting_date' || field === 'class_ending_date') ? '' : new Date().toISOString().split('T')[0];
            const maxAttr = maxDate ? `max="${maxDate}"` : '';

            return `
                <div class="edit-form">
                    <input type="date" ${maxAttr} value="${value}" class="form-control form-control-sm">
                    <div class="btn-group mt-1">
                        <button class="btn btn-success btn-sm save-edit">Save</button>
                        <button class="btn btn-secondary btn-sm cancel-edit">Cancel</button>
                    </div>
                </div>
            `;
        }

        function createTimeField(field, currentValue) {
            const value = (currentValue && currentValue !== '-') ? currentValue : '';
            return `
                <div class="edit-form">
                    <input type="time" value="${value}" class="form-control form-control-sm">
                    <div class="btn-group mt-1">
                        <button class="btn btn-success btn-sm save-edit">Save</button>
                        <button class="btn btn-secondary btn-sm cancel-edit">Cancel</button>
                    </div>
                </div>
            `;
        }

        function createBatchSelect(courseId, currentId) {
            return `
                <div class="edit-form">
                    <select class="form-select form-select-sm">
                        <option value="">Loading...</option>
                    </select>
                    <div class="btn-group mt-1">
                        <button type="button" class="btn btn-success btn-sm save-edit">Save</button>
                        <button type="button" class="btn btn-secondary btn-sm cancel-edit">Cancel</button>
                    </div>
                </div>
            `;
        }

        function createAdmissionBatchField(batchId, currentId) {
            return `
                <div class="edit-form">
                    <select class="form-select form-select-sm" data-batch-id="${batchId}">
                        <option value="">Loading...</option>
                    </select>
                    <div class="btn-group mt-1">
                        <button type="button" class="btn btn-success btn-sm save-edit">Save</button>
                        <button type="button" class="btn btn-secondary btn-sm cancel-edit">Cancel</button>
                    </div>
                </div>
            `;
        }

        function loadBatchesForEdit($select, courseId, currentId) {
            if (!courseId) {
                $select.html('<option value="">No course selected</option>');
                return;
            }

            $.get(`/api/batches/by-course/${courseId}`)
                .done(function(response) {
                    let options = '<option value="">Select Batch</option>';
                    if (response.success && response.batches) {
                        response.batches.forEach(function(batch) {
                            const isSelected = (currentId && String(currentId) === String(batch.id)) ? 'selected' : '';
                            options += `<option value="${batch.id}" ${isSelected}>${batch.title}</option>`;
                        });
                    }
                    $select.html(options);
                    $select.focus();
                })
                .fail(function() {
                    $select.html('<option value="">Error loading batches</option>');
                });
        }

        function loadAdmissionBatchesForEdit($select, batchId, currentId) {
            if (!batchId) {
                $select.html('<option value="">No batch selected</option>');
                return;
            }

            $.get(`/api/admission-batches/by-batch/${batchId}`)
                .done(function(list) {
                    let options = '<option value="">Select Admission Batch</option>';
                    list.forEach(function(item) {
                        const isSelected = (currentId && String(currentId) === String(item.id)) ? 'selected' : '';
                        options += `<option value="${item.id}" ${isSelected}>${item.title}</option>`;
                    });
                    $select.html(options);
                    $select.focus();
                })
                .fail(function() {
                    $select.html('<option value="">Error loading admission batches</option>');
                });
        }

        function createClassTimeSelect(courseId, programmeType, currentId) {
            return `
                <div class="edit-form">
                    <select class="form-select form-select-sm" data-course-id="${courseId}" data-programme-type="${programmeType}">
                        <option value="">Loading...</option>
                    </select>
                    <div class="btn-group mt-1">
                        <button type="button" class="btn btn-success btn-sm save-edit">Save</button>
                        <button type="button" class="btn btn-secondary btn-sm cancel-edit">Cancel</button>
                    </div>
                </div>
            `;
        }

        function loadClassTimesForEdit($select, courseId, programmeType, currentId) {
            if (!courseId || !programmeType) {
                $select.html('<option value="">No course or programme type selected</option>');
                return;
            }

            $.get(`/api/class-times/by-course/${courseId}?class_type=${programmeType}`)
                .done(function(list) {
                    let options = '<option value="">Select Class Time</option>';
                    if (list && list.length > 0) {
                        list.forEach(function(classTime) {
                            const fromTime = new Date('2000-01-01 ' + classTime.from_time).toLocaleTimeString('en-US', {
                                hour: '2-digit',
                                minute: '2-digit',
                                hour12: true
                            });
                            const toTime = new Date('2000-01-01 ' + classTime.to_time).toLocaleTimeString('en-US', {
                                hour: '2-digit',
                                minute: '2-digit',
                                hour12: true
                            });
                            const isSelected = (currentId && String(currentId) === String(classTime.id)) ? 'selected' : '';
                            options += `<option value="${classTime.id}" ${isSelected}>${fromTime} - ${toTime}</option>`;
                        });
                    }
                    $select.html(options);
                    $select.focus();
                })
                .fail(function() {
                    $select.html('<option value="">Error loading class times</option>');
                });
        }

        function createPhoneField(currentCode, currentPhone) {
            const codeOptionsEl = document.getElementById('country-codes-json');
            let codeOptions = {};
            try {
                codeOptions = codeOptionsEl ? JSON.parse(codeOptionsEl.textContent || '{}') : {};
            } catch (e) {
                codeOptions = {};
            }
            const buildOptions = (selected) => {
                let opts = '<option value="">Select Country</option>';
                for (const c in codeOptions) {
                    const isSel = String(selected) === String(c) ? 'selected' : '';
                    opts += `<option value="${c}" ${isSel}>${c} - ${codeOptions[c]}</option>`;
                }
                return opts;
            };
            const safePhone = (currentPhone && currentPhone !== 'N/A') ? currentPhone : '';
            return `
                <div class="edit-form">
                    <div class="row g-1">
                        <div class="col-5">
                            <select class="form-select form-select-sm" name="code">
                                ${buildOptions(currentCode)}
                            </select>
                        </div>
                        <div class="col-7">
                            <input type="text" value="${safePhone}" class="form-control form-control-sm" placeholder="Phone number">
                        </div>
                    </div>
                    <div class="btn-group mt-1">
                        <button type="button" class="btn btn-success btn-sm save-edit">Save</button>
                        <button type="button" class="btn btn-secondary btn-sm cancel-edit">Cancel</button>
                    </div>
                </div>
            `;
        }

        function createSelectField(field, currentValue) {
            let options = '';

            if (field === 'call_status') {
                options = `
                    <option value="">Select Call Status</option>
                    <option value="Call Not Answered" ${currentValue === 'Call Not Answered' ? 'selected' : ''}>Call Not Answered</option>
                    <option value="Switched Off" ${currentValue === 'Switched Off' ? 'selected' : ''}>Switched Off</option>
                    <option value="Line Busy" ${currentValue === 'Line Busy' ? 'selected' : ''}>Line Busy</option>
                    <option value="Student Asks to Call Later" ${currentValue === 'Student Asks to Call Later' ? 'selected' : ''}>Student Asks to Call Later</option>
                    <option value="Lack of Interest in Conversation" ${currentValue === 'Lack of Interest in Conversation' ? 'selected' : ''}>Lack of Interest in Conversation</option>
                    <option value="Wrong Contact" ${currentValue === 'Wrong Contact' ? 'selected' : ''}>Wrong Contact</option>
                    <option value="Inconsistent Responses" ${currentValue === 'Inconsistent Responses' ? 'selected' : ''}>Inconsistent Responses</option>
                    <option value="Task Complete" ${currentValue === 'Task Complete' ? 'selected' : ''}>Task Complete</option>
                    <option value="Admission cancel" ${currentValue === 'Admission cancel' ? 'selected' : ''}>Admission cancel</option>
                `;
            } else if (field === 'class_information') {
                options = `
                    <option value="">Select Class Information</option>
                    <option value="phone call" ${currentValue === 'phone call' ? 'selected' : ''}>Phone Call</option>
                    <option value="whatsapp" ${currentValue === 'whatsapp' ? 'selected' : ''}>WhatsApp</option>
                `;
            } else if (field === 'orientation_class_status') {
                options = `
                    <option value="">Select Orientation Class Status</option>
                    <option value="Participated" ${currentValue === 'Participated' ? 'selected' : ''}>Participated</option>
                    <option value="Did not participated" ${currentValue === 'Did not participated' ? 'selected' : ''}>Did not participated</option>
                `;
            } else if (field === 'whatsapp_group_status') {
                options = `
                    <option value="">Select WhatsApp Group Status</option>
                    <option value="sent link" ${currentValue === 'sent link' ? 'selected' : ''}>Sent Link</option>
                    <option value="task complete" ${currentValue === 'task complete' ? 'selected' : ''}>Task Complete</option>
                `;
            } else if (field === 'class_status') {
                options = `
                    <option value="">Select Class Status</option>
                    <option value="Running" ${currentValue === 'Running' ? 'selected' : ''}>Running</option>
                    <option value="Cancel" ${currentValue === 'Cancel' ? 'selected' : ''}>Cancel</option>
                    <option value="complete" ${currentValue === 'complete' ? 'selected' : ''}>Complete</option>
                `;
            }

            return `
                <div class="edit-form">
                    <select class="form-select form-select-sm">
                        ${options}
                    </select>
                    <div class="btn-group mt-1">
                        <button type="button" class="btn btn-success btn-sm save-edit">Save</button>
                        <button type="button" class="btn btn-secondary btn-sm cancel-edit">Cancel</button>
                    </div>
                </div>
            `;
        }

        // Handle ID card generation form submission
        $(document).off('submit', '.id-card-generate-form').on('submit', '.id-card-generate-form', function(e) {
            e.preventDefault();
            e.stopImmediatePropagation();

            const $form = $(this);
            const $button = $form.find('button[type="submit"]');
            const originalText = $button.text();
            const loadingText = $button.data('loading-text') || 'Generating...';

            $button.prop('disabled', true).text(loadingText);

            $.ajax({
                url: $form.attr('action'),
                method: 'POST',
                data: $form.serialize(),
                success: function(response) {
                    if (response.success) {
                        show_alert('success', response.message || 'ID Card generated successfully!');
                        // Reload the page to show updated buttons
                        setTimeout(() => {
                            (typeof window.reloadProgrammeCourseDataTable === 'function' ? window.reloadProgrammeCourseDataTable() : location.reload());
                        }, 1000);
                    } else {
                        show_alert('error', response.message || 'Failed to generate ID Card');
                    }
                },
                error: function(xhr) {
                    let errorMessage = 'Failed to generate ID Card';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    show_alert('error', errorMessage);
                },
                complete: function() {
                    $button.prop('disabled', false).text(originalText);
                }
            });
        });
    });

    // Toggle Academic Verification with confirmation modal
    let academicVerifyUrl = null;
    $(document).off('click', '.toggle-academic-verify-btn').on('click', '.toggle-academic-verify-btn', function(e) {
        e.preventDefault();
        const $btn = $(this);
        const url = $btn.data('url');
        const name = $btn.data('name') || 'this student';
        const isVerified = String($btn.data('verified')) === '1';

        academicVerifyUrl = url;

        const actionText = isVerified ? 'unverify' : 'verify';
        const modalText = `Are you sure you want to ${actionText} academic status for <strong>${name}</strong>?`;
        $('#academicVerifyModalText').html(modalText);
        const $confirmBtn = $('#confirmAcademicVerifyBtn');
        $confirmBtn.removeClass('btn-danger btn-success').addClass(isVerified ? 'btn-danger' : 'btn-success');
        $('#academicVerifyModal').modal('show');
    });

    $('#confirmAcademicVerifyBtn').on('click', function() {
        if (!academicVerifyUrl) return;
        const $confirmBtn = $(this);
        const originalHtml = $confirmBtn.html();
        $confirmBtn.prop('disabled', true).addClass('disabled');
        $.post(academicVerifyUrl, {
                _token: '{{ csrf_token() }}'
            })
            .done(function(res) {
                if (res && res.success) {
                    show_alert('success', res.message || 'Updated');
                    $('#academicVerifyModal').modal('hide');
                    setTimeout(() => {
                        (typeof window.reloadProgrammeCourseDataTable === 'function' ? window.reloadProgrammeCourseDataTable() : location.reload());
                    }, 600);
                } else {
                    show_alert('error', (res && res.message) ? res.message : 'Failed to update');
                }
            })
            .fail(function(xhr) {
                let msg = 'Failed to update';
                if (xhr && xhr.responseJSON && xhr.responseJSON.message) {
                    msg = xhr.responseJSON.message;
                }
                show_alert('error', msg);
            })
            .always(function() {
                $confirmBtn.prop('disabled', false).removeClass('disabled').html(originalHtml);
                academicVerifyUrl = null;
            });
    });

    // Toggle Support Verification with confirmation modal
    let supportVerifyUrl = null;
    $(document).off('click', '.toggle-support-verify-btn').on('click', '.toggle-support-verify-btn', function(e) {
        e.preventDefault();
        const $btn = $(this);
        const url = $btn.data('url');
        const name = $btn.data('name') || 'this student';
        const isVerified = String($btn.data('verified')) === '1';

        supportVerifyUrl = url;

        const actionText = isVerified ? 'unverify' : 'verify';
        const modalText = `Are you sure you want to ${actionText} support status for <strong>${name}</strong>?`;
        $('#supportVerifyModalText').html(modalText);
        const $confirmBtn = $('#confirmSupportVerifyBtn');
        $confirmBtn.removeClass('btn-danger btn-success').addClass(isVerified ? 'btn-danger' : 'btn-success');
        $('#supportVerifyModal').modal('show');
    });

    $('#confirmSupportVerifyBtn').on('click', function() {
        if (!supportVerifyUrl) return;
        const $confirmBtn = $(this);
        const originalHtml = $confirmBtn.html();
        $confirmBtn.prop('disabled', true).addClass('disabled');
        $.post(supportVerifyUrl, {
                _token: '{{ csrf_token() }}'
            })
            .done(function(res) {
                if (res && res.success) {
                    show_alert('success', res.message || 'Updated');
                    $('#supportVerifyModal').modal('hide');
                    setTimeout(() => {
                        (typeof window.reloadProgrammeCourseDataTable === 'function' ? window.reloadProgrammeCourseDataTable() : location.reload());
                    }, 600);
                } else {
                    show_alert('error', (res && res.message) ? res.message : 'Failed to update');
                }
            })
            .fail(function(xhr) {
                let msg = 'Failed to update';
                if (xhr && xhr.responseJSON && xhr.responseJSON.message) {
                    msg = xhr.responseJSON.message;
                }
                show_alert('error', msg);
            })
            .always(function() {
                $confirmBtn.prop('disabled', false).removeClass('disabled').html(originalHtml);
                supportVerifyUrl = null;
            });
    });

    // Handle Change Course modal buttons
    $(document).on('click', '.js-change-course-modal', function(e) {
        e.preventDefault();
        const url = $(this).data('modal-url');
        const title = $(this).data('modal-title') || 'Change Course';
        if (typeof show_ajax_modal === 'function' && url) {
            show_ajax_modal(url, title);
        }
    });

    // Handle cancellation flag modal
    $(document).on('click', '.js-cancel-flag', function(e) {
        e.preventDefault();
        const url = $(this).data('cancel-url');
        const title = $(this).data('modal-title') || 'Cancellation Confirmation';
        if (typeof show_ajax_modal === 'function' && url) {
            show_ajax_modal(url, title);
        }
    });

    // Delegated submit handler for cancellation flag modal
    $(document).on('submit', '#cancelFlagForm', function(e) {
        e.preventDefault();
        const form = $(this);
        const submitUrl = form.data('submit-url');
        if (!submitUrl) {
            return form.off('submit').submit();
        }

        const submitBtn = form.find('button[type="submit"]');
        const originalText = submitBtn.html();
        submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Saving...');

        $.ajax({
            url: submitUrl,
            method: 'POST',
            data: form.serialize(),
            success: function (response) {
                $('#ajax_modal').modal('hide');
                if (typeof showToast === 'function') {
                    showToast(response.message, 'success');
                } else if (typeof toast_success === 'function') {
                    toast_success(response.message);
                } else {
                    alert(response.message);
                }
                (typeof window.reloadProgrammeCourseDataTable === 'function' ? window.reloadProgrammeCourseDataTable() : location.reload());
            },
            error: function (xhr) {
                let message = 'Unable to update cancellation flag.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    message = xhr.responseJSON.message;
                } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                    message = Object.values(xhr.responseJSON.errors).flat().join('\n');
                }
                if (typeof showToast === 'function') {
                    showToast(message, 'error');
                } else if (typeof toast_error === 'function') {
                    toast_error(message);
                } else {
                    alert(message);
                }
            },
            complete: function () {
                submitBtn.prop('disabled', false).html(originalText);
            }
        });
    });
</script>
@endpush

<!-- Support Verify Modal -->
<div class="modal fade" id="supportVerifyModal" tabindex="-1" aria-labelledby="supportVerifyModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="supportVerifyModalLabel">Confirm Action</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p id="supportVerifyModalText" class="mb-0"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="confirmSupportVerifyBtn">
                    <span class="confirm-text">Confirm</span>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Academic Verify Modal -->
<div class="modal fade" id="academicVerifyModal" tabindex="-1" aria-labelledby="academicVerifyModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="academicVerifyModalLabel">Confirm Action</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p id="academicVerifyModalText" class="mb-0"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="confirmAcademicVerifyBtn">
                    <span class="confirm-text">Confirm</span>
                </button>
            </div>
        </div>
    </div>
</div>