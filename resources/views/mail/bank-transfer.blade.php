<x-mail::message>
# Dear {{ $data['name'] }},

We are writing to inform you that we have not yet received your payment via bank transfer. Please find the details of your pending transaction below:

**Amount Due:** {{ $data['amount_due'] }}<br>
**Transaction Reference:** {{ $data['transaction_reference'] }}<br>
**Due Date:** {{ $data['due_date'] }}<br>
**Bank Details for Payment:**

- **Bank Name:** {{ $data['bank_name'] }}
- **Account Name:** {{ $data['account_name'] }}
- **Account Number:** {{ $data['account_number'] }}

Please ensure that the payment is made before the due date to avoid any service interruptions. Once the payment has been completed, kindly send us the transaction receipt to **admin@philtoa.ph**.

If you have any questions or require further assistance, feel free to contact us.

Best regards,<br>
PHILTOA
</x-mail::message>
