<!-- attendance-input.blade.php -->
@extends('layouts.lang')

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html5-qrcode/2.3.4/html5-qrcode.min.js" integrity="sha512-k/KAe4Yff9EUdYI5/IAHlwUswqeipP+Cp5qnrsUjTPCgl51La2/JhyyjNciztD7mWNKLSXci48m7cctATKfLlQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">

<style>
  [x-cloak] { display: none !important; }
  
  body, html {
    margin: 0;
    padding: 0;
    height: 100vh;
    width: 100%;
    overflow: hidden;
    background: linear-gradient(135deg, #4128b1ff 0%, #4128b1ff 100%);
    font-family: 'Poppins', sans-serif;
  }
  
  .container-fluid.content-inner {
    height: 100vh;
    width: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 20px;
    overflow: hidden;
  }

  .content-card {
    background-color: white;
    border-radius: 20px;
    padding: 40px;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
    max-width: 900px;
    width: 100%;
    max-height: 95vh;
    overflow-y: auto;
    animation: fadeInUp 0.6s ease-out;
  }

  .attendance-center {
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
  }

  .logo-container {
    margin-bottom: 20px;
  }

  .logo-container img {
    max-width: 150px;
    max-height: 150px;
    object-fit: contain;
  }

  h2, h3, h4 {
    font-family: 'Poppins', sans-serif;
    color: #2d3748 !important;
    margin: 0;
  }

  .header-main {
    font-size: 1.3rem;
    font-weight: 500;
    margin-bottom: 10px;
    color: #4a5568 !important;
  }

  .event-title {
    font-size: 1.8rem;
    font-weight: 600;
    margin-bottom: 15px;
    color: #1a202c !important;
  }

  .event-details {
    font-size: 1.1rem;
    font-weight: 400;
    margin-bottom: 8px;
    color: #4a5568 !important;
  }

  .event-details strong {
    color: #667eea;
    font-weight: 600;
  }

  .event-date {
    font-size: 1rem;
    margin-bottom: 30px;
    color: #718096 !important;
  }

  /* Toggle Switch Styles */
  .checkbox-wrapper-35 {
    margin-bottom: 30px;
  }

  .checkbox-wrapper-35 .switch {
    display: none;
  }

  .checkbox-wrapper-35 .switch + label {
    align-items: center;
    color: #4a5568;
    cursor: pointer;
    display: flex;
    font-family: 'Poppins', sans-serif;
    font-size: 14px;
    font-weight: 500;
    line-height: 20px;
    position: relative;
    user-select: none;
  }

  .checkbox-wrapper-35 .switch + label::before,
  .checkbox-wrapper-35 .switch + label::after {
    content: '';
    display: block;
  }

  .checkbox-wrapper-35 .switch + label::before {
    background-color: #cbd5e0;
    border-radius: 500px;
    height: 24px;
    margin-right: 12px;
    transition: background-color 0.125s ease-out;
    width: 44px;
  }

  .checkbox-wrapper-35 .switch + label::after {
    background-color: #fff;
    border-radius: 50%;
    box-shadow: 0 3px 8px rgba(0, 0, 0, 0.15), 0 3px 1px rgba(0, 0, 0, 0.06);
    height: 20px;
    left: 2px;
    position: absolute;
    top: 2px;
    transition: transform 0.125s ease-out;
    width: 20px;
  }

  .checkbox-wrapper-35 .switch + label .switch-x-text {
    display: block;
    margin-right: 12px;
  }

  .checkbox-wrapper-35 .switch + label .switch-x-toggletext {
    display: block;
    font-weight: 600;
    height: 20px;
    overflow: hidden;
    position: relative;
    width: 80px;
  }

  .checkbox-wrapper-35 .switch + label .switch-x-unchecked,
  .checkbox-wrapper-35 .switch + label .switch-x-checked {
    left: 0;
    position: absolute;
    top: 0;
    transition: transform 0.125s ease-out, opacity 0.125s ease-out;
  }

  .checkbox-wrapper-35 .switch + label .switch-x-unchecked {
    opacity: 1;
    transform: none;
  }

  .checkbox-wrapper-35 .switch + label .switch-x-checked {
    opacity: 0;
    transform: translate3d(0, 100%, 0);
  }

  .checkbox-wrapper-35 .switch + label .switch-x-hiddenlabel {
    position: absolute;
    visibility: hidden;
  }

  .checkbox-wrapper-35 .switch:checked + label::before {
    background-color: #667eea;
  }

  .checkbox-wrapper-35 .switch:checked + label::after {
    transform: translate3d(20px, 0, 0);
  }

  .checkbox-wrapper-35 .switch:checked + label .switch-x-unchecked {
    opacity: 0;
    transform: translate3d(0, -100%, 0);
  }

  .checkbox-wrapper-35 .switch:checked + label .switch-x-checked {
    opacity: 1;
    transform: none;
  }

  /* Scanner Section */
  .scanner-section {
    width: 100%;
    margin-top: 20px;
  }

  .scanner-instruction {
    font-size: 1.1rem;
    font-weight: 500;
    color: #4a5568;
    margin-bottom: 20px;
  }

  #reader {
    width: 100%;
    max-width: 400px;
    margin: 0 auto;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
  }

  #result {
    margin-top: 20px;
    font-size: 1.2rem;
    font-weight: 500;
    color: #667eea;
  }

  #rfid-status {
    margin-top: 20px;
    font-size: 1.2rem;
    font-weight: 500;
  }

  #rfidInput {
    border: none;
    outline: none;
    box-shadow: none;
    color: transparent;
    caret-color: transparent;
    background-color: transparent;
    -webkit-text-fill-color: transparent;
    position: absolute;
    left: -9999px;
  }

  @keyframes fadeInUp {
    from {
      opacity: 0;
      transform: translateY(30px);
    }
    to {
      opacity: 1;
      transform: translateY(0);
    }
  }

  @media (max-width: 768px) {
    .content-card {
      padding: 30px 20px;
      max-height: 98vh;
    }

    .logo-container img {
      max-width: 120px;
      max-height: 120px;
    }

    .event-title {
      font-size: 1.5rem;
    }

    .header-main {
      font-size: 1.1rem;
    }

    .event-details {
      font-size: 1rem;
    }

    #reader {
      max-width: 300px;
    }
  }
</style>

<div class="container-fluid content-inner">
  <div class="content-card">
    <div class="attendance-center">
      <div class="logo-container">
        <img src="{{ asset('assets/linkstack/images/'.findFile('avatar')) }}" alt="Logo" />
      </div>

      <h3 class="header-main">Welcome to our</h3>
      <h2 class="event-title">{{ $event }}</h2>
      <h3 class="event-details">held at <strong>{{ $address }}</strong></h3>
      <h3 class="event-date">Today <strong>{{ $start }}</strong></h3>

      <div class="checkbox-wrapper-35">
        <input value="private" name="switch" id="switch" type="checkbox" class="switch" onchange="toggleDiv()">
        <label for="switch">
          <span class="switch-x-text"></span>
          <span class="switch-x-toggletext">
            <span class="switch-x-unchecked">
              <span class="switch-x-hiddenlabel">Unchecked: </span>NFC Scan
            </span>
            <span class="switch-x-checked">
              <span class="switch-x-hiddenlabel">Checked: </span>QR Code Scan
            </span>
          </span>
        </label>
      </div>
      
      <!-- Scanner Input Field -->
      <div id="rfid" class="scanner-section" style="display: none;">
        <h4 class="scanner-instruction">Please tap your card to the card reader to mark your attendance</h4>
        <input type='text' id="rfidInput" name="rfid_no" autofocus />
        <div id="rfid-status"></div>
      </div>
      
      <!-- QR code Scanner -->
      <div id="qr" class="scanner-section" style="display: none;">
        <h4 class="scanner-instruction">Please scan the QR code of your ticket or NFC Card here</h4>
        <div id="reader"></div>
        <div id="result"></div>
      </div>
    </div>
  </div>
</div>

<!-- Representative Confirmation Modal -->
<div class="modal fade" id="repConfirmModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Confirm Representative</h5>
      </div>
      <div class="modal-body text-center">
        <i class="bi bi-person-badge text-primary" style="font-size: 3rem;"></i>
        <h5 class="mt-3">
          Are you <strong id="repName"></strong> the representative of <strong id="userName"></strong>?
        </h5>
      </div>
      <div class="modal-footer justify-content-center">
        <button type="button" class="btn btn-success px-4" onclick="confirmRepresentative(true)">
          <i class="bi bi-check-circle"></i> Yes
        </button>
        <button type="button" class="btn btn-secondary px-4" onclick="confirmRepresentative(false)">
          <i class="bi bi-x-circle"></i> No
        </button>
      </div>
    </div>
  </div>
</div>

<!-- Custom Representative Modal -->
<div class="modal fade" id="customRepModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Enter Representative Name</h5>
      </div>
      <div class="modal-body">
        <p class="text-muted mb-3">
          Please enter the name of the representative for <strong id="userNameCustom"></strong>
        </p>
        <div class="mb-3">
          <label for="customRepInput" class="form-label">Representative Name</label>
          <input type="text" class="form-control" id="customRepInput" placeholder="Enter representative name">
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" onclick="cancelCustomRep()">Cancel</button>
        <button type="button" class="btn btn-primary" onclick="submitCustomRep()">Submit</button>
      </div>
    </div>
  </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
const scanner = new Html5Qrcode("reader");
let isScannerRunning = false;
let currentScanData = null;
const enableRepPrompt = {{ $enable_rep_prompt ?? 0 }};
const eventId = {{ $event_id }};
const csrfToken = "{{ csrf_token() }}";
const lastScanMode = "{{ $last_scan_mode ?? 'rfid' }}"; // Get from PHP

// Ensure RFID input always has focus
setInterval(() => {
    const rfidInput = document.getElementById('rfidInput');
    if (rfidInput && document.getElementById('rfid').style.display !== 'none') {
        if (document.activeElement !== rfidInput) {
            rfidInput.focus();
        }
    }
}, 100);

// Handle RFID input with debounce to prevent multiple submissions
let rfidTimeout = null;
document.getElementById('rfidInput')?.addEventListener('input', function(e) {
    const value = e.target.value.trim();
    
    if (rfidTimeout) {
        clearTimeout(rfidTimeout);
    }
    
    if (value.length >= 8) {
        rfidTimeout = setTimeout(() => {
            handleScan(value, 'rfid');
            e.target.value = '';
            rfidTimeout = null;
        }, 100);
    }
});

document.getElementById('rfidInput')?.addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        e.preventDefault();
        const value = e.target.value.trim();
        
        if (value.length >= 8) {
            if (rfidTimeout) {
                clearTimeout(rfidTimeout);
            }
            handleScan(value, 'rfid');
            e.target.value = '';
            rfidTimeout = null;
        }
    }
});

// Handle Enter key press in custom representative input
function handleCustomRepEnter(event) {
    if (event.key === 'Enter' || event.keyCode === 13) {
        event.preventDefault();
        event.stopPropagation();
        submitCustomRep();
        return false;
    }
}

function handleScan(identifier, scanType) {
    showLoading(scanType);

    $.ajax({
        url: '/attendance/scan-process',
        type: 'POST',
        data: {
            identifier: identifier,
            event_id: eventId,
            scan_mode: scanType, // Send scan mode to backend
            _token: csrfToken
        },
        success: function(response) {
            if (response.status === 'success') {
                currentScanData = response.data;
                
                if (response.data.type === 'ticket') {
                    redirectToPage('success', null, '{{ $event }}');
                } 
                else if (enableRepPrompt == 1) {
                    document.getElementById('userName').textContent = response.data.user_name;
                    document.getElementById('userNameCustom').textContent = response.data.user_name;
                    
                    if (response.data.representative) {
                        document.getElementById('repName').textContent = response.data.representative;
                        const modal = new bootstrap.Modal(document.getElementById('repConfirmModal'));
                        modal.show();
                    } else {
                        const modal = new bootstrap.Modal(document.getElementById('customRepModal'));
                        modal.show();
                    }
                }
                else {
                    submitAttendance(response.data.user_id, response.data.attendance_id, response.data.representative, response.data.user_name);
                }
            } else {
                if (response.redirect) {
                    const hasSeat = response.data?.has_seat ?? false;
                    const tableName = response.data?.table_name ?? null;
                    const chairNumber = response.data?.chair_number ?? null;
                    
                    redirectToPage('error', response.message, '{{ $event }}', null, hasSeat, tableName, chairNumber);
                }
            }
        },
        error: function(xhr) {
            redirectToPage('error', 'Error processing scan. Please try again.', '{{ $event }}');
        }
    });
}

function confirmRepresentative(confirmed) {
    const repModal = bootstrap.Modal.getInstance(document.getElementById('repConfirmModal'));
    if (repModal) {
        repModal.hide();
    }
    
    if (confirmed) {
        submitAttendance(currentScanData.user_id, currentScanData.attendance_id, currentScanData.representative, currentScanData.user_name);
    } else {
        setTimeout(() => {
            const customModal = new bootstrap.Modal(document.getElementById('customRepModal'));
            customModal.show();
            // Focus on the input field when modal opens
            setTimeout(() => {
                document.getElementById('customRepInput').focus();
            }, 300);
        }, 300);
    }
}

function cancelCustomRep() {
    const customModal = bootstrap.Modal.getInstance(document.getElementById('customRepModal'));
    if (customModal) {
        customModal.hide();
    }
    document.getElementById('customRepInput').value = '';
    currentScanData = null;
    
    // Clear the loading/processing state
    const rfidStatus = document.getElementById('rfid-status');
    if (rfidStatus) {
        rfidStatus.innerHTML = '';
    }
    const qrResult = document.getElementById('result');
    if (qrResult) {
        qrResult.innerHTML = '';
    }
    
    const rfidInput = document.getElementById('rfidInput');
    if (rfidInput && document.getElementById('rfid').style.display !== 'none') {
        setTimeout(() => rfidInput.focus(), 300);
    }
}

function submitCustomRep() {
    const customRep = document.getElementById('customRepInput').value.trim();
    
    if (!customRep) {
        alert('Please enter a representative name');
        return;
    }
    
    const customModal = bootstrap.Modal.getInstance(document.getElementById('customRepModal'));
    if (customModal) {
        customModal.hide();
    }
    
    submitAttendance(currentScanData.user_id, currentScanData.attendance_id, customRep, currentScanData.user_name, true);
    document.getElementById('customRepInput').value = '';
}

function submitAttendance(userId, attendanceId, representative, userName, updateUserRep = false) {
    $.ajax({
        url: '/attendance/submit',
        type: 'POST',
        data: {
            user_id: userId,
            attendance_id: attendanceId,
            representative: representative,
            update_user_rep: updateUserRep ? 1 : 0,
            event_id: eventId,
            enable_rep_prompt: enableRepPrompt, // ADD THIS
            _token: csrfToken
        },
        success: function(response) {
            if (response.status === 'success' && response.redirect) {
                redirectToPage('success', null, response.data.event, response.data.name, response.data.has_seat, response.data.table_name, response.data.chair_number);
            } else if (response.status === 'error' && response.redirect) {
                redirectToPage('error', response.message, '{{ $event }}');
            }
            currentScanData = null;
        },
        error: function(xhr) {
            redirectToPage('error', 'Error submitting attendance. Please try again.', '{{ $event }}');
            currentScanData = null;
        }
    });
}


function redirectToPage(type, message, event, name = null, hasSeat = false, tableName = null, chairNumber = null) {
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = type === 'success' ? '/attendance-success' : '/attendance-error';
    
    const csrfInput = document.createElement('input');
    csrfInput.type = 'hidden';
    csrfInput.name = '_token';
    csrfInput.value = csrfToken;
    form.appendChild(csrfInput);
    
    if (event) {
        const eventInput = document.createElement('input');
        eventInput.type = 'hidden';
        eventInput.name = 'event';
        eventInput.value = event;
        form.appendChild(eventInput);
    }
    
    if (name) {
        const nameInput = document.createElement('input');
        nameInput.type = 'hidden';
        nameInput.name = 'name';
        nameInput.value = name;
        form.appendChild(nameInput);
    }
    
    if (hasSeat && tableName && chairNumber) {
        const hasSeatInput = document.createElement('input');
        hasSeatInput.type = 'hidden';
        hasSeatInput.name = 'has_seat';
        hasSeatInput.value = '1';
        form.appendChild(hasSeatInput);
        
        const tableInput = document.createElement('input');
        tableInput.type = 'hidden';
        tableInput.name = 'table_name';
        tableInput.value = tableName;
        form.appendChild(tableInput);
        
        const chairInput = document.createElement('input');
        chairInput.type = 'hidden';
        chairInput.name = 'chair_number';
        chairInput.value = chairNumber;
        form.appendChild(chairInput);
    }
    
    if (message && type === 'error') {
        const messageInput = document.createElement('input');
        messageInput.type = 'hidden';
        messageInput.name = 'error';
        messageInput.value = message;
        form.appendChild(messageInput);
    }
    
    // ADD THIS: Pass enable_rep_prompt parameter
    const repPromptInput = document.createElement('input');
    repPromptInput.type = 'hidden';
    repPromptInput.name = 'enable_rep_prompt';
    repPromptInput.value = enableRepPrompt;
    form.appendChild(repPromptInput);
    
    document.body.appendChild(form);
    form.submit();
}

function showLoading(scanType) {
    if (scanType === 'rfid') {
        const rfidDiv = document.getElementById('rfid');
        let statusDiv = document.getElementById('rfid-status');
        
        if (!statusDiv) {
            statusDiv = document.createElement('div');
            statusDiv.id = 'rfid-status';
            rfidDiv.appendChild(statusDiv);
        }
        
        statusDiv.innerHTML = `<p style="color: #667eea;">Processing scan...</p>`;
    } else {
        document.getElementById('result').innerHTML = `<p>Processing scan...</p>`;
    }
}

function handleScanSuccess(decodedText) {
    if (isScannerRunning) {
        stopScanner();
    }
    handleScan(decodedText, 'qr');
}

function stopScanner() {
    if (isScannerRunning) {
        scanner.stop().then(() => {
            isScannerRunning = false;
            scanner.clear();
            document.getElementById("reader").style.display = "none";
        }).catch(err => {
            console.error(`Error stopping the scanner: ${err}`);
        });
    }
}

function toggleDiv() {
    const isChecked = document.getElementById("switch").checked;
    document.getElementById("rfid").style.display = isChecked ? "none" : "block";
    document.getElementById("qr").style.display = isChecked ? "block" : "none";

    if (isChecked) {
        stopScanner();
        setTimeout(() => {
            startScanner();
        }, 100);
    } else {
        stopScanner();
    }
}

function startScanner() {
    const readerElement = document.getElementById("reader");
    if (!readerElement) {
        console.error("Reader element not found");
        return;
    }

    readerElement.style.display = "block";

    scanner.start(
        { facingMode: "environment" },
        {
            fps: 10,
            qrbox: { width: 250, height: 250 },
        },
        (decodedText, decodedResult) => {
            handleScanSuccess(decodedText);
        },
        (errorMessage) => {
            // Handle scan errors silently
        }
    ).then(() => {
        isScannerRunning = true;
    }).catch((err) => {
        console.error(`Error starting the scanner: ${err}`);
    });
}

// Initialize with saved scan mode
document.addEventListener("DOMContentLoaded", function() {
    const switchElement = document.getElementById("switch");
    
    // Restore toggle state from session
    if (lastScanMode === 'qr') {
        switchElement.checked = true;
    } else {
        switchElement.checked = false;
    }
    
    // Apply the saved state
    toggleDiv();
    
    // Add Enter key listener to custom rep input
    const customRepInput = document.getElementById('customRepInput');
    if (customRepInput) {
        customRepInput.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' || e.keyCode === 13) {
                e.preventDefault();
                e.stopPropagation();
                submitCustomRep();
                return false;
            }
        });
    }
});
</script>