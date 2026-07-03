@extends('layouts.mantis')

@section('title', 'Class Times Management')

@section('content')
<!-- [ breadcrumb ] start -->
<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-6">
                <div class="page-header-title">
                    <h5 class="m-b-10">Class Times Management</h5>
                </div>
            </div>
            <div class="col-md-6">
                <ul class="breadcrumb d-flex justify-content-end">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item">Class Times</li>
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
                    <h5 class="mb-0">Class Times List</h5>
                    <a href="javascript:void(0);" class="btn btn-primary btn-sm px-3"
                        onclick="show_small_modal('{{ route('admin.class-times.add') }}', 'Add Class Time')">
                        <i class="ti ti-plus"></i> Add New
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped datatable">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Course</th>
                                <th>Class Type</th>
                                <th>From Time</th>
                                <th>To Time</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($classTimes as $index => $classTime)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $classTime->course->title }}</td>
                                <td>
                                    <span class="badge {{ $classTime->class_type == 'online' ? 'bg-info' : 'bg-warning' }}">
                                        {{ ucfirst($classTime->class_type ?? 'online') }}
                                    </span>
                                </td>
                                <td>{{ $classTime->from_time ? date('h:i A', strtotime($classTime->from_time)) : '-' }}</td>
                                <td>{{ $classTime->to_time ? date('h:i A', strtotime($classTime->to_time)) : '-' }}</td>
                                <td>
                                    <span class="badge {{ $classTime->is_active ? 'bg-success' : 'bg-danger' }}">
                                        {{ $classTime->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td>
                                    <a href="javascript:void(0);" class="btn btn-warning btn-sm shadow-sm px-3"
                                        onclick="show_small_modal('{{ route('admin.class-times.edit', $classTime->id) }}', 'Edit Class Time')"
                                        title="Edit">
                                        <i class="ti ti-edit"></i> Edit
                                    </a>
                                    @if(is_super_admin() || is_admission_counsellor())
                                    <a href="javascript:void(0);" class="btn btn-danger btn-sm shadow-sm px-3"
                                        onclick="delete_modal('{{ route('admin.class-times.delete', $classTime->id) }}')" title="Delete">
                                        <i class="ti ti-trash"></i> Delete
                                    </a>
                                    @endif
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
<!-- [ Main Content ] end -->
@endsection

@push('scripts')
<script>
// DataTable is now initialized globally in footer-scripts.blade.php
// No need for duplicate initialization here
</script>
@endpush

