@extends('layouts.lang')

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html5-qrcode/2.3.4/html5-qrcode.min.js" integrity="sha512-k/KAe4Yff9EUdYI5/IAHlwUswqeipP+Cp5qnrsUjTPCgl51La2/JhyyjNciztD7mWNKLSXci48m7cctATKfLlQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<!-- Add the Google Fonts link for Poppins -->
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300&display=swap" rel="stylesheet">

<style>
    [x-cloak] {
        display: none !important;
    }

    body,
    html {
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

    h2,
    h3,
    h4 {
        font-family: 'Poppins', sans-serif;
        font-weight: 500;
        color: #052884 !important;
    }

    #preview {
        width: 100%;
        height: auto;
        max-width: 400px;
        border: 2px solid black;
        position: relative;
    }

    #indicator {
        margin-top: 10px;
        width: 50px;
        height: 50px;
        background-color: red;
        border-radius: 50%;
        border: 2px solid black;
        transition: background-color 0.3s ease;
    }

    /* Overlay for success state */
    #preview.success {
        border-color: green;
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
                    <a href="https://tapik.ph" target="_blank" rel="noopener noreferrer"
                        style="display: flex; justify-content: center; align-items: center;">
                        <img src="{{ asset('assets/linkstack/images/' . findFile('avatar')) }}" alt="Philtoa Logo"
                            style="max-width: 400px; max-height: 400px;" />
                    </a>
                    <section>
                        <div class="attendance-center">
                            <h2 class="header-main">
                                Welcome to our {{ $event }}
                            </h2>
                            <h2 class="mb-4">
                                held at {{ $address }}
                            </h2>
                            <h3 style="margin-bottom: 5rem">
                                Today {{ $start }}
                            </h3>
                            <h4 class="mb-4">
                                Please scan the QR code of your ticket here
                            </h4>

                            <!-- QR code scanner -->
                            <div id="reader"></div>
                            <div id="result"></div>
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script defer src="{{ url('assets/js/cdn.min.js') }}"></script>
<script>
   const scanner = new Html5Qrcode("reader");

function startScanner() {
    scanner.start(
        { facingMode: "environment" }, // Use back camera
        {
            fps: 10, // Lower FPS if needed
            qrbox: { width: 250, height: 250 },
        },
        (decodedText, decodedResult) => {
            // Ensure scanner is still running before proceeding
            handleScanSuccess(decodedText);
        },
        (errorMessage) => {
            //console.warn(`QR Code scan error: ${errorMessage}`);
        }
    ).catch((err) => {
        console.error(`Error starting the scanner: ${err}`);
    });
}

function handleScanSuccess(decodedText) {
    // Immediately display the result to the user
    document.getElementById('result').innerHTML = `
        <h2>Scanning...</h2>
        <p>Checking ticket number...</p>
    `;

    // Make the AJAX call to check the ticket
    $.ajax({
        url: '/scan-ticket',  // Your Laravel route
        type: 'POST',
        data: {
            ticket_no: decodedText, // Send the decoded ticket number
             _token: "{{ csrf_token() }}"
        },
        success: function(response) {
    if (response.status === 'success') {
        document.getElementById('result').innerHTML = `
            <h2>Success!</h2>
            <p>Thank you Name, You are now Checked in</p>
        `;
    } else {
        document.getElementById('result').innerHTML = `
            <h2>Error!</h2>
            <p>${response.message}</p>
        `;
        console.log(`${response.message}`);
    }
    // Clear the scanner after the response is handled
    scanner.clear();
    document.getElementById('reader').remove();
},
        error: function(xhr, status, error) {
            // Handle any AJAX error here
            document.getElementById('result').innerHTML = `
                <h2>Error!</h2>
                <p>Could not verify the ticket. Please try again.</p>
            `;
        }
    });
}

// Start the scanner when the page loads
window.onload = startScanner;
</script>
