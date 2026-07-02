<form action="{{ route('admin.leads.bulk-reassign.submit') }}" method="post" enctype="multipart/form-data" id="bulkReassignForm">
    @csrf

    <div class="bulk-reassign-section bulk-reassign-section--transfer">
        <div class="bulk-reassign-section__header">
            <span class="bulk-reassign-section__step">1</span>
            <div>
                <h6 class="bulk-reassign-section__title">Re-assign To & From</h6>
                <p class="bulk-reassign-section__hint">Choose who receives the leads and who they are moving from.</p>
            </div>
        </div>
        <div class="bulk-reassign-section__body">
            <div class="row g-3 align-items-stretch">
                <div class="col-lg-5">
                    <div class="bulk-reassign-transfer-block bulk-reassign-transfer-block--to">
                        <div class="bulk-reassign-transfer-block__title">
                            <i class="ti ti-user-check"></i> Re-assign To
                        </div>
                        <div class="row g-3">
                            <div class="col-12">
                                <div class="bulk-reassign-field">
                                    <label for="to_team_id"><i class="ti ti-users-group me-1"></i>To Team</label>
                                    <select class="form-control bulk-reassign-select2" name="to_team_id" id="to_team_id" required>
                                        <option value="">Select Team</option>
                                        @foreach ($teams as $team)
                                        <option value="{{ $team->id }}" {{ old('to_team_id') == $team->id ? 'selected' : '' }}>{{ $team->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="bulk-reassign-field">
                                    <label for="telecaller_id"><i class="ti ti-user me-1"></i>Telecaller</label>
                                    <select class="form-control bulk-reassign-select2" name="telecaller_id" id="telecaller_id" required>
                                        <option value="">Select Team First</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-2 d-flex align-items-center justify-content-center">
                    <div class="bulk-reassign-transfer-arrow" title="Move leads">
                        <i class="ti ti-arrow-right"></i>
                    </div>
                </div>

                <div class="col-lg-5">
                    <div class="bulk-reassign-transfer-block bulk-reassign-transfer-block--from">
                        <div class="bulk-reassign-transfer-block__title">
                            <i class="ti ti-user-minus"></i> Re-assign From
                        </div>
                        <div class="row g-3">
                            <div class="col-12">
                                <div class="bulk-reassign-field">
                                    <label for="from_team_id"><i class="ti ti-users-group me-1"></i>From Team</label>
                                    <select class="form-control bulk-reassign-select2" name="from_team_id" id="from_team_id" required>
                                        <option value="">Select Team</option>
                                        @foreach ($teams as $team)
                                        <option value="{{ $team->id }}" {{ old('from_team_id') == $team->id ? 'selected' : '' }}>{{ $team->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="bulk-reassign-field">
                                    <label for="from_telecaller_id"><i class="ti ti-user me-1"></i>Telecaller</label>
                                    <select class="form-control bulk-reassign-select2" name="from_telecaller_id" id="from_telecaller_id" required>
                                        <option value="">Select Team First</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="bulk-reassign-section bulk-reassign-section--filter">
        <div class="bulk-reassign-section__header">
            <span class="bulk-reassign-section__step">2</span>
            <div>
                <h6 class="bulk-reassign-section__title">Lead Filters</h6>
                <p class="bulk-reassign-section__hint">Narrow down which leads should appear in the list below.</p>
            </div>
        </div>
        <div class="bulk-reassign-section__body">
            <div class="row g-3">
                <div class="col-lg-3 col-md-6">
                    <div class="bulk-reassign-field">
                        <label for="lead_source_id"><i class="ti ti-source-code me-1"></i>Lead Source</label>
                        <select class="form-control bulk-reassign-filter-select" name="lead_source_id" id="lead_source_id" required>
                            <option value="">Select Source</option>
                            @foreach ($leadSources as $source)
                            <option value="{{ $source->id }}" {{ old('lead_source_id') == $source->id ? 'selected' : '' }}>{{ $source->title }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="bulk-reassign-field">
                        <label for="lead_status_id"><i class="ti ti-flag me-1"></i>Lead Status</label>
                        <select class="form-control bulk-reassign-filter-select" name="lead_status_id" id="lead_status_id" required>
                            <option value="">Select Status</option>
                            @foreach ($leadStatuses as $status)
                            <option value="{{ $status->id }}" {{ old('lead_status_id') == $status->id ? 'selected' : '' }}>{{ $status->title }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="bulk-reassign-field">
                        <label for="reassign_course_id"><i class="ti ti-school me-1"></i>Course <span class="text-muted fw-normal">(Optional)</span></label>
                        <select class="form-control bulk-reassign-filter-select" name="course_id" id="reassign_course_id">
                            <option value="">All Courses</option>
                            @foreach ($courses as $course)
                            <option value="{{ $course->id }}" {{ old('course_id') == $course->id ? 'selected' : '' }}>{{ $course->title }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="row g-2">
                        <div class="col-6">
                            <div class="bulk-reassign-field">
                                <label for="lead_from_date"><i class="ti ti-calendar me-1"></i>From Date</label>
                                <input type="date" id="lead_from_date" name="lead_from_date" class="form-control" value="{{ old('lead_from_date') }}" required>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="bulk-reassign-field">
                                <label for="lead_to_date">To Date</label>
                                <input type="date" id="lead_to_date" name="lead_to_date" class="form-control" value="{{ old('lead_to_date') }}" required>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="bulk-reassign-section bulk-reassign-section--leads">
        <div class="bulk-reassign-section__header">
            <span class="bulk-reassign-section__step">3</span>
            <div>
                <h6 class="bulk-reassign-section__title">Select Leads</h6>
                <p class="bulk-reassign-section__hint">Check the leads you want to reassign, or use quick select by count.</p>
            </div>
        </div>
        <div class="bulk-reassign-section__body">
            <div class="bulk-reassign-leads-toolbar">
                <div class="bulk-reassign-leads-toolbar__meta">
                    <span class="bulk-reassign-count-pill" id="loaded_count_pill">
                        <i class="ti ti-list"></i> Loaded: <span id="loaded_count">0</span>
                    </span>
                    <span class="bulk-reassign-count-pill bulk-reassign-count-pill--selected" id="selected_count_pill">
                        <i class="ti ti-checkbox"></i> Selected: <span id="selected_count">0</span>
                    </span>
                </div>
                <div class="bulk-reassign-quick-select">
                    <label for="select_count">Quick select top</label>
                    <input type="number" id="select_count" class="form-control form-control-sm" min="1" placeholder="e.g. 10">
                </div>
            </div>

            <div class="table-responsive bulk-operations-table" id="bulk_operations_table">
                <div class="bulk-operations-table__loader">
                    <div class="spinner-border spinner-border-sm text-primary mb-2" role="status"></div>
                    <div>Loading leads...</div>
                </div>
                <table class="table table-hover bulk-table mb-0">
                    <thead>
                        <tr>
                            <th style="width: 50px;">#</th>
                            <th>Lead</th>
                            <th>Status</th>
                            <th>Course</th>
                            <th>Remarks</th>
                            <th>Date</th>
                            <th style="width: 70px;" class="text-center">
                                <input type="checkbox" id="check_all" class="bulk-checkbox" title="Select all">
                            </th>
                        </tr>
                    </thead>
                    <tbody id="lead_table_body">
                        <tr class="bulk-empty-row">
                            <td colspan="7">
                                <i class="ti ti-filter"></i>
                                Complete the filters above to load matching leads.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="bulk-reassign-actions">
        <p class="bulk-reassign-actions__hint mb-0">
            <i class="ti ti-info-circle me-1"></i>
            Re-assigned leads will be moved to the selected telecaller and status will reset to New.
        </p>
        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('leads.index') }}" class="btn btn-outline-secondary">
                <i class="ti ti-arrow-left me-1"></i> Cancel
            </a>
            <button class="btn btn-success px-4" type="submit" id="reassign_btn" disabled>
                <i class="ti ti-exchange me-1"></i> Re-Assign Selected
            </button>
        </div>
    </div>
</form>

@push('scripts')
<script>
    $(document).ready(function() {
        const $toTeam = $('#to_team_id');
        const $toTelecaller = $('#telecaller_id');
        const $fromTeam = $('#from_team_id');
        const $fromTelecaller = $('#from_telecaller_id');
        const $leadTableBody = $('#lead_table_body');
        const $bulkTable = $('#bulk_operations_table');
        const $loadedCount = $('#loaded_count');
        const $selectedCount = $('#selected_count');

        const oldToTeam = @json(old('to_team_id'));
        const oldToTelecaller = @json(old('telecaller_id'));
        const oldFromTeam = @json(old('from_team_id'));
        const oldFromTelecaller = @json(old('from_telecaller_id'));

        function initSelect2($select, placeholder) {
            if (!$select.length || !$.fn.select2) {
                return;
            }

            if ($select.hasClass('select2-hidden-accessible')) {
                $select.select2('destroy');
            }

            $select.select2({
                placeholder: placeholder,
                allowClear: true,
                width: '100%'
            });
        }

        function loadTeamTelecallers(teamId, $select, selectedId) {
            const deferred = $.Deferred();

            if (!teamId) {
                $select.html('<option value="">Select Team First</option>');
                initSelect2($select, 'Select Telecaller');
                $select.val('').trigger('change.select2');
                deferred.resolve();
                return deferred.promise();
            }

            $select.html('<option value="">Loading telecallers...</option>');
            initSelect2($select, 'Select Telecaller');

            $.ajax({
                url: '{{ route("leads.telecallers-by-team") }}',
                type: 'GET',
                data: { team_id: teamId },
                success: function(response) {
                    $select.html('<option value="">Select Telecaller</option>');

                    if (response.telecallers && response.telecallers.length > 0) {
                        $.each(response.telecallers, function(index, telecaller) {
                            const label = telecaller.team_name
                                ? telecaller.name + ' (' + telecaller.team_name + ')'
                                : telecaller.name;
                            $select.append('<option value="' + telecaller.id + '">' + label + '</option>');
                        });
                    } else {
                        $select.append('<option value="">No telecallers found in this team</option>');
                    }

                    initSelect2($select, 'Select Telecaller');

                    if (selectedId) {
                        $select.val(String(selectedId)).trigger('change.select2');
                    } else {
                        $select.val('').trigger('change.select2');
                    }

                    deferred.resolve();
                },
                error: function() {
                    $select.html('<option value="">Error loading telecallers</option>');
                    initSelect2($select, 'Select Telecaller');
                    deferred.resolve();
                }
            });

            return deferred.promise();
        }

        function updateCounts() {
            const loaded = $leadTableBody.find('input[type="checkbox"]').length;
            const selected = $leadTableBody.find('input[type="checkbox"]:checked').length;
            $loadedCount.text(loaded);
            $selectedCount.text(selected);
        }

        function showEmptyState(message, icon) {
            $leadTableBody.html(
                '<tr class="bulk-empty-row"><td colspan="7">' +
                '<i class="ti ' + (icon || 'ti-filter') + '"></i>' +
                message +
                '</td></tr>'
            );
            $('#check_all').prop('checked', false);
            $('#select_count').val('');
            updateCounts();
            toggleSubmitButton();
        }

        function toggleSubmitButton() {
            const anyChecked = $leadTableBody.find('input[type="checkbox"]:checked').length > 0;
            $('#reassign_btn').prop('disabled', !anyChecked);
            updateCounts();
        }

        $('#check_all').on('change', function() {
            const isChecked = $(this).is(':checked');
            $leadTableBody.find('input[type="checkbox"]').prop('checked', isChecked);
            toggleSubmitButton();
        });

        $leadTableBody.on('change', 'input[type="checkbox"]', function() {
            const total = $leadTableBody.find('input[type="checkbox"]').length;
            const checked = $leadTableBody.find('input[type="checkbox"]:checked').length;
            $('#check_all').prop('checked', total > 0 && total === checked);
            toggleSubmitButton();
        });

        $('#select_count').on('input', function() {
            const count = parseInt($(this).val()) || 0;
            const checkboxes = $leadTableBody.find('input[type="checkbox"]');
            checkboxes.prop('checked', false);
            checkboxes.slice(0, count).prop('checked', true);
            toggleSubmitButton();
        });

        function loadBulkReassignLeads() {
            const leadSourceId = $('#lead_source_id').val();
            const leadStatusId = $('#lead_status_id').val();
            const teleCallerId = $fromTelecaller.val();
            const leadFromDate = $('#lead_from_date').val();
            const leadToDate = $('#lead_to_date').val();
            const courseId = $('#reassign_course_id').val();

            if (leadSourceId && teleCallerId && leadFromDate && leadToDate && leadStatusId) {
                $bulkTable.addClass('is-loading');

                $.ajax({
                    url: '{{ route("admin.leads.get-by-source-reassign") }}',
                    type: 'POST',
                    data: {
                        lead_source_id: leadSourceId,
                        tele_caller_id: teleCallerId,
                        from_date: leadFromDate,
                        to_date: leadToDate,
                        lead_status_id: leadStatusId,
                        course_id: courseId || ''
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        $bulkTable.removeClass('is-loading');
                        $leadTableBody.html(response);

                        if ($leadTableBody.find('input[type="checkbox"]').length === 0) {
                            showEmptyState('No leads found for the selected filters.', 'ti-mood-empty');
                            return;
                        }

                        $('#check_all').prop('checked', false);
                        $('#select_count').val('');
                        toggleSubmitButton();
                    },
                    error: function() {
                        $bulkTable.removeClass('is-loading');
                        showEmptyState('Unable to load leads. Please try again.', 'ti-alert-triangle');
                    }
                });
            } else {
                showEmptyState('Complete the filters above to load matching leads.', 'ti-filter');
            }
        }

        initSelect2($toTeam, 'Select Team');
        initSelect2($fromTeam, 'Select Team');
        initSelect2($toTelecaller, 'Select Telecaller');
        initSelect2($fromTelecaller, 'Select Telecaller');
        $('.bulk-reassign-filter-select').each(function() {
            initSelect2($(this), $(this).find('option:first').text() || 'Select');
        });

        $toTeam.on('change', function() {
            loadTeamTelecallers($(this).val(), $toTelecaller, '');
        });

        $fromTeam.on('change', function() {
            loadTeamTelecallers($(this).val(), $fromTelecaller, '').done(loadBulkReassignLeads);
        });

        $fromTelecaller.on('change', loadBulkReassignLeads);
        $('#lead_source_id, #lead_from_date, #lead_to_date, #lead_status_id, #reassign_course_id').on('change', loadBulkReassignLeads);

        $.when(
            oldToTeam ? loadTeamTelecallers(oldToTeam, $toTelecaller, oldToTelecaller) : $.Deferred().resolve(),
            oldFromTeam ? loadTeamTelecallers(oldFromTeam, $fromTelecaller, oldFromTelecaller) : $.Deferred().resolve()
        ).done(function() {
            loadBulkReassignLeads();
        });
    });
</script>
@endpush
