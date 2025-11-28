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
              <p class="text-center">Activate your profile!</p>
              <form id="activate-profile-form" method="POST" action="{{ route('activate.profile') }}" enctype="multipart/form-data">

                @csrf
                <div class="row">
                  <div class="col-lg-12">
                      <div class="form-group">
                          <label for="code" class="form-label">Code</label>
                          <input type="text" class="form-control @if(isset($invalidCode) && $invalidCode) is-invalid @endif" id="code" name="code" aria-describedby="code" placeholder=" " :value="old('code')" required autofocus>
                          <div class="text-danger" id="invalid-code-message" style="display: none;">Please provide a valid activation code.</div>
                      </div>
                  </div>
                    <div class="col-lg-12">
                      <div class="form-group">
                        <label for="name" class="form-label">{{__('messages.Display Name')}}</label>
                        <input type="text" class="form-control" id="name" name="name" aria-describedby="name" placeholder=" " :value="old('name')" required autofocus >
                      </div>
                    </div>
                   <!-- @include('auth.url-validation')     --> 
                   
                   <div class="col-lg-12">
                    <label class="form-label" for="customFile">{{__('messages.Profile Picture')}}</label>
                    <input type="file" accept="image/jpeg,image/jpg,image/png,image/webp" name="image" class="form-control" id="customFile">
                  </div>
                       
                    <div class="col-lg-12">
                      <div class="form-group">
                        <label for="email" class="form-label">{{__('messages.Email')}}</label>
                        <input type="email" class="form-control" id="email" name="email" aria-describedby="email" placeholder=" " :value="old('email')" required autofocus >
                      </div>
                    </div>
                    <div class="col-lg-12">
                    <div class="form-group">
                        <label for="password" class="form-label">{{__('messages.Password')}}</label>
                        <input type="password" class="form-control" id="password" aria-describedby="password" placeholder=" " name="password" required autocomplete="new-password" />
                        <i class="fas fa-eye toggle-password" id="togglePassword"></i>
                    </div>
                    </div>
                    <div class="col-lg-12 d-flex justify-content-between">
                      <div class="form-check mb-3">
                        <input type="checkbox" class="form-check-input" name="remember" id="remember_me">
                        <label class="form-check-label" for="remember_me">{{__('messages.Remember Me')}}</label>
                      </div>
                    </div>
                  </div>                  
                <div class="d-flex justify-content-center">
                  <button id="submit-btn" type="submit" class="btn btn-primary">Activate</button>
                </div>
                @if(env('ENABLE_SOCIAL_LOGIN') == 'true')
                <p class="text-center my-3">{{__('messages.or sign in with other accounts?')}}</p>
                <div class="d-flex justify-content-center">
                  <ul class="list-group list-group-horizontal list-group-flush">
                    @if(!empty(env('FACEBOOK_CLIENT_ID')))
                    <li class="list-group-item border-0 pb-0">
                      <a href="{{ route('social.redirect','facebook') }}">
                        <i class="bi bi-facebook"></i>
                      </a>
                    </li>
                    @endif
                    @if(!empty(env('TWITTER_CLIENT_ID')))
                    <li class="list-group-item border-0 pb-0">
                      <a href="{{ route('social.redirect','twitter') }}">
                        <i class="bi bi-twitter"></i>
                      </a>
                    </li>
                    @endif
                    @if(!empty(env('GOOGLE_CLIENT_ID')))
                    <li class="list-group-item border-0 pb-0">
                      <a href="{{ route('social.redirect','google') }}">
                        <i class="bi bi-google"></i>
                      </a>
                    </li>
                    @endif
                    @if(!empty(env('GITHUB_CLIENT_ID')))
                    <li class="list-group-item border-0 pb-0">
                      <a href="{{ route('social.redirect','github') }}">
                        <i class="bi bi-github"></i>
                      </a>
                    </li>
                    @endif
                  </ul>
                </div>
                @else
                <br>
                @endif
              </form>
            </div>
          </div>          

    </x-auth-card>
</x-guest-layout>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script>
    document.getElementById('code').addEventListener('input', function() {
        this.classList.remove('is-invalid');
        var invalidFeedback = this.parentNode.querySelector('.invalid-feedback');
        if (invalidFeedback) {
            invalidFeedback.style.display = 'none';
        }
    });

    $(document).ready(function() {
    $('#activate-profile-form').on('submit', function(e) {
        e.preventDefault();

        var formData = new FormData(this);

        $.ajax({
            type: 'POST',
            url: $(this).attr('action'),
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                console.log('success');
                window.location.href = "/get-started/" + response.code; 
            },
            error: function(jqXHR, textStatus, errorThrown) {
                $('#code').addClass('is-invalid'); 
                $('#code').parent().find('.invalid-feedback').show();
                $('#invalid-code-message').show();
            }
        });
    });

    $('#code').on('input', function() {
        $(this).removeClass('is-invalid');
        $(this).parent().find('.invalid-feedback').hide();
        $('#invalid-code-message').hide();
    });
});

const togglePassword = document.querySelector('#togglePassword');
        const password = document.querySelector('#password');

        togglePassword.addEventListener('click', function (e) {
            // Toggle the type attribute using getAttribute() and setAttribute()
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);

            // Toggle the eye / eye slash icon
            this.classList.toggle('fa-eye');
            this.classList.toggle('fa-eye-slash');
        });
    
</script>

<style>
        .form-group {
            position: relative;
            display: flex;
            flex-direction: column;
        }

        .form-control {
            padding-right: 2.5rem; /* Adjust padding to make space for the icon */
        }

        .toggle-password {
            position: absolute;
            right: 10px;
            top: 70%;
            transform: translateY(-50%);
            cursor: pointer;
        }
    </style>
