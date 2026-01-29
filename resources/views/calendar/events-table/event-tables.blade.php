<!-- Event Tables -->
@extends('layouts.sidebar')

@section('content')
<style>
    :root {
        --primary-color: #4f46e5;
        --success-color: #10b981;
        --danger-color: #ef4444;
        --warning-color: #f59e0b;
        --card-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
    }

    .table-card {
        border: 2px solid #e2e8f0;
        border-radius: 12px;
        padding: 1.5rem;
        background: white;
        box-shadow: var(--card-shadow);
        transition: all 0.3s ease;
        height: 100%;
    }

    .table-card:hover {
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        transform: translateY(-2px);
    }

    .table-card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 1rem;
        padding-bottom: 1rem;
        border-bottom: 2px solid #e2e8f0;
    }

    .table-card-title {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        font-size: 1.25rem;
        font-weight: 700;
        color: #1e293b;
    }

    .table-icon {
        width: 40px;
        height: 40px;
        background: linear-gradient(135deg, var(--primary-color), #6366f1);
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.25rem;
    }

    .chair-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
        gap: 0.75rem;
        margin-top: 1rem;
    }

    .chair-item {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.75rem;
        background: #f8fafc;
        border: 2px solid #e2e8f0;
        border-radius: 8px;
        transition: all 0.2s ease;
    }

    .chair-item:hover {
        background: #f1f5f9;
        border-color: var(--primary-color);
    }

    .chair-number {
        width: 32px;
        height: 32px;
        background: var(--primary-color);
        color: white;
        border-radius: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 0.875rem;
        flex-shrink: 0;
    }

    .chair-select-wrapper {
        flex: 1;
        min-width: 0;
    }

    /* View Container Styles */
    .tables-view-container {
        width: 100%;
    }

    .list-view,
    .diagram-view {
        display: none;
        width: 100%;
    }

    .list-view.active,
    .diagram-view.active {
        display: flex;
        flex-wrap: wrap;
        margin: -0.75rem;
    }

    .list-view .table-card-wrapper,
    .diagram-view .table-card-wrapper {
        padding: 0.75rem;
        width: 100%;
    }

    .diagram-container {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        min-height: 300px;
        padding: 1.5rem;
        width: 100%;
    }

    .diagram-table {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 1.5rem;
        margin: 1rem auto;
        max-width: 100%;
    }

    .diagram-chairs-grid {
        display: grid;
        grid-template-columns: repeat(5, 70px);
        gap: 0.75rem;
        justify-content: center;
    }

    .diagram-chair {
        width: 70px;
        height: 70px;
        border-radius: 8px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s ease;
        font-size: 0.7rem;
        font-weight: 600;
        text-align: center;
        padding: 0.4rem;
        word-wrap: break-word;
        border: 2px solid #cbd5e1;
        position: relative;
    }

    .diagram-chair:hover {
        transform: scale(1.08);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
        z-index: 10;
    }

    .diagram-chair.empty {
        background: #f1f5f9;
        color: #64748b;
        border-style: dashed;
    }

    .diagram-chair.assigned {
        color: white;
        border-color: rgba(0, 0, 0, 0.2);
    }

    .chair-label {
        font-size: 0.6rem;
        opacity: 0.9;
        margin-bottom: 0.2rem;
        font-weight: 700;
    }

    .chair-user-name {
        font-size: 0.65rem;
        line-height: 1.1;
        max-width: 100%;
        overflow: hidden;
        text-overflow: ellipsis;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
    }

    .diagram-table-center {
        width: 100px;
        height: 100px;
        background: linear-gradient(135deg, var(--primary-color), #6366f1);
        border-radius: 12px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 700;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }

    .diagram-table-name {
        font-size: 0.75rem;
        margin-top: 0.25rem;
        text-align: center;
        padding: 0 0.25rem;
    }

    .select2-container {
        width: 100% !important;
        z-index: 1050;
    }

    .modal {
        z-index: 1055;
    }

    .modal-backdrop {
        z-index: 1050;
    }

    .select2-container--bootstrap-5 .select2-selection {
        border: 1px solid #cbd5e1;
        border-radius: 6px;
        min-height: 36px;
    }

    .stats-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.5rem 1rem;
        border-radius: 8px;
        font-weight: 600;
        font-size: 0.875rem;
    }

    .stats-badge.assigned {
        background: #d1fae5;
        color: #065f46;
    }

    .stats-badge.available {
        background: #dbeafe;
        color: #1e40af;
    }

    .empty-state {
        text-align: center;
        padding: 4rem 2rem;
        color: #64748b;
    }

    .empty-state i {
        font-size: 4rem;
        margin-bottom: 1rem;
        opacity: 0.5;
    }

    .card-header {
        border-left: 4px solid var(--primary-color);
    }

    .btn-action {
        padding: 0.5rem 1rem;
        border-radius: 6px;
        font-weight: 600;
        transition: all 0.2s ease;
    }

    .table-actions {
        display: flex;
        gap: 0.5rem;
    }

    .view-toggle-btn {
        border: 2px solid #e2e8f0;
        background: white;
        padding: 0.5rem 1rem;
        border-radius: 6px;
        transition: all 0.2s ease;
        cursor: pointer;
        white-space: nowrap;
        font-size: 0.875rem;
    }

    .view-toggle-btn:hover {
        background: #f8fafc;
    }

    .view-toggle-btn.active {
        background: var(--primary-color);
        color: white;
        border-color: var(--primary-color);
    }

    .search-filter-container {
        display: flex;
        gap: 0.75rem;
        align-items: center;
        margin-bottom: 1.5rem;
        flex-wrap: wrap;
    }

    .search-wrapper {
        flex: 1;
        position: relative;
        min-width: 200px;
    }

    .search-wrapper input {
        padding-left: 2.5rem;
        width: 100%;
    }

    .search-icon {
        position: absolute;
        left: 0.75rem;
        top: 50%;
        transform: translateY(-50%);
        color: #64748b;
        pointer-events: none;
    }

    .view-toggle-group {
        display: flex;
        gap: 0.5rem;
        flex-shrink: 0;
    }

    /* Responsive Grid Layouts */
    @media (min-width: 1400px) {
        .list-view .table-card-wrapper,
        .diagram-view .table-card-wrapper {
            width: 33.333333%;
        }
    }

    @media (min-width: 992px) and (max-width: 1399px) {
        .list-view .table-card-wrapper,
        .diagram-view .table-card-wrapper {
            width: 50%;
        }
    }

    @media (min-width: 768px) and (max-width: 991px) {
        .list-view .table-card-wrapper,
        .diagram-view .table-card-wrapper {
            width: 100%;
        }

        .diagram-chair {
            width: 60px;
            height: 60px;
            font-size: 0.65rem;
        }

        .diagram-chairs-grid {
            grid-template-columns: repeat(5, 60px);
        }

        .diagram-table-center {
            width: 85px;
            height: 85px;
        }

        .diagram-table-name {
            font-size: 0.7rem;
        }

        .chair-grid {
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
        }
    }

    @media (max-width: 767px) {
        .list-view .table-card-wrapper,
        .diagram-view .table-card-wrapper {
            width: 100%;
        }

        .search-filter-container {
            flex-direction: column;
            align-items: stretch;
        }

        .search-wrapper {
            width: 100%;
        }

        .view-toggle-group {
            width: 100%;
        }

        .view-toggle-btn {
            flex: 1;
            justify-content: center;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .diagram-chair {
            width: 55px;
            height: 55px;
            font-size: 0.6rem;
        }

        .diagram-chairs-grid {
            grid-template-columns: repeat(4, 55px);
        }

        .diagram-table-center {
            width: 75px;
            height: 75px;
        }

        .diagram-table-center i {
            font-size: 1.5rem !important;
        }

        .diagram-table-name {
            font-size: 0.65rem;
        }

        .chair-label {
            font-size: 0.55rem;
        }

        .chair-user-name {
            font-size: 0.6rem;
        }

        .chair-grid {
            grid-template-columns: 1fr;
        }

        .table-card {
            padding: 1rem;
        }

        .diagram-container {
            padding: 1rem;
            min-height: 250px;
        }

        .diagram-table {
            gap: 1rem;
        }
    }

    @media (max-width: 480px) {
        .table-card-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 0.75rem;
        }

        .table-actions {
            width: 100%;
            justify-content: flex-end;
        }

        .stats-badge {
            font-size: 0.75rem;
            padding: 0.4rem 0.75rem;
        }

        .diagram-chair {
            width: 50px;
            height: 50px;
        }

        .diagram-chairs-grid {
            grid-template-columns: repeat(3, 50px);
        }

        .diagram-table-center {
            width: 65px;
            height: 65px;
        }
    }
</style>

<div class="container-fluid content-inner mt-n5 py-0">
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
                        <i class="bi bi-table"></i> Seat Management
                    </h4>
                    <p class="text-muted mb-0">
                        <small><i class="bi bi-info-circle"></i> Manage event tables and chair assignments</small>
                    </p>
                </div>
                <div class="card-body">
                    <!-- Event Selection and Actions -->
                    <div class="row mb-4">
                        <div class="col-lg-6 col-md-12 mb-3 mb-lg-0">
                            <label for="eventSelect" class="form-label">Select Event</label>
                            <select id="eventSelect" class="form-select" style="width: 100%;">
                                <option value="">-- Select Event --</option>
                                @foreach ($events as $event)
                                    <option value="{{ $event->id }}">{{ $event->title }} - {{ $event->start->format('M d, Y') }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-lg-6 col-md-12 d-flex align-items-end gap-2">
                            <button type="button" id="addTableBtn" class="btn btn-primary flex-fill" disabled>
                                <i class="bi bi-plus-circle"></i> Add Tables
                            </button>
                            <button type="button" id="refreshBtn" class="btn btn-secondary flex-fill" disabled>
                                <i class="bi bi-arrow-clockwise"></i> Refresh
                            </button>
                        </div>
                    </div>

                    <!-- Search and View Toggle -->
                    <div id="searchFilterContainer" class="search-filter-container" style="display: none;">
                        <div class="search-wrapper">
                            <i class="bi bi-search search-icon"></i>
                            <input type="text" id="tableSearch" class="form-control" placeholder="Search tables...">
                        </div>
                        <div class="view-toggle-group">
                            <button class="view-toggle-btn active" data-view="list">
                                <i class="bi bi-list-ul"></i> List
                            </button>
                            <button class="view-toggle-btn" data-view="diagram">
                                <i class="bi bi-diagram-3"></i> Diagram
                            </button>
                        </div>
                    </div>

                    <!-- Tables Display -->
                    <div id="tablesContainer">
                        <div class="empty-state">
                            <i class="bi bi-inbox"></i>
                            <h5>No Event Selected</h5>
                            <p>Please select an event to view and manage tables</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Tables Modal -->
<div class="modal fade" id="addTablesModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Tables</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="addTablesForm">
                    <div id="tablesInputContainer">
                        <div class="table-input-row mb-3">
                            <div class="row">
                                <div class="col-md-6">
                                    <label class="form-label">Table Name *</label>
                                    <input type="text" class="form-control table-name" placeholder="e.g., VIP Table" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Number of Seats *</label>
                                    <div class="input-group">
                                        <input type="number" class="form-control chair-count" placeholder="e.g., 6" min="1" max="50" required>
                                        <button type="button" class="btn btn-danger remove-table-btn" style="display: none;">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-md-12">
                                    <div class="form-check">
                                        <input class="form-check-input manual-assignment-check" type="checkbox" value="1">
                                        <label class="form-check-label">
                                            <i class="bi bi-hand-index"></i> Manual Assignment
                                            <small class="text-muted d-block">Check this if you want to manually assign seats (auto-assignment will skip this table)</small>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <button type="button" id="addAnotherTableBtn" class="btn btn-outline-primary">
                        <i class="bi bi-plus"></i> Add Another Table
                    </button>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" id="saveTablesBtn" class="btn btn-primary">Save Tables</button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Table Modal -->
<div class="modal fade" id="editTableModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Table</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="editTableForm">
                    <input type="hidden" id="edit_table_id">
                    <div class="mb-3">
                        <label class="form-label">Table Name *</label>
                        <input type="text" id="edit_table_name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Number of Seats *</label>
                        <input type="number" id="edit_chair_count" class="form-control" min="1" max="50" required>
                        <small class="form-text text-muted">Note: Reducing seats will remove assignments from the highest numbered seats</small>
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="edit_manual_assignment" value="1">
                            <label class="form-check-label" for="edit_manual_assignment">
                                <i class="bi bi-hand-index"></i> Manual Assignment
                                <small class="text-muted d-block">Check this if you want to manually assign seats (auto-assignment will skip this table)</small>
                            </label>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" id="updateTableBtn" class="btn btn-primary">Update Table</button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteTableModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Deletion</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this table? This will remove all chair assignments.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" id="confirmDeleteBtn" class="btn btn-danger">Delete</button>
            </div>
        </div>
    </div>
</div>

<!-- Assign Chair Modal (for diagram view) -->
<div class="modal fade" id="assignChairModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Assign Chair</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="assign_chair_id">
                <div class="mb-3">
                    <label class="form-label">Select Attendee</label>
                    <select id="assign_user_select" class="form-select" style="width: 100%;">
                        <option value="">-- Unassigned --</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" id="confirmAssignBtn" class="btn btn-primary">Assign</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('sidebar-scripts')
<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />

<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
(function() {
    'use strict';
    
    let currentEventId = null;
    let eventAttendees = [];
    let deleteTableId = null;
    let allTables = [];
    let currentView = 'list';
    let currentChairId = null;

    // Predefined colors for chairs
    const chairColors = [
        '#ef4444', '#f97316', '#f59e0b', '#eab308', '#84cc16',
        '#22c55e', '#10b981', '#14b8a6', '#06b6d4', '#0ea5e9',
        '#3b82f6', '#6366f1', '#8b5cf6', '#a855f7', '#d946ef',
        '#ec4899', '#f43f5e'
    ];

    $(document).ready(function() {
        setTimeout(function() {
            if (typeof $.fn.select2 === 'undefined') {
                console.error('Select2 not loaded after delay');
                alert('Select2 library failed to load. Please refresh the page.');
                return;
            }
            initializeEventSelect();
            setupEventHandlers();
        }, 100);
    });

    function initializeEventSelect() {
        try {
            $('#eventSelect').select2({
                theme: 'bootstrap-5',
                placeholder: '-- Select Event --',
                allowClear: true,
                width: '100%'
            });

            $('#eventSelect').on('change', function() {
                const selectedValue = $(this).val();
                
                if (selectedValue && selectedValue !== '') {
                    currentEventId = selectedValue;
                    $('#addTableBtn').prop('disabled', false);
                    $('#refreshBtn').prop('disabled', false);
                    $('#searchFilterContainer').show();
                    loadTables();
                } else {
                    currentEventId = null;
                    $('#addTableBtn').prop('disabled', true);
                    $('#refreshBtn').prop('disabled', true);
                    $('#searchFilterContainer').hide();
                    showEmptyState();
                }
            });
        } catch (error) {
            console.error('Error initializing event select:', error);
        }
    }

    function setupEventHandlers() {
        $('#addTableBtn').on('click', function() {
            if (!currentEventId) {
                alert('Please select an event first');
                return;
            }
            resetAddTablesForm();
            $('#addTablesModal').modal('show');
        });

        $('#refreshBtn').on('click', function() {
            if (currentEventId) {
                loadTables();
            }
        });

        $('#addAnotherTableBtn').on('click', addTableInput);
        $('#saveTablesBtn').on('click', saveTables);
        $('#updateTableBtn').on('click', updateTable);
        $('#confirmDeleteBtn').on('click', confirmDeleteTable);
        $('#confirmAssignBtn').on('click', confirmAssignChair);

        $(document).on('click', '.remove-table-btn', function() {
            $(this).closest('.table-input-row').remove();
            updateRemoveButtons();
        });

        // View toggle
        $('.view-toggle-btn').on('click', function() {
            const view = $(this).data('view');
            if (view !== currentView) {
                currentView = view;
                $('.view-toggle-btn').removeClass('active');
                $(this).addClass('active');
                displayTables(allTables);
            }
        });

        // Search functionality
        $('#tableSearch').on('input', function() {
            const searchTerm = $(this).val().toLowerCase();
            filterTables(searchTerm);
        });

        // Diagram chair click
        $(document).on('click', '.diagram-chair', function() {
            currentChairId = $(this).data('chair-id');
            const currentUserId = $(this).data('user-id');
            
            // Populate modal
            $('#assign_chair_id').val(currentChairId);
            $('#assign_user_select').html('<option value="">-- Unassigned --</option>');
            
            eventAttendees.forEach(function(attendee) {
                const selected = currentUserId === attendee.id ? 'selected' : '';
                $('#assign_user_select').append(
                    `<option value="${attendee.id}" ${selected}>${escapeHtml(attendee.name)}</option>`
                );
            });

            $('#assign_user_select').select2({
                theme: 'bootstrap-5',
                dropdownParent: $('#assignChairModal'),
                width: '100%',
                placeholder: '-- Unassigned --',
                allowClear: true
            });

            $('#assignChairModal').modal('show');
        });
    }

    function confirmAssignChair() {
        const chairId = $('#assign_chair_id').val();
        const userId = $('#assign_user_select').val() || null;
        
        assignChair(chairId, userId);
        $('#assignChairModal').modal('hide');
    }

    function filterTables(searchTerm) {
        if (!searchTerm) {
            $('.table-card-wrapper').show();
            return;
        }

        $('.table-card-wrapper').each(function() {
            const tableName = $(this).find('.table-card-title div:first').text().toLowerCase();
            if (tableName.includes(searchTerm)) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    }

function addTableInput() {
    const newRow = `
        <div class="table-input-row mb-3">
            <div class="row">
                <div class="col-md-6">
                    <label class="form-label">Table Name *</label>
                    <input type="text" class="form-control table-name" placeholder="e.g., Table 2" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Number of Seats *</label>
                    <div class="input-group">
                        <input type="number" class="form-control chair-count" placeholder="e.g., 6" min="1" max="50" required>
                        <button type="button" class="btn btn-danger remove-table-btn">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-md-12">
                    <div class="form-check">
                        <input class="form-check-input manual-assignment-check" type="checkbox" value="1">
                        <label class="form-check-label">
                            <i class="bi bi-hand-index"></i> Manual Assignment
                            <small class="text-muted d-block">Check this if you want to manually assign seats</small>
                        </label>
                    </div>
                </div>
            </div>
        </div>
    `;
    $('#tablesInputContainer').append(newRow);
    updateRemoveButtons();
}

    function updateRemoveButtons() {
        const rows = $('.table-input-row');
        rows.find('.remove-table-btn').toggle(rows.length > 1);
    }

    function resetAddTablesForm() {
        $('#tablesInputContainer').html(`
            <div class="table-input-row mb-3">
                <div class="row">
                    <div class="col-md-6">
                        <label class="form-label">Table Name *</label>
                        <input type="text" class="form-control table-name" placeholder="e.g., VIP Table" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Number of Seats *</label>
                        <div class="input-group">
                            <input type="number" class="form-control chair-count" placeholder="e.g., 6" min="1" max="50" required>
                            <button type="button" class="btn btn-danger remove-table-btn" style="display: none;">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-md-12">
                        <div class="form-check">
                            <input class="form-check-input manual-assignment-check" type="checkbox" value="1">
                            <label class="form-check-label">
                                <i class="bi bi-hand-index"></i> Manual Assignment
                                <small class="text-muted d-block">Check this if you want to manually assign seats</small>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        `);
    }

function saveTables() {
    const tables = [];
    let isValid = true;

    $('.table-input-row').each(function() {
        const tableName = $(this).find('.table-name').val().trim();
        const chairCount = $(this).find('.chair-count').val();
        const manualAssignment = $(this).find('.manual-assignment-check').is(':checked');

        if (!tableName || !chairCount || chairCount < 1) {
            isValid = false;
            return false;
        }

        tables.push({
            table_name: tableName,
            chair_count: parseInt(chairCount),
            manual_assignment: manualAssignment ? 1 : 0
        });
    });

    if (!isValid) {
        alert('Please fill in all fields correctly');
        return;
    }

    $.ajax({
        url: '{{ route("event.tables.store") }}',
        method: 'POST',
        data: {
            event_id: currentEventId,
            tables: tables,
            _token: '{{ csrf_token() }}'
        },
        success: function(response) {
            $('#addTablesModal').modal('hide');
            loadTables();
            showAlert('success', response.message || 'Tables created successfully');
        },
        error: function(xhr) {
            const message = xhr.responseJSON?.message || 'Error creating tables';
            showAlert('danger', message);
        }
    });
}

    function loadTables() {
        if (!currentEventId) return;

        $('#tablesContainer').html(`
            <div class="text-center py-5">
                <div class="spinner-border text-primary" role="status"></div>
                <p class="mt-2">Loading tables...</p>
            </div>
        `);

        // Load attendees first
        $.ajax({
            url: '{{ route("event.tables.attendees") }}',
            method: 'GET',
            data: { event_id: currentEventId },
            success: function(response) {
                eventAttendees = response.attendees || [];
                loadTablesData();
            },
            error: function(xhr) {
                eventAttendees = [];
                loadTablesData();
            }
        });
    }

    function loadTablesData() {
        $.ajax({
            url: '{{ route("event.tables.fetch") }}',
            method: 'GET',
            data: { event_id: currentEventId },
            success: function(response) {
                if (response.success) {
                    allTables = response.tables;
                    displayTables(response.tables);
                } else {
                    showAlert('danger', response.message || 'Error loading tables');
                    showEmptyState();
                }
            },
            error: function(xhr) {
                const message = xhr.responseJSON?.message || 'Error loading tables';
                showAlert('danger', message);
                showEmptyState();
            }
        });
    }

    function displayTables(tables) {
        if (!tables || tables.length === 0) {
            $('#tablesContainer').html(`
                <div class="empty-state">
                    <i class="bi bi-inbox"></i>
                    <h5>No Tables Found</h5>
                    <p>Click "Add Tables" to create tables for this event</p>
                </div>
            `);
            return;
        }

        if (currentView === 'list') {
            displayListView(tables);
        } else {
            displayDiagramView(tables);
        }
    }

    function displayListView(tables) {
    let html = '<div class="list-view active tables-view-container">';
    
    tables.forEach(function(table) {
        const assignedCount = table.chairs.filter(c => c.user_id).length;
        const availableCount = table.chair_count - assignedCount;
        const isManual = table.manual_assignment;
        
        html += `
            <div class="table-card-wrapper">
                <div class="table-card">
                    <div class="table-card-header">
                        <div class="table-card-title">
                            <div class="table-icon">
                                <i class="bi bi-table"></i>
                            </div>
                            <div>
                                <div>
                                    ${escapeHtml(table.table_name)}
                                    ${isManual ? '<span class="badge bg-warning ms-2"><i class="bi bi-hand-index"></i> Manual</span>' : '<span class="badge bg-success ms-2"><i class="bi bi-lightning"></i> Auto</span>'}
                                </div>
                                <small class="text-muted">${table.chair_count} seats</small>
                            </div>
                        </div>
                        <div class="table-actions">
                            <button class="btn btn-sm btn-warning edit-table-btn" data-table-id="${table.id}" 
                                data-table-name="${escapeHtml(table.table_name)}" 
                                data-chair-count="${table.chair_count}"
                                data-manual-assignment="${isManual ? 1 : 0}">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <button class="btn btn-sm btn-danger delete-table-btn" data-table-id="${table.id}">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </div>
                    <div class="mb-3">
                        <span class="stats-badge assigned">
                            <i class="bi bi-person-check"></i> ${assignedCount} Assigned
                        </span>
                        <span class="stats-badge available">
                            <i class="bi bi-person-plus"></i> ${availableCount} Available
                        </span>
                    </div>
                    <div class="chair-grid">
        `;

        table.chairs.forEach(function(chair) {
            const selectId = `chair_${chair.id}`;
            html += `
                <div class="chair-item">
                    <div class="chair-number">${chair.chair_number}</div>
                    <div class="chair-select-wrapper">
                        <select class="form-select form-select-sm chair-select" 
                            data-chair-id="${chair.id}" 
                            id="${selectId}">
                            <option value="">-- Unassigned --</option>
            `;
            
            eventAttendees.forEach(function(attendee) {
                const selected = chair.user_id === attendee.id ? 'selected' : '';
                html += `<option value="${attendee.id}" ${selected}>${escapeHtml(attendee.name)}</option>`;
            });

            html += `
                        </select>
                    </div>
                </div>
            `;
        });

        html += `
                    </div>
                </div>
            </div>
        `;
    });

    html += '</div>';
    
    $('#tablesContainer').html(html);
    initializeChairSelects();
    setupTableActions();
}

    function displayDiagramView(tables) {
        let html = '<div class="diagram-view active tables-view-container">';
        
        tables.forEach(function(table) {
            const assignedCount = table.chairs.filter(c => c.user_id).length;
            const availableCount = table.chair_count - assignedCount;
            
            html += `
                <div class="table-card-wrapper">
                    <div class="table-card">
                        <div class="table-card-header">
                            <div class="table-card-title">
                                <div class="table-icon">
                                    <i class="bi bi-diagram-3"></i>
                                </div>
                                <div>
                                    <div>${escapeHtml(table.table_name)}</div>
                                    <small class="text-muted">${table.chair_count} seats</small>
                                </div>
                            </div>
                            <div class="table-actions">
                                <button class="btn btn-sm btn-warning edit-table-btn" data-table-id="${table.id}" 
                                    data-table-name="${escapeHtml(table.table_name)}" 
                                    data-chair-count="${table.chair_count}">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button class="btn btn-sm btn-danger delete-table-btn" data-table-id="${table.id}">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </div>
                        <div class="mb-3">
                            <span class="stats-badge assigned">
                                <i class="bi bi-person-check"></i> ${assignedCount} Assigned
                            </span>
                            <span class="stats-badge available">
                                <i class="bi bi-person-plus"></i> ${availableCount} Available
                            </span>
                        </div>
                        <div class="diagram-container">
                            ${generateDiagramLayout(table)}
                        </div>
                    </div>
                </div>
            `;
        });

        html += '</div>';
        
        $('#tablesContainer').html(html);
        setupTableActions();
    }

    function generateDiagramLayout(table) {
        const chairs = table.chairs;
        const maxColsPerRow = 5;
        
        let html = '<div class="diagram-table">';
        
        // Table element at top (centered)
        html += `
            <div class="diagram-table-center">
                <i class="bi bi-table" style="font-size: 2rem;"></i>
                <div class="diagram-table-name">${escapeHtml(table.table_name)}</div>
            </div>
        `;
        
        // Chairs grid below table
        html += '<div class="diagram-chairs-grid">';
        
        chairs.forEach(function(chair, index) {
            const user = chair.user;
            const color = getChairColor(index);
            const isAssigned = user && user.id;
            
            html += `
                <div class="diagram-chair ${isAssigned ? 'assigned' : 'empty'}" 
                     data-chair-id="${chair.id}"
                     data-user-id="${user?.id || ''}"
                     style="background-color: ${isAssigned ? color : '#f1f5f9'};">
                    <div class="chair-label">C${chair.chair_number}</div>
                    <div class="chair-user-name">
                        ${isAssigned ? escapeHtml(user.name) : 'Empty'}
                    </div>
                </div>
            `;
        });
        
        html += '</div></div>';
        return html;
    }

    function getChairColor(index) {
        return chairColors[index % chairColors.length];
    }

    function initializeChairSelects() {
        $('.chair-select').each(function() {
            $(this).select2({
                theme: 'bootstrap-5',
                width: '100%',
                placeholder: '-- Unassigned --',
                allowClear: true
            });
        });

        $('.chair-select').on('select2:select select2:clear', function(e) {
            const chairId = $(this).data('chair-id');
            const userId = $(this).val() || null;
            assignChair(chairId, userId);
        });
    }

    function setupTableActions() {
    $('.edit-table-btn').on('click', function() {
        const tableId = $(this).data('table-id');
        const tableName = $(this).data('table-name');
        const chairCount = $(this).data('chair-count');
        const manualAssignment = $(this).data('manual-assignment') == 1;
        
        $('#edit_table_id').val(tableId);
        $('#edit_table_name').val(tableName);
        $('#edit_chair_count').val(chairCount);
        $('#edit_manual_assignment').prop('checked', manualAssignment);
        $('#editTableModal').modal('show');
    });

    $('.delete-table-btn').on('click', function() {
        deleteTableId = $(this).data('table-id');
        $('#deleteTableModal').modal('show');
    });
}

    function assignChair(chairId, userId) {
        $.ajax({
            url: '{{ route("event.tables.assign") }}',
            method: 'POST',
            data: {
                chair_id: chairId,
                user_id: userId,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    showAlert('success', response.message || 'Chair assignment updated');
                    loadTables();
                } else {
                    showAlert('danger', response.message || 'Error updating assignment');
                    loadTables();
                }
            },
            error: function(xhr) {
                const message = xhr.responseJSON?.message || 'Error updating assignment';
                showAlert('danger', message);
                loadTables();
            }
        });
    }

    function updateTable() {
    const tableId = $('#edit_table_id').val();
    const tableName = $('#edit_table_name').val().trim();
    const chairCount = $('#edit_chair_count').val();
    const manualAssignment = $('#edit_manual_assignment').is(':checked'); // This returns true/false

    if (!tableName || !chairCount || chairCount < 1) {
        alert('Please fill in all fields correctly');
        return;
    }

    $.ajax({
        url: `/event-tables/${tableId}`,
        method: 'PUT',
        data: {
            table_name: tableName,
            chair_count: parseInt(chairCount),
            manual_assignment: manualAssignment ? 1 : 0, // Convert to 1 or 0 for Laravel
            _token: '{{ csrf_token() }}'
        },
        success: function(response) {
            $('#editTableModal').modal('hide');
            loadTables();
            showAlert('success', response.message || 'Table updated successfully');
        },
        error: function(xhr) {
            const message = xhr.responseJSON?.message || 'Error updating table';
            showAlert('danger', message);
        }
    });
}


    function confirmDeleteTable() {
        if (!deleteTableId) return;

        $.ajax({
            url: `/event-tables/${deleteTableId}`,
            method: 'DELETE',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                $('#deleteTableModal').modal('hide');
                loadTables();
                showAlert('success', response.message || 'Table deleted successfully');
                deleteTableId = null;
            },
            error: function(xhr) {
                const message = xhr.responseJSON?.message || 'Error deleting table';
                showAlert('danger', message);
            }
        });
    }

    function showEmptyState() {
        $('#tablesContainer').html(`
            <div class="empty-state">
                <i class="bi bi-inbox"></i>
                <h5>No Event Selected</h5>
                <p>Please select an event to view and manage tables</p>
            </div>
        `);
    }

    function showAlert(type, message) {
        const alertHtml = `
            <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;
        $('.content-inner').prepend(alertHtml);
        
        setTimeout(function() {
            $('.alert').fadeOut('slow', function() {
                $(this).remove();
            });
        }, 5000);
    }

    function escapeHtml(text) {
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return text.replace(/[&<>"']/g, m => map[m]);
    }
})();
</script>
@endpush