<!-- Updated attendance-input.blade.php -->
@extends('layouts.lang')

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html5-qrcode/2.3.4/html5-qrcode.min.js" integrity="sha512-k/KAe4Yff9EUdYI5/IAHlwUswqeipP+Cp5qnrsUjTPCgl51La2/JhyyjNciztD7mWNKLSXci48m7cctATKfLlQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300&display=swap" rel="stylesheet">

<style>
  [x-cloak] { display: none !important; }
  body, html {
    margin: 0;
    padding: 0;
    height: 100%;
    width: 100%;
  }
  .container-fluid.content-inner {
    background-color: white !important;
    height: 100vh;
    width: 100vw;
    display: flex;
    align-items: center;
    justify-content: center;
  }
  .attendance-center {
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
  }
  .body {
    color: black !important;
  }
  .header-main {
    margin-top: 50px;
  }
  h2, h3, h4 {
    font-family: 'Poppins', sans-serif;
    font-weight: 500;
    color: #1c2972 !important;
  }
  .checkbox-wrapper-35 .switch {
    display: none;
  }
  .checkbox-wrapper-35 .switch + label {
    -webkit-box-align: center;
    -webkit-align-items: center;
    -ms-flex-align: center;
    align-items: center;
    color: #78768d;
    cursor: pointer;
    display: -webkit-box;
    display: -webkit-flex;
    display: -ms-flexbox;
    display: flex;
    font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
    font-size: 10px;
    line-height: 15px;
    position: relative;
    -webkit-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;
    user-select: none;
  }
  .checkbox-wrapper-35 .switch + label::before,
  .checkbox-wrapper-35 .switch + label::after {
    content: '';
    display: block;
  }
  .checkbox-wrapper-35 .switch + label::before {
    background-color: #05012c;
    border-radius: 500px;
    height: 15px;
    margin-right: 8px;
    -webkit-transition: background-color 0.125s ease-out;
    transition: background-color 0.125s ease-out;
    width: 25px;
  }
  .checkbox-wrapper-35 .switch + label::after {
    background-color: #fff;
    border-radius: 13px;
    box-shadow: 0 3px 1px 0 rgba(37, 34, 71, 0.05), 0 2px 2px 0 rgba(37, 34, 71, 0.1), 0 3px 3px 0 rgba(37, 34, 71, 0.05);
    height: 13px;
    left: 1px;
    position: absolute;
    top: 1px;
    -webkit-transition: -webkit-transform 0.125s ease-out;
    transition: -webkit-transform 0.125s ease-out;
    transition: transform 0.125s ease-out;
    transition: transform 0.125s ease-out, -webkit-transform 0.125s ease-out;
    width: 13px;
  }
  .checkbox-wrapper-35 .switch + label .switch-x-text {
    display: block;
    margin-right: .3em;
  }
  .checkbox-wrapper-35 .switch + label .switch-x-toggletext {
    display: block;
    font-weight: bold;
    height: 15px;
    overflow: hidden;
    position: relative;
    width: 25px;
  }
  .checkbox-wrapper-35 .switch + label .switch-x-unchecked,
  .checkbox-wrapper-35 .switch + label .switch-x-checked {
    left: 0;
    position: absolute;
    top: 0;
    -webkit-transition: opacity 0.125s ease-out, -webkit-transform 0.125s ease-out;
    transition: opacity 0.125s ease-out, -webkit-transform 0.125s ease-out;
    transition: transform 0.125s ease-out, opacity 0.125s ease-out;
    transition: transform 0.125s ease-out, opacity 0.125s ease-out, -webkit-transform 0.125s ease-out;
  }
  .checkbox-wrapper-35 .switch + label .switch-x-unchecked {
    opacity: 1;
    -webkit-transform: none;
    transform: none;
  }
  .checkbox-wrapper-35 .switch + label .switch-x-checked {
    opacity: 0;
    -webkit-transform: translate3d(0, 100%, 0);
    transform: translate3d(0, 100%, 0);
  }
  .checkbox-wrapper-35 .switch + label .switch-x-hiddenlabel {
    position: absolute;
    visibility: hidden;
  }
  .checkbox-wrapper-35 .switch:checked + label::before {
    background-color: #ffb500;
  }
  .checkbox-wrapper-35 .switch:checked + label::after {
    -webkit-transform: translate3d(10px, 0, 0);
    transform: translate3d(10px, 0, 0);
  }
  .checkbox-wrapper-35 .switch:checked + label .switch-x-unchecked {
    opacity: 0;
    -webkit-transform: translate3d(0, -100%, 0);
    transform: translate3d(0, -100%, 0);
  }
  .checkbox-wrapper-35 .switch:checked + label .switch-x-checked {
    opacity: 1;
    -webkit-transform: none;
    transform: none;
  }
  #reader {
    width: 400px;
  }
  #result {
    text-align: center;
    font-size: 1.5rem;
  }
</style>

<div class="container content-inner">
  <div class="row">
    <div class="col-lg-12">
      <div class="row">
        <div class="col-sm-12">
          <div style="display: flex; justify-content: center; align-items: center; cursor: pointer;">
            <img src="{{ asset('assets/linkstack/images/'.findFile('avatar')) }}" 
                alt="Logo" 
                style="max-width: 400px; max-height: 400px;" />
          </div>
          <section>
            <div class="attendance-center">
              <h3 class="header-main">Welcome to our</h3>
              <h2 class="mb-4"><strong>{{ $event }}</strong></h2>
              <h3>held at</h3>
              <h2 class="mb-4"><strong>{{ $address }}</strong></h2>
              <h3 style="margin-bottom: 3rem">Today <strong>{{ $start }}</strong></h3>

              <div class="checkbox-wrapper-35 pb-4">
                <input value="private" name="switch" id="switch" type="checkbox" class="switch" onchange="toggleDiv()">
                <label for="switch">
                  <span class="switch-x-text"></span>
                  <span class="switch-x-toggletext">
                    <span class="switch-x-unchecked">
                      <span class="switch-x-hiddenlabel">Unchecked: </span>Scan
                    </span>
                    <span class="switch-x-checked">
                      <span class="switch-x-hiddenlabel">Checked: </span>QR
                    </span>
                  </span>
                </label>
              </div>
              
              <!-- Scanner Input Field -->
              <div id="rfid" style="display: none;">
                <h4 class="mb-4">Please tap your card to the card reader to mark your attendance</h4>
                <input type='text' 
                       id="rfidInput" 
                       name="rfid_no" 
                       class='form-control' 
                       autofocus 
                       style="border: none; outline: none; box-shadow: none; color: transparent; caret-color: transparent; background-color: transparent; -webkit-text-fill-color: transparent;" />
              </div>
              
              <!-- QR code Scanner -->
              <div id="qr" style="display: none; align-items: center; display: flex; flex-direction: column; justify-content: center;">
                <h4 class="mb-4">Please scan the QR code of your ticket or NFC Card here</h4>
                <div id="reader" style="margin: 0 auto;"></div>
                <div id="result" style="margin-top: 1rem;"></div>
              </div>
            </div>
          </section>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Representative Confirmation Modal - UPDATED VERSION -->
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
    
    // Clear any existing timeout
    if (rfidTimeout) {
        clearTimeout(rfidTimeout);
    }
    
    // Check if value has sufficient length (adjust as needed)
    if (value.length >= 8) {
        // Set a small timeout to ensure the full scan is captured
        rfidTimeout = setTimeout(() => {
            handleScan(value, 'rfid');
            e.target.value = '';
            rfidTimeout = null;
        }, 100);
    }
});

// Also handle on Enter key press for manual input
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

function handleScan(identifier, scanType) {
    // Show loading message
    showLoading(scanType);

    $.ajax({
        url: '/attendance/scan-process',
        type: 'POST',
        data: {
            identifier: identifier,
            event_id: eventId,
            _token: csrfToken
        },
        success: function(response) {
            if (response.status === 'success') {
                currentScanData = response.data;
                
                // Check if it's a ticket scan (no representative prompt needed)
                if (response.data.type === 'ticket') {
                    // Redirect to success page for tickets
                    redirectToPage('success', null, '{{ $event }}');
                } 
                // Check if representative prompt is enabled
                else if (enableRepPrompt == 1) {
                    // Set the user name in both modals
                    document.getElementById('userName').textContent = response.data.user_name;
                    document.getElementById('userNameCustom').textContent = response.data.user_name;
                    
                    // Check if user has a representative set
                    if (response.data.representative) {
                        // Show representative confirmation modal
                        document.getElementById('repName').textContent = response.data.representative;
                        const modal = new bootstrap.Modal(document.getElementById('repConfirmModal'));
                        modal.show();
                    } else {
                        // No representative set - go directly to custom input
                        const modal = new bootstrap.Modal(document.getElementById('customRepModal'));
                        modal.show();
                    }
                }
                // No prompt enabled - process directly
                else {
                    submitAttendance(response.data.user_id, response.data.attendance_id, response.data.representative, response.data.user_name);
                }
            } else {
                // Redirect to error page
                if (response.redirect) {
                    redirectToPage('error', response.message, '{{ $event }}');
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
        // Use the representative from user profile
        submitAttendance(currentScanData.user_id, currentScanData.attendance_id, currentScanData.representative, currentScanData.user_name);
    } else {
        // Show custom representative input
        setTimeout(() => {
            const customModal = new bootstrap.Modal(document.getElementById('customRepModal'));
            customModal.show();
        }, 300); // Small delay to ensure first modal is fully hidden
    }
}

// Updated cancelCustomRep function
function cancelCustomRep() {
    const customModal = bootstrap.Modal.getInstance(document.getElementById('customRepModal'));
    if (customModal) {
        customModal.hide();
    }
    document.getElementById('customRepInput').value = '';
    currentScanData = null;
    
    // Refocus on RFID input if in RFID mode
    const rfidInput = document.getElementById('rfidInput');
    if (rfidInput && document.getElementById('rfid').style.display !== 'none') {
        setTimeout(() => rfidInput.focus(), 300);
    }
}

// Updated submitCustomRep function
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
            _token: csrfToken
        },
        success: function(response) {
            if (response.status === 'success' && response.redirect) {
                // Redirect to success page
                redirectToPage('success', null, response.data.event, response.data.name);
            } else if (response.status === 'error' && response.redirect) {
                // Redirect to error page
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

function redirectToPage(type, message, event, name = null) {
    // Create a form to submit with session data
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = type === 'success' ? '/attendance-success' : '/attendance-error';
    
    // Add CSRF token
    const csrfInput = document.createElement('input');
    csrfInput.type = 'hidden';
    csrfInput.name = '_token';
    csrfInput.value = csrfToken;
    form.appendChild(csrfInput);
    
    // Add event
    if (event) {
        const eventInput = document.createElement('input');
        eventInput.type = 'hidden';
        eventInput.name = 'event';
        eventInput.value = event;
        form.appendChild(eventInput);
    }
    
    // Add name (for success page)
    if (name) {
        const nameInput = document.createElement('input');
        nameInput.type = 'hidden';
        nameInput.name = 'name';
        nameInput.value = name;
        form.appendChild(nameInput);
    }
    
    // Add error message (for error page)
    if (message && type === 'error') {
        const messageInput = document.createElement('input');
        messageInput.type = 'hidden';
        messageInput.name = 'error';
        messageInput.value = message;
        form.appendChild(messageInput);
    }
    
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
            statusDiv.style.marginTop = '20px';
            statusDiv.style.fontSize = '1.2rem';
            statusDiv.style.fontWeight = 'bold';
            rfidDiv.appendChild(statusDiv);
        }
        
        statusDiv.innerHTML = `<p style="color: #0d6efd;">Processing scan...</p>`;
    } else {
        document.getElementById('result').innerHTML = `
            <h2>Scanning...</h2>
            <p>Checking identifier...</p>
        `;
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

// Initialize with NFC/RFID selected by default
document.addEventListener("DOMContentLoaded", function() {
    toggleDiv();
});
</script>