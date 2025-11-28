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
                                        <li class="breadcrumb-item">Discount</li>
                                    </ol>
                                </div>
                                <div class="row">
                                    <div class="col-6 col-lg-6 p-1">
                                        <button class="btn button bg-dark text-white" onclick="exportToExcel()">Export</button>
                                    </div>
                                    <div class="col-6 col-lg-6 text-end p-1">
                                        <button class="btn button  text-white" style="background-color: rgb(5, 40, 132)" data-bs-toggle="modal" data-bs-target="#addDiscountModal">+ Create Discount</button>
                                    </div>
                                </div>
                                <div class="table-responsive">
                                    <table id="example" class="table table-striped" style="width:100%">
                                        <thead>
                                            <tr>
                                                <th scope="col">Code</th>
                                                <th scope="col">Name</th>
                                                <th scope="col">Type</th>
                                                <th scope="col">Amount</th>
                                                <th scope="col">Valid From</th>
                                                <th scope="col">Valid Until</th>
                                                <th scope="col">Status</th>
                                                <th scope="col">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($discounts as $discount)
                                            <tr>
                                                  <td>{{$discount->code}}</td>
                                                  <td>{{$discount->name}}</td>
                                                  <td>{{$discount->type}}</td>
                                                  <td>{{$discount->amount}}</td>
                                                  <td>{{$discount->valid_from}}</td>
                                                  <td>{{$discount->valid_until}}</td>
                                                  <td class="{{ $discount->is_active === 1 ? 'text-success' : 'text-danger' }}">
                                                    {{ $discount->is_active === 1 ? 'Active' : 'Inactive' }}
                                                </td>
                                                
                                                  <td>
                                                    <button class="btn btn-sm btn-warning" 
                                                            data-bs-toggle="modal" 
                                                            data-bs-target="#editDiscountModal" 
                                                            onclick="populateEditModal({{ json_encode($discount) }})">
                                                            <i class='fa fa-edit text-white'></i>
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
</div>
<!-- Modal for Creating Discount -->
<div class="modal fade" id="addDiscountModal" tabindex="-1" aria-labelledby="addDiscountModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addDiscountModalLabel">Create Discount</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="discountForm" action="{{ route('discounts.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="code" class="form-label d-flex align-items-center">
                            Code (Max Characters: 12)
                            <button type="button" class="btn btn-sm ms-2 p-0 m-0" style="color: blue;" onclick="generateCode()" title="Generate Code">
                                Generate Code <!-- Use Bootstrap icon for 'generate' -->
                            </button>
                        </label>
                        <input type="text" name="code" id="code" class="form-control" maxlength="12" required>
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
                        <input type="hidden" name="is_active" value="0"> <!-- Hidden input to submit 0 if unchecked -->
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="saveDiscountBtn" disabled>Save Discount</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- Modal for Editing Discount -->
<div class="modal fade" id="editDiscountModal" tabindex="-1" aria-labelledby="editDiscountModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editDiscountModalLabel">Edit Discount</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editDiscountForm" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="id" id="edit_id">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_code" class="form-label d-flex align-items-center">
                            Code (Max Characters: 12)
                        </label>
                        <input type="text" name="code" id="edit_code" class="form-control" maxlength="12" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_name" class="form-label">Name</label>
                        <input type="text" name="name" id="edit_name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_type" class="form-label">Type</label>
                        <select name="type" id="edit_type" class="form-select" required>
                            <option value="">Select Type</option>
                            <option value="percentage">Percentage</option>
                            <option value="fixed">Fixed Amount</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="edit_amount" class="form-label">Amount</label>
                        <input type="number" name="amount" id="edit_amount" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_valid_from" class="form-label">Valid From</label>
                        <input type="date" name="valid_from" id="edit_valid_from" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_valid_until" class="form-label">Valid Until</label>
                        <input type="date" name="valid_until" id="edit_valid_until" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_is_active" class="form-label">Status</label>
                        <select name="is_active" id="edit_is_active" class="form-select" required>
                            <option value="">Select Status</option>
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Discount</button>
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
    function populateEditModal(discount) {
        document.getElementById("edit_id").value = discount.id;
        document.getElementById("edit_code").value = discount.code;
        document.getElementById("edit_name").value = discount.name;
        document.getElementById("edit_type").value = discount.type;
        document.getElementById("edit_amount").value = discount.amount;
        document.getElementById("edit_valid_from").value = discount.valid_from;
        document.getElementById("edit_valid_until").value = discount.valid_until;
        document.getElementById("edit_is_active").value = discount.is_active;

        const formAction = "{{ route('discounts.update', ':id') }}".replace(':id', discount.id);
        document.getElementById("editDiscountForm").action = formAction;
    }


    function generateCode() {
        const characters = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        let code = "";
        for (let i = 0; i < 12; i++) {
            code += characters.charAt(Math.floor(Math.random() * characters.length));
        }
        document.getElementById("code").value = code;
    }

    // Disable the save button until all required fields are filled
    document.addEventListener("DOMContentLoaded", function() {
        const discountForm = document.getElementById("discountForm");
        const saveDiscountBtn = document.getElementById("saveDiscountBtn");

        discountForm.addEventListener("input", function() {
            let isFormComplete = true;
            discountForm.querySelectorAll("input[required], select[required]").forEach((input) => {
                if (!input.value.trim()) {
                    isFormComplete = false;
                }
            });
            saveDiscountBtn.disabled = !isFormComplete;
        });
    });
    document.addEventListener("DOMContentLoaded", function() {
        const discountForm = document.getElementById("discountForm");
        const saveDiscountBtn = document.getElementById("saveDiscountBtn");

        discountForm.addEventListener("input", function() {
            let isFormComplete = true;
            // Check if each required input has a value
            discountForm.querySelectorAll("input[required], select[required]").forEach((input) => {
                if (!input.value.trim()) {
                    isFormComplete = false;
                }
            });
            // Enable or disable the button based on form completion
            saveDiscountBtn.disabled = !isFormComplete;
        });
    });
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
