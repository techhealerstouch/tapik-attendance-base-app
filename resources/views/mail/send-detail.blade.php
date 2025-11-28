<x-mail::message>
# Hi!

Pleasure to meet you. Please find my contact details below:

First name: {{ $firstname }}<br>
Last name: {{ $lastname }}<br>
Company: {{ $company }}<br>
Phone: {{ $mobile }}<br>
Email: {{ $email }}<br>

<x-mail::button :url="url($urldownload)">
Add to contacts
</x-mail::button>

Lets keep in touch.

Best regards,<br>
{{ $firstname }}
</x-mail::message>
