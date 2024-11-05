<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::defaultView('vendor.pagination.bootstrap-4');

        // Make sure to pass notifications to all views
        View::composer('*', function ($view) {
            if (Auth::check()) { // Ensure user is authenticated
                $user = Auth::user();

                // Get all notifications
                $notifications = Notification::with('sender')
                    ->where('user_id', $user->id)
                    ->where('read', 0)
                    ->orderByDesc('created_at')
                    ->get();

                // Filter notifications by type
                $connectionNotifications = $notifications->where('type', 'New Connection');
                $messageNotifications = $notifications->where('type', 'New Message');
                $adminMessageNotifications = $notifications->where('type', 'New Admin Message');

                // Calculate notification counts
                $connectionNotificationCount = $connectionNotifications->count();
                $messagenotificationCount = $messageNotifications->count();
                $adminnotificationCount = $adminMessageNotifications->count();

                // Total notification count
                $totalMessageNotificationCount = $messagenotificationCount + $adminnotificationCount;

                // Pass the notifications and counts to all views
                $view->with([
                    'notifications' => $notifications,
                    'connectionNotificationCount' => $connectionNotificationCount,
                    'messagenotificationCount' => $messagenotificationCount,
                    'adminnotificationCount' => $adminnotificationCount,
                    'totalMessageNotificationCount' => $totalMessageNotificationCount,
                ]);
            }
        });
    }
}
