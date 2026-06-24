@extends('layouts.mantis')

@section('title', 'Settings')

@section('content')
<!-- [ breadcrumb ] start -->
<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-6">
                <div class="page-header-title">
                    <h5 class="m-b-10">Website Settings</h5>
                </div>
            </div>
            <div class="col-md-6">
                <ul class="breadcrumb d-flex justify-content-end">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item">Settings</li>
                </ul>
            </div>
        </div>
    </div>
</div>
<!-- [ breadcrumb ] end -->

<!-- [ Main Content ] start -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5>Website Settings</h5>
            </div>
            <div class="card-body">
                <!-- Site Settings -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card border">
                            <div class="card-header">
                                <h6 class="mb-0">Site Information</h6>
                            </div>
                            <div class="card-body">
                                <form id="siteSettingsForm">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="site_name" class="form-label">Site Name</label>
                                                <input type="text" class="form-control" id="site_name" name="site_name" 
                                                       value="{{ $siteSettings['site_name'] }}" required>
                                                <div class="form-text">This will be displayed in the browser title and header</div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="site_description" class="form-label">Site Description</label>
                                                <input type="text" class="form-control" id="site_description" name="site_description" 
                                                       value="{{ $siteSettings['site_description'] }}" required>
                                                <div class="form-text">This will be used for SEO and meta descriptions</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-end">
                                        <button type="submit" class="btn btn-primary" id="updateSiteSettingsBtn">
                                            <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                                            Update Site Settings
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="row">
                    <!-- Logo Settings -->
                        <div class="col-md-6">
                        <div class="card border">
                            <div class="card-header">
                                <h6 class="mb-0">Website Logo</h6>
                            </div>
                            <div class="card-body">
                                <div class="text-center mb-3">
                                    <img id="current-logo" src="{{ asset('storage/logo.png') }}" alt="Current Logo" 
                                         class="img-fluid" style="max-height: 150px; max-width: 300px;"
                                         onerror="this.src='{{ asset('assets/mantis/images/logo.svg') }}'">
                        </div>
                                <form id="logoForm" enctype="multipart/form-data">
                                    @csrf
                            <div class="mb-3">
                                        <label for="logo" class="form-label">Upload New Logo</label>
                                        <input type="file" class="form-control" id="logo" name="logo" accept="image/*" required>
                                        <div class="form-text">Supported formats: JPEG, PNG, JPG, GIF, SVG. Max size: 2MB</div>
                                        <div id="logo_error" class="text-danger mt-1" style="display: none;"></div>
                            </div>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="ti ti-upload"></i> Update Logo
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Favicon Settings -->
                        <div class="col-md-6">
                        <div class="card border">
                            <div class="card-header">
                                <h6 class="mb-0">Website Favicon</h6>
                            </div>
                            <div class="card-body">
                                <div class="text-center mb-3">
                                    <img id="current-favicon" src="{{ asset('storage/favicon.ico') }}" alt="Current Favicon" 
                                         class="img-fluid" style="max-height: 64px; max-width: 64px;"
                                         onerror="this.src='{{ asset('favicon.ico') }}'">
                        </div>
                                <form id="faviconForm" enctype="multipart/form-data">
                                    @csrf
                            <div class="mb-3">
                                        <label for="favicon" class="form-label">Upload New Favicon</label>
                                        <input type="file" class="form-control" id="favicon" name="favicon" accept="image/*,.ico" required>
                                        <div class="form-text">Supported formats: ICO, PNG, JPG, JPEG. Max size: 2MB</div>
                                        <div id="favicon_error" class="text-danger mt-1" style="display: none;"></div>
                            </div>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="ti ti-upload"></i> Update Favicon
                                    </button>
                                </form>
                            </div>
                        </div>
                        </div>
                    </div>

                <!-- Background Image Settings -->
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="card border">
                            <div class="card-header">
                                <h6 class="mb-0">Login Page Background Image</h6>
                            </div>
                            <div class="card-body">
                                <div class="text-center mb-3">
                                    <img id="current-bg-image" src="{{ \App\Helpers\PublicStorageHelper::publicUrl($siteSettings['bg_image']) }}" alt="Current Background Image"
                                         class="img-fluid rounded" style="max-height: 200px; max-width: 400px; border: 1px solid #dee2e6;"
                                         onerror="this.src='{{ asset('assets/mantis/images/auth-bg.jpg') }}'">
                                </div>
                                <form id="bgImageForm" enctype="multipart/form-data">
                                    @csrf
                                    <div class="mb-3">
                                        <label for="bg_image" class="form-label">Upload New Background Image</label>
                                        <input type="file" class="form-control" id="bg_image" name="bg_image" accept="image/*">
                                        <div class="form-text">Supported formats: JPEG, PNG, JPG, GIF, SVG. Max size: 2MB. Recommended: 1920x1080px</div>
                                        <div id="bg_image_error" class="text-danger mt-1" style="display: none;"></div>
                                    </div>
                                    <div class="d-flex gap-2">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="ti ti-upload"></i> Update Background Image
                                        </button>
                                        <!-- <button type="button" class="btn btn-outline-danger" id="removeBgImageBtn">
                                            <i class="ti ti-trash"></i> Remove Background Image
                                        </button> -->
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- [ Main Content ] end -->
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // File size validation for logo
    $('#logo').on('change', function() {
        const file = this.files[0];
        const errorDiv = $('#logo_error');
        
        if (file) {
            const fileSize = file.size / 1024 / 1024; // Convert to MB
            if (fileSize > 2) {
                errorDiv.text('File size must be less than 2MB. Current file size: ' + fileSize.toFixed(2) + 'MB').show();
                this.value = '';
                return false;
            } else {
                errorDiv.hide();
            }
        } else {
            errorDiv.hide();
        }
    });

    // File size validation for favicon
    $('#favicon').on('change', function() {
        const file = this.files[0];
        const errorDiv = $('#favicon_error');
        
        if (file) {
            const fileSize = file.size / 1024 / 1024; // Convert to MB
            if (fileSize > 2) {
                errorDiv.text('File size must be less than 2MB. Current file size: ' + fileSize.toFixed(2) + 'MB').show();
                this.value = '';
                return false;
            } else {
                errorDiv.hide();
            }
        } else {
            errorDiv.hide();
        }
    });

    // File size validation for background image
    $('#bg_image').on('change', function() {
        const file = this.files[0];
        const errorDiv = $('#bg_image_error');
        
        if (file) {
            const fileSize = file.size / 1024 / 1024; // Convert to MB
            if (fileSize > 2) {
                errorDiv.text('File size must be less than 2MB. Current file size: ' + fileSize.toFixed(2) + 'MB').show();
                this.value = '';
                return false;
            } else {
                errorDiv.hide();
            }
        } else {
            errorDiv.hide();
        }
    });

    // Handle logo form submission
    $('#logoForm').on('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const submitBtn = $(this).find('button[type="submit"]');
        const originalText = submitBtn.html();
        
        // Show loading state
        submitBtn.html('<i class="ti ti-loader-2 spin"></i> Uploading...');
        submitBtn.prop('disabled', true);
        
        $.ajax({
            url: '{{ route("admin.website.settings.update-logo") }}',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    toast_success(response.message);
                    // Update logo display
                    $('#current-logo').attr('src', response.logo_url);
                } else {
                    toast_danger(response.message);
                }
            },
            error: function(xhr) {
                const response = xhr.responseJSON;
                if (response && response.message) {
                    toast_danger(response.message);
                } else {
                    toast_danger('An error occurred while updating the logo.');
                }
            },
            complete: function() {
                // Reset button state
                submitBtn.html(originalText);
                submitBtn.prop('disabled', false);
            }
        });
    });

    // Handle favicon form submission
    $('#faviconForm').on('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const submitBtn = $(this).find('button[type="submit"]');
        const originalText = submitBtn.html();
        
        // Show loading state
        submitBtn.html('<i class="ti ti-loader-2 spin"></i> Uploading...');
        submitBtn.prop('disabled', true);
        
        $.ajax({
            url: '{{ route("admin.website.settings.update-favicon") }}',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    toast_success(response.message);
                    // Update favicon display
                    $('#current-favicon').attr('src', response.favicon_url);
                } else {
                    toast_danger(response.message);
                }
            },
            error: function(xhr) {
                const response = xhr.responseJSON;
                if (response && response.message) {
                    toast_danger(response.message);
                } else {
                    toast_danger('An error occurred while updating the favicon.');
                }
            },
            complete: function() {
                // Reset button state
                submitBtn.html(originalText);
                submitBtn.prop('disabled', false);
            }
        });
    });


    // Handle site settings form submission
    $('#siteSettingsForm').on('submit', function(e) {
        e.preventDefault();
        
        const formData = $(this).serialize();
        const submitBtn = $(this).find('button[type="submit"]');
        const originalText = submitBtn.html();
        
        // Show loading state
        submitBtn.html('<i class="ti ti-loader-2 spin"></i> Updating...');
        submitBtn.prop('disabled', true);
        
        $.ajax({
            url: '{{ route("admin.website.settings.update-site-settings") }}',
            type: 'POST',
            data: formData,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    toast_success(response.message);
                } else {
                    toast_danger(response.message);
                }
            },
            error: function(xhr) {
                const response = xhr.responseJSON;
                if (response && response.message) {
                    toast_danger(response.message);
                } else {
                    toast_danger('An error occurred while updating site settings.');
                }
            },
            complete: function() {
                // Reset button state
                submitBtn.html(originalText);
                submitBtn.prop('disabled', false);
            }
        });
    });

    // Handle background image form submission
    $('#bgImageForm').on('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const submitBtn = $(this).find('button[type="submit"]');
        const originalText = submitBtn.html();
        
        // Show loading state
        submitBtn.html('<i class="ti ti-loader-2 spin"></i> Uploading...');
        submitBtn.prop('disabled', true);
        
        $.ajax({
            url: '{{ route("admin.website.settings.update-bg-image") }}',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    // Update the preview image
                    $('#current-bg-image').attr('src', response.bg_image_url);
                    toast_success(response.message);
                } else {
                    toast_danger(response.message);
                }
            },
            error: function(xhr) {
                const response = xhr.responseJSON;
                if (response && response.message) {
                    toast_danger(response.message);
                } else {
                    toast_danger('An error occurred while updating the background image.');
                }
            },
            complete: function() {
                // Reset button state
                submitBtn.html(originalText);
                submitBtn.prop('disabled', false);
            }
        });
    });

    // Apply colors on page load
    applyThemeColors();

    // Handle remove background image
    $('#removeBgImageBtn').on('click', function() {
        if (confirm('Are you sure you want to remove the background image? This will reset it to the default image.')) {
            const removeBtn = $(this);
            const originalText = removeBtn.html();
            
            // Show loading state
            removeBtn.html('<i class="ti ti-loader-2 spin"></i> Removing...');
            removeBtn.prop('disabled', true);
            
            $.ajax({
                url: '{{ route("admin.website.settings.remove-bg-image") }}',
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        toast_success(response.message);
                        // Update the preview image to default
                        $('#current-bg-image').attr('src', '{{ asset("assets/mantis/images/auth-bg.jpg") }}');
                    } else {
                        toast_danger(response.message);
                    }
                },
                error: function(xhr) {
                    const response = xhr.responseJSON;
                    if (response && response.message) {
                        toast_danger(response.message);
                    } else {
                        toast_danger('An error occurred while removing the background image.');
                    }
                },
                complete: function() {
                    // Reset button state
                    removeBtn.html(originalText);
                    removeBtn.prop('disabled', false);
                }
            });
        }
    });
});
</script>

<style>
.spin {
    animation: spin 1s linear infinite;
}

@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}
</style>
@endpush