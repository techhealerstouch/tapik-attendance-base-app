<!-- Updated Attendance Management Page -->
@extends('layouts.sidebar')

@section('content')
<div class="container-fluid content-inner mt-n5 py-0">
    <!-- Success/Error Alerts -->
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-lg-12">
            <div class="card rounded">
                <div class="card-header">
                    <h4 class="card-title">
                        <i class="bi bi-clipboard-check"></i> Attendance Management
                    </h4>
                    <p class="text-muted mb-0">
                        <small><i class="bi bi-info-circle"></i> Manage and track event attendance</small>
                    </p>
                </div>
                <div class="card-body">
                    <!-- Event Selection Row -->
                    <div class="row mb-4">
                        <!-- Left Column: Event Selection -->
                        <div class="col-lg-6 col-md-12 mb-3 mb-lg-0">
                            <label for="eventSelect" class="form-label">Select Event</label>
                            <select id="eventSelect" class="form-select">
                                <option value="">-- Select Event --</option>
                                @foreach ($eventTitles as $title)
                                    <option value="{{ $title }}">{{ $title }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Right Column: Action Buttons -->
                        <div class="col-lg-6 col-md-12 d-flex align-items-end gap-2">
                            <button type="button" id="attendancePageBtn" class="btn btn-primary flex-fill" onclick="showRepresentativeModal()" disabled>
                                <i class="bi bi-qr-code-scan"></i> Attendance Page
                            </button>
                            <a href="/attendance/live-preview" class="btn btn-danger flex-fill">
                                <i class="bi bi-broadcast"></i> Live Preview
                            </a>
                            <button type="button" class="btn btn-success flex-fill" data-bs-toggle="modal"
                                data-bs-target="#addNewModal">
                                <i class="bi bi-plus-circle"></i> Create
                            </button>
                        </div>
                    </div>

                    <!-- Attendance Table -->
                    <div class="table-responsive">
                        <table id="attendanceTable" class="table table-striped" style="width:100%">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>E-Mail</th>
                                    <th>Event</th>
                                    <th>Time In</th>
                                    <th>Represented By</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="attendanceTableBody">
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">
                                        <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                        <p class="mb-0">No Event Selected</p>
                                        <small>Please select an event from the dropdown above</small>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Representative Prompt Modal -->
<div class="modal fade" id="representativePromptModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Representative Prompt</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <i class="bi bi-question-circle text-primary" style="font-size: 3rem;"></i>
                <h5 class="mt-3">Enable Representative Prompt?</h5>
                <p class="text-muted">
                    Do you want attendees to confirm their representative after scanning?
                </p>
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-success px-4" onclick="openAttendancePage(true)">
                    <i class="bi bi-check-circle"></i> Yes
                </button>
                <button type="button" class="btn btn-secondary px-4" onclick="openAttendancePage(false)">
                    <i class="bi bi-x-circle"></i> No
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Create Manual Attendance Modal -->
<div class="modal fade" id="addNewModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Manual Attendance</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="manualAttendanceForm" action="{{ route('attendance.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="names" class="form-label">Select Member/User</label>
                        <select class="form-control select-members" id="names" name="userId" style="width: 100%" required>
                            <option value="">Select User/Member</option>
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
                        <label for="add_event" class="form-label">Event</label>
                        <select class="form-control" id="add_event" name="add_event" required>
                            <option value="" disabled selected>Select an event</option>
                            @foreach ($eventTitles as $title)
                                <option value="{{ $title }}">{{ $title }}</option>
                            @endforeach
                        </select>
                    </div>
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
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="document.getElementById('manualAttendanceForm').submit();">Create</button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Attendance Modal -->
<div class="modal fade" id="editAttendanceModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Attendance</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('updateAttendance') }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <input type="hidden" name="id" id="attendance-id">
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

<!-- Delete Attendance Modal -->
<div class="modal fade" id="deleteScheduleModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Deletion</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this attendance record?
            </div>
            <div class="modal-footer">
                <form id="deleteScheduleForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('sidebar-scripts')
<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />

<!-- DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/2.0.8/css/dataTables.bootstrap5.css">

<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<!-- DataTables JS -->
<script src="https://cdn.datatables.net/2.0.8/js/dataTables.js"></script>
<script src="https://cdn.datatables.net/2.0.8/js/dataTables.bootstrap5.js"></script>

<!-- XLSX for Excel Export -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.0/xlsx.full.min.js"></script>

<style>
    .select2-container {
        z-index: 1040 !important;
    }
    .select2-dropdown {
        z-index: 1040 !important;
    }
    .modal {
        z-index: 1055 !important;
    }
    .modal-backdrop {
        z-index: 1050 !important;
    }
    .card-header {
        border-left: 4px solid #0d6efd;
    }
    .btn {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }
</style>

<script>
(function() {
    'use strict';
    
    let dataTable = null;
    let currentSelectedEvent = '';

    // Wait for DOM to be fully loaded
    document.addEventListener('DOMContentLoaded', function() {
        console.log('DOM loaded, jQuery version:', $.fn.jquery);
        
        // Initialize Select2 for event dropdown
        try {
            $('#eventSelect').select2({
                theme: 'bootstrap-5',
                placeholder: '-- Select Event --',
                allowClear: true,
                width: '100%'
            });
            console.log('Event Select2 initialized');
        } catch (e) {
            console.error('Error initializing event select2:', e);
        }

        // Initialize Select2 for user selection
        try {
            $('.select-members').select2({
                placeholder: "Select User/Member",
                allowClear: true,
                dropdownParent: $('#addNewModal')
            });
            console.log('User Select2 initialized');
        } catch (e) {
            console.error('Error initializing user select2:', e);
        }

        // Handle event selection
        $('#eventSelect').on('select2:select', function(e) {
            currentSelectedEvent = e.params.data.id;
            console.log('Event selected:', currentSelectedEvent);
            
            // Enable Attendance Page button
            $('#attendancePageBtn').prop('disabled', false);
            
            if (currentSelectedEvent && currentSelectedEvent !== '') {
                loadAttendanceData(currentSelectedEvent);
            }
        });

        $('#eventSelect').on('select2:clear', function(e) {
            console.log('Event cleared');
            currentSelectedEvent = '';
            
            // Disable Attendance Page button
            $('#attendancePageBtn').prop('disabled', true);
            
            showNoEventSelected();
        });

        // Edit attendance
        $(document).on('click', '.edit-attendance', function() {
            $('#attendance-id').val($(this).data('id'));
            $('#event-select').val($(this).data('event'));
            $('#rep-by').val($(this).data('rep'));
            $('#status-select').val($(this).data('status'));
        });

        // Delete attendance
        $(document).on('click', '.delete-schedule', function() {
            const scheduleId = $(this).data('id');
            $('#deleteScheduleForm').attr('action', '/attendance/delete/' + scheduleId);
        });

        // Handle delete form
        $('#deleteScheduleForm').on('submit', function(e) {
            e.preventDefault();
            $.ajax({
                url: $(this).attr('action'),
                method: 'DELETE',
                data: $(this).serialize(),
                success: function(response) {
                    alert(response.message || 'Deleted successfully');
                    $('#deleteScheduleModal').modal('hide');
                    if (currentSelectedEvent) {
                        loadAttendanceData(currentSelectedEvent);
                    }
                },
                error: function(xhr) {
                    alert('Failed to delete: ' + (xhr.responseJSON?.message || 'Unknown error'));
                }
            });
        });
    });

    function loadAttendanceData(eventName) {
        console.log('Loading attendance for:', eventName);
        
        // Show loading
        $('#attendanceTableBody').html(`
            <tr>
                <td colspan="7" class="text-center py-4">
                    <div class="spinner-border text-primary"></div>
                    <p class="mt-2">Loading...</p>
                </td>
            </tr>
        `);

        // Destroy DataTable if exists
        if (dataTable) {
            dataTable.destroy();
            dataTable = null;
        }

        // AJAX call
        $.ajax({
            url: '{{ route("attendance.fetch") }}',
            method: 'GET',
            data: { event: eventName },
            success: function(response) {
                console.log('Response received:', response);
                displayAttendanceData(response);
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', xhr.responseText);
                $('#attendanceTableBody').html(`
                    <tr>
                        <td colspan="7" class="text-center text-danger py-4">
                            <i class="bi bi-exclamation-triangle fs-1 d-block mb-2"></i>
                            <p class="mb-0">Error loading data</p>
                            <small>${error}</small>
                        </td>
                    </tr>
                `);
            }
        });
    }

    function displayAttendanceData(attendances) {
        const tbody = $('#attendanceTableBody');
        tbody.empty();

        if (!attendances || attendances.length === 0) {
            tbody.html(`
                <tr>
                    <td colspan="7" class="text-center text-muted py-4">
                        <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                        <p class="mb-0">No Attendance Records</p>
                    </td>
                </tr>
            `);
            return;
        }

        attendances.forEach(function(attendance) {
            const userName = attendance.user?.name || 'N/A';
            const userEmail = attendance.user?.email || 'N/A';
            const timeIn = attendance.time_in 
                ? new Date(attendance.time_in).toLocaleString('en-US', {
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit',
                    second: '2-digit',
                    hour12: true
                })
                : '-------';
            const repBy = attendance.rep_by || '-------';
            
            let statusBadge = '<span class="badge bg-warning text-white">Pending</span>';
            if (attendance.status === 'Present') {
                statusBadge = '<span class="badge bg-success">Present</span>';
            } else if (attendance.status === 'Absent') {
                statusBadge = '<span class="badge bg-danger">Absent</span>';
            }

            tbody.append(`
                <tr>
                    <td>${userName}</td>
                    <td>${userEmail}</td>
                    <td>${attendance.event_name}</td>
                    <td>${timeIn}</td>
                    <td>${repBy}</td>
                    <td>${statusBadge}</td>
                    <td>
                        <button type="button" class="btn btn-warning btn-sm edit-attendance"
                            data-id="${attendance.id}"
                            data-event="${attendance.event_name}"
                            data-rep="${attendance.rep_by || ''}"
                            data-status="${attendance.status}"
                            data-bs-toggle="modal" 
                            data-bs-target="#editAttendanceModal">
                            <i class="fa fa-edit"></i>
                        </button>
                        <button type="button" class="btn btn-danger btn-sm delete-schedule"
                            data-id="${attendance.id}" 
                            data-bs-toggle="modal"
                            data-bs-target="#deleteScheduleModal">
                            <i class="fa fa-trash"></i>
                        </button>
                    </td>
                </tr>
            `);
        });

        // Initialize DataTable
        try {
            dataTable = $('#attendanceTable').DataTable({
                pageLength: 10,
                order: [[3, 'desc']],
                language: {
                    search: "_INPUT_",
                    searchPlaceholder: "Search..."
                }
            });
            console.log('DataTable initialized');
        } catch (e) {
            console.error('Error initializing DataTable:', e);
        }
    }

    function showNoEventSelected() {
        if (dataTable) {
            dataTable.destroy();
            dataTable = null;
        }
        
        $('#attendanceTableBody').html(`
            <tr>
                <td colspan="7" class="text-center text-muted py-4">
                    <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                    <p class="mb-0">No Event Selected</p>
                    <small>Please select an event</small>
                </td>
            </tr>
        `);
    }

    // Show representative prompt modal
    window.showRepresentativeModal = function() {
        if (!currentSelectedEvent || currentSelectedEvent === '') {
            alert('Please select an event first');
            return;
        }
        
        $('#representativePromptModal').modal('show');
    };

    // Open Attendance Page function with representative prompt option
    window.openAttendancePage = function(enablePrompt) {
        if (!currentSelectedEvent || currentSelectedEvent === '') {
            alert('Please select an event first');
            return;
        }
        
        // Hide the modal
        $('#representativePromptModal').modal('hide');
        
        // Create a form and submit it to open in new tab
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("attendance.page") }}';
        form.target = '_blank';
        
        // Add CSRF token
        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '_token';
        csrfInput.value = '{{ csrf_token() }}';
        form.appendChild(csrfInput);
        
        // Add event name
        const eventInput = document.createElement('input');
        eventInput.type = 'hidden';
        eventInput.name = 'event';
        eventInput.value = currentSelectedEvent;
        form.appendChild(eventInput);
        
        // Add representative prompt flag
        const repPromptInput = document.createElement('input');
        repPromptInput.type = 'hidden';
        repPromptInput.name = 'enable_rep_prompt';
        repPromptInput.value = enablePrompt ? '1' : '0';
        form.appendChild(repPromptInput);
        
        // Submit the form
        document.body.appendChild(form);
        form.submit();
        document.body.removeChild(form);
    };

    // Export to Excel function - make it global
    window.exportToExcel = function() {
        if (!currentSelectedEvent) {
            alert('Please select an event first');
            return;
        }

        const data = [];
        
        // Headers
        const headers = ['Name', 'Email', 'Event', 'Time In', 'Represented By', 'Status'];
        data.push(headers);

        // Data rows
        $('#attendanceTable tbody tr').each(function() {
            if ($(this).find('td').length > 1) {
                const row = [];
                $(this).find('td').slice(0, 6).each(function() {
                    row.push($(this).text().trim());
                });
                data.push(row);
            }
        });

        const wb = XLSX.utils.book_new();
        const ws = XLSX.utils.aoa_to_sheet(data);
        XLSX.utils.book_append_sheet(wb, ws, 'Attendance');
        
        const fileName = currentSelectedEvent.replace(/[^a-z0-9]/gi, '_') + '_attendance.xlsx';
        XLSX.writeFile(wb, fileName);
    };

    // Make functions available globally
    window.loadAttendanceData = loadAttendanceData;
    window.displayAttendanceData = displayAttendanceData;
    window.showNoEventSelected = showNoEventSelected;
})();
</script>
@endpush