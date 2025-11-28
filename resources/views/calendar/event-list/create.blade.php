@extends('layouts.sidebar')

@section('content')
    <script src="{{ asset('resources/ckeditor/ckeditor.js') }}"></script>
    <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/js/all.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.8/css/dataTables.bootstrap5.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.0/xlsx.full.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js" defer></script>
    
    <svg xmlns="http://www.w3.org/2000/svg" style="display: none;">
        <symbol id="check-circle-fill" fill="currentColor" viewBox="0 0 16 16">
            <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z" />
        </symbol>
        <symbol id="info-fill" fill="currentColor" viewBox="0 0 16 16">
            <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z" />
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
                    <div class="card-header">
                        <h4 class="card-title">Create Event</h4>
                        <p class="text-muted mb-0">Set up a new event with participants and food services</p>
                    </div>
                    <div class="card-body">
                        <div class="col-sm-6">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="/dashboard">Home</a></li>
                                <li class="breadcrumb-item">Event List</li>
                                <li class="breadcrumb-item active">Create Event</li>
                            </ol>
                        </div>

                        <!-- Info Alert -->
                        <div class="alert alert-info d-flex align-items-center" role="alert">
                            <i class="bi bi-info-circle me-2"></i>
                            <div>
                                <strong>Tip:</strong> Select food services that will be available during this event. Participants can claim these services using the claiming interface.
                            </div>
                        </div>

                        <form method="POST" action="{{ route('events.store') }}">
                            @csrf
                            
                            <!-- Event Basic Information -->
                            <h5 class="mb-3"><i class="bi bi-calendar-event"></i> Event Information</h5>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="title" class="form-label">
                                        Event Name *
                                        <i class="bi bi-question-circle text-muted" 
                                           data-bs-toggle="tooltip" 
                                           title="Enter a clear, descriptive name for your event"></i>
                                    </label>
                                    <input type="text" 
                                           name="title" 
                                           id="title" 
                                           class="form-control" 
                                           placeholder="e.g., Annual Conference 2024"
                                           required>
                                    <small class="form-text text-muted">This will be displayed to all participants</small>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="address" class="form-label">
                                        Venue/Address *
                                        <i class="bi bi-question-circle text-muted" 
                                           data-bs-toggle="tooltip" 
                                           title="Physical location where the event will take place"></i>
                                    </label>
                                    <input type="text" 
                                           name="address" 
                                           id="address" 
                                           class="form-control" 
                                           placeholder="e.g., Grand Ballroom, Hotel XYZ"
                                           required>
                                    <small class="form-text text-muted">Include room/hall name if applicable</small>
                                </div>
                            </div>
                        
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="start" class="form-label">
                                        Start Date and Time *
                                        <i class="bi bi-question-circle text-muted" 
                                           data-bs-toggle="tooltip" 
                                           title="When does the event begin?"></i>
                                    </label>
                                    <input type="datetime-local" 
                                           name="start" 
                                           id="start" 
                                           class="form-control" 
                                           required>
                                    <small class="form-text text-muted">Event start time</small>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="end" class="form-label">
                                        End Date and Time *
                                        <i class="bi bi-question-circle text-muted" 
                                           data-bs-toggle="tooltip" 
                                           title="When does the event end?"></i>
                                    </label>
                                    <input type="datetime-local" 
                                           name="end" 
                                           id="end" 
                                           class="form-control" 
                                           required>
                                    <small class="form-text text-muted">Event end time</small>
                                </div>
                            </div>
                        
                            <div class="mb-4">
                                <label for="description" class="form-label">
                                    Description
                                    <i class="bi bi-question-circle text-muted" 
                                       data-bs-toggle="tooltip" 
                                       title="Additional details about the event"></i>
                                </label>
                                <textarea name="description" 
                                          id="description" 
                                          class="form-control" 
                                          rows="3"
                                          placeholder="Provide event details, agenda, dress code, or any important information..."></textarea>
                                <small class="form-text text-muted">Optional event description for participants</small>
                            </div>

                            <hr class="my-4">

                            <!-- Participants Section -->
                            <h5 class="mb-3"><i class="bi bi-people"></i> Participants</h5>
                            <div class="row">
                                <div class="form-group col-md-12 mb-3">
                                    <label for="group_id" class="control-label">
                                        Group
                                        <i class="bi bi-question-circle text-muted" 
                                           data-bs-toggle="tooltip" 
                                           title="Select a group to automatically invite all group members"></i>
                                    </label>
                                    <select class="form-control select-group" 
                                            name="group_id" 
                                            style="width: 100% !important">
                                        <option value="">Select Group (Optional)</option>
                                        @foreach ($groups as $group)
                                            <option value="{{ $group->id }}">{{ $group->name }}</option>
                                        @endforeach
                                    </select>
                                    <small class="form-text text-muted">All members of the selected group will be added to the event</small>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-12 mb-4">
                                    <label for="userId" class="control-label">
                                        Additional Members/Users
                                        <i class="bi bi-question-circle text-muted" 
                                           data-bs-toggle="tooltip" 
                                           title="Manually select specific users to invite"></i>
                                    </label>
                                    <select class="form-control select-members" 
                                            name="userId[]" 
                                            multiple="multiple" 
                                            style="width: 100% !important">
                                        <option value="select-all">Select All</option>
                                        @foreach ($users as $user)
                                            <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                                        @endforeach
                                    </select>
                                    <small class="form-text text-muted">Add individual users in addition to group members</small>
                                </div>
                            </div>

                            <hr class="my-4">

                            <!-- Food Services Section -->
                            <h5 class="mb-3"><i class="bi bi-egg-fried"></i> Food Services</h5>
                            <div class="row">
                                <div class="col-md-12 mb-4">
                                    <label for="food_services" class="control-label">
                                        Available Food Services
                                        <i class="bi bi-question-circle text-muted" 
                                           data-bs-toggle="tooltip" 
                                           title="Select which food services will be available during this event"></i>
                                    </label>
                                    <select class="form-control select-food-services" 
                                            name="food_service_ids[]" 
                                            multiple="multiple" 
                                            style="width: 100% !important">
                                        @foreach ($foodServices as $service)
                                            <option value="{{ $service->id }}">
                                                {{ $service->name }} 
                                                @if($service->start_time && $service->end_time)
                                                    ({{ $service->start_time->format('H:i') }} - {{ $service->end_time->format('H:i') }})
                                                @endif
                                            </option>
                                        @endforeach
                                    </select>
                                    <small class="form-text text-muted">Participants will be able to claim these food services during the event</small>
                                </div>
                            </div>

                            <!-- Food Service Quantities -->
                            <div id="foodServiceQuantities" style="display: none;">
                                <div class="card bg-light mb-4">
                                    <div class="card-body">
                                        <h6 class="card-title">Set Quantities for Selected Services</h6>
                                        <p class="text-muted small">Leave blank for unlimited quantity</p>
                                        <div id="quantityInputs"></div>
                                    </div>
                                </div>
                            </div>

                            <hr class="my-4">

                            <!-- Notification Settings -->
                            <h5 class="mb-3"><i class="bi bi-envelope"></i> Notifications</h5>
                            <div class="mb-4">
                                <div class="form-check form-switch">
                                    <input type="hidden" name="send_mail" value="0"> 
                                    <input class="form-check-input" 
                                           type="checkbox" 
                                           role="switch"
                                           id="send_mail" 
                                           name="send_mail" 
                                           value="1">
                                    <label class="form-check-label" for="send_mail">
                                        <strong>Send Email Invitations</strong>
                                        <i class="bi bi-question-circle text-muted" 
                                           data-bs-toggle="tooltip" 
                                           title="Send email notifications to all participants with event details"></i>
                                    </label>
                                    <br>
                                    <small class="form-text text-muted">Participants will receive an email with event details and invitation</small>
                                </div>
                            </div>
                                                                    
                            <hr>

                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="bi bi-save"></i> Create Event
                                </button>
                                <a href="{{ route('events.index_list') }}" class="btn btn-secondary btn-lg">
                                    <i class="bi bi-x-circle"></i> Cancel
                                </a>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Help Card -->
                <div class="card rounded mt-3">
                    <div class="card-header bg-light">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-lightbulb text-warning"></i> Quick Guide
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <h6 class="text-primary"><i class="bi bi-1-circle"></i> Event Details</h6>
                                <p class="small">Enter basic event information including name, venue, and schedule.</p>
                            </div>
                            <div class="col-md-4">
                                <h6 class="text-primary"><i class="bi bi-2-circle"></i> Add Participants</h6>
                                <p class="small">Select a group or individually choose participants to invite.</p>
                            </div>
                            <div class="col-md-4">
                                <h6 class="text-primary"><i class="bi bi-3-circle"></i> Food Services</h6>
                                <p class="small">Select available meals and set quantities if needed.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function() {
            // Initialize tooltips
            $('[data-bs-toggle="tooltip"]').tooltip();

            // Initialize Select2 for members
            $('.select-members').select2({
                placeholder: "Select members/users to invite",
                allowClear: true
            });

            // Initialize Select2 for groups
            $('.select-group').select2({
                placeholder: "Select Group (Optional)",
                allowClear: true
            });

            // Initialize Select2 for food services
            $('.select-food-services').select2({
                placeholder: "Select food services for this event",
                allowClear: true
            });

            // "Select All" functionality for members
            $('.select-members').on('select2:select', function(e) {
                if (e.params.data.id === 'select-all') {
                    $(this).val($(this).find('option:not([value="select-all"])').map(function() {
                        return $(this).val();
                    }).get()).trigger('change');
                }
            });

            $('.select-members').on('select2:unselect', function(e) {
                if (e.params.data.id === 'select-all') {
                    $(this).val(null).trigger('change');
                }
            });

            // Food services selection handler
            $('.select-food-services').on('change', function() {
                const selectedServices = $(this).val();
                const $quantitiesSection = $('#foodServiceQuantities');
                const $quantityInputs = $('#quantityInputs');

                if (selectedServices && selectedServices.length > 0) {
                    $quantitiesSection.show();
                    $quantityInputs.empty();

                    selectedServices.forEach(serviceId => {
                        const serviceName = $(this).find(`option[value="${serviceId}"]`).text();
                        const inputHtml = `
                            <div class="row mb-2">
                                <div class="col-md-6">
                                    <label class="form-label small">${serviceName}</label>
                                </div>
                                <div class="col-md-6">
                                    <input type="number" 
                                           name="food_service_quantities[${serviceId}]" 
                                           class="form-control form-control-sm" 
                                           placeholder="Unlimited"
                                           min="0">
                                </div>
                            </div>
                        `;
                        $quantityInputs.append(inputHtml);
                    });
                } else {
                    $quantitiesSection.hide();
                    $quantityInputs.empty();
                }
            });

            // Date validation
            $('#start').on('change', function() {
                const startDate = new Date($(this).val());
                $('#end').attr('min', $(this).val());
            });

            $('#end').on('change', function() {
                const startDate = new Date($('#start').val());
                const endDate = new Date($(this).val());
                
                if (endDate < startDate) {
                    alert('End date cannot be before start date');
                    $(this).val('');
                }
            });
        });
    </script>
@endsection