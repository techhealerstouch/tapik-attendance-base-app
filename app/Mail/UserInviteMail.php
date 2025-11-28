<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Address;

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

    public function __construct($eventName, $name, $eventStart, $eventEnd, $eventAddress, $url)
    {
        $this->eventName = $eventName;
        $this->eventStart = $eventStart;
        $this->eventEnd = $eventEnd;
        $this->eventAddress = $eventAddress;
        $this->name = $name;
        $this->url = $url;
        $this->subject = 'Event Invitation';
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
            markdown: 'mail.event-notification',
        );
    }

    public function attachments()
    {
        return []; // no attachments
    }

    public function build()
    {
        return $this
            ->markdown('mail.event-notification')
            ->subject($this->subject);
    }
}
