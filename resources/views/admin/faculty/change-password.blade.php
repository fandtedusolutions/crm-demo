<div class="container p-2">
    <form action="{{ route('admin.faculty.update-password', $facultyUser->id) }}" method="post">
        @csrf
        <div class="row">
            <div class="col-md-12">
                <div class="mb-3">
                    <label class="form-label" for="password">New Password <span class="text-danger">*</span></label>
                    <input type="password" name="password" class="form-control" id="password" placeholder="Enter New Password" required>
                </div>
            </div>

            <div class="col-md-12">
                <div class="mb-3">
                    <label class="form-label" for="password_confirmation">Confirm Password <span class="text-danger">*</span></label>
                    <input type="password" name="password_confirmation" class="form-control" id="password_confirmation" placeholder="Confirm New Password" required>
                </div>
            </div>
        </div>

        <button type="submit" class="btn btn-success float-end">Update Password</button>
    </form>
</div>
