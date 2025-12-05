<!DOCTYPE html>
<html lang="en">
<head>
    @extends('layouts.lang')

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300&display=swap" rel="stylesheet">

    <style>
    [x-cloak] { display: none!important; }
    body, html {
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
        .header-main{
            margin-top: 50px;
        }
        h2, h3, h4 {
            font-family: 'Poppins', sans-serif;
            font-weight: 500;
            color: #db5363 !important;
            
        }

    </style>
</head>
<body>
    <div class="container-fluid content-inner">
        <div class="row">
            <div class="col-lg-12">
                <div class="row">
                    <div class="col-sm-12">
                        <a href="{{ config('app.url') }}" target="_blank" rel="noopener noreferrer" style="display: flex; justify-content: center; align-items: center;">
                            <img src="{{ asset('assets/linkstack/images/'.findFile('avatar')) }}" alt="Logo" style="max-width: 400px; max-height: 400px;" />
                        </a>
                        <section class="text-gray-400 ">
                            <div i class="attendance-center">
                                <h2 class="header-main">
                                    <i class="fas fa-times mx-2" style="color: red; font-size: 140px;"></i> <!-- Red color, custom size -->
                                </h2>

                                <h2 class="mb-4">
                                    @if (session('error'))
                                        {{ session('error') }}
                                    @else
                                        There seems to be a problem with your ID. Please approach an admin staff.
                                    @endif
                                </h2>
                                <!-- this is the scanner component -->
                            </section>
                            <form id="attendanceForm" action="/attendance-input" method="POST">
                                @csrf
                                <input type="hidden" name="event" value="{{ session('event') }}">
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script defer src="{{ url('assets/js/cdn.min.js') }}"></script>
    <script src="{{ url('vendor/livewire/livewire/dist/livewire.js') }}"></script>
    <script src="{{ url('assets/js/livewire-sortable.js') }}"></script>
    <script>
        window.onload = function() {
            window.setTimeout(function() {
                document.getElementById('attendanceForm').submit();
            }, 3000); // 3000 milliseconds = 3 seconds
        };
    </script>
</body>
</html>
<style>
    .attendance-center {
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
    }

    .body {
        color: black !important;
    }

    .header-main{
        margin-top: 50px;
    }
    
</style>


