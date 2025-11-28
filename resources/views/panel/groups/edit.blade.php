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
                                            <li class="breadcrumb-item">Manage Groups</li>
                                            <li class="breadcrumb-item">Edit Group</li>
                                        </ol>
                                    </div>
                                    <form method="POST" action="{{ route('groups.update', $groupInfo->id) }}">
                                        @csrf
                                        @method('PUT')
                                        <div class="row">
                                            <div class="form-group col-md-6">
                                                <label for="name">Group Name</label>
                                                <input type="text" class="form-control" name="name" id="name" value="{{ $groupInfo->name ?? '' }}" required>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="form-group col-md-6">
                                                <label for="description">Description</label>
                                                <input type="text" class="form-control" name="description" id="description" value="{{ $groupInfo->description ?? '' }}">
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="form-group col-md-6">
                                                <label for="name" class="control-label">Select Users/Members</label>
                                                <select class="form-control select-members" name="userId[]" multiple="multiple" style="width: 100% !important">
                                                    <option value="select-all">Select All</option>
                                                    @foreach ($users as $user)
                                                        <option value="{{ $user->id }}" 
                                                            @if($usersInGroup->contains($user->id)) selected @endif>
                                                            {{ $user->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        
                                        <button type="submit" class="btn btn-primary">Update</button>
                                    </form>
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
        $(document).ready(function() {
            // Initialize Select2 for users and events
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

            function convertToMilitaryTime(time) {
                const [hours, minutes, modifier] = time.match(/(\d+):(\d+)\s?(AM|PM)/i).slice(1);
                let militaryHours = hours === '12' ? 0 : parseInt(hours, 10);
                if (modifier.toUpperCase() === 'PM' && hours !== '12') {
                    militaryHours += 12;
                }
                return `${('0' + militaryHours).slice(-2)}:${minutes}`;
            }
            $(document).on('click', '.edit-attendance', function() {
                var scheduleId = $(this).data('id');

                $.ajax({
                    url: '/attendance/attendance_logs/' + scheduleId,
                    method: 'GET',
                    success: function(data) {
                        if (data.error) {
                            alert(data.error);
                        } else {
                            console.log(data)
                            // Update the form fields in the modal with the received data
                            $('#editAttendanceLogModal #name').val(data.user.name);
                            $('#editAttendanceLogModal #date').val(data.date);
                            $('#editAttendanceLogModal #time_in').val(data.time_in ?
                                toMilitaryTime(data.time_in) : null);
                            $('#editAttendanceLogModal #time_out').val(data.time_out ?
                                toMilitaryTime(data.time_out) : null);
                        }
                    },
                    error: function(xhr) {
                        console.log('AJAX Error:', xhr);
                        alert('Failed to retrieve schedule data.');
                    }
                });
            });

            $(document).on('click', '.delete-attendance', function() {
                scheduleIdToDelete = $(this).data('id');
                $('#deleteScheduleForm').attr('action', '/attendance/attendance_logs/' +
                scheduleIdToDelete);
            });

            // Handle the delete operation
            $('#deleteScheduleForm').on('submit', function(e) {
                e.preventDefault();
                $.ajax({
                    url: $(this).attr('action'),
                    method: 'DELETE',
                    data: $(this).serialize(),
                    success: function(response) {
                        if (response.success) {
                            alert(response.success);
                            location.reload(); // Reload the page to reflect the changes
                        } else if (response.error) {
                            alert(response.error);
                        }
                    },
                    error: function(xhr) {
                        console.log('AJAX Error:', xhr);
                        alert('Failed to delete the schedule.');
                    }
                });
            });


            // Initialize DataTable
            new DataTable('#example');
        });

       
    </script>
@endsection
