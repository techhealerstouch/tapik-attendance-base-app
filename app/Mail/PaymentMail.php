<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Address;

class PaymentMail extends Mailable
{
    use Queueable, SerializesModels;
    public $url;
    public $eventName;
    public $ticketName;
    public $name;
    public $amount;
    public $expiry_date;
    public $invoice_no;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($url, $eventName, $ticketName, $name, $amount, $expiry_date, $invoice_no)
    {
        $this->url = $url;
        $this->eventName = $eventName;
        $this->ticketName = $ticketName;
        $this->name = $name;
        $this->amount = $amount;
        $this->expiry_date = $expiry_date;
        $this->invoice_no = $invoice_no;
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
            from: new Address(config('mail.from.address'), 'Ticket payment for ' . $this->eventName),
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
            markdown: 'mail.payment',
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
}
