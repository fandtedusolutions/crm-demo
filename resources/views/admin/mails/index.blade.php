@extends('layouts.mantis')

@section('title', 'Mail')

@section('content')
<!-- [ breadcrumb ] start -->
<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-6">
                <div class="page-header-title">
                    <h5 class="m-b-10">Mail Management</h5>
                </div>
            </div>
            <div class="col-md-6">
                <ul class="breadcrumb d-flex justify-content-end">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item">Mail</li>
                </ul>
            </div>
        </div>
    </div>
</div>
<!-- [ breadcrumb ] end -->

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Mail List</h5>
                    <a href="javascript:void(0);" class="btn btn-primary btn-sm px-3"
                        onclick="show_large_modal('{{ route('admin.mails.add') }}', 'Add Mail')">
                        <i class="ti ti-plus"></i> Add New
                    </a>
                </div>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('admin.mails.index') }}" id="mailsFilterForm" class="mb-4">
                    <div class="row g-2 align-items-end">
                        <div class="col-md-3 col-lg-3">
                            <label for="filter_course_id" class="form-label">Course</label>
                            <select class="form-select form-select-sm" name="course_id" id="filter_course_id">
                                <option value="">All Courses</option>
                                @foreach($courses as $course)
                                    <option value="{{ $course->id }}" {{ (int) ($selectedCourseId ?? 0) === (int) $course->id ? 'selected' : '' }}>
                                        {{ $course->title }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3 col-lg-3">
                            <label for="filter_batch_id" class="form-label">Batch</label>
                            <select class="form-select form-select-sm" name="batch_id" id="filter_batch_id"
                                {{ $selectedCourseId ? '' : 'disabled' }}>
                                <option value="">All Batches</option>
                                @foreach($batches as $batch)
                                    <option value="{{ $batch->id }}" {{ (int) ($selectedBatchId ?? 0) === (int) $batch->id ? 'selected' : '' }}>
                                        {{ $batch->title }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3 col-lg-3">
                            <label for="filter_admission_batch_id" class="form-label">Admission Batch</label>
                            <select class="form-select form-select-sm" name="admission_batch_id" id="filter_admission_batch_id"
                                {{ $selectedBatchId ? '' : 'disabled' }}>
                                <option value="all" {{ ($selectedAdmissionBatchId ?? '') === 'all' ? 'selected' : '' }}>
                                    All Admission Batches
                                </option>
                                @foreach($admissionBatches as $admissionBatch)
                                    <option value="{{ $admissionBatch->id }}" {{ (int) ($selectedAdmissionBatchId ?? 0) === (int) $admissionBatch->id ? 'selected' : '' }}>
                                        {{ $admissionBatch->title }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-auto">
                            <button type="submit" class="btn btn-primary btn-sm">
                                <i class="ti ti-filter me-1"></i> Filter
                            </button>
                        </div>
                        @if($hasActiveFilters ?? false)
                        <div class="col-md-auto">
                            <a href="{{ route('admin.mails.index') }}" class="btn btn-outline-secondary btn-sm">
                                <i class="ti ti-x me-1"></i> Clear
                            </a>
                        </div>
                        @endif
                    </div>
                </form>

                @if($hasActiveFilters ?? false)
                    <p class="text-muted small mb-3">
                        Showing {{ $mails->count() }} {{ $mails->count() === 1 ? 'mail' : 'mails' }} matching filters.
                    </p>
                @endif

                <div class="table-responsive">
                    <table class="table table-striped datatable">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Course</th>
                                <th>Batch</th>
                                <th>Admission Batch</th>
                                <th>Content</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($mails as $mail)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $mail->course?->title ?? '-' }}</td>
                                <td>{{ $mail->batch?->title ?? '-' }}</td>
                                <td>{{ $mail->admission_batch_id ? ($mail->admissionBatch?->title ?? '-') : 'All Admission Batches' }}</td>
                                <td>
                                    <span class="text-muted">{{ Str::limit(strip_tags($mail->content), 80) }}</span>
                                </td>
                                <td>
                                    <a href="javascript:void(0);" class="btn btn-warning btn-sm shadow-sm px-3"
                                        onclick="show_large_modal('{{ route('admin.mails.edit', $mail->id) }}', 'Edit Mail')"
                                        title="Edit">
                                        <i class="ti ti-edit"></i> Edit
                                    </a>
                                    @if(can_delete_subject_areas_mails_flags())
                                    <a href="javascript:void(0);" class="btn btn-danger btn-sm shadow-sm px-3"
                                        onclick="delete_modal('{{ route('admin.mails.delete', $mail->id) }}')" title="Delete">
                                        <i class="ti ti-trash"></i> Delete
                                    </a>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">
                                    @if($hasActiveFilters ?? false)
                                        No mails found for the selected filters.
                                    @else
                                        No mails found.
                                    @endif
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function() {
    const $course = $('#filter_course_id');
    const $batch = $('#filter_batch_id');
    const $admission = $('#filter_admission_batch_id');
    const selectedBatchId = @json($selectedBatchId ?? null);
    const selectedAdmissionBatchId = @json($selectedAdmissionBatchId ?? null);

    function loadFilterBatches(courseId, keepBatchId, done) {
        $batch.html('<option value="">All Batches</option>');
        $admission.html('<option value="all">All Admission Batches</option>')
            .prop('disabled', true);

        if (!courseId) {
            $batch.prop('disabled', true);
            if (typeof done === 'function') done();
            return;
        }

        $.get(`/api/batches/by-course/${courseId}`).done(function(response) {
            if (response.success && response.batches) {
                response.batches.forEach(function(b) {
                    const sel = keepBatchId && String(keepBatchId) === String(b.id) ? 'selected' : '';
                    $batch.append(`<option value="${b.id}" ${sel}>${b.title}</option>`);
                });
            }
            $batch.prop('disabled', false);
        }).always(function() {
            if (typeof done === 'function') done();
        });
    }

    function loadFilterAdmissionBatches(batchId, keepAdmissionId) {
        $admission.html('<option value="all">All Admission Batches</option>');

        if (!batchId) {
            $admission.prop('disabled', true);
            return;
        }

        $.get(`/api/admission-batches/by-batch/${batchId}`).done(function(list) {
            list.forEach(function(i) {
                const sel = keepAdmissionId && String(keepAdmissionId) === String(i.id) ? 'selected' : '';
                $admission.append(`<option value="${i.id}" ${sel}>${i.title}</option>`);
            });
            if (keepAdmissionId === 'all') {
                $admission.find('option[value="all"]').prop('selected', true);
            }
            $admission.prop('disabled', false);
        });
    }

    $course.on('change', function() {
        const courseId = $(this).val();
        loadFilterBatches(courseId, '');
    });

    $batch.on('change', function() {
        loadFilterAdmissionBatches($(this).val(), '');
    });

    if (!$course.val()) {
        $batch.prop('disabled', true);
        $admission.prop('disabled', true);
    } else if (!$batch.val()) {
        $admission.prop('disabled', true);
    }
})();
</script>
@endpush
