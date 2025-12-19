<?php

$pages = DB::table('pages')->get();
foreach($pages as $page)
{
    //Gets value from database
}

?>
<div style="margin-top: 10px">

<x-guest-layout>
@include('layouts.lang')
    <x-auth-card>
        <x-slot name="logo"></x-slot>

        <!-- Session Status -->
        <x-auth-session-status class="mb-4" :status="session('status')" />

        <!-- Validation Errors -->
        <x-auth-validation-errors class="mb-4" :errors="$errors" />

        <div class="container mt-5 w-100 " style="height: 100%;">
            <div class="card" style="padding-right: 20px;padding-left: 20px">
          
              <div class="logo-main" style="text-align: center; margin-bottom: 20px">
                  @if(file_exists(base_path("assets/linkstack/images/").findFile('avatar')))
                      <div class="logo-normal">
                          <img class="img logo" src="{{ asset('assets/linkstack/images/'.findFile('avatar')) }}" style="width:auto;height:60px;">
                      </div>
                      <div class="logo-mini">
                          <img class="img logo" src="{{ asset('assets/linkstack/images/'.findFile('avatar')) }}" style="width:auto;height:30px;">
                      </div>
                  @else
                      <div class="logo-normal">
                          <img class="img logo" type="image/svg+xml" src="{{ asset('assets/linkstack/images/logo.svg') }}" width="30px" height="30px">
                      </div>
                      <div class="logo-mini">
                          <img class="img logo" type="image/svg+xml" src="{{ asset('assets/linkstack/images/logo.svg') }}" width="30px" height="30px">
                      </div>
                  @endif
              </div>
              <form id="multi-step-form" method="POST">
              @csrf
              <p class="text-center">Setup your profile</p>
              <div id="setup-steps">
                    <!-- Initially, step 1 is displayed, others are hidden -->
                    <div class="step" id="step1" style="display: block;">
                        @include('setup.steps.step1')
                    </div>
                    <div class="step" id="step2" style="display: none;">
                        @include('setup.steps.step2')
                    </div>
                    <div class="step" id="step3" style="display: none;">
                        @include('setup.steps.step3')
                    </div>
                    <div class="step" id="step4" style="display: none;">
                        @include('setup.steps.step4')
                    </div>
                    <div class="step" id="step5" style="display: none;">
                        @include('setup.steps.step5')
                    </div>
                    <div class="step-indicator text-center mb-3">
                        Page <span id="current-step">1</span> of 5
                    </div>
                    <div class="d-flex justify-content-center mt-3">

                        <button id="prev-btn" class="btn btn-primary" onclick="changeStep(-1)" style="display: none;">Previous</button>
                        <span style="margin-right: 10px;"></span>
                        <button id="next-btn" class="btn btn-primary" onclick="changeStep(1)">Next</button>
                        <span style="margin-right: 10px;"></span>
                        <button id="submit-btn" class="btn btn-primary" style="display: none;" onclick="handleSubmit()">Submit</button>
                    </div> 
                    <div class="d-flex justify-content-center mt-3" style="margin-bottom: 15px">
                        <button id="skip-btn" class="btn btn-secondary" style="background-color: transparent; border-color: transparent; color: #db5363;" onclick="changeStep(1)">Skip</button>
                    </div> 
                </div>

            </div>
    

          </div>   
  

    </x-auth-card>
</x-guest-layout>
</div>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

<script>
$(document).ready(function() {
    // Get code from URL
    const url = new URL(window.location.href);
    const code = url.pathname.split('/').pop();
    
    let currentStep = localStorage.getItem('currentStep') ? parseInt(localStorage.getItem('currentStep')) : 1;
    console.log('Current step retrieved from localStorage:', currentStep);
    const totalSteps = 5;

    // Function to hide all steps except the current one
    function hideAllSteps() {
        $('.step').hide();
    }

    function saveFormDataToLocalStorage(step) {
        // Only serialize inputs within the current step
        const formData = $('#step' + step + ' :input').serializeArray();
        localStorage.setItem(`formData_step${step}`, JSON.stringify(formData));
        console.log(`Form data for step ${step} saved to localStorage:`, formData);
    }

    function loadDataFromLocalStorage(step) {
        const storedData = localStorage.getItem(`formData_step${step}`);
        if (storedData) {
            const data = JSON.parse(storedData);
            data.forEach(item => {
                $(`[name="${item.name}"]`).val(item.value);
            });
        }
    }

    // Function to change steps
    function changeStep(direction) {
        // Save current step data
        saveFormDataToLocalStorage(currentStep);

        // Hide all steps
        hideAllSteps();

        // Update the current step
        currentStep += direction;
        currentStep = Math.max(1, Math.min(totalSteps, currentStep));
        console.log('New current step:', currentStep);

        // Show the new step
        $('#step' + currentStep).show();

        // Update step indicator
        $('#current-step').text(currentStep);

        // Save the current step to localStorage
        localStorage.setItem('currentStep', currentStep);
        console.log('Current step saved to localStorage:', currentStep);

        // Load data for the new current step
        loadDataFromLocalStorage(currentStep);

        // Hide or show the previous button based on current step
        if (currentStep === 1) {
            $('#prev-btn').hide();
        } else {
            $('#prev-btn').show();
        }

        // Hide or show the next button based on current step
        if (currentStep === totalSteps) {
            $('#next-btn').hide();
            $('#submit-btn').show();
        } else {
            $('#next-btn').show();
            $('#submit-btn').hide();
        }

        // Hide or show the skip button based on whether it's the final step
        if (currentStep === totalSteps) {
            $('#skip-btn').hide();
        } else {
            $('#skip-btn').show();
        }
    }

    // Make changeStep available globally
    window.changeStep = function(direction) {
        changeStep(direction);
    };

    // Call loadDataFromLocalStorage for the initial step
    loadDataFromLocalStorage(currentStep);

    // Immediately determine the visibility of the Previous button based on the current step
    if (currentStep > 1) {
        $('#prev-btn').show();
    } else {
        $('#prev-btn').hide();
    }

    // Hook up the step change event
    $('#prev-btn').on('click', function(e) {
        e.preventDefault(); // Prevent form submission
        changeStep(-1);
    });

    $('#next-btn').on('click', function(e) {
        e.preventDefault(); // Prevent form submission
        changeStep(1);
    });

    // Add click event listener for the Skip button
    $('#skip-btn').on('click', function(e) {
        e.preventDefault(); // Prevent form submission
        changeStep(1); // Jump to the next step
    });

    // Hide all steps initially except the current one
    hideAllSteps();
    $('#step' + currentStep).show();
});

function handleSubmit() {
    const url = new URL(window.location.href);
    const code = url.pathname.split('/').pop(); 
    const formData = $('#multi-step-form').serialize(); // Serialize the form data
    
    console.log('Form data on submit:');
    console.log(formData);

    $.ajax({
        url: `/setup-profile/${code}/submit`, // URL to your controller method
        type: "POST",
        data: formData, // Form data to be sent
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            // Handle success response from the server
            console.log("Form submitted successfully");
            clearFormLocalStorage();
            window.location.href = "/" + code; 
        },
        error: function(xhr, status, error) {
            // Handle error response from the server
            console.error("Error submitting form:", error);
            console.error("Response:", xhr.responseText);
            alert("Error submitting form. Please check console for details.");
        }
    });
}

function clearFormLocalStorage() {
    // Remove specific items related to the form steps
    for (let i = 1; i <= 5; i++) {
        localStorage.removeItem(`formData_step${i}`);
    }
    localStorage.removeItem('currentStep');
}


</script>