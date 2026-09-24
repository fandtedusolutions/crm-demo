<div class="container p-2">
    <form action="{{ route('admin.addon-courses.updateForm', $addonCourse->id) }}" method="POST">
        @csrf
        @method('PUT')
        @include('admin.addon-courses.partials.form-fields')
        <div class="d-flex justify-content-end gap-2">
            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-primary">Update</button>
        </div>
    </form>
</div>
