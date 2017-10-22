<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;

class NotificationsController extends Controller
{
    /**
     * @param Authenticatable $user
     * @return mixed
     */
    public function get(Authenticatable $user)
    {
        $notifications = $user->unreadNotifications();

        return [
            'notifications' => $notifications->take(3)->get(),
            'count' => $notifications->count(),
            'urlApprove' => route('admin.notifications.action'),
            'urlAll' => route('admin.notifications.index'),
        ];
    }

    /**
     * @param Request $request
     * @param Authenticatable $user
     * @return \Illuminate\View\View
     */
    public function index(Request $request, Authenticatable $user)
    {
        $query = $user->notifications();

        if ($request->filled('read')) {
            switch ($request->query('read')) {
                case 1:
                    $query->whereNull('read_at');
                    break;
                case 2:
                    $query->whereNotNull('read_at');
            }
        }

        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->query('start_date'));
        }

        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->query('end_date'));
        }

        return view('admin.auth.notifications.index')->with([
            'items' => $query->paginate(config('crud.per_page'))
        ]);
    }

    /**
     * @param DatabaseNotification $notification
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(DatabaseNotification $notification)
    {
        try {
            $notification->delete();

            flash()->success('The record was successfully deleted!');
        } catch (Exception $e) {
            flash()->success('Something went wrong! Please try again.');
        }

        return back();
    }

    /**
     * @param Authenticatable $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function clean(Authenticatable $user)
    {
        try {
            $user->readNotifications()->delete();

            flash()->success('All your already read notifications have been successfully deleted!');
        } catch (Exception $e) {
            flash()->success('Something went wrong! Please try again.');
        }

        return back();
    }

    /**
     * @param Authenticatable $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function delete(Authenticatable $user)
    {
        try {
            $user->notifications()->delete();

            flash()->success('All your notifications have been successfully deleted!');
        } catch (Exception $e) {
            flash()->success('Something went wrong! Please try again.');
        }

        return back();
    }

    /**
     * @param DatabaseNotification $notification
     * @return \Illuminate\Http\RedirectResponse
     */
    public function action(DatabaseNotification $notification)
    {
        try {
            $notification->markAsRead();

            return isset($notification->data['url']) ? redirect($notification->data['url']) : back();
        } catch (Exception $e) {
            flash()->success('Something went wrong! Please try again.');
            return back();
        }
    }

    /**
     * @param DatabaseNotification $notification
     * @return \Illuminate\Http\RedirectResponse
     */
    public function read(DatabaseNotification $notification)
    {
        try {
            $notification->markAsRead();

            flash()->success('The notification has been successfully marked as read!');
        } catch (Exception $e) {
            flash()->success('Something went wrong! Please try again.');
        }

        return back();
    }

    /**
     * @param Authenticatable $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function readAll(Authenticatable $user)
    {
        try {
            $user->unreadNotifications->each(function ($notification) {
                $notification->markAsRead();
            });

            flash()->success('All your unread notifications have been successfully marked as read!');
        } catch (Exception $e) {
            flash()->success('Something went wrong! Please try again.');
        }

        return back();
    }
}