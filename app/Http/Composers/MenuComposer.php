<?php

namespace App\Http\Composers;

use Illuminate\View\View;

class MenuComposer
{
    /**
     * @param  View  $view
     * @return void
     */
    public function compose(View $view)
    {
        $user = auth()->user();
        $menu = menu()->make(function ($menu) {
            $menu->add(function ($item) {
                $item->name('Dashboard')->url(route('admin'))->data('icon', 'fa-home')->active('admin');
            });

            /*$menu->add(function ($item) use ($menu) {
                $access = $item->name('Access Control')->data('icon', 'fa-sign-in')->active('admin/admin-users');

                $menu->child($access, function ($item) {
                    $item->name('Admin Users')->url(route('admin.admin.users'))->permissions('admin-users-list')->active('admin/admin-users');
                });
            });*/
        })->filter(function ($item) use ($user) {
            return $user->hasAnyPermission($item->permissions());
        });

        $view->with('menu', $menu);
    }
}