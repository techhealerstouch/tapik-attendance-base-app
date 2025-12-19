<!-- Attendance Report Page -->
@extends('layouts.sidebar')

@section('content')
<div class="container-fluid content-inner mt-n5 py-0">
    <div class="row">
        <div class="col-lg-12">
            <div class="card rounded">
                <div class="card-header">
                    <h4 class="card-title">
                        <i class="bi bi-bar-chart-fill"></i> Attendance Report
                    </h4>
                    <p class="text-muted mb-0">
                        <small><i class="bi bi-info-circle"></i> View comprehensive attendance analytics and reports</small>
                    </p>
                </div>
                <div class="card-body">
                    <!-- Event Selection Row -->
                    <div class="row mb-4 align-items-end">
                        <div class="col-lg-9 col-md-8 mb-3 mb-lg-0">
                            <label for="eventSelect" class="form-label">Select Event</label>
                            <select id="eventSelect" class="form-select">
                                <option value="">-- Select Event --</option>
                                @foreach ($events as $event)
                                    <option value="{{ $event->id }}">{{ $event->title }} - {{ \Carbon\Carbon::parse($event->start)->format('M d, Y') }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-lg-3 col-md-4">
                            <button type="button" id="exportExcelBtn" class="btn btn-success w-100" onclick="exportToExcel()" disabled>
                                <i class="bi bi-file-earmark-excel"></i> Export to Excel
                            </button>
                        </div>
                    </div>

                    <!-- Report Content -->
                    <div id="summarySection" style="display: none;">
                        <!-- Event Information Header -->
                        <div class="card bg-light border-0 mb-4">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col-md-8">
                                        <h5 class="mb-1" id="eventTitle"></h5>
                                        <p class="text-muted mb-0" id="eventDateTime"></p>
                                    </div>
                                    <div class="col-md-4 text-md-end mt-3 mt-md-0">
                                        <div class="d-inline-block">
                                            <span class="text-muted small">Attendance Rate</span>
                                            <h2 class="mb-0 text-primary" id="attendanceRate"></h2>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Main Statistics Grid -->
                        <div class="row mb-4">
                            <!-- Attendance Status -->
                            <div class="col-lg-6 mb-4">
                                <div class="card h-100">
                                    <div class="card-body">
                                        <h6 class="card-subtitle mb-3 text-muted">
                                            <i class="bi bi-clipboard-check"></i> Attendance Status
                                        </h6>
                                        <div class="row g-3">
                                            <div class="col-6">
                                                <div class="d-flex justify-content-between align-items-center mb-2">
                                                    <span class="text-muted">Total Registered</span>
                                                    <span class="badge bg-primary" id="totalRegistered">0</span>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="d-flex justify-content-between align-items-center mb-2">
                                                    <span class="text-muted">Present</span>
                                                    <span class="badge bg-success" id="presentCount">0</span>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <span class="text-muted">Pending</span>
                                                    <span class="badge bg-warning text-dark" id="pendingCount">0</span>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <span class="text-muted">Absent</span>
                                                    <span class="badge bg-danger" id="absentCount">0</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Arrival Statistics -->
                            <div class="col-lg-6 mb-4">
                                <div class="card h-100">
                                    <div class="card-body">
                                        <h6 class="card-subtitle mb-3 text-muted">
                                            <i class="bi bi-clock-history"></i> Arrival Breakdown
                                        </h6>
                                        <div class="row g-3">
                                            <div class="col-4 text-center">
                                                <div class="mb-1">
                                                    <i class="bi bi-arrow-down-circle text-success fs-4"></i>
                                                </div>
                                                <h4 class="mb-0 text-success" id="earlyCount">0</h4>
                                                <small class="text-muted">Early</small>
                                            </div>
                                            <div class="col-4 text-center">
                                                <div class="mb-1">
                                                    <i class="bi bi-check-circle text-primary fs-4"></i>
                                                </div>
                                                <h4 class="mb-0 text-primary" id="onTimeCount">0</h4>
                                                <small class="text-muted">On Time</small>
                                            </div>
                                            <div class="col-4 text-center">
                                                <div class="mb-1">
                                                    <i class="bi bi-arrow-up-circle text-warning fs-4"></i>
                                                </div>
                                                <h4 class="mb-0 text-warning" id="lateCount">0</h4>
                                                <small class="text-muted">Late</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Scan Statistics -->
                            <div class="col-lg-12">
                                <div class="card">
                                    <div class="card-body">
                                        <h6 class="card-subtitle mb-3 text-muted">
                                            <i class="bi bi-qr-code-scan"></i> Scan Analytics
                                        </h6>
                                        <div class="row text-center">
                                            <div class="col-md-3 col-6 mb-3 mb-md-0">
                                                <h4 class="mb-1 text-info" id="usersScanned">0</h4>
                                                <small class="text-muted">Users Scanned</small>
                                            </div>
                                            <div class="col-md-3 col-6 mb-3 mb-md-0">
                                                <h4 class="mb-1 text-secondary" id="totalScans">0</h4>
                                                <small class="text-muted">Total Scans</small>
                                            </div>
                                            <div class="col-md-3 col-6">
                                                <h4 class="mb-1 text-primary" id="avgScans">0</h4>
                                                <small class="text-muted">Avg per User</small>
                                            </div>
                                            <div class="col-md-3 col-6">
                                                <h4 class="mb-1 text-warning" id="multipleScans">0</h4>
                                                <small class="text-muted">Multiple Scans</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Attendance Details Table -->
                    <div id="attendanceDetailsSection" style="display: none;">
                        <h5 class="mb-3"><i class="bi bi-list-ul"></i> Detailed Attendance Records</h5>
                        <div class="table-responsive">
                            <table id="attendanceTable" class="table table-striped table-hover" style="width:100%">
                                <thead class="table-light">
                                    <tr>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Status</th>
                                        <th>Time In</th>
                                        <th>Arrival Status</th>
                                        <th>Represented By</th>
                                    </tr>
                                </thead>
                                <tbody id="attendanceTableBody"></tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Seat Assignment Table -->
                    <div id="seatAssignmentSection" style="display: none;" class="mt-4">
                        <h5 class="mb-3"><i class="bi bi-grid-3x3"></i> Seat Assignments</h5>
                        <div class="table-responsive">
                            <table id="seatAssignmentTable" class="table table-striped table-hover" style="width:100%">
                                <thead class="table-light">
                                    <tr>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Table Name</th>
                                        <th>Chair #</th>
                                    </tr>
                                </thead>
                                <tbody id="seatAssignmentTableBody"></tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Empty State -->
                    <div id="emptyState" class="text-center text-muted py-5">
                        <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                        <p class="mb-0">No Event Selected</p>
                        <small>Please select an event from the dropdown above to view the report</small>
                    </div>
                </div>
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
    .card {
        border: 1px solid #e9ecef;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
    }
    
    .table th {
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.85rem;
        letter-spacing: 0.5px;
    }
</style>

<script>
(function() {
    'use strict';
    
    let attendanceDataTable = null;
    let seatDataTable = null;
    let currentSelectedEvent = '';
    let currentReportData = null;

    document.addEventListener('DOMContentLoaded', function() {
        // Initialize Select2
        $('#eventSelect').select2({
            theme: 'bootstrap-5',
            placeholder: '-- Select Event --',
            allowClear: true,
            width: '100%'
        });

        // Handle event selection
        $('#eventSelect').on('select2:select', function(e) {
            currentSelectedEvent = e.params.data.id;
            $('#exportExcelBtn').prop('disabled', false);
            loadReportData(currentSelectedEvent);
        });

        $('#eventSelect').on('select2:clear', function() {
            currentSelectedEvent = '';
            $('#exportExcelBtn').prop('disabled', true);
            showEmptyState();
        });
    });

    function loadReportData(eventId) {
        // Show loading
        showLoading();

        $.ajax({
            url: '{{ route("attendance.report.fetch") }}',
            method: 'GET',
            data: { event: eventId },
            success: function(response) {
                currentReportData = response;
                displayReport(response);
            },
            error: function(xhr) {
                console.error('Error:', xhr);
                alert('Error loading report data');
                showEmptyState();
            }
        });
    }

    function displayReport(data) {
        // Hide empty state and show sections
        $('#emptyState').hide();
        $('#summarySection').show();
        $('#attendanceDetailsSection').show();
        
        // Show seat assignment section if there are seat assignments
        if (data.seat_assignments && data.seat_assignments.length > 0) {
            $('#seatAssignmentSection').show();
        } else {
            $('#seatAssignmentSection').hide();
        }

        // Display Event Info
        displayEventInfo(data.event);
        
        // Display Summary Data
        displaySummaryData(data.summary, data.scan_stats);
        
        // Display Attendance Table
        displayAttendanceTable(data.attendances);
        
        // Display Seat Assignment Table
        displaySeatAssignmentTable(data.seat_assignments);
    }

    function displayEventInfo(event) {
        const startDate = new Date(event.start);
        const endDate = new Date(event.end);
        
        $('#eventTitle').text(event.title);
        $('#eventDateTime').text(
            startDate.toLocaleDateString('en-US', { 
                weekday: 'long', 
                year: 'numeric', 
                month: 'long', 
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            }) + ' - ' + 
            endDate.toLocaleTimeString('en-US', { 
                hour: '2-digit', 
                minute: '2-digit' 
            })
        );
    }

    function displaySummaryData(summary, scanStats) {
        // Attendance Rate
        $('#attendanceRate').text(summary.attendance_rate + '%');
        
        // Attendance Status
        $('#totalRegistered').text(summary.total_registered);
        $('#presentCount').text(summary.present);
        $('#pendingCount').text(summary.pending);
        $('#absentCount').text(summary.absent);
        
        // Arrival Statistics
        $('#earlyCount').text(summary.early_arrivals);
        $('#onTimeCount').text(summary.on_time_arrivals);
        $('#lateCount').text(summary.late_arrivals);
        
        // Scan Statistics
        $('#usersScanned').text(scanStats.total_users_scanned);
        $('#totalScans').text(scanStats.total_scans);
        $('#avgScans').text(scanStats.avg_scans_per_user);
        $('#multipleScans').text(scanStats.users_with_multiple_scans);
    }

    function displayAttendanceTable(attendances) {
        if (attendanceDataTable) {
            attendanceDataTable.destroy();
            attendanceDataTable = null;
        }

        const tbody = $('#attendanceTableBody');
        tbody.empty();

        if (!attendances || attendances.length === 0) {
            tbody.html(`
                <tr>
                    <td colspan="6" class="text-center text-muted py-4">
                        <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                        <p class="mb-0">No Attendance Records</p>
                    </td>
                </tr>
            `);
            return;
        }

        attendances.forEach(att => {
            const statusBadge = att.status === 'Present' 
                ? '<span class="badge bg-success">Present</span>'
                : att.status === 'Absent'
                ? '<span class="badge bg-danger">Absent</span>'
                : '<span class="badge bg-warning text-dark">Pending</span>';

            const timeIn = att.time_in 
                ? new Date(att.time_in).toLocaleString()
                : '-------';

            let arrivalBadge = '<span class="badge bg-secondary">N/A</span>';
            if (att.arrival_status !== 'N/A') {
                if (att.arrival_status === 'Early') {
                    arrivalBadge = `<span class="badge bg-success">Early (${Math.abs(att.minutes_difference)} min)</span>`;
                } else if (att.arrival_status === 'Late') {
                    arrivalBadge = `<span class="badge bg-warning text-dark">Late (${att.minutes_difference} min)</span>`;
                } else {
                    arrivalBadge = '<span class="badge bg-primary">On Time</span>';
                }
            }

            tbody.append(`
                <tr>
                    <td>${att.user?.name || 'N/A'}</td>
                    <td>${att.user?.email || 'N/A'}</td>
                    <td>${statusBadge}</td>
                    <td>${timeIn}</td>
                    <td>${arrivalBadge}</td>
                    <td>${att.rep_by || '-------'}</td>
                </tr>
            `);
        });

        // Initialize DataTable
        attendanceDataTable = $('#attendanceTable').DataTable({
            pageLength: 25,
            order: [[3, 'desc']],
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Search..."
            }
        });
    }

    function displaySeatAssignmentTable(seatAssignments) {
        if (seatDataTable) {
            seatDataTable.destroy();
            seatDataTable = null;
        }

        const tbody = $('#seatAssignmentTableBody');
        tbody.empty();

        if (!seatAssignments || seatAssignments.length === 0) {
            tbody.html(`
                <tr>
                    <td colspan="4" class="text-center text-muted py-4">
                        <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                        <p class="mb-0">No Seat Assignments</p>
                    </td>
                </tr>
            `);
            return;
        }

        seatAssignments.forEach(seat => {
            tbody.append(`
                <tr>
                    <td>${seat.user_name || 'N/A'}</td>
                    <td>${seat.user_email || 'N/A'}</td>
                    <td>${seat.table_name || 'N/A'}</td>
                    <td><span class="badge bg-info">${seat.chair_number}</span></td>
                </tr>
            `);
        });

        // Initialize DataTable
        seatDataTable = $('#seatAssignmentTable').DataTable({
            pageLength: 25,
            order: [[2, 'asc'], [3, 'asc']],
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Search..."
            }
        });
    }

    function showLoading() {
        $('#emptyState').html(`
            <div class="spinner-border text-primary"></div>
            <p class="mt-2">Loading report...</p>
        `).show();
        $('#summarySection').hide();
        $('#attendanceDetailsSection').hide();
        $('#seatAssignmentSection').hide();
    }

    function showEmptyState() {
        $('#summarySection').hide();
        $('#attendanceDetailsSection').hide();
        $('#seatAssignmentSection').hide();
        $('#emptyState').html(`
            <i class="bi bi-inbox fs-1 d-block mb-2"></i>
            <p class="mb-0">No Event Selected</p>
            <small>Please select an event from the dropdown above to view the report</small>
        `).show();
        currentReportData = null;
    }

    window.exportToExcel = function() {
        if (!currentReportData) {
            alert('No data to export');
            return;
        }

        const wb = XLSX.utils.book_new();
        
        // Summary Sheet
        const summaryData = [
            ['ATTENDANCE REPORT'],
            ['Event:', currentReportData.event.title],
            ['Start:', new Date(currentReportData.event.start).toLocaleString()],
            ['End:', new Date(currentReportData.event.end).toLocaleString()],
            [''],
            ['ATTENDANCE SUMMARY'],
            ['Total Registered', currentReportData.summary.total_registered],
            ['Present', currentReportData.summary.present],
            ['Pending', currentReportData.summary.pending],
            ['Absent', currentReportData.summary.absent],
            ['Attendance Rate', currentReportData.summary.attendance_rate + '%'],
            ['Total Seats Assigned', currentReportData.summary.total_seats_assigned || 0],
            [''],
            ['ARRIVAL STATISTICS'],
            ['Early Arrivals', currentReportData.summary.early_arrivals],
            ['On Time', currentReportData.summary.on_time_arrivals],
            ['Late Arrivals', currentReportData.summary.late_arrivals],
            [''],
            ['SCAN STATISTICS'],
            ['Users Scanned', currentReportData.scan_stats.total_users_scanned],
            ['Total Scans', currentReportData.scan_stats.total_scans],
            ['Average Scans per User', currentReportData.scan_stats.avg_scans_per_user],
            ['Users with Multiple Scans', currentReportData.scan_stats.users_with_multiple_scans],
        ];
        const wsSummary = XLSX.utils.aoa_to_sheet(summaryData);
        XLSX.utils.book_append_sheet(wb, wsSummary, 'Summary');

        // Attendance Details Sheet
        const attendanceData = [
            ['Name', 'Email', 'Status', 'Time In', 'Arrival Status', 'Minutes Difference', 'Represented By']
        ];
        
        currentReportData.attendances.forEach(att => {
            attendanceData.push([
                att.user?.name || 'N/A',
                att.user?.email || 'N/A',
                att.status,
                att.time_in ? new Date(att.time_in).toLocaleString() : 'N/A',
                att.arrival_status,
                att.minutes_difference !== null ? att.minutes_difference : 'N/A',
                att.rep_by || 'N/A'
            ]);
        });
        
        const wsAttendance = XLSX.utils.aoa_to_sheet(attendanceData);
        XLSX.utils.book_append_sheet(wb, wsAttendance, 'Attendance Details');

        // Seat Assignments Sheet
        if (currentReportData.seat_assignments && currentReportData.seat_assignments.length > 0) {
            const seatData = [
                ['Name', 'Email', 'Table Name', 'Chair Number']
            ];
            
            currentReportData.seat_assignments.forEach(seat => {
                seatData.push([
                    seat.user_name || 'N/A',
                    seat.user_email || 'N/A',
                    seat.table_name || 'N/A',
                    seat.chair_number
                ]);
            });
            
            const wsSeats = XLSX.utils.aoa_to_sheet(seatData);
            XLSX.utils.book_append_sheet(wb, wsSeats, 'Seat Assignments');
        }

        // Multiple Scans Sheet (if any)
        if (currentReportData.multiple_scans && currentReportData.multiple_scans.length > 0) {
            const scansData = [
                ['Name', 'Email', 'Scan Count', 'First Scan', 'Last Scan']
            ];
            
            currentReportData.multiple_scans.forEach(scan => {
                scansData.push([
                    scan.user_name,
                    scan.user_email,
                    scan.scan_count,
                    new Date(scan.first_scan).toLocaleString(),
                    new Date(scan.last_scan).toLocaleString()
                ]);
            });
            
            const wsScans = XLSX.utils.aoa_to_sheet(scansData);
            XLSX.utils.book_append_sheet(wb, wsScans, 'Multiple Scans');
        }

        const fileName = `Attendance_Report_${currentReportData.event.title.replace(/[^a-z0-9]/gi, '_')}_${new Date().toISOString().split('T')[0]}.xlsx`;
        XLSX.writeFile(wb, fileName);
    };
})();
</script>
@endpush