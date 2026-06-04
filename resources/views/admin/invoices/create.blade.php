@extends('layouts.mantis')

@section('title', 'Create Invoice - ' . $student->name)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">Create Invoice for {{ $student->name }}</h4>
                        <a href="{{ route('admin.invoices.index', $student->id) }}" class="btn btn-secondary">
                            <i class="ti ti-arrow-left"></i> Back to Invoices
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.invoices.store', $student->id) }}" method="POST" id="invoiceCreateForm">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="invoice_type" class="form-label">Invoice Type <span class="text-danger">*</span></label>
                                    <select class="form-control @error('invoice_type') is-invalid @enderror" name="invoice_type" id="invoice_type" required>
                                        <option value="">Select Invoice Type</option>
                                        <option value="course" {{ old('invoice_type') == 'course' ? 'selected' : '' }}>Course</option>
                                        <option value="e-service" {{ old('invoice_type') == 'e-service' ? 'selected' : '' }}>E-Service</option>
                                        <option value="batch_change" {{ old('invoice_type') == 'batch_change' ? 'selected' : '' }}>Batch Change</option>
                                        <option value="fine" {{ old('invoice_type') == 'fine' ? 'selected' : '' }}>Fine</option>
                                    </select>
                                    @error('invoice_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div id="course_type_fields" class="row g-3" style="display: none;">
                                @include('admin.invoices.partials.course-invoice-fields', [
                                    'courses' => $courses,
                                    'courseFeeContext' => $courseFeeContext,
                                ])
                            </div>

                            <div class="col-md-6" id="batch_change_selection" style="display: none;">
                                <div class="mb-3">
                                    <label for="batch_change_batch_id" class="form-label">New Batch <span class="text-danger">*</span></label>
                                    <select class="form-control @error('batch_id') is-invalid @enderror" id="batch_change_batch_id" disabled>
                                        <option value="">Select Batch</option>
                                    </select>
                                    @error('batch_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">Fixed fee: ₹2,000.00. Student batch updates after invoice is created.</div>
                                </div>
                            </div>

                            <div class="col-md-6" id="service_name_field" style="display: none;">
                                <div class="mb-3">
                                    <label for="service_name" class="form-label">Service Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('service_name') is-invalid @enderror"
                                           name="service_name" id="service_name" value="{{ old('service_name') }}" disabled>
                                    @error('service_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6" id="service_amount_field" style="display: none;">
                                <div class="mb-3">
                                    <label for="service_amount" class="form-label">Service Amount <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control @error('service_amount') is-invalid @enderror"
                                           name="service_amount" id="service_amount" step="0.01" min="0"
                                           value="{{ old('service_amount') }}" disabled>
                                    @error('service_amount')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6" id="fine_type_field" style="display: none;">
                                <div class="mb-3">
                                    <label for="fine_type" class="form-label">Fine Type <span class="text-danger">*</span></label>
                                    <select class="form-control @error('fine_type') is-invalid @enderror" name="fine_type" id="fine_type" disabled>
                                        <option value="">Select Fine Type</option>
                                        <option value="Bose Registration Fine" {{ old('fine_type') == 'Bose Registration Fine' ? 'selected' : '' }}>Bose Registration Fine</option>
                                        <option value="Nios Registration Fine" {{ old('fine_type') == 'Nios Registration Fine' ? 'selected' : '' }}>Nios Registration Fine</option>
                                        <option value="Nios Exam Fine" {{ old('fine_type') == 'Nios Exam Fine' ? 'selected' : '' }}>Nios Exam Fine</option>
                                    </select>
                                    @error('fine_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6" id="fine_amount_field" style="display: none;">
                                <div class="mb-3">
                                    <label for="fine_amount" class="form-label">Fine Amount <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control @error('fine_amount') is-invalid @enderror"
                                           name="fine_amount" id="fine_amount" step="0.01" min="0"
                                           value="{{ old('fine_amount') }}" disabled>
                                    <div class="form-text">This value will be copied to Total Amount.</div>
                                    @error('fine_amount')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="total_amount" class="form-label">Total Amount <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control @error('total_amount') is-invalid @enderror"
                                           name="total_amount" id="total_amount" step="0.01" min="0"
                                           value="{{ old('total_amount') }}" required>
                                    @error('total_amount')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="invoice_date" class="form-label">Invoice Date <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control @error('invoice_date') is-invalid @enderror"
                                           name="invoice_date" id="invoice_date"
                                           value="{{ old('invoice_date', now()->toDateString()) }}" required>
                                    @error('invoice_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="mb-3">
                                    <label class="form-label">Student Information</label>
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <p><strong>Name:</strong> {{ $student->name }}</p>
                                                    <p><strong>Phone:</strong> {{ $student->code }} {{ $student->phone }}</p>
                                                    <p><strong>Email:</strong> {{ $student->email ?? 'N/A' }}</p>
                                                    <p><strong>Lead Type:</strong> {{ $courseFeeContext['isB2b'] ? 'B2B (Plan / B2B amount for Junior Vlogger)' : 'In House' }}</p>
                                                </div>
                                                <div class="col-md-6">
                                                    <p><strong>Current Course:</strong> {{ $student->course->title ?? 'N/A' }}</p>
                                                    <p><strong>Batch:</strong> {{ $student->batch->title ?? 'N/A' }}</p>
                                                    <p><strong>Academic Assistant:</strong> {{ $student->academicAssistant->name ?? 'N/A' }}</p>
                                                    @if($student->leadDetail)
                                                        <p><strong>Class:</strong> {{ $student->leadDetail->class ?? 'N/A' }}</p>
                                                        @if($student->leadDetail->university_id)
                                                            <p><strong>University:</strong> {{ $student->leadDetail->university->title ?? 'N/A' }}</p>
                                                            <p><strong>Course Type:</strong> {{ $student->leadDetail->course_type ?? 'N/A' }}</p>
                                                        @endif
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12">
                                <button type="submit" class="btn btn-primary" id="invoiceSubmitBtn">
                                    <i class="ti ti-device-floppy"></i> Create Invoice
                                </button>
                                <a href="{{ route('admin.invoices.index', $student->id) }}" class="btn btn-secondary">
                                    <i class="ti ti-x"></i> Cancel
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const studentId = @json($student->id);
    const invoiceTypeSelect = document.getElementById('invoice_type');
    const courseTypeFields = document.getElementById('course_type_fields');
    const courseSelect = document.getElementById('course_id');
    const courseBatchSelect = document.getElementById('course_batch_id');
    const batchChangeSelect = document.getElementById('batch_change_batch_id');
    const totalAmountInput = document.getElementById('total_amount');
    const serviceAmountInput = document.getElementById('service_amount');
    const fineAmountInput = document.getElementById('fine_amount');
    const form = document.getElementById('invoiceCreateForm');
    let submitting = false;

    const existingCourseInvoices = @json($existingCourseInvoices);
    let courseInvoiceCanSubmit = true;

    const studentData = {
        class: @json($courseFeeContext['studentClass'] ? strtolower($courseFeeContext['studentClass']) : null),
        courseType: @json($courseFeeContext['courseType']),
        universityId: @json($student->leadDetail?->university_id),
        isB2b: @json($courseFeeContext['isB2b']),
        batchChangeCourseId: @json($student->course_id),
        oldBatchId: @json(old('batch_id')),
        oldCourseId: @json(old('course_id')),
    };

    function toNumber(value) {
        const numeric = parseFloat(String(value ?? '').replace(/[^\d.-]/g, ''));
        return Number.isFinite(numeric) ? numeric : 0;
    }

    function formatINR(amount) {
        const safe = Number.isFinite(amount) ? amount : 0;
        return '₹' + safe.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    function csrfToken() {
        const meta = document.querySelector('meta[name="csrf-token"]');
        return meta ? meta.getAttribute('content') : '';
    }

    function shouldUseB2bBatchAmount(courseId) {
        return parseInt(courseId, 10) === 25 && !!studentData.isB2b;
    }

    function getBatchDataAmounts(option) {
        if (!option) {
            return { amount: 0, b2bAmount: 0, sslcAmount: 0, plustwoAmount: 0 };
        }
        return {
            amount: toNumber(option.getAttribute('data-amount')),
            b2bAmount: toNumber(option.getAttribute('data-b2b-amount')),
            sslcAmount: toNumber(option.getAttribute('data-sslc-amount')),
            plustwoAmount: toNumber(option.getAttribute('data-plustwo-amount')),
        };
    }

    function getBatchLabel(courseId) {
        return shouldUseB2bBatchAmount(courseId) ? 'Plan' : 'Batch';
    }

    function setCourseInvoiceCanSubmit(canSubmit) {
        courseInvoiceCanSubmit = canSubmit;
        const submitBtn = document.getElementById('invoiceSubmitBtn');
        if (invoiceTypeSelect.value === 'course') {
            submitBtn.disabled = !canSubmit;
        } else if (!submitting) {
            submitBtn.disabled = false;
        }
    }

    function showCourseValidationAlert(message, existingInvoice) {
        const wrap = document.getElementById('course_invoice_validation_wrap');
        const alertEl = document.getElementById('course_invoice_validation_alert');
        if (!wrap || !alertEl) return;
        let html = message;
        if (existingInvoice && existingInvoice.show_url) {
            html += ' <a href="' + existingInvoice.show_url + '" class="alert-link" target="_blank">View invoice ' + (existingInvoice.invoice_number || '') + '</a>';
        }
        alertEl.innerHTML = html;
        wrap.style.display = message ? 'block' : 'none';
    }

    function clearCourseValidationAlert() {
        showCourseValidationAlert('', null);
    }

    function applyCourseInvoiceValidation(payload) {
        if (!payload) {
            clearCourseValidationAlert();
            setCourseInvoiceCanSubmit(true);
            return;
        }
        const messages = [];
        if (payload.existing_invoice) {
            messages.push(payload.message || ('An invoice already exists for this course and batch (Invoice #' + payload.existing_invoice.invoice_number + ').'));
        }
        if (payload.batch_error) {
            messages.push(payload.batch_error);
        }
        if (!payload.success && payload.message && messages.length === 0) {
            messages.push(payload.message);
        }
        const message = messages.join(' ');
        const canSubmit = payload.can_submit !== false && !payload.existing_invoice && !payload.batch_error;
        showCourseValidationAlert(message, payload.existing_invoice || null);
        setCourseInvoiceCanSubmit(canSubmit);
    }

    function courseBatchInvoiceKey(courseId, batchId) {
        return String(courseId) + ':' + (batchId ? String(batchId) : 'none');
    }

    function checkExistingCourseBatchInvoiceLocal(courseId, batchId) {
        if (!courseId) {
            clearCourseValidationAlert();
            setCourseInvoiceCanSubmit(true);
            return false;
        }
        const isEdumaster = parseInt(courseId, 10) === 23;
        if (!isEdumaster && !batchId) {
            clearCourseValidationAlert();
            return false;
        }
        const key = courseBatchInvoiceKey(courseId, batchId || null);
        const existing = existingCourseInvoices[key];
        if (existing) {
            let message = 'An invoice already exists for this course and batch (Invoice #' + existing.invoice_number + ').';
            if (existing.course_title && existing.batch_title) {
                message = 'An invoice already exists for ' + existing.course_title + ' — ' + existing.batch_title
                    + ' (Invoice #' + existing.invoice_number + ').';
            } else if (existing.course_title) {
                message = 'An invoice already exists for ' + existing.course_title
                    + ' (Invoice #' + existing.invoice_number + ').';
            }
            applyCourseInvoiceValidation({
                success: false,
                message: message,
                existing_invoice: existing,
                can_submit: false,
            });
            return true;
        }
        return false;
    }

    function setFieldEnabled(el, enabled, withName) {
        if (!el) return;
        el.disabled = !enabled;
        if (withName !== undefined) {
            if (enabled && withName) {
                el.setAttribute('name', withName);
            } else {
                el.removeAttribute('name');
            }
        }
    }

    function toggleFields() {
        const invoiceType = invoiceTypeSelect.value;

        courseTypeFields.style.display = 'none';
        document.getElementById('batch_change_selection').style.display = 'none';
        document.getElementById('service_name_field').style.display = 'none';
        document.getElementById('service_amount_field').style.display = 'none';
        document.getElementById('fine_type_field').style.display = 'none';
        document.getElementById('fine_amount_field').style.display = 'none';

        setFieldEnabled(courseSelect, false);
        setFieldEnabled(courseBatchSelect, false);
        setFieldEnabled(batchChangeSelect, false);
        setFieldEnabled(document.getElementById('service_name'), false);
        setFieldEnabled(document.getElementById('service_amount'), false);
        setFieldEnabled(document.getElementById('fine_type'), false);
        setFieldEnabled(document.getElementById('fine_amount'), false);

        ['fee_pg_amount', 'fee_ug_amount', 'fee_plustwo_amount', 'fee_sslc_amount', 'custom_total_amount'].forEach(function(id) {
            setFieldEnabled(document.getElementById(id), false);
        });

        totalAmountInput.readOnly = false;

        if (invoiceType === 'course') {
            courseTypeFields.style.display = 'flex';
            courseTypeFields.classList.add('row', 'g-3');
            setFieldEnabled(courseSelect, true, 'course_id');
            setFieldEnabled(courseBatchSelect, true, 'batch_id');
            ['fee_pg_amount', 'fee_ug_amount', 'fee_plustwo_amount', 'fee_sslc_amount', 'custom_total_amount'].forEach(function(id) {
                const el = document.getElementById(id);
                if (el) setFieldEnabled(el, true, id);
            });
            if (studentData.oldCourseId) {
                handleCourseChange();
            } else {
                resetFeePreview();
                clearCourseValidationAlert();
                setCourseInvoiceCanSubmit(true);
            }
        } else {
            clearCourseValidationAlert();
            setCourseInvoiceCanSubmit(true);
        }

        if (invoiceType === 'batch_change') {
            document.getElementById('batch_change_selection').style.display = 'block';
            setFieldEnabled(batchChangeSelect, true, 'batch_id');
            loadBatchChangeBatches(studentData.oldBatchId);
            totalAmountInput.value = '2000';
            totalAmountInput.readOnly = true;
        } else if (invoiceType === 'e-service') {
            document.getElementById('service_name_field').style.display = 'block';
            document.getElementById('service_amount_field').style.display = 'block';
            setFieldEnabled(document.getElementById('service_name'), true, 'service_name');
            setFieldEnabled(document.getElementById('service_amount'), true, 'service_amount');
        } else if (invoiceType === 'fine') {
            document.getElementById('fine_type_field').style.display = 'block';
            document.getElementById('fine_amount_field').style.display = 'block';
            setFieldEnabled(document.getElementById('fine_type'), true, 'fine_type');
            setFieldEnabled(document.getElementById('fine_amount'), true, 'fine_amount');
            totalAmountInput.value = fineAmountInput.value || '';
            totalAmountInput.readOnly = true;
        }
    }

    function loadBatchesByCourse(courseId, selectedBatchId) {
        const batchSection = document.getElementById('course_batch_selection');
        const batchLabel = getBatchLabel(courseId);
        const batchLabelEl = batchSection ? batchSection.querySelector('label[for="course_batch_id"]') : null;
        if (batchLabelEl) {
            batchLabelEl.innerHTML = batchLabel + ' <span class="text-danger batch-required-marker">*</span>';
        }

        if (!courseId) {
            courseBatchSelect.innerHTML = '<option value="">Select ' + batchLabel + '</option>';
            batchSection.style.display = 'none';
            return;
        }

        if (parseInt(courseId, 10) === 23) {
            batchSection.style.display = 'none';
            courseBatchSelect.innerHTML = '<option value="">No batch required</option>';
            courseBatchSelect.value = '';
            recalculateFromApi(courseId, null);
            return;
        }

        batchSection.style.display = 'block';
        courseBatchSelect.innerHTML = '<option value="">Loading...</option>';

        $.get('/api/batches/by-course/' + courseId).done(function(response) {
            let options = '<option value="">Select ' + batchLabel + '</option>';
            if (response.success && response.batches) {
                response.batches.forEach(function(batch) {
                    const selected = String(selectedBatchId) === String(batch.id) ? ' selected' : '';
                    const amount = batch.amount != null ? batch.amount : '';
                    const b2bAmount = batch.b2b_amount != null ? batch.b2b_amount : '';
                    const useB2b = shouldUseB2bBatchAmount(courseId);
                    let optionLabel = batch.title;
                    if (useB2b && b2bAmount !== '') {
                        optionLabel += ' — ' + formatINR(b2bAmount) + ' (B2B)';
                    } else if (amount !== '') {
                        optionLabel += ' — ' + formatINR(amount);
                    }
                    if (parseInt(batch.is_active, 10) === 0) {
                        optionLabel += ' (Inactive)';
                    }
                    options += '<option value="' + batch.id + '"' + selected +
                        ' data-amount="' + (batch.amount ?? '') + '"' +
                        ' data-sslc-amount="' + (batch.sslc_amount ?? '') + '"' +
                        ' data-plustwo-amount="' + (batch.plustwo_amount ?? '') + '"' +
                        ' data-b2b-amount="' + (batch.b2b_amount ?? '') + '">' +
                        optionLabel +
                        '</option>';
                });
            }
            courseBatchSelect.innerHTML = options;
            if (selectedBatchId) {
                courseBatchSelect.value = selectedBatchId;
            }
            if (courseBatchSelect.value) {
                if (checkExistingCourseBatchInvoiceLocal(courseId, courseBatchSelect.value)) {
                    return;
                }
                recalculateFromApi(courseId, courseBatchSelect.value);
            } else {
                resetFeePreview();
                resetCourseInvoiceAmounts();
                if (parseInt(courseId, 10) === 23) {
                    if (checkExistingCourseBatchInvoiceLocal(courseId, null)) {
                        return;
                    }
                    recalculateFromApi(courseId, null);
                } else {
                    applyCourseInvoiceValidation({
                        success: false,
                        batch_error: 'Please select a ' + getBatchLabel(courseId).toLowerCase() + ' for this course.',
                        can_submit: false,
                    });
                }
            }
        }).fail(function() {
            courseBatchSelect.innerHTML = '<option value="">Error loading batches</option>';
        });
    }

    function recalculateFromApi(courseId, batchId) {
        if (!courseId) {
            totalAmountInput.value = '';
            return;
        }

        const payload = {
            _token: csrfToken(),
            course_id: courseId,
            batch_id: batchId || null,
            custom_total_amount: $('#custom_total_amount').val() || null,
            fee_pg_amount: $('#fee_pg_amount').val() || null,
            fee_ug_amount: $('#fee_ug_amount').val() || null,
            fee_plustwo_amount: $('#fee_plustwo_amount').val() || null,
            fee_sslc_amount: $('#fee_sslc_amount').val() || null,
        };

        $.post('/api/invoices/calculate-amount/' + studentId, payload).done(function(response) {
            if (!response.success) {
                applyCourseInvoiceValidation(response);
                return;
            }
            applyCourseInvoiceValidation(response);
            showFeePreview();
            const standardPreview = document.getElementById('standard_course_fee_preview');
            if (standardPreview && parseInt(courseId, 10) !== 23) {
                standardPreview.style.display = 'block';
            }
            totalAmountInput.value = parseFloat(response.total_amount).toFixed(2);

            const preview = document.getElementById('course_total_preview');
            if (preview) {
                preview.style.display = '';
                preview.textContent = 'Total: ' + formatINR(response.total_amount);
            }
            const batchPreview = document.getElementById('batch_amount_preview');
            if (batchPreview) batchPreview.style.display = '';

            const titleEl = document.getElementById('preview_course_title');
            const amountLine = document.getElementById('preview_course_amount_line');
            if (titleEl && response.course_title) {
                titleEl.textContent = response.course_title;
            }
            if (amountLine && shouldUseB2bBatchAmount(courseId)) {
                amountLine.textContent = '';
            } else if (amountLine && parseInt(courseId, 10) !== 23) {
                amountLine.textContent = ' - ' + formatINR(response.course_amount);
            }

            const uniBlock = document.getElementById('preview_university_block');
            if (uniBlock) {
                uniBlock.style.display = parseInt(courseId, 10) === 9 ? '' : 'none';
            }

            updateBatchPreview(courseId, batchId, response);
        }).fail(function(xhr) {
            const data = xhr.responseJSON;
            if (data) {
                applyCourseInvoiceValidation(data);
            }
        });
    }

    function updateBatchPreview(courseId, batchId, apiResponse) {
        const preview = document.getElementById('batch_amount_preview');
        if (!preview || !courseBatchSelect) return;

        const selectedOption = courseBatchSelect.options[courseBatchSelect.selectedIndex];
        if (!selectedOption || !selectedOption.value) {
            return;
        }

        const amounts = getBatchDataAmounts(selectedOption);
        const title = selectedOption.text.split('—')[0].trim();
        const useB2b = apiResponse && apiResponse.use_b2b_batch_amount !== undefined
            ? !!apiResponse.use_b2b_batch_amount
            : shouldUseB2bBatchAmount(courseId);
        let shownAmount = amounts.amount;
        let label = '';

        if (useB2b) {
            shownAmount = amounts.b2bAmount;
            label = apiResponse && apiResponse.batch_amount_label ? apiResponse.batch_amount_label : 'B2B Amount';
            if ((!shownAmount || shownAmount <= 0) && apiResponse && apiResponse.batch_amount) {
                shownAmount = parseFloat(apiResponse.batch_amount);
            }
        } else if (parseInt(courseId, 10) === 16 && studentData.class) {
            if (studentData.class === 'sslc' && amounts.sslcAmount > 0) {
                shownAmount = amounts.sslcAmount;
                label = 'SSLC Amount';
            } else if (amounts.plustwoAmount > 0) {
                shownAmount = amounts.plustwoAmount;
                label = 'Plus Two Amount';
            }
        }

        const labelHtml = label ? ' <span class="badge bg-primary ms-1">' + label + '</span>' : '';
        preview.innerHTML = '<i class="ti ti-layers-intersect"></i> ' + getBatchLabel(courseId) + ': <strong>' + title + '</strong> - ' + formatINR(shownAmount) + labelHtml;
    }

    function resetFeePreview() {
        const emptyHint = document.getElementById('course_fee_empty_hint');
        const standardPreview = document.getElementById('standard_course_fee_preview');
        const totalPreview = document.getElementById('course_total_preview');
        const batchPreview = document.getElementById('batch_amount_preview');
        if (emptyHint) emptyHint.style.display = '';
        if (standardPreview) standardPreview.style.display = 'none';
        if (totalPreview) totalPreview.style.display = 'none';
        if (batchPreview) batchPreview.style.display = 'none';
    }

    function resetCourseInvoiceAmounts() {
        totalAmountInput.value = '';
        const customTotal = document.getElementById('custom_total_amount');
        if (customTotal) customTotal.value = '';
        ['fee_pg_amount', 'fee_ug_amount', 'fee_plustwo_amount', 'fee_sslc_amount'].forEach(function(id) {
            const el = document.getElementById(id);
            if (el) el.value = '';
        });
        const totalPreview = document.getElementById('course_total_preview');
        if (totalPreview) {
            totalPreview.textContent = '';
            totalPreview.style.display = 'none';
        }
    }

    function showFeePreview() {
        const emptyHint = document.getElementById('course_fee_empty_hint');
        if (emptyHint) emptyHint.style.display = 'none';
    }

    function loadBatchChangeBatches(selectedBatchId) {
        const courseId = studentData.batchChangeCourseId;
        if (!courseId || !batchChangeSelect) {
            batchChangeSelect.innerHTML = '<option value="">No course on student record</option>';
            return;
        }
        batchChangeSelect.innerHTML = '<option value="">Loading...</option>';
        $.get('/api/batches/by-course/' + courseId).done(function(response) {
            let options = '<option value="">Select Batch</option>';
            if (response.success && response.batches) {
                response.batches.forEach(function(batch) {
                    const selected = selectedBatchId && String(selectedBatchId) === String(batch.id) ? ' selected' : '';
                    options += '<option value="' + batch.id + '"' + selected + '>' + batch.title + '</option>';
                });
            }
            batchChangeSelect.innerHTML = options;
        }).fail(function() {
            batchChangeSelect.innerHTML = '<option value="">Error loading batches</option>';
        });
    }

    function handleCourseChange() {
        const courseId = courseSelect.value;

        if (!courseId) {
            resetFeePreview();
            resetCourseInvoiceAmounts();
            document.getElementById('course_batch_selection').style.display = 'none';
            courseBatchSelect.innerHTML = '<option value="">Select Batch</option>';
            clearCourseValidationAlert();
            setCourseInvoiceCanSubmit(true);
            return;
        }

        resetCourseInvoiceAmounts();
        resetFeePreview();
        clearCourseValidationAlert();
        setCourseInvoiceCanSubmit(true);
        showFeePreview();
        const edumasterSection = document.getElementById('edumaster_fee_breakdown');
        const standardPreview = document.getElementById('standard_course_fee_preview');
        const selectedOption = courseSelect.options[courseSelect.selectedIndex];

        if (parseInt(courseId, 10) === 23) {
            if (checkExistingCourseBatchInvoiceLocal(courseId, null)) {
                return;
            }
            showFeePreview();
            if (edumasterSection) edumasterSection.style.display = 'block';
            if (standardPreview) standardPreview.style.display = 'none';
            document.querySelectorAll('.batch-required-marker').forEach(function(el) { el.style.display = 'none'; });
            const edTitle = document.getElementById('edumaster_course_title');
            if (edTitle && selectedOption) {
                edTitle.textContent = selectedOption.dataset.title || selectedOption.text;
            }
        } else {
            if (edumasterSection) edumasterSection.style.display = 'none';
            if (standardPreview) standardPreview.style.display = 'block';
            document.querySelectorAll('.batch-required-marker').forEach(function(el) { el.style.display = ''; });
            const titleEl = document.getElementById('preview_course_title');
            const amountLine = document.getElementById('preview_course_amount_line');
            if (titleEl && selectedOption) {
                titleEl.textContent = selectedOption.dataset.title || selectedOption.text;
            }
            if (amountLine && selectedOption && !shouldUseB2bBatchAmount(courseId)) {
                amountLine.textContent = ' - ' + formatINR(selectedOption.dataset.amount);
            } else if (amountLine && shouldUseB2bBatchAmount(courseId)) {
                amountLine.textContent = '';
            }
        }

        const preserveBatch = courseSelect.value == studentData.oldCourseId ? studentData.oldBatchId : null;
        loadBatchesByCourse(courseId, preserveBatch);
    }

    invoiceTypeSelect.addEventListener('change', toggleFields);
    courseSelect.addEventListener('change', handleCourseChange);
    courseBatchSelect.addEventListener('change', function() {
        const courseId = courseSelect.value;
        const batchId = this.value || null;
        resetCourseInvoiceAmounts();
        if (!batchId) {
            resetFeePreview();
            clearCourseValidationAlert();
            setCourseInvoiceCanSubmit(true);
            return;
        }
        if (checkExistingCourseBatchInvoiceLocal(courseId, batchId)) {
            return;
        }
        clearCourseValidationAlert();
        setCourseInvoiceCanSubmit(true);
        recalculateFromApi(courseId, batchId);
    });

    $('#fee_pg_amount, #fee_ug_amount, #fee_plustwo_amount, #fee_sslc_amount').on('input', function() {
        if (parseInt(courseSelect.value, 10) !== 23) return;
        const sum = toNumber($('#fee_pg_amount').val()) + toNumber($('#fee_ug_amount').val()) +
            toNumber($('#fee_plustwo_amount').val()) + toNumber($('#fee_sslc_amount').val());
        $('#custom_total_amount').val(sum.toFixed(2));
        recalculateFromApi(courseSelect.value, null);
    });

    $('#custom_total_amount').on('input', function() {
        if (parseInt(courseSelect.value, 10) === 23) {
            totalAmountInput.value = toNumber(this.value).toFixed(2);
        }
    });

    serviceAmountInput.addEventListener('input', function() {
        if (invoiceTypeSelect.value === 'e-service') {
            totalAmountInput.value = this.value;
        }
    });

    fineAmountInput.addEventListener('input', function() {
        if (invoiceTypeSelect.value === 'fine') {
            totalAmountInput.value = this.value;
        }
    });

    form.addEventListener('submit', function(e) {
        if (submitting) {
            e.preventDefault();
            return;
        }
        if (invoiceTypeSelect.value === 'course' && !courseInvoiceCanSubmit) {
            e.preventDefault();
            return;
        }
        if (invoiceTypeSelect.value === 'course') {
            const courseId = courseSelect.value;
            const batchId = parseInt(courseId, 10) === 23 ? null : (courseBatchSelect.value || null);
            if (checkExistingCourseBatchInvoiceLocal(courseId, batchId)) {
                e.preventDefault();
                return;
            }
            if (parseInt(courseId, 10) !== 23 && !courseBatchSelect.value) {
                e.preventDefault();
                applyCourseInvoiceValidation({
                    success: false,
                    batch_error: 'Please select a ' + getBatchLabel(courseId).toLowerCase() + ' for this course.',
                    can_submit: false,
                });
                return;
            }
        }
        submitting = true;
        document.getElementById('invoiceSubmitBtn').disabled = true;
    });

    toggleFields();
});
</script>
@endpush
@endsection
