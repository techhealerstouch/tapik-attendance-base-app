<x-mail::message>
# Dear {{ $name }},

We have confirmed your payment. <br>

If you have any questions or encounter any issues, please feel free to contact us at admin@{{ config('app.url') }}.ph.

Best regards,<br>
{{ config('app.name') }}
</x-mail::message>
