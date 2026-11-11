<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AccountStatusMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly User $user,
        public readonly string $status,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Your Bazaar Chalo account status');
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.account-status',
            with: [
                'userName' => $this->user->name,
                'status' => strtolower($this->status),
                'homeUrl' => route('frontend.home'),
            ],
        );
    }
}
