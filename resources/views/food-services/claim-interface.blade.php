<!-- resources/views/food-services/claim-interface.blade.php -->
@extends('layouts.sidebar')

@section('content')
    <div class="container-fluid content-inner mt-n5 py-0">
        <div class="row">
            <div class="col-lg-12">
                <div class="card rounded">
                    <div class="card-header">
                        <h4 class="card-title">Food Service Claiming</h4>
                        <p class="text-muted mb-0"><small><i class="bi bi-info-circle"></i> Select an event to start the claiming process</small></p>
                    </div>
                    <div class="card-body">
                        <!-- Event Selection -->
                        <div class="row justify-content-center">
                            <div class="col-lg-8 col-md-10">
                                <div class="mb-4">
                                    <label for="eventSelect" class="form-label fs-5">Select Event</label>
                                    <select id="eventSelect" class="form-select form-select-lg">
                                        <option value="">-- Select Event --</option>
                                        @foreach($events as $event)
                                            <option value="{{ $event->id }}">
                                                {{ $event->title }} ({{ $event->start->format('M d, Y') }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Claiming Interface Button -->
                                <div class="text-center">
                                    <button class="btn btn-primary btn-lg px-5" id="openClaimingBtn" disabled>
                                        <i class="bi bi-box-arrow-up-right"></i> Open Claiming Interface
                                    </button>
                                    <p class="text-muted mt-2 small">
                                        <i class="bi bi-info-circle"></i> This will open the claiming interface in a new tab
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Optional: Event Details Preview -->
                        <div id="eventPreview" class="row justify-content-center mt-4" style="display: none;">
                            <div class="col-lg-8 col-md-10">
                                <div class="card border-primary">
                                    <div class="card-body">
                                        <h6 class="card-title text-primary">
                                            <i class="bi bi-calendar-event"></i> Selected Event
                                        </h6>
                                        <div id="eventDetails"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('sidebar-scripts')
     <!-- jQuery (Load this FIRST before any other scripts) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
    
        <!-- Select2 CSS -->
        <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
        <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
        
        <!-- Select2 JS -->
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
        
        <script>
            $(document).ready(function() {
                // Initialize Select2 for event dropdown
                $('#eventSelect').select2({
                    theme: 'bootstrap-5',
                    placeholder: '-- Select Event --',
                    allowClear: true,
                    width: '100%'
                });

                // Enable/disable button and show preview when event is selected
                $('#eventSelect').change(function() {
                    const eventId = $(this).val();
                    const eventText = $(this).find('option:selected').text();
                    
                    if (eventId) {
                        $('#openClaimingBtn').prop('disabled', false);
                        $('#eventDetails').html(`
                            <p class="mb-0"><strong>${eventText}</strong></p>
                        `);
                        $('#eventPreview').show();
                    } else {
                        $('#openClaimingBtn').prop('disabled', true);
                        $('#eventPreview').hide();
                    }
                });

                // Open claiming interface in new tab
                $('#openClaimingBtn').click(function() {
                    const eventId = $('#eventSelect').val();
                    if (eventId) {
                        const url = '{{ route("food-service.claiming-page") }}?event_id=' + eventId;
                        window.open(url, '_blank');
                    }
                });
            });
        </script>
    @endpush
@endsection