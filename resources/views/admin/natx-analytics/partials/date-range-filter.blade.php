@php
    use App\Helpers\DateRangeHelper;

    $defaultPreset = $defaultDatePreset ?? DateRangeHelper::natxDefaultPreset();
    $isCustomRange = ($filters['date_range'] ?? $defaultPreset) === DateRangeHelper::PRESET_CUSTOM;
@endphp

<div class="col-md-2">
    <label class="form-label">Date Range</label>
    <select name="date_range" class="form-select form-select-sm js-call-analytics-date-range">
        @foreach(DateRangeHelper::natxOptions() as $value => $label)
            <option value="{{ $value }}" {{ ($filters['date_range'] ?? $defaultPreset) === $value ? 'selected' : '' }}>
                {{ $label }}
            </option>
        @endforeach
    </select>
</div>
<div class="col-md-2 js-call-analytics-custom-dates" style="{{ $isCustomRange ? '' : 'display:none;' }}">
    <label class="form-label">From Date</label>
    <input type="date" name="start_date" class="form-control form-control-sm js-call-analytics-start-date"
           value="{{ $filters['start_date'] }}">
</div>
<div class="col-md-2 js-call-analytics-custom-dates" style="{{ $isCustomRange ? '' : 'display:none;' }}">
    <label class="form-label">To Date</label>
    <input type="date" name="end_date" class="form-control form-control-sm js-call-analytics-end-date"
           value="{{ $filters['end_date'] }}">
</div>

@once
    @push('scripts')
    <script>
        function toggleCallAnalyticsCustomDates($form) {
            const isCustom = $form.find('.js-call-analytics-date-range').val() === 'custom';
            const $customFields = $form.find('.js-call-analytics-custom-dates');
            const $inputs = $form.find('.js-call-analytics-start-date, .js-call-analytics-end-date');

            $customFields.toggle(isCustom);
            $inputs.prop('disabled', !isCustom);
        }

        $(document).on('change', '.js-call-analytics-date-range', function () {
            toggleCallAnalyticsCustomDates($(this).closest('form'));
        });

        $(function () {
            $('form').has('.js-call-analytics-date-range').each(function () {
                toggleCallAnalyticsCustomDates($(this));
            });
        });
    </script>
    @endpush
@endonce
