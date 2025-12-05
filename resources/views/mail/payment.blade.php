<x-mail::message>
# Dear {{ $name }},

We hope you are excited about the upcoming {{ $eventName }}. This is a reminder that a ticket is still pending payment.

Ticket Details: <br>
Event Name: {{ $eventName }}<br>
Ticket Name: {{ $ticketName }}<br>
Amount Due: ₱{{ $amount }}.00<br>
Due Date: {{ $expiry_date }}<br>
Payment Portal: Xendit<br>
Order Reference: {{ $invoice_no }}<br>

To complete your registration and secure your spot, please make the payment by the due date. You can complete the payment via Xendit:
<x-mail::button :url="$url">
Pay via Xendit
</x-mail::button>

If you have any questions or encounter any issues, please feel free to contact us at admin@{{ config('app.name') }}.ph.

Best regards,<br>
{{ config('app.name') }}
</x-mail::message>
