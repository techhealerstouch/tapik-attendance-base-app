@extends('layouts.sidebar')

@section('content')
    <script src="{{ asset('resources/ckeditor/ckeditor.js') }}"></script>
    <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/js/all.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.8/css/dataTables.bootstrap5.css">
    <script src="https://cdn.datatables.net/2.0.8/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/2.0.8/js/dataTables.bootstrap5.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.0/xlsx.full.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js" defer></script>
    <svg xmlns="http://www.w3.org/2000/svg" style="display: none;">
        <symbol id="check-circle-fill" fill="currentColor" viewBox="0 0 16 16">
            <path
                d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z" />
        </symbol>
        <symbol id="info-fill" fill="currentColor" viewBox="0 0 16 16">
            <path
                d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z" />
        </symbol>
        <symbol id="exclamation-triangle-fill" fill="currentColor" viewBox="0 0 16 16">
            <path
                d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z" />
        </symbol>
    </svg>
    <div class="container-fluid content-inner mt-n5 py-0">
        @if (session('success'))
            <div class="alert alert-success d-flex align-items-center justify-content-between" role="alert">
                <div>
                    <svg class="bi flex-shrink-0 me-2" width="24" height="24" role="img" aria-label="Success:">
                        <use xlink:href="#check-circle-fill" />
                    </svg>
                    {{ session('success') }}
                </div>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        <!-- Error Message -->
        @if (session('error'))
            <div class="alert alert-danger d-flex align-items-center justify-content-between" role="alert">
                <div>
                    <svg class="bi flex-shrink-0 me-2" width="24" height="24" role="img" aria-label="Danger:">
                        <use xlink:href="#exclamation-triangle-fill" />
                    </svg>
                    {{ session('error') }}
                </div>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        <div class="row">
            <div class="col-lg-12">
                <div class="card rounded">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-12">
                                <section class="text-gray-400">
                                    <div class="col-sm-6">
                                        <ol class="breadcrumb">
                                            <li class="breadcrumb-item"><a href="/dashboard">Home</a></li>
                                            <li class="breadcrumb-item">Order</li>
                                            <li class="breadcrumb-item">View Order</li>
                                        </ol>
                                    </div>
                                    <div class="col-sm-6 text-right">
                                        <button id="printInvoice" class="btn btn-primary">Print Order</button>
                                        @if($invoices->status === "PAID")
                                            <button disabled class="btn btn-success">Paid</button>
                                        @endif
                                        @if($invoices->status === "PENDING")
                                            <button class="btn btn-danger" id="expireInvoiceBtn" data-invoice-id="{{ $invoices->xendit_invoice_no }}">Void</button>
                                        @endif
                                        @if($invoices->status === "EXPIRED")
                                            <button disabled class="btn btn-warning">Expired</button>
                                        @endif
                                        
                                    </div>
                                    <div id="invoiceholder">

                                        <div id="headerimage"></div>
                                        <div id="invoice" class="effect2">

                                            <div id="invoice-top">
                                                <a href="{{ route('panelIndex') }}" class="navbar-brand">
                                                    <!-- Logo Start -->
                                                    <img class="img logo"
                                                        src="{{ asset('assets/linkstack/images/' . findFile('avatar')) }}"
                                                        style="width:auto;height:70px;">
                                                </a>
                                                <div class="title">
                                                    <h1>Order# {{ $invoices->invoice_no }}</h1>
                                                    <p>Issued:
                                                        {{ \Carbon\Carbon::parse($invoices->created_at)->format('F d, Y') }}</br>
                                                        Payment Due:
                                                        {{ \Carbon\Carbon::parse($invoices->expiry_date)->format('F d, Y') }}
                                                    </p>

                                                </div><!--End Title-->
                                            </div><!--End InvoiceTop-->



                                            <div id="invoice-mid">
                                                <div class="info">
                                                    <h2>{{ $invoices->user->name ?? $invoices->first_name . ' ' . $invoices->last_name}}</h2>
                                                    <p>{{ $invoices->user->email ?? $invoices->email}}</br>
                                                </div>

                                                <div id="project">
                                                    <h2>Order Description</h2>
                                                    <p>{{ $invoices->description }}</p>
                                                </div>

                                            </div><!--End Invoice Mid-->

                                            <div id="invoice-bot">

                                                <div id="table">
                                                    <table>
                                                        <tr class="tabletitle">
                                                            <td class="item">
                                                                <h2>Item</h2>
                                                            </td>
                                                            <td class="Hours">
                                                                <h2>Status</h2>
                                                            </td>
                                                            <td class="subtotal">
                                                                <h2>Expiry Date</h2>
                                                            </td>
                                                            <td class="subtotal">
                                                                <h2>Payment Method</h2>
                                                            </td>

                                                            <td class="Rate">
                                                                <h2>Amount</h2>
                                                            </td>
                                                        </tr>

                                                        <tr class="service">
                                                            <td class="tableitem">
                                                                <p class="itemtext">
                                                                    <strong>{{ $invoices->ticket->event->title }}</strong>
                                                                    <br>{{ $invoices->ticket->name }}</p>
                                                            </td>
                                                            <td class="tableitem">
                                                                <p class="itemtext">{{ $invoices->status ?? ''}}</p>
                                                            </td>
                                                            <td class="tableitem">
                                                                <p class="itemtext">
                                                                    {{ \Carbon\Carbon::parse($invoices->expiry_date)->format('F d, Y') }}
                                                                </p>
                                                            </td>
                                                            <td class="tableitem">
                                                                <p class="itemtext">{{ $invoices->payment_method ?? '' }}
                                                                </p>
                                                            </td>
                                                            <td class="tableitem">
                                                                <p class="itemtext">₱{{ $invoices->amount }}</p>
                                                            </td>
                                                        </tr>

                                                        <tr class="tabletitle">
                                                            <td></td>
                                                            <td></td>
                                                            <td></td>
                                                            <td class="Rate">
                                                                <h2>Total</h2>
                                                            </td>
                                                            <td class="payment">
                                                                <h2>₱{{ $invoices->amount ?? '' }}</h2>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </div><!--End Table-->


                                                <div id="legalcopy">
                                                    <p class="legal"><strong>Thank you for your business!</strong>  Payment
                                                        is expected within 31 days; please process this invoice within that
                                                        time. There will be a 5% interest charge per month on late invoices.
                                                    </p>
                                                </div>

                                            </div><!--End InvoiceBot-->
                                        </div><!--End Invoice-->
                                    </div><!-- End Invoice Holder-->
                                </section>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/2.0.8/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/2.0.8/js/dataTables.bootstrap5.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.0/xlsx.full.min.js"></script>
    <script>
        document.getElementById("printInvoice").addEventListener("click", function() {
            // Get the invoice holder content
            var invoiceContent = document.getElementById("invoiceholder").innerHTML;

            // Create a hidden iframe
            var printFrame = document.createElement('iframe');
            printFrame.style.position = 'absolute';
            printFrame.style.width = '0';
            printFrame.style.height = '0';
            printFrame.style.border = 'none';
            document.body.appendChild(printFrame);

            // Write content to the iframe
            var doc = printFrame.contentWindow || printFrame.contentDocument;
            doc.document.open();
            doc.document.write('<html><head><title>Print Invoice</title>');
            doc.document.write('<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">'); // Add your styles here
            doc.document.write('<style>@import url(https://fonts.googleapis.com/css?family=Roboto:100,300,400,900,700,500,300,100);*{margin:0;box-sizing:border-box;}body{background:#E0E0E0;font-family:"Roboto",sans-serif;background-repeat:repeat-y;background-size:100%;}::selection{background:#f31544;color:#FFF;}h1{font-size:1.5em;color:#222;}h2{font-size:.9em;}h3{font-size:1.2em;font-weight:300;line-height:2em;}p{font-size:.7em;color:#666;line-height:1.2em;}#invoiceholder{width:100%;height:100%;}#headerimage{z-index:-1;position:relative;top:-50px;height:350px;background-image:url("http://michaeltruong.ca/images/invoicebg.jpg");box-shadow:inset 0 2px 4px rgba(0,0,0,.15),inset 0 -2px 4px rgba(0,0,0,.15);overflow:hidden;background-attachment:fixed;background-size:1920px 80%;background-position:50% -90%;}#invoice{position:relative;top:-290px;margin:0 auto;width:100%;background:#FFF;}[id*="invoice-"]{border-bottom:1px solid #EEE;padding:30px;}#invoice-top{min-height:120px;}#invoice-mid{min-height:120px;}#invoice-bot{min-height:250px;}.logo1{float:left;height:60px;width:60px;background:url("http://michaeltruong.ca/images/logo1.png") no-repeat;background-size:60px 60px;}.clientlogo{float:left;height:60px;width:60px;background:url("http://michaeltruong.ca/images/client.jpg") no-repeat;background-size:60px 60px;border-radius:50px;}.info{display:block;float:left;margin-left:20px;}.title{float:right;}.title p{text-align:right;}#project{margin-left:52%;}table{width:100%;border-collapse:collapse;}td{padding:5px 0 5px 15px;border:1px solid #EEE;}.tabletitle{padding:5px;background:#EEE;}.service{border:1px solid #EEE;}.item{width:50%;}.itemtext{font-size:.9em;}#legalcopy{margin-top:30px;}form{float:right;margin-top:30px;text-align:right;}.effect2{position:relative;}.effect2:before,.effect2:after{z-index:-1;position:absolute;content:"";bottom:15px;left:10px;width:50%;top:80%;max-width:300px;background:#777;box-shadow:0 15px 10px #777;transform:rotate(-3deg);}.effect2:after{transform:rotate(3deg);right:10px;left:auto;}.legal{width:100%;@media print{#headerimage{box-shadow:none;}*{box-shadow:none;}body{background:#E0E0E0;}}</style>');
            doc.document.write('</head><body>');
            doc.document.write(invoiceContent);
            doc.document.write('</body></html>');
            doc.document.close();

            // Wait until the content is loaded, then print
            printFrame.onload = function() {
                printFrame.contentWindow.print();
                document.body.removeChild(printFrame); // Remove the iframe after printing
            };
        });
        $(document).ready(function() {
            $('#expireInvoiceBtn').on('click', function() {
                var invoiceId = $(this).data('invoice-id');

                $.ajax({
                    url: '/invoice/' + invoiceId + '/expire',
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}' // Include CSRF token for security
                    },
                    success: function(response) {
                        alert(response.message); // Show success message
                        location.reload(); // Refresh the page
                    },
                    error: function(xhr) {
                        alert('Failed to expire the invoice: ' + xhr.responseJSON.message);
                    }
                });
            });
        });

    </script>

    <style>
        @import url(https://fonts.googleapis.com/css?family=Roboto:100,300,400,900,700,500,300,100);

        * {
            margin: 0;
            box-sizing: border-box;

        }

        body {
            background: #E0E0E0;
            font-family: 'Roboto', sans-serif;
            background-image: url('');
            background-repeat: repeat-y;
            background-size: 100%;
        }

        ::selection {
            background: #f31544;
            color: #FFF;
        }

        ::moz-selection {
            background: #f31544;
            color: #FFF;
        }

        h1 {
            font-size: 1.5em;
            color: #222;
        }

        h2 {
            font-size: .9em;
        }

        h3 {
            font-size: 1.2em;
            font-weight: 300;
            line-height: 2em;
        }

        p {
            font-size: .7em;
            color: #666;
            line-height: 1.2em;
        }

        #invoiceholder {
            width: 100%;
            hieght: 100%;
        }

        #headerimage {
            z-index: -1;
            position: relative;
            top: -50px;
            height: 350px;
            background-image: url('http://michaeltruong.ca/images/invoicebg.jpg');

            -webkit-box-shadow: inset 0 2px 4px rgba(0, 0, 0, .15), inset 0 -2px 4px rgba(0, 0, 0, .15);
            -moz-box-shadow: inset 0 2px 4px rgba(0, 0, 0, .15), inset 0 -2px 4px rgba(0, 0, 0, .15);
            box-shadow: inset 0 2px 4px rgba(0, 0, 0, .15), inset 0 -2px 4px rgba(0, 0, 0, .15);
            overflow: hidden;
            background-attachment: fixed;
            background-size: 1920px 80%;
            background-position: 50% -90%;
        }

        #invoice {
            position: relative;
            top: -290px;
            margin: 0 auto;
            width: 100%;
            background: #FFF;
        }

        [id*='invoice-'] {
            /* Targets all id with 'col-' */
            border-bottom: 1px solid #EEE;
            padding: 30px;
        }

        #invoice-top {
            min-height: 120px;
        }

        #invoice-mid {
            min-height: 120px;
        }

        #invoice-bot {
            min-height: 250px;
        }

        .logo1 {
            float: left;
            height: 60px;
            width: 60px;
            background: url(http://michaeltruong.ca/images/logo1.png) no-repeat;
            background-size: 60px 60px;
        }

        .clientlogo {
            float: left;
            height: 60px;
            width: 60px;
            background: url(http://michaeltruong.ca/images/client.jpg) no-repeat;
            background-size: 60px 60px;
            border-radius: 50px;
        }

        .info {
            display: block;
            float: left;
            margin-left: 20px;
        }

        .title {
            float: right;
        }

        .title p {
            text-align: right;
        }

        #project {
            margin-left: 52%;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        td {
            padding: 5px 0 5px 15px;
            border: 1px solid #EEE
        }

        .tabletitle {
            padding: 5px;
            background: #EEE;
        }

        .service {
            border: 1px solid #EEE;
        }

        .item {
            width: 50%;
        }

        .itemtext {
            font-size: .9em;
        }

        #legalcopy {
            margin-top: 30px;
        }

        form {
            float: right;
            margin-top: 30px;
            text-align: right;
        }


        .effect2 {
            position: relative;
        }

        .effect2:before,
        .effect2:after {
            z-index: -1;
            position: absolute;
            content: "";
            bottom: 15px;
            left: 10px;
            width: 50%;
            top: 80%;
            max-width: 300px;
            background: #777;
            -webkit-box-shadow: 0 15px 10px #777;
            -moz-box-shadow: 0 15px 10px #777;
            box-shadow: 0 15px 10px #777;
            -webkit-transform: rotate(-3deg);
            -moz-transform: rotate(-3deg);
            -o-transform: rotate(-3deg);
            -ms-transform: rotate(-3deg);
            transform: rotate(-3deg);
        }

        .effect2:after {
            -webkit-transform: rotate(3deg);
            -moz-transform: rotate(3deg);
            -o-transform: rotate(3deg);
            -ms-transform: rotate(3deg);
            transform: rotate(3deg);
            right: 10px;
            left: auto;
        }



        .legal {
            width: 100%;
        }
    </style>
@endsection
