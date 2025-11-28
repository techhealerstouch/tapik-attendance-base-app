@extends('layouts.sidebar')

@section('content')
    <div class="container-fluid content-inner mt-n5 py-0">
        <div class="row">
            <div class="col-lg-12">
                <div class="card rounded">
                    <div class="card-header">
                        <h4 class="card-title">Food Service Claiming</h4>
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
                        </div>

                        <!-- Scanner Interface -->
                        <div id="scannerSection" style="display: none;">
                            <div class="row">
                                <div class="col-md-6 mb-4">
                                    <label for="userIdentifier" class="form-label">Scan QR/NFC or Enter Reference Code</label>
                                    <div class="input-group">
                                        <input type="text" 
                                               id="userIdentifier" 
                                               class="form-control form-control-lg" 
                                               placeholder="Scan or type code..."
                                               autofocus>
                                        <button class="btn btn-primary" id="scanBtn">
                                            <i class="bi bi-search"></i> Scan
                                        </button>
                                    </div>
                                    <small class="text-muted">Press Enter after scanning or typing</small>
                                </div>
                            </div>

                            <!-- Alert Messages -->
                            <div id="alertContainer"></div>

                            <!-- User Info and Food Services -->
                            <div id="userInfoSection" style="display: none;">
                                <div class="card bg-light mb-4">
                                    <div class="card-body">
                                        <h5 class="card-title">User Information</h5>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <strong>Name:</strong> <span id="userName"></span>
                                            </div>
                                            <div class="col-md-4">
                                                <strong>Email:</strong> <span id="userEmail"></span>
                                            </div>
                                            <div class="col-md-4">
                                                <strong>Reference Code:</strong> <span id="userCode"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Food Services List -->
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">Available Food Services</h5>
                                    </div>
                                    <div class="card-body">
                                        <div id="foodServicesList" class="row g-3"></div>
                                    </div>
                                </div>

                                <div class="mt-3">
                                    <button class="btn btn-secondary" id="resetBtn">
                                        <i class="bi bi-arrow-clockwise"></i> Scan Another User
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('sidebar-scripts')
        <script>
            let currentUserId = null;
            let currentEventId = null;
            let foodServices = [];

            $(document).ready(function() {
                // Show scanner when event is selected
                $('#eventSelect').change(function() {
                    currentEventId = $(this).val();
                    if (currentEventId) {
                        $('#scannerSection').show();
                        $('#userIdentifier').focus();
                    } else {
                        $('#scannerSection').hide();
                        resetInterface();
                    }
                });

                // Handle scan button click
                $('#scanBtn').click(function() {
                    scanUser();
                });

                // Handle enter key
                $('#userIdentifier').keypress(function(e) {
                    if (e.which === 13) {
                        scanUser();
                    }
                });

                // Reset button
                $('#resetBtn').click(function() {
                    resetInterface();
                });
            });

            function scanUser() {
                const identifier = $('#userIdentifier').val().trim();
                
                if (!identifier) {
                    showAlert('Please enter or scan a code', 'warning');
                    return;
                }

                if (!currentEventId) {
                    showAlert('Please select an event first', 'warning');
                    return;
                }

                // Show loading
                $('#userIdentifier').prop('disabled', true);
                $('#scanBtn').prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Scanning...');

                $.ajax({
                    url: '{{ route("food-service.scan") }}',
                    method: 'POST',
                    data: {
                        identifier: identifier,
                        event_id: currentEventId,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        currentUserId = response.user.id;
                        foodServices = response.food_services;
                        
                        displayUserInfo(response.user);
                        displayFoodServices(response.food_services);
                        
                        $('#userInfoSection').show();
                        showAlert('User found successfully!', 'success');
                    },
                    error: function(xhr) {
                        const error = xhr.responseJSON?.error || 'Error scanning user';
                        showAlert(error, 'danger');
                        resetInterface();
                    },
                    complete: function() {
                        $('#userIdentifier').prop('disabled', false);
                        $('#scanBtn').prop('disabled', false).html('<i class="bi bi-search"></i> Scan');
                    }
                });
            }

            function displayUserInfo(user) {
                $('#userName').text(user.name);
                $('#userEmail').text(user.email);
                $('#userCode').text(user.activate_code || user.rfid_no || 'N/A');
            }

            function displayFoodServices(services) {
                const container = $('#foodServicesList');
                container.empty();

                services.forEach(service => {
                    const card = $(`
                        <div class="col-md-4">
                            <div class="card ${service.is_claimed ? 'border-success' : 'border-primary'}">
                                <div class="card-body">
                                    <h6 class="card-title">
                                        ${service.name}
                                        ${service.is_claimed ? '<span class="badge bg-success ms-2">Claimed</span>' : ''}
                                    </h6>
                                    <p class="card-text small text-muted">${service.description || ''}</p>
                                    ${service.quantity ? `
                                        <p class="mb-2 small">
                                            <strong>Available:</strong> ${service.remaining}/${service.quantity}
                                        </p>
                                    ` : ''}
                                    ${service.serving_start ? `
                                        <p class="mb-2 small">
                                            <strong>Time:</strong> ${service.serving_start} - ${service.serving_end}
                                        </p>
                                    ` : ''}
                                    <div class="mt-2">
                                        ${service.can_claim && !service.is_claimed ? `
                                            <button class="btn btn-primary btn-sm w-100 claim-btn" 
                                                    data-service-id="${service.id}"
                                                    ${service.remaining === 0 ? 'disabled' : ''}>
                                                <i class="bi bi-check-circle"></i> Claim
                                            </button>
                                        ` : service.is_claimed ? `
                                            <button class="btn btn-danger btn-sm w-100 unclaim-btn" 
                                                    data-service-id="${service.id}">
                                                <i class="bi bi-x-circle"></i> Unclaim
                                            </button>
                                        ` : `
                                            <button class="btn btn-secondary btn-sm w-100" disabled>
                                                Not Available
                                            </button>
                                        `}
                                    </div>
                                </div>
                            </div>
                        </div>
                    `);
                    container.append(card);
                });

                // Bind claim buttons
                $('.claim-btn').click(function() {
                    const serviceId = $(this).data('service-id');
                    claimService(serviceId, $(this));
                });

                // Bind unclaim buttons
                $('.unclaim-btn').click(function() {
                    const serviceId = $(this).data('service-id');
                    unclaimService(serviceId, $(this));
                });
            }

            function claimService(serviceId, button) {
                if (!confirm('Confirm claiming this food service?')) {
                    return;
                }

                button.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Processing...');

                $.ajax({
                    url: '{{ route("food-service.process-claim") }}',
                    method: 'POST',
                    data: {
                        user_id: currentUserId,
                        event_id: currentEventId,
                        food_service_id: serviceId,
                        claim_method: 'manual',
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        showAlert(response.message, 'success');
                        // Refresh the display
                        scanUser();
                    },
                    error: function(xhr) {
                        const error = xhr.responseJSON?.error || 'Error claiming service';
                        showAlert(error, 'danger');
                        button.prop('disabled', false).html('<i class="bi bi-check-circle"></i> Claim');
                    }
                });
            }

            function unclaimService(serviceId, button) {
                if (!confirm('Confirm removing this claim?')) {
                    return;
                }

                button.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Processing...');

                $.ajax({
                    url: '{{ route("food-service.unclaim") }}',
                    method: 'POST',
                    data: {
                        user_id: currentUserId,
                        event_id: currentEventId,
                        food_service_id: serviceId,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        showAlert(response.message, 'success');
                        // Refresh the display
                        scanUser();
                    },
                    error: function(xhr) {
                        const error = xhr.responseJSON?.error || 'Error unclaiming service';
                        showAlert(error, 'danger');
                        button.prop('disabled', false).html('<i class="bi bi-x-circle"></i> Unclaim');
                    }
                });
            }

            function showAlert(message, type) {
                const alert = $(`
                    <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                        ${message}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                `);
                
                $('#alertContainer').html(alert);
                
                // Auto dismiss after 5 seconds
                setTimeout(() => {
                    alert.fadeOut(() => alert.remove());
                }, 5000);
            }

            function resetInterface() {
                currentUserId = null;
                $('#userIdentifier').val('');
                $('#userInfoSection').hide();
                $('#alertContainer').empty();
                $('#userIdentifier').focus();
            }
        </script>
    @endpush
@endsection