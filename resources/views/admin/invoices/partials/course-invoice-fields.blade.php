@php
    $ctx = $courseFeeContext;
    $course = $ctx['course'];
    $batchLabel = $ctx['usePlanLabelsForBatch'] ? 'Plan' : 'Batch';
    $selectedCourseId = old('course_id') !== null && old('course_id') !== '' ? (int) old('course_id') : 0;
    $selectedBatchId = old('batch_id');
@endphp

<div class="col-12" id="course_invoice_validation_wrap" style="display:none;">
    <div class="alert alert-warning mb-0" id="course_invoice_validation_alert" role="alert"></div>
</div>

<div class="col-md-6" id="course_selection">
    <div class="mb-3">
        <label for="course_id" class="form-label">Course <span class="text-danger">*</span></label>
        <select class="form-control @error('course_id') is-invalid @enderror" name="course_id" id="course_id">
            <option value="">Select Course</option>
            @foreach($courses as $courseOption)
                <option value="{{ $courseOption->id }}"
                    data-title="{{ $courseOption->title }}"
                    data-amount="{{ (float) ($courseOption->amount ?? 0) }}"
                    {{ (int) $selectedCourseId === (int) $courseOption->id ? 'selected' : '' }}>
                    {{ $courseOption->title }}
                </option>
            @endforeach
        </select>
        @error('course_id')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="col-md-6" id="course_batch_selection" style="{{ (!$course || $ctx['isEdumasterCourse']) ? 'display:none' : '' }}">
    <div class="mb-3">
        <label for="course_batch_id" class="form-label">{{ $batchLabel }} <span class="text-danger batch-required-marker">*</span></label>
        <select class="form-control @error('batch_id') is-invalid @enderror" name="batch_id" id="course_batch_id">
            <option value="">Select {{ $batchLabel }}</option>
            @foreach($ctx['batches'] as $batchOption)
                <option value="{{ $batchOption->id }}"
                    data-amount="{{ (float) ($batchOption->amount ?? 0) }}"
                    data-sslc-amount="{{ (float) ($batchOption->sslc_amount ?? 0) }}"
                    data-plustwo-amount="{{ (float) ($batchOption->plustwo_amount ?? 0) }}"
                    data-b2b-amount="{{ (float) ($batchOption->b2b_amount ?? 0) }}"
                    {{ (string) $selectedBatchId === (string) $batchOption->id ? 'selected' : '' }}>
                    {{ $batchOption->title }}@if(!empty($ctx['useB2bBatchAmount']) && $batchOption->b2b_amount !== null)
                         — ₹{{ number_format((float) $batchOption->b2b_amount, 2) }} (B2B)@elseif($batchOption->amount !== null)
                         — ₹{{ number_format((float) $batchOption->amount, 2) }}@endif{{ (int) ($batchOption->is_active ?? 0) === 0 ? ' (Inactive)' : '' }}
                </option>
            @endforeach
        </select>
        @error('batch_id')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="col-12" id="course_fee_preview_section">
    <div class="mb-3">
        <label class="form-label">Course Fee Details</label>

        <div class="bg-light p-3 rounded" id="edumaster_fee_breakdown" style="{{ ($course && $ctx['isEdumasterCourse']) ? '' : 'display:none' }}">
            <div class="mb-2">
                <strong id="edumaster_course_title">{{ $course?->title }}</strong>
                <small class="text-muted d-block">Enter the fee breakdown (same as convert form).</small>
            </div>
            <div class="row g-3">
                <div class="col-md-4">
                    <label for="custom_total_amount" class="form-label">Total Amount</label>
                    <input type="number" class="form-control" name="custom_total_amount" id="custom_total_amount" step="0.01" min="0"
                        value="{{ old('custom_total_amount') }}">
                </div>
                <div class="col-md-4">
                    <label for="fee_pg_amount" class="form-label">PG Amount</label>
                    <input type="number" class="form-control" name="fee_pg_amount" id="fee_pg_amount" step="0.01" min="0" value="{{ old('fee_pg_amount') }}">
                </div>
                <div class="col-md-4">
                    <label for="fee_ug_amount" class="form-label">UG Amount</label>
                    <input type="number" class="form-control" name="fee_ug_amount" id="fee_ug_amount" step="0.01" min="0" value="{{ old('fee_ug_amount') }}">
                </div>
                <div class="col-md-4">
                    <label for="fee_plustwo_amount" class="form-label">Plus Two Amount</label>
                    <input type="number" class="form-control" name="fee_plustwo_amount" id="fee_plustwo_amount" step="0.01" min="0" value="{{ old('fee_plustwo_amount') }}">
                </div>
                <div class="col-md-4">
                    <label for="fee_sslc_amount" class="form-label">SSLC Amount</label>
                    <input type="number" class="form-control" name="fee_sslc_amount" id="fee_sslc_amount" step="0.01" min="0" value="{{ old('fee_sslc_amount') }}">
                </div>
            </div>
        </div>

        <div class="form-control-plaintext bg-light p-2 rounded" id="standard_course_fee_preview" style="{{ ($ctx['isEdumasterCourse'] || !$course) ? 'display:none' : '' }}">
            <strong id="preview_course_title">{{ $course?->title ?? 'Select a course' }}</strong>
            <span id="preview_course_amount_line">
                @if($course)
                    - ₹{{ number_format($ctx['courseAmount'], 2) }}
                @endif
            </span>

            <span id="preview_university_block" style="display:none;"></span>
            <span id="preview_class_block" style="display:none;"></span>

            <br><small class="text-info" id="batch_amount_preview" style="{{ $course ? '' : 'display:none' }}">
                <i class="ti ti-layers-intersect"></i> {{ $batchLabel }}: <strong>{{ $ctx['batch']?->title ?? '-' }}</strong>
                @if($course && $ctx['batch'])
                    - ₹{{ number_format($ctx['batchAmount'], 2) }}
                    @if($ctx['batchAmountLabel'])
                        <span class="badge bg-primary ms-1">{{ $ctx['batchAmountLabel'] }}</span>
                    @endif
                @endif
            </small>

            <br><strong class="text-primary" id="course_total_preview" style="{{ $course ? '' : 'display:none' }}">
                @if($course)
                    Total: ₹{{ number_format($ctx['totalAmount'], 2) }}
                @endif
            </strong>
        </div>

        <p class="text-muted mb-0" id="course_fee_empty_hint" style="{{ $course ? 'display:none' : '' }}">Select a course and batch to calculate fees.</p>
    </div>
</div>
