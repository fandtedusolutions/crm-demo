@extends('layouts.mantis')

@section('title', 'Course Mapping')

@section('content')
<style>
    .course-mapping-page .stat-card {
        position: relative;
        overflow: hidden;
        border: 0;
        border-radius: 12px;
        background: #fff;
        box-shadow: 0 1px 2px rgba(29, 38, 48, 0.04), 0 8px 24px rgba(29, 38, 48, 0.06);
        height: 100%;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .course-mapping-page .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 2px 4px rgba(29, 38, 48, 0.05), 0 12px 28px rgba(29, 38, 48, 0.08);
    }
    .course-mapping-page .stat-card .stat-card-body {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        padding: 1.25rem 1.35rem;
        min-height: 108px;
    }
    .course-mapping-page .stat-card::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        width: 4px;
    }
    .course-mapping-page .stat-card.total::before { background: #4680ff; }
    .course-mapping-page .stat-card.mapped::before { background: #2ca87f; }
    .course-mapping-page .stat-card.unmapped::before { background: #e58a00; }
    .course-mapping-page .stat-card .stat-label {
        color: #6c757d;
        font-size: 0.8125rem;
        font-weight: 500;
        margin-bottom: 0.35rem;
        letter-spacing: 0.01em;
    }
    .course-mapping-page .stat-card .stat-value {
        font-size: 1.75rem;
        font-weight: 700;
        line-height: 1.1;
        color: #1d2630;
        margin-bottom: 0.2rem;
    }
    .course-mapping-page .stat-card .stat-hint {
        color: #8b95a3;
        font-size: 0.75rem;
        margin: 0;
    }
    .course-mapping-page .stat-card .stat-icon {
        width: 52px;
        height: 52px;
        border-radius: 14px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 1.4rem;
        flex-shrink: 0;
    }
    .course-mapping-page .stat-card.total .stat-icon { background: rgba(70, 128, 255, 0.12); color: #4680ff; }
    .course-mapping-page .stat-card.mapped .stat-icon { background: rgba(44, 168, 127, 0.12); color: #2ca87f; }
    .course-mapping-page .stat-card.unmapped .stat-icon { background: rgba(229, 138, 0, 0.12); color: #e58a00; }
    .course-mapping-page .stat-card.total .stat-value { color: #4680ff; }
    .course-mapping-page .stat-card.mapped .stat-value { color: #2ca87f; }
    .course-mapping-page .stat-card.unmapped .stat-value { color: #e58a00; }
    .course-mapping-page .intro-banner {
        border: 1px solid #e7ecf3;
        border-radius: 12px;
        background: linear-gradient(135deg, #f5f9ff 0%, #ffffff 60%);
        padding: 1.1rem 1.25rem;
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.8);
    }
    .course-mapping-page .intro-banner .stat-icon {
        width: 42px;
        height: 42px;
        border-radius: 12px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        background: rgba(70, 128, 255, 0.12);
        color: #4680ff;
        flex-shrink: 0;
    }
    .course-mapping-page .mapping-card {
        border: 0;
        border-radius: 12px;
        box-shadow: 0 1px 2px rgba(29, 38, 48, 0.04), 0 8px 24px rgba(29, 38, 48, 0.06);
        overflow: hidden;
    }
    .course-mapping-page .mapping-card .card-header {
        background: #fff;
        border-bottom: 1px solid #eef2f6;
        padding: 1.15rem 1.35rem;
    }
    .course-mapping-page .lms-id-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 42px;
        padding: 0.2rem 0.55rem;
        border-radius: 6px;
        background: #f1f4f9;
        color: #495057;
        font-weight: 600;
        font-size: 0.8125rem;
    }
    .course-mapping-page .lms-title {
        font-weight: 600;
        color: #1d2630;
    }
    .course-mapping-page .mapped-course-name {
        font-size: 0.8rem;
        color: #2ca87f;
        margin-top: 0.35rem;
    }
    .course-mapping-page .status-pill {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.3rem 0.65rem;
        border-radius: 50px;
        font-size: 0.75rem;
        font-weight: 600;
    }
    .course-mapping-page .status-pill.mapped {
        background: #e8f8ef;
        color: #2ca87f;
    }
    .course-mapping-page .status-pill.unmapped {
        background: #fff4e6;
        color: #c77700;
    }
    .course-mapping-page .mapping-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
        align-items: center;
    }
    .course-mapping-page .empty-state {
        text-align: center;
        padding: 3rem 1.5rem;
        border: 1px dashed #dbe3ef;
        border-radius: 12px;
        background: #fafbfd;
    }
    .course-mapping-page .empty-state .empty-icon {
        width: 64px;
        height: 64px;
        margin: 0 auto 1rem;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #e8f3ff;
        color: #2f63ff;
        font-size: 1.75rem;
    }
    .course-mapping-page #courseMappingTable td {
        vertical-align: middle;
    }
    .course-mapping-page .crm-course-select {
        min-width: 220px;
        border-radius: 8px;
        border-color: #dbe3ef;
        padding-top: 0.45rem;
        padding-bottom: 0.45rem;
    }
    .course-mapping-page .crm-course-select:focus {
        border-color: #4680ff;
        box-shadow: 0 0 0 0.2rem rgba(70, 128, 255, 0.15);
    }
    .course-mapping-page .cm-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.4rem;
        border-radius: 8px;
        font-weight: 600;
        font-size: 0.8125rem;
        line-height: 1.2;
        padding: 0.5rem 0.95rem;
        border: 1px solid transparent;
        transition: all 0.2s ease;
        white-space: nowrap;
        box-shadow: none;
    }
    .course-mapping-page .cm-btn i {
        font-size: 1rem;
        line-height: 1;
    }
    .course-mapping-page .cm-btn:disabled {
        opacity: 0.7;
        cursor: not-allowed;
    }
    .course-mapping-page .cm-btn-refresh {
        background: #4680ff;
        color: #fff;
        box-shadow: 0 4px 12px rgba(70, 128, 255, 0.28);
    }
    .course-mapping-page .cm-btn-refresh:hover,
    .course-mapping-page .cm-btn-refresh:focus {
        background: #3469e0;
        color: #fff;
        box-shadow: 0 6px 16px rgba(70, 128, 255, 0.35);
        transform: translateY(-1px);
    }
    .course-mapping-page .cm-btn-map {
        background: #e8f8ef;
        color: #2ca87f;
        border-color: #cfeedd;
    }
    .course-mapping-page .cm-btn-map:hover,
    .course-mapping-page .cm-btn-map:focus {
        background: #2ca87f;
        color: #fff;
        border-color: #2ca87f;
    }
    .course-mapping-page .cm-btn-unmap {
        background: #fff;
        color: #dc2626;
        border-color: #f3c4c4;
    }
    .course-mapping-page .cm-btn-unmap:hover,
    .course-mapping-page .cm-btn-unmap:focus {
        background: #dc2626;
        color: #fff;
        border-color: #dc2626;
    }
    .course-mapping-page .cm-btn-lg {
        padding: 0.65rem 1.25rem;
        font-size: 0.875rem;
        border-radius: 10px;
    }
</style>

<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-6">
                <div class="page-header-title">
                    <h5 class="m-b-10">Course Mapping</h5>
                </div>
            </div>
            <div class="col-md-6">
                <ul class="breadcrumb d-flex justify-content-end">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item">LMS Setup</li>
                    <li class="breadcrumb-item">Course Mapping</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<div class="course-mapping-page">
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-4">
            <div class="stat-card total">
                <div class="stat-card-body">
                    <div>
                        <div class="stat-label">LMS Courses</div>
                        <div class="stat-value">{{ $stats['total'] }}</div>
                        <p class="stat-hint">Synced from LMS API</p>
                    </div>
                    <span class="stat-icon"><i class="ti ti-books"></i></span>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-4">
            <div class="stat-card mapped">
                <div class="stat-card-body">
                    <div>
                        <div class="stat-label">Mapped</div>
                        <div class="stat-value">{{ $stats['mapped'] }}</div>
                        <p class="stat-hint">Linked to CRM courses</p>
                    </div>
                    <span class="stat-icon"><i class="ti ti-link"></i></span>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-4">
            <div class="stat-card unmapped">
                <div class="stat-card-body">
                    <div>
                        <div class="stat-label">Unmapped</div>
                        <div class="stat-value">{{ $stats['unmapped'] }}</div>
                        <p class="stat-hint">Waiting to be connected</p>
                    </div>
                    <span class="stat-icon"><i class="ti ti-unlink"></i></span>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card mapping-card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
                        <div>
                            <h5 class="mb-1">LMS ↔ CRM Mapping</h5>
                            <small class="text-muted">Connect each LMS course to its matching CRM course</small>
                        </div>
                        <button type="button" id="refreshLmsCoursesBtn" class="cm-btn cm-btn-refresh">
                            <i class="ti ti-refresh"></i>
                            <span>Refresh from LMS</span>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="intro-banner mb-4">
                        <div class="d-flex align-items-start gap-3">
                            <span class="stat-icon"><i class="ti ti-info-circle"></i></span>
                            <div>
                                <div class="fw-semibold mb-1">How it works</div>
                                <div class="text-muted small mb-0">
                                    Use <strong>Refresh from LMS</strong> to sync courses from the LMS API.
                                    Only new or renamed courses are updated. Then select a CRM course and click
                                    <strong>Map</strong> to save the connection.
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($lmsCourses->isEmpty())
                        <div class="empty-state">
                            <div class="empty-icon">
                                <i class="ti ti-cloud-download"></i>
                            </div>
                            <h6 class="mb-2">No LMS courses yet</h6>
                            <p class="text-muted mb-3">
                                Pull the latest courses from your LMS API to start mapping.
                            </p>
                            <button type="button" class="cm-btn cm-btn-refresh cm-btn-lg" id="refreshLmsCoursesBtnEmpty">
                                <i class="ti ti-refresh"></i>
                                <span>Refresh from LMS</span>
                            </button>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover align-middle" id="courseMappingTable">
                                <thead>
                                    <tr>
                                        <th style="width: 50px;">#</th>
                                        <th style="width: 90px;">LMS ID</th>
                                        <th>LMS Course</th>
                                        <th style="width: 120px;">Status</th>
                                        <th>CRM Course</th>
                                        <th style="width: 200px;">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($lmsCourses as $lmsCourse)
                                        @php $isMapped = (bool) $lmsCourse->mapping; @endphp
                                        <tr data-lms-course-id="{{ $lmsCourse->id }}">
                                            <td class="text-muted">{{ $loop->iteration }}</td>
                                            <td>
                                                <span class="lms-id-badge">#{{ $lmsCourse->id }}</span>
                                            </td>
                                            <td>
                                                <div class="lms-title">{{ $lmsCourse->title }}</div>
                                                @if($isMapped)
                                                    <div class="mapped-course-name">
                                                        <i class="ti ti-arrow-right me-1"></i>{{ $lmsCourse->mapping->course?->title ?? 'Mapped' }}
                                                    </div>
                                                @endif
                                            </td>
                                            <td>
                                                @if($isMapped)
                                                    <span class="status-pill mapped">
                                                        <i class="ti ti-check"></i> Mapped
                                                    </span>
                                                @else
                                                    <span class="status-pill unmapped">
                                                        <i class="ti ti-alert-circle"></i> Pending
                                                    </span>
                                                @endif
                                            </td>
                                            <td>
                                                <select class="form-select form-select-sm crm-course-select"
                                                    data-lms-course-id="{{ $lmsCourse->id }}">
                                                    <option value="">Select CRM course</option>
                                                    @foreach($courses as $course)
                                                        <option value="{{ $course->id }}"
                                                            {{ (int) ($lmsCourse->mapping?->course_id ?? 0) === (int) $course->id ? 'selected' : '' }}>
                                                            {{ $course->title }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td>
                                                <div class="mapping-actions">
                                                    <button type="button"
                                                        class="cm-btn cm-btn-map save-mapping-btn"
                                                        data-lms-course-id="{{ $lmsCourse->id }}"
                                                        data-map-url="{{ route('admin.course-mapping.map', $lmsCourse->id) }}">
                                                        <i class="ti ti-{{ $isMapped ? 'refresh' : 'link' }}"></i>
                                                        <span>{{ $isMapped ? 'Update' : 'Map' }}</span>
                                                    </button>
                                                    @if($isMapped)
                                                        <button type="button"
                                                            class="cm-btn cm-btn-unmap unmap-btn"
                                                            data-unmap-url="{{ route('admin.course-mapping.unmap', $lmsCourse->id) }}">
                                                            <i class="ti ti-unlink"></i>
                                                            <span>Unmap</span>
                                                        </button>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function () {
    const refreshUrl = @json(route('admin.course-mapping.refresh'));
    const csrfToken = $('meta[name="csrf-token"]').attr('content');

    function refreshLmsCourses(btn) {
        const originalHtml = btn.html();

        btn.prop('disabled', true);
        btn.html('<i class="ti ti-loader-2 spin"></i><span>Refreshing...</span>');

        $.ajax({
            url: refreshUrl,
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            success: function (response) {
                if (response.success) {
                    toast_success(response.message);
                    setTimeout(function () {
                        window.location.reload();
                    }, 800);
                } else {
                    toast_danger(response.message || 'Failed to refresh LMS courses.');
                    btn.prop('disabled', false);
                    btn.html(originalHtml);
                }
            },
            error: function (xhr) {
                toast_danger(xhr.responseJSON?.message || 'Failed to refresh LMS courses.');
                btn.prop('disabled', false);
                btn.html(originalHtml);
            }
        });
    }

    $('#refreshLmsCoursesBtn, #refreshLmsCoursesBtnEmpty').on('click', function () {
        refreshLmsCourses($(this));
    });

    if ($.fn.DataTable && $('#courseMappingTable').length) {
        $('#courseMappingTable').DataTable({
            order: [[2, 'asc']],
            pageLength: 25,
            columnDefs: [
                { orderable: false, targets: [4, 5] }
            ],
            language: {
                search: '',
                searchPlaceholder: 'Search LMS courses...'
            }
        });
    }

    $(document).on('click', '.save-mapping-btn', function () {
        const btn = $(this);
        const lmsCourseId = btn.data('lms-course-id');
        const mapUrl = btn.data('map-url');
        const select = $('.crm-course-select[data-lms-course-id="' + lmsCourseId + '"]');
        const courseId = select.val();
        const originalHtml = btn.html();

        if (!courseId) {
            toast_danger('Please select a CRM course to map.');
            return;
        }

        btn.prop('disabled', true);
        btn.html('<i class="ti ti-loader-2 spin"></i><span>Saving...</span>');

        $.ajax({
            url: mapUrl,
            type: 'POST',
            data: {
                course_id: courseId,
                _token: csrfToken
            },
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            success: function (response) {
                if (response.success) {
                    toast_success(response.message);
                    setTimeout(function () {
                        window.location.reload();
                    }, 800);
                } else {
                    toast_danger(response.message || 'Failed to save mapping.');
                    btn.prop('disabled', false);
                    btn.html(originalHtml);
                }
            },
            error: function (xhr) {
                toast_danger(xhr.responseJSON?.message || 'Failed to save mapping.');
                btn.prop('disabled', false);
                btn.html(originalHtml);
            }
        });
    });

    $(document).on('click', '.unmap-btn', function () {
        const btn = $(this);
        const unmapUrl = btn.data('unmap-url');
        const originalHtml = btn.html();

        if (!confirm('Remove this course mapping?')) {
            return;
        }

        btn.prop('disabled', true);
        btn.html('<i class="ti ti-loader-2 spin"></i><span>Removing...</span>');

        $.ajax({
            url: unmapUrl,
            type: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            success: function (response) {
                if (response.success) {
                    toast_success(response.message);
                    setTimeout(function () {
                        window.location.reload();
                    }, 800);
                } else {
                    toast_danger(response.message || 'Failed to remove mapping.');
                    btn.prop('disabled', false);
                    btn.html(originalHtml);
                }
            },
            error: function (xhr) {
                toast_danger(xhr.responseJSON?.message || 'Failed to remove mapping.');
                btn.prop('disabled', false);
                btn.html(originalHtml);
            }
        });
    });
});
</script>
@endpush
