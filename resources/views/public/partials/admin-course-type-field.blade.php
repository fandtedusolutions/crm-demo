@if(isset($courseTypes) && $courseTypes->count() > 0)
<div class="col-md-6">
    <div class="form-group">
        <label class="form-label">Course Type <span class="required">*</span></label>
        <select class="form-control" name="course_type_id" id="course_type_id" required>
            <option value="">Select Course Type</option>
            @foreach($courseTypes as $courseType)
                <option value="{{ $courseType->id }}">{{ $courseType->title }}</option>
            @endforeach
        </select>
    </div>
</div>
@endif
