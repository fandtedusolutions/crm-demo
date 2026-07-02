@once
    @push('scripts')
    <script>
        function createSelectAppStatus(currentVal) {
            var choices = [
                { value: '', label: 'Select APP Status' },
                { value: 'Provided app', label: 'Provided app' },
                { value: 'OTP Problem', label: 'OTP Problem' },
                { value: 'Task Completed', label: 'Task Completed' },
            ];
            var html = '';
            choices.forEach(function (choice) {
                html += '<option value="' + choice.value + '"' + (String(currentVal) === choice.value ? ' selected' : '') + '>' + choice.label + '</option>';
            });
            return '<div class="edit-form"><select class="form-select form-select-sm">' + html + '</select><div class="btn-group mt-1"><button type="button" class="btn btn-success btn-sm save-edit">Save</button><button type="button" class="btn btn-secondary btn-sm cancel-edit">Cancel</button></div></div>';
        }
    </script>
    @endpush
@endonce
