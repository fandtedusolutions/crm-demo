@extends('layouts.mantis')

@section('title', 'Board of Open Schooling and Skill Education Converted Mentor List')

@section('content')
@php $appTimezone = config('app.timezone'); @endphp
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
                    <h5 class="m-b-10">Board of Open Schooling and Skill Education Converted Mentor List</h5>
                </div>
            </div>
            <div class="col-md-6">
                <div class="d-flex justify-content-end align-items-center gap-3">
                    <ul class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.converted-leads.index') }}">Converted Leads</a></li>
                        <li class="breadcrumb-item">Board of Open Schooling and Skill Education Converted Mentor List</li>
                    </ul>
                    <a href="{{ route('admin.converted-leads.index') }}" class="btn btn-secondary">
                        <i class="ti ti-arrow-left"></i> Back to Converted Leads
                    </a>
                </div>
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
                <form method="GET" action="{{ route('admin.mentor-bosse-converted-leads.index') }}" id="filterForm">
                    <div class="row g-3 align-items-end">
                        <div class="col-12 col-sm-6 col-md-2">
                            <label for="search" class="form-label">Search</label>
                            <input type="text" class="form-control" id="search" name="search"
                                value="{{ request('search') }}" placeholder="Name, Phone, Email, Application Number">
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
                        <div class="col-12 col-sm-6 col-md-2">
                            <label for="subject_id" class="form-label">Subject</label>
                            <select class="form-select" id="subject_id" name="subject_id">
                                <option value="">All Subjects</option>
                                @foreach($subjects as $subject)
                                <option value="{{ $subject->id }}" {{ request('subject_id') == $subject->id ? 'selected' : '' }}>
                                    {{ $subject->title }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-sm-6 col-md-2">
                            <label for="registration_status" class="form-label">Registration Status</label>
                            <select class="form-select" id="registration_status" name="registration_status">
                                <option value="">All</option>
                                <option value="Paid" {{ request('registration_status')==='Paid' ? 'selected' : '' }}>Paid</option>
                                <option value="Not Paid" {{ request('registration_status')==='Not Paid' ? 'selected' : '' }}>Not Paid</option>
                            </select>
                        </div>
                        <div class="col-12 col-sm-6 col-md-2">
                            <label for="student_status" class="form-label">Student Status</label>
                            <select class="form-select" id="student_status" name="student_status">
                                <option value="">All</option>
                                <option value="Low Level" {{ request('student_status')==='Low Level' ? 'selected' : '' }}>Low Level</option>
                                <option value="Below Medium" {{ request('student_status')==='Below Medium' ? 'selected' : '' }}>Below Medium</option>
                                <option value="Medium Level" {{ request('student_status')==='Medium Level' ? 'selected' : '' }}>Medium Level</option>
                                <option value="Advanced Level" {{ request('student_status')==='Advanced Level' ? 'selected' : '' }}>Advanced Level</option>
                            </select>
                        </div>
                        @include('admin.converted-leads.partials.mentor-flag-filter-field')
                        
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
                            <button type="submit" class="btn btn-primary">
                                <i class="ti ti-search"></i> Filter
                            </button>
                            <a href="{{ route('admin.mentor-bosse-converted-leads.index') }}" class="btn btn-secondary">
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
        <div class="card" data-mentor-update-url="{{ route('admin.mentor-bosse-converted-leads.update-mentor-details', ['id' => '__ID__']) }}">
            <div class="card-header">
                <h5>Board of Open Schooling and Skill Education Converted Mentor List</h5>
            </div>
            <div class="card-body">
                <!-- Desktop Table View -->
                <div class="d-none d-lg-block">
                    <div class="table-responsive">
                        <table class="table table-hover data_table_basic" id="mentorBosseTable">
                            <thead>
                                <tr>
                                    <th>SL No</th>
                                    <th>Converted Date</th>
                                    <th>Academic Verified At</th>
                                    <th>Support Verified At</th>
                                    <th>Registration Number</th>
                                    <th>Flag</th>
                                    <th>Call Time</th>
                                    <th>Name</th>
                                    <th>Type</th>
                                    <th>DOB</th>
                                    <th>Registration Fee</th>
                                    <th>Enrollment Number</th>
                                    <th>Application Number</th>
                                    <th>Status</th>
                                    <th>Phone</th>
                                    <th>WhatsApp</th>
                                    @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor())
                                    <th>Parent Phone</th>
                                    @endif
                                    <th>Subject</th>
                                    <th>Batch</th>
                                    <th>Admission Batch</th>
                                    <th>Registration Status</th>
                                    <th>Technology Side</th>
                                    <th>Student Status</th>
                                    <th>CALL - 1</th>
                                    <th>APP</th>
                                    <th>WhatsApp Group</th>
                                    <th>Telegram Group</th>
                                    <th>Problems</th>
                                    <th>CALL - 2</th>
                                    <th>Mentor Live 1</th>
                                    <th>FIRST LIVE</th>
                                    <th>FIRST EXAM Registration</th>
                                    <th>FIRST EXAM</th>
                                    <th>CALL - 3</th>
                                    <th>Mentor Live 2</th>
                                    <th>SECOND LIVE</th>
                                    <th>Second Exam</th>
                                    <th>CALL - 4</th>
                                    <th>Mentor Live 3</th>
                                    <th>Model Exam Live</th>
                                    <th>Model Exam</th>
                                    <th>Practical</th>
                                    <th>CALL - 5</th>
                                    <th>Mentor Live 4</th>
                                    <th>Self Registration</th>
                                    <th>CALL - 6</th>
                                    <th>Assignment</th>
                                    <th>CALL - 7</th>
                                    <th>Mock Test</th>
                                    <th>CALL - 8</th>
                                    <th>Admit Card</th>
                                    <th>CALL - 9</th>
                                    <th>Mentor Live 5</th>
                                    <th>Exam Subject - 1</th>
                                    <th>Exam Subject - 2</th>
                                    <th>Exam Subject - 3</th>
                                    <th>Exam Subject - 4</th>
                                    <th>Exam Subject - 5</th>
                                    <th>Exam Subject - 6</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($convertedLeads as $index => $convertedLead)
                                    @include('admin.converted-leads.partials.mentor-bosse-dt-desktop-row')
                                @empty
                                <tr>
                                    <td colspan="56" class="text-center">No converted leads found for Board of Open Schooling and Skill Education mentoring</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>


                <!-- Mobile Card View -->
                <div class="d-lg-none" id="mentorBosseMobileCardsWrap">
                    @forelse($convertedLeads as $convertedLead)
                    @include('admin.converted-leads.partials.mentor-bosse-mobile-card')
                    @empty
                    <div class="text-center py-3 text-muted">No converted leads found for Board of Open Schooling and Skill Education mentoring</div>
                    @endforelse
                </div>

                @if(method_exists($convertedLeads, 'links'))
                <div class="mt-3">
                    {{ $convertedLeads->withQueryString()->links('pagination::bootstrap-5') }}
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
<!-- [ Main Content ] end -->

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

    #mentorBosseTable thead th,
    #mentorBosseTable tbody td {
        white-space: nowrap;
    }

    #mentorBosseTable thead th {
        position: sticky;
        top: 0;
        z-index: 5;
        background: #fff;
        box-shadow: inset 0 -1px 0 #e9ecef;
    }

    #mentorBosseTable tbody tr:hover {
        background: #fafbff;
    }

    #mentorBosseTable td .display-value {
        display: inline-block;
        max-width: 220px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        vertical-align: middle;
    }

    #mentorBosseTable .btn-group .btn {
        margin-right: 4px;
    }

    #mentorBosseTable .btn-group .btn:last-child {
        margin-right: 0;
    }
</style>
@endpush

@push('scripts')
@include('admin.converted-leads.partials.placement-modal-reopen-script')
<script>
    $(document).ready(function() {
        // DataTable is automatically initialized by layout for tables with 'data_table_basic' class

        // Batch filter enhancement
        $('#batch_id').on('change', function() {
            // Auto-submit form when batch is changed for better UX
            $('#filterForm').submit();
        });

        // Dependent filters: load admission batches by batch
        function loadAdmissionBatchesByBatch(batchId, selectedId) {
            const $admission = $('#admission_batch_id');
            $admission.html('<option value="">Loading...</option>');
            if (!batchId) {
                $admission.html('<option value="">All Admission Batches</option>');
                return;
            }
            $.get(`/api/admission-batches/by-batch/${batchId}`).done(function(list) {
                let opts = '<option value="">All Admission Batches</option>';
                list.forEach(function(i) {
                    const sel = String(selectedId) === String(i.id) ? 'selected' : '';
                    opts += `<option value="${i.id}" ${sel}>${i.title}</option>`;
                });
                $admission.html(opts);
            }).fail(function() {
                $admission.html('<option value="">All Admission Batches</option>');
            });
        }

        // Initialize dependent dropdowns on load
        loadAdmissionBatchesByBatch($('#batch_id').val(), $('#admission_batch_id').data('selected'));

        // On batch change â†’ reload admission batches
        $('#batch_id').on('change', function() {
            const bid = $(this).val();
            loadAdmissionBatchesByBatch(bid, '');
        });

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

            if (field === 'subject_id') {
                editForm = createSubjectField(field, currentValue);
            } else if (field === 'problems') {
                editForm = createTextareaField(field, currentValue);
            } else if (['registration_status', 'technology_side', 'student_status', 'call_1', 'call_2', 'call_3', 'call_4', 'call_5', 'call_6', 'call_7', 'call_8', 'call_9', 'app', 'whatsapp_group', 'telegram_group', 'mentor_live_1', 'mentor_live_2', 'mentor_live_3', 'mentor_live_4', 'mentor_live_5', 'first_live', 'first_exam_registration', 'first_exam', 'second_live', 'second_exam', 'model_exam_live', 'model_exam', 'practical', 'self_registration', 'assignment', 'mock_test', 'admit_card', 'exam_subject_1', 'exam_subject_2', 'exam_subject_3', 'exam_subject_4', 'exam_subject_5', 'exam_subject_6', 'status'].includes(field)) {
                editForm = createSelectField(field, currentValue);
            } else {
                editForm = createInputField(field, currentValue);
            }

            container.addClass('editing');
            container.append(editForm);

            // Load subjects if it's a subject field
            if (field === 'subject_id') {
                const $select = container.find('select');
                loadSubjectsForEdit($select, currentValue);
            }

            container.find('input, select, textarea').first().focus();
        });

        // Save inline edit
        $(document).off('click.saveInline').on('click.saveInline', '.save-edit', function(e) {
            e.preventDefault();
            e.stopPropagation();

            const container = $(this).closest('.inline-edit');
            const field = container.data('field');
            const id = container.data('id');
            const value = container.find('input, select, textarea').val();

            const btn = $(this);
            if (btn.data('busy')) return;
            btn.data('busy', true);
            btn.prop('disabled', true).html('<i class="ti ti-loader-2 spin"></i>');

            $.ajax({
                url: `/admin/mentor-bosse-converted-leads/${id}/update-mentor-details`,
                method: 'POST',
                data: {
                    field: field,
                    value: value,
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        container.find('.display-value').text(response.value || value);
                        // Update the data-current attribute with the new value
                        container.data('current', response.value || value);
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
            const inputType = field === 'call_time' ? 'time' : 'text';
            return `
                <div class="edit-form">
                    <input type="${inputType}" value="${displayValue}" class="form-control form-control-sm" autocomplete="off" autocapitalize="off" spellcheck="false">
                    <div class="btn-group mt-1">
                        <button type="button" class="btn btn-success btn-sm save-edit">Save</button>
                        <button type="button" class="btn btn-secondary btn-sm cancel-edit">Cancel</button>
                    </div>
                </div>
            `;
        }

        function createSubjectField(field, currentValue) {
            return `
                <div class="edit-form">
                    <select class="form-select form-select-sm">
                        <option value="">Select Subject</option>
                    </select>
                    <div class="btn-group mt-1">
                        <button type="button" class="btn btn-success btn-sm save-edit">Save</button>
                        <button type="button" class="btn btn-secondary btn-sm cancel-edit">Cancel</button>
                    </div>
                </div>
            `;
        }

        function createTextareaField(field, currentValue) {
            const displayValue = currentValue === '-' ? '' : currentValue;
            return `
                <div class="edit-form">
                    <textarea rows="3" class="form-control form-control-sm" autocomplete="off" autocapitalize="off" spellcheck="false">${displayValue}</textarea>
                    <div class="btn-group mt-1">
                        <button type="button" class="btn btn-success btn-sm save-edit">Save</button>
                        <button type="button" class="btn btn-secondary btn-sm cancel-edit">Cancel</button>
                    </div>
                </div>
            `;
        }

        function loadSubjectsForEdit($select, currentValue) {
            $.get('/api/subjects/by-course/2').done(function(list) {
                let options = '<option value="">Select Subject</option>';
                list.forEach(function(item) {
                    const selected = String(currentValue) === String(item.id) ? 'selected' : '';
                    options += `<option value="${item.id}" ${selected}>${item.title}</option>`;
                });
                $select.html(options);
            }).fail(function() {
                $select.html('<option value="">Error loading subjects</option>');
            });
        }

        function createSelectField(field, currentValue) {
            let options = '';

            if (field === 'registration_status') {
                options = `
                    <option value="">Select Registration Status</option>
                    <option value="Paid" ${currentValue === 'Paid' ? 'selected' : ''}>Paid</option>
                    <option value="Not Paid" ${currentValue === 'Not Paid' ? 'selected' : ''}>Not Paid</option>
                `;
            } else if (field === 'technology_side') {
                options = `
                    <option value="">Select Technology Side</option>
                    <option value="No Knowledge" ${currentValue === 'No Knowledge' ? 'selected' : ''}>No Knowledge</option>
                    <option value="Limited Knowledge" ${currentValue === 'Limited Knowledge' ? 'selected' : ''}>Limited Knowledge</option>
                    <option value="Moderate Knowledge" ${currentValue === 'Moderate Knowledge' ? 'selected' : ''}>Moderate Knowledge</option>
                    <option value="High Knowledge" ${currentValue === 'High Knowledge' ? 'selected' : ''}>High Knowledge</option>
                `;
            } else if (field === 'student_status') {
                options = `
                    <option value="">Select Student Status</option>
                    <option value="Low Level" ${currentValue === 'Low Level' ? 'selected' : ''}>Low Level</option>
                    <option value="Below Medium" ${currentValue === 'Below Medium' ? 'selected' : ''}>Below Medium</option>
                    <option value="Medium Level" ${currentValue === 'Medium Level' ? 'selected' : ''}>Medium Level</option>
                    <option value="Advanced Level" ${currentValue === 'Advanced Level' ? 'selected' : ''}>Advanced Level</option>
                `;
            } else if (['call_1', 'call_2', 'call_3', 'call_4', 'call_5', 'call_6', 'call_7', 'call_8', 'call_9'].includes(field)) {
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
                `;
            } else if (field === 'app') {
                options = `
                    <option value="">Select APP Status</option>
                    <option value="Provided app" ${currentValue === 'Provided app' ? 'selected' : ''}>Provided app</option>
                    <option value="OTP Problem" ${currentValue === 'OTP Problem' ? 'selected' : ''}>OTP Problem</option>
                    <option value="Task Completed" ${currentValue === 'Task Completed' ? 'selected' : ''}>Task Completed</option>
                    <option value="Not Respond" ${currentValue === 'Not Respond' ? 'selected' : ''}>Not Respond</option>
                `;
            } else if (field === 'whatsapp_group') {
                options = `
                    <option value="">Select WhatsApp Group Status</option>
                    <option value="Sent link" ${currentValue === 'Sent link' ? 'selected' : ''}>Sent link</option>
                    <option value="Task Completed" ${currentValue === 'Task Completed' ? 'selected' : ''}>Task Completed</option>
                    <option value="Not Responding" ${currentValue === 'Not Responding' ? 'selected' : ''}>Not Responding</option>
                    <option value="Task Complete" ${currentValue === 'Task Complete' ? 'selected' : ''}>Task Complete</option>
                `;
            } else if (field === 'telegram_group') {
                options = `
                    <option value="">Select Telegram Group Status</option>
                    <option value="Sent link" ${currentValue === 'Sent link' ? 'selected' : ''}>Sent link</option>
                    <option value="task complete" ${currentValue === 'task complete' ? 'selected' : ''}>Task complete</option>
                `;
            } else if (['mentor_live_1', 'mentor_live_2', 'mentor_live_3', 'mentor_live_4', 'mentor_live_5'].includes(field)) {
                options = `
                    <option value="">Select Mentor Live Status</option>
                    <option value="Not Respond" ${currentValue === 'Not Respond' ? 'selected' : ''}>Not Respond</option>
                    <option value="Task Complete" ${currentValue === 'Task Complete' ? 'selected' : ''}>Task Complete</option>
                `;
            } else if (['first_live', 'second_live', 'model_exam_live'].includes(field)) {
                options = `
                    <option value="">Select Live Status</option>
                    <option value="Not Respond" ${currentValue === 'Not Respond' ? 'selected' : ''}>Not Respond</option>
                    <option value="1 subject attend" ${currentValue === '1 subject attend' ? 'selected' : ''}>1 subject attend</option>
                    <option value="2 subject attend" ${currentValue === '2 subject attend' ? 'selected' : ''}>2 subject attend</option>
                    <option value="3 subject attend" ${currentValue === '3 subject attend' ? 'selected' : ''}>3 subject attend</option>
                    <option value="4 subject attend" ${currentValue === '4 subject attend' ? 'selected' : ''}>4 subject attend</option>
                    <option value="5 subject attend" ${currentValue === '5 subject attend' ? 'selected' : ''}>5 subject attend</option>
                    <option value="6 subject attend" ${currentValue === '6 subject attend' ? 'selected' : ''}>6 subject attend</option>
                    <option value="Task complete" ${currentValue === 'Task complete' ? 'selected' : ''}>Task complete</option>
                `;
            } else if (['first_exam_registration', 'practical', 'self_registration', 'mock_test', 'admit_card'].includes(field)) {
                options = `
                    <option value="">Select Status</option>
                    <option value="Did not" ${currentValue === 'Did not' ? 'selected' : ''}>Did not</option>
                    <option value="Task complete" ${currentValue === 'Task complete' ? 'selected' : ''}>Task complete</option>
                `;
            } else if (['first_exam', 'second_exam', 'model_exam', 'assignment'].includes(field)) {
                options = `
                    <option value="">Select Exam Status</option>
                    <option value="not respond" ${currentValue === 'not respond' ? 'selected' : ''}>not respond</option>
                    <option value="1 subject attend" ${currentValue === '1 subject attend' ? 'selected' : ''}>1 subject attend</option>
                    <option value="2 subject attend" ${currentValue === '2 subject attend' ? 'selected' : ''}>2 subject attend</option>
                    <option value="3 subject attend" ${currentValue === '3 subject attend' ? 'selected' : ''}>3 subject attend</option>
                    <option value="4 subject attend" ${currentValue === '4 subject attend' ? 'selected' : ''}>4 subject attend</option>
                    <option value="5 subject attend" ${currentValue === '5 subject attend' ? 'selected' : ''}>5 subject attend</option>
                    <option value="6 subject attend" ${currentValue === '6 subject attend' ? 'selected' : ''}>6 subject attend</option>
                    <option value="task complete" ${currentValue === 'task complete' ? 'selected' : ''}>task complete</option>
                `;
            } else if (['exam_subject_1', 'exam_subject_2', 'exam_subject_3', 'exam_subject_4', 'exam_subject_5', 'exam_subject_6'].includes(field)) {
                options = `
                    <option value="">Select Exam Subject Status</option>
                    <option value="Did not log in on time" ${currentValue === 'Did not log in on time' ? 'selected' : ''}>Did not log in on time</option>
                    <option value="missed the exam" ${currentValue === 'missed the exam' ? 'selected' : ''}>missed the exam</option>
                    <option value="technical issue" ${currentValue === 'technical issue' ? 'selected' : ''}>technical issue</option>
                    <option value="task complete" ${currentValue === 'task complete' ? 'selected' : ''}>task complete</option>
                `;
            } else if (field === 'status') {
                options = `
                    <option value="">Registration fee</option>
                    <option value="Paid" ${currentValue === 'Paid' ? 'selected' : ''}>Paid</option>
                    <option value="Received" ${currentValue === 'Received' ? 'selected' : ''}>Received</option>
                    <option value="Admission cancel" ${currentValue === 'Admission cancel' ? 'selected' : ''}>Admission cancel</option>
                    <option value="Active" ${currentValue === 'Active' ? 'selected' : ''}>Active</option>
                    <option value="Inactive" ${currentValue === 'Inactive' ? 'selected' : ''}>Inactive</option>
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
    });
</script>
@include('admin.converted-leads.partials.mentor-flag-inline-scripts')
@endpush
