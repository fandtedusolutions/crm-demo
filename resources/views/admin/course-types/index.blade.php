@extends('layouts.mantis')

@section('title', 'Course Types Management')

@section('content')
<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-6">
                <div class="page-header-title">
                    <h5 class="m-b-10">Course Types Management</h5>
                </div>
            </div>
            <div class="col-md-6">
                <ul class="breadcrumb d-flex justify-content-end">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item">Settings</li>
                    <li class="breadcrumb-item">Course Types</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Course Types List</h5>
                    <a href="javascript:void(0);" class="btn btn-primary btn-sm px-3"
                        onclick="show_small_modal('{{ route('admin.course-types.add') }}', 'Add Course Type')">
                        <i class="ti ti-plus"></i> Add New
                    </a>
                </div>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('admin.course-types.index') }}" class="mb-4">
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
                        @if($selectedCourseId)
                        <div class="col-md-auto">
                            <a href="{{ route('admin.course-types.index') }}" class="btn btn-outline-secondary btn-sm">
                                <i class="ti ti-x me-1"></i> Clear
                            </a>
                        </div>
                        @endif
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-striped datatable">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Title</th>
                                <th>Course</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($courseTypes as $courseType)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $courseType->title }}</td>
                                <td>{{ $courseType->course?->title ?? '-' }}</td>
                                <td>
                                    <span class="badge {{ $courseType->is_active ? 'bg-success' : 'bg-danger' }}">
                                        {{ $courseType->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td>
                                    <a href="javascript:void(0);" class="btn btn-warning btn-sm shadow-sm px-3"
                                        onclick="show_small_modal('{{ route('admin.course-types.edit', $courseType->id) }}', 'Edit Course Type')"
                                        title="Edit">
                                        <i class="ti ti-edit"></i> Edit
                                    </a>
                                    <a href="javascript:void(0);" class="btn btn-danger btn-sm shadow-sm px-3"
                                        onclick="delete_modal('{{ route('admin.course-types.delete', $courseType->id) }}')" title="Delete">
                                        <i class="ti ti-trash"></i> Delete
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
