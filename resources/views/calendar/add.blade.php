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
    
    <style>
        :root {
            --primary-color: #4f46e5;
            --primary-hover: #4338ca;
            --secondary-color: #6366f1;
            --success-color: #10b981;
            --info-color: #3b82f6;
            --warning-color: #f59e0b;
            --danger-color: #ef4444;
            --light-bg: #f8fafc;
            --card-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
            --card-shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }

        .professional-card {
            border: none;
            border-radius: 12px;
            box-shadow: var(--card-shadow);
            transition: all 0.3s ease;
            background: white;
        }

        .professional-card:hover {
            box-shadow: var(--card-shadow-lg);
        }

        .card-header-professional {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
            border-radius: 12px 12px 0 0 !important;
            padding: 1.5rem;
            border: none;
        }

        .card-header-professional h4 {
            margin: 0;
            font-weight: 600;
            font-size: 1.5rem;
        }

        .card-header-professional p {
            margin: 0.5rem 0 0 0;
            opacity: 0.9;
            font-size: 0.95rem;
        }

        .form-label {
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 0.5rem;
            font-size: 0.95rem;
        }

        .form-control, .form-select {
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            padding: 0.625rem 0.875rem;
            transition: all 0.2s ease;
            font-size: 0.95rem;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
            outline: none;
        }

        .section-header {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 1.5rem;
            padding-bottom: 0.75rem;
            border-bottom: 2px solid #e2e8f0;
        }

        .section-header h5 {
            margin: 0;
            font-weight: 700;
            color: #1e293b;
            font-size: 1.25rem;
        }

        .section-header i {
            font-size: 1.5rem;
            color: var(--primary-color);
        }

        .alert-professional {
            border: none;
            border-radius: 10px;
            padding: 1rem 1.25rem;
            border-left: 4px solid;
            box-shadow: var(--card-shadow);
        }

        .alert-info.alert-professional {
            background: #dbeafe;
            border-left-color: var(--info-color);
            color: #1e40af;
        }

        .alert-success.alert-professional {
            background: #d1fae5;
            border-left-color: var(--success-color);
            color: #065f46;
        }

        .alert-danger.alert-professional {
            background: #fee2e2;
            border-left-color: var(--danger-color);
            color: #991b1b;
        }

        .btn-professional {
            padding: 0.75rem 1.5rem;
            font-weight: 600;
            border-radius: 8px;
            transition: all 0.2s ease;
            border: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-primary.btn-professional {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
        }

        .btn-primary.btn-professional:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.4);
        }

        .btn-secondary.btn-professional {
            background: #64748b;
        }

        .btn-secondary.btn-professional:hover {
            background: #475569;
            transform: translateY(-2px);
        }

        .quantity-card {
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            border: 2px solid #cbd5e1;
            border-radius: 10px;
            padding: 1.5rem;
        }

        .quantity-card h6 {
            color: #1e293b;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .form-check-input:checked {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .form-check-label {
            font-weight: 600;
            color: #1e293b;
        }

        .breadcrumb {
            background: transparent;
            padding: 0;
            margin-bottom: 1.5rem;
        }

        .breadcrumb-item a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 500;
        }

        .breadcrumb-item.active {
            color: #64748b;
        }

        .help-card {
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
            border: none;
            border-radius: 12px;
            box-shadow: var(--card-shadow);
        }

        .help-card .card-header {
            background: transparent;
            border: none;
            padding: 1.25rem;
        }

        .help-card h6 {
            font-weight: 700;
            color: #1e293b;
        }

        .tooltip-icon {
            color: #94a3b8;
            cursor: help;
            transition: color 0.2s ease;
        }

        .tooltip-icon:hover {
            color: var(--primary-color);
        }

        .select2-container--default .select2-selection--multiple,
        .select2-container--default .select2-selection--single {
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            min-height: 42px;
        }

        .select2-container--default.select2-container--focus .select2-selection--multiple,
        .select2-container--default.select2-container--focus .select2-selection--single {
            border-color: var(--primary-color);
        }

        small.form-text {
            color: #64748b;
            font-size: 0.875rem;
            margin-top: 0.25rem;
            display: block;
        }

        hr.professional-divider {
            border: none;
            height: 2px;
            background: linear-gradient(to right, transparent, #e2e8f0, transparent);
            margin: 2rem 0;
        }

        .step-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            background: var(--primary-color);
            color: white;
            border-radius: 50%;
            font-weight: 700;
            margin-right: 0.5rem;
        }
    </style>
    
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
            <div class="alert alert-success alert-professional d-flex align-items-center justify-content-between" role="alert">
                <div class="d-flex align-items-center">
                    <svg class="bi flex-shrink-0 me-2" width="24" height="24" role="img" aria-label="Success:">
                        <use xlink:href="#check-circle-fill" />
                    </svg>
                    <strong>{{ session('success') }}</strong>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        
        @if (session('error'))
            <div class="alert alert-danger alert-professional d-flex align-items-center justify-content-between" role="alert">
                <div class="d-flex align-items-center">
                    <svg class="bi flex-shrink-0 me-2" width="24" height="24" role="img" aria-label="Danger:">
                        <use xlink:href="#exclamation-triangle-fill" />
                    </svg>
                    <strong>{{ session('error') }}</strong>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="row">
            <div class="col-lg-12">
                <div class="card professional-card">
                    <div class="card-header card-header-professional">
                        <h4 class="card-title">Create New Event</h4>
                        <p class="mb-0">Set up a new event with participants and food services</p>
                    </div>
                    <div class="card-body p-4">
                        <div class="col-sm-6">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="/dashboard">Home</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('events.index_list') }}">Event List</a></li>
                                <li class="breadcrumb-item active">Create Event</li>
                            </ol>
                        </div>

                        <!-- Info Alert -->
                        <div class="alert alert-info alert-professional d-flex align-items-center" role="alert">
                            <i class="bi bi-info-circle me-3 fs-4"></i>
                            <div>
                                <strong>Tip:</strong> Select food services that will be available during this event. Participants can claim these services using the claiming interface.
                            </div>
                        </div>

                        <form method="POST" action="{{ route('events.store') }}">
                            @csrf
                            
                            <!-- Event Basic Information -->
                            <div class="section-header">
                                <i class="bi bi-calendar-event"></i>
                                <h5>Event Information</h5>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="title" class="form-label">
                                        Event Name *
                                        <i class="bi bi-question-circle tooltip-icon" 
                                           data-bs-toggle="tooltip" 
                                           title="Enter a clear, descriptive name for your event"></i>
                                    </label>
                                    <input type="text" 
                                           name="title" 
                                           id="title" 
                                           class="form-control" 
                                           placeholder="e.g., Annual Conference 2024"
                                           required>
                                    <small class="form-text">This will be displayed to all participants</small>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="address" class="form-label">
                                        Venue/Address *
                                        <i class="bi bi-question-circle tooltip-icon" 
                                           data-bs-toggle="tooltip" 
                                           title="Physical location where the event will take place"></i>
                                    </label>
                                    <input type="text" 
                                           name="address" 
                                           id="address" 
                                           class="form-control" 
                                           placeholder="e.g., Grand Ballroom, Hotel XYZ"
                                           required>
                                    <small class="form-text">Include room/hall name if applicable</small>
                                </div>
                            </div>
                        
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="start" class="form-label">
                                        Start Date and Time *
                                        <i class="bi bi-question-circle tooltip-icon" 
                                           data-bs-toggle="tooltip" 
                                           title="When does the event begin?"></i>
                                    </label>
                                    <input type="datetime-local" 
                                           name="start" 
                                           id="start" 
                                           class="form-control" 
                                           required>
                                    <small class="form-text">Event start time</small>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="end" class="form-label">
                                        End Date and Time *
                                        <i class="bi bi-question-circle tooltip-icon" 
                                           data-bs-toggle="tooltip" 
                                           title="When does the event end?"></i>
                                    </label>
                                    <input type="datetime-local" 
                                           name="end" 
                                           id="end" 
                                           class="form-control" 
                                           required>
                                    <small class="form-text">Event end time</small>
                                </div>
                            </div>
                        
                            <div class="mb-4">
                                <label for="description" class="form-label">
                                    Description
                                    <i class="bi bi-question-circle tooltip-icon" 
                                       data-bs-toggle="tooltip" 
                                       title="Additional details about the event"></i>
                                </label>
                                <textarea name="description" 
                                          id="description" 
                                          class="form-control" 
                                          rows="4"
                                          placeholder="Provide event details, agenda, dress code, or any important information..."></textarea>
                                <small class="form-text">Optional event description for participants</small>
                            </div>

                            <hr class="professional-divider">

                            <!-- Participants Section -->
                            <div class="section-header">
                                <i class="bi bi-people"></i>
                                <h5>Participants</h5>
                            </div>

                            <div class="row">
                                <div class="form-group col-md-12 mb-3">
                                    <label for="group_id" class="form-label">
                                        Group
                                        <i class="bi bi-question-circle tooltip-icon" 
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
                                    <small class="form-text">All members of the selected group will be added to the event</small>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-12 mb-4">
                                    <label for="userId" class="form-label">
                                        Additional Members/Users
                                        <i class="bi bi-question-circle tooltip-icon" 
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
                                    <small class="form-text">Add individual users in addition to group members</small>
                                </div>
                            </div>

                            <hr class="professional-divider">

                            <!-- Food Services Section -->
                            <div class="section-header">
                                <i class="bi bi-egg-fried"></i>
                                <h5>Food Services</h5>
                            </div>

                            <div class="row">
                                <div class="col-md-12 mb-4">
                                    <label for="food_services" class="form-label">
                                        Available Food Services
                                        <i class="bi bi-question-circle tooltip-icon" 
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
                                    <small class="form-text">Participants will be able to claim these food services during the event</small>
                                </div>
                            </div>

                            <!-- Food Service Quantities -->
                            <div id="foodServiceQuantities" style="display: none;">
                                <div class="quantity-card mb-4">
                                    <h6><i class="bi bi-calculator me-2"></i>Set Quantities for Selected Services</h6>
                                    <p class="text-muted small mb-3">Leave blank for unlimited quantity</p>
                                    <div id="quantityInputs"></div>
                                </div>
                            </div>

                            <hr class="professional-divider">

                            <!-- Notification Settings -->
                            <div class="section-header">
                                <i class="bi bi-envelope"></i>
                                <h5>Notifications</h5>
                            </div>

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
                                        Send Email Invitations
                                        <i class="bi bi-question-circle tooltip-icon" 
                                           data-bs-toggle="tooltip" 
                                           title="Send email notifications to all participants with event details"></i>
                                    </label>
                                    <br>
                                    <small class="form-text">Participants will receive an email with event details and invitation</small>
                                </div>
                            </div>
                                                                    
                            <hr class="professional-divider">

                            <div class="d-flex gap-3">
                                <button type="submit" class="btn btn-primary btn-professional">
                                    <i class="bi bi-save"></i> Create Event
                                </button>
                                <a href="{{ route('events.index_list') }}" class="btn btn-secondary btn-professional">
                                    <i class="bi bi-x-circle"></i> Cancel
                                </a>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Help Card -->
                <div class="card help-card mt-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="bi bi-lightbulb text-warning me-2"></i> Quick Guide
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4 mb-3 mb-md-0">
                                <h6><span class="step-badge">1</span>Event Details</h6>
                                <p class="small mb-0">Enter basic event information including name, venue, and schedule.</p>
                            </div>
                            <div class="col-md-4 mb-3 mb-md-0">
                                <h6><span class="step-badge">2</span>Add Participants</h6>
                                <p class="small mb-0">Select a group or individually choose participants to invite.</p>
                            </div>
                            <div class="col-md-4">
                                <h6><span class="step-badge">3</span>Food Services</h6>
                                <p class="small mb-0">Select available meals and set quantities if needed.</p>
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
                            <div class="row mb-3 align-items-center">
                                <div class="col-md-6">
                                    <label class="form-label mb-0">${serviceName}</label>
                                </div>
                                <div class="col-md-6">
                                    <input type="number" 
                                           name="food_service_quantities[${serviceId}]" 
                                           class="form-control" 
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