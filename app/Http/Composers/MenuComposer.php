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

            $menu->add(function ($item) use ($menu) {
                $access = $item->name('Access Control')->data('icon', 'fa-sign-in')->active('admin/admin*');

                $menu->child($access, function ($item) {
                    $item->name('Admin Roles')->url(route('admin.admin_roles.index'))->permissions('admin-roles-list')->active('admin/admin-roles/*');
                });

                $menu->child($access, function ($item) {
                    $item->name('Admin Users')->url(route('admin.admin_users.index'))->permissions('admin-users-list')->active('admin/admin-users/*');
                });
            });






            $menu->add(function ($item) use ($menu) {
                $test = $item->name('Test')->data('icon', 'fa-text-width')->active('admin/test/*');

                $menu->child($test, function ($item) {
                    $item->name('Test')->url(route('admin.test.index'))->active('admin/test/*');
                });
            });
        })->filter(function ($item) use ($user) {
            return $user->isSuper() || $user->hasAnyPermission($item->permissions());
        });

        $view->with('menu', $menu);
    }
}