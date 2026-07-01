@php
    $usePlanLabelsForBatch = (int) ($lead->course_id ?? 0) === 25 && (int) ($lead->is_b2b ?? 0) === 1;
    $batchSelectFieldLabel = $usePlanLabelsForBatch ? 'Plan' : 'Batch';
@endphp
<form id="convertLeadForm" enctype="multipart/form-data">
    @csrf
    <div class="row g-3">
        <div class="col-lg-12">
            <div class="p-1">
                <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="name" value="{{ $lead->title }}" required>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="p-1">
                <label for="code" class="form-label">Country Code <span class="text-danger">*</span></label>
                <select class="form-control" name="code" required>
                    <option value="">Select Code</option>
                    @foreach($country_codes as $code => $country)
                    <option value="{{ $code }}" {{ $lead->code == $code ? 'selected' : '' }}>
                        {{ $code }} - {{ $country }}
                    </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="p-1">
                <label for="phone" class="form-label">Phone <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="phone" value="{{ $lead->phone }}" required>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="p-1">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" name="email" value="{{ $lead->email }}">
            </div>
        </div>


        <div class="col-lg-6">
            <div class="p-1">
                <label for="dob" class="form-label">Date of Birth</label>
                <input type="date" class="form-control" name="dob" id="dob" max="{{ date('Y-m-d') }}" value="{{ $lead->studentDetails && $lead->studentDetails->date_of_birth ? $lead->studentDetails->date_of_birth->format('Y-m-d') : '' }}">
            </div>
        </div>

        <div class="col-lg-6">
            <div class="p-1">
                <label for="modal_board_id" class="form-label">Board</label>
                <select class="form-control" name="board_id" id="modal_board_id">
                    <option value="">Select Board</option>
                    @foreach($boards as $board)
                    <option value="{{ $board->id }}">{{ $board->title }} ({{ $board->code }})</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="p-1">
                <label for="modal_batch_id" class="form-label">
                    {{ $batchSelectFieldLabel }} @if((int) ($lead->course_id ?? 0) !== 23)<span class="text-danger">*</span>@endif
                </label>
                <select class="form-control" name="batch_id" id="modal_batch_id" {{ (int) ($lead->course_id ?? 0) !== 23 ? 'required' : '' }}>
                    <option value="">Select {{ $batchSelectFieldLabel }}</option>
                    @foreach($batches as $item)
                    <option
                        value="{{ $item->id }}"
                        data-amount="{{ (float) ($item->amount ?? 0) }}"
                        data-sslc-amount="{{ (float) ($item->sslc_amount ?? 0) }}"
                        data-plustwo-amount="{{ (float) ($item->plustwo_amount ?? 0) }}"
                        data-b2b-amount="{{ (float) ($item->b2b_amount ?? 0) }}"
                        {{ (int) ($batch->id ?? 0) === (int) $item->id ? 'selected' : '' }}>
                        {{ $item->title }}{{ (int) ($item->is_active ?? 0) === 0 ? ' (Inactive)' : '' }}
                    </option>
                    @endforeach
                </select>
            </div>
        </div>

        @if($course && $course->title)
        <div class="col-lg-12">
            <div class="p-1">
                <div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox" name="need_mobile" id="need_mobile" value="1">
                    <label class="form-check-label" for="need_mobile">
                        Need Mobile (+₹1000.00)
                    </label>
                </div>
            </div>
        </div>
        <div class="col-lg-12" id="asset_id_field_wrapper" style="display: none;">
            <div class="p-1">
                <label for="asset_id" class="form-label">Asset ID <span class="text-danger" id="asset_id_required_marker" style="display: none;">*</span></label>
                <input type="text" class="form-control" name="asset_id" id="asset_id" placeholder="Enter asset ID">
            </div>
        </div>
        <div class="col-lg-12">
            <div class="p-1">
                <label class="form-label">Course Information</label>
                @if($course->id == 23)
                <div class="bg-light p-3 rounded">
                    <div class="mb-2">
                        <strong>{{ $course->title }}</strong>
                        <small class="text-muted d-block">Enter the fee breakdown for this course.</small>
                    </div>
                    <div class="row g-3">
                        <div class="col-lg-4">
                            <label for="course23_custom_total_amount" class="form-label">Total Amount</label>
                            <input type="number" class="form-control" name="custom_total_amount" id="course23_custom_total_amount" step="0.01" min="0" value="{{ number_format($totalAmount, 2, '.', '') }}">
                        </div>
                        <div class="col-lg-4">
                            <label for="course23_fee_pg_amount" class="form-label">PG Amount</label>
                            <input type="number" class="form-control" name="fee_pg_amount" id="course23_fee_pg_amount" step="0.01" min="0" value="">
                        </div>
                        <div class="col-lg-4">
                            <label for="course23_fee_ug_amount" class="form-label">UG Amount</label>
                            <input type="number" class="form-control" name="fee_ug_amount" id="course23_fee_ug_amount" step="0.01" min="0" value="">
                        </div>
                        <div class="col-lg-4">
                            <label for="course23_fee_plustwo_amount" class="form-label">Plus Two Amount</label>
                            <input type="number" class="form-control" name="fee_plustwo_amount" id="course23_fee_plustwo_amount" step="0.01" min="0" value="">
                        </div>
                        <div class="col-lg-4">
                            <label for="course23_fee_sslc_amount" class="form-label">SSLC Amount</label>
                            <input type="number" class="form-control" name="fee_sslc_amount" id="course23_fee_sslc_amount" step="0.01" min="0" value="">
                        </div>
                    </div>
                </div>
                @else
                <div class="form-control-plaintext bg-light p-2 rounded">
                    <strong>{{ $course->title }}</strong> - ₹{{ number_format($courseAmount, 2) }}

                    @if($course->id == 9 && $courseType && $university)
                    <br><small class="text-info">
                        <i class="fas fa-university"></i> University: <strong>{{ $university->title }}</strong>
                    </small>
                    <br><small class="text-info">
                        <i class="fas fa-graduation-cap"></i> Course Type: <strong>{{ $courseType }}</strong>
                    </small>
                    @if($universityAmount > 0)
                    <br><small class="text-info">
                        <i class="fas fa-rupee-sign"></i> {{ $courseType }} Course Fee: ₹{{ number_format($universityAmount, 2) }}
                    </small>
                    @endif
                    @endif

                    @if($studentClass)
                    <br><small class="text-info">
                        <i class="fas fa-user-graduate"></i> Class: <strong>{{ strtoupper($studentClass) }}</strong>
                    </small>
                    @endif

                    <br><small class="text-info" id="batch_amount_preview">
                        <i class="fas fa-layer-group"></i> {{ $batchSelectFieldLabel }}: <strong>{{ $batch->title ?? '-' }}</strong> - ₹{{ number_format($batchAmount, 2) }}
                        @if($batchAmountLabel)
                        <span class="badge bg-primary ms-1">{{ $batchAmountLabel }}</span>
                        @endif
                    </small>

                    <div class="mt-2">
                        <label for="custom_total_amount" class="form-label mb-1">Total Amount</label>
                        <input type="number" class="form-control" name="custom_total_amount" id="custom_total_amount" step="0.01" min="0" value="{{ number_format($totalAmount, 2, '.', '') }}">
                        <small class="text-muted" id="course_total_preview">Auto-calculated from course, batch, and university fees. You can override this amount.</small>
                    </div>
                </div>
                @endif
            </div>
        </div>
        @endif


        <div class="col-12">
            <div class="p-1">
                <label for="remarks" class="form-label">Remarks</label>
                <textarea class="form-control" name="remarks" rows="3" placeholder="Enter conversion remarks">{{ $lead->remarks }}</textarea>
            </div>
        </div>

        <!-- Payment Collection Section -->
        @if($course && $course->title)
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">Payment Collection</h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="payment_collected" id="modal_payment_collected" value="1">
                                <label class="form-check-label" for="modal_payment_collected">
                                    Payment Collected
                                </label>
                            </div>
                        </div>

                        <div id="payment_fields" style="display: none;" class="col-12">
                            <div class="row g-3">
                                @if($lead->course_id == 23)
                                <div class="col-12">
                                    <div class="alert alert-info mb-0">
                                        Enter the paid amount for each category and upload the corresponding payment proof.
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="p-1">
                                        <label for="payment_pg_amount" class="form-label">PG Paid Amount <span class="text-danger payment-required" style="display: none;">*</span></label>
                                        <input type="number" class="form-control" name="payment_pg_amount" id="payment_pg_amount" step="0.01" min="0">
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="p-1">
                                        <label for="payment_pg_file" class="form-label">PG Payment Proof <span class="text-danger payment-required" style="display: none;">*</span></label>
                                        <input type="file" class="form-control" name="payment_pg_file" id="payment_pg_file" accept=".pdf,.jpg,.jpeg,.png">
                                    </div>
                                </div>

                                <div class="col-lg-6">
                                    <div class="p-1">
                                        <label for="payment_ug_amount" class="form-label">UG Paid Amount <span class="text-danger payment-required" style="display: none;">*</span></label>
                                        <input type="number" class="form-control" name="payment_ug_amount" id="payment_ug_amount" step="0.01" min="0">
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="p-1">
                                        <label for="payment_ug_file" class="form-label">UG Payment Proof <span class="text-danger payment-required" style="display: none;">*</span></label>
                                        <input type="file" class="form-control" name="payment_ug_file" id="payment_ug_file" accept=".pdf,.jpg,.jpeg,.png">
                                    </div>
                                </div>

                                <div class="col-lg-6">
                                    <div class="p-1">
                                        <label for="payment_plustwo_amount" class="form-label">Plus Two Paid Amount <span class="text-danger payment-required" style="display: none;">*</span></label>
                                        <input type="number" class="form-control" name="payment_plustwo_amount" id="payment_plustwo_amount" step="0.01" min="0">
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="p-1">
                                        <label for="payment_plustwo_file" class="form-label">Plus Two Payment Proof <span class="text-danger payment-required" style="display: none;">*</span></label>
                                        <input type="file" class="form-control" name="payment_plustwo_file" id="payment_plustwo_file" accept=".pdf,.jpg,.jpeg,.png">
                                    </div>
                                </div>

                                <div class="col-lg-6">
                                    <div class="p-1">
                                        <label for="payment_sslc_amount" class="form-label">SSLC Paid Amount <span class="text-danger payment-required" style="display: none;">*</span></label>
                                        <input type="number" class="form-control" name="payment_sslc_amount" id="payment_sslc_amount" step="0.01" min="0">
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="p-1">
                                        <label for="payment_sslc_file" class="form-label">SSLC Payment Proof <span class="text-danger payment-required" style="display: none;">*</span></label>
                                        <input type="file" class="form-control" name="payment_sslc_file" id="payment_sslc_file" accept=".pdf,.jpg,.jpeg,.png">
                                        <small class="text-muted">Accepted formats: PDF, JPG, JPEG, PNG (Max: 2MB)</small>
                                    </div>
                                </div>
                                @else
                                <div class="col-lg-6">
                                    <div class="p-1">
                                        <label for="modal_payment_amount" class="form-label">Payment Amount <span class="text-danger payment-required" style="display: none;">*</span></label>
                                        <input type="number" class="form-control" name="payment_amount" id="modal_payment_amount" step="0.01" min="0">
                                    </div>
                                </div>
                                @endif

                                <div class="col-lg-6">
                                    <div class="p-1">
                                        <label for="modal_payment_type" class="form-label">Payment Type <span class="text-danger payment-required" style="display: none;">*</span></label>
                                        <select class="form-control" name="payment_type" id="modal_payment_type">
                                            <option value="">Select Payment Type</option>
                                            <option value="Cash">Cash</option>
                                            <option value="Online">Online</option>
                                            <option value="Bank">Bank</option>
                                            <option value="Cheque">Cheque</option>
                                            <option value="Card">Card</option>
                                            <option value="Other">Other</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-lg-6">
                                    <div class="p-1">
                                        <label for="modal_transaction_id" class="form-label">Transaction ID <span class="text-danger payment-required" style="display: none;">*</span></label>
                                        <input type="text" class="form-control" name="transaction_id" id="modal_transaction_id" placeholder="Enter transaction ID">
                                    </div>
                                </div>

                                <div class="col-lg-6">
                                    <div class="p-1">
                                        <label for="modal_payment_date" class="form-label">Payment Date</label>
                                        <input type="date" class="form-control" name="payment_date" id="modal_payment_date" value="{{ date('Y-m-d') }}">
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="p-1">
                                        @if($lead->course_id != 23)
                                        <label for="modal_payment_file" class="form-label">Upload Receipt/Proof <span class="text-danger payment-required" style="display: none;">*</span></label>
                                        <input type="file" class="form-control" name="payment_file" id="modal_payment_file" accept=".pdf,.jpg,.jpeg,.png">
                                        <small class="text-muted">Accepted formats: PDF, JPG, JPEG, PNG (Max: 2MB)</small>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <div class="col-12 p-2">
            <button class="btn btn-success float-end" type="button" id="convertLeadBtn">
                <span class="btn-text">Convert Lead</span>
                <span class="btn-loading" style="display: none;">
                    <i class="ti ti-loader-2 spin"></i> Converting...
                </span>
            </button>
        </div>
    </div>
</form>

<style>
    .spin {
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        from {
            transform: rotate(0deg);
        }

        to {
            transform: rotate(360deg);
        }
    }

    .field-error {
        font-size: 0.875rem;
        margin-top: 0.25rem;
        display: block;
        color: #dc3545 !important;
        font-weight: 500;
    }

    .form-control.is-invalid {
        border-color: #dc3545 !important;
        box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25) !important;
    }

    .form-control.is-invalid:focus {
        border-color: #dc3545 !important;
        box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25) !important;
    }

    /* Ensure error messages are visible */
    .p-1 .field-error {
        margin-top: 0.25rem;
        margin-bottom: 0.5rem;
        display: block;
        width: 100%;
    }
</style>

<script>
    $(document).ready(function() {
        const $form = $('#convertLeadForm');
        if (!$form.length || $form.data('convert-init')) {
            return;
        }
        $form.data('convert-init', true);

        let convertSubmitting = false;

        // Cache jQuery objects
        const $paymentCheckbox = $('#modal_payment_collected');
        const $paymentFields = $('#payment_fields');
        const $convertBtn = $('#convertLeadBtn');

        const totalAmountValue = @json($totalAmount);
        const courseId = @json($lead->course_id);
        const isCourse23 = courseId == 23;
        const isEdumasterCourse = Number(courseId) === 23;
        const isB2BLead = @json((int) ($lead->is_b2b ?? 0) === 1);
        const batchPreviewLabel = @json($batchSelectFieldLabel);
        const studentClass = @json($studentClass ? strtolower($studentClass) : null);
        const baseCourseAmount = @json((float) ($courseAmount ?? 0));
        const baseUniversityAmount = @json((float) ($universityAmount ?? 0));
        const $batchSelect = $('#modal_batch_id');
        const $batchAmountPreview = $('#batch_amount_preview');
        const $courseTotalPreview = $('#course_total_preview');
        const $course23TotalInput = $('#course23_custom_total_amount');
        const $customTotalInput = $('#custom_total_amount');
        const $course23FeePg = $('#course23_fee_pg_amount');
        const $course23FeeUg = $('#course23_fee_ug_amount');
        const $course23FeePlustwo = $('#course23_fee_plustwo_amount');
        const $course23FeeSslc = $('#course23_fee_sslc_amount');
        const $paymentAmountInput = $('#modal_payment_amount'); // only exists for non-course23
        const $needMobileCheckbox = $('#need_mobile');
        const $assetIdFieldWrapper = $('#asset_id_field_wrapper');
        const $assetIdInput = $('#asset_id');
        const $assetIdRequiredMarker = $('#asset_id_required_marker');
        const mobileAddonAmount = 1000;
        let totalManuallyEdited = false;

        @if(!$course || !$course->title)
        // Hide payment section if no course is available
        $paymentCheckbox.closest('.card').hide();
        @endif

        function toNumber(value) {
            const numeric = parseFloat(String(value ?? '').replace(/[^\d.-]/g, ''));
            return Number.isFinite(numeric) ? numeric : 0;
        }

        function formatINR(amount) {
            const safe = Number.isFinite(amount) ? amount : 0;
            return '₹' + safe.toLocaleString('en-IN', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        }

        function getComputedTotalAmount() {
            let totalAmount = 0;
            const selectedOption = $batchSelect.find('option:selected');
            let batchAmount = 0;

            if (selectedOption.length && selectedOption.val()) {
                const amount = toNumber(selectedOption.data('amount'));
                const sslcAmount = toNumber(selectedOption.data('sslc-amount'));
                const plustwoAmount = toNumber(selectedOption.data('plustwo-amount'));
                const b2bAmount = toNumber(selectedOption.data('b2b-amount'));

                if (isB2BLead) {
                    batchAmount = b2bAmount;
                } else if (courseId == 16) {
                    if (studentClass === 'sslc' && sslcAmount > 0) {
                        batchAmount = sslcAmount;
                    } else if (plustwoAmount > 0) {
                        batchAmount = plustwoAmount;
                    } else {
                        batchAmount = amount;
                    }
                } else {
                    batchAmount = amount;
                }
            }

            if (isB2BLead) {
                totalAmount = batchAmount;
            } else {
                totalAmount = baseCourseAmount + batchAmount + baseUniversityAmount;
            }

            if ($needMobileCheckbox.is(':checked')) {
                totalAmount += mobileAddonAmount;
            }

            return totalAmount;
        }

        function getSelectedTotalAmount() {
            if (isCourse23 && $course23TotalInput.length) {
                return toNumber($course23TotalInput.val());
            }

            if (totalManuallyEdited && $customTotalInput.length) {
                return toNumber($customTotalInput.val());
            }

            return getComputedTotalAmount();
        }

        function toggleNeedMobileFields() {
            if ($needMobileCheckbox.is(':checked')) {
                $assetIdFieldWrapper.show();
                $assetIdInput.prop('required', true);
                $assetIdRequiredMarker.show();
            } else {
                $assetIdFieldWrapper.hide();
                $assetIdInput.prop('required', false).val('');
                $assetIdRequiredMarker.hide();
            }
            updateTotalAmount();
        }

        // Show/hide payment fields based on checkbox
        function togglePaymentFields() {
            if ($paymentCheckbox.is(':checked')) {
                $paymentFields.show();
                updateTotalAmount();

                // Make payment fields required
                $('#modal_payment_type').prop('required', true);
                $('#modal_transaction_id').prop('required', true);
                if (!isCourse23) {
                    $paymentAmountInput.prop('required', true);
                    $('#modal_payment_file').prop('required', true);
                }
                $('.payment-required').show();
            } else {
                $paymentFields.hide();

                // Remove required attribute and clear values
                $paymentAmountInput.prop('required', false).val('');
                $('#modal_payment_type').prop('required', false).val('');
                $('#modal_transaction_id').prop('required', false).val('');
                $('#modal_payment_file').prop('required', false).val('');
                $('#payment_pg_amount, #payment_ug_amount, #payment_plustwo_amount, #payment_sslc_amount').val('');
                $('#payment_pg_file, #payment_ug_file, #payment_plustwo_file, #payment_sslc_file').val('');
                $('.payment-required').hide();
            }
        }

        $paymentCheckbox.on('change click', function() {
            setTimeout(togglePaymentFields, 10);
        });
        $needMobileCheckbox.on('change', function() {
            if (!isCourse23) {
                totalManuallyEdited = false;
            }
            toggleNeedMobileFields();
        });

        // For course 23, keep payment total synced with entered total
        if (isCourse23) {
            $course23TotalInput.on('input', function() {
                totalManuallyEdited = true;
                updateTotalAmount();
            });

            // Auto-calc total from breakdown unless user has manually overridden it
            $course23FeePg.add($course23FeeUg).add($course23FeePlustwo).add($course23FeeSslc).on('input', function() {
                const sum = toNumber($course23FeePg.val()) + toNumber($course23FeeUg.val()) + toNumber($course23FeePlustwo.val()) + toNumber($course23FeeSslc.val());
                if (!totalManuallyEdited || !$course23TotalInput.val()) {
                    $course23TotalInput.val(sum.toFixed(2));
                }
                updateTotalAmount();
            });
        } else if ($customTotalInput.length) {
            $customTotalInput.on('input', function() {
                totalManuallyEdited = true;
                updateTotalAmount();
            });

            // Set max payment amount (single payment flow)
            $paymentAmountInput.on('input', function() {
                const totalAmount = getSelectedTotalAmount();
                if (toNumber($(this).val()) > totalAmount) {
                    $(this).val(totalAmount);
                }
            });
        }

        function updateTotalAmount() {
            @if($course && $course->title)
            let amount = getSelectedTotalAmount();

            if (!isCourse23 && $customTotalInput.length && (!totalManuallyEdited || !$customTotalInput.val())) {
                amount = getComputedTotalAmount();
                $customTotalInput.val(amount.toFixed(2));
            }

            if ($courseTotalPreview.length && !isCourse23) {
                $courseTotalPreview.text('Current total: ' + formatINR(amount));
            }

            if (!isCourse23 && $paymentAmountInput.length) {
                $paymentAmountInput.attr('max', amount);
                $paymentAmountInput.data('total-amount', amount);
            }
            @else
            // No course information available
            if ($customTotalInput.length) {
                $customTotalInput.val('');
            }
            @endif
        }


        // Initialize on page load
        updateTotalAmount();
        toggleNeedMobileFields();
        $batchSelect.on('change', function() {
            totalManuallyEdited = false;
            const selectedOption = $(this).find('option:selected');
            if ($batchAmountPreview.length && selectedOption.length && selectedOption.val()) {
                const title = selectedOption.text();
                const amount = toNumber(selectedOption.data('amount'));
                const sslcAmount = toNumber(selectedOption.data('sslc-amount'));
                const plustwoAmount = toNumber(selectedOption.data('plustwo-amount'));
                const b2bAmount = toNumber(selectedOption.data('b2b-amount'));

                let shownAmount = amount;
                let label = '';
                if (isB2BLead) {
                    shownAmount = b2bAmount;
                    label = 'B2B Amount';
                } else if (courseId == 16) {
                    if (studentClass === 'sslc' && sslcAmount > 0) {
                        shownAmount = sslcAmount;
                        label = 'SSLC Amount';
                    } else if (plustwoAmount > 0) {
                        shownAmount = plustwoAmount;
                        label = 'Plus Two Amount';
                    }
                }

                const labelHtml = label ? ` <span class="badge bg-primary ms-1">${label}</span>` : '';
                $batchAmountPreview.html(`<i class="fas fa-layer-group"></i> ${batchPreviewLabel}: <strong>${title}</strong> - ${formatINR(shownAmount)}${labelHtml}`);
            }
            updateTotalAmount();
        });

        $(document).off('click.convertLead', '#convertLeadBtn');
        $(document).on('click.convertLead', '#convertLeadBtn', function(e) {
            e.preventDefault();
            submitConvertForm();
        });

        // Form submission function
        function submitConvertForm() {
            if (convertSubmitting) {
                return;
            }

            const $btnText = $convertBtn.find('.btn-text');
            const $btnLoading = $convertBtn.find('.btn-loading');

            // Clear previous errors
            clearFormErrors();

            // Client-side validation
            if (!validateForm()) {
                return;
            }

            convertSubmitting = true;

            // Show loading state
            $convertBtn.prop('disabled', true);
            $btnText.hide();
            $btnLoading.show();

            // Submit form via AJAX using jQuery
            const formData = new FormData($form[0]);

            // Always submit computed total amount (includes mobile add-on when selected)
            formData.set('custom_total_amount', getSelectedTotalAmount());

            console.log('Submitting form to:', '{{ route("leads.convert.submit", $lead->id) }}');
            console.log('Form data:', Object.fromEntries(formData));


            $.ajax({
                url: '{{ route("leads.convert.submit", $lead->id) }}',
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    'X-Requested-With': 'XMLHttpRequest'
                },
                success: function(data) {
                    console.log('AJAX Success:', data);
                    if (data.success) {
                        // Show success message
                        showNotification('success', data.message || 'Lead converted successfully!');

                        // Close modal and refresh page
                        setTimeout(() => {
                            location.reload();
                        }, 1500);
                        return;
                    }

                    convertSubmitting = false;

                    // Handle validation errors
                    if (data.errors) {
                        displayFormErrors(data.errors);
                        showNotification('error', 'Please correct the errors below.');
                    } else {
                        showNotification('error', data.message || 'Failed to convert lead. Please try again.');
                    }

                    $convertBtn.prop('disabled', false);
                    $btnText.show();
                    $btnLoading.hide();
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error Details:');
                    console.error('Status:', xhr.status);
                    console.error('Status Text:', xhr.statusText);
                    console.error('Response Text:', xhr.responseText);
                    console.error('Error:', error);

                    convertSubmitting = false;

                    let errorMessage = 'An error occurred. Please try again.';

                    // Check if it's a validation error (422 status)
                    if (xhr.status === 422) {
                        try {
                            const data = JSON.parse(xhr.responseText);
                            console.log('Parsed validation response:', data);
                            if (data.errors) {
                                displayFormErrors(data.errors);
                                errorMessage = 'Please correct the errors below.';
                            } else {
                                errorMessage = data.message || 'Validation failed.';
                            }
                        } catch (e) {
                            console.error('Error parsing validation response:', e);
                            errorMessage = 'Validation failed. Please check your input.';
                        }
                    } else if (xhr.status === 409) {
                        try {
                            const data = JSON.parse(xhr.responseText);
                            errorMessage = data.message || 'This lead has already been converted.';
                        } catch (e) {
                            errorMessage = 'This lead has already been converted.';
                        }
                    }

                    showNotification('error', errorMessage);

                    $convertBtn.prop('disabled', false);
                    $btnText.show();
                    $btnLoading.hide();
                }
            });
        }

        // Display form validation errors
        function displayFormErrors(errors) {
            console.log('Displaying form errors:', errors);

            // Only proceed if there are actual errors
            if (!errors || Object.keys(errors).length === 0) {
                console.log('No errors to display');
                return;
            }

            // Clear previous errors
            $('.field-error').remove();
            $('.form-control').removeClass('is-invalid');

            // Display errors for each field
            $.each(errors, function(field, messages) {
                // Map field names to modal IDs
                const modalFieldMap = {};

                const fieldId = modalFieldMap[field] || field;
                let fieldElement = $(`#${fieldId}`);
                if (fieldElement.length === 0) {
                    fieldElement = $(`[name="${field}"]`).first();
                }

                const fieldValue = fieldElement.val();

                if (fieldValue && fieldValue.trim() !== '' && fieldValue !== '0') {
                    return; // Skip this field as it has a value
                }

                // Use the field element we found
                let fieldElementToUse = fieldElement;

                if (fieldElementToUse.length) {
                    // Add error class to field
                    fieldElementToUse.addClass('is-invalid');
                    // Add error message below field
                    const errorHtml = `<div class="field-error text-danger small mt-1">${messages[0]}</div>`;

                    // Try to find the container
                    let container = fieldElementToUse.closest('.p-1');
                    if (container.length === 0) {
                        container = fieldElementToUse.closest('.form-group');
                    }
                    if (container.length === 0) {
                        container = fieldElementToUse.closest('.col-lg-6, .col-lg-12, .col-12');
                    }

                    if (container.length) {
                        container.append(errorHtml);
                    } else {
                        // Fallback: append after the field element
                        fieldElementToUse.after(errorHtml);
                    }
                } else {
                    // Try to show error in a general location
                    const errorHtml = `<div class="field-error text-danger small mt-1">${field}: ${messages[0]}</div>`;
                    $('.card-body').first().append(errorHtml);
                }
            });
        }

        // Client-side form validation
        function validateForm() {
            let isValid = true;
            const errors = {};


            // Check required fields
            const requiredFields = ['name', 'code', 'phone'];
            if (!isEdumasterCourse) {
                requiredFields.push('batch_id');
            }

            requiredFields.forEach(function(field) {
                // Map field names to modal IDs
                const modalFieldMap = {};

                const fieldId = modalFieldMap[field] || field;
                let fieldElement = $(`#${fieldId}`);
                if (fieldElement.length === 0) {
                    fieldElement = $(`[name="${field}"]`).first();
                }

                const fieldValue = fieldElement.val();

                // Check if field is empty or has no value selected
                if (!fieldValue || fieldValue.trim() === '' || fieldValue === '0') {
                    const label = field === 'batch_id' ? batchPreviewLabel.toLowerCase() : field.replace('_', ' ');
                    errors[field] = [`The ${label} field is required.`];
                    isValid = false;
                }
            });

            // Check payment fields if payment collected is checked
            if ($paymentCheckbox.is(':checked')) {
                if (!$('#modal_payment_type').val()) {
                    errors['payment_type'] = ['The payment type field is required.'];
                    isValid = false;
                }

                if (!$('#modal_transaction_id').val()) {
                    errors['transaction_id'] = ['The transaction id field is required.'];
                    isValid = false;
                }

                if (isCourse23) {
                    const pgPaid = toNumber($('#payment_pg_amount').val());
                    const ugPaid = toNumber($('#payment_ug_amount').val());
                    const plustwoPaid = toNumber($('#payment_plustwo_amount').val());
                    const sslcPaid = toNumber($('#payment_sslc_amount').val());

                    const totalPaid = pgPaid + ugPaid + plustwoPaid + sslcPaid;
                    const totalAmount = getSelectedTotalAmount();

                    if (totalPaid <= 0) {
                        errors['payment_pg_amount'] = ['At least one paid amount (PG/UG/Plus Two/SSLC) is required.'];
                        isValid = false;
                    }

                    if (totalAmount > 0 && totalPaid > totalAmount) {
                        errors['custom_total_amount'] = ['Total paid amount cannot exceed the total amount.'];
                        isValid = false;
                    }

                    if (pgPaid > 0 && !$('#payment_pg_file').val()) {
                        errors['payment_pg_file'] = ['PG payment proof is required when PG paid amount is entered.'];
                        isValid = false;
                    }
                    if (ugPaid > 0 && !$('#payment_ug_file').val()) {
                        errors['payment_ug_file'] = ['UG payment proof is required when UG paid amount is entered.'];
                        isValid = false;
                    }
                    if (plustwoPaid > 0 && !$('#payment_plustwo_file').val()) {
                        errors['payment_plustwo_file'] = ['Plus Two payment proof is required when Plus Two paid amount is entered.'];
                        isValid = false;
                    }
                    if (sslcPaid > 0 && !$('#payment_sslc_file').val()) {
                        errors['payment_sslc_file'] = ['SSLC payment proof is required when SSLC paid amount is entered.'];
                        isValid = false;
                    }
                } else {
                    const paymentAmount = toNumber($paymentAmountInput.val());
                    const totalAmount = getSelectedTotalAmount();

                    if (!$paymentAmountInput.val() || paymentAmount <= 0) {
                        errors['payment_amount'] = ['The payment amount field is required and must be greater than 0.'];
                        isValid = false;
                    }

                    if (totalAmount > 0 && paymentAmount > totalAmount) {
                        errors['payment_amount'] = ['Payment amount cannot exceed the total amount.'];
                        isValid = false;
                    }

                    if (!$('#modal_payment_file').val()) {
                        errors['payment_file'] = ['The payment file field is required.'];
                        isValid = false;
                    }
                }
            }

            if ($needMobileCheckbox.is(':checked') && !$assetIdInput.val().trim()) {
                errors['asset_id'] = ['The asset id field is required when Need Mobile is checked.'];
                isValid = false;
            }

            // Only display errors if there are actual validation errors
            if (!isValid && Object.keys(errors).length > 0) {
                displayFormErrors(errors);
                showNotification('error', 'Please correct the errors below.');
            }

            return isValid;
        }

        // Clear form errors
        function clearFormErrors() {
            $('.field-error').remove();
            $('.form-control').removeClass('is-invalid');
        }

        // Clear errors on input change
        $('input, select, textarea').on('input change', function() {
            $(this).removeClass('is-invalid');
            $(this).closest('.p-1').find('.field-error').remove();

            // If field now has a value, clear any validation errors
            const fieldValue = $(this).val();
            if (fieldValue && fieldValue.trim() !== '' && fieldValue !== '0') {
                console.log(`Field ${$(this).attr('name')} now has value, clearing errors`);
            }
        });

        // Function to check if field should show validation error
        function shouldShowFieldError(fieldName, fieldValue) {
            // Don't show error if field has a valid value
            if (fieldValue && fieldValue.trim() !== '' && fieldValue !== '0') {
                return false;
            }
            return true;
        }

        // Notification function
        function showNotification(type, message) {
            // Use the project's toast notification system
            if (typeof showToast === 'function') {
                showToast(message, type);
            } else {
                // Fallback to alert if toast is not available
                if (type === 'success') {
                    console.log('Success: ' + message);
                } else {
                    console.log('Error: ' + message);
                }
            }
        }
    });

    // Test function for debugging
    function testFunctionality() {
        console.log('=== TESTING FUNCTIONALITY ===');

        // Test checkbox
        const $checkbox = $('#payment_collected');
        console.log('Checkbox found:', $checkbox.length > 0);
        console.log('Checkbox checked:', $checkbox.is(':checked'));

        // Test payment fields
        const $paymentFields = $('#payment_fields');
        console.log('Payment fields found:', $paymentFields.length > 0);
        console.log('Payment fields visible:', $paymentFields.is(':visible'));


        // Test checkbox toggle
        if ($checkbox.length) {
            $checkbox.prop('checked', !$checkbox.is(':checked'));
            console.log('Checkbox toggled to:', $checkbox.is(':checked'));
            if ($paymentFields.length) {
                $paymentFields.toggle();
                console.log('Payment fields display set to:', $paymentFields.is(':visible'));
            }
        }
    }
</script>