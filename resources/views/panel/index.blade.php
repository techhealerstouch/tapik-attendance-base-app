@extends('layouts.sidebar')

@section('content')
<div class="container-fluid content-inner mt-n5 py-0">
    <div class="row">
        <div class="col-lg-12">
            <div class="card rounded">
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <h3 class="mb-4"><i class="bi bi-menu-up"></i> {{ __('messages.Dashboard') }}</h3>
                            <section class="mb-3 text-center p-4 w-full">
                                <div class="d-flex">
                                    <div class="p-2 h6">
                                        <i class="bi bi-link"></i> {{ __('messages.Total Links:') }} 
                                        <span class="text-primary">{{ $links }}</span>
                                    </div>
                                    <div class="p-2 h6">
                                        <i class="bi bi-eye"></i> {{ __('messages.Link Clicks:') }} 
                                        <span class="text-primary">{{ $clicks }}</span>
                                    </div>
                                </div>
                                <div class="text-center w-100">
                                    <a href="{{ url('/studio/links') }}">{{ __('messages.View/Edit Links') }}</a>
                                </div>
                                <div class="w-100 text-left">
                                    <h6><i class="bi bi-sort-up"></i> {{ __('messages.Top Links:') }}</h6>
                                    @php $i = 0; @endphp
                                    <div class="bd-example">
                                        <ol class="list-group list-group-numbered text-left">
                                            @if ($toplinks == "[]")
                                                <div class="container">
                                                    <div class="row justify-content-center mt-3">
                                                        <div class="col-6 text-center">
                                                            <p class="p-2">{{ __('messages.You haven’t added any links yet') }}</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            @else
                                                @foreach ($toplinks as $link)
                                                    @php
                                                        $linkName = str_replace('default ', '', $link->name);
                                                        $i++;
                                                    @endphp
                                                    @if ($link->name !== "phone" && $link->name !== 'heading' && $link->button_id !== 96)
                                                        <li class="list-group-item d-flex justify-content-between align-items-start">
                                                            <div class="ms-2 me-auto text-truncate">
                                                                <div class="fw-bold text-truncate">{{ $link->title }}</div>
                                                                {{ $link->link }}
                                                            </div>
                                                            <span class="badge bg-primary rounded-pill p-2">
                                                                {{ $link->click_number }} - {{ __('messages.clicks') }}
                                                            </span>
                                                        </li>
                                                    @endif
                                                @endforeach
                                            @endif
                                        </ol>
                                    </div>
                                </div>
                            </section>
                        </div>
                    </div>
                </div>
            </div>
        </div>

         <!-- Check if $user->new_counter == 1 -->
         @if (auth()->user()->new_counter == 1)
         <script>
             // Wait for the DOM to be fully loaded
             document.addEventListener("DOMContentLoaded", function() {
                 // Open the modal if condition is true
                 var myModal = new bootstrap.Modal(document.getElementById('exampleModalToggle'));
                 myModal.show();
             });
         </script>
     @endif

     <!-- CenterModal HTML -->
     <div class="modal fade" id="exampleModalToggle" aria-hidden="true" aria-labelledby="exampleModalToggleLabel" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content text-center">
                <!-- Center the image -->
                <img src="{{asset('assets/images/dashboard/unlock.png')}}" class="mx-auto mt-4" height="100px" width="100px" alt="Unlock Icon">
                
                <!-- Add the text -->
                <div class="modal-body">
                    <p>You are currently using the system-generated password. If you wish to change it, please click the Change Password button below.</p>
                </div>
    
                <!-- Change Password button -->
                <a href="/studio/profile" class="btn btn-primary mx-auto mb-4" >Change Password</a>
            </div>
        </div>
    </div>
    

        <div class="col-lg-12">
            <div class="card rounded">
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-12">
                            @if (auth()->user()->role == 'admin' && !config('linkstack.single_user_mode'))
                                <section class="mb-3 text-gray-800 text-center p-4 w-full">
                                    <div class="font-weight-bold text-left h3">{{ __('messages.Site statistics:') }}</div>
                                    <br>
                                    <div class="d-flex flex-wrap justify-content-around">
                                        <div class="p-2">
                                            <h3 class="text-primary">
                                                <strong><i class="bi bi-share-fill"> {{ $siteLinks }} </i></strong>
                                            </h3>
                                            <span class="text-muted">{{ __('messages.Total links') }}</span>
                                        </div>
                                        <div class="p-2">
                                            <h3 class="text-primary">
                                                <strong><i class="bi bi-eye-fill"> {{ $siteClicks }} </i></strong>
                                            </h3>
                                            <span class="text-muted">{{ __('messages.Total clicks') }}</span>
                                        </div>
                                        <div class="p-2">
                                            <h3 class="text-primary">
                                                <strong><i class="bi bi-person-fill"> {{ $userNumber }} </i></strong>
                                            </h3>
                                            <span class="text-muted">{{ __('messages.Total users') }}</span>
                                        </div>
                                    </div>
                                </section>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-12">
            <div class="card rounded">
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <section class="mb-3 text-gray-800 text-center p-4 w-full">
                                <div class="font-weight-bold text-left h3">{{ __('messages.Registrations:') }}</div>
                                <br>
                                <div class="d-flex flex-wrap justify-content-around">
                                    <div class="p-2">
                                        <h3 class="text-primary">
                                            <strong>{{ $lastMonthCount }}</strong>
                                        </h3>
                                        <span class="text-muted">{{ __('messages.Last 30 days') }}</span>
                                    </div>
                                    <div class="p-2">
                                        <h3 class="text-primary">
                                            <strong>{{ $lastWeekCount }}</strong>
                                        </h3>
                                        <span class="text-muted">{{ __('messages.Last 7 days') }}</span>
                                    </div>
                                    <div class="p-2">
                                        <h3 class="text-primary">
                                            <strong>{{ $last24HrsCount }}</strong>
                                        </h3>
                                        <span class="text-muted">{{ __('messages.Last 24 hours') }}</span>
                                    </div>
                                </div>
                            </section>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-12">
            <div class="card rounded">
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <section class="mb-3 text-gray-800 text-center p-4 w-full">
                                <div class="font-weight-bold text-left h3">{{ __('messages.Active users:') }}</div>
                                <br>
                                <div class="d-flex flex-wrap justify-content-around">
                                    <div class="p-2">
                                        <h3 class="text-primary">
                                            <strong>{{ $updatedLast30DaysCount }}</strong>
                                        </h3>
                                        <span class="text-muted">{{ __('messages.Last 30 days') }}</span>
                                    </div>
                                    <div class="p-2">
                                        <h3 class="text-primary">
                                            <strong>{{ $updatedLast7DaysCount }}</strong>
                                        </h3>
                                        <span class="text-muted">{{ __('messages.Last 7 days') }}</span>
                                    </div>
                                    <div class="p-2">
                                        <h3 class="text-primary">
                                            <strong>{{ $updatedLast24HrsCount }}</strong>
                                        </h3>
                                        <span class="text-muted">{{ __('messages.Last 24 hours') }}</span>
                                    </div>
                                </div>
                            </section>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
