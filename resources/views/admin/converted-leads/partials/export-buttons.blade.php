<div class="btn-group">
    <a
        id="export-excel-btn"
        href="{{ route('admin.converted-leads.universal-export', array_merge(request()->all(), ['page' => $exportPage, 'format' => 'excel'])) }}"
        class="btn btn-success btn-sm"
    >
        <i class="ti ti-file-spreadsheet"></i> Excel
    </a>
    <a
        id="export-pdf-btn"
        href="{{ route('admin.converted-leads.universal-export', array_merge(request()->all(), ['page' => $exportPage, 'format' => 'pdf'])) }}"
        class="btn btn-danger btn-sm"
    >
        <i class="ti ti-file-type-pdf"></i> PDF
    </a>
</div>
