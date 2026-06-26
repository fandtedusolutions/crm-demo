@if($showTeamTelecallerFilters ?? false)
@php
    $selectedTeamIds = $selectedTeamIds ?? [];
    $selectedTelecallerIds = $selectedTelecallerIds ?? [];
    $filterTelecallers = $filterTelecallers ?? collect();
    $filterColClass = $filterColClass ?? 'col-12 col-md-6 col-lg-3';
@endphp
<div class="{{ $filterColClass }} team-telecaller-filter-field" data-filter-type="team">
    <label for="filter_team_ids" class="form-label team-telecaller-filter-label">
        <span><i class="ti ti-users-group"></i> Team</span>
        <small class="team-telecaller-filter-count" id="filter_team_ids_count"></small>
    </label>
    <select
        class="form-select form-select-sm team-telecaller-filter-select"
        name="team_ids[]"
        id="filter_team_ids"
        multiple
        data-placeholder="All Teams"
    >
        <option value="all" {{ empty($selectedTeamIds) ? 'selected' : '' }}>All Teams</option>
        @foreach($teams as $team)
            <option
                value="{{ $team->id }}"
                {{ in_array($team->id, $selectedTeamIds, true) ? 'selected' : '' }}
            >
                {{ $team->name }}
            </option>
        @endforeach
    </select>
</div>

<div class="{{ $filterColClass }} team-telecaller-filter-field" data-filter-type="telecaller">
    <label for="filter_telecaller_ids" class="form-label team-telecaller-filter-label">
        <span><i class="ti ti-headset"></i> Telecaller</span>
        <small class="team-telecaller-filter-count" id="filter_telecaller_ids_count"></small>
    </label>
    <select
        class="form-select form-select-sm team-telecaller-filter-select"
        name="telecaller_ids[]"
        id="filter_telecaller_ids"
        multiple
        data-placeholder="All Telecallers"
    >
        <option value="all" {{ empty($selectedTelecallerIds) ? 'selected' : '' }}>All Telecallers</option>
        @foreach($filterTelecallers as $telecaller)
            <option
                value="{{ $telecaller->id }}"
                {{ in_array($telecaller->id, $selectedTelecallerIds, true) ? 'selected' : '' }}
                data-team="{{ $telecaller->team?->name ?? 'No Team' }}"
                data-email="{{ $telecaller->email }}"
            >
                {{ $telecaller->name }}
            </option>
        @endforeach
    </select>
</div>

@once
@push('styles')
<style>
    .team-telecaller-filter-field {
        min-width: 0;
    }

    .team-telecaller-filter-label {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.5rem;
        margin-bottom: 0.35rem;
        font-size: 0.8125rem;
        font-weight: 600;
        color: #495057;
    }

    .team-telecaller-filter-label span {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
    }

    .team-telecaller-filter-label i {
        font-size: 0.95rem;
        color: #4680ff;
    }

    .team-telecaller-filter-count {
        font-size: 0.7rem;
        font-weight: 500;
        color: #868e96;
        white-space: nowrap;
    }

    .team-telecaller-filter-field .select2-container {
        width: 100% !important;
    }

    .team-telecaller-filter-field .select2-container--default .select2-selection--multiple {
        min-height: calc(1.5em + 0.5rem + 2px);
        max-height: calc(1.5em + 0.5rem + 2px);
        border: 1px solid #dbe0ea;
        border-radius: 0.375rem;
        background: #fff;
        padding: 0.125rem 0.35rem;
        transition: border-color 0.15s ease, box-shadow 0.15s ease;
    }

    .team-telecaller-filter-field .select2-container--default.select2-container--focus .select2-selection--multiple,
    .team-telecaller-filter-field .select2-container--default.select2-container--open .select2-selection--multiple {
        border-color: #86b7fe;
        box-shadow: 0 0 0 0.2rem rgba(70, 128, 255, 0.12);
    }

    .team-telecaller-filter-field .select2-container--default .select2-selection--multiple .select2-selection__rendered {
        display: flex;
        flex-wrap: nowrap;
        align-items: center;
        gap: 0.2rem;
        margin: 0;
        padding: 0;
        overflow-x: auto;
        overflow-y: hidden;
        max-height: 1.5rem;
        scrollbar-width: thin;
    }

    .team-telecaller-filter-field .select2-container--default .select2-selection--multiple .select2-selection__rendered::-webkit-scrollbar {
        height: 4px;
    }

    .team-telecaller-filter-field .select2-container--default .select2-selection--multiple .select2-selection__rendered::-webkit-scrollbar-thumb {
        background: #ced4da;
        border-radius: 999px;
    }

    .team-telecaller-filter-field .select2-container--default .select2-selection--multiple .select2-selection__choice {
        display: inline-flex;
        align-items: center;
        max-width: 140px;
        margin: 0;
        padding: 0.1rem 0.45rem;
        border-radius: 999px;
        border: 1px solid rgba(70, 128, 255, 0.22);
        background: rgba(70, 128, 255, 0.08);
        color: #2f4f9f;
        font-size: 0.72rem;
        font-weight: 600;
        line-height: 1.2;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .team-telecaller-filter-field .select2-container--default .select2-selection--multiple .select2-selection__choice__display {
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .team-telecaller-filter-field .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
        color: #6c757d;
        margin-right: 0.25rem;
        border-right: none;
        font-size: 0.85rem;
        line-height: 1;
    }

    .team-telecaller-filter-field .select2-container--default .select2-selection--multiple .select2-selection__choice__remove:hover {
        color: #dc3545;
        background: transparent;
    }

    .team-telecaller-filter-field .select2-container--default .select2-selection--multiple .select2-selection__choice.filter-choice-all {
        border-color: #dbe0ea;
        background: #f8f9fa;
        color: #6c757d;
    }

    .team-telecaller-filter-field .select2-container--default .select2-search--inline .select2-search__field {
        margin-top: 0;
        height: 1.25rem;
        font-size: 0.8125rem;
    }

    .team-telecaller-filter-dropdown {
        border: 1px solid #dbe0ea;
        border-radius: 0.5rem;
        box-shadow: 0 8px 24px rgba(15, 23, 42, 0.08);
        overflow: hidden;
    }

    .team-telecaller-filter-dropdown .select2-search__field {
        border: 1px solid #dbe0ea !important;
        border-radius: 0.375rem !important;
        padding: 0.25rem 0.5rem !important;
        font-size: 0.8125rem !important;
    }

    .team-telecaller-filter-dropdown .select2-results__option {
        padding: 0.55rem 0.75rem;
        border-bottom: 1px solid #f1f3f5;
    }

    .team-telecaller-filter-dropdown .select2-results__option:last-child {
        border-bottom: none;
    }

    .team-telecaller-filter-dropdown .select2-results__option--highlighted[aria-selected] {
        background: rgba(70, 128, 255, 0.1) !important;
        color: #1f2937 !important;
    }

    .team-telecaller-filter-dropdown .select2-results__option--highlighted[aria-selected] .team-filter-option-desc,
    .team-telecaller-filter-dropdown .select2-results__option--highlighted[aria-selected] .telecaller-filter-option-desc {
        color: #495057 !important;
    }

    .team-filter-option-title,
    .telecaller-filter-option-title {
        font-weight: 600;
        font-size: 0.8125rem;
        line-height: 1.3;
        color: #212529;
    }

    .team-filter-option-desc,
    .telecaller-filter-option-desc {
        font-size: 0.75rem;
        color: #6c757d;
        line-height: 1.35;
        white-space: normal;
        margin-top: 0.15rem;
    }

    .team-filter-option-meta,
    .telecaller-filter-option-meta {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        margin-top: 0.25rem;
        flex-wrap: wrap;
    }

    .team-filter-badge,
    .telecaller-filter-badge {
        display: inline-block;
        padding: 0.08rem 0.45rem;
        border-radius: 999px;
        font-size: 0.68rem;
        font-weight: 600;
        line-height: 1.3;
        background: #eef2ff;
        color: #3b5bdb;
    }

    .team-filter-badge.is-b2b {
        background: #fff4e6;
        color: #e67700;
    }

    .team-filter-option,
    .telecaller-filter-option {
        pointer-events: none;
    }
</style>
@endpush

@push('scripts')
<script>
(function() {
    if (window.TeamTelecallerFilters && window.TeamTelecallerFilters.initialized) {
        return;
    }

    const telecallersRoute = @json(route('leads.telecallers-by-team'));

    function escapeHtml(value) {
        return String(value || '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;');
    }

    function formatTeamOption(option) {
        return option.text;
    }

    function formatTelecallerOption(option) {
        if (!option.id || option.id === 'all') {
            return option.text;
        }

        const $option = $(option.element);
        const title = escapeHtml(option.text || '');
        const team = escapeHtml($option.data('team') || '');
        const email = escapeHtml($option.data('email') || '');

        return $(`
            <div class="telecaller-filter-option">
                <div class="telecaller-filter-option-title">${title}</div>
                <div class="telecaller-filter-option-desc">
                    ${team ? `Team: ${team}` : ''}${team && email ? ' | ' : ''}${email ? `Email: ${email}` : ''}
                </div>
            </div>
        `);
    }

    function formatCompactSelection(option) {
        return escapeHtml(option.text || '');
    }

    function updateSelectionCounts() {
        const teamCount = getSelectedTeamIds().length;
        const telecallerCount = getSelectedTelecallerIds().length;

        $('#filter_team_ids_count').text(teamCount ? `${teamCount} selected` : 'All');
        $('#filter_telecaller_ids_count').text(telecallerCount ? `${telecallerCount} selected` : 'All');
    }

    function styleAllChoiceChips() {
        $('.team-telecaller-filter-field .select2-selection__choice').each(function() {
            const text = $(this).attr('title') || $(this).text().replace(/×/g, '').trim();
            if (text === 'All Teams' || text === 'All Telecallers') {
                $(this).addClass('filter-choice-all');
            } else {
                $(this).removeClass('filter-choice-all');
            }
        });
    }

    function initSelect($select, type) {
        if (!$select.length || !$.fn.select2) {
            return;
        }

        if ($select.hasClass('select2-hidden-accessible')) {
            $select.select2('destroy');
        }

        const isTeam = type === 'team';
        $select.select2({
            placeholder: $select.data('placeholder') || 'Select...',
            allowClear: false,
            width: '100%',
            closeOnSelect: false,
            dropdownCssClass: 'team-telecaller-filter-dropdown',
            templateResult: isTeam ? formatTeamOption : formatTelecallerOption,
            templateSelection: formatCompactSelection,
            escapeMarkup: function(markup) {
                return markup;
            }
        });

        styleAllChoiceChips();
        updateSelectionCounts();
    }

    function getSelectedTeamIds() {
        return ($('#filter_team_ids').val() || []).filter(function(value) {
            return value && value !== 'all';
        });
    }

    function getSelectedTelecallerIds() {
        return ($('#filter_telecaller_ids').val() || []).filter(function(value) {
            return value && value !== 'all';
        });
    }

    function bindAllExclusiveSelect($select, onSelectionChange) {
        $select.off('select2:select.teamTelecallerFilters select2:unselect.teamTelecallerFilters change.teamTelecallerFilters');

        $select.on('select2:select.teamTelecallerFilters', function(e) {
            const selectedId = String(e.params.data.id);

            if (selectedId === 'all') {
                $select.val(['all']).trigger('change');
                return;
            }

            const specificValues = ($select.val() || []).filter(function(value) {
                return value && String(value) !== 'all';
            });

            $select.val(specificValues).trigger('change');
        });

        $select.on('select2:unselect.teamTelecallerFilters', function() {
            window.setTimeout(function() {
                const remaining = ($select.val() || []).filter(function(value) {
                    return value && String(value) !== 'all';
                });

                if (remaining.length === 0) {
                    $select.val(['all']).trigger('change');
                }
            }, 0);
        });

        $select.on('change.teamTelecallerFilters', function() {
            styleAllChoiceChips();
            updateSelectionCounts();
            if (typeof onSelectionChange === 'function') {
                onSelectionChange();
            }
        });
    }

    function rebuildTelecallerOptions(telecallers, selectedIds) {
        const $telecallerSelect = $('#filter_telecaller_ids');
        const keepSelected = selectedIds || getSelectedTelecallerIds();
        let html = '<option value="all">All Telecallers</option>';

        telecallers.forEach(function(telecaller) {
            const selected = keepSelected.includes(String(telecaller.id)) ? 'selected' : '';
            const teamName = telecaller.team_name || 'No Team';
            const email = telecaller.email || '';
            html += `<option value="${telecaller.id}" data-team="${teamName}" data-email="${email}" ${selected}>${telecaller.name}</option>`;
        });

        $telecallerSelect.html(html);

        if (keepSelected.length === 0) {
            $telecallerSelect.val(['all']);
        } else {
            $telecallerSelect.val(keepSelected);
        }

        initSelect($telecallerSelect, 'telecaller');
        bindAllExclusiveSelect($telecallerSelect);
    }

    function loadTelecallersForTeams(teamIds, selectedTelecallerIds) {
        const payload = teamIds.length ? { team_ids: teamIds } : { team_id: 'all' };

        return $.get(telecallersRoute, payload).done(function(response) {
            rebuildTelecallerOptions(response.telecallers || [], selectedTelecallerIds || []);
        }).fail(function() {
            rebuildTelecallerOptions([], []);
        });
    }

    function initTeamTelecallerFilters() {
        const $teamSelect = $('#filter_team_ids');
        const $telecallerSelect = $('#filter_telecaller_ids');

        if (!$teamSelect.length) {
            return;
        }

        initSelect($teamSelect, 'team');
        initSelect($telecallerSelect, 'telecaller');

        bindAllExclusiveSelect($teamSelect, function() {
            loadTelecallersForTeams(getSelectedTeamIds());
        });

        bindAllExclusiveSelect($telecallerSelect);
    }

    window.TeamTelecallerFilters = {
        initialized: true,
        init: initTeamTelecallerFilters,
        getSelectedTeamIds: getSelectedTeamIds,
        getSelectedTelecallerIds: getSelectedTelecallerIds,
        appendParams: function(params) {
            getSelectedTeamIds().forEach(function(id) {
                params.append('team_ids[]', id);
            });
            getSelectedTelecallerIds().forEach(function(id) {
                params.append('telecaller_ids[]', id);
            });
        },
        extendDataTableParams: function(params) {
            params.team_ids = getSelectedTeamIds();
            params.telecaller_ids = getSelectedTelecallerIds();
        },
        loadFromUrl: function(urlParams) {
            let teamIds = urlParams.getAll('team_ids[]');
            let telecallerIds = urlParams.getAll('telecaller_ids[]');

            if (!teamIds.length && urlParams.get('team_id')) {
                teamIds = [urlParams.get('team_id')];
            }
            if (!telecallerIds.length && urlParams.get('telecaller_id')) {
                telecallerIds = [urlParams.get('telecaller_id')];
            }

            if (teamIds.length) {
                $('#filter_team_ids').val(teamIds).trigger('change');
                loadTelecallersForTeams(teamIds, telecallerIds).always(function() {
                    if (telecallerIds.length) {
                        $('#filter_telecaller_ids').val(telecallerIds).trigger('change');
                    }
                });
                return;
            }

            if (telecallerIds.length) {
                $('#filter_telecaller_ids').val(telecallerIds).trigger('change');
            }
        },
        reset: function() {
            $('#filter_team_ids').val(['all']).trigger('change');
            $('#filter_telecaller_ids').val(['all']).trigger('change');
            loadTelecallersForTeams([]);
        }
    };

    $(document).ready(function() {
        initTeamTelecallerFilters();
    });
})();
</script>
@endpush
@endonce
@endif
