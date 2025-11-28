<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Address;

class DetailsMail extends Mailable
{
    use Queueable, SerializesModels;
    public $details;
    public $subject;
    public $firstname;
    public $lastname;
    public $company;
    public $title;
    public $mobile;
    public $email;
    public $urldownload;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($details, $subject, $firstname, $lastname, $company, $title, $mobile, $email, $urldownload)
    {
        $this->details = $details;
        $this->subject = $subject;
        $this->firstname = $firstname;
        $this->lastname = $lastname;
        $this->company = $company;
        $this->title = $title;
        $this->mobile = $mobile;
        $this->email = $email;
        $this->urldownload = $urldownload;
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
            from: new Address(config('mail.from.address'), $this->firstname . ' ' . $this->lastname),
            replyTo: [
                new Address($this->email, $this->firstname . ' ' . $this->lastname)
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
            markdown: 'mail.send-detail',
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
