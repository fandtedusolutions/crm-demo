@extends('layouts.mantis')

@section('title', 'Addon Courses')

@section('content')
<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-6">
                <div class="page-header-title">
                    <h5 class="m-b-10">Addon Courses</h5>
                </div>
            </div>
            <div class="col-md-6">
                <ul class="breadcrumb d-flex justify-content-end">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item">Addon Courses</li>
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
                    <h5 class="mb-0">Addon Course List</h5>
                    <a href="javascript:void(0);" class="btn btn-primary btn-sm px-3"
                        onclick="show_small_modal('{{ route('admin.addon-courses.add') }}', 'Add Addon Course')">
                        <i class="ti ti-plus"></i> Add New
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover datatable" id="addonCoursesTable">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Course</th>
                                <th>Addon Course</th>
                                <th>Status</th>
                                <th>Created At</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($addonCourses as $addonCourse)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $addonCourse->course->title ?? '—' }}</td>
                                <td>{{ $addonCourse->addonCourse->title ?? '—' }}</td>
                                <td>
                                    <span class="badge {{ $addonCourse->is_active ? 'bg-success' : 'bg-danger' }}">
                                        {{ $addonCourse->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td>{{ optional($addonCourse->created_at)->format('d-m-Y') }}</td>
                                <td class="text-end">
                                    <a href="javascript:void(0);" class="btn btn-warning btn-sm shadow-sm px-3"
                                        onclick="show_small_modal('{{ route('admin.addon-courses.edit', $addonCourse->id) }}', 'Edit Addon Course')"
                                        title="Edit">
                                        <i class="ti ti-edit"></i> Edit
                                    </a>
                                    <a href="javascript:void(0);" class="btn btn-danger btn-sm shadow-sm px-3"
                                        onclick="deleteAddonCourse({{ $addonCourse->id }})" title="Delete">
                                        <i class="ti ti-trash"></i> Delete
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">No addon courses found.</td>
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
    function deleteAddonCourse(id) {
        if (confirm('Are you sure you want to delete this addon course?')) {
            $.ajax({
                url: `/admin/addon-courses/${id}`,
                method: 'DELETE',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        toast_success(response.message);
                        setTimeout(() => { location.reload(); }, 800);
                    } else {
                        toast_error(response.error || 'Delete failed');
                    }
                },
                error: function(xhr) {
                    let errorMessage = 'Delete failed';
                    if (xhr.responseJSON && xhr.responseJSON.error) {
                        errorMessage = xhr.responseJSON.error;
                    }
                    toast_error(errorMessage);
                }
            });
        }
    }
</script>
@endpush
