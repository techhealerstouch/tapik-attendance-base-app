<?php use App\Models\User; ?>

@extends('layouts.sidebar')

@section('content')
    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>

    <div class="container-fluid content-inner mt-n5 py-0">
        <div class="row">
            <div class="col-lg-12">
                <div class="card rounded">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-12">
                                <section class="text-gray-400">
                                    <h2 class="mb-4 card-header">
                                        <i class="bi bi-person"> {{ __('messages.Manage Users') }}</i>
                                    </h2>
                                    <div class="card-body p-0 p-md-3">
                                        <div id="select-active" class="d-none">
                                            <h5 class="mb-2">{{ __('messages.Select Action') }}:</h5>
                                            <button class="mb-3 btn btn-danger rounded-pill btn-sm">
                                                <span class="btn-inner">
                                                    <svg class="icon-16" width="16" viewBox="0 0 24 24" fill="none"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                                            d="M20.2871 5.24297C20.6761 5.24297 21 5.56596 21 5.97696V6.35696C21 6.75795 20.6761 7.09095 20.2871 7.09095H3.71385C3.32386 7.09095 3 6.75795 3 6.35696V5.97696C3 5.56596 3.32386 5.24297 3.71385 5.24297H6.62957C7.22185 5.24297 7.7373 4.82197 7.87054 4.22798L8.02323 3.54598C8.26054 2.61699 9.0415 2 9.93527 2H14.0647C14.9488 2 15.7385 2.61699 15.967 3.49699L16.1304 4.22698C16.2627 4.82197 16.7781 5.24297 17.3714 5.24297H20.2871ZM18.8058 19.134C19.1102 16.2971 19.6432 9.55712 19.6432 9.48913C19.6626 9.28313 19.5955 9.08813 19.4623 8.93113C19.3193 8.78413 19.1384 8.69713 18.9391 8.69713H5.06852C4.86818 8.69713 4.67756 8.78413 4.54529 8.93113C4.41108 9.08813 4.34494 9.28313 4.35467 9.48913C4.35646 9.50162 4.37558 9.73903 4.40755 10.1359C4.54958 11.8992 4.94517 16.8102 5.20079 19.134C5.38168 20.846 6.50498 21.922 8.13206 21.961C9.38763 21.99 10.6811 22 12.0038 22C13.2496 22 14.5149 21.99 15.8094 21.961C17.4929 21.932 18.6152 20.875 18.8058 19.134Z"
                                                            fill="currentColor"></path>
                                                    </svg>
                                                </span>
                                                Delete
                                            </button>
                                        </div>

                                        <div class="d-flex justify-content-end">
                                            <button type="button" class="btn btn-primary mx-2" data-bs-toggle="modal" data-bs-target="#createUsersModal">
                                                <i class="bi bi-person-plus"></i> Add New Users
                                            </button>
                                            <button type="button" class="btn btn-primary mx-2" data-bs-toggle="modal" data-bs-target="#importUsersModal">
                                                <i class="bi bi-cloud-upload"></i> Import Users
                                            </button>
                                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exportUsersModal">
                                                <i class="bi bi-cloud-download"></i> Export
                                            </button>
                                        </div>

                                        <!-- Loading Modal -->
                                        <div class="modal fade" id="loadingModal" tabindex="-1" aria-labelledby="loadingModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content">
                                                    <div class="modal-body text-center">
                                                        <div id="loadingSpinner" class="spinner-border text-primary" role="status">
                                                            <span class="visually-hidden">Loading...</span>
                                                        </div>
                                                        <div id="loadingIcon" class="d-none">
                                                            <i class="bi fs-1" id="statusIcon"></i>
                                                        </div>
                                                        <p id="loadingText" class="mt-3">Processing your request, please wait...</p>
                                                        <button type="button" class="btn btn-primary mt-3 d-none" id="closeLoadingBtn" data-bs-dismiss="modal">Close</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Export Users Modal -->
                                        <div class="modal fade" id="exportUsersModal" tabindex="-1" aria-labelledby="exportUsersModalLabel" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="exportUsersModalLabel">
                                                            <i class="bi bi-cloud-download"></i> Export Options
                                                        </h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="d-grid gap-3">
                                                            <button type="button" class="btn btn-primary btn-lg" id="exportQRCodes">
                                                                <i class="bi bi-qr-code"></i> Export QR Codes
                                                                <br>
                                                                <small class="d-block mt-2 text-white-50">Download Excel file with QR codes for user profiles</small>
                                                            </button>
                                                            
                                                            <button type="button" class="btn btn-success btn-lg" id="exportUserCredentials">
                                                                <i class="bi bi-file-earmark-spreadsheet"></i> Export User Credentials
                                                                <br>
                                                                <small class="d-block mt-2 text-white-50">Download Excel file with user credentials (Name, Email, Password, URL)</small>
                                                            </button>
                                                        </div>
                                                        
                                                        <div class="alert alert-info mt-3" role="alert">
                                                            <i class="bi bi-info-circle"></i> 
                                                            <strong>Note:</strong> Both exports will generate Excel (.xlsx) files. Please wait for the download to complete.
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <livewire:user-table />

                                        <!-- Create Users Modal -->
                                        <div class="modal fade" id="createUsersModal" tabindex="-1"
                                            aria-labelledby="createUsersModalLabel" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="createUsersModalLabel">
                                                            <i class="bi bi-person-plus-fill"></i> Create New Users
                                                        </h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                            aria-label="Close"></button>
                                                    </div>
                                                    <form id="createUsersForm" action="{{ route('createMultipleUsers') }}" method="POST">
                                                        @csrf
                                                        <div class="modal-body">
                                                            <div class="mb-3">
                                                                <label for="userCount" class="form-label">
                                                                    Number of Users to Create
                                                                    <i class="bi bi-info-circle" data-bs-toggle="tooltip" 
                                                                       title="Specify how many user accounts you want to create. Each user will have a unique activation code."></i>
                                                                </label>
                                                                <input type="number" class="form-control" id="userCount" 
                                                                       name="user_count" min="1" max="100" value="1" required>
                                                                <small class="form-text text-muted">
                                                                    Enter a number between 1 and 100
                                                                </small>
                                                            </div>
                                                            
                                                            <div class="mb-3">
                                                                <div class="form-check">
                                                                    <input class="form-check-input" type="checkbox" 
                                                                           id="activateUsers" name="activate_users" value="1">
                                                                    <label class="form-check-label" for="activateUsers">
                                                                        Activate users upon creation
                                                                        <i class="bi bi-info-circle" data-bs-toggle="tooltip" 
                                                                           title="If checked, users will be activated immediately and can start using their accounts. If unchecked, users will need to activate their accounts using their unique activation code."></i>
                                                                    </label>
                                                                </div>
                                                                <small class="form-text text-muted">
                                                                    Deactivated users will need to use their activation code to access their account
                                                                </small>
                                                            </div>

                                                            <div class="alert alert-info" role="alert">
                                                                <i class="bi bi-lightbulb"></i> 
                                                                <strong>Note:</strong> Each user will be assigned a unique activation code. 
                                                                Users will be named as "Unset-User-X" until they personalize their accounts.
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                            <button type="submit" class="btn btn-primary">
                                                                <i class="bi bi-check-circle"></i> Create Users
                                                            </button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Import Users Modal -->
                                        <div class="modal fade" id="importUsersModal" tabindex="-1" aria-labelledby="importUsersModalLabel" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="importUsersModalLabel">
                                                            <i class="bi bi-cloud-upload"></i> Import Users
                                                        </h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="mb-3">
                                                            <label for="importType" class="form-label">
                                                                Import Type
                                                                <i class="bi bi-info-circle" data-bs-toggle="tooltip" 
                                                                   title="Select whether you're importing individual users or company profiles"></i>
                                                            </label>
                                                            <select class="form-select" id="importType">
                                                                <option value="individual" selected>
                                                                    Individual Users ({{ $config['individual_prefix'] ?? 'nmyl' }} prefix)
                                                                </option>
                                                                <option value="company">
                                                                    Company Profiles ({{ $config['company_prefix'] ?? 'pt' }} prefix)
                                                                </option>
                                                            </select>
                                                        </div>
                                                        
                                                        <div class="alert alert-info" role="alert">
                                                            <strong id="importTypeHelp">Individual Users:</strong>
                                                            <ul id="importTypeDetails" class="mb-0 mt-2">
                                                                <li>Requires: FIRST NAME, M.I. + LAST NAME, EMAIL ADDRESS</li>
                                                                <li>Required: LOCAL POSITION, LGU, Province, REGION, CONTACT NO.</li>
                                                                <li>Optional: Type of Membership, BIRTHDAY, website_url</li>
                                                                <li>Code prefix: <code>{{ $config['individual_prefix'] ?? 'nmyl' }}</code></li>
                                                            </ul>
                                                        </div>

                                                        <div class="mb-3">
                                                            <label for="userFile" class="form-label">Choose File (CSV or XLSX)</label>
                                                            <input type="file" class="form-control" id="userFile" accept=".xlsx,.csv">
                                                        </div>
                                                        <div id="filePreview" class="mt-3"></div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" id="processFile" class="btn btn-primary">
                                                            <i class="bi bi-upload"></i> Import
                                                        </button>
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <script type="text/javascript">
                                            // Initialize tooltips and export handlers
                                            document.addEventListener('DOMContentLoaded', function() {
                                                var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
                                                var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                                                    return new bootstrap.Tooltip(tooltipTriggerEl);
                                                });

                                                // Export QR Codes handler
                                                $('#exportQRCodes').on('click', function() {
                                                    var loadingModal = new bootstrap.Modal(document.getElementById('loadingModal'));
                                                    var loadingText = document.getElementById('loadingText');
                                                    var loadingSpinner = document.getElementById('loadingSpinner');
                                                    var loadingIcon = document.getElementById('loadingIcon');
                                                    var statusIcon = document.getElementById('statusIcon');
                                                    var closeBtn = document.getElementById('closeLoadingBtn');
                                                    
                                                    $('#exportUsersModal').modal('hide');
                                                    
                                                    // Reset modal state
                                                    loadingSpinner.classList.remove('d-none');
                                                    loadingIcon.classList.add('d-none');
                                                    closeBtn.classList.add('d-none');
                                                    loadingText.textContent = "Generating QR codes, please wait...";
                                                    
                                                    loadingModal.show();

                                                    $.ajax({
                                                        url: '/generate-qr-code',
                                                        method: 'POST',
                                                        data: {
                                                            _token: '{{ csrf_token() }}'
                                                        },
                                                        xhrFields: {
                                                            responseType: 'blob'
                                                        },
                                                        success: function(response) {
                                                            // Hide spinner, show success icon
                                                            loadingSpinner.classList.add('d-none');
                                                            loadingIcon.classList.remove('d-none');
                                                            statusIcon.className = 'bi bi-check-circle-fill text-success fs-1';
                                                            loadingText.textContent = "QR codes generated successfully! Download will start shortly...";
                                                            closeBtn.classList.remove('d-none');

                                                            var url = URL.createObjectURL(response);
                                                            var today = new Date();
                                                            var dateString = today.getFullYear() + '-' + 
                                                                ('0' + (today.getMonth() + 1)).slice(-2) + '-' + 
                                                                ('0' + today.getDate()).slice(-2);

                                                            var filename = 'QR_Codes_' + dateString + '.xlsx';

                                                            var link = document.createElement('a');
                                                            link.href = url;
                                                            link.download = filename;
                                                            document.body.appendChild(link);
                                                            link.click();
                                                            document.body.removeChild(link);

                                                            URL.revokeObjectURL(url);
                                                        },
                                                        error: function(xhr, status, error) {
                                                            // Hide spinner, show error icon
                                                            loadingSpinner.classList.add('d-none');
                                                            loadingIcon.classList.remove('d-none');
                                                            statusIcon.className = 'bi bi-x-circle-fill text-danger fs-1';
                                                            
                                                            var errorMessage = 'An error occurred while generating the QR codes.';
                                                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                                                errorMessage = xhr.responseJSON.message;
                                                            } else if (xhr.status === 404) {
                                                                errorMessage = 'No users found to generate QR codes.';
                                                            } else if (xhr.status === 500) {
                                                                errorMessage = 'Server error occurred. Please try again later.';
                                                            }
                                                            
                                                            loadingText.textContent = errorMessage;
                                                            closeBtn.classList.remove('d-none');
                                                        }
                                                    });
                                                });

                                                // Export User Credentials handler
                                                $('#exportUserCredentials').on('click', function() {
                                                    var loadingModal = new bootstrap.Modal(document.getElementById('loadingModal'));
                                                    var loadingText = document.getElementById('loadingText');
                                                    var loadingSpinner = document.getElementById('loadingSpinner');
                                                    var loadingIcon = document.getElementById('loadingIcon');
                                                    var statusIcon = document.getElementById('statusIcon');
                                                    var closeBtn = document.getElementById('closeLoadingBtn');
                                                    
                                                    $('#exportUsersModal').modal('hide');
                                                    
                                                    // Reset modal state
                                                    loadingSpinner.classList.remove('d-none');
                                                    loadingIcon.classList.add('d-none');
                                                    closeBtn.classList.add('d-none');
                                                    loadingText.textContent = "Generating user credentials export, please wait...";
                                                    
                                                    loadingModal.show();

                                                    $.ajax({
                                                        url: '/export-user-credentials',
                                                        method: 'POST',
                                                        data: {
                                                            _token: '{{ csrf_token() }}'
                                                        },
                                                        xhrFields: {
                                                            responseType: 'blob'
                                                        },
                                                        success: function(response) {
                                                            // Hide spinner, show success icon
                                                            loadingSpinner.classList.add('d-none');
                                                            loadingIcon.classList.remove('d-none');
                                                            statusIcon.className = 'bi bi-check-circle-fill text-success fs-1';
                                                            loadingText.textContent = "User credentials exported successfully! Download will start shortly...";
                                                            closeBtn.classList.remove('d-none');

                                                            var url = URL.createObjectURL(response);
                                                            var today = new Date();
                                                            var dateString = today.getFullYear() + '-' + 
                                                                ('0' + (today.getMonth() + 1)).slice(-2) + '-' + 
                                                                ('0' + today.getDate()).slice(-2);

                                                            var filename = 'User_Credentials_' + dateString + '.xlsx';

                                                            var link = document.createElement('a');
                                                            link.href = url;
                                                            link.download = filename;
                                                            document.body.appendChild(link);
                                                            link.click();
                                                            document.body.removeChild(link);

                                                            URL.revokeObjectURL(url);
                                                        },
                                                        error: function(xhr, status, error) {
                                                            // Hide spinner, show error icon
                                                            loadingSpinner.classList.add('d-none');
                                                            loadingIcon.classList.remove('d-none');
                                                            statusIcon.className = 'bi bi-x-circle-fill text-danger fs-1';
                                                            
                                                            var errorMessage = 'An error occurred while generating the user credentials export.';
                                                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                                                errorMessage = xhr.responseJSON.message;
                                                            } else if (xhr.status === 404) {
                                                                errorMessage = 'No users found to export.';
                                                            } else if (xhr.status === 500) {
                                                                errorMessage = 'Server error occurred. Please try again later.';
                                                            }
                                                            
                                                            loadingText.textContent = errorMessage;
                                                            closeBtn.classList.remove('d-none');
                                                        }
                                                    });
                                                });
                                            });

                                            const prefixConfig = {
                                                individual: '{{ $config['individual_prefix'] ?? 'nmyl' }}',
                                                company: '{{ $config['company_prefix'] ?? 'pt' }}'
                                            };

                                            document.getElementById('importType').addEventListener('change', function() {
                                                const importType = this.value;
                                                const helpTitle = document.getElementById('importTypeHelp');
                                                const helpDetails = document.getElementById('importTypeDetails');
                                                
                                                if (importType === 'company') {
                                                    helpTitle.textContent = 'Company Profiles:';
                                                    helpDetails.innerHTML = `
                                                        <li>Requires: company_name, primary_email_address</li>
                                                        <li>Optional: office_address_main, mobile_number, telephone_number, website_url</li>
                                                        <li>Code prefix: <code>${prefixConfig.company}</code></li>
                                                    `;
                                                } else {
                                                    helpTitle.textContent = 'Individual Users:';
                                                    helpDetails.innerHTML = `
                                                        <li>Requires: FIRST NAME, M.I. + LAST NAME, EMAIL ADDRESS</li>
                                                        <li>Required: LOCAL POSITION, LGU, Province, REGION, CONTACT NO.</li>
                                                        <li>Optional: Type of Membership, BIRTHDAY, website_url</li>
                                                        <li>Code prefix: <code>${prefixConfig.individual}</code></li>
                                                    `;
                                                }
                                            });

                                            // Function to confirm and delete users
                                            var confirmIt = function(e) {
                                                e.preventDefault();
                                                if (confirm("{{ __('messages.confirm.delete.user') }}")) {
                                                    var userId = this.getAttribute('data-id');
                                                    this.innerHTML =
                                                        '<div class="d-flex justify-content-center"><div class="spinner-border spinner-border-sm text-primary" role="status"><span class="visually-hidden">Loading...</span></div></div>';
                                                    deleteUserData(userId);
                                                }
                                            };

                                            var deleteUserData = function(userId) {
                                                var xhr = new XMLHttpRequest();
                                                xhr.open('POST', `{{ route('deleteTableUser', ['id' => ':id']) }}`.replace(':id', userId), true);
                                                xhr.setRequestHeader('X-CSRF-TOKEN', '{{ csrf_token() }}');
                                                xhr.setRequestHeader('Content-Type', 'application/json;charset=UTF-8');
                                                xhr.onreadystatechange = function() {
                                                    if (xhr.readyState === 4 && xhr.status === 200) {
                                                        refreshLivewireTable();
                                                    }
                                                };
                                                xhr.send(JSON.stringify({
                                                    id: userId
                                                }));
                                            };

                                            // Function to handle user actions (verification and blocking)
                                            var handleUserClick = function(e) {
                                                e.preventDefault();
                                                var userId = this.getAttribute('data-id');
                                                this.innerHTML =
                                                    '<div class="d-flex justify-content-center"><div class="spinner-border spinner-border-sm text-primary" role="status"><span class="visually-hidden">Loading...</span></div></div>';
                                                sendUserAction(userId);
                                            };

                                            var sendUserAction = function(userId) {
                                                var xhr = new XMLHttpRequest();
                                                xhr.open('GET', userId, true);
                                                xhr.onreadystatechange = function() {
                                                    if (xhr.readyState === 4 && xhr.status === 200) {
                                                        refreshLivewireTable();
                                                    }
                                                };
                                                xhr.send();
                                            };

                                            // Attach click event listeners
                                            var attachClickEventListeners = function(className, handler) {
                                                var elems = document.getElementsByClassName(className);
                                                for (var i = 0, l = elems.length; i < l; i++) {
                                                    elems[i].addEventListener('click', handler, false);
                                                }
                                            };

                                            // Function to refresh the Livewire table
                                            var refreshLivewireTable = function() {
                                                Livewire.components.getComponentsByName('user-table')[0].$wire.$refresh()
                                            };

                                            attachClickEventListeners('confirmation', confirmIt);
                                            attachClickEventListeners('user-email', handleUserClick);
                                            attachClickEventListeners('user-block', handleUserClick);
                                            attachClickEventListeners('user-status', handleUserClick);

                                            let parsedData = [];

                                            document.getElementById('processFile').addEventListener('click', () => {
                                                const fileInput = document.getElementById('userFile');
                                                const file = fileInput.files[0];

                                                if (!file) {
                                                    alert('Please select a file.');
                                                    return;
                                                }

                                                if (file.name.endsWith('.csv')) {
                                                    Papa.parse(file, {
                                                        header: true,
                                                        complete: function(results) {
                                                            parsedData = results.data.filter(row => validateRow(row));
                                                            uploadData();
                                                        },
                                                        error: function(error) {
                                                            console.error('Error parsing CSV:', error);
                                                            alert('Error parsing the CSV file.');
                                                        },
                                                    });
                                                } else if (file.name.endsWith('.xlsx')) {
                                                    const reader = new FileReader();
                                                    reader.onload = (e) => {
                                                        const data = new Uint8Array(e.target.result);
                                                        const workbook = XLSX.read(data, {
                                                            type: 'array'
                                                        });
                                                        const sheetName = workbook.SheetNames[0];
                                                        parsedData = XLSX.utils.sheet_to_json(workbook.Sheets[sheetName]).filter(row => validateRow(
                                                            row));
                                                        uploadData();
                                                    };
                                                    reader.readAsArrayBuffer(file);
                                                } else {
                                                    alert('Invalid file format. Please upload a .csv or .xlsx file.');
                                                }
                                            });

                                            function validateRow(row) {
                                                return Object.values(row).some(value => value && value.toString().trim() !== '');
                                            }

                                            function uploadData() {
                                                const filePreviewDiv = document.getElementById('filePreview');
                                                const importType = document.getElementById('importType').value;
                                                const prefix = prefixConfig[importType];

                                                const loadingMessage = document.createElement('div');
                                                loadingMessage.textContent = `Importing ${importType} users with prefix "${prefix}"...`;
                                                loadingMessage.id = 'loading-message';
                                                loadingMessage.style.backgroundColor = '#f8f9fa';
                                                loadingMessage.style.color = '#6c757d';
                                                loadingMessage.style.padding = '10px';
                                                loadingMessage.style.borderRadius = '5px';
                                                loadingMessage.style.fontSize = '14px';
                                                loadingMessage.style.marginTop = '10px';
                                                filePreviewDiv.appendChild(loadingMessage);

                                                fetch('{{ route('importUsers') }}', {
                                                    method: 'POST',
                                                    headers: {
                                                        'Content-Type': 'application/json',
                                                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                                    },
                                                    body: JSON.stringify({
                                                        users: parsedData,
                                                        import_type: importType
                                                    }),
                                                })
                                                .then((response) => response.json())
                                                .then((data) => {
                                                    alert(data.message);
                                                    location.reload();
                                                })
                                                .catch((error) => {
                                                    console.error('Error uploading data:', error);
                                                    alert('Failed to upload data.');
                                                })
                                                .finally(() => {
                                                    const loadingMessage = document.getElementById('loading-message');
                                                    if (loadingMessage) {
                                                        loadingMessage.remove();
                                                    }
                                                });
                                            }
                                        </script>
                                    </div>
                                </section>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('sidebar-stylesheets')
        <script defer src="{{ url('assets/js/cdn.min.js') }}"></script>
        <script src="{{ url('vendor/livewire/livewire/dist/livewire.js') }}"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/PapaParse/5.3.2/papaparse.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    @endpush

    @push('sidebar-scripts')
        <livewire:scripts />
        <script src="{{ url('assets/js/livewire-sortable.js') }}"></script>
    @endpush
@endsection