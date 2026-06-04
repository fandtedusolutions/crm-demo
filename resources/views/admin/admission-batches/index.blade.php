@extends('layouts.mantis')

@section('title', 'Admission Batch Management')

@section('content')
<!-- [ breadcrumb ] start -->
<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-6">
                <div class="page-header-title">
                    <h5 class="m-b-10">Admission Batch Management</h5>
                </div>
            </div>
            <div class="col-md-6">
                <ul class="breadcrumb d-flex justify-content-end">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item">Settings</li>
                    <li class="breadcrumb-item">Admission Batches</li>
                </ul>
            </div>
        </div>
    </div>
</div>
<!-- [ breadcrumb ] end -->

<!-- [ Main Content ] start -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Admission Batch List</h5>
                    <a href="javascript:void(0);" class="btn btn-primary btn-sm px-3"
                        onclick="show_small_modal('{{ route('admin.admission-batches.add') }}', 'Add Admission Batch')">
                        <i class="ti ti-plus"></i> Add New
                    </a>
                </div>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('admin.admission-batches.index') }}" class="mb-4">
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
                        <div class="col-md-auto">
                            <button type="submit" class="btn btn-primary btn-sm">
                                <i class="ti ti-filter me-1"></i> Filter
                            </button>
                        </div>
                        @if($hasActiveFilters ?? false)
                        <div class="col-md-auto">
                            <a href="{{ route('admin.admission-batches.index') }}" class="btn btn-outline-secondary btn-sm">
                                <i class="ti ti-x me-1"></i> Clear
                            </a>
                        </div>
                        @endif
                    </div>
                </form>

                @if($hasActiveFilters ?? false)
                    <p class="text-muted small mb-3">
                        Showing {{ $admissionBatches->count() }} {{ $admissionBatches->count() === 1 ? 'admission batch' : 'admission batches' }} matching filters.
                    </p>
                @endif

                <div class="table-responsive">
                    <table class="table table-striped datatable">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Title</th>
                                <th>Course - Batch</th>
                                <th>Mentor</th>
                                <th>Description</th>
                                <th>Status</th>
                                <th>Created At</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($admissionBatches as $index => $admissionBatch)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $admissionBatch->title }}</td>
                                <td>
                                    @php($batch = $admissionBatch->batch)
                                    {{ $batch && $batch->course ? ($batch->course->title . ' - ') : '' }}{{ $batch->title ?? 'N/A' }}
                                </td>
                                <td>{{ $admissionBatch->mentor->name ?? 'No Mentor Assigned' }}</td>
                                <td>{{ $admissionBatch->description ?? 'N/A' }}</td>
                                <td>
                                    @if($admissionBatch->is_active)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-danger">Inactive</span>
                                    @endif
                                </td>
                                <td>{{ $admissionBatch->created_at->format('M d, Y') }}</td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="javascript:void(0);" class="btn btn-sm btn-info"
                                            onclick="show_small_modal('{{ route('admin.admission-batches.edit', $admissionBatch->id) }}', 'Edit Admission Batch')">
                                            <i class="ti ti-edit"></i>
                                        </a>
                                        <a href="javascript:void(0);" class="btn btn-sm btn-danger"
                                            onclick="delete_modal('{{ route('admin.admission-batches.destroy', $admissionBatch->id) }}')" title="Delete">
                                            <i class="ti ti-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">
                                    @if($hasActiveFilters ?? false)
                                        No admission batches found for the selected filters.
                                    @else
                                        No admission batches found.
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
<!-- [ Main Content ] end -->
@endsection

@push('scripts')
<script>
(function() {
    const $course = $('#filter_course_id');
    const $batch = $('#filter_batch_id');

    function loadFilterBatches(courseId, keepBatchId) {
        $batch.html('<option value="">All Batches</option>');

        if (!courseId) {
            $batch.prop('disabled', true);
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
        });
    }

    $course.on('change', function() {
        loadFilterBatches($(this).val(), '');
    });

    if (!$course.val()) {
        $batch.prop('disabled', true);
    }
})();
</script>
@endpush
