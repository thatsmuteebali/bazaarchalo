<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContactMessageMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly string $name,
        public readonly string $email,
        public readonly string $message,
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New contact inquiry from ' . $this->name,
            replyTo: [new Address($this->email, $this->name)],
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.contact-message',
            with: [
                'senderName' => $this->name,
                'senderEmail' => $this->email,
                'messageBody' => $this->message,
                'receivedAt' => now()->format('F j, Y \a\t g:i A'),
            ],
        );
    }
}
