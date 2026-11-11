<?php

namespace App\Services;

use App\Models\Category;
use App\Models\User;
use App\Notifications\MarketplaceNotification;
use Illuminate\Support\Facades\Notification;

class MarketplaceNotificationService
{
    public function notifyActiveSellersOfCategory(Category $category): void
    {
        User::query()
            ->where('role', 'seller')
            ->where('status', 'Active')
            ->chunkById(100, function ($sellers) use ($category) {
                Notification::send($sellers, new MarketplaceNotification(
                    'New category available',
                    'The '.$category->name.' category is now active.',
                    route('seller.products.create', [], false),
                    'bi bi-tags',
                ));
            });
    }

    public function notifyAdminsOfPendingSubmission(
        string $entity,
        string $name,
        User $seller,
        string $url,
        string $icon,
    ): void {
        $admins = User::query()
            ->where('role', 'admin');

        $admins->chunkById(100, function ($users) use ($entity, $name, $seller, $url, $icon) {
            Notification::send($users, new MarketplaceNotification(
                'New '.$entity.' pending review',
                $seller->name.' submitted '.$name.'. It is awaiting your review.',
                $url,
                $icon,
            ));
        });
    }

    public function notifySellerOfStatus(
        User $seller,
        string $entity,
        string $name,
        string $status,
        string $url,
        string $icon,
    ): void {
        $seller->notify(new MarketplaceNotification(
            'Your '.$entity.' is '.strtolower($status),
            'Your '.$entity.' "'.$name.'" is now '.strtolower($status).'.',
            $url,
            $icon,
        ));
    }
}
