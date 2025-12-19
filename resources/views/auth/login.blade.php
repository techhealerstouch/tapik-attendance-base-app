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

        <div class="container mt-5 d-flex justify-content-center">
          <div class="card p-5 shadow-lg" style="max-width:480px; width:100%; border-radius:16px;">
              
              <!-- Centered Logo -->
              <div class="text-center mb-4">
                <a href="{{ url('') }}" class="d-inline-block">
                  <div class="logo-main">
                    @if(file_exists(base_path("assets/linkstack/images/").findFile('avatar')))
                      <img class="img logo" src="{{ asset('assets/linkstack/images/'.findFile('avatar')) }}" style="width:auto; height:100px; object-fit:contain;">
                    @else
                      <img class="img logo" type="image/svg+xml" src="{{ asset('assets/linkstack/images/logo.svg') }}" style="width:80px; height:80px;">
                    @endif
                  </div>
                </a>
              </div>

              <!-- Header -->
              <h2 class="mb-2 text-center fw-bold">{{__('messages.Sign In')}}</h2>
              <p class="text-center text-muted mb-4">{{__('messages.Login to stay connected')}}</p>

              <!-- Login Form -->
              <form method="POST" action="{{ route('login') }}">
                @csrf
                
                <div class="mb-3">
                  <label for="email" class="form-label fw-semibold">{{__('messages.Email')}}</label>
                  <input type="email" 
                         class="form-control form-control-lg" 
                         id="email" 
                         name="email" 
                         placeholder="your@email.com" 
                         :value="old('email')" 
                         required 
                         autofocus>
                </div>

                <div class="mb-3">
                  <label for="password" class="form-label fw-semibold">{{__('messages.Password')}}</label>
                  <input type="password" 
                         class="form-control form-control-lg" 
                         id="password" 
                         name="password" 
                         placeholder="Enter your password" 
                         required 
                         autocomplete="current-password">
                </div>

                <div class="d-flex justify-content-between align-items-center mb-4">
                  <div class="form-check">
                    <input type="checkbox" class="form-check-input" name="remember" id="remember_me">
                    <label class="form-check-label" for="remember_me">{{__('messages.Remember Me')}}</label>
                  </div>
                  <a href="{{ route('password.request') }}" class="text-decoration-none">{{__('messages.Forgot Password?')}}</a>
                </div>

                <div class="d-grid mb-3">
                  <button type="submit" class="btn btn-primary btn-lg">{{__('messages.Sign In')}}</button>
                </div>

                @if(env('ENABLE_SOCIAL_LOGIN') == 'true')
                <div class="position-relative my-4">
                  <hr>
                  <span class="position-absolute top-50 start-50 translate-middle bg-white px-3 text-muted">
                    {{__('messages.or sign in with other accounts?')}}
                  </span>
                </div>

                <div class="d-flex justify-content-center gap-3 mb-3">
                  @if(!empty(env('FACEBOOK_CLIENT_ID')))
                  <a href="{{ route('social.redirect','facebook') }}" class="btn btn-outline-secondary btn-lg" style="width:50px; height:50px; border-radius:50%; display:flex; align-items:center; justify-content:center;">
                    <i class="bi bi-facebook"></i>
                  </a>
                  @endif
                  
                  @if(!empty(env('TWITTER_CLIENT_ID')))
                  <a href="{{ route('social.redirect','twitter') }}" class="btn btn-outline-secondary btn-lg" style="width:50px; height:50px; border-radius:50%; display:flex; align-items:center; justify-content:center;">
                    <i class="bi bi-twitter"></i>
                  </a>
                  @endif
                  
                  @if(!empty(env('GOOGLE_CLIENT_ID')))
                  <a href="{{ route('social.redirect','google') }}" class="btn btn-outline-secondary btn-lg" style="width:50px; height:50px; border-radius:50%; display:flex; align-items:center; justify-content:center;">
                    <i class="bi bi-google"></i>
                  </a>
                  @endif
                  
                  @if(!empty(env('GITHUB_CLIENT_ID')))
                  <a href="{{ route('social.redirect','github') }}" class="btn btn-outline-secondary btn-lg" style="width:50px; height:50px; border-radius:50%; display:flex; align-items:center; justify-content:center;">
                    <i class="bi bi-github"></i>
                  </a>
                  @endif
                </div>
                @endif

                @if ((env('ALLOW_REGISTRATION')) and !config('linkstack.single_user_mode'))
                <p class="text-center text-muted mb-0">
                  {{__('messages.Don\'t have an account?')}} 
                  <a href="{{ route('register') }}" class="text-decoration-none fw-semibold">{{__('messages.Click here to sign up')}}</a>
                </p>
                @endif
              </form>
            </div>
          </div>

    </x-auth-card>
</x-guest-layout>