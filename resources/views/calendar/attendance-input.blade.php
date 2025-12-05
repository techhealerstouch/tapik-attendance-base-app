@extends('layouts.lang')

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html5-qrcode/2.3.4/html5-qrcode.min.js" integrity="sha512-k/KAe4Yff9EUdYI5/IAHlwUswqeipP+Cp5qnrsUjTPCgl51La2/JhyyjNciztD7mWNKLSXci48m7cctATKfLlQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<!-- Add the Google Fonts link for Poppins -->
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
  /* From Uiverse.io by Bodyhc */ 
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
          <section >
            <div i class="attendance-center">
             <h3 class="header-main">
                Welcome to our
              </h3>
              <h2 class="mb-4">
                <strong>{{ $event }}</strong> 
              </h2>
              <h3>
                held at
              </h3>
              <h2 class="mb-4">
                <strong>{{ $address }}</strong>
              </h2>
              <h3 style="margin-bottom: 3rem">
                Today <strong>{{ $start }}</strong>
              </h3>

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
              
              <div id="rfid" style="display: none;">
                <h4 class="mb-4">
                  Please tap your card to the card reader to mark your attendance
                </h4>
                <!-- this is the scanner component -->
                <livewire:attendance-form :event="$event" />
              </div>
              
              <div id="qr" style="display: none; align-items: center; display: flex; flex-direction: column; justify-content: center;"> <!-- Use flex properties to center -->
                <h4 class="mb-4">
                  Please scan the QR code of your ticket or NFC Card here
                </h4>
                <div id="reader" style="margin: 0 auto;"></div> <!-- Center the reader -->
                <div id="result" style="margin-top: 1rem;"></div> <!-- Add margin for spacing -->
              </div>

            </div>
          </section>
        </div>
      </div>
    </div>
  </div>
</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script defer src="{{ url('assets/js/cdn.min.js') }}"></script>
<script src="{{ url('vendor/livewire/livewire/dist/livewire.js') }}"></script>
<livewire:scripts />
<script src="{{ url('assets/js/livewire-sortable.js') }}"></script>
<script>
    const scanner = new Html5Qrcode("reader");
  document.addEventListener('livewire:load', function () {
    const rfidInput = document.querySelector('input[name="rfid_no"]');
    rfidInput.addEventListener('input', function () {
      Livewire.emit('submitAttendance');
    });
  });
  let isScannerRunning = false; // Flag to track scanner state
  function handleScanSuccess(decodedText) {
    if (isScannerRunning) {
        stopScanner(); // Ensure scanner stops before processing
    }

    // Proceed with handling the scan result
    document.getElementById('result').innerHTML = `
        <h2>Scanning...</h2>
        <p>Checking ticket number...</p>
    `;

    // AJAX call to check the ticket
    $.ajax({
        url: '/scan-ticket',
        type: 'POST',
        data: {
            ticket_no: decodedText,
            event_no: {{ $event_id }},
            _token: "{{ csrf_token() }}"
        },
        success: function(response) {
            // Handle success
            if (response.status === 'success') {
            document.getElementById('result').innerHTML = `
                <h2 style="color: green !important;">Success!</h2>
                <p>${response.message}</p>
                `;
                stopScanner();
                resetScanner();
            } else if (response.status === 'error') {
                document.getElementById('result').innerHTML = `
                    <h2 style="color: red !important;">Error!</h2>
                    <p>${response.message}</p>
                `;
                stopScanner();
                resetScanner();
            } else if (response.status === 'already_scanned') {
                document.getElementById('result').innerHTML = `
                    <h2 style="color: blue !important;">Scan completed!</h2>
                    <p>${response.message}</p>
                `;
                stopScanner();
                resetScanner();
            }
        },
        error: function(xhr, status, error) {
            // Handle error
            document.getElementById('result').innerHTML = `
                <h2 style="color: red !important;>Error!</h2>
                <p>Could not verify the ticket. Please try again.</p>
            `;
            stopScanner(); // Stop scanner on error as well
            resetScanner();
        }
    });
}

function resetScanner() {
    setTimeout(() => {
        document.getElementById('result').innerHTML = ``;
        document.getElementById("reader").style.display = "block";
        startScanner();
    }, 2000); // Wait 2 seconds before restarting the scanner
}

function stopScanner() {
    if (isScannerRunning) {
        scanner.stop().then(() => {
            isScannerRunning = false; // Update the flag
            scanner.clear();
            document.getElementById("reader").style.display = "none";
        }).catch(err => {
            console.error(`Error stopping the scanner: ${err}`);
        });
    }
}

function toggleDiv() {
    var isChecked = document.getElementById("switch").checked;
    document.getElementById("rfid").style.display = isChecked ? "none" : "block";
    document.getElementById("qr").style.display = isChecked ? "block" : "none";

    if (isChecked) {
        // Stop the scanner if it's running before starting it again
        stopScanner();
        setTimeout(() => {
            startScanner(); // Start the scanner after ensuring it has stopped
        }, 100); // Slight delay to ensure the stop process completes
    } else {
        // Stop the scanner if it's running
        stopScanner();
    }
}

function startScanner() {
    const readerElement = document.getElementById("reader");
    if (!readerElement) {
        console.error("Reader element not found");
        return;
    }

    // Ensure the reader is visible
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
            // Handle any error that occurs during scanning
            //console.warn(`QR Code scan error: ${errorMessage}`);
        }
    ).then(() => {
        isScannerRunning = true;
    }).catch((err) => {
        console.error(`Error starting the scanner: ${err}`);
    });
}

// Initialize with NFC/RFID selected by default
document.addEventListener("DOMContentLoaded", function() {
    toggleDiv(); // Set the correct initial state
});

</script>
