<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Address;
use App\Models\EmailTemplate;

class UserInviteMail extends Mailable
{
    use Queueable, SerializesModels;

    public $eventName;
    public $eventStart;
    public $eventEnd;
    public $eventAddress;
    public $name;
    public $url;
    public $subject;
    public $dynamicContent;

    public function __construct($eventName, $name, $eventStart, $eventEnd, $eventAddress, $url)
    {
        $this->eventName = $eventName;
        $this->eventStart = $eventStart;
        $this->eventEnd = $eventEnd;
        $this->eventAddress = $eventAddress;
        $this->name = $name;
        $this->url = $url;
        
        // Load template from database
        $template = EmailTemplate::where('name', 'event-invitation')
            ->where('is_active', true)
            ->first();
        
        if ($template) {
            // Render subject with variables
            $this->subject = $template->renderSubject([
                'name' => $name,
                'eventName' => $eventName,
                'eventStart' => $eventStart,
                'eventEnd' => $eventEnd,
                'eventAddress' => $eventAddress,
            ]);
            
            // Render content with variables
            $this->dynamicContent = $template->render([
                'name' => $name,
                'eventName' => $eventName,
                'eventStart' => $eventStart,
                'eventEnd' => $eventEnd,
                'eventAddress' => $eventAddress,
                'appName' => config('app.name')
            ]);
        } else {
            // Fallback if no template exists
            $this->subject = 'Event Invitation';
            $this->dynamicContent = $this->getDefaultContent();
        }
    }

    public function envelope()
    {
        return new Envelope(
            from: new Address(config('mail.from.address'), config('mail.from.name') ?? ('Event Invitation: ' . $this->eventName)),
            replyTo: [
                new Address(config('mail.from.address'), null)
            ],
            subject: $this->subject,
        );
    }

    public function content()
    {
        return new Content(
            markdown: 'mail.event-notification-dynamic',
        );
    }

    public function attachments()
    {
        return []; // no attachments
    }

    public function build()
    {
        return $this
            ->markdown('mail.event-notification-dynamic')
            ->subject($this->subject);
    }

    /**
     * Get default content if no template exists in database
     */
    private function getDefaultContent()
    {
        return "# Dear {$this->name},

You are cordially invited to **{$this->eventName}**, scheduled to take place on **{$this->eventStart}** at **{$this->eventAddress}**. <br>

We look forward to your presence at the event.

**In order to mark your attendance as present. Please scan the QR code of your NFC card.**

If you have any questions or encounter any issues, feel free to reach out to us.

Best regards,<br>
" . config('app.name');
    }
}