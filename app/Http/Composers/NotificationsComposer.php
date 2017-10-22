<?php

namespace App\Http\Composers;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\View\View;

class NotificationsComposer
{
    /**
     * The logged in user instance.
     *
     * @var Authenticatable
     */
    public $user;

    /**
     * Construct the admin menu.
     *
     * @param View $view
     */
    public function compose(View $view)
    {
        if (!($this->user = auth()->user())) {
            return;
        }

        $notifications = $this->user->unreadNotifications();

        $view->with([
            'notifications' => $notifications->take(5)->get(),
            'count' => $notifications->count(),
        ]);
    }
}