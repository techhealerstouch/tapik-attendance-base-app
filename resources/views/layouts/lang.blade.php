<html lang="{{ config('app.locale') }}">

@if(file_exists(base_path("assets/linkstack/images/").findFile('favicon')))
<link rel="icon" type="image/png" href="{{ asset('assets/linkstack/images/'.findFile('favicon')) }}">
@else
<link rel="icon" type="image/svg+xml" href="{{ asset('assets/linkstack/images/logo.svg') }}">
@endif

<!-- Library / Plugin Css Build -->
<link rel="stylesheet" href="{{asset('assets/css/core/libs.min.css')}}" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
<!-- Aos Animation Css -->
<link rel="stylesheet" href="{{asset('assets/vendor/aos/dist/aos.css')}}" />

@include('layouts.fonts')

<!-- Hope Ui Design System Css -->
<link rel="stylesheet" href="{{asset('assets/css/hope-ui.min.css?v=2.0.0')}}" />

<!-- Custom Css -->
<link rel="stylesheet" href="{{asset('assets/css/custom.min.css?v=2.0.0')}}" />

<!-- Dark Css -->
<link rel="stylesheet" href="{{asset('assets/css/dark.min.css')}}" />

<!-- Customizer Css -->



<!-- RTL Css -->
<link rel="stylesheet" href="{{asset('assets/css/rtl.min.css')}}" />

<meta name="csrf-token" content="{{ csrf_token() }}">
<link rel="stylesheet" href="{{ asset('assets/linkstack/css/hover-min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/linkstack/css/animate.css') }}">
<link rel="stylesheet" href="{{ asset('assets/external-dependencies/bootstrap-icons.css') }}">

