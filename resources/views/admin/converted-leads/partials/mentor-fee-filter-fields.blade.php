<div class="col-12 col-sm-6 col-md-2">
    <label for="reg_fee" class="form-label">REG. FEE</label>
    <select class="form-select" id="reg_fee" name="reg_fee">
        <option value="">All</option>
        @foreach(\App\Support\MentorFlagFieldSupport::regFeeFilterOptions() as $regFeeOption)
            <option value="{{ $regFeeOption }}" {{ request('reg_fee') === $regFeeOption ? 'selected' : '' }}>{{ $regFeeOption }}</option>
        @endforeach
    </select>
</div>
<div class="col-12 col-sm-6 col-md-2">
    <label for="exam_fee" class="form-label">EXAM FEE</label>
    <select class="form-select" id="exam_fee" name="exam_fee">
        <option value="">All</option>
        @foreach(\App\Support\MentorFlagFieldSupport::examFeeFilterOptions() as $examFeeOption)
            <option value="{{ $examFeeOption }}" {{ request('exam_fee') === $examFeeOption ? 'selected' : '' }}>{{ $examFeeOption }}</option>
        @endforeach
    </select>
</div>
