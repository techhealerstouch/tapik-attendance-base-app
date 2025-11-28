<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Event Ticket</title>
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
            width: 150px;
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
<body>
    @foreach($tickets as $ticket)
        <div class="ticket">
            <table>
                <tr>
                    <td class="qr-code">
                        <img id="generatedImage" draggable="false" src="{{ $ticket['qr_code'] }}" style="margin-top: 15px; width:100%; height:auto;" alt="QR Code">
                    </td>
                    <td class="ticket-info">
                        <hr>
                        <div class="event-name">{{ $ticket['event_name'] }}</div>
                        <p style="text-align: Center; margin-bottom: 15px;">{{ $ticket['address'] }}</p>
                        <p><strong>Attendee:</strong> {{ $ticket['attendee_name'] }}</p> <!-- Display Attendee Name -->
                        <p><strong>Date:</strong> 
                            @if($ticket['start_date'] == $ticket['end_date'])
                                {{ $ticket['start_date'] }}
                            @else
                                {{ $ticket['start_date'] }} - {{ $ticket['end_date'] }}
                            @endif
                        </p>
                        <p><strong>Time:</strong> {{ $ticket['start_time'] }} - {{ $ticket['end_time'] }}</p>
                        <hr>
                        <p style="text-align: Center; margin: 0">{{ $ticket['ticket_no'] }}</p>
                    </td>
                </tr>
            </table>
        </div>
    @endforeach
</body>
</html>
