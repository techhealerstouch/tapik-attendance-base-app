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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous">
    </script>
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
    <style>
        /* body {
        font-family: Arial, sans-serif;
        background-color: #f0f0f0;
        margin: 0;
        padding: 20px;
        } */

        .container {
        max-width: 600px;
        margin: 0 auto;
        }

        h1 {
        text-align: center;
        color: #333;
        }

        .event-card {
        background-color: white;
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 10px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
        }

        .event-card:first-child {
        border-left: 4px solid #4caf50;
        }

        .event-card h2 {
        margin: 0 0 5px 0;
        color: #333;
        }

        .event-card p {
        margin: 0;
        color: #666;
        }

        .event-card .event {
        color: #1e88e5;
        font-weight: bold;
        }


        .event-card2 {
        background-color: white;
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 10px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
        }

        .event-card2:first-child {
        border-left: 4px solid #4caf50;
        }

        .event-card2 h2 {
        margin: 0 0 5px 0;
        color: #333;
        }

        .event-card2 p {
        margin: 0;
        color: #666;
        }

        .event-card2 .event {
        color: #1e88e5;
        font-weight: bold;
        }


    </style>
    <div class="container-fluid content-inner mt-n5 py-0">

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
                                            <li class="breadcrumb-item"><a href="/attendance">Attendance</a></li>
                                            <li class="breadcrumb-item">Live Preview</li>
                                        </ol>
                                    </div>
                                    <h1>Live Preview</h1>
                                    <div style="max-width: 600px !important; display: flex; justify-content: center; margin: 0 auto;">
                                        <select style="width: 100% !important" id="event-filter" class="form-control select2">
                                            <option value="all">All Events</option>
                                            @foreach($events as $event)
                                                <option value="{{ $event->id }}">{{ $event->title }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <h3 class="mt-5 text-center">Members</h3>
                                            <hr>
                                            <div class="mt-3" id="event-stack"></div>
                                        </div>
                                        <div class="col-md-6">
                                            <h3 class="mt-5 text-center">Guests</h3>
                                            <hr>
                                            <div class="mt-3" id="event-stack-2"></div>
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

    <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/2.0.8/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/2.0.8/js/dataTables.bootstrap5.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.0/xlsx.full.min.js"></script>

    <script>
  $(document).ready(function() {
    $('.select2').select2();

    let selectedEventId = 'all'; // Default eventId to "all"

    // Update the fetch events function to use the correct URL
    function fetchEvents(eventId) {
        $.ajax({
            url: '/attendance/live-attendance-user/' + eventId, // Correct the URL format
            method: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.attendances) {
                    updateEventStack(response.attendances);
                }
                if (response.ticketGuests) {
                    updateEventStack2(response.ticketGuests);
                }
            },
            error: function(xhr, status, error) {
                console.error('Error fetching events:', error);
            }
        });
    }

    function formatTime(dateString) {
        const date = new Date(dateString);
        return date.toLocaleTimeString([], { hour: "2-digit", minute: "2-digit", second: "2-digit" });
    }

    function createEventCard(attendance) {
        return `
            <div class="event-card">
                <h2>${attendance.user?.name}</h2>
                <p>${formatTime(attendance.time_in)}</p>
                <p class="event">${attendance.event.title}</p>
            </div>
        `;
    }

    function updateEventStack(attendances) {
    if (attendances.length === 0) {
        $('#event-stack').html('<p style="color: gray; font-style: italic;">No members attendance data found.</p>');
    } else {
        $('#event-stack').html(attendances.map(createEventCard).join(''));
    }
}

    function createEventCard2(ticketGuest) {
        return `
            <div class="event-card2">
                <h2>${ticketGuest.ticket_no}</h2>
                <p>${formatTime(ticketGuest.updated_at)}</p>
                <p class="event">${ticketGuest.ticket && ticketGuest.ticket.event ? ticketGuest.ticket.event.title : 'No event'}</p>
            </div>
        `;
    }

    function updateEventStack2(ticketGuests) {
    if (ticketGuests.length === 0) {
        $('#event-stack-2').html('<p style="color: gray; font-style: italic;">No guest attendance data found.</p>');
    } else {
        $('#event-stack-2').html(ticketGuests.map(createEventCard2).join(''));
    }
}

    // Fetch events initially with the default "all"
    fetchEvents(selectedEventId);

    // Fetch events when dropdown selection changes
    $('#event-filter').on('change', function() {
        selectedEventId = $(this).val();
        fetchEvents(selectedEventId);
    });

    // Fetch events every 5 seconds using the selected eventId
    setInterval(() => {
        fetchEvents(selectedEventId);
    }, 5000);
});

</script>
@endsection
