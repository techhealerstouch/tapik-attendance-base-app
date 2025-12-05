<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\EmailTemplate;

class EmailTemplateSeeder extends Seeder
{
    public function run()
    {
        EmailTemplate::create([
            'name' => 'event-invitation',
            'subject' => 'Event Invitation: {{ eventName }}',
            'content' => '# Dear {{ name }},

You are cordially invited to **{{ eventName }}**, scheduled to take place on **{{ eventStart }}** at **{{ eventAddress }}**. <br>

We look forward to your presence at the event.

**In order to mark your attendance as present. Please scan the QR code of your NFC card.**

If you have any questions or encounter any issues, feel free to reach out to us.

Best regards,<br>
{{ appName }}',
            'available_variables' => ['name', 'eventName', 'eventStart', 'eventAddress', 'appName'],
            'is_active' => true
        ]);
    }
}