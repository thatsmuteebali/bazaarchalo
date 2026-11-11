<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class AccountStatusNotification extends Notification
{
    use Queueable;

    public function __construct(private readonly string $status) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $active = $this->status === 'Active';

        return [
            'title' => 'Your account is '.strtolower($this->status),
            'message' => $active
                ? 'Your Bazaar Chalo account has been activated.'
                : 'Your Bazaar Chalo account has been deactivated.',
            'url' => route('frontend.home', absolute: false),
            'icon' => $active ? 'bi bi-person-check' : 'bi bi-person-x',
        ];
    }
}
