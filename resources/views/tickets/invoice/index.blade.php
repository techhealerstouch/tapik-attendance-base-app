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
      <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
    </symbol>
    <symbol id="info-fill" fill="currentColor" viewBox="0 0 16 16">
      <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z"/>
    </symbol>
    <symbol id="exclamation-triangle-fill" fill="currentColor" viewBox="0 0 16 16">
      <path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"/>
    </symbol>
  </svg>
<div class="container-fluid content-inner mt-n5 py-0">
    @if(session('success'))
        <div class="alert alert-success d-flex align-items-center justify-content-between" role="alert">
            <div>
                <svg class="bi flex-shrink-0 me-2" width="24" height="24" role="img" aria-label="Success:">
                    <use xlink:href="#check-circle-fill"/>
                </svg>
                {{ session('success') }}
            </div>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    <!-- Error Message -->
    @if(session('error'))
        <div class="alert alert-danger d-flex align-items-center justify-content-between" role="alert">
            <div>
                <svg class="bi flex-shrink-0 me-2" width="24" height="24" role="img" aria-label="Danger:"><use xlink:href="#exclamation-triangle-fill"/></svg>
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
                                        <li class="breadcrumb-item">Invoice</li>
                                    </ol>
                                </div>
                                <div class="row">
                                    <div class="col-6 col-lg-6 p-1">
                                        <button class="btn button bg-dark text-white" onclick="exportToExcel()">Export</button>
                                    </div>
                                    <div class="col-6 col-lg-6 text-end p-1">
                                        <button class="btn button  text-white" style="background-color: rgb(5, 40, 132)" data-bs-toggle="modal" data-bs-target="#addnew">+ Create Order</button>
                                    </div>
                                </div>
                                <div class="table-responsive">
                                    <table id="example" class="table table-striped" style="width:100%">
                                        <thead>
                                            <tr>
                                                <th scope="col">Name</th>
                                                <th scope="col">Pass</th>
                                                <th scope="col">Event</th>
                                                <th scope="col">Price</th>
                                                <th scope="col">Status</th>
                                                <th scope="col">Paid Amount</th>
                                                <th scope="col">Payment Method</th>
                                                <th scope="col">Order #</th>
                                                <th scope="col">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($invoices as $invoice)
                                                <tr>
                                                    <td>{{ $invoice->user->name ?? $invoice->first_name . ' ' . $invoice->last_name}}</td>
                                                    <td>{{ $invoice->ticket->name }}</td>
                                                    <td>{{ $invoice->ticket->event->title }}</td>
                                                    <td>{{ $invoice->amount}}</td>
                                                    <td>
                                                        <span class="badge 
                                                            @if ($invoice->status == 'PAID') bg-success
                                                            @elseif ($invoice->status == 'PENDING') bg-warning
                                                            @elseif ($invoice->status == 'EXPIRED') bg-danger
                                                            @endif">
                                                            {{ $invoice->status }}
                                                        </span>
                                                    </td>
                                                    <td>{{ $invoice->paid_amount ?? 'Unpaid'}}</td>
                                                    <td>{{ $invoice->payment_method }}</td>
                                                    <td>{{ $invoice->invoice_no }}</td>
                                                    <td>
                                                        @if($invoice->payment_method === "CASH" && $invoice->status !== "PAID")
                                                            <button type="button"
                                                                class="btn btn-success btn-sm edit-invoice"
                                                                data-id="{{ $invoice->id }}" data-bs-toggle="modal"
                                                                data-bs-target="#editInvoiceModal">
                                                                <i class="fa fa-money-bill"></i>
                                                            </button>
                                                        @elseif($invoice->payment_method === "BANK_TRANSFER" && $invoice->status !== "PAID")
                                                            <button type="button"
                                                                class="btn btn-success btn-sm edit-invoice"
                                                                data-id="{{ $invoice->id }}" data-bs-toggle="modal"
                                                                data-bs-target="#payBankTransferModal">
                                                                <i class="fa fa-money-bill"></i>
                                                            </button>
                                                        @endif
                                                        <a href="/invoice/view/{{ $invoice->id }}"
                                                            class="btn btn-primary btn-sm">
                                                            <i class="fa fa-eye"></i>
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
<div class="modal fade" id="addnew">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
            <h5 class="modal-title"><b>Manual Invoice</b></h5>
              
            </div>
            <div class="modal-body text-left">
                <form class="form-horizontal" method="POST" action="{{ route('invoice.store') }}">
                    @csrf
                    <div class="form-group">
                        <label for="name" class="col-sm-6 control-label">Select User</label>
                        <div class="bootstrap-timepicker">
                            <select class="form-control select-members" name="userId" style="width: 100% !important">
                                <option value="select-all">Select User</option>
                                @foreach ($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="name" class="col-sm-6 control-label">Select Pass</label>
                        <div class="bootstrap-timepicker">
                            <select class="form-control select-members" name="ticketId" style="width: 100% !important">
                                <option value="select-all">Select Pass</option>
                                @foreach ($tickets as $ticket)
                                    <option value="{{ $ticket->id }}">{{ $ticket->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="paymentOption" class="col-sm-6 control-label">Select Payment Type</label>
                        <div class="col-sm-12">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="paymentOption" id="cashOption" value="cash" checked>
                                <label class="form-check-label" for="cashOption">
                                    Cash
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="paymentOption" id="xenditOption" value="xendit">
                                <label class="form-check-label" for="xenditOption">
                                    Xendit
                                </label>
                            </div>
                        </div>
                    </div>
                    
                    {{-- <div class="form-group">
                        <label for="name" class="col-sm-6 control-label">Select Users/Members</label>
                        <div class="bootstrap-timepicker">
                            <select class="form-control select-members" name="userId[]" multiple="multiple" style="width: 100% !important">
                                <option value="select-all">Select All</option>
                            </select>
                        </div>
                    </div> --}}

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger btn-flat pull-left" data-bs-dismiss="modal"><i class="fa fa-close"></i> Close</button>
                <button type="submit" class="btn btn-primary btn-flat"><i class="fa fa-save"></i> Save</button>            
                </form>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="editInvoiceModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><b>Edit Invoice</b></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
            <form class="form-horizontal" method="POST" action="{{ route('invoice.update', $invoice->id ?? '') }}">
                @csrf
                @method('PUT') <!-- Use PUT if you are updating an existing record -->
                <div class="form-group">
                    <label for="paid_amount" class="col-sm-6 control-label">Amount Paid (₱)</label>
                    <div class="bootstrap-timepicker">
                        <input type="number" placeholder="₱0.00" class="form-control timepicker" id="paid_amount" name="paid_amount">
                    </div>
                </div>
                <div class="form-group">
                    <label for="status" class="col-sm-6 control-label">Status</label>
                    <div class="bootstrap-timepicker">
                        <select class="form-control select-members1" id="status" name="status" style="width: 100% !important">
                            <option value="PAID">Paid</option>
                            <option value="PENDING">Pending</option>
                            <option value="EXPIRED">Expired</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger btn-flat pull-left" data-bs-dismiss="modal"><i class="fa fa-close"></i> Close</button>
                    <button type="submit" class="btn btn-primary btn-flat"><i class="fa fa-save"></i> Save</button>
                </div>
            </form>
        </div>
        </div>
    </div>
</div>


<div class="modal fade" id="payBankTransferModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><b>Bank Transfer Invoice</b></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
            <form class="form-horizontal" method="POST" action="{{ route('invoice.update', $invoice->id ?? '') }}">
                @csrf
                @method('PUT') <!-- Use PUT if you are updating an existing record -->
                <div class="form-group">
                    <label for="paid_amount" class="col-sm-6 control-label">Amount Paid (₱)</label>
                    <div class="bootstrap-timepicker">
                        <input type="number" placeholder="₱0.00" class="form-control timepicker" id="paid_amount" name="paid_amount">
                    </div>
                </div>
                <div class="form-group">
                    <label for="status" class="col-sm-6 control-label">Status</label>
                    <div class="bootstrap-timepicker">
                        <select class="form-control" id="status" name="status" style="width: 100% !important">
                            <option value="PAID">Paid</option>
                            <option value="PENDING">Pending</option>
                            <option value="EXPIRED">Expired</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger btn-flat pull-left" data-bs-dismiss="modal"><i class="fa fa-close"></i> Close</button>
                    <button type="submit" class="btn btn-primary btn-flat"><i class="fa fa-save"></i> Save</button>
                </div>
            </form>
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
       // Initialize Select2
    const $select = $('.select-members').select2({
        dropdownParent: $('#addnew'),
        placeholder: "Select User to add subscription",
        allowClear: true
    });
    // "Select All" functionality
    $select.on('select2:select', function(e) {
        if (e.params.data.id === 'select-all') {
            // Select all options
            $select.val($select.find('option:not([value="select-all"])').map(function() {
                return $(this).val();
            }).get()).trigger('change');
        }
    });

    $select.on('select2:unselect', function(e) {
        if (e.params.data.id === 'select-all') {
            // Deselect all options
            $select.val(null).trigger('change');
        }
    });

    const $select1 = $('.select-members1').select2({
        dropdownParent: $('#editInvoiceModal'),
        placeholder: "Select User to add subscription",
        allowClear: true
    });
    // "Select All" functionality
    $select1.on('select2:select', function(e) {
        if (e.params.data.id === 'select-all') {
            // Select all options
            $select1.val($select1.find('option:not([value="select-all"])').map(function() {
                return $(this).val();
            }).get()).trigger('change');
        }
    });
    document.querySelectorAll('.edit-invoice').forEach(button => {
        button.addEventListener('click', function() {
            var invoiceId = this.getAttribute('data-id');
            var payBankTransferForm = document.querySelector('#payBankTransferModal form');
            var editInvoiceForm = document.querySelector('#editInvoiceModal form');
            
            // Set the form action dynamically for each modal
            if (payBankTransferForm) {
                payBankTransferForm.action = '/invoice/' + invoiceId;
            }
            if (editInvoiceForm) {
                editInvoiceForm.action = '/invoice/' + invoiceId;
            }
        });
    });
    $select1.on('select2:unselect', function(e) {
        if (e.params.data.id === 'select-all') {
            // Deselect all options
            $select1.val(null).trigger('change');
        }
    });

        $(document).on('click', '.edit-invoice', function() {
                var invoice_id = $(this).data('id');

                $.ajax({
                    url: '/invoice/show/' + invoice_id,
                    method: 'GET',
                    success: function(data) {
                        if (data.error) {
                            alert(data.error);
                        } else {
                            // Update the form fields in the modal with the received data
                            ///$('#editInvoiceModal #status').val(data.status);
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
                $('#deleteScheduleForm').attr('action', '/attendance/attendance_logs/' + scheduleIdToDelete);
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
