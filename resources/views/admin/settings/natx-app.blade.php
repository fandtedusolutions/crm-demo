@extends('layouts.mantis')

@section('title', 'NatX App Settings')

@section('content')
<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-6">
                <div class="page-header-title">
                    <h5 class="m-b-10">NatX App Settings</h5>
                </div>
            </div>
            <div class="col-md-6">
                <ul class="breadcrumb d-flex justify-content-end">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item">Settings</li>
                    <li class="breadcrumb-item">NatX App</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">NatX App Update Settings</h5>
            </div>
            <div class="card-body">
                <form id="natxAppSettingsForm" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label for="app_version" class="form-label">App Version</label>
                        <input type="text" class="form-control" id="app_version" name="app_version"
                               value="{{ $settings['app_version'] }}" placeholder="e.g. 1.0.1" required>
                        <div class="form-text">Latest version available on the server. App compares this with installed version.</div>
                    </div>

                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="force_update" name="force_update" value="1"
                                   {{ $settings['force_update'] ? 'checked' : '' }}>
                            <label class="form-check-label" for="force_update">Force Update</label>
                        </div>
                        <div class="form-text">If enabled, users on older versions must update before using the app.</div>
                    </div>

                    <div class="mb-3">
                        <label for="download_url" class="form-label">App Download Link (optional)</label>
                        <input type="url" class="form-control" id="download_url" name="download_url"
                               value="{{ $settings['download_url'] }}" placeholder="https://play.google.com/... or direct APK URL">
                        <div class="form-text">Used when no APK is uploaded below. Play Store or external link is fine.</div>
                    </div>

                    <div class="mb-3">
                        <label for="apk_file" class="form-label">Upload APK</label>
                        <input type="file" class="form-control" id="apk_file" name="apk_file" accept=".apk,application/vnd.android.package-archive">
                        <div class="form-text">Upload NatX APK (max 100 MB). Uploaded file takes priority over download link.</div>
                    </div>

                    @if($settings['apk_path'])
                    <div class="alert alert-light border mb-3">
                        <div class="fw-semibold mb-1">Current uploaded APK</div>
                        <div class="small text-break">{{ $settings['apk_url'] }}</div>
                        <button type="button" class="btn btn-outline-danger btn-sm mt-2" id="removeApkBtn">
                            <i class="ti ti-trash"></i> Remove APK
                        </button>
                    </div>
                    @endif

                    <div class="text-end">
                        <button type="submit" class="btn btn-primary" id="saveNatxAppSettingsBtn">
                            <span class="spinner-border spinner-border-sm d-none" role="status"></span>
                            Save Settings
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">API Preview</h5>
            </div>
            <div class="card-body">
                <p class="text-muted small">The NatX app calls this endpoint on launch to check for updates.</p>
                <code class="d-block small mb-3">GET /api/v1/natx/app/version?app_version=1.0.0</code>
                <pre class="bg-light p-3 rounded small mb-0" id="apiPreview">{{ json_encode([
                    'success' => true,
                    'data' => $apiPreview,
                ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(function () {
    $('#natxAppSettingsForm').on('submit', function (e) {
        e.preventDefault();

        const $btn = $('#saveNatxAppSettingsBtn');
        const $spinner = $btn.find('.spinner-border');
        const formData = new FormData(this);
        formData.set('force_update', $('#force_update').is(':checked') ? '1' : '0');

        $btn.prop('disabled', true);
        $spinner.removeClass('d-none');

        $.ajax({
            url: '{{ route('admin.natx-app.settings.update') }}',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                if (response.success) {
                    alert(response.message || 'Settings saved.');
                    window.location.reload();
                }
            },
            error: function (xhr) {
                const message = xhr.responseJSON?.message || 'Failed to save settings.';
                alert(message);
            },
            complete: function () {
                $btn.prop('disabled', false);
                $spinner.addClass('d-none');
            }
        });
    });

    $('#removeApkBtn').on('click', function () {
        if (!confirm('Remove the uploaded APK file?')) {
            return;
        }

        $.post('{{ route('admin.natx-app.settings.remove-apk') }}', {
            _token: '{{ csrf_token() }}'
        }).done(function (response) {
            alert(response.message || 'APK removed.');
            window.location.reload();
        }).fail(function () {
            alert('Failed to remove APK.');
        });
    });
});
</script>
@endpush
