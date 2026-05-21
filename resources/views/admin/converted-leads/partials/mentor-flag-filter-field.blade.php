<div class="col-12 col-sm-6 col-md-2">
    <label for="flag_id" class="form-label">Flag</label>
    <select class="form-select" id="flag_id" name="flag_id">
        <option value="">All Flags</option>
        @foreach(($flags ?? collect()) as $flag)
            <option value="{{ $flag->id }}" {{ (string) request('flag_id') === (string) $flag->id ? 'selected' : '' }}>
                {{ $flag->title }}
            </option>
        @endforeach
    </select>
</div>
