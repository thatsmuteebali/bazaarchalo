<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class NotificationController extends Controller
{
    public function index(Request $request): View
    {
        $panel = $this->panel($request);
        $notifications = $request->user()
            ->notifications()
            ->latest()
            ->paginate(20);

        return view('shared.notifications.index', compact('notifications', 'panel'));
    }

    public function read(Request $request, string $notification): \Illuminate\Http\RedirectResponse
    {
        $panel = $this->panel($request);
        $notification = $request->user()
            ->notifications()
            ->whereKey($notification)
            ->firstOrFail();

        $notification->markAsRead();
        $url = $notification->data['url'] ?? null;

        if (is_string($url) && str_starts_with($url, '/') && ! str_starts_with($url, '//')) {
            return redirect($url);
        }

        return redirect()->route($panel.'.notifications.index');
    }

    public function readAll(Request $request): \Illuminate\Http\RedirectResponse
    {
        $panel = $this->panel($request);
        $request->user()->unreadNotifications()->update(['read_at' => now()]);

        return redirect()->route($panel.'.notifications.index')
            ->with('success', 'All notifications marked as read.');
    }

    private function panel(Request $request): string
    {
        $panel = $request->route('panel');

        abort_unless(in_array($panel, ['admin', 'seller', 'customer'], true), 404);

        return $panel;
    }
}
