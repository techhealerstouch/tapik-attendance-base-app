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
                                            <li class="breadcrumb-item">Event List</li>
                                        </ol>
                                    </div>
                                    <div class="row">
                                        <div class="col-6 col-lg-6 p-1">
                                            <button class="btn button bg-dark text-white"
                                                onclick="exportToExcel()">Export</button>
                                        </div>
                                        <div class="col-6 col-lg-6 text-end p-1">
                                            <a href="/event-list/create" class="btn button text-white" style="background-color: rgb(5, 40, 132);">
                                                Add Event
                                            </a>
                                        </div>
                                    </div>
                                    <div class="table-responsive">
                                        <table id="example" class="table table-striped" style="width:100%">
                                            <thead>
                                                <tr>
                                                    <th scope="col">Event Name</th>
                                                    <th scope="col">Start </th>
                                                    <th scope="col">End</th>
                                                    <th scope="col">Address</th>
                                                    <th scope="col">Status</th>
                                                    <th scope="col">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($events as $event)
                                                    <tr>
                                                        <td>{{ $event->title }}</td>
                                                        <td>{{ \Carbon\Carbon::parse($event->start)->format('F j, Y g:iA') }}</td>
                                                        <td>{{ \Carbon\Carbon::parse($event->end)->format('F j, Y g:iA') }}</td>                                                        
                                                        <td>{{ $event->address }}</td>
                                                        <td>{{ $event->status == 1 ? 'Active' : 'InActive' }}</td>
                                                        {{-- <td>{{ $attendee->attendance ? $attendee->attendance->status : 'Empty' }}</td> --}}
                                                        <td>
                                                            <a href="{{ route('events.edit', $event->id) }}" class="btn btn-sm btn-warning">
                                                                <i class="fa fa-edit text-white"></i>
                                                            </a>                                                       
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
    </div>
    <!-- Modal for Creating Discount -->
    <div class="modal fade" id="addDiscountModal" tabindex="-1" aria-labelledby="addDiscountModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addDiscountModalLabel">Add Attendee</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="discountForm" action="{{ route('discounts.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="code" class="form-label d-flex align-items-center">
                                Code (Max Characters: 12)
                                <button type="button" class="btn btn-sm ms-2 p-0 m-0" style="color: blue;"
                                    onclick="generateCode()" title="Generate Code">
                                    Generate Code <!-- Use Bootstrap icon for 'generate' -->
                                </button>
                            </label>
                            <input type="text" name="code" id="code" class="form-control" maxlength="12"
                                required>
                        </div>
                        <div class="mb-3">
                            <label for="name" class="form-label">Name</label>
                            <input type="text" name="name" id="name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="type" class="form-label">Type</label>
                            <select name="type" id="type" class="form-select" required>
                                <option value="">Select Type</option>
                                <option value="percentage">Percentage</option>
                                <option value="fixed">Fixed Amount</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="amount" class="form-label">Amount</label>
                            <input type="number" name="amount" id="amount" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="valid_from" class="form-label">Valid From</label>
                            <input type="date" name="valid_from" id="valid_from" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="valid_until" class="form-label">Valid Until</label>
                            <input type="date" name="valid_until" id="valid_until" class="form-control" required>
                        </div>
                        <div class="mb-3 form-check">
                            <input type="checkbox" name="is_active" id="is_active" class="form-check-input" checked>
                            <label class="form-check-label" for="is_active">Active</label>
                            <input type="hidden" name="is_active" value="0">
                            <!-- Hidden input to submit 0 if unchecked -->
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary" id="saveEventButton" disabled>Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Modal for Editing Discount -->
    <div class="modal fade" id="editEventModal" tabindex="-1" aria-labelledby="editEventModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editEventModalLabel">Edit Event</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editEventForm" method="POST">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="id" id="edit_id">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="edit_title" class="form-label">Event Name</label>
                            <input type="text" name="title" id="edit_title" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_start" class="form-label">Start Date and Time</label>
                            <input type="datetime-local" name="start" id="edit_start" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_end" class="form-label">End Date and Time</label>
                            <input type="datetime-local" name="end" id="edit_end" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label for="edit_status" class="form-label">Status</label>
                            <select name="status" id="edit_status" class="form-select" required>
                                <option value="">Select Status</option>
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/2.0.8/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/2.0.8/js/dataTables.bootstrap5.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.0/xlsx.full.min.js"></script>
    <script>
        function populateEditModal(event) {
            document.getElementById("edit_id").value = event.id;
            document.getElementById("edit_title").value = event.title;
            document.getElementById("edit_start").value = event.start;
            document.getElementById("edit_end").value = event.end;
            document.getElementById("edit_status").value = event.status;

            // Use the full base URL for the route
            const formAction = `/event-list/${event.id}`;
            document.getElementById("editEventForm").action = formAction;
        }

        // Disable the save button until all required fields are filled
        document.addEventListener("DOMContentLoaded", function() {
            const discountForm = document.getElementById("discountForm");
            const saveEventButton = document.getElementById("saveEventButton");

            discountForm.addEventListener("input", function() {
                let isFormComplete = true;
                discountForm.querySelectorAll("input[required], select[required]").forEach((input) => {
                    if (!input.value.trim()) {
                        isFormComplete = false;
                    }
                });
                saveEventButton.disabled = !isFormComplete;
            });
        });
        document.addEventListener("DOMContentLoaded", function() {
            const discountForm = document.getElementById("discountForm");
            const saveEventButton = document.getElementById("saveEventButton");

            discountForm.addEventListener("input", function() {
                let isFormComplete = true;
                // Check if each required input has a value
                discountForm.querySelectorAll("input[required], select[required]").forEach((input) => {
                    if (!input.value.trim()) {
                        isFormComplete = false;
                    }
                });
                // Enable or disable the button based on form completion
                saveEventButton.disabled = !isFormComplete;
            });
        });
        $(document).ready(function() {
            // Initialize Select2
            $('.select-members').select2({
                placeholder: "Select User to add in the group",
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


            // Initialize DataTable
            new DataTable('#example');
        });

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
            var fileName = 'Invoice_data_' + formattedDate + '.xlsx';
            XLSX.writeFile(wb, fileName);
        }
    </script>
@endsection
