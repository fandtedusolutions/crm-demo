@extends('layouts.mantis')

@section('title', 'Post-sales Management')

@section('content')
<!-- [ breadcrumb ] start -->
<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-6">
                <div class="page-header-title">
                    <h5 class="m-b-10">Post-sales Management</h5>
                </div>
            </div>
            <div class="col-md-6">
                <ul class="breadcrumb d-flex justify-content-end">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item">User Management</li>
                    <li class="breadcrumb-item">Post-sales</li>
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
                    <h5 class="mb-0">Post-sales Users List</h5>
                    <a href="javascript:void(0);" class="btn btn-primary btn-sm px-3"
                        onclick="show_small_modal('{{ route('admin.post-sales.add') }}', 'Add Post-sales User')">
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
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Is Head</th>
                                <th>Post-sale GM</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($postSalesUsers as $index => $postSalesUser)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $postSalesUser->name }}</td>
                                <td>{{ $postSalesUser->email }}</td>
                                <td>{{ $postSalesUser->phone ?? '-' }}</td>
                                <td>
                                    @if($postSalesUser->is_head)
                                        <span class="badge bg-primary">Head</span>
                                    @else
                                        <span class="badge bg-secondary">Member</span>
                                    @endif
                                </td>
                                <td>
                                    @if($postSalesUser->is_postsale_gm)
                                        <span class="badge bg-dark">Post-sale GM</span>
                                    @else
                                        <span class="badge bg-light text-dark">No</span>
                                    @endif
                                </td>
                                <td>
                                    @if($postSalesUser->is_active)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-danger">Inactive</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="javascript:void(0);" class="btn btn-warning btn-sm shadow-sm px-3"
                                        onclick="show_small_modal('{{ route('admin.post-sales.edit', $postSalesUser->id) }}', 'Edit Post-sales User')"
                                        title="Edit">
                                        <i class="ti ti-edit"></i> Edit
                                    </a>
                                    <a href="javascript:void(0);" class="btn btn-info btn-sm shadow-sm px-3"
                                        onclick="show_small_modal('{{ route('admin.post-sales.change-password', $postSalesUser->id) }}', 'Change Password')" title="Change Password">
                                        <i class="ti ti-key"></i> Password
                                    </a>
                                    <a href="javascript:void(0);" class="btn btn-danger btn-sm shadow-sm px-3"
                                        onclick="delete_modal('{{ route('admin.post-sales.delete', $postSalesUser->id) }}')" title="Delete">
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
<!-- [ Main Content ] end -->

@endsection

@push('scripts')
<script>
// DataTable is now initialized globally in footer-scripts.blade.php
// No need for duplicate initialization here
</script>
@endpush
