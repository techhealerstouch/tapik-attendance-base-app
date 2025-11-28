@extends('layouts.sidebar')

@section('content')
{{-- <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script> --}}
{{-- <link rel="stylesheet" href="https://cdn.datatables.net/2.0.8/css/dataTables.bootstrap5.css"> --}}
<script src="{{ asset('resources/ckeditor/ckeditor.js') }}"></script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://code.jquery.com/jquery-3.5.1.js"></script>
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
                                        <li class="breadcrumb-item">Attendance</li>
                                    </ol>
                                </div>
                                <div class="attendance-center">
                                    <h2 class="mb-4 card-header" style="background-color: transparent !important">
                                        <i> Attendance</i>
                                    </h2>
                                    <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                        data-bs-target="#attendanceModal"
                                        style="background-color: transparent; color: blue; border:none; border-color: transparent; padding-left: 10px;">
                                        Go to Users Attendance Page
                                    </button>
                                    <!-- <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                        data-bs-target="#ticketModal"
                                        style="background-color: transparent; color: blue; border: 0; padding-left: 10px;">
                                        Go to Guest Ticket Verification Page
                                    </button>  -->
                                    <br>

                                </div>

                                <br>
                                <div>
                                    <div class="mb-3 row">
                                        <div class="col-sm pr-0">
                                            {{-- <select class="form-control" id="event" name="event">
                                                <option value="" disabled selected>Filter by Event</option>
                                                @foreach ($eventTitles as $title)
                                                    <option value="{{ $title }}">{{ $title }}</option>
                                                @endforeach
                                            </select> --}}
                                        </div>
                                        <div class="col-sm-auto pl-0">
                                            <a href="/attendance/live-preview" class="btn btn-danger" >
                                                Live Preview
                                            </a>
                                        </div>
                                        <div class="col-sm-auto px-1">
                                            <button type="button" class="btn btn-success"
                                                onclick="exportToExcel()">Export as Excel</button>
                                                
                                        </div>
                                        <div class="col-sm-auto pl-0">
                                                <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                                data-bs-target="#addNewModal">
                                                + Create
                                            </button>
                                        </div>

                                    </div>
                                </div>
                                <div class="table-responsive">
                                    <table id="example" class="table table-striped" style="width:100%">
                                        <thead>
                                            <tr>
                                                <th scope="col">Name</th>
                                                <th scope="col">E-Mail</th>
                                                <th scope="col">Event</th>
                                                <th scope="col">Time In</th>
                                                <th scope="col">Represented By</th>
                                                <th scope="col">Status</th>
                                                <th scope="col">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($attendances as $attendance)
                                                <tr>
                                                    <td>{{ optional($attendance->user)->name ?? 'N/A' }}</td>
                                                    <td>{{ optional($attendance->user)->email ?? 'N/A' }}</td>
                                                    <td>{{ $attendance->event_name }}</td>
                                                    <td>
                                                        {{ $attendance->time_in ? \Carbon\Carbon::parse($attendance->time_in)->format('F j, Y h:i:sA') : '-------' }}
                                                    </td>                                                    
                                                    <td>{{ $attendance->rep_by ? $attendance->rep_by : '-------' }}</td>                                                    
                                                    <td>{{ $attendance->status }}</td>
                                                    <td>
                                                        <button type="button" class="btn btn-warning btn-sm edit-attendance"
                                                                data-id="{{ $attendance->id }}"
                                                                data-event="{{ $attendance->event_name }}"
                                                                data-time="{{ $attendance->time_in }}"
                                                                data-rep="{{ $attendance->rep_by }}"
                                                                data-status="{{ $attendance->status }}"
                                                                data-bs-toggle="modal" data-bs-target="#editAttendanceModal">
                                                            <i class="fa fa-edit" style="color: white;"></i>
                                                        </button>
                                                        <button type="button"
                                                            class="btn btn-danger btn-sm delete-schedule"
                                                            data-id="{{ $attendance->id }}" data-bs-toggle="modal"
                                                            data-bs-target="#deleteScheduleModal">
                                                            <i class='fa fa-trash'></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>

                                </div>

                        </div>


                        <!-- Modal Structure -->
                        <div class="modal fade" id="addNewModal" >
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="addNewModalLabel">Manual Attendance</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <form id="manualAttendanceForm" action="{{ route('attendance.store') }}"
                                            method="POST">
                                            @csrf
                                            <div class="col-md-12 mb-3">
                                                <label for="names" class="control-label">Select Member/User</label>
                                                <select class="form-control select-members" id="names" name="userId" style="width: 100% !important">
                                                    <option value="select-all">Select User/Member</option>
                                                    @foreach ($users as $user)
                                                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="mb-3">
                                                <label for="add-rep-by" class="form-label">Represented by</label>
                                                <input type="text" name="add_rep_by" id="add-rep-by" class="form-control">
                                            </div>
                        
                                            <div class="mb-3">
                                                <label for="add-rep-by" class="form-label">Event</label>
                                                <select class="form-control" id="add_event" name="add_event" required>
                                                    <option value="" disabled selected>Select an event</option>
                                                    @foreach ($eventTitles as $title)
                                                        <option value="{{ $title }}">{{ $title }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <!-- Status Select -->
                                            <div class="mb-3">
                                                <label for="status-selects" class="form-label">Status</label>
                                                <select class="form-select" id="status-selects" name="add_status">
                                                    <option value="Present">Present</option>
                                                    <option value="Absent">Absent</option>
                                                    <option value="Pending">Pending</option>
                                                </select>
                                            </div>
                                        </form>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-primary"
                                            onclick="document.getElementById('manualAttendanceForm').submit();">Create</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Delete Schedule Modal --}}
                        <div class="modal fade" id="deleteScheduleModal" tabindex="-1"
                            aria-labelledby="deleteScheduleModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="deleteScheduleModalLabel">Confirm Deletion</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        Are you sure you want to delete this attendance?
                                    </div>
                                    <div class="modal-footer">
                                        <form id="deleteScheduleForm" method="POST" action="">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="btn btn-secondary"
                                                data-bs-dismiss="modal">Cancel</button>
                                            <button type="submit" class="btn btn-danger">Delete</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Open Attendance Page Input Modal -->
                        <div class="modal fade" id="attendanceModal" tabindex="-1"
                            aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="exampleModalLabel">Select Event</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <form id="attendancePage" action="{{ route('attendance.page') }}"
                                            method="POST">
                                            @csrf
                                            <div class="mb-3">
                                                <select class="form-control" id="event" name="event" required>
                                                    <option value="" disabled selected>Select an event</option>
                                                    @foreach ($eventTitles as $title)
                                                        <option value="{{ $title }}">{{ $title }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </form>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-primary"
                                            onclick="document.getElementById('attendancePage').submit();">Go</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="modal fade" id="editAttendanceModal" tabindex="-1" aria-labelledby="editAttendanceModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="editAttendanceModalLabel">Edit Attendance</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <form action="{{ route('updateAttendance') }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-body">
                                            <input type="hidden" name="id" id="attendance-id">

                                            <!-- Event Select -->
                                            <div class="mb-3">
                                                <label for="event-select" class="form-label">Event</label>
                                                <select class="form-control" id="event-select" name="event" required>
                                                    <option value="" disabled>Select an event</option>
                                                    @foreach ($eventTitles as $title)
                                                        <option value="{{ $title }}">{{ $title }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                        

                                            <div class="mb-3">
                                                <label for="rep-by" class="form-label">Represented by</label>
                                                <input type="text" name="rep_by" id="rep-by" class="form-control">
                                            </div>
                        
                                            <!-- Status Select -->
                                            <div class="mb-3">
                                                <label for="status-select" class="form-label">Status</label>
                                                <select class="form-select" id="status-select" name="status">
                                                    <option value="Present">Present</option>
                                                    <option value="Absent">Absent</option>
                                                    <option value="Pending">Pending</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                            <button type="submit" class="btn btn-primary">Save changes</button>
                                        </div>
                                    </form>
                                </div>
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




    <script defer src="{{ url('assets/js/cdn.min.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/2.0.8/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/2.0.8/js/dataTables.bootstrap5.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.0/xlsx.full.min.js"></script>



    {{-- <script src="{{ url('assets/js/livewire-sortable.js') }}"></script> --}}
    <script>

        $(document).ready(function () {
            $('.select-members').select2({
                placeholder: "Select members/users to the event",
                allowClear: true
            });

            const $eventSelect = $('.select-event').select2({
                placeholder: "Select Event",
                allowClear: true
            });

            // "Select All" functionality
            $('.select-members').on('select2:select', function(e) {
                if (e.params.data.id === 'select-all') {
                    // Select all options
                    $(this).val($(this).find('option:not([value="select-all"])').map(function() {
                        return $(this).val();
                    }).get()).trigger('change');
                }
            });

            $('.select-members').on('select2:unselect', function(e) {
                if (e.params.data.id === 'select-all') {
                    // Deselect all options
                    $(this).val(null).trigger('change');
                }
            });

            new DataTable('#example');
        document.getElementById('event').addEventListener('change', function() {
            $selectedEvent = this.value;
        });

    });

        document.addEventListener('DOMContentLoaded', () => {

    const editButtons = document.querySelectorAll('.edit-attendance');
    editButtons.forEach(button => {
        button.addEventListener('click', () => {
            // Fill hidden input with ID
            document.getElementById('attendance-id').value = button.dataset.id;

            // Set event dropdown to the correct value
            const eventSelect = document.getElementById('event-select');
            eventSelect.value = button.dataset.event;


            // Set rep-by input to the correct value (fix this line)
            const rep = document.getElementById('rep-by');
            rep.value = button.dataset.rep; // Corrected this line

            // Set status dropdown to the correct value
            const statusSelect = document.getElementById('status-select');
            statusSelect.value = button.dataset.status;
        });
    });
});

        document.getElementById('event').addEventListener('change', function() {
            var selectedEvent = this.value;

            // Make an AJAX request to fetch updated attendance data
            $.ajax({
                url: "{{ route('attendance.fetch') }}",
                type: 'GET',
                data: {
                    event: selectedEvent
                },
                success: function(response) {
                    var tbody = $('#example tbody');
                    tbody.empty(); // Clear existing rows
                    if (response.length === 0) {
                        tbody.append('<tr><td colspan="5">No data to be displayed</td></tr>');
                    } else {
                        $.each(response, function(index, attendance) {
                            tbody.append('<tr>' +
                                '<td>' + attendance.user.rfid_no + '</td>' +
                                '<td>' + attendance.user.name + '</td>' +
                                '<td>' + attendance.event_name + '</td>' +
                                '<td>' + attendance.time_in + '</td>' +
                                '<td>' + attendance.status + '</td>' +
                                '</tr>');
                        });
                    }
                }
            });
        });

        $(document).ready(function() {
            $(document).on('click', '.delete-schedule', function() {
                scheduleIdToDelete = $(this).data('id');
                $('#deleteScheduleForm').attr('action', '/attendance/delete/' + scheduleIdToDelete);
            });

            // Handle the delete operation
            $('#deleteScheduleForm').on('submit', function(e) {
                e.preventDefault();
                $.ajax({
                    url: $(this).attr('action'),
                    method: 'DELETE',
                    data: $(this).serialize(),
                    success: function(response) {
                        if (response.message) { // Check for the 'message' key
                            alert(response.message); // Show the success message
                            location.reload(); // Reload the page to reflect the changes
                        }
                    },
                    error: function(xhr) {
                        console.log('AJAX Error:', xhr);
                        alert('Failed to delete the schedule.');
                    }
                });
            });
        });

        function exportToExcel() {
            // Get the selected event value from the dropdown
            var selectedEvent = document.getElementById('event').value;

            // Get table data
            var table = document.getElementById('example');
            var rows = table.querySelectorAll('tr');
            var data = [];

            // Iterate through rows and cells to get data
            rows.forEach(function(row) {
                var rowData = [];
                row.querySelectorAll('td, th').forEach(function(cell) { // Include th in the selection
                    rowData.push(cell.innerText);
                });
                data.push(rowData);
            });

            // Prepare Excel file
            var wb = XLSX.utils.book_new();
            var ws = XLSX.utils.aoa_to_sheet(data);

            // Add table headers to the sheet
            var headerData = [];
            table.querySelectorAll('thead tr th').forEach(function(headerCell) {
                headerData.push(headerCell.innerText);
            });
            XLSX.utils.sheet_add_aoa(ws, [headerData], {
                origin: -1
            });

            // Append data to sheet
            XLSX.utils.book_append_sheet(wb, ws, 'Attendance Data');

            // Save Excel file with Event name in the file name
            var fileName = selectedEvent + '_attendance_data.xlsx'; // Include Event name in the file name
            XLSX.writeFile(wb, fileName);
        }
    </script>

    <style>
        .attendance-center {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
        }
        .select2-container {
    z-index: 9999 !important; /* Ensure it's above the modal */
}
.select2-dropdown {
z-index: 10060 !important;
}
        
    </style>
@endsection
