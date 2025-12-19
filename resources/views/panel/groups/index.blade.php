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
                        <i class="bi bi-people"></i> Group Management
                    </h4>
                    <p class="text-muted mb-0">
                        <small><i class="bi bi-info-circle"></i> Manage and organize your groups</small>
                    </p>
                </div>
                <div class="card-body">
                    <!-- Breadcrumb -->
                    <div class="row mb-3">
                        <div class="col-sm-12">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="/dashboard">Home</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Manage Groups</li>
                                </ol>
                            </nav>
                        </div>
                    </div>

                    <!-- Action Buttons Row -->
                    <div class="row mb-3">
                        <div class="col-12 d-flex justify-content-end gap-2">
                            <button class="btn btn-dark btn-sm" onclick="exportToExcel()">
                                <i class="bi bi-file-earmark-excel"></i> Export to Excel
                            </button>
                            <a href="/admin/groups/create" class="btn btn-primary btn-sm">
                                <i class="bi bi-plus-circle"></i> Create Group
                            </a>
                        </div>
                    </div>

                    <!-- Groups Table -->
                    <div class="table-responsive">
                        <table id="groupsTable" class="table table-striped" style="width:100%">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Description</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($groups as $group)
                                    <tr>
                                        <td>{{ $group->name }}</td>
                                        <td>{{ $group->description }}</td>
                                        <td>
                                            @if($group->is_active)
                                                <span class="badge bg-success">Active</span>
                                            @else
                                                <span class="badge bg-secondary">Inactive</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="/admin/groups/{{ $group->id }}/edit" class="btn btn-warning btn-sm">
                                                <i class="fa fa-edit text-white"></i>
                                            </a>
                                            <button type="button" class="btn btn-danger btn-sm">
                                                <i class="fa fa-trash"></i>
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
        console.log('DOM loaded, initializing groups table');
        
        // Initialize DataTable
        try {
            dataTable = $('#groupsTable').DataTable({
                pageLength: 10,
                order: [[0, 'asc']], // Sort by name ascending
                language: {
                    search: "_INPUT_",
                    searchPlaceholder: "Search groups..."
                }
            });
            console.log('DataTable initialized');
        } catch (e) {
            console.error('Error initializing DataTable:', e);
        }
    });

    // Export to Excel function
    window.exportToExcel = function() {
        const data = [];
        
        // Headers
        const headers = ['Name', 'Description', 'Status'];
        data.push(headers);

        // Data rows
        $('#groupsTable tbody tr').each(function() {
            if ($(this).find('td').length > 1) {
                const row = [];
                $(this).find('td').slice(0, 3).each(function() {
                    // Clean up the text content
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
        XLSX.utils.book_append_sheet(wb, ws, 'Groups');
        
        // Generate filename with timestamp
        const now = new Date();
        const formattedDate = now.getFullYear() + '-' +
            String(now.getMonth() + 1).padStart(2, '0') + '-' +
            String(now.getDate()).padStart(2, '0') + '_' +
            String(now.getHours()).padStart(2, '0') + '-' +
            String(now.getMinutes()).padStart(2, '0') + '-' +
            String(now.getSeconds()).padStart(2, '0');
        
        const fileName = 'Groups_' + formattedDate + '.xlsx';
        
        // Save file
        XLSX.writeFile(wb, fileName);
    };
})();
</script>
@endpush