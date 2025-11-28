@extends('layouts.sidebar')

@section('content')
    <div class="container-fluid content-inner mt-n5 py-0">
        <div class="row">
            <div class="col-lg-12">
                <div class="card rounded">
                    <div class="card-header">
                        <h4 class="card-title">Food Service Reports</h4>
                    </div>
                    <div class="card-body">
                        <!-- Event Selection -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="eventSelect" class="form-label">Select Event</label>
                                <select id="eventSelect" class="form-select">
                                    <option value="">-- Select Event --</option>
                                    @foreach($events as $event)
                                        <option value="{{ $event->id }}">
                                            {{ $event->title }} ({{ $event->start->format('M d, Y') }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 d-flex align-items-end">
                                <button id="exportBtn" class="btn btn-success" disabled>
                                    <i class="bi bi-download"></i> Export to CSV
                                </button>
                            </div>
                        </div>

                        <!-- Summary Cards -->
                        <div id="summarySection" style="display: none;">
                            <div class="row mb-4">
                                <div class="col-md-4">
                                    <div class="card bg-primary text-white">
                                        <div class="card-body">
                                            <h6 class="card-title">Total Food Services</h6>
                                            <h3 id="totalServices">0</h3>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card bg-success text-white">
                                        <div class="card-body">
                                            <h6 class="card-title">Total Claims</h6>
                                            <h3 id="totalClaims">0</h3>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card bg-info text-white">
                                        <div class="card-body">
                                            <h6 class="card-title">Unique Users</h6>
                                            <h3 id="uniqueUsers">0</h3>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Reports Section -->
                        <div id="reportsSection" style="display: none;">
                            <div class="accordion" id="foodServiceAccordion">
                                <!-- Food service reports will be dynamically loaded here -->
                            </div>
                        </div>

                        <!-- Loading State -->
                        <div id="loadingState" class="text-center py-5" style="display: none;">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="mt-2">Loading report data...</p>
                        </div>

                        <!-- Empty State -->
                        <div id="emptyState" class="text-center py-5">
                            <i class="bi bi-file-earmark-text" style="font-size: 4rem; color: #ccc;"></i>
                            <p class="text-muted mt-3">Select an event to view reports</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('sidebar-scripts')
        <script>
            let currentEventId = null;

            $(document).ready(function() {
                $('#eventSelect').change(function() {
                    currentEventId = $(this).val();
                    if (currentEventId) {
                        loadEventReport(currentEventId);
                        $('#exportBtn').prop('disabled', false);
                    } else {
                        resetReports();
                        $('#exportBtn').prop('disabled', true);
                    }
                });

                $('#exportBtn').click(function() {
                    if (currentEventId) {
                        window.location.href = `/food-service-reports/export/${currentEventId}`;
                    }
                });
            });

            function loadEventReport(eventId) {
                // Show loading state
                $('#emptyState').hide();
                $('#summarySection').hide();
                $('#reportsSection').hide();
                $('#loadingState').show();

                $.ajax({
                    url: `/food-service-reports/event/${eventId}`,
                    method: 'GET',
                    success: function(response) {
                        displaySummary(response.summary);
                        displayReports(response.report);
                        
                        $('#loadingState').hide();
                        $('#summarySection').show();
                        $('#reportsSection').show();
                    },
                    error: function(xhr) {
                        console.error('Error loading report:', xhr);
                        alert('Error loading report. Please try again.');
                        $('#loadingState').hide();
                        $('#emptyState').show();
                    }
                });
            }

            function displaySummary(summary) {
                $('#totalServices').text(summary.total_services);
                $('#totalClaims').text(summary.total_claims);
                $('#uniqueUsers').text(summary.unique_users);
            }

            function displayReports(reports) {
                const accordion = $('#foodServiceAccordion');
                accordion.empty();

                if (reports.length === 0) {
                    accordion.html('<p class="text-center text-muted">No food services found for this event</p>');
                    return;
                }

                reports.forEach((service, index) => {
                    const accordionItem = `
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="heading${index}">
                                <button class="accordion-button ${index !== 0 ? 'collapsed' : ''}" 
                                        type="button" 
                                        data-bs-toggle="collapse" 
                                        data-bs-target="#collapse${index}">
                                    <div class="w-100 d-flex justify-content-between align-items-center pe-3">
                                        <span><strong>${service.food_service_name}</strong></span>
                                        <span class="badge bg-primary">${service.total_claimed} claimed</span>
                                    </div>
                                </button>
                            </h2>
                            <div id="collapse${index}" 
                                 class="accordion-collapse collapse ${index === 0 ? 'show' : ''}" 
                                 data-bs-parent="#foodServiceAccordion">
                                <div class="accordion-body">
                                    <div class="row mb-3">
                                        <div class="col-md-4">
                                            <strong>Total Quantity:</strong> ${service.total_quantity}
                                        </div>
                                        <div class="col-md-4">
                                            <strong>Total Claimed:</strong> ${service.total_claimed}
                                        </div>
                                        <div class="col-md-4">
                                            <strong>Remaining:</strong> ${service.remaining}
                                        </div>
                                    </div>
                                    
                                    ${service.claims.length > 0 ? `
                                        <div class="table-responsive">
                                            <table class="table table-sm table-striped claims-table">
                                                <thead>
                                                    <tr>
                                                        <th>User</th>
                                                        <th>Email</th>
                                                        <th>Claimed At</th>
                                                        <th>Claimed By</th>
                                                        <th>Method</th>
                                                        <th>Notes</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    ${service.claims.map(claim => `
                                                        <tr>
                                                            <td>${claim.user_name}</td>
                                                            <td>${claim.user_email}</td>
                                                            <td>${claim.claimed_at}</td>
                                                            <td>${claim.claimed_by}</td>
                                                            <td><span class="badge bg-secondary">${claim.claim_method}</span></td>
                                                            <td>${claim.notes || '-'}</td>
                                                        </tr>
                                                    `).join('')}
                                                </tbody>
                                            </table>
                                        </div>
                                    ` : '<p class="text-muted text-center">No claims yet</p>'}
                                </div>
                            </div>
                        </div>
                    `;
                    
                    accordion.append(accordionItem);
                });

                // Initialize DataTables for each claims table
                $('.claims-table').each(function() {
                    $(this).DataTable({
                        pageLength: 10,
                        order: [[2, 'desc']], // Order by claimed_at desc
                        dom: 'frtip'
                    });
                });
            }

            function resetReports() {
                currentEventId = null;
                $('#summarySection').hide();
                $('#reportsSection').hide();
                $('#loadingState').hide();
                $('#emptyState').show();
                $('#foodServiceAccordion').empty();
            }
        </script>
    @endpush
@endsection