<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Address;

class InviteMail extends Mailable
{
    use Queueable, SerializesModels;
    public $eventName;
    public $eventStart;
    public $eventEnd ;
    public $eventAddress;
    public $name;
    public $url;
    public $filePath;
    public $fileName;
    public $subject;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($eventName, $name, $eventStart, $eventEnd, $eventAddress, $url, $filePath, $fileName)
    {
        $this->eventName = $eventName;
        $this->eventStart = $eventStart;
        $this->eventEnd = $eventEnd;
        $this->eventAddress = $eventAddress;
        $this->name = $name;
        $this->url = $url;
        $this->filePath = $filePath;
        $this->fileName = $fileName;
        $this->subject = 'Event Invitation';
    }

   /* public function __construct()
    {

    }*/

    /**
     * Get the message envelope.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
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

    /*public function envelope()
    {
        return new Envelope(
            subject: 'hello'
        );
    }*/

    /**
     * Get the message content definition.
     *
     * @return \Illuminate\Mail\Mailables\Content
     */
    public function content()
    {
        return new Content(
            markdown: 'mail.event-notification',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array
     */
    public function attachments()
    {
        return [];
    }

    public function build()
    {
        return $this
            ->markdown('mail.event-notification') // Use your markdown view here
            ->subject($this->subject)
            ->attach($this->filePath, [
                'as' => $this->fileName, // Specify the name for the attachment
                'mime' => 'application/pdf',
            ]);
    }

    
}
