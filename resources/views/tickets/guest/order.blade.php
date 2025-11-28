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

    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/MaterialDesign-Webfont/5.3.45/css/materialdesignicons.css"
        integrity="sha256-NAxhqDvtY0l4xn+YVa6WjAcmd94NNfttjNsDmNatFVc=" crossorigin="anonymous" />
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
            margin-top: 1.5rem;
            /* Equivalent to mt-4 */
        }
    }

    body {
        margin-top: 20px;
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
        border-color: #052884 !important
    }


    .font-size-16 {
        font-size: 16px !important;
    }

    .text-truncate {
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    a {
        text-decoration: none !important;
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
        -webkit-transition: border-color .15s ease-in-out, -webkit-box-shadow .15s ease-in-out;
        transition: border-color .15s ease-in-out, -webkit-box-shadow .15s ease-in-out;
        transition: border-color .15s ease-in-out, box-shadow .15s ease-in-out;
        transition: border-color .15s ease-in-out, box-shadow .15s ease-in-out, -webkit-box-shadow .15s ease-in-out;
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
                <div class="col-lg-12 col-md-12 bg-height p-0 d-flex flex-column justify-content-center align-items-center"
                    style="height: 150px; background-color: 
#052884">
                    <h3 class="text-white mt-3" style="font-size: 20px">Checkout</h3>
                    <div class="d-flex justify-content-center align-items-center"> <!-- Adjust height as needed -->
                        <p class="text-white" style="margin-top: 10px; padding: 0 10px;">
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="container-fluid mt-3 mb-3">
            <div class="row">
                <div class="col-xl-8">
                    <div class="card">
                        <div class="card-body">
                            <ol class="activity-checkout mb-0 px-4 mt-3">
                                <!-- Order Info and Client Details -->
                                <li class="checkout-item">
                                    <div class="avatar checkout-icon p-1">
                                        <div class="avatar-title rounded-circle" style="background-color:#052884">
                                            <i class="bx bxs-receipt text-white font-size-20"></i>
                                        </div>
                                    </div>
                                    <div class="feed-item-list">
                                        <div>
                                            <h5 class="font-size-16 mb-1">Details</h5>
                                            <p class="text-muted text-truncate mb-4">Order info and Client details</p>
                                            <div class="mb-3">
                                                <form id="paymentForm">
                                                    <div id="ticket-container">
                                                        <!-- Ticket 1 fields -->
                                                        <div class="ticket" id="ticket-1">
                                                            <h6>Pass</h6>
                                                            <div class="row">
                                                                <div class="col-lg-4">
                                                                    <div class="mb-3">
                                                                        <label class="form-label"
                                                                            for="billing-fname-1">First Name</label>
                                                                        <input type="text" class="form-control"
                                                                            id="billing-fname-1" name="firstName[]"
                                                                            placeholder="Enter first name" required>
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-4">
                                                                    <div class="mb-3">
                                                                        <label class="form-label"
                                                                            for="billing-lname-1">Last Name</label>
                                                                        <input type="text" class="form-control"
                                                                            id="billing-lname-1" name="lastName[]"
                                                                            placeholder="Enter last name" required>
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-4">
                                                                    <div class="mb-3">
                                                                        <label class="form-label"
                                                                            for="billing-email-address-1">Email
                                                                            Address</label>
                                                                        <input type="email" class="form-control"
                                                                            id="billing-email-address-1"
                                                                            name="email[]" placeholder="Enter email"
                                                                            required>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <button type="button" id="addTicketBtn"
                                                        class="btn btn-secondary mt-2 p-0"
                                                        style="border:transparent; background:transparent; color:blue;">Add
                                                        another pass</button>
                                                    <input type="hidden" name="ticket_id" value="{{ $tickets->id }}">
                                                    <input type="hidden" id="totalPrice" name="total_price">
                                                    <input type="hidden" id="quantity" name="quantity">
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </li>

                                <!-- Payment Method Selection -->
                                <li class="checkout-item">
                                    <div class="avatar checkout-icon p-1">
                                        <div class="avatar-title rounded-circle" style="background-color:#052884">
                                            <i class="bx bxs-wallet-alt text-white font-size-20"></i>
                                        </div>
                                    </div>
                                    <div class="feed-item-list">
                                        <div>
                                            <h5 class="font-size-16 mb-1">Payment Info</h5>
                                            <p class="text-muted text-truncate mb-4">Payment Method</p>
                                        </div>
                                        <div>
                                            <h5 class="font-size-14 mb-3">Payment method :</h5>
                                            <div class="row">
                                                
                                                {{-- <div class="col-lg-3 col-sm-6 mb-2">
                                                    <div>
                                                        <label class="card-radio-label">
                                                            <input type="radio" name="pay-method" value="xendit"
                                                                class="card-radio-input" checked>
                                                            <span class="card-radio py-3 text-center text-truncate">
                                                                <i class="bx bx-credit-card d-block h2 mb-3"></i>
                                                                Xendit
                                                            </span>
                                                        </label>
                                                    </div>
                                                </div> --}}
                                                <div class="col-lg-3 col-sm-6 mb-2">
                                                    <div>
                                                        <label class="card-radio-label">
                                                            <input type="radio" name="pay-method" value="paymongo" class="card-radio-input">
                                                            <span class="card-radio py-3 text-center text-truncate d-flex flex-column align-items-center">
                                                                <img src="https://static1.eyellowpages.ph/uploads/yp_business/photo/2066114/normal_paymongo-philippines-inc-1681184832.png" alt="PayMongo" class="d-block h2" style="max-width: 55px;">
                                                                PayMongo
                                                            </span>
                                                        </label>
                                                    </div>
                                                </div>
                                                
                                                
                                                <div class="col-lg-3 col-sm-6 mb-2">
                                                    <div>
                                                        <label class="card-radio-label">
                                                            <input type="radio" name="pay-method"
                                                                value="bank-transfer" class="card-radio-input">
                                                            <span class="card-radio py-3 text-center text-truncate">
                                                                <i class="bx bx-credit-card d-block h2 mt-3"></i>
                                                                Bank Transfer
                                                            </span>
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            </ol>

                            <div class="row my-4">
                                <div class="col">
                                    <a href="/ticket" class="btn btn-link text-muted">
                                        <i class="mdi mdi-arrow-left me-1"></i> Back </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Order Summary and Payment Button -->
                <div class="col-xl-4 pl-0">
                    <div class="card checkout-order-summary">
                        <div class="card-body">
                            <div class="p-3 mb-3" style="background-color:#052884">
                                <h5 class="font-size-16 mb-0 text-white">Order Summary </h5>
                            </div>
                            <hr style="border: none; height: 2px; background-color: black; width: 100%;">
                            <div class="p-2">
                                <h5 class="card-title">Event: {{ $tickets->event->title }}</h5>
                                <p class="card-text mb-1" style="font-size: 14px">
                                    Pass: {{ $tickets->name }}
                                </p>
                                <p class="card-text" style="font-size: 14px; margin-bottom: 0px;">
                                    <span class="bi bi-calendar" style="color: green; font-size: 14px;"> Start Date:
                                    </span>
                                    {{ \Carbon\Carbon::parse($tickets->start_date)->format('F d, Y') }}
                                </p>
                                <p class="card-text mb-1" style="font-size: 14px">
                                    <span class="bi bi-calendar" style="color: red; font-size: 14px;"> End Date:
                                    </span>
                                    {{ \Carbon\Carbon::parse($tickets->end_date)->format('F d, Y') }}
                                </p>
                                <p class="card-text mb-1" style="font-size: 14px">
                                    <strong>Price: ₱<span id="ticket-price">{{ $tickets->price }}</span></strong>
                                </p>
                                <p class="card-text mb-1" style="font-size: 14px">
                                    <strong>Quantity: <span id="quantity-display">1</span></strong>
                                </p>
                            </div>
                            <hr style="border: none; height: 2px; background-color: black; width: 100%;">

                            <div class="d-flex flex-column mb-3">
                                <input type="text" id="discount" class="form-control" placeholder="Enter Discount Code" aria-label="Discount" style="width: 100%;">
                                <div id="discount-message-container" style="margin-top: 5px;"></div>
                            </div>
                            
                            <div class="d-flex justify-content-between fw-bold mb-3">
                                <span>Total</span>
                                <span id="total-price">₱{{ $tickets->price }}</span>
                            </div>

                            <!-- Proceed to Payment Button -->
                            <button type="button" id="proceedPayment" class="btn btn-primary w-100">
                                <span id="proceedText">Proceed to Payment</span>
                                <span id="loadingSpinner" class="spinner-border spinner-border-sm" role="status"
                                    style="display: none;"></span>
                            </button>
                        </div>
                    </div>
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

                {{ __('messages.Copyright') }} &copy; @php echo date('Y'); @endphp <a
                    href="https://tapik.ph">{{ config('app.name') }}</a>

            </div>

        </div>

    </footer>

    <!-- Footer Section End -->

    <script>
        let ticketCount = 1;
        let ticketPrice = parseFloat(document.getElementById('ticket-price').innerText);
        let discountAmount = 0; // Store discount amount globally

        // Add new ticket section
        document.getElementById('addTicketBtn').addEventListener('click', function() {
        ticketCount++;
        const ticketContainer = document.getElementById('ticket-container');
        const ticketDiv = document.createElement('div');
        ticketDiv.classList.add('ticket');
        ticketDiv.id = `ticket-${ticketCount}`;
        ticketDiv.innerHTML = `
            <h6>Pass <button type="button" class="btn btn-sm remove-ticket" style="background:transparent; color:blue;" onclick="removeTicket(${ticketCount})">Remove</button></h6>
            <div class="row">
                <div class="col-lg-4">
                    <div class="mb-3">
                        <label class="form-label" for="billing-fname-${ticketCount}">First Name</label>
                        <input type="text" class="form-control" id="billing-fname-${ticketCount}" name="firstName[]" placeholder="Enter name" required>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="mb-3">
                        <label class="form-label" for="billing-lname-${ticketCount}">Last Name</label>
                        <input type="text" class="form-control" id="billing-lname-${ticketCount}" name="lastName[]" placeholder="Enter name" required>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="mb-3">
                        <label class="form-label" for="billing-email-address-${ticketCount}">Email Address</label>
                        <input type="email" class="form-control" id="billing-email-address-${ticketCount}" name="email[]" placeholder="Enter email" required>
                    </div>
                </div>
            </div>
        `;
        ticketContainer.appendChild(ticketDiv);
        updateTicketSummary();
    });
         // Remove ticket
    function removeTicket(ticketId) {
        const ticketDiv = document.getElementById(`ticket-${ticketId}`);
        ticketDiv.remove();
        ticketCount--;
        updateTicketSummary();
    }

    // Update quantity and total price dynamically, including discount
    function updateTicketSummary() {
        const totalPrice = ticketCount * ticketPrice;
        const discountedPrice = Math.max(0, totalPrice - discountAmount); // Ensure total isn't negative
        document.getElementById('quantity-display').innerText = ticketCount;
        document.getElementById('total-price').innerText = `₱${discountedPrice.toFixed(2)}`;
        document.getElementById('totalPrice').value = discountedPrice;
        document.getElementById('quantity').value = ticketCount;
    }

        function updateTotal() {
            const quantityInput = document.getElementById('quantity');
            const ticketPrice = parseFloat(document.getElementById('ticket-price').innerText);
            const quantity = Math.max(1, parseInt(quantityInput.value)); // Ensure at least 1
            const total = (ticketPrice * quantity).toFixed(2);

            // Update quantity display
            document.getElementById('quantity-display').innerText = quantity;

            // Update total price display
            document.getElementById('total-price').innerText = `₱${total}`;
        }
        document.getElementById('proceedPayment').addEventListener('click', function() {
            let isValid = true;

            // Loop through all ticket sections
            for (let i = 1; i <= ticketCount; i++) {
                const firstName = document.getElementById(`billing-fname-${i}`);
                const lastName = document.getElementById(`billing-lname-${i}`);
                const email = document.getElementById(`billing-email-address-${i}`);

                resetError(firstName, `billing-fname-error-${i}`);
                resetError(lastName, `billing-lname-error-${i}`);
                resetError(email, `billing-email-address-error-${i}`);

                if (firstName.value.trim() === '') {
                    showError(firstName, `billing-fname-error-${i}`, 'First Name is required');
                    isValid = false;
                }
                if (lastName.value.trim() === '') {
                    showError(lastName, `billing-lname-error-${i}`, 'Last Name is required');
                    isValid = false;
                }
                if (email.value.trim() === '') {
                    showError(email, `billing-email-address-error-${i}`, 'Email Address is required');
                    isValid = false;
                }
            }

            if (!isValid) {
                return;
            }

            // Proceed with the AJAX request if the form is valid
            var formData = new FormData(document.getElementById('paymentForm'));
            var selectedMethod = document.querySelector('input[name="pay-method"]:checked').value;
            var endpoint;
            if (selectedMethod === 'xendit') {
                endpoint = '/ticket/xendit-payment';
            } else if (selectedMethod === 'paymongo') {
                endpoint = '/ticket/paymongo-payment';
            } else {
                endpoint = '/ticket/bank-transfer';
            }

            // Disable button and show loading spinner
            var proceedButton = document.getElementById('proceedPayment');
            var loadingSpinner = document.getElementById('loadingSpinner');

            proceedButton.disabled = true;
            loadingSpinner.style.display = 'inline-block';

            fetch(endpoint, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    proceedButton.disabled = false;
                    loadingSpinner.style.display = 'none';
                    if (data.status === "success") {
                        window.location.href = data.url;
                    } else {
                        alert('Payment failed. Please try again.');
                    }
                })
                .catch(error => {
                    proceedButton.disabled = false;
                    loadingSpinner.style.display = 'none';
                    alert('An error occurred. Please try again.');
                });
        });

        // Function to show error
        // Function to show error
        function showError(inputElement, errorElementId, errorMessage) {
            inputElement.style.borderColor = 'red';
            const errorElement = document.getElementById(errorElementId);
            errorElement.style.display = 'block';
            errorElement.textContent = errorMessage;
        }

        // Function to reset error
        function resetError(inputElement, errorElementId) {
            inputElement.style.borderColor = ''; // Reset to default
            const errorElement = document.getElementById(errorElementId); // Get the error element
            if (errorElement) { // Check if the error element exists
                errorElement.style.display = 'none';
            }
        }
        window.addEventListener('DOMContentLoaded', (event) => {
            updateTicketSummary();
        });

        // Discount code validation and application
    document.getElementById('discount').addEventListener('input', function() {
        const discountCode = this.value;
        const quantity = parseInt(document.getElementById('quantity-display').innerText);
        let messageElement = document.getElementById('discount-message');
        if (messageElement) messageElement.remove();

        if (discountCode.length > 0) {
            fetch('/validate-discount', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ code: discountCode })
            })
            .then(response => response.json())
            .then(data => {
                if (!messageElement) {
                    messageElement = document.createElement('span');
                    messageElement.id = 'discount-message';
                }

                if (data.message === 'Discount code is valid') {
                    discountAmount = data.amount; // Set discount amount
                    messageElement.textContent = `Discount code is valid! You got ₱${discountAmount} off!`;
                    messageElement.style.color = 'green';
                } else {
                    discountAmount = 0; // Reset discount amount if invalid
                    messageElement.textContent = 'Invalid or expired discount code';
                    messageElement.style.color = 'red';
                }

                document.getElementById('discount').after(messageElement);
                updateTicketSummary();
            })
            .catch(error => console.error('Error:', error));
        } else {
            discountAmount = 0; // Reset discount amount if code is cleared
            updateTicketSummary();
        }
    });

    </script>

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
