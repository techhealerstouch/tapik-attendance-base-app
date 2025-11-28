<?php
$pages = DB::table('pages')->get();
foreach($pages as $page)
{
	//Gets value from database
}
?>

<x-guest-layout>
@include('layouts.lang')

    <x-auth-card>
        <x-slot name="logo"></x-slot>

        <!-- Session Status -->
        <x-auth-session-status class="mb-4" :status="session('status')" />

        <!-- Validation Errors -->
        <x-auth-validation-errors class="mb-4" :errors="$errors" />

        <div class="container mt-5 w-100">
          <div class="card p-5">
              <!--<a href="{{ url('') }}" class="d-flex align-items-center mb-3">
                Logo start
                <div class="logo-main">
                    @if(file_exists(base_path("assets/linkstack/images/").findFile('avatar')))
                    <div class="logo-normal">
                      <img class="img logo" src="{{ asset('assets/linkstack/images/'.findFile('avatar')) }}" style="width:auto;height:30px;">
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
                   logo End
                <h4 class="logo-title ms-3">{{env('APP_NAME')}}</h4>
              </a>-->
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
                <h2 class="mb-2 text-center mt-4">Account Activated!</h2>

                <p class="text-center"><b>Your account has bee activated</b></p>

                <p class="text-center mb-4">Click Get Started to setup your profile</p>

                <div class="row">               
                <div class="d-flex justify-content-center">
              <button id="getStartedBtn" class="btn btn-primary">Get Started</button>
            </div>
            </div>
          </div>          

    </x-auth-card>
</x-guest-layout>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('getStartedBtn').addEventListener('click', function(event) {
        event.preventDefault(); // Prevent the default form submission behavior

        // Use window.location.pathname to get the path part of the URL
        const path = window.location.pathname;
        const segments = path.split('/');
        const code = segments[segments.length - 1]; // Assuming the code is the last segment

        // Construct the new URL
        const newUrl = '/setup-profile/' + code;
        console.log(newUrl)
        // Redirect to the new URL
        window.location.href = newUrl;
    });
});
</script>

