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
                                            <li class="breadcrumb-item">Guest Pass</li>
                                        </ol>
                                    </div>
                                    <!-- <div class="col-12 col-lg-12 text-end p-1">
                                        <div class="dropdown d-inline-block">
                                            <button class="btn button bg-transparent text-dark dropdown-toggle p-0"
                                                id="eventDropdownButton" type="button" data-bs-toggle="dropdown"
                                                aria-expanded="false">
                                                Select Event
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end"
                                                aria-labelledby="eventDropdownButton">
                                                @foreach ($events as $event)
                                                    <li><a class="dropdown-item" href="#"
                                                            onclick="selectEvent('{{ $event->id }}', '{{ $event->title }}')">{{ $event->title }}</a>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div> -->
                                    <div class="col-12 col-lg-12 text-center p-1">
                                        <h3 id="selectedEventHeading"></h3>
                                    </div>
                                    <div class="row">
                                        <div class="col-6 col-lg-6 p-3">
                                            <button class="btn button bg-success text-white" onclick="exportToExcel()">Export Excel</button>
                                            <button class="btn button bg-primary text-white" data-bs-toggle="modal" data-bs-target="#bulkExportModal">Bulk Export Pass</button>
                                        </div>
                                        <div class="col-6 col-lg-6 p-3 d-flex justify-content-end">
                                            <button class="btn button bg-primary text-white mx-2" data-bs-toggle="modal" data-bs-target="#createTicketModal">
                                                Create Pass
                                            </button>
                                            <button class="btn button bg-primary text-white" data-bs-toggle="modal" data-bs-target="#generatePassModal">
                                                Generate Pass
                                            </button>
                                        </div>
                                    </div>
                                    
                                    <div class="table-responsive">
                                        <table id="example" class="table table-striped" style="width:100%">
                                            <thead>
                                                <tr>
                                                    <th scope="col">Pass No.</th>
                                                    <th scope="col">Event</th>
                                                    <th scope="col">Ordered By</th>
                                                    <th scope="col">Status</th>
                                                    <th scope="col">Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <!-- This will be dynamically updated by JavaScript -->
                                            
                                                @foreach ($tickets as $ticket)
                                                    <tr>
                                                        <td>{{ $ticket->ticket_no }}</td>
                                            
                                                        <td>
                                                            {{ optional($ticket->ticket?->event)->title ?? 'No Event' }}
                                                        </td>
                                            
                                                        <td>
                                                            {{ $ticket->invoice->first_name ?? $ticket->first_name }} {{ $ticket->invoice->last_name ?? $ticket->last_name }}
                                                        </td>
                                            
                                                        <td>
                                                            {{ $ticket->is_scanned === 1 ? 'Scanned' : 'Not Scanned' }}
                                                        </td>
                                            
                                                        <td>
                                                        <button type="button" class="btn btn-primary btn-sm" onclick="exportPass('{{ $ticket->ticket_no }}')">
                                                            <i class="fa fa-download"></i>
                                                        </button>
                                                            
                                                            <button type="button" data-bs-toggle="modal" data-bs-target="#editTicketModal" 
                                                                class="btn btn-success btn-sm" 
                                                                onclick="openEditModal({{ $ticket->id }}, '{{ $ticket->first_name }}', '{{ $ticket->last_name }}', '{{ $ticket->is_scanned }}', {{ $ticket->ticket_id }})">
                                                                <i class='fa fa-edit'></i>
                                                            </button>

                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                            
                                            
                                        </table>
                                    </div>
                                </section>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="generatePassModal" tabindex="-1" aria-labelledby="generatePassModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="generatePassModalLabel">Generate Pass</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="generatePassForm" method="POST" action="{{ route('guest-tickets.bulk_store') }}">
                    @csrf
                    <div class="mb-3">
                        <label for="passCount" class="form-label">Select how many unnamed passes to be generated</label>
                        <select class="form-select" id="passCount" name="passCount" required>
                            <option value="1">1</option>
                            <option value="10">10</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                            <option value="200">200</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="ticketEventGenerate" class="form-label">Select the ticket for bulk export</label>
                        <select id="ticketEventGenerate" class="form-select" name="ticket" required>
                            @foreach ($ticket_list as $ticket)
                                <option value="{{ $ticket->id }}">{{ $ticket->event->title }} - {{ $ticket->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="submit" form="generatePassForm" class="btn btn-primary">Generate</button>
            </div>
        </div>
    </div>
</div>
    <div class="modal fade" id="bulkExportModal" tabindex="-1" aria-labelledby="bulkExportModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="bulkExportModalLabel">Bulk Export Pass</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="ticketEventBulk" class="form-label">Select the ticket for bulk export</label>
                        <select id="ticketEventBulk" class="form-select" name="ticket" required>
                            @foreach ($ticket_list as $ticket)
                                <option value="{{ $ticket->id }}">{{ $ticket->event->title }} - {{ $ticket->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" onclick="exportPassBulk()">Export Passes</button>
                </div>
            </div>
        </div>
    </div>

    
<!-- Edit Ticket Modal -->
<div class="modal fade" id="editTicketModal" tabindex="-1" aria-labelledby="editTicketModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editTicketModalLabel">Edit Ticket</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editTicketForm" method="POST" action="{{ route('guest-tickets.update' , $ticket->id ?? '') }}">
                    @csrf
                    @method('PUT')
                    <input type="hidden" id="ticket_id" name="ticket_id">

                    <div class="mb-3">
                        <label for="edit_first_name" class="form-label">First Name</label>
                        <input type="text" class="form-control" id="edit_first_name" name="first_name" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_last_name" class="form-label">Last Name</label>
                        <input type="text" class="form-control" id="edit_last_name" name="last_name" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_ticket" class="form-label">Ticket</label>
                        <select id="edit_ticket" class="form-select" name="ticket" required>
                            @foreach ($ticket_list as $ticket)
                                <option value="{{ $ticket->id }}">{{ $ticket->event->title }} - {{ $ticket->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="edit_status" class="form-label">Status</label>
                        <select id="edit_status" class="form-select" name="status" required>
                            <option value="1">Scanned</option>
                            <option value="0">Not Scanned</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="document.getElementById('editTicketForm').submit();">Save Changes</button>
            </div>
        </div>
    </div>
</div>

    <div class="modal fade" id="createTicketModal" tabindex="-1" aria-labelledby="createTicketModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createTicketModalLabel">Create Ticket</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="createTicketForm" method="POST" action="{{ route('guest-tickets.store') }}">
                        @csrf
                        <div class="mb-3">
                            <label for="ticketEventCreate" class="form-label">Ticket</label>
                            <select id="ticketEventCreate" class="form-select" name="ticket" required>
                                @foreach ($ticket_list as $ticket_list)
                                    <option value="{{ $ticket_list->id }}">{{ $ticket_list->event->title }} - {{ $ticket_list->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="first_name" class="form-label">First Name</label>
                            <input type="text" class="form-control" id="first_name" name="first_name" required>
                        </div>
                        <div class="mb-3">
                            <label for="last_name" class="form-label">Last Name</label>
                            <input type="text" class="form-control" id="last_name" name="last_name" required>
                        </div>
                        <div class="mb-3">
                            <label for="ticketStatus" class="form-label">Status</label>
                            <select id="ticketStatus" class="form-select" name="status" required>
                                <option value="1">Scanned</option>
                                <option value="0">Not Scanned</option>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" onclick="document.getElementById('createTicketForm').submit();">Create Ticket</button>
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

            // Initialize DataTable
            new DataTable('#example');
        });
        function openEditModal(ticketId, firstName, lastName, status, selectedTicketId) {
            document.getElementById('ticket_id').value = ticketId;
            document.getElementById('edit_first_name').value = firstName;
            document.getElementById('edit_last_name').value = lastName;
            document.getElementById('edit_ticket').value = selectedTicketId; // Set correct ticket
            document.getElementById('edit_status').value = status; // Set correct status
        }

        function exportPass(ticketId) {
            fetch(`/guest-tickets/export-pass/${ticketId}`)
                .then(response => response.blob()) // Get response as a Blob (binary large object)
                .then(blob => {
                    const url = window.URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    a.href = url;
                    a.download = `${ticketId}.pdf`; // Set the filename dynamically
                    document.body.appendChild(a);
                    a.click();
                    document.body.removeChild(a);
                    window.URL.revokeObjectURL(url);
                })
                .catch(error => console.error('Error:', error));
        }

        function exportPassBulk() {
            const ticketId = document.getElementById('ticketEventBulk').value; // Get selected ticket ID
            const currentDateTime = new Date().toISOString().replace(/[:.]/g, '-');
            fetch(`/guest-tickets/export-pass-bulk/${ticketId}`)
                .then(response => response.blob()) // Get response as a Blob (binary large object)
                .then(blob => {
                    const url = window.URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    a.href = url;
                    a.download = `${currentDateTime}.pdf`;
                    document.body.appendChild(a);
                    a.click();
                    document.body.removeChild(a);
                    window.URL.revokeObjectURL(url);
                })
                .catch(error => console.error('Error:', error));
        }



        function exportToExcel() {
            // Get table data
            var table = document.getElementById('example');
            var rows = table.querySelectorAll('tr');
            var data = [];

            // Iterate through rows and cells to get data
            rows.forEach(function(row) {
                var rowData = [];
                var cells = row.querySelectorAll('td, th'); // Get all cells (td and th)

                cells.forEach(function(cell, index) {
                    // Skip the last column (Actions column)
                    if (index !== cells.length - 1) {
                        rowData.push(cell.innerText);
                    }
                });
                data.push(rowData);
            });

            // Prepare Excel file
            var wb = XLSX.utils.book_new();
            var ws = XLSX.utils.aoa_to_sheet(data);

            // Append data to sheet
            XLSX.utils.book_append_sheet(wb, ws, 'Attendance Data');

            var now = new Date();
            var formattedDate = now.getFullYear() + '-' +
                String(now.getMonth() + 1).padStart(2, '0') + '-' +
                String(now.getDate()).padStart(2, '0') + '_' +
                String(now.getHours()).padStart(2, '0') + '-' +
                String(now.getMinutes()).padStart(2, '0') + '-' +
                String(now.getSeconds()).padStart(2, '0');

            // Save Excel file with the current date and time in the file name
            var fileName = formattedDate + '_attendance_data.xlsx';
            XLSX.writeFile(wb, fileName);
        }

        function selectEvent(eventId, eventName) {
            // Update the button text with the selected event
            document.getElementById('eventDropdownButton').textContent = eventName;

            // Update the heading text with the selected event name
            document.getElementById('selectedEventHeading').textContent = `${eventName}`;

            // Save the selected event to localStorage (optional for persistence across page reloads)
            localStorage.setItem('eventId', eventId);
            localStorage.setItem('selectedEventName', eventName);

            // Make an AJAX request to get the tickets for the selected event
            fetch(`/guest-tickets/filter/${eventId}`)
                .then(response => response.json())
                .then(tickets => {
                    // Update the tickets table with the filtered data
                    let ticketsTableBody = document.querySelector('#example tbody');
                    ticketsTableBody.innerHTML = ''; // Clear the existing table rows

                    tickets.forEach(ticket => {
                        let firstName = ticket.first_name; // Check if invoice exists before accessing first_name
                        let lastName = ticket.last_name;
                        let row = `
                    <tr>
                        <td>${ticket.ticket_no}</td>
                        <td>${ticket.ticket.event.title}</td>
                        <td>${firstName} ${lastName}</td>
                        <td>${ticket.is_scanned === 1 ? 'Scanned' : 'Not Scanned'}</td>
                    </tr>
                `;
                        ticketsTableBody.insertAdjacentHTML('beforeend', row);
                    });
                })
                .catch(error => console.error('Error fetching tickets:', error));
        }

        // On page load, check if an event is already selected and update the UI
        document.addEventListener('DOMContentLoaded', function() {
            const savedEventName = localStorage.getItem('selectedEventName');
            const savedEventId = localStorage.getItem('eventId');

            if (savedEventName && savedEventId) {
                document.getElementById('eventDropdownButton').textContent = savedEventName;
                document.getElementById('selectedEventHeading').textContent = `${savedEventName}`;

                // Fetch tickets for the saved event
                selectEvent(savedEventId, savedEventName);
            }
        });
    </script>
@endsection
