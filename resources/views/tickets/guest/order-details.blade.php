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

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/MaterialDesign-Webfont/5.3.45/css/materialdesignicons.css" integrity="sha256-NAxhqDvtY0l4xn+YVa6WjAcmd94NNfttjNsDmNatFVc=" crossorigin="anonymous" />
<link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>

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

<style>
    @media (max-width: 991.98px) {
    .mt-small {
        margin-top: 1.5rem; /* Equivalent to mt-4 */
    }
    }
    body{margin-top:20px;
    background-color: #f1f3f7;
    }

    .card {
        margin-bottom: 24px;
        -webkit-box-shadow: 0 2px 3px #e4e8f0;
        box-shadow: 0 2px 3px #e4e8f0;
    }
    .card {
        position: relative;
        display: -webkit-box;
        display: -ms-flexbox;
        display: flex;
        -webkit-box-orient: vertical;
        -webkit-box-direction: normal;
        -ms-flex-direction: column;
        flex-direction: column;
        min-width: 0;
        word-wrap: break-word;
        background-color: #fff;
        background-clip: border-box;
        border: 1px solid #eff0f2;
        border-radius: 1rem;
    }
    .activity-checkout {
        list-style: none
    }

    .activity-checkout .checkout-icon {
        position: absolute;
        top: -4px;
        left: -24px
    }

    .activity-checkout .checkout-item {
        position: relative;
        padding-bottom: 24px;
        padding-left: 35px;
        border-left: 2px solid #f5f6f8
    }

    .activity-checkout .checkout-item:first-child {
        border-color: #052884
    }

    .activity-checkout .checkout-item:first-child:after {
        background-color: #052884
    }

    .activity-checkout .checkout-item:last-child {
        border-color: transparent
    }

    .activity-checkout .checkout-item.crypto-activity {
        margin-left: 50px
    }

    .activity-checkout .checkout-item .crypto-date {
        position: absolute;
        top: 3px;
        left: -65px
    }



    .avatar-xs {
        height: 1rem;
        width: 1rem
    }

    .avatar-sm {
        height: 2rem;
        width: 2rem
    }

    .avatar {
        height: 3rem;
        width: 3rem
    }

    .avatar-md {
        height: 4rem;
        width: 4rem
    }

    .avatar-lg {
        height: 5rem;
        width: 5rem
    }

    .avatar-xl {
        height: 6rem;
        width: 6rem
    }

    .avatar-title {
        -webkit-box-align: center;
        -ms-flex-align: center;
        align-items: center;
        background-color: #052884;
        color: #fff;
        display: -webkit-box;
        display: -ms-flexbox;
        display: flex;
        font-weight: 500;
        height: 100%;
        -webkit-box-pack: center;
        -ms-flex-pack: center;
        justify-content: center;
        width: 100%
    }

    .avatar-group {
        display: -webkit-box;
        display: -ms-flexbox;
        display: flex;
        -ms-flex-wrap: wrap;
        flex-wrap: wrap;
        padding-left: 8px
    }

    .avatar-group .avatar-group-item {
        margin-left: -8px;
        border: 2px solid #fff;
        border-radius: 50%;
        -webkit-transition: all .2s;
        transition: all .2s
    }

    .avatar-group .avatar-group-item:hover {
        position: relative;
        -webkit-transform: translateY(-2px);
        transform: translateY(-2px)
    }

    .card-radio {
        background-color: #fff;
        border: 2px solid #eff0f2;
        border-radius: .75rem;
        padding: .5rem;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        display: block
    }

    .card-radio:hover {
        cursor: pointer
    }

    .card-radio-label {
        display: block
    }

    .edit-btn {
        width: 35px;
        height: 35px;
        line-height: 40px;
        text-align: center;
        position: absolute;
        right: 25px;
        margin-top: -50px
    }

    .card-radio-input {
        display: none
    }

    .card-radio-input:checked+.card-radio {
        border-color: #052884!important
    }


    .font-size-16 {
        font-size: 16px!important;
    }
    .text-truncate {
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    a {
        text-decoration: none!important;
    }


    .form-control {
        display: block;
        width: 100%;
        padding: 0.47rem 0.75rem;
        font-size: .875rem;
        font-weight: 400;
        line-height: 1.5;
        color: #545965;
        background-color: #fff;
        background-clip: padding-box;
        border: 1px solid #e2e5e8;
        -webkit-appearance: none;
        -moz-appearance: none;
        appearance: none;
        border-radius: 0.75rem;
        -webkit-transition: border-color .15s ease-in-out,-webkit-box-shadow .15s ease-in-out;
        transition: border-color .15s ease-in-out,-webkit-box-shadow .15s ease-in-out;
        transition: border-color .15s ease-in-out,box-shadow .15s ease-in-out;
        transition: border-color .15s ease-in-out,box-shadow .15s ease-in-out,-webkit-box-shadow .15s ease-in-out;
    }

    .edit-btn {
        width: 35px;
        height: 35px;
        line-height: 40px;
        text-align: center;
        position: absolute;
        right: 25px;
        margin-top: -50px;
    }

    .ribbon {
        position: absolute;
        right: -26px;
        top: 20px;
        -webkit-transform: rotate(45deg);
        transform: rotate(45deg);
        color: #fff;
        font-size: 13px;
        font-weight: 500;
        padding: 1px 22px;
        font-size: 13px;
        font-weight: 500
    }
</style>

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

        <section class="login-content" style="padding-top: 30px">
            <div id="landing" class="container-fluid">
                <div class="row justify-content-center">
                    <div class="col-lg-12 col-md-12 bg-height p-0 d-flex flex-column justify-content-center align-items-center" style="height: 150px; background-color: 
#052884">
                        <h3 class="text-white mt-3" style="font-size: 20px">Order Details</h3>
                        <div class="d-flex justify-content-center align-items-center"> <!-- Adjust height as needed -->
                            <p class="text-white" style="margin-top: 10px; padding: 0 10px;">
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="container mt-5 d-flex justify-content-center">
                <div class="card p-4 mt-3">
                    <div class="first d-flex justify-content-between align-items-center mb-3">
                        <div class="info">
                            <span class="d-block name">Thank you, {{$invoice->first_name}}</span>
                            <span class="order">Invoice # - {{$invoice->invoice_no}}</span>
                        </div>
                    </div>
                    <div class="detail">
                        <span class="d-block summery">Your order has been created. Please check your email for the billing information.</span>
                    </div>
                    <hr style="color: #000; height: 2px; border: none; background-color: #000;">
                    <div class="text">
                        <span class="d-block new mb-1">Pass Details:</span>
                    </div>
                    <div class="p-0">
                        <h5 class="card-title">{{$invoice->ticket->event->title}}</h5>
                        <p class="card-text mb-1" style="font-size: 14px"><strong>Pass:</strong> {{$invoice->ticket->name}}</p>
                        <p class="card-text mb-1" style="font-size: 14px"><strong>Status:</strong> <span id="quantity-display">{{$invoice->status}}</span></p>
                        <p class="card-text mb-1" style="font-size: 14px"><strong>Quantity:</strong> <span id="quantity-display">{{$invoice->quantity}}</span></p>
                    </div>
                    <div class="d-flex justify-content-between fw-bold mb-3">
                        <span>Total</span>
                        <span id="total-price">₱{{$invoice->amount}}</span>
                    </div>
                    <hr style="color: #000; height: 2px; border: none; background-color: #000;">
                    <div class="last d-flex align-items-center mt-2 justify-content-center">
                        <span class="address-line">If you have any questions or concerns, contact us:</span>
                    </div>
                    <div class="money d-flex flex-column mt-1 align-items-center">
                        <span class="ml-2">admin@philtoa.ph</span>
                        <span class="ml-2">placeholder@gmail.com</span>
                        <span class="ml-2">+63977 804 3787</span>
                    </div>
                    
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

                {{ __('messages.Copyright') }} &copy; @php echo date('Y'); @endphp <a href="https://tapik.ph">{{ config('app.name') }}</a>

            </div>

        </div>

    </footer>

    <style>
        body{
	background-color: #eee;
}
.card{
	    background-color: #fff;
    width: 500px;
    border: none;
    border-radius: 16px;

}

.info{
      line-height: 19px;
}

.name{
	color: #052884;
    font-size: 18px;
    font-weight: 600;
}

.order{
	font-size: 14px;
	font-weight: 400;
	color: #818181;
}
.detail{

	line-height:19px;
}

.summery{


	    color: #818181;
    font-weight: 400;
    font-size: 13px;
}
   
.last, .money {
    text-align: center; /* Center text within the div */
}

.text{

	line-height:15px;
}
.new{

	color: #000;
	font-size: 14px;
	font-weight: 600;
}
.money{


	font-size: 14px;
	font-weight:500;
}
.address{

color: #818181;
	font-weight:500;
	font-size:14px;
}

.last{

	font-size: 10px;
	font-weight: 500;

}


.address-line{
	color: #052884;
    font-size: 11px;
    font-weight: 700;
}
    </style>

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
