@extends('layouts.sidebar')

@section('content')
    <style>
        .stats-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 12px;
            padding: 24px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin-bottom: 24px;
        }

        .stats-number {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .stats-label {
            font-size: 0.875rem;
            opacity: 0.9;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .event-card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 16px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            border-left: 4px solid #667eea;
            transition: all 0.3s ease;
            animation: slideIn 0.3s ease;
        }

        .event-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.12);
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .event-card h2 {
            margin: 0 0 8px 0;
            color: #2d3748;
            font-size: 1.1rem;
            font-weight: 600;
        }

        .event-card .time {
            color: #718096;
            font-size: 0.875rem;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .event-card .event-title {
            color: #667eea;
            font-weight: 600;
            font-size: 0.875rem;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .member-badge {
            background: #667eea;
            color: white;
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .guest-badge {
            background: #f59e0b;
            color: white;
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .section-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 20px;
            padding-bottom: 12px;
            border-bottom: 2px solid #e2e8f0;
        }

        .section-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: #2d3748;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .count-badge {
            background: #667eea;
            color: white;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.875rem;
            font-weight: 600;
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #a0aec0;
        }

        .empty-state i {
            font-size: 4rem;
            margin-bottom: 16px;
            opacity: 0.5;
        }

        .empty-state p {
            font-size: 1.1rem;
            margin-bottom: 8px;
            color: #718096;
        }

        .empty-state small {
            color: #a0aec0;
        }

        .select2-container {
            z-index: 9999 !important;
        }

        .select2-dropdown {
            z-index: 10060 !important;
        }

        .auto-refresh-indicator {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #f7fafc;
            padding: 8px 16px;
            border-radius: 8px;
            font-size: 0.875rem;
            color: #4a5568;
        }

        .pulse-dot {
            width: 8px;
            height: 8px;
            background: #48bb78;
            border-radius: 50%;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% {
                opacity: 1;
            }
            50% {
                opacity: 0.5;
            }
        }

        .loading-spinner {
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 40px;
        }

        .spinner {
            width: 40px;
            height: 40px;
            border: 4px solid #e2e8f0;
            border-top-color: #667eea;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        .card-header-custom {
            border-left: 4px solid #667eea;
            background: linear-gradient(to right, #f7fafc, white);
        }
    </style>

    <div class="container-fluid content-inner mt-n5 py-0">
        <div class="row">
            <div class="col-lg-12">
                <div class="card rounded">
                    <div class="card-header card-header-custom">
                        <h4 class="card-title mb-2">
                            <i class="fas fa-broadcast-tower"></i> Live Attendance Preview
                        </h4>
                        <p class="text-muted mb-0">
                            <small><i class="fas fa-info-circle"></i> Real-time attendance monitoring with auto-refresh every 5 seconds</small>
                        </p>
                    </div>
                    <div class="card-body">
                        <!-- Event Selection -->
                        <div class="row mb-4">
                            <div class="col-md-4">
                                <label for="event-filter" class="form-label fw-bold">
                                    <i class="fas fa-calendar-alt"></i> Select Event
                                </label>
                                <select id="event-filter" class="form-control">
                                    <option value="">-- Select Event --</option>
                                    @foreach($events as $event)
                                        <option value="{{ $event->id }}">{{ $event->title }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="limit-filter" class="form-label fw-bold">
                                    <i class="fas fa-list-ol"></i> Display Limit
                                </label>
                                <select id="limit-filter" class="form-control">
                                    <option value="10">Show 10 Recent</option>
                                    <option value="100">Show 100 Recent</option>
                                    <option value="500">Show 500 Recent</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold d-block">
                                    <i class="fas fa-ticket-alt"></i> Display Options
                                </label>
                                <div class="form-check" style="padding-top: 8px;">
                                    <input class="form-check-input" type="checkbox" id="show-guests-checkbox">
                                    <label class="form-check-label" for="show-guests-checkbox">
                                        Display Guests Checked In
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Auto Refresh Indicator -->
                        <div class="row mb-3">
                            <div class="col-12 d-flex justify-content-end">
                                <div class="auto-refresh-indicator">
                                    <span class="pulse-dot"></span>
                                    <span>Auto-refreshing every 15 seconds</span>
                                </div>
                            </div>
                        </div>

                        <!-- Content Area -->
                        <div id="content-area">
                            <!-- Default Empty State -->
                            <div class="empty-state">
                                <i class="fas fa-calendar-check"></i>
                                <p class="mb-2">No Event Selected</p>
                                <small>Please select an event from the dropdown above to view live attendance</small>
                            </div>
                        </div>

                        <!-- Stats and Data (Hidden by default) -->
                        <div id="data-container" style="display: none;">
                            <!-- Statistics Row -->
                            <div class="row mb-4">
                                <div class="col-md-6" id="member-stats-card">
                                    <div class="stats-card">
                                        <div class="stats-number" id="member-count">0/0</div>
                                        <div class="stats-label">
                                            <i class="fas fa-users"></i> Members Present
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6" id="guest-stats-card" style="display: none;">
                                    <div class="stats-card" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);">
                                        <div class="stats-number" id="guest-count">0</div>
                                        <div class="stats-label">
                                            <i class="fas fa-ticket-alt"></i> Guests Checked In
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Attendance Data Row -->
                            <div class="row">
                                <div id="member-column" class="col-md-12">
                                    <div class="section-header">
                                        <div class="section-title">
                                            <i class="fas fa-users"></i>
                                            <span>Members</span>
                                        </div>
                                        <span class="count-badge" id="member-badge">0</span>
                                    </div>
                                    <div id="event-stack"></div>
                                </div>
                                <div id="guest-column" class="col-md-6" style="display: none;">
                                    <div class="section-header">
                                        <div class="section-title">
                                            <i class="fas fa-ticket-alt"></i>
                                            <span>Guests</span>
                                        </div>
                                        <span class="count-badge" id="guest-badge" style="background: #f59e0b;">0</span>
                                    </div>
                                    <div id="event-stack-2"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('sidebar-scripts')
<!-- Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />

<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
(function() {
    'use strict';
    
    let selectedEventId = null;
    let selectedLimit = 10; // Default limit
    let showGuests = false; // Default to hide guests
    let totalMembers = 0;
    let refreshInterval = null;

    // Wait for DOM to be fully loaded
    document.addEventListener('DOMContentLoaded', function() {
        console.log('DOM loaded, initializing...');
        
        // Wait a bit for Select2 to be fully loaded
        setTimeout(function() {
            initializeSelect2();
            initializeEventHandlers();
            showEmptyState();
        }, 100);
    });

    function initializeSelect2() {
        try {
            if (typeof $.fn.select2 !== 'undefined') {
                $('#event-filter').select2({
                    theme: 'bootstrap-5',
                    placeholder: '-- Select Event --',
                    allowClear: true,
                    width: '100%'
                });
                
                $('#limit-filter').select2({
                    theme: 'bootstrap-5',
                    minimumResultsForSearch: Infinity, // Disable search for small list
                    width: '100%'
                });
                
                console.log('Select2 initialized successfully');
            } else {
                console.error('Select2 is not loaded');
                // Fallback: use regular select
                $('#event-filter').addClass('form-select');
                $('#limit-filter').addClass('form-select');
            }
        } catch (e) {
            console.error('Error initializing Select2:', e);
            // Fallback: use regular select
            $('#event-filter').addClass('form-select');
            $('#limit-filter').addClass('form-select');
        }
    }

    function initializeEventHandlers() {
        // Event selection change
        $('#event-filter').on('change', function() {
            selectedEventId = $(this).val();
            console.log('Event selected:', selectedEventId);
            
            // Clear existing interval
            if (refreshInterval) {
                clearInterval(refreshInterval);
                refreshInterval = null;
            }
            
            if (selectedEventId) {
                fetchEvents(selectedEventId, selectedLimit);
                // Start auto-refresh
                refreshInterval = setInterval(() => {
                    fetchEvents(selectedEventId, selectedLimit);
                }, 15000);
            } else {
                showEmptyState();
            }
        });
        
        // Limit selection change
        $('#limit-filter').on('change', function() {
            selectedLimit = parseInt($(this).val());
            console.log('Limit changed to:', selectedLimit);
            
            // If an event is already selected, refresh with new limit
            if (selectedEventId) {
                fetchEvents(selectedEventId, selectedLimit);
            }
        });
        
        // Show/Hide Guests checkbox change
        $('#show-guests-checkbox').on('change', function() {
            showGuests = $(this).is(':checked');
            console.log('Show guests:', showGuests);
            
            toggleGuestDisplay();
            
            // If an event is already selected, refresh data
            if (selectedEventId) {
                fetchEvents(selectedEventId, selectedLimit);
            }
        });
    }
    
    function toggleGuestDisplay() {
        if (showGuests) {
            // Show guests column and stats
            $('#guest-column').show().removeClass('col-md-6').addClass('col-md-6');
            $('#member-column').removeClass('col-md-12').addClass('col-md-6');
            $('#guest-stats-card').show().removeClass('col-md-6').addClass('col-md-6');
            $('#member-stats-card').removeClass('col-md-6').addClass('col-md-6');
        } else {
            // Hide guests column and stats
            $('#guest-column').hide();
            $('#member-column').removeClass('col-md-6').addClass('col-md-12');
            $('#guest-stats-card').hide();
            $('#member-stats-card').removeClass('col-md-6').addClass('col-md-12');
        }
    }

    function fetchEvents(eventId, limit) {
        if (!eventId) {
            showEmptyState();
            return;
        }

        console.log('Fetching events for:', eventId, 'with limit:', limit);

        // Show loading
        $('#data-container').show();
        $('#content-area').hide();
        $('#event-stack').html('<div class="loading-spinner"><div class="spinner"></div></div>');
        $('#event-stack-2').html('<div class="loading-spinner"><div class="spinner"></div></div>');

        $.ajax({
            url: '/attendance/live-attendance-user/' + eventId,
            method: 'GET',
            data: { limit: limit },
            dataType: 'json',
            success: function(response) {
                console.log('Response received:', response);
                if (response.attendances || response.ticketGuests) {
                    totalMembers = response.totalMembers || 0;
                    updateEventStack(response.attendances || [], totalMembers);
                    updateEventStack2(response.ticketGuests || []);
                } else {
                    showNoData();
                }
            },
            error: function(xhr, status, error) {
                console.error('Error fetching events:', error);
                showError();
            }
        });
    }

    function showEmptyState() {
        $('#content-area').html(`
            <div class="empty-state">
                <i class="fas fa-calendar-check"></i>
                <p class="mb-2">No Event Selected</p>
                <small>Please select an event from the dropdown above to view live attendance</small>
            </div>
        `).show();
        $('#data-container').hide();
    }

    function showNoData() {
        $('#event-stack').html(`
            <div class="empty-state" style="padding: 40px 20px;">
                <i class="fas fa-user-slash" style="font-size: 3rem;"></i>
                <p style="font-size: 1rem;">No members attendance data found</p>
            </div>
        `);
        $('#event-stack-2').html(`
            <div class="empty-state" style="padding: 40px 20px;">
                <i class="fas fa-ticket-alt" style="font-size: 3rem;"></i>
                <p style="font-size: 1rem;">No guest attendance data found</p>
            </div>
        `);
        updateCounts(0, 0, 0);
    }

    function showError() {
        $('#event-stack').html(`
            <div class="empty-state" style="padding: 40px 20px;">
                <i class="fas fa-exclamation-triangle" style="font-size: 3rem; color: #f56565;"></i>
                <p style="font-size: 1rem; color: #f56565;">Error loading data</p>
            </div>
        `);
        $('#event-stack-2').html(`
            <div class="empty-state" style="padding: 40px 20px;">
                <i class="fas fa-exclamation-triangle" style="font-size: 3rem; color: #f56565;"></i>
                <p style="font-size: 1rem; color: #f56565;">Error loading data</p>
            </div>
        `);
    }

    function formatTime(dateString) {
        const date = new Date(dateString);
        return date.toLocaleTimeString([], { hour: "2-digit", minute: "2-digit", second: "2-digit" });
    }

    function createEventCard(attendance) {
        return `
            <div class="event-card">
                <h2>
                    <span class="member-badge">MEMBER</span>
                    ${attendance.user?.name || 'N/A'}
                </h2>
                <div class="time">
                    <i class="far fa-clock"></i>
                    ${formatTime(attendance.time_in)}
                </div>
                <div class="event-title">
                    <i class="fas fa-calendar"></i>
                    ${attendance.event?.title || 'N/A'}
                </div>
            </div>
        `;
    }

    function updateEventStack(attendances, total) {
        const presentCount = attendances.length;
        
        if (presentCount === 0) {
            $('#event-stack').html(`
                <div class="empty-state" style="padding: 40px 20px;">
                    <i class="fas fa-user-slash" style="font-size: 3rem;"></i>
                    <p style="font-size: 1rem;">No members checked in yet</p>
                </div>
            `);
        } else {
            $('#event-stack').html(attendances.map(createEventCard).join(''));
        }

        updateCounts(presentCount, total, attendances.length);
    }

    function createEventCard2(ticketGuest) {
        return `
            <div class="event-card" style="border-left-color: #f59e0b;">
                <h2>
                    <span class="guest-badge">GUEST</span>
                    ${ticketGuest.ticket_no || 'N/A'}
                </h2>
                <div class="time">
                    <i class="far fa-clock"></i>
                    ${formatTime(ticketGuest.updated_at)}
                </div>
                <div class="event-title" style="color: #f59e0b;">
                    <i class="fas fa-calendar"></i>
                    ${ticketGuest.ticket?.event?.title || 'N/A'}
                </div>
            </div>
        `;
    }

    function updateEventStack2(ticketGuests) {
        if (!showGuests) {
            // If guests are hidden, don't update
            return;
        }
        
        const guestCount = ticketGuests.length;
        
        if (guestCount === 0) {
            $('#event-stack-2').html(`
                <div class="empty-state" style="padding: 40px 20px;">
                    <i class="fas fa-ticket-alt" style="font-size: 3rem;"></i>
                    <p style="font-size: 1rem;">No guests checked in yet</p>
                </div>
            `);
        } else {
            $('#event-stack-2').html(ticketGuests.map(createEventCard2).join(''));
        }

        $('#guest-count').text(guestCount);
        $('#guest-badge').text(guestCount);
    }

    function updateCounts(present, total, memberCount) {
        $('#member-count').text(`${present}/${total}`);
        $('#member-badge').text(memberCount);
    }

    // Make functions available globally for debugging
    window.livePreview = {
        fetchEvents: fetchEvents,
        showEmptyState: showEmptyState,
        selectedEventId: function() { return selectedEventId; }
    };

    // Cleanup on page unload
    window.addEventListener('beforeunload', function() {
        if (refreshInterval) {
            clearInterval(refreshInterval);
        }
    });
})();
</script>
@endpush