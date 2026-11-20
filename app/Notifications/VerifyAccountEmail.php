<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;

class VerifyAccountEmail extends VerifyEmail
{
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Verify your Bazaar Chalo email address')
            ->view('emails.verify-email', [
                'userName' => $notifiable->name,
                'verificationUrl' => $this->verificationUrl($notifiable),
                'expirationMinutes' => config('auth.verification.expire', 60),
            ]);
    }
}
