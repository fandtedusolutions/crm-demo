@php
    $parentCourseId = (isset($course) && $course) ? $course->id : ($addonParentCourseId ?? null);
    $addonCourseOptions = \App\Support\AddonCourseSupport::forCourse($parentCourseId ? (int) $parentCourseId : null);
    $selectedAddonCourseId = (string) old('addon_course_id', '');
@endphp
@if($addonCourseOptions->isNotEmpty())
<div class="row">
    <div class="col-md-6">
        <div class="form-group mb-3">
            <label class="form-label">Addon Course</label>
            <select class="form-control" name="addon_course_id" id="addon_course_id">
                <option value="">Select Addon Course</option>
                @foreach($addonCourseOptions as $addonCourseOption)
                    <option value="{{ $addonCourseOption->addon_course_id }}" {{ $selectedAddonCourseId === (string) $addonCourseOption->addon_course_id ? 'selected' : '' }}>
                        {{ $addonCourseOption->addonCourse->title }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>
</div>
@endif
