<!-- Attendance Error -->
<!DOCTYPE html>
<html lang="en">
<head>
    @extends('layouts.lang')

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">

    <style>
      [x-cloak] { display: none!important; }
      
      body, html {
            margin: 0;
            padding: 0;
            min-height: 100vh;
            width: 100%;
            background: linear-gradient(135deg, #4128b1ff 0%, #4128b1ff 100%);
            font-family: 'Poppins', sans-serif;
        }
        
        .container-fluid.content-inner {
            min-height: 100vh;
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 20px;
        }

        .content-card {
            background-color: white;
            border-radius: 20px;
            padding: 50px 40px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            max-width: 700px;
            width: 100%;
            animation: fadeInUp 0.6s ease-out;
        }

        .attendance-center {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
        }

        .logo-container {
            margin-bottom: 30px;
        }

        .logo-container img {
            max-width: 200px;
            max-height: 200px;
            object-fit: contain;
        }

        h2, h3, h4 {
            font-family: 'Poppins', sans-serif;
            color: #2d3748 !important;
            margin: 0;
        }

        .header-main {
            font-size: 1.8rem;
            font-weight: 600;
            margin-bottom: 20px;
            color: #1a202c !important;
        }

        .error-icon {
            color: #f56565 !important;
            font-size: 100px;
            margin: 20px 0;
            animation: shake 0.5s ease-out;
        }

        .error-message {
            font-size: 1.3rem;
            font-weight: 500;
            margin: 20px 0;
            color: #4a5568 !important;
            line-height: 1.6;
        }

        .error-info {
            background: white;
            border: 2px dashed #feb2b2;
            padding: 25px;
            border-radius: 12px;
            margin: 30px 0;
            width: 100%;
        }

        .error-info-content {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 15px;
        }

        .error-info-icon {
            font-size: 2rem;
            color: #f56565;
        }

        .error-info-text {
            font-size: 1rem;
            color: #4a5568;
            text-align: left;
        }

        .seat-info {
            background: white;
            border: 2px dashed #cbd5e0;
            padding: 30px;
            border-radius: 12px;
            margin: 30px 0;
            width: 100%;
            animation: slideIn 0.5s ease-out;
        }

        .seat-info h2 {
            color: #2d3748 !important;
            font-size: 1.3rem;
            font-weight: 600;
            margin-bottom: 20px;
        }

        .seat-details {
            display: flex;
            justify-content: space-around;
            align-items: center;
            margin: 20px 0;
            gap: 30px;
        }

        .seat-detail-item {
            flex: 1;
        }

        .seat-label {
            font-size: 0.9rem;
            color: #718096;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .table-name {
            font-size: 2rem;
            font-weight: 600;
            color: #667eea;
        }

        .chair-number {
            font-size: 2.5rem;
            font-weight: 700;
            color: #764ba2;
        }

        .seat-instruction {
            margin-top: 20px;
            font-size: 0.95rem;
            color: #4a5568;
            padding: 12px;
            background: #f7fafc;
            border-radius: 8px;
        }

        .seat-instruction i {
            color: #667eea;
            margin-right: 8px;
        }

        .redirect-message {
            font-size: 0.95rem;
            color: #718096 !important;
            margin-top: 30px;
            font-style: italic;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes shake {
            0%, 100% {
                transform: translateX(0);
            }
            10%, 30%, 50%, 70%, 90% {
                transform: translateX(-10px);
            }
            20%, 40%, 60%, 80% {
                transform: translateX(10px);
            }
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @media (max-width: 768px) {
            .content-card {
                padding: 40px 30px;
            }

            .header-main {
                font-size: 1.5rem;
            }

            .error-icon {
                font-size: 80px;
            }

            .error-message {
                font-size: 1.1rem;
            }

            .error-info-content {
                flex-direction: column;
                text-align: center;
            }

            .error-info-text {
                text-align: center;
            }

            .seat-details {
                flex-direction: column;
                gap: 20px;
            }

            .table-name {
                font-size: 1.6rem;
            }

            .chair-number {
                font-size: 2rem;
            }
        }
    </style>
</head>
<body>
    <div class="container-fluid content-inner">
        <div class="content-card">
            <div class="attendance-center">
                <div class="logo-container">
                    <a href="{{ config('app.url') }}" target="_blank" rel="noopener noreferrer">
                        <img src="{{ asset('assets/linkstack/images/'.findFile('avatar')) }}" 
                            alt="Logo" />
                    </a>
                </div>

                <i class="fas fa-times-circle error-icon"></i>

                <h2 class="error-message">
                    @if($error)
                        {{ $error }}
                    @else
                        There seems to be a problem with your ID
                    @endif
                </h2>
                
                @if($has_seat ?? false)
                    <div class="seat-info">
                        <h2>Your Previously Assigned Seat</h2>
                        
                        <div class="seat-details">
                            <div class="seat-detail-item">
                                <div class="seat-label">Table</div>
                                <div class="table-name">{{ $table_name }}</div>
                            </div>
                            
                            <div class="seat-detail-item">
                                <div class="seat-label">Seat</div>
                                <div class="chair-number">{{ $chair_number }}</div>
                            </div>
                        </div>

                        <div class="seat-instruction">
                            <i class="fas fa-info-circle"></i>
                            Your seat remains reserved for you
                        </div>
                    </div>
                @endif

                <div class="error-info">
                    <div class="error-info-content">
                        <i class="fas fa-exclamation-triangle error-info-icon"></i>
                        <div class="error-info-text">
                            <strong>Need help?</strong><br>
                            Please approach an admin staff or helpdesk for assistance.
                        </div>
                    </div>
                </div>

                <p class="redirect-message">
                    <i class="fas fa-sync-alt"></i> Redirecting you back in 7 seconds...
                </p>
            </div>
        </div>
    </div>

    <!-- Replace the form section at the bottom of attendance-error.blade.php with this: -->

<form id="attendanceForm" action="/attendance-input" method="POST">
    @csrf
    <input type="hidden" name="event" value="{{ $event }}">
    <input type="hidden" name="enable_rep_prompt" value="{{ $enable_rep_prompt ?? 0 }}">
</form>

<script defer src="{{ url('assets/js/cdn.min.js') }}"></script>
<script src="{{ url('vendor/livewire/livewire/dist/livewire.js') }}"></script>
<script src="{{ url('assets/js/livewire-sortable.js') }}"></script>

<script>
    window.onload = function() {
        window.setTimeout(function() {
            document.getElementById('attendanceForm').submit();
        }, 7000);
    };
</script>
</body>
</html>