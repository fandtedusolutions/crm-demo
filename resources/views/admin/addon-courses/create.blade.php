<div class="container p-2">
    <form action="{{ route('admin.addon-courses.submit') }}" method="POST">
        @csrf
        @include('admin.addon-courses.partials.form-fields', ['addonCourse' => null])
        <div class="d-flex justify-content-end gap-2">
            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-primary">Save</button>
        </div>
    </form>
</div>
