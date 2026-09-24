@php
    $selectedCourseId = (string) old('course_id', $addonCourse?->course_id ?? '');
    $selectedAddonId = (string) old('addon_course_id', $addonCourse?->addon_course_id ?? '');
@endphp

<div class="mb-3">
    <label for="course_id" class="form-label">Course <span class="text-danger">*</span></label>
    <select class="form-select" id="course_id" name="course_id" required>
        <option value="">Select course</option>
        @foreach($courses as $course)
            <option value="{{ $course->id }}" {{ $selectedCourseId === (string) $course->id ? 'selected' : '' }}>
                {{ $course->title }}
            </option>
        @endforeach
    </select>
    <div class="form-text">Course that needs an addon</div>
</div>

<div class="mb-3">
    <label for="addon_course_id" class="form-label">Addon Course <span class="text-danger">*</span></label>
    <select class="form-select" id="addon_course_id" name="addon_course_id" required>
        <option value="">Select addon course</option>
        @foreach($courses as $course)
            <option value="{{ $course->id }}" {{ $selectedAddonId === (string) $course->id ? 'selected' : '' }}>
                {{ $course->title }}
            </option>
        @endforeach
    </select>
    <div class="form-text">Course selected above is not listed here</div>
</div>

<div class="form-check mb-3">
    <input class="form-check-input" type="checkbox" id="is_active" name="is_active" {{ old('is_active', $addonCourse?->is_active ?? true) ? 'checked' : '' }}>
    <label class="form-check-label" for="is_active">Active</label>
</div>

<script>
(function () {
    var courseSelect = document.getElementById('course_id');
    var addonSelect = document.getElementById('addon_course_id');
    if (!courseSelect || !addonSelect) {
        return;
    }

    var addonOptions = Array.prototype.map.call(addonSelect.options, function (option) {
        return { value: option.value, text: option.text };
    });

    function syncAddonOptions() {
        var parentId = courseSelect.value;
        var current = addonSelect.value;
        addonSelect.innerHTML = '';

        addonOptions.forEach(function (option) {
            if (option.value !== '' && option.value === parentId) {
                return;
            }

            var element = document.createElement('option');
            element.value = option.value;
            element.textContent = option.text;
            if (option.value === current && option.value !== parentId) {
                element.selected = true;
            }
            addonSelect.appendChild(element);
        });

        if (current === parentId) {
            addonSelect.value = '';
        }
    }

    courseSelect.addEventListener('change', syncAddonOptions);
    syncAddonOptions();
})();
</script>
