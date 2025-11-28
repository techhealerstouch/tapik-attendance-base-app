<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Address;

class BankTransferMail extends Mailable
{
    use Queueable, SerializesModels;
    public $data;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($data)
    {

        $this->data = $data; // Store the data array
        $this->subject = 'Billing Information - Pending Bank Transfer';
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
            from: new Address(config('mail.from.address'), 'Billing Information'),
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
            markdown: 'mail.bank-transfer',
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

    
}
