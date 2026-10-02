<!--   Core JS Files   -->
<!-- jQuery is already loaded in main layout -->
<script src="{{ asset('assets/js/core/popper.min.js') }}"></script>
<script src="{{ asset('assets/js/core/bootstrap.min.js') }}"></script>

<!-- jQuery Scrollbar -->
<script src="{{ asset('assets/js/plugin/jquery-scrollbar/jquery.scrollbar.min.js') }}"></script>

<!-- Chart JS -->
<script src="{{ asset('assets/js/plugin/chart.js/chart.min.js') }}"></script>

<!-- jQuery Sparkline -->
<script src="{{ asset('assets/js/plugin/jquery.sparkline/jquery.sparkline.min.js') }}"></script>

<!-- Chart Circle -->
<script src="{{ asset('assets/js/plugin/chart-circle/circles.min.js') }}"></script>

<!-- Datatables -->
<script src="{{ asset('assets/js/plugin/datatables/datatables.min.js') }}"></script>

<!-- Bootstrap Notify -->
<script src="{{ asset('assets/js/plugin/bootstrap-notify/bootstrap-notify.min.js') }}"></script>

<!-- jQuery Vector Map - removed as not used in current layout -->

<!-- Sweet Alert -->
<script src="{{ asset('assets/js/plugin/sweetalert/sweetalert.min.js') }}"></script>

<!-- Kaiadmin JS -->
<script src="{{ asset('assets/js/kaiadmin.min.js') }}"></script>


<!-- Include Toastify -->
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>

<!-- Telecaller Tracking Script -->
@if(is_telecaller())
<script src="{{ asset('assets/js/telecaller-tracking.js') }}"></script>
<script>
    // Set user role for tracking script
    window.userRoleId = {{ \App\Helpers\AuthHelper::getRoleId() }};
    
    // Set environment variables for tracking script
    window.disableWorkingHoursCheck = {{ app()->environment('local', 'development', 'testing') ? 'true' : 'false' }};
    window.appEnvironment = '{{ app()->environment() }}';
</script>
@endif

<!-- Voxbay Calling Integration -->
<script src="{{ asset('assets/js/voxbay.js') }}"></script>

<!-- Custom Scripts -->
<script>
    // Global DataTable initialization function
    function initializeTables() {
        // Initialize all tables with data_table_basic or datatable class
        $('.data_table_basic, .datatable').each(function() {
            var $table = $(this);
            var tableId = $table.attr('id') || 'table_' + Math.random().toString(36).substr(2, 9);
            var lastPageKey = 'lastPage_' + tableId;
            var lastPage = localStorage.getItem(lastPageKey);
            var defaultOrder = $table.data('order');
            var pageLength = parseInt($table.data('page-length'), 10);
            var parsedOrder = null;
            var effectivePageLength = !isNaN(pageLength) && pageLength > 0 ? pageLength : 25;
            var lastPageIndex = lastPage !== null ? parseInt(lastPage, 10) : null;

            if (defaultOrder) {
                try {
                    parsedOrder = JSON.parse(defaultOrder);
                } catch (e) {
                    console.warn('Invalid data-order attribute for table', tableId, defaultOrder);
                }
            }
            
            // Initialize the DataTable
            var table = new DataTable(this, {
                dom: "Bfrtip",
                buttons: ["csv", "excel", "print", "pdf"],
                pagingType: "full_numbers",
                scrollCollapse: true,
                paging: true,
                stateSave: true,
                stateDuration: -1,
                pageLength: effectivePageLength,
                lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
                order: parsedOrder || [[0, 'asc']],
                
                // Set the initial page if there was a stored page
                "displayStart": lastPageIndex !== null ? lastPageIndex * effectivePageLength : 0,  // Multiply by page size
                
                // Language configuration
                language: {
                    processing: "Loading...",
                    emptyTable: "No data available",
                    zeroRecords: "No matching records found",
                    lengthMenu: "Show _MENU_ entries",
                    info: "Showing _START_ to _END_ of _TOTAL_ entries",
                    infoEmpty: "Showing 0 to 0 of 0 entries",
                    infoFiltered: "(filtered from _MAX_ total entries)",
                    search: "Search:",
                    paginate: {
                        first: "First",
                        last: "Last",
                        next: "Next",
                        previous: "Previous"
                    }
                }
            });
            
            // Listen for the page change event and store the current page
            table.on('page', function() {
                var pageInfo = table.page.info();
                localStorage.setItem(lastPageKey, pageInfo.page);
            });
        });
    }

    $(document).ready(function() {
        $.ajaxPrefilter(function (options) {
            var listingCourseId = $('.inline-edit.editing').attr('data-listing-course-id');
            if (!listingCourseId) {
                return;
            }
            if (typeof options.data === 'string') {
                if (options.data.indexOf('field=batch_id') === -1 && options.data.indexOf('field=admission_batch_id') === -1) {
                    return;
                }
                if (options.data.indexOf('listing_course_id=') !== -1) {
                    return;
                }
                options.data += (options.data.length ? '&' : '') + 'listing_course_id=' + encodeURIComponent(listingCourseId);
                return;
            }
            if ($.isPlainObject(options.data)) {
                if (options.data.field !== 'batch_id' && options.data.field !== 'admission_batch_id') {
                    return;
                }
                if (!options.data.listing_course_id) {
                    options.data.listing_course_id = listingCourseId;
                }
            }
        });

        // Initialize any custom functionality here
        // console.log('CRM Dashboard loaded successfully');
        
        // Initialize global DataTables
        initializeTables();
        
        // Initialize sidebar toggle
        $('.toggle-sidebar').click(function() {
            $('body').toggleClass('sidebar-collapse');
        });
        
        // Initialize Bootstrap dropdowns
        $('.dropdown-toggle').dropdown();
        
        // Initialize Bootstrap dropdowns with proper event handling
        $('[data-bs-toggle="dropdown"]').on('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            $(this).next('.dropdown-menu').toggle();
        });
        
        // Close dropdowns when clicking outside
        $(document).on('click', function(e) {
            if (!$(e.target).closest('.dropdown').length) {
                $('.dropdown-menu').hide();
            }
        });
        
        // Initialize search functionality
        $('.btn-search').click(function(e) {
            e.preventDefault();
            var searchTerm = $(this).siblings('input').val();
            if (searchTerm) {
                console.log('Searching for:', searchTerm);
                // Add search functionality here
            }
        });
        
        // Initialize tooltips
        $('[data-bs-toggle="tooltip"]').tooltip();
        
        // Initialize popovers
        $('[data-bs-toggle="popover"]').popover();
        
        // Initialize Select2 globally for elements with select2 class
        function initializeGlobalSelect2() {
            if (typeof $.fn.select2 !== 'undefined') {
                // Initialize single selects (but not the telecaller dropdown)
                $('.select2:not(#telecaller)').each(function() {
                    if (!$(this).hasClass('select2-hidden-accessible')) {
                        $(this).select2({
                            placeholder: 'Select an option...',
                            allowClear: true,
                            width: '100%'
                        });
                    }
                });
                
                // Initialize multiple selects (but not the telecaller dropdown)
                $('.select2-multiple:not(#telecaller)').each(function() {
                    if (!$(this).hasClass('select2-hidden-accessible')) {
                        $(this).select2({
                            placeholder: 'Select options...',
                            allowClear: true,
                            width: '100%'
                        });
                    }
                });
                
                return true;
            } else {
                return false;
            }
        }
        
        // Try to initialize immediately
        if (!initializeGlobalSelect2()) {
            // If failed, retry after a short delay
            setTimeout(initializeGlobalSelect2, 200);
        }
    });

    // Show toast messages from session
    @if(session()->has('message_success'))
        toast_success(`{!! session('message_success') !!}`);
    @endif

    @if(session()->has('message_warning'))
        toast_warning(`{!! session('message_warning') !!}`);
    @endif

    @if(session()->has('message_danger'))
        toast_error(`{!! session('message_danger') !!}`);
    @endif

    @if(session()->has('message_primary'))
        toast_primary(`{!! session('message_primary') !!}`);
    @endif

    // Toast Success
    function toast_success(message, duration = 3000) {
        var myToastContent = document.createElement('div');
        myToastContent.innerHTML = '<div style="width:320px; color: white; font-weight: 500;">' + message + '</div>';
        Toastify({
            node: myToastContent,
            gravity: "top",
            position: "right",
            className: "success",
            duration: duration,
            offset: {
                x: 20,
                y: 80
            },
            style: {
                background: "#39B39C",
                borderRadius: "8px",
                boxShadow: "0 4px 12px rgba(0,0,0,0.15)",
                zIndex: 9999
            }
        }).showToast();
    }

    // Toast Warning
    function toast_warning(message, duration = 3000) {
        var myToastContent = document.createElement('div');
        myToastContent.innerHTML = '<div style="width:320px; color: white; font-weight: 500;">' + message + '</div>';
        Toastify({
            node: myToastContent,
            gravity: "top",
            position: "right",
            className: "warning",
            duration: duration,
            offset: {
                x: 20,
                y: 80
            },
            style: {
                background: "#F6B84B",
                borderRadius: "8px",
                boxShadow: "0 4px 12px rgba(0,0,0,0.15)",
                zIndex: 9999
            }
        }).showToast();
    }

    // Toast Error
    function toast_error(message, duration = 3000) {
        var myToastContent = document.createElement('div');
        myToastContent.innerHTML = '<div style="width:320px; color: white; font-weight: 500;">' + message + '</div>';
        Toastify({
            node: myToastContent,
            gravity: "top",
            position: "right",
            className: "danger",
            duration: duration,
            offset: {
                x: 20,
                y: 80
            },
            style: {
                background: "#EF6547",
                borderRadius: "8px",
                boxShadow: "0 4px 12px rgba(0,0,0,0.15)",
                zIndex: 9999
            }
        }).showToast();
    }

    // Toast Danger (alias for toast_error)
    function toast_danger(message, duration = 3000) {
        toast_error(message, duration);
    }

    // Toast Primary
    function toast_primary(message, duration = 3000) {
        var myToastContent = document.createElement('div');
        myToastContent.innerHTML = '<div style="width:320px; color: white; font-weight: 500;">' + message + '</div>';
        Toastify({
            node: myToastContent,
            gravity: "top",
            position: "right",
            className: "primary",
            duration: duration,
            offset: {
                x: 20,
                y: 80
            },
            style: {
                background: "#007bff",
                borderRadius: "8px",
                boxShadow: "0 4px 12px rgba(0,0,0,0.15)",
                zIndex: 9999
            }
        }).showToast();
    }

    // Notification system
    function loadNotifications() {
        // Check if notification elements exist on the page
        const notificationDropdown = document.getElementById('notificationDropdown');
        if (!notificationDropdown) {
            console.log('Notification dropdown not found, skipping notification load');
            return;
        }

        fetch('{{ route("notifications.api") }}')
            .then(async response => {
                const contentType = response.headers.get('content-type') || '';
                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}`);
                }
                if (!contentType.includes('application/json')) {
                    // Avoid parsing HTML error pages like "<!DOCTYPE ..."
                    throw new Error('Non-JSON response');
                }
                return response.json();
            })
            .then(data => {
                const notificationList = document.getElementById('notificationList');
                const notificationLoading = document.getElementById('notificationLoading');
                const notificationEmpty = document.getElementById('notificationEmpty');
                
                // Check if elements exist before accessing their properties
                if (notificationLoading) {
                    notificationLoading.style.display = 'none';
                }
                
                if (data.notifications && data.notifications.length > 0) {
                    if (notificationEmpty) {
                        notificationEmpty.style.display = 'none';
                    }
                    
                    // Render notifications
                    if (notificationList) {
                        notificationList.innerHTML = '';
                        
                        data.notifications.forEach(notification => {
                            const notificationItem = document.createElement('div');
                            notificationItem.className = `list-group-item list-group-item-action ${notification.is_read ? '' : 'bg-light'}`;
                            notificationItem.setAttribute('data-notification-id', notification.id);
                            notificationItem.style.cssText = 'padding: 12px 16px; margin-bottom: 8px; border-radius: 8px; border: 1px solid #e9ecef;';
                            
                            notificationItem.innerHTML = `
                                <div class="d-flex">
                                    <div class="flex-shrink-0 me-3">
                                        <div class="notification-icon bg-${getNotificationColor(notification.type)} text-white rounded-circle d-flex align-items-center justify-content-center" 
                                             style="width: 40px; height: 40px; min-width: 40px;">
                                            <i class="ti ti-${getNotificationIcon(notification.type)}" style="font-size: 18px;"></i>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="d-flex justify-content-between align-items-start mb-1">
                                            <h6 class="mb-0 ${notification.is_read ? 'text-muted' : 'fw-semibold'}" style="font-size: 14px;">${notification.title}</h6>
                                            <small class="text-muted ms-2" style="white-space: nowrap;">${notification.created_at}</small>
                                        </div>
                                        <div class="text-body mb-1 ${notification.is_read ? 'text-muted' : ''}" style="font-size: 13px; line-height: 1.4;"></div>
                                        <small class="text-muted" style="font-size: 11px;">by ${notification.created_by}</small>
                                    </div>
                                </div>
                            `;
                            
                            // Set the message HTML content properly
                            const messageDiv = notificationItem.querySelector('.text-body');
                            if (messageDiv) {
                                messageDiv.innerHTML = notification.message;
                            }
                            
                            notificationList.appendChild(notificationItem);
                        });
                    }
                } else {
                    if (notificationEmpty) {
                        notificationEmpty.style.display = 'block';
                    }
                }
            })
            .catch(error => {
                console.error('Error loading notifications:', error);
                const notificationLoading = document.getElementById('notificationLoading');
                const notificationEmpty = document.getElementById('notificationEmpty');
                
                if (notificationLoading) {
                    notificationLoading.style.display = 'none';
                }
                if (notificationEmpty) {
                    notificationEmpty.style.display = 'block';
                }
            });
    }

    // Function to check and update unread notification count (without marking as read)
    function updateUnreadCount() {
        const notificationBadge = document.getElementById('notificationBadge');
        const notificationBellIcon = document.getElementById('notificationBellIcon');
        if (!notificationBadge || !notificationBellIcon) {
            return;
        }

        fetch('{{ route("notifications.unread-count") }}')
            .then(async response => {
                const contentType = response.headers.get('content-type') || '';
                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}`);
                }
                if (!contentType.includes('application/json')) {
                    throw new Error('Non-JSON response');
                }
                return response.json();
            })
            .then(data => {
                const unreadCount = data.unread_count || 0;
                if (unreadCount > 0) {
                    // Show badge with count
                    notificationBadge.textContent = unreadCount;
                    notificationBadge.style.display = 'inline-block';
                    // Make bell icon red
                    notificationBellIcon.style.color = '#dc3545';
                } else {
                    // Hide badge
                    notificationBadge.style.display = 'none';
                    // Reset bell icon color to default
                    notificationBellIcon.style.color = '';
                }
            })
            .catch(error => {
                console.error('Error loading unread count:', error);
            });
    }

    function markAllAsRead() {
        // Get all unread notifications
        const unreadNotifications = document.querySelectorAll('.list-group-item.bg-light[data-notification-id]');
        if (unreadNotifications.length === 0) {
            return; // No unread notifications to mark
        }

        // Extract notification IDs from the current notifications
        const notificationIds = [];
        unreadNotifications.forEach(item => {
            const notificationId = item.getAttribute('data-notification-id');
            if (notificationId) {
                notificationIds.push(notificationId);
            }
        });

        if (notificationIds.length === 0) {
            return;
        }

        // Mark all unread notifications as read
        Promise.all(notificationIds.map(id => 
            fetch(`/notifications/${id}/mark-read`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
        ))
        .then(() => {
            // Reload notifications to update read status
            loadNotifications();
            // Update badge count after marking as read
            updateUnreadCount();
        })
        .catch(error => {
            console.error('Error marking notifications as read:', error);
        });
    }


    function getNotificationColor(type) {
        const colors = {
            'info': 'primary',
            'success': 'success',
            'warning': 'warning',
            'error': 'danger'
        };
        return colors[type] || 'primary';
    }

    function getNotificationIcon(type) {
        const icons = {
            'info': 'info-circle',
            'success': 'circle-check',
            'warning': 'alert-triangle',
            'error': 'alert-circle'
        };
        return icons[type] || 'bell';
    }

    // Global show_alert function for compatibility
    function show_alert(type, message) {
        switch(type) {
            case 'success':
                toast_success(message);
                break;
            case 'error':
            case 'danger':
                toast_error(message);
                break;
            case 'warning':
                toast_warning(message);
                break;
            case 'info':
            case 'primary':
                toast_primary(message);
                break;
            default:
                toast_primary(message);
        }
    }

    // Load notifications when page loads
    document.addEventListener('DOMContentLoaded', function() {
        // Only load notifications for non-admin users
        const notificationDropdown = document.getElementById('notificationDropdown');
        if (notificationDropdown) {
            // Load unread count on page load
            updateUnreadCount();
            
            // Check unread count periodically (every 10 seconds) without marking as read
            // This will update the count in real-time when new notifications arrive
            setInterval(updateUnreadCount, 10000);
            
            // Load notifications and mark as read only when dropdown is opened
            notificationDropdown.addEventListener('show.bs.dropdown', function() {
                // Load notifications when dropdown is about to open
                loadNotifications();
            });
            
            notificationDropdown.addEventListener('shown.bs.dropdown', function() {
                // Mark all notifications as read when dropdown is fully shown
                markAllAsRead();
                // Update badge count after marking as read
                setTimeout(() => {
                    updateUnreadCount();
                }, 500);
            });
        }
    });
</script>