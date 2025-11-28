<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User QR</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            
        }
        .ticket {
            border: 1px solid rgb(60, 60, 60);
            width: 600px;
            padding: 10px;
            text-align: center;
            margin-bottom: 20px;
        }
        .event-name {
            font-size: 24px;
            font-weight: bold;
            text-align: Center;
            color: rgb(0, 0, 0);
        }
        .ticket-info {
            text-align: left;
            padding-left: 25px;
        }
        .qr-code {
            width: 400px;
            margin: 0 auto;
        }
        table {
            width: 100%;
        }
        td {
            vertical-align: top;
        }
        p{
            font-size: 14px;
            margin: 5px;
            color: rgb(0, 0, 0);
        }
    </style>
</head>
<body style="display: flex; align-items: center; justify-content: center; height: 100vh; margin: 0;">
    <div style="text-align: center;">
    <img src="{{ public_path('assets/linkstack/images/'.findFile('avatar')) }}" alt="Philtoa Logo" style="max-width: 400px; max-height: 400px;" />

        <h2>{{ $data['event_name'] }}</h2>
        <div class="qr-code">
            <img id="generatedImage" draggable="false" src="{{ $data['qr_code'] }}" style="margin-top: 20px; width:100%; height:auto;" alt="QR Code">
            <p><strong>{{ $data['name'] }}</strong></p>
        </div>
    </div>
</body>
</html>
