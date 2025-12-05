<!-- resources/views/food-services/claiming-page.blade.php -->
@extends('layouts.lang')


    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-lg-12">
                <div class="card rounded">
                    <div class="card-header bg-primary text-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h4 class="card-title text-white mb-1">Food Service Claiming Interface</h4>
                                <p class="mb-0 small"><i class="bi bi-calendar-event"></i> Event: <strong id="eventTitle">{{ $event->title }}</strong></p>
                            </div>
                            <button class="btn btn-light btn-sm" onclick="window.close()">
                                <i class="bi bi-x-lg"></i> Close
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <p class="text-muted mb-4"><small><i class="bi bi-info-circle"></i> Note: Users must check in to the event first before claiming food services</small></p>
                        
                        <!-- Scanner Mode Toggle -->
                        <div class="text-center mb-4">
                            <div class="btn-group" role="group" aria-label="Scanner mode toggle">
                                <button type="button" class="btn btn-primary" id="nfcModeBtn">
                                    <i class="bi bi-upc-scan"></i> NFC / Reference Code
                                </button>
                                <button type="button" class="btn btn-outline-primary" id="qrModeBtn">
                                    <i class="bi bi-qr-code-scan"></i> QR Code Scanner
                                </button>
                            </div>
                        </div>

                        <!-- NFC/Reference Code Section (Default Visible) -->
                        <div id="nfcSection" class="mb-4">
                            <div class="row justify-content-center">
                                <div class="col-lg-8 col-md-10">
                                    <label for="userIdentifier" class="form-label fs-5">Scan NFC or Enter Reference Code</label>
                                    <div class="input-group input-group-lg">
                                        <input type="text" 
                                               id="userIdentifier" 
                                               class="form-control" 
                                               placeholder="Scan or type code..."
                                               autofocus>
                                        <button class="btn btn-primary" id="scanBtn">
                                            <i class="bi bi-search"></i> Scan
                                        </button>
                                    </div>
                                    <small class="text-muted">Press Enter after scanning or typing</small>
                                </div>
                            </div>
                        </div>

                        <!-- QR Code Scanner Section (Hidden by Default) -->
                        <div id="qrSection" style="display: none;" class="mb-4">
                            <div class="row justify-content-center">
                                <div class="col-lg-6 col-md-8">
                                    <label class="form-label fs-5">QR Code Scanner</label>
                                    <div class="card border-primary">
                                        <div class="card-body p-2">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <h6 class="mb-0 small">Camera Scanner</h6>
                                                <button class="btn btn-sm btn-primary" id="toggleCameraBtn">
                                                    <i class="bi bi-camera"></i> Start Camera
                                                </button>
                                            </div>
                                            <div id="qrReader" style="display: none; width: 100%; max-height: 400px; overflow: hidden;"></div>
                                            <div id="qrStatus" class="text-center text-muted small mt-2">
                                                Click "Start Camera" to scan QR codes
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Alert Messages -->
                        <div id="alertContainer"></div>

                        <!-- User Info and Food Services -->
                        <div id="userInfoSection" style="display: none;">
                            <!-- User Information Card -->
                            <div class="card border-info mb-4">
                                <div class="card-header bg-primary bg-opacity-0">
                                    <h5 class="card-title pb-4 text-white">
                                        <i class="bi bi-person-circle text-white"></i> User Information
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="row g-3">
                                        <div class="col-md-4">
                                            <div class="d-flex align-items-center">
                                                <i class="bi bi-person-fill text-primary me-2 fs-5"></i>
                                                <div>
                                                    <small class="text-muted d-block">Name</small>
                                                    <strong id="userName"></strong>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="d-flex align-items-center">
                                                <i class="bi bi-envelope-fill text-primary me-2 fs-5"></i>
                                                <div>
                                                    <small class="text-muted d-block">Email</small>
                                                    <strong id="userEmail"></strong>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="d-flex align-items-center">
                                                <i class="bi bi-upc-scan text-primary me-2 fs-5"></i>
                                                <div>
                                                    <small class="text-muted d-block">Reference Code</small>
                                                    <strong id="userCode"></strong>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Food Services List -->
                            <div class="card">
                                <div class="card-header bg-primary">
                                    <h5 class="card-title pb-4 text-white">
                                        <i class="bi bi-cart-check-fill text-white"></i> Available Food Services
                                    </h5>
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
     <!-- jQuery (Load this FIRST before any other scripts) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
    
    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    
    <!-- HTML5 QR Code Scanner -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html5-qrcode/2.3.4/html5-qrcode.min.js" integrity="sha512-k/KAe4Yff9EUdYI5/IAHlwUswqeipP+Cp5qnrsUjTPCgl51La2/JhyyjNciztD7mWNKLSXci48m7cctATKfLlQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    
    <style>
        #qrReader {
            border-radius: 8px;
            overflow: hidden;
        }
        
        #qrReader video {
            width: 100% !important;
            height: auto !important;
            max-height: 400px !important;
            border-radius: 8px;
            object-fit: cover;
        }

        #qrReader__scan_region {
            border-radius: 8px !important;
        }

        #qrReader__dashboard_section_csr {
            max-height: 400px !important;
        }

        /* Toggle button styling */
        .btn-group .btn {
            min-width: 200px;
        }

        /* Mobile responsive adjustments */
        @media (max-width: 768px) {
            #qrReader {
                max-width: 100%;
            }
            
            #qrReader video {
                max-height: 300px !important;
            }

            .btn-group .btn {
                min-width: 150px;
                font-size: 0.9rem;
            }
        }

        /* User info styling */
        #userInfoSection .card-header {
            border-left: 4px solid #0dcaf0;
        }

        #userInfoSection .bi {
            font-size: 1.2rem;
        }

        /* Smooth transitions */
        #nfcSection, #qrSection {
            transition: opacity 0.3s ease-in-out;
        }
    </style>
    
    <script>
        let currentUserId = null;
        let currentUserIdentifier = null; // Store the last scanned identifier
        let currentEventId = {{ $event->id }};
        let foodServices = [];
        let qrScanner = null;
        let isScannerRunning = false;
        let currentMode = 'nfc'; // 'nfc' or 'qr'

        $(document).ready(function() {
            // Initialize QR Scanner
            qrScanner = new Html5Qrcode("qrReader");

            // Mode toggle buttons
            $('#nfcModeBtn').click(function() {
                switchToNFCMode();
            });

            $('#qrModeBtn').click(function() {
                switchToQRMode();
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

            // Toggle Camera Button
            $('#toggleCameraBtn').click(function() {
                if (isScannerRunning) {
                    stopQRScanner();
                } else {
                    startQRScanner();
                }
            });

            // AGGRESSIVE AUTO-FOCUS for NFC mode - Keep cursor always in input field
            const inputField = document.getElementById('userIdentifier');
            
            // Force focus function (only when in NFC mode)
            function forceFocus() {
                if (currentMode === 'nfc') {
                    inputField.focus();
                }
            }

            // Initial focus
            forceFocus();

            // Continuously force focus every 100ms (only in NFC mode)
            setInterval(forceFocus, 100);

            // Force focus on blur (only in NFC mode)
            inputField.addEventListener('blur', function() {
                if (currentMode === 'nfc') {
                    forceFocus();
                }
            });

            // Force focus on any document click (only in NFC mode)
            document.addEventListener('click', function(e) {
                if (currentMode === 'nfc' && !e.target.closest('#qrModeBtn')) {
                    forceFocus();
                }
            });
        });

        function switchToNFCMode() {
            if (currentMode === 'nfc') return;

            // Stop QR scanner if running
            if (isScannerRunning) {
                stopQRScanner();
            }

            // Update UI
            currentMode = 'nfc';
            $('#nfcSection').show();
            $('#qrSection').hide();
            $('#nfcModeBtn').removeClass('btn-outline-primary').addClass('btn-primary');
            $('#qrModeBtn').removeClass('btn-primary').addClass('btn-outline-primary');

            // Focus on input
            setTimeout(() => {
                $('#userIdentifier').focus();
            }, 100);
        }

        function switchToQRMode() {
            if (currentMode === 'qr') return;

            // Update UI
            currentMode = 'qr';
            $('#nfcSection').hide();
            $('#qrSection').show();
            $('#nfcModeBtn').removeClass('btn-primary').addClass('btn-outline-primary');
            $('#qrModeBtn').removeClass('btn-outline-primary').addClass('btn-primary');

            // Auto-start camera after a short delay
            setTimeout(() => {
                startQRScanner();
            }, 300);
        }

        function startQRScanner() {
            const qrReaderElement = document.getElementById("qrReader");
            qrReaderElement.style.display = "block";
            
            $('#qrStatus').html('<span class="text-info"><i class="bi bi-camera-video"></i> Starting camera...</span>');
            $('#toggleCameraBtn').html('<span class="spinner-border spinner-border-sm"></span>').prop('disabled', true);

            qrScanner.start(
                { facingMode: "environment" },
                {
                    fps: 10,
                    qrbox: { width: 250, height: 250 },
                },
                (decodedText, decodedResult) => {
                    handleQRCodeSuccess(decodedText);
                },
                (errorMessage) => {
                    // Ignore scanning errors (happens when no QR code in view)
                }
            ).then(() => {
                isScannerRunning = true;
                $('#qrStatus').html('<span class="text-success"><i class="bi bi-camera-video-fill"></i> Camera active - Point at QR code</span>');
                $('#toggleCameraBtn').html('<i class="bi bi-stop-circle"></i> Stop Camera').removeClass('btn-primary').addClass('btn-danger').prop('disabled', false);
            }).catch((err) => {
                console.error(`Error starting scanner: ${err}`);
                $('#qrStatus').html('<span class="text-danger"><i class="bi bi-exclamation-triangle"></i> Camera access error</span>');
                $('#toggleCameraBtn').html('<i class="bi bi-camera"></i> Start Camera').prop('disabled', false);
                qrReaderElement.style.display = "none";
            });
        }

        function stopQRScanner() {
            if (isScannerRunning) {
                $('#toggleCameraBtn').html('<span class="spinner-border spinner-border-sm"></span>').prop('disabled', true);
                
                qrScanner.stop().then(() => {
                    isScannerRunning = false;
                    qrScanner.clear();
                    document.getElementById("qrReader").style.display = "none";
                    $('#qrStatus').html('<span class="text-muted">Click "Start Camera" to scan QR codes</span>');
                    $('#toggleCameraBtn').html('<i class="bi bi-camera"></i> Start Camera').removeClass('btn-danger').addClass('btn-primary').prop('disabled', false);
                }).catch(err => {
                    console.error(`Error stopping scanner: ${err}`);
                    $('#toggleCameraBtn').html('<i class="bi bi-camera"></i> Start Camera').removeClass('btn-danger').addClass('btn-primary').prop('disabled', false);
                });
            }
        }

        function handleQRCodeSuccess(decodedText) {
            // Stop scanner temporarily
            stopQRScanner();

            // Update status
            $('#qrStatus').html('<span class="text-info"><i class="bi bi-hourglass-split"></i> Processing QR code...</span>');

            // Process the scanned code directly
            processScannedCode(decodedText);

            // Restart scanner after 3 seconds
            setTimeout(() => {
                if (currentMode === 'qr') {
                    startQRScanner();
                }
            }, 3000);
        }

        function scanUser() {
            const identifier = $('#userIdentifier').val().trim();
            
            if (!identifier) {
                showAlert('Please enter or scan a code', 'warning');
                return;
            }

            processScannedCode(identifier);
        }

        function processScannedCode(identifier) {
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
                    currentUserIdentifier = identifier; // Store the identifier for later use
                    foodServices = response.food_services;
                    
                    displayUserInfo(response.user);
                    displayFoodServices(response.food_services);
                    
                    $('#userInfoSection').show();
                    showAlert('User found successfully! Attendance verified ✓', 'success');
                    
                    // Clear input field after successful scan
                    $('#userIdentifier').val('');
                },
                error: function(xhr) {
                    const error = xhr.responseJSON?.error || 'Error scanning user';
                    const isAttendanceError = xhr.responseJSON?.attendance_required || false;
                    const isRegistrationError = xhr.responseJSON?.event_registration_required || false;
                    
                    if (isRegistrationError) {
                        showAlert(`<strong>⚠️ Not Registered:</strong><br>${error}`, 'danger');
                        
                        if (xhr.responseJSON?.user) {
                            currentUserId = xhr.responseJSON.user.id;
                            currentUserIdentifier = identifier;
                            displayUserInfo(xhr.responseJSON.user);
                            $('#userInfoSection').show();
                            $('#foodServicesList').html(`
                                <div class="col-12">
                                    <div class="alert alert-danger">
                                        <i class="bi bi-exclamation-octagon-fill"></i> 
                                        <strong>This user is not registered for this event.</strong><br>
                                        Please ensure the user is registered for the event before they can claim food services.
                                    </div>
                                </div>
                            `);
                        }
                    } else if (isAttendanceError) {
                        showAlert(`<strong>⚠️ Attendance Required:</strong><br>${error}`, 'warning');
                        
                        if (xhr.responseJSON?.user) {
                            currentUserId = xhr.responseJSON.user.id;
                            currentUserIdentifier = identifier;
                            displayUserInfo(xhr.responseJSON.user);
                            $('#userInfoSection').show();
                            $('#foodServicesList').html(`
                                <div class="col-12">
                                    <div class="alert alert-warning">
                                        <i class="bi bi-exclamation-triangle-fill"></i> 
                                        <strong>This user has not checked in to the event yet.</strong><br>
                                        Please ensure the user checks in at the attendance station before claiming food services.
                                    </div>
                                </div>
                            `);
                        }
                    } else {
                        showAlert(error, 'danger');
                    }
                    
                    // Clear input field after error
                    $('#userIdentifier').val('');
                },
                complete: function() {
                    $('#userIdentifier').prop('disabled', false);
                    $('#scanBtn').prop('disabled', false).html('<i class="bi bi-search"></i> Scan');
                    // Refocus on input field if in NFC mode
                    if (currentMode === 'nfc') {
                        $('#userIdentifier').focus();
                    }
                }
            });
        }

        function displayUserInfo(user) {
            $('#userName').text(user.name);
            $('#userEmail').text(user.email);
            $('#userCode').text(user.activate_code || 'N/A');
        }

        function displayFoodServices(services) {
            const container = $('#foodServicesList');
            container.empty();

            if (services.length === 0) {
                container.html('<div class="col-12"><div class="alert alert-info">No food services available for this event.</div></div>');
                return;
            }

            services.forEach(service => {
                const formatTime = (timeString) => {
                    if (!timeString) return '';
                    
                    if (timeString.length === 5 || timeString.length === 8) {
                        const [hours, minutes] = timeString.split(':');
                        const hour = parseInt(hours);
                        const ampm = hour >= 12 ? 'PM' : 'AM';
                        const displayHour = hour % 12 || 12;
                        return `${displayHour.toString().padStart(2, '0')}:${minutes} ${ampm}`;
                    }
                    
                    try {
                        const date = new Date(timeString);
                        const hours = date.getHours();
                        const minutes = date.getMinutes();
                        const ampm = hours >= 12 ? 'PM' : 'AM';
                        const displayHour = hours % 12 || 12;
                        return `${displayHour.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')} ${ampm}`;
                    } catch (e) {
                        return timeString;
                    }
                };

                const timeDisplay = service.serving_start && service.serving_end 
                    ? `<p class="mb-2 small">
                           <strong>Time:</strong> ${formatTime(service.serving_start)} - ${formatTime(service.serving_end)}
                       </p>`
                    : '';

                const card = $(`
                    <div class="col-lg-4 col-md-6 col-sm-12">
                        <div class="card h-100 ${service.is_claimed ? 'border-success' : 'border-primary'}">
                            <div class="card-body">
                                <h6 class="card-title">
                                    ${service.name}
                                    ${service.is_claimed ? '<span class="badge bg-success ms-2">Redeemed</span>' : ''}
                                </h6>
                                <p class="card-text small text-muted">${service.description || ''}</p>
                                ${service.quantity ? `
                                    <p class="mb-2 small">
                                        <strong>Available:</strong> ${service.remaining}/${service.quantity}
                                    </p>
                                ` : ''}
                                ${timeDisplay}
                                <div class="mt-2">
                                    ${service.can_claim && !service.is_claimed ? `
                                        <button class="btn btn-primary btn-sm w-100 claim-btn" 
                                                data-service-id="${service.id}"
                                                ${service.remaining === 0 ? 'disabled' : ''}>
                                            <i class="bi bi-check-circle"></i> Redeem
                                        </button>
                                    ` : service.is_claimed ? `
                                        <button class="btn btn-success btn-sm w-100" disabled>
                                            <i class="bi bi-check-circle-fill"></i> Already Redeemed
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

            $('.claim-btn').click(function() {
                const serviceId = $(this).data('service-id');
                claimService(serviceId, $(this));
            });
        }

        function claimService(serviceId, button) {
            if (!confirm('Confirm redeeming this food service?')) {
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
                    
                    // Refresh the user data using the stored identifier
                    if (currentUserIdentifier) {
                        refreshUserData();
                    }
                },
                error: function(xhr) {
                    const error = xhr.responseJSON?.error || 'Error redeeming service';
                    const isAttendanceError = xhr.responseJSON?.attendance_required || false;
                    
                    if (isAttendanceError) {
                        showAlert(`<strong>⚠️ Attendance Required:</strong><br>${error}`, 'warning');
                    } else {
                        showAlert(error, 'danger');
                    }
                    
                    button.prop('disabled', false).html('<i class="bi bi-check-circle"></i> Redeem');
                }
            });
        }

        function refreshUserData() {
            if (!currentUserIdentifier) return;

            $.ajax({
                url: '{{ route("food-service.scan") }}',
                method: 'POST',
                data: {
                    identifier: currentUserIdentifier,
                    event_id: currentEventId,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    foodServices = response.food_services;
                    displayFoodServices(response.food_services);
                },
                error: function(xhr) {
                    console.error('Error refreshing user data:', xhr);
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
            
            setTimeout(() => {
                alert.fadeOut(() => alert.remove());
            }, 5000);
        }

        function resetInterface() {
            currentUserId = null;
            currentUserIdentifier = null;
            $('#userIdentifier').val('');
            $('#userInfoSection').hide();
            $('#alertContainer').empty();
            
            if (currentMode === 'nfc') {
                $('#userIdentifier').focus();
            }
        }

        $(window).on('beforeunload', function() {
            if (isScannerRunning) {
                stopQRScanner();
            }
        });
    </script>