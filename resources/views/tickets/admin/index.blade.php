<!-- Guest Pass -->
@extends('layouts.sidebar')

@section('content')
<div class="container-fluid content-inner mt-n5 py-0">
    <!-- Success/Error Alerts -->
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <svg class="bi flex-shrink-0 me-2" width="24" height="24" role="img" aria-label="Success:">
                <use xlink:href="#check-circle-fill" />
            </svg>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <svg class="bi flex-shrink-0 me-2" width="24" height="24" role="img" aria-label="Danger:">
                <use xlink:href="#exclamation-triangle-fill" />
            </svg>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-lg-12">
            <div class="card rounded">
                <div class="card-header">
                    <h4 class="card-title">
                        <i class="bi bi-ticket-perforated"></i> Guest Pass Management
                    </h4>
                    <p class="text-muted mb-0">
                        <small><i class="bi bi-info-circle"></i> Manage and track guest passes</small>
                    </p>
                </div>
                <div class="card-body">
                    <!-- Breadcrumb -->
                    <div class="row mb-3">
                        <div class="col-sm-12">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="/dashboard">Home</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Guest Pass</li>
                                </ol>
                            </nav>
                        </div>
                    </div>

                    <!-- Action Buttons Row -->
                    <div class="row mb-3">
                        <div class="col-12 d-flex justify-content-end gap-2">
                            <button class="btn btn-success btn-sm" onclick="exportToExcel()">
                                <i class="bi bi-file-earmark-excel"></i> Export Excel
                            </button>
                            <button class="btn btn-info btn-sm text-white" data-bs-toggle="modal" data-bs-target="#bulkExportModal">
                                <i class="bi bi-file-earmark-pdf"></i> Bulk Export Pass
                            </button>
                            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#createTicketModal">
                                <i class="bi bi-plus-circle"></i> Create Pass
                            </button>
                            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#generatePassModal">
                                <i class="bi bi-gear"></i> Generate Pass
                            </button>
                        </div>
                    </div>

                    <!-- Guest Pass Table -->
                    <div class="table-responsive">
                        <table id="guestPassTable" class="table table-striped" style="width:100%">
                            <thead>
                                <tr>
                                    <th>Pass No.</th>
                                    <th>Event</th>
                                    <th>Ordered By</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($tickets as $ticket)
                                    <tr>
                                        <td>{{ $ticket->ticket_no }}</td>
                                        <td>{{ optional($ticket->ticket?->event)->title ?? 'No Event' }}</td>
                                        <td>
                                            {{ $ticket->invoice->first_name ?? $ticket->first_name }} 
                                            {{ $ticket->invoice->last_name ?? $ticket->last_name }}
                                        </td>
                                        <td>
                                            @if($ticket->is_scanned === 1)
                                                <span class="badge bg-success">Scanned</span>
                                            @else
                                                <span class="badge bg-warning text-dark">Not Scanned</span>
                                            @endif
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-primary btn-sm" onclick="exportPass('{{ $ticket->ticket_no }}')">
                                                <i class="fa fa-download"></i>
                                            </button>
                                            <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#editTicketModal" 
                                                onclick="openEditModal({{ $ticket->id }}, '{{ $ticket->first_name }}', '{{ $ticket->last_name }}', '{{ $ticket->is_scanned }}', {{ $ticket->ticket_id }})">
                                                <i class='fa fa-edit'></i>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Generate Pass Modal -->
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
                            @foreach ($ticket_list as $ticket_item)
                                <option value="{{ $ticket_item->id }}">{{ $ticket_item->event->title }} - {{ $ticket_item->name }}</option>
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

<!-- Bulk Export Modal -->
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
                        @foreach ($ticket_list as $ticket_item)
                            <option value="{{ $ticket_item->id }}">{{ $ticket_item->event->title }} - {{ $ticket_item->name }}</option>
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

<!-- Create Ticket Modal -->
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
                            @foreach ($ticket_list as $ticket_item)
                                <option value="{{ $ticket_item->id }}">{{ $ticket_item->event->title }} - {{ $ticket_item->name }}</option>
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

<!-- Edit Ticket Modal -->
<div class="modal fade" id="editTicketModal" tabindex="-1" aria-labelledby="editTicketModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editTicketModalLabel">Edit Ticket</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editTicketForm" method="POST" action="">
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
                            @foreach ($ticket_list as $ticket_item)
                                <option value="{{ $ticket_item->id }}">{{ $ticket_item->event->title }} - {{ $ticket_item->name }}</option>
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

<!-- SVG Icons -->
<svg xmlns="http://www.w3.org/2000/svg" style="display: none;">
    <symbol id="check-circle-fill" fill="currentColor" viewBox="0 0 16 16">
        <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z" />
    </symbol>
    <symbol id="exclamation-triangle-fill" fill="currentColor" viewBox="0 0 16 16">
        <path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z" />
    </symbol>
</svg>
@endsection

@push('sidebar-scripts')
<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />

<!-- DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/2.0.8/css/dataTables.bootstrap5.css">

<!-- Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<!-- DataTables JS -->
<script src="https://cdn.datatables.net/2.0.8/js/dataTables.js"></script>
<script src="https://cdn.datatables.net/2.0.8/js/dataTables.bootstrap5.js"></script>

<!-- XLSX for Excel Export -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.0/xlsx.full.min.js"></script>

<style>
    .card-header {
        border-left: 4px solid #0d6efd;
    }
    .btn {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }
    .breadcrumb {
        background-color: transparent;
        padding: 0;
        margin-bottom: 0;
    }
</style>

<script>
(function() {
    'use strict';
    
    let dataTable = null;

    // Wait for DOM to be fully loaded
    document.addEventListener('DOMContentLoaded', function() {
        console.log('DOM loaded, initializing guest pass table');
        
        // Initialize DataTable
        try {
            dataTable = $('#guestPassTable').DataTable({
                pageLength: 10,
                order: [[0, 'desc']], // Sort by Pass No. descending
                language: {
                    search: "_INPUT_",
                    searchPlaceholder: "Search guest passes..."
                }
            });
            console.log('DataTable initialized');
        } catch (e) {
            console.error('Error initializing DataTable:', e);
        }
    });

    // Open Edit Modal function
    window.openEditModal = function(ticketId, firstName, lastName, status, selectedTicketId) {
        document.getElementById('ticket_id').value = ticketId;
        document.getElementById('edit_first_name').value = firstName;
        document.getElementById('edit_last_name').value = lastName;
        document.getElementById('edit_ticket').value = selectedTicketId;
        document.getElementById('edit_status').value = status;
        
        // Set the form action dynamically
        document.getElementById('editTicketForm').action = `/guest-tickets/${ticketId}`;
    };

    // Export Pass function
    window.exportPass = function(ticketId) {
        fetch(`/guest-tickets/export-pass/${ticketId}`)
            .then(response => response.blob())
            .then(blob => {
                const url = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = `${ticketId}.pdf`;
                document.body.appendChild(a);
                a.click();
                document.body.removeChild(a);
                window.URL.revokeObjectURL(url);
            })
            .catch(error => console.error('Error:', error));
    };

    // Export Pass Bulk function
    window.exportPassBulk = function() {
        const ticketId = document.getElementById('ticketEventBulk').value;
        const currentDateTime = new Date().toISOString().replace(/[:.]/g, '-');
        
        fetch(`/guest-tickets/export-pass-bulk/${ticketId}`)
            .then(response => response.blob())
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
    };

    // Export to Excel function
    window.exportToExcel = function() {
        const data = [];
        
        // Headers
        const headers = ['Pass No.', 'Event', 'Ordered By', 'Status'];
        data.push(headers);

        // Data rows
        $('#guestPassTable tbody tr').each(function() {
            if ($(this).find('td').length > 1) {
                const row = [];
                $(this).find('td').slice(0, 4).each(function() {
                    let text = $(this).text().trim();
                    row.push(text);
                });
                data.push(row);
            }
        });

        // Create workbook
        const wb = XLSX.utils.book_new();
        const ws = XLSX.utils.aoa_to_sheet(data);
        
        // Add worksheet to workbook
        XLSX.utils.book_append_sheet(wb, ws, 'Guest Passes');
        
        // Generate filename with timestamp
        const now = new Date();
        const formattedDate = now.getFullYear() + '-' +
            String(now.getMonth() + 1).padStart(2, '0') + '-' +
            String(now.getDate()).padStart(2, '0') + '_' +
            String(now.getHours()).padStart(2, '0') + '-' +
            String(now.getMinutes()).padStart(2, '0') + '-' +
            String(now.getSeconds()).padStart(2, '0');
        
        const fileName = 'GuestPasses_' + formattedDate + '.xlsx';
        
        // Save file
        XLSX.writeFile(wb, fileName);
    };
})();
</script>
@endpush