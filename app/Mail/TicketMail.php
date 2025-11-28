<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Address;

class TicketMail extends Mailable
{
    use Queueable, SerializesModels;
    public $name;
    public $filePath;
    public $fileName;
    public $subject;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($name, $filePath, $fileName)
    {

        $this->name = $name;
        $this->filePath = $filePath;
        $this->fileName = $fileName;
        $this->subject = 'Payment Confirmation & Event Ticket';
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
            from: new Address(config('mail.from.address'), 'Payment Confirmed'),
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
            markdown: 'mail.ticket',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array
     */
    // public function build()
    // {
    //     return $this
    //         ->subject($this->subject)
    //         ->markdown('mail.ticket')
    //         // Attach the saved PDF from its file path
    //         ->attachFromPath($this->filePath, [
    //             'mime' => 'application/pdf',
    //         ]);
    // }
    public function build()
    {
        return $this
            ->markdown('mail.ticket') // Use your markdown view here
            ->subject($this->subject)
            ->attach($this->filePath, [
                'as' => $this->fileName, // Specify the name for the attachment
                'mime' => 'application/pdf',
            ]);
    }
    
}
