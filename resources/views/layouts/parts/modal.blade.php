<!-- Normal Modal -->
<div id="small_modal" class="modal fade" tabindex="-1" aria-labelledby="small_modal_label" aria-hidden="true" style="display: none;" role="dialog">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-primary-subtle p-2">
                <h5 class="modal-title" id="small-modal-title"></h5>
                <button type="button" class="btn-close text-danger" data-bs-dismiss="modal" aria-label="Close"> </button>
            </div>
            <div class="modal-body" id="small-modal-content">
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
 
<!-- Ajax Modal -->
<div id="ajax_modal" class="modal fade" tabindex="-1" aria-labelledby="ajax_modal_label" aria-hidden="true" style="display: none;" role="dialog">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-primary-subtle p-2">
                <h5 class="modal-title" id="ajax-modal-title"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> </button>
            </div>
            <div class="modal-body" id="ajax-modal-content">
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<!-- X-Large Modal -->
<div id="large_modal" class="modal fade" tabindex="-1" aria-labelledby="large_modal_label" aria-hidden="true" style="display: none;" role="dialog">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-primary-subtle p-2">
                <h5 class="modal-title" id="large-modal-title"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> </button>
            </div>
            <div class="modal-body" id="large-modal-content">
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<!-- X-Large Modal -->
<div id="full_modal" class="modal fade" tabindex="-1" aria-labelledby="full_modal_label" aria-hidden="true" style="display: none;" role="dialog">
    <div class="modal-dialog modal-fullscreen modal-dialog-scrollable mx-auto" style="max-width: 1200px;">
        <div class="modal-content">
            <div class="modal-header bg-primary-subtle p-2">
                <h5 class="modal-title" id="full-modal-title"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> </button>
            </div>
            <div class="modal-body" id="full-modal-content">
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<script type="text/javascript">
    function show_small_modal(url, header) {
        // SHOWING AJAX PRELOADER IMAGE
        $('#small-modal-content').html('<div style="padding:40px; text-align:center;"><img src="{{ asset("assets/loader.gif") }}" width="150" height="150" alt="Loading..."></div>');
        $('#small-modal-title').html('Loading...');
        // LOADING THE AJAX MODAL
        $('#small_modal').modal({
            backdrop: 'static',
            keyboard: false
        });
        $('#small_modal').modal('show');

        // SHOW AJAX RESPONSE ON REQUEST SUCCESS
        call_ajax_view(url, '#small-modal-content');
        $('#small-modal-title').html(header);
    }

    $('#small_modal').on('hidden.bs.modal', function() {
        $('#small-modal-content').empty();
    });

    function show_ajax_modal(url, header, leadId = null) {
        // SHOWING AJAX PRELOADER IMAGE
        $('#ajax-modal-content').html('<div style="padding:40px; text-align:center;"><img src="{{ asset("assets/loader.gif") }}" width="150" height="150" alt="Loading..."></div>');
        $('#ajax-modal-title').html('Loading...');
        
        // Store lead ID in modal data attribute if provided
        if (leadId) {
            $('#ajax_modal').attr('data-lead-id', leadId);
        }
        
        // LOADING THE AJAX MODAL
        $('#ajax_modal').modal({
            backdrop: 'static',
            keyboard: false
        });
        $('#ajax_modal').modal('show');
        call_ajax_view(url, '#ajax-modal-content');
        $('#ajax-modal-title').html(header);
    }

    function show_large_modal(url, header) {
        // SHOWING AJAX PRELOADER IMAGE
        $('#large-modal-content').html('<div style="padding:40px; text-align:center;"><img src="{{ asset("assets/loader.gif") }}" width="150" height="150" alt="Loading..."></div>');
        $('#large-modal-title').html('Loading...');
        // LOADING THE AJAX MODAL
        $('#large_modal').modal({
            backdrop: 'static',
            keyboard: false
        });
        $('#large_modal').modal('show');

        // SHOW AJAX RESPONSE ON REQUEST SUCCESS
        call_ajax_view(url, '#large-modal-content');
        $('#large-modal-title').html(header);
    }

    function show_full_modal(url, header) {
        // SHOWING AJAX PRELOADER IMAGE
        $('#full-modal-content').html('<div style="padding:40px; text-align:center;"><img src="{{ asset("assets/loader.gif") }}" width="150" height="150" alt="Loading..."></div>');
        $('#full-modal-title').html('Loading...');
        // LOADING THE AJAX MODAL
        $('#full_modal').modal({
            backdrop: 'static',
            keyboard: false
        });
        $('#full_modal').modal('show');

        // SHOW AJAX RESPONSE ON REQUEST SUCCESS
        call_ajax_view(url, '#full-modal-content');
        $('#full-modal-title').html(header);
    }

    function alert_modal_success(message = '', message_title = 'Success!', cancel_button = 'Okay') {
        Swal.fire({
            html: '<div class="mt-3">' +
                '<lord-icon src="https://cdn.lordicon.com/lupuorrc.json" trigger="loop" colors="primary:#0ab39c,secondary:#405189" style="width:120px;height:120px"></lord-icon>' +
                '<div class="mt-4 pt-2 fs-15">' +
                '<h4>' + message_title + '</h4>' +
                '<p class="text-muted mx-4 mb-0">' + message + '</p>' +
                '</div>' +
                '</div>',
            showCancelButton: true,
            showConfirmButton: false,
            cancelButtonClass: 'btn btn-success w-xs mb-1',
            cancelButtonText: cancel_button,
            buttonsStyling: false,
            showCloseButton: true
        })
    }

    function alert_modal_error(message = 'Something went wrong..!', cancel_button = 'Okay') {
        Swal.fire({
            html: '<div class="mt-3">' +
                '<lord-icon src="https://cdn.lordicon.com/tdrtiskw.json" trigger="loop" colors="primary:#f06548,secondary:#f7b84b" style="width:120px;height:120px"></lord-icon>' +
                '<div class="mt-4 pt-2 fs-15">' +
                '<h2>Oops...!</h2>' +
                '<p class="text-muted mx-4 mb-0">' + message +'</p>' +
                '</div>' +
                '</div>',
            showCancelButton: true,
            showConfirmButton: false,
            cancelButtonClass: 'btn btn-danger1 btn-outline-danger w-xs mb-1',
            cancelButtonText: 'Dismiss',
            buttonsStyling: false,
            showCloseButton: true
        })
    }

    function confirm_modal(
        message = 'Are you Sure ?',
        message_description = 'Are you Sure You want to Delete this Account ?',
        button_text = 'Yes, Delete It!'
    ) {
        Swal.fire({
            html: '<div class="mt-3">' +
                '<lord-icon src="https://cdn.lordicon.com/gsqxdxog.json" trigger="loop" colors="primary:#f7b84b,secondary:#f06548" style="width:100px;height:100px"></lord-icon>' +
                '<div class="mt-4 pt-2 fs-15 mx-5">' +
                '<h4>' + message + '</h4>' +
                '<p class="text-muted mx-4 mb-0"> ' + message_description + '</p>' +
                '</div>' +
                '</div>',
            showCancelButton: true,
            confirmButtonClass: 'btn btn-primary w-xs me-2 mb-1',
            confirmButtonText: button_text,
            cancelButtonClass: 'btn btn-danger w-xs mb-1',
            buttonsStyling: false,
            showCloseButton: true
        })
    }

    function delete_modal(delete_url, message = 'Are you sure?') {
        Swal.fire({
            title: message,
            text: "This action cannot be undone!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel',
            reverseButtons: true,
            allowOutsideClick: false,
            allowEscapeKey: false,
            preConfirm: () => {
                const params = new URLSearchParams();
                params.append('_method', 'DELETE');

                return fetch(delete_url, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                        'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: params
                }).then(async response => {
                    let data = {};
                    try {
                        data = await response.json();
                    } catch (e) {
                        // non-JSON response
                    }
                    if (!response.ok || (data && data.success === false)) {
                        const msg = (data && (data.message || data.error)) || 'Delete failed.';
                        throw new Error(msg);
                    }
                    return data;
                }).catch(error => {
                    Swal.showValidationMessage(error.message || 'Delete failed.');
                });
            }
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire('Deleted!', 'Item has been deleted.', 'success').then(() => {
                    location.reload();
                });
            }
        });
    }


    // AJAX function to load content
    function call_ajax_view(url, target) {
        $.ajax({
            url: url,
            type: 'GET',
            success: function(response) {
                $(target).html(response);
                $(target).find('script').each(function() {
                    if (this.src) {
                        const src = this.getAttribute('src');
                        if (!src || document.querySelector('script[src="' + src + '"]')) {
                            return;
                        }
                        const tag = document.createElement('script');
                        tag.src = src;
                        tag.async = false;
                        document.head.appendChild(tag);
                        return;
                    }

                    const code = this.text || this.textContent || '';
                    if (!code.trim()) {
                        return;
                    }
                    $.globalEval(code);
                });
            },
            error: function(xhr, status, error) {
                $(target).html('<div class="alert alert-danger">Error loading content: ' + error + '</div>');
            }
        });
    }
</script>
