@extends('layouts.mantis')

@section('title', 'Academic Delivery Structure Management')

@section('content')
<!-- [ breadcrumb ] start -->
<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-6">
                <div class="page-header-title">
                    <h5 class="m-b-10">Academic Delivery Structure</h5>
                </div>
            </div>
            <div class="col-md-6">
                <ul class="breadcrumb d-flex justify-content-end">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item">Master Data</li>
                    <li class="breadcrumb-item">Academic Delivery Structure</li>
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
                    <h5 class="mb-0">Academic Delivery Structure List</h5>
                    @if(has_permission('admin/academic-delivery-structures/index') || \App\Helpers\RoleHelper::is_academic_counselor())
                    <a href="javascript:void(0);" class="btn btn-primary btn-sm px-3"
                        onclick="show_small_modal('{{ route('admin.academic-delivery-structures.add') }}', 'Add Academic Delivery Structure')">
                        <i class="ti ti-plus"></i> Add New
                    </a>
                    @endif
                </div>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('admin.academic-delivery-structures.index') }}" class="mb-4">
                    <div class="row g-2 align-items-end">
                        <div class="col-md-4 col-lg-3">
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
                        <div class="col-md-auto">
                            <button type="submit" class="btn btn-primary btn-sm">
                                <i class="ti ti-filter me-1"></i> Filter
                            </button>
                        </div>
                        @if($selectedCourseId ?? false)
                        <div class="col-md-auto">
                            <a href="{{ route('admin.academic-delivery-structures.index') }}" class="btn btn-outline-secondary btn-sm">
                                <i class="ti ti-x me-1"></i> Clear
                            </a>
                        </div>
                        @endif
                    </div>
                </form>

                @if($selectedCourseId ?? false)
                    @php
                        $filteredCourse = $courses->firstWhere('id', $selectedCourseId);
                    @endphp
                    <p class="text-muted small mb-3">
                        Showing structures for <strong>{{ $filteredCourse?->title ?? 'selected course' }}</strong>
                        ({{ $academicDeliveryStructures->count() }} {{ $academicDeliveryStructures->count() === 1 ? 'record' : 'records' }})
                    </p>
                @endif

                <div class="table-responsive">
                    <table class="table table-striped datatable">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Title</th>
                                <th>Course</th>
                                <th>Descriptions</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($academicDeliveryStructures as $structure)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $structure->title }}</td>
                                <td>{{ $structure->course ? $structure->course->title : '-' }}</td>
                                <td>
                                    @php $descriptions = $structure->descriptions ?? []; @endphp
                                    @if(count($descriptions) > 0)
                                        <ul class="mb-0 ps-3 list-unstyled">
                                            @foreach($descriptions as $desc)
                                                <li class="mb-1">• {{ $desc }}</li>
                                            @endforeach
                                        </ul>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="javascript:void(0);" class="btn btn-info btn-sm shadow-sm px-3 me-1"
                                        onclick="show_small_modal('{{ route('admin.academic-delivery-structures.view', $structure->id) }}', 'View Academic Delivery Structure')"
                                        title="View">
                                        <i class="ti ti-eye"></i> View
                                    </a>
                                    @if(has_permission('admin/academic-delivery-structures/index') || \App\Helpers\RoleHelper::is_academic_counselor())
                                    <a href="javascript:void(0);" class="btn btn-warning btn-sm shadow-sm px-3 me-1"
                                        onclick="show_small_modal('{{ route('admin.academic-delivery-structures.edit', $structure->id) }}', 'Edit Academic Delivery Structure')"
                                        title="Edit">
                                        <i class="ti ti-edit"></i> Edit
                                    </a>
                                    <a href="javascript:void(0);" class="btn btn-danger btn-sm shadow-sm px-3"
                                        onclick="delete_modal('{{ route('admin.academic-delivery-structures.delete', $structure->id) }}')" title="Delete">
                                        <i class="ti ti-trash"></i> Delete
                                    </a>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">
                                    @if($selectedCourseId ?? false)
                                        No academic delivery structures found for the selected course.
                                    @else
                                        No academic delivery structures found.
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
