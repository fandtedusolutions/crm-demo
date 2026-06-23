<form action="{{ route('admin.batches.submit') }}" method="post">
    @csrf
    <div class="row g-3">
        <div class="col-lg-6">
            <div class="p-1">
                <label for="course_id" class="form-label">Course <span class="text-danger">*</span></label>
                <select class="form-control" name="course_id" id="course_id" required>
                    <option value="">Select Course</option>
                    @foreach($courses as $course)
                        <option value="{{ $course->id }}">{{ $course->title }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="p-1">
                <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="title" id="title" value="{{ old('title') }}" required>
            </div>
        </div>

        <div class="col-lg-6 amount-general">
            <div class="p-1">
                <label for="amount" class="form-label">Amount</label>
                <div class="input-group">
                    <span class="input-group-text">₹</span>
                    <input type="number" step="0.01" min="0" class="form-control" name="amount" id="amount" value="{{ old('amount') }}" placeholder="Enter amount">
                </div>
            </div>
        </div>

        <div class="col-lg-6 gmvss-amount d-none">
            <div class="p-1">
                <label for="sslc_amount" class="form-label">SSLC Amount (Grameen Mukt Vidhyalayi Shiksha Sansthan)</label>
                <div class="input-group">
                    <span class="input-group-text">₹</span>
                    <input type="number" step="0.01" min="0" class="form-control" name="sslc_amount" id="sslc_amount" value="{{ old('sslc_amount') }}" placeholder="Enter SSLC amount">
                </div>
            </div>
        </div>

        <div class="col-lg-6 gmvss-amount d-none">
            <div class="p-1">
                <label for="plustwo_amount" class="form-label">Plus Two Amount (Grameen Mukt Vidhyalayi Shiksha Sansthan)</label>
                <div class="input-group">
                    <span class="input-group-text">₹</span>
                    <input type="number" step="0.01" min="0" class="form-control" name="plustwo_amount" id="plustwo_amount" value="{{ old('plustwo_amount') }}" placeholder="Enter Plus Two amount">
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="p-1">
                <label for="b2b_amount" class="form-label">B2B Amount</label>
                <div class="input-group">
                    <span class="input-group-text">₹</span>
                    <input type="number" step="0.01" min="0" class="form-control" name="b2b_amount" id="b2b_amount" value="{{ old('b2b_amount') }}" placeholder="Enter B2B amount">
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="p-1">
                <label for="is_active" class="form-label">Status</label>
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" name="is_active" id="is_active" checked>
                    <label class="form-check-label" for="is_active">Active</label>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="p-1">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control" name="description" id="description" rows="3" placeholder="Enter batch description"></textarea>
            </div>
        </div>

        <div class="col-12 p-2">
            <button class="btn btn-primary float-end" type="submit">Save Batch</button>
        </div>
    </div>
</form>

<script>
(function () {
    const courseSelect = document.getElementById('course_id');
    const gmvssFields = document.querySelectorAll('.gmvss-amount');
    const generalAmount = document.querySelectorAll('.amount-general');

    function toggleGmvssFields() {
        const value = courseSelect ? courseSelect.value : '';
        const isGmvss = value === '16' || value === 16;
        gmvssFields.forEach(el => el.classList.toggle('d-none', !isGmvss));
        generalAmount.forEach(el => el.classList.toggle('d-none', isGmvss));
    }

    if (courseSelect) {
        toggleGmvssFields();
        courseSelect.addEventListener('change', toggleGmvssFields);
    }
})();
</script>
