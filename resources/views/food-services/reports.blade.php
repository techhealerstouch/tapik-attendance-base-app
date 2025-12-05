@extends('layouts.sidebar')

@section('content')
    <div class="container-fluid content-inner mt-n5 py-0">
        <div class="row">
            <div class="col-lg-12">
                <div class="card rounded">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">Food Service Reports</h4>
                        <button id="exportBtn" class="btn btn-success" disabled>
                            <i class="bi bi-download"></i> Export to Excel
                        </button>
                    </div>
                    <div class="card-body">
                        <!-- Event Selection -->
                        <div class="row mb-4">
                            <div class="col-md-8">
                                <label for="eventSelect" class="form-label fw-bold">Select Event</label>
                                <select id="eventSelect" class="form-select">
                                    <option value="">-- Select Event --</option>
                                    @foreach($events as $event)
                                        <option value="{{ $event->id }}">
                                            {{ $event->title }} ({{ $event->start->format('M d, Y') }} - {{ $event->end->format('M d, Y') }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Quick Actions</label>
                                <div class="d-flex gap-2">
                                    <button id="printBtn" class="btn btn-outline-primary flex-fill" disabled>
                                        <i class="bi bi-printer"></i> Print
                                    </button>
                                    <button id="refreshBtn" class="btn btn-outline-secondary flex-fill" disabled>
                                        <i class="bi bi-arrow-clockwise"></i> Refresh
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Event Summary Header -->
                        <div id="eventHeader" style="display: none;">
                            <div class="alert alert-info mb-4">
                                <div class="row">
                                    <div class="col-md-8">
                                        <h5 class="mb-1" id="eventTitle"></h5>
                                        <p class="mb-0" id="eventDates"></p>
                                    </div>
                                    <div class="col-md-4 text-end">
                                        <small class="text-muted">Report Generated</small><br>
                                        <strong id="reportTime"></strong>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Summary Cards -->
                        <div id="summarySection" style="display: none;">
                            <div class="row g-3 mb-4">
                                <div class="col-md-3">
                                    <div class="card border-0 shadow-sm h-100">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <h6 class="text-muted mb-1">Total Services</h6>
                                                    <h2 class="mb-0" id="totalServices">0</h2>
                                                </div>
                                                <div class="bg-primary bg-opacity-10 p-3 rounded">
                                                    <i class="bi bi-shop text-primary" style="font-size: 2rem;"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card border-0 shadow-sm h-100">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <h6 class="text-muted mb-1">Total Claims</h6>
                                                    <h2 class="mb-0" id="totalClaims">0</h2>
                                                </div>
                                                <div class="bg-success bg-opacity-10 p-3 rounded">
                                                    <i class="bi bi-check-circle text-success" style="font-size: 2rem;"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card border-0 shadow-sm h-100">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <h6 class="text-muted mb-1">Unique Users</h6>
                                                    <h2 class="mb-0" id="uniqueUsers">0</h2>
                                                </div>
                                                <div class="bg-info bg-opacity-10 p-3 rounded">
                                                    <i class="bi bi-people text-info" style="font-size: 2rem;"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card border-0 shadow-sm h-100">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <h6 class="text-muted mb-1">Avg Claims/Service</h6>
                                                    <h2 class="mb-0" id="avgClaims">0</h2>
                                                </div>
                                                <div class="bg-warning bg-opacity-10 p-3 rounded">
                                                    <i class="bi bi-graph-up text-warning" style="font-size: 2rem;"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Food Services Report Cards -->
                        <div id="reportsSection" style="display: none;">
                            <h5 class="mb-3">Service Details</h5>
                            <div id="serviceCardsContainer" class="row g-3">
                                <!-- Service cards will be dynamically loaded here -->
                            </div>

                            <!-- Claims Details Section -->
                            <div class="mt-5">
                                <h5 class="mb-3">All Claims Details</h5>
                                <div class="card border-0 shadow-sm">
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table id="allClaimsTable" class="table table-hover">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th>Food Service</th>
                                                        <th>User Name</th>
                                                        <th>Email</th>
                                                        <th>Claimed At</th>
                                                        <th>Claimed By</th>
                                                        <th>Method</th>
                                                        <th>Notes</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <!-- Claims will be populated here -->
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Total Scan per User Section -->
                            <div class="mt-5">
                                <h5 class="mb-3">Total Scan per User</h5>
                                <div class="card border-0 shadow-sm">
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table id="scanStatsTable" class="table table-hover">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th>Name</th>
                                                        <th>Email</th>
                                                        <th>Scan Count</th>
                                                        <th>First Scan</th>
                                                        <th>Last Scan</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <!-- Scan stats will be populated here -->
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Loading State -->
                        <div id="loadingState" class="text-center py-5" style="display: none;">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="mt-3 text-muted">Loading report data...</p>
                        </div>

                        <!-- Empty State -->
                        <div id="emptyState" class="text-center py-5">
                            <i class="bi bi-file-earmark-text" style="font-size: 4rem; color: #ccc;"></i>
                            <p class="text-muted mt-3">Select an event to view comprehensive reports</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('sidebar-scripts')
        <script>
            let currentEventId = null;
            let currentEventData = null;
            let claimsTable = null;
            let scanStatsTable = null;

            $(document).ready(function() {
                $('#eventSelect').change(function() {
                    currentEventId = $(this).val();
                    if (currentEventId) {
                        loadEventReport(currentEventId);
                        $('#exportBtn, #printBtn, #refreshBtn').prop('disabled', false);
                    } else {
                        resetReports();
                        $('#exportBtn, #printBtn, #refreshBtn').prop('disabled', true);
                    }
                });

                $('#exportBtn').click(function() {
                    if (currentEventId) {
                        window.location.href = `/food-service-reports/export/${currentEventId}`;
                    }
                });

                $('#printBtn').click(function() {
                    window.print();
                });

                $('#refreshBtn').click(function() {
                    if (currentEventId) {
                        loadEventReport(currentEventId);
                    }
                });
            });

            function loadEventReport(eventId) {
                $('#emptyState').hide();
                $('#eventHeader, #summarySection, #reportsSection').hide();
                $('#loadingState').show();

                $.ajax({
                    url: `/food-service-reports/event/${eventId}`,
                    method: 'GET',
                    success: function(response) {
                        currentEventData = response;
                        displayEventHeader(response.event);
                        displaySummary(response.summary);
                        displayServiceCards(response.report);
                        displayAllClaims(response.report);
                        displayScanStats(response.scan_stats);
                        
                        $('#loadingState').hide();
                        $('#eventHeader, #summarySection, #reportsSection').show();
                    },
                    error: function(xhr) {
                        console.error('Error loading report:', xhr);
                        alert('Error loading report. Please try again.');
                        $('#loadingState').hide();
                        $('#emptyState').show();
                    }
                });
            }

            function displayEventHeader(event) {
                $('#eventTitle').text(event.title);
                $('#eventDates').text(`${event.start} to ${event.end}`);
                $('#reportTime').text(new Date().toLocaleString());
            }

            function displaySummary(summary) {
                $('#totalServices').text(summary.total_services);
                $('#totalClaims').text(summary.total_claims);
                $('#uniqueUsers').text(summary.unique_users);
                
                const avgClaims = summary.total_services > 0 
                    ? (summary.total_claims / summary.total_services).toFixed(1)
                    : 0;
                $('#avgClaims').text(avgClaims);
            }

            function displayServiceCards(reports) {
                const container = $('#serviceCardsContainer');
                container.empty();

                if (reports.length === 0) {
                    container.html('<div class="col-12"><p class="text-center text-muted">No food services found for this event</p></div>');
                    return;
                }

                reports.forEach((service) => {
                    const percentage = service.total_quantity !== 'Unlimited' 
                        ? ((service.total_claimed / service.total_quantity) * 100).toFixed(1)
                        : 0;
                    
                    const statusClass = service.remaining === 0 || service.remaining === 'N/A' 
                        ? 'danger' 
                        : percentage > 75 ? 'warning' : 'success';

                    const card = `
                        <div class="col-md-6 col-lg-4">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start mb-3">
                                        <h5 class="card-title mb-0">${service.food_service_name}</h5>
                                        <span class="badge bg-${statusClass}">${service.total_claimed} claimed</span>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <div class="d-flex justify-content-between mb-1">
                                            <small class="text-muted">Utilization</small>
                                            <small class="text-muted">${service.total_quantity !== 'Unlimited' ? percentage + '%' : 'N/A'}</small>
                                        </div>
                                        <div class="progress" style="height: 8px;">
                                            <div class="progress-bar bg-${statusClass}" role="progressbar" 
                                                 style="width: ${service.total_quantity !== 'Unlimited' ? percentage : 100}%" 
                                                 aria-valuenow="${percentage}" aria-valuemin="0" aria-valuemax="100">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row g-2 mb-3">
                                        <div class="col-6">
                                            <div class="bg-light p-2 rounded text-center">
                                                <small class="text-muted d-block">Total Quantity</small>
                                                <strong>${service.total_quantity}</strong>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="bg-light p-2 rounded text-center">
                                                <small class="text-muted d-block">Remaining</small>
                                                <strong class="text-${statusClass}">${service.remaining}</strong>
                                            </div>
                                        </div>
                                    </div>

                                    ${service.claims.length > 0 ? `
                                        <button class="btn btn-sm btn-outline-primary w-100" 
                                                onclick="viewServiceDetails('${service.food_service_name}', ${service.food_service_id})">
                                            <i class="bi bi-eye"></i> View ${service.claims.length} Claim${service.claims.length > 1 ? 's' : ''}
                                        </button>
                                    ` : '<p class="text-muted text-center mb-0 small">No claims yet</p>'}
                                </div>
                            </div>
                        </div>
                    `;
                    
                    container.append(card);
                });
            }

            function displayAllClaims(reports) {
    const tbody = $('#allClaimsTable tbody');
    
    // Destroy existing DataTable if it exists
    if (claimsTable) {
        claimsTable.destroy();
        claimsTable = null;
    }
    
    // Clear the tbody AFTER destroying the DataTable
    tbody.empty();

    reports.forEach((service) => {
        service.claims.forEach((claim) => {
            const methodBadgeClass = {
                'Qr': 'primary',
                'Nfc': 'success',
                'Manual': 'secondary'
            };

            const row = `
                <tr>
                    <td><strong>${service.food_service_name}</strong></td>
                    <td>${claim.user_name}</td>
                    <td>${claim.user_email}</td>
                    <td>${claim.claimed_at}</td>
                    <td>${claim.claimed_by}</td>
                    <td><span class="badge bg-${methodBadgeClass[claim.claim_method] || 'secondary'}">${claim.claim_method}</span></td>
                    <td>${claim.notes || '-'}</td>
                </tr>
            `;
            tbody.append(row);
        });
    });

    // Initialize DataTable
    claimsTable = $('#allClaimsTable').DataTable({
        pageLength: 25,
        order: [[3, 'desc']], // Order by claimed_at desc
        dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>rtip',
        language: {
            search: "_INPUT_",
            searchPlaceholder: "Search claims..."
        }
    });
}

function displayScanStats(scanStats) {
    const tbody = $('#scanStatsTable tbody');
    
    // Destroy existing DataTable if it exists
    if (scanStatsTable) {
        scanStatsTable.destroy();
        scanStatsTable = null;
    }
    
    // Clear the tbody AFTER destroying the DataTable
    tbody.empty();

    if (!scanStats || scanStats.length === 0) {
        tbody.html('<tr><td colspan="5" class="text-center text-muted">No scan data available for this event</td></tr>');
        return;
    }

    scanStats.forEach((stat) => {
        const row = `
            <tr>
                <td><strong>${stat.user_name}</strong></td>
                <td>${stat.user_email}</td>
                <td><span class="badge bg-primary">${stat.scan_count}</span></td>
                <td>${stat.first_scanned_at}</td>
                <td>${stat.last_scanned_at}</td>
            </tr>
        `;
        tbody.append(row);
    });

    // Initialize DataTable
    scanStatsTable = $('#scanStatsTable').DataTable({
        pageLength: 25,
        order: [[2, 'desc']], // Order by scan_count desc
        dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>rtip',
        language: {
            search: "_INPUT_",
            searchPlaceholder: "Search users..."
        }
    });
}

            function viewServiceDetails(serviceName, serviceId) {
                if (!currentEventData) return;

                const service = currentEventData.report.find(s => s.food_service_id === serviceId);
                if (!service) return;

                // Filter the DataTable to show only this service's claims
                claimsTable.search(serviceName).draw();
                
                // Scroll to the claims table
                $('html, body').animate({
                    scrollTop: $('#allClaimsTable').offset().top - 100
                }, 500);
            }

            function resetReports() {
                currentEventId = null;
                currentEventData = null;
                $('#eventHeader, #summarySection, #reportsSection').hide();
                $('#loadingState').hide();
                $('#emptyState').show();
                $('#serviceCardsContainer').empty();
                
                if (claimsTable) {
                    claimsTable.destroy();
                    claimsTable = null;
                }
                
                if (scanStatsTable) {
                    scanStatsTable.destroy();
                    scanStatsTable = null;
                }
            }
        </script>

        <style>
            @media print {
                .btn, .form-select, #emptyState, .card-header button {
                    display: none !important;
                }
                
                .card {
                    break-inside: avoid;
                    page-break-inside: avoid;
                }
            }
        </style>
    @endpush
@endsection