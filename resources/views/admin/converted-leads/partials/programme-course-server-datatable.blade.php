@php
    $courseDataTableId = $courseDataTableId ?? 'programmeCourseTable';
    $scopedCourseId = (int) ($scopedCourseId ?? 0);
    $programmeDtLayout = $programmeDtLayout ?? 'digital_programme';
    $showParentDt = \App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor();

    $dtCol = fn ($data, $name = null) => [
        'data' => $data,
        'name' => $name ?? $data,
        'orderable' => false,
        'searchable' => false,
    ];

    $cols = [
        $dtCol('sl_no'),
        $dtCol('academic'),
        $dtCol('support'),
        $dtCol('converted_date'),
    ];

    if ($programmeDtLayout === 'ai_python') {
        $cols[] = $dtCol('registration_number');
        $cols[] = $dtCol('course_flag');
    } else {
        $cols[] = $dtCol('register_number');
        $cols[] = $dtCol('course_flag');
    }

    $cols = array_merge($cols, [
        $dtCol('name'),
        $dtCol('type'),
        $dtCol('phone'),
        $dtCol('whatsapp'),
    ]);

    if ($showParentDt) {
        $cols[] = $dtCol('parent_phone');
    }

    if ($programmeDtLayout === 'digital_programme') {
        $cols = array_merge($cols, [
            $dtCol('programme_type'),
            $dtCol('location'),
            $dtCol('class_time'),
        ]);
    }

    $cols = array_merge($cols, [
        $dtCol('batch'),
        $dtCol('admission_batch'),
        $dtCol('finance_approval'),
        $dtCol('faculty'),
        $dtCol('internship_id'),
        $dtCol('email'),
        $dtCol('call_status'),
        $dtCol('class_information'),
        $dtCol('orientation_class_status'),
        $dtCol('class_starting_date'),
        $dtCol('class_ending_date'),
        $dtCol('whatsapp_group_status'),
    ]);

    if ($programmeDtLayout === 'ai_python') {
        $cols[] = $dtCol('class_time');
    }

    $cols = array_merge($cols, [
        $dtCol('class_status'),
        $dtCol('complete_cancel_date'),
        $dtCol('remarks'),
        $dtCol('actions'),
    ]);
@endphp

@push('styles')
<style>
#{{ $courseDataTableId }} thead th,
#{{ $courseDataTableId }} tbody td {
    white-space: nowrap;
}
#{{ $courseDataTableId }} thead th {
    position: sticky;
    top: 0;
    z-index: 5;
    background: #fff;
    box-shadow: inset 0 -1px 0 #e9ecef;
}
#{{ $courseDataTableId }} tbody tr:hover {
    background: #fafbff;
}
#{{ $courseDataTableId }} td .display-value {
    display: inline-block;
    max-width: 220px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    vertical-align: middle;
}
</style>
@endpush

@push('scripts')
<script type="application/json" id="programmeCourseDtColumns-{{ $courseDataTableId }}">{!! json_encode($cols) !!}</script>
<script>
(function() {
    const TABLE_SEL = '#{{ $courseDataTableId }}';
    const DATA_URL = @json(route('admin.converted-leads.data'));
    const SCOPED_COURSE_ID = {{ $scopedCourseId }};

    window.reloadProgrammeCourseDataTable = function() {
        if (typeof $ !== 'undefined' && $.fn.DataTable && $.fn.DataTable.isDataTable(TABLE_SEL)) {
            $(TABLE_SEL).DataTable().ajax.reload(null, false);
        } else {
            location.reload();
        }
    };

    function programmeCourseFilterParams() {
        const $f = $('#filterForm');
        return {
            filter_search: ($f.find('#search').val() || '').trim(),
            search: ($f.find('#search').val() || '').trim(),
            status: $f.find('#status').val() || '',
            call_status: $f.find('#call_status').val() || '',
            class_information: $f.find('#class_information').val() || '',
            orientation_class_status: $f.find('#orientation_class_status').val() || '',
            whatsapp_group_status: $f.find('#whatsapp_group_status').val() || '',
            class_status: $f.find('#class_status').val() || '',
            course_flag_id: $f.find('#course_flag_id').val() || '',
            date_from: $f.find('#date_from').val() || '',
            date_to: $f.find('#date_to').val() || '',
            batch_id: $f.find('#batch_id').val() || '',
            admission_batch_id: $f.find('#admission_batch_id').val() || '',
            programme_type: $f.find('#programme_type').length ? ($f.find('#programme_type').val() || '') : '',
            scoped_course_id: SCOPED_COURSE_ID
        };
    }

    function loadAdmissionBatchesByBatchForForm(batchId, selectedId) {
        const $admission = $('#admission_batch_id');
        if (!$admission.length) return;
        $admission.html('<option value="">Loading...</option>');
        if (!batchId) {
            $admission.html('<option value="">All Admission Batches</option>');
            return;
        }
        $.get('/api/admission-batches/by-batch/' + batchId).done(function(list) {
            let opts = '<option value="">All Admission Batches</option>';
            (list || []).forEach(function(i) {
                const sel = String(selectedId) === String(i.id) ? 'selected' : '';
                opts += '<option value="' + i.id + '" ' + sel + '>' + $('<div>').text(i.title).html() + '</option>';
            });
            $admission.html(opts);
        }).fail(function() {
            $admission.html('<option value="">All Admission Batches</option>');
        });
    }

    $(function() {
        const colsEl = document.getElementById('programmeCourseDtColumns-{{ $courseDataTableId }}');
        const columns = colsEl ? JSON.parse(colsEl.textContent || '[]') : [];

        $('#filterForm').addClass('js-programme-scoped-dt-form');

        $('#batch_id').off('change.programmeDt').on('change.programmeDt', function() {
            const bid = $(this).val();
            loadAdmissionBatchesByBatchForForm(bid, '');
            if ($.fn.DataTable.isDataTable(TABLE_SEL)) {
                $(TABLE_SEL).DataTable().ajax.reload();
            }
        });

        $('#filterForm').off('submit.programmeDt').on('submit.programmeDt', function(e) {
            e.preventDefault();
            if ($.fn.DataTable.isDataTable(TABLE_SEL)) {
                $(TABLE_SEL).DataTable().ajax.reload();
            }
        });

        $(TABLE_SEL).removeClass('data_table_basic');
        if ($.fn.DataTable.isDataTable(TABLE_SEL)) {
            $(TABLE_SEL).DataTable().destroy();
        }

        $(TABLE_SEL).DataTable({
            processing: true,
            serverSide: true,
            deferRender: true,
            ajax: {
                url: DATA_URL,
                type: 'GET',
                data: function(d) {
                    $.extend(d, programmeCourseFilterParams());
                }
            },
            pageLength: 25,
            lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
            order: [],
            ordering: false,
            scrollX: true,
            autoWidth: false,
            columns: columns
        });

        const initialBatchId = $('#batch_id').val();
        const initialAdmissionBatchId = $('#admission_batch_id').data('selected');
        if (initialBatchId) {
            loadAdmissionBatchesByBatchForForm(initialBatchId, initialAdmissionBatchId);
        }
    });
})();
</script>
@endpush
