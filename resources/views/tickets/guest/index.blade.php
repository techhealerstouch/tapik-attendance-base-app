<!doctype html>

@include('layouts.lang')

<head>

    <meta charset="utf-8">

    @php $GLOBALS['themeName'] = config('advanced-config.home_theme'); @endphp

    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    @if (env('CUSTOM_META_TAGS') == 'true' and config('advanced-config.title') != '')
        <title>{{ config('advanced-config.title') }}</title>
    @else
        <title>{{ config('app.name') }}</title>
    @endif



    <!--#### BEGIN Meta Tags social media preview images  ####-->

    <!-- This shows a preview for title, description and avatar image of users profiles if shared on social media sites -->



    <!-- Facebook Meta Tags -->

    <meta property="og:url" content="{{ url('') }}">

    <meta property="og:type" content="website">

    <meta property="og:title" content="{{ env('APP_NAME') }}">


    @if (file_exists(base_path('assets/linkstack/images/') . findFile('avatar')))
        <meta property="og:image" content="{{ asset('assets/linkstack/images/' . findFile('avatar')) }}">
    @else
        <meta property="og:image" content="{{ asset('assets/linkstack/images/logo.svg') }}">
    @endif



    <!-- Twitter Meta Tags -->

    <meta name="twitter:card" content="summary_large_image">

    <meta property="twitter:domain" content="{{ url('') }}">

    <meta property="twitter:url" content="{{ url('') }}">

    <meta name="twitter:title" content="{{ env('APP_NAME') }}">

    @if (file_exists(base_path('assets/linkstack/images/') . findFile('avatar')))
        <meta name="twitter:image" content="{{ asset('assets/linkstack/images/' . findFile('avatar')) }}">
    @else
        <meta name="twitter:image" content="{{ asset('assets/linkstack/images/logo.svg') }}">
    @endif



    <!--#### END Meta Tags social media preview images  ####-->



    <!-- Favicon -->

    @if (file_exists(base_path('assets/linkstack/images/') . findFile('favicon')))
        <link rel="icon" type="image/png" href="{{ asset('assets/linkstack/images/' . findFile('favicon')) }}">
    @else
        <link rel="icon" type="image/svg+xml" href="{{ asset('assets/linkstack/images/logo.svg') }}">
    @endif



    <script src="{{ asset('assets/js/detect-dark-mode.js') }}"></script>



    <!-- Library / Plugin Css Build -->

    <link rel="stylesheet" href="{{ asset('assets/css/core/libs.min.css') }}" />



    <!-- Aos Animation Css -->

    <link rel="stylesheet" href="{{ asset('assets/vendor/aos/dist/aos.css') }}" />



    @include('layouts.fonts')



    <!-- Hope Ui Design System Css -->

    <link rel="stylesheet" href="{{ asset('assets/css/hope-ui.min.css?v=2.0.0') }}" />



    <!-- Custom Css -->

    <link rel="stylesheet" href="{{ asset('assets/css/custom.min.css?v=2.0.0') }}" />



    <!-- Dark Css -->

    <link rel="stylesheet" href="{{ asset('assets/css/dark.min.css') }}" />



    <!-- Customizer Css -->

    @if (file_exists(base_path('assets/dashboard-themes/dashboard.css')))
        <link rel="stylesheet" href="{{ asset('assets/dashboard-themes/dashboard.css') }}" />
    @else
        <link rel="stylesheet" href="{{ asset('assets/css/customizer.min.css') }}" />
    @endif



    <!-- RTL Css -->

    <link rel="stylesheet" href="{{ asset('assets/css/rtl.min.css') }}" />

    




</head>



@php

    $pages = DB::table('pages')->get();

    foreach ($pages as $page) {
    }

@endphp



<body class=" " data-bs-spy="scroll" data-bs-target="#elements-section" data-bs-offset="0" tabindex="0">

    <!--Nav Start-->

    <nav class="nav navbar navbar-expand-lg navbar-light iq-navbar fixed-top">

        <div class="container-fluid navbar-inner">

            <a href="{{ route('panelIndex') }}" class="navbar-brand">



                <!--Logo start-->

                <div class="logo-main">

                    @if (file_exists(base_path('assets/linkstack/images/') . findFile('avatar')))
                        <div class="logo-normal">

                            <img class="img logo" src="{{ asset('assets/linkstack/images/' . findFile('avatar')) }}"
                                style="width:auto;height:30px;">

                        </div>

                        <div class="logo-mini">

                            <img class="img logo" src="{{ asset('assets/linkstack/images/' . findFile('avatar')) }}"
                                style="width:auto;height:30px;">

                        </div>
                    @else
                        <div class="logo-normal">

                            <img class="img logo" type="image/svg+xml"
                                src="{{ asset('assets/linkstack/images/logo.svg') }}" width="30px" height="30px">

                        </div>

                        <div class="logo-mini">

                            <img class="img logo" type="image/svg+xml"
                                src="{{ asset('assets/linkstack/images/logo.svg') }}" width="30px" height="30px">

                        </div>
                    @endif

                </div>

                <!--logo End-->





                <!--<h4 class="logo-title">{{ env('APP_NAME') }}</h4>-->

            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false"
                aria-label="Toggle navigation">

                <span class="navbar-toggler-icon">

                    <span class="mt-2 navbar-toggler-bar bar1"></span>

                    <span class="navbar-toggler-bar bar2"></span>

                    <span class="navbar-toggler-bar bar3"></span>

                </span>

            </button>

            <div class="collapse navbar-collapse" id="navbarSupportedContent">

                <ul class="mb-2 navbar-nav ms-auto align-items-center navbar-list mb-lg-0">

                    @if (Route::has('login'))

                        @auth

                            <li class="me-0 me-xl-2">

                                <a class="btn btn-primary btn-sm d-flex gap-2 align-items-center" aria-current="page"
                                    href="{{ url('dashboard') }}">

                                    {{ __('messages.Dashboard') }}

                                </a>

                            </li>
                        @else
                            @if (Route::has('login'))
                                <li class="me-0 me-xl-2">

                                    <a class="btn btn-primary btn-sm d-flex gap-2 align-items-center" aria-current="page"
                                        href="{{ route('login') }}">

                                        {{ __('messages.Log in') }}

                                    </a>

                                </li>
                            @endif



                            @if (env('ALLOW_REGISTRATION') and !config('linkstack.single_user_mode'))
                                <li class="me-0 me-xl-2">

                                    <a class="btn btn-secondary btn-sm d-flex gap-2 align-items-center"
                                        aria-current="page" href="{{ route('register') }}">

                                        {{ __('messages.Register') }}

                                    </a>

                                </li>
                            @endif

                        @endauth

                    @endif

                </ul>

            </div>

        </div>

    </nav>

    <!--Nav End-->



    <!-- loader Start -->

    <div id="loading">

        <div class="loader simple-loader">

            <div class="loader-body"></div>

        </div>
    </div>

    <!-- loader END -->

        <section class="login-content" style="padding-top: 60px">
            <div id="landing" class="container-fluid">
                <div class="row justify-content-center">
                    <div class="col-lg-12 col-md-12 bg-height p-0 d-flex flex-column justify-content-center align-items-center" style="height: 150px; background-color: 
#052884">
                        <h3 class="text-white mt-3" style="font-size: 20px">Get Your Pass Now!</h3>
                        <div class="d-flex justify-content-center align-items-center"> <!-- Adjust height as needed -->
                            <p class="text-white" style="margin-top: 10px; padding: 0 10px;">
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div id="projects" class="container">
                <div class="row justify-content-center align-items-center">
                    @foreach($tickets as $ticket)
                    <div class="col-md-4 d-flex justify-content-center p-lg-5 my-3">
                            <div class="card mb-5 mb-xl-0 card-style" >
                                <img src="{{ asset('assets/linkstack/images/' . findFile('avatar')) }}"" class="card-img-top" alt="Project Image">
                                <div class="card-body">
                                    <h5 class="card-title">{{$ticket->event->title}}</h5>
                                    <p class="card-text" style="font-size: 14px">
                                        {{$ticket->name}}
                                    </p>
                                    <p class="card-text" style="font-size: 14px; margin-bottom: 0px;">
                                        <span class="bi bi-calendar" style="color: green; font-size: 14px;"> Start Date: </span>
                                        {{ \Carbon\Carbon::parse($ticket->start_date)->format('F d, Y') }}
                                    </p>
                                    <p class="card-text" style="font-size: 14px">
                                        <span class="bi bi-calendar" style="color: red; font-size: 14px;"> End Date: </span>
                                        {{ \Carbon\Carbon::parse($ticket->end_date)->format('F d, Y') }}
                                    </p>
                                    <p class="card-text" style="font-size: 14px">
                                        ₱{{$ticket->price}}
                                    </p>
                                    <div class="d-flex justify-content-center">
                                        <a href="/ticket/{{$ticket->id}}" class="btn btn-primary" style="background-color: #052884; border-color: transparent !important; padding-top: 6px; padding-bottom: 6px; border-radius: 20px;"> Buy Pass</a>
                                    </div>
                                </div>
                            </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </section>



    <!-- Footer Section Start -->

    <footer class="footer fixed-bottom">

        <div class="footer-body justify-content-center">

            <ul class="left-panel list-inline mb-0 p-0">

                @if (env('DISPLAY_FOOTER') === true)

                    @if (env('DISPLAY_FOOTER_HOME') === true)
                        <li class="list-inline-item"><a class="list-inline-item"
                                href="@if (str_replace('"', '', EnvEditor::getKey('HOME_FOOTER_LINK')) === '') {{ url('') }}@else{{ str_replace('"', '', EnvEditor::getKey('HOME_FOOTER_LINK')) }} @endif">{{ footer('Home') }}</a>
                        </li>
                    @endif

                    @if (env('DISPLAY_FOOTER_TERMS') === true)
                        <li class="list-inline-item"><a class="list-inline-item"
                                href="{{ url('') }}/pages/{{ strtolower(footer('Terms')) }}">{{ footer('Terms') }}</a>
                        </li>
                    @endif

                    @if (env('DISPLAY_FOOTER_PRIVACY') === true)
                        <li class="list-inline-item"><a class="list-inline-item"
                                href="{{ url('') }}/pages/{{ strtolower(footer('Privacy')) }}">{{ footer('Privacy') }}</a>
                        </li>
                    @endif

                    @if (env('DISPLAY_FOOTER_CONTACT') === true)
                        <li class="list-inline-item"><a class="list-inline-item"
                                href="{{ url('') }}/pages/{{ strtolower(footer('Contact')) }}">{{ footer('Contact') }}</a>
                        </li>
                    @endif

                @endif

            </ul>

            <div class="right-panel ">

                {{ __('messages.Copyright') }} &copy; @php echo date('Y'); @endphp <a href="{{ config('app.url') }}">{{ config('app.name') }}</a>

            </div>

        </div>

    </footer>

    <!-- Footer Section End -->



    <!-- Library Bundle Script -->

    <script src="{{ asset('assets/js/core/libs.min.js') }}"></script>



    <!-- External Library Bundle Script -->

    <script src="{{ asset('assets/js/core/external.min.js') }}"></script>



    <!-- Widgetchart Script -->

    <script src="{{ asset('assets/js/charts/widgetcharts.js') }}"></script>



    <!-- mapchart Script -->

    <script src="{{ asset('assets/js/charts/vectore-chart.js') }}"></script>

    <script src="{{ asset('assets/js/charts/dashboard.js') }}"></script>



    <!-- fslightbox Script -->

    <script src="{{ asset('assets/js/plugins/fslightbox.js') }}"></script>



    <!-- Settings Script -->

    <script src="{{ asset('assets/js/plugins/setting.js') }}"></script>



    <!-- Slider-tab Script -->

    <script src="{{ asset('assets/js/plugins/slider-tabs.js') }}"></script>



    <!-- Form Wizard Script -->

    <script src="{{ asset('assets/js/plugins/form-wizard.js') }}"></script>



    <!-- AOS Animation Plugin-->

    <script src="{{ asset('assets/vendor/aos/dist/aos.js') }}"></script>



    <!-- App Script -->

    <script src="{{ asset('assets/js/hope-ui.js') }}" defer></script>



    <!-- Flatpickr Script -->

    <script src="{{ asset('assets/vendor/flatpickr/dist/flatpickr.min.js') }}"></script>

    <script src="{{ asset('assets/js/plugins/flatpickr.js') }}" defer></script>



    <script src="{{ asset('assets/js/plugins/prism.mini.js') }}"></script>



</body>

</html>
