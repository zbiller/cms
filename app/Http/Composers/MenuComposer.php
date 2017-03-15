<?php

namespace App\Http\Composers;

use App\Helpers\Menu\Item;
use App\Helpers\Menu\Menu;
use Illuminate\View\View;

class MenuComposer
{
    /**
     * Construct the front menu.
     *
     * @param  View  $view
     * @return void
     */
    public function front(View $view)
    {
        // the front menu goes here
    }

    /**
     * Construct the admin menu.
     *
     * @param View $view
     */
    public function admin(View $view)
    {
        $user = auth()->user();
        $menu = menu()->make(function (Menu $menu) {
            $menu->add(function (Item $item) {
                $item->name('Dashboard')->url(route('admin'))->data('icon', 'fa-home')->active('admin');
            });

            $menu->add(function ($item) use ($menu) {
                $access = $item->name('Access Control')->data('icon', 'fa-sign-in')->active('admin/admin*');

                $menu->child($access, function (Item $item) {
                    $item->name('Admin Roles')->url(route('admin.admin_roles.index'))->permissions('admin-roles-list')->active('admin/admin-roles/*');
                });

                $menu->child($access, function (Item $item) {
                    $item->name('Admin Users')->url(route('admin.admin_users.index'))->permissions('admin-users-list')->active('admin/admin-users/*');
                });
            });

            $menu->add(function (Item $item) use ($menu) {
                $test = $item->name('Test')->data('icon', 'fa-text-width')->active('admin/test/*');

                $menu->child($test, function (Item $item) {
                    $item->name('Test')->url(route('admin.test.index'))->active('admin/test/*');
                });
            });
        })->filter(function (Item $item) use ($user) {
            return $user->isSuperUser() || $user->hasAnyPermission($item->permissions());
        });

        $view->with('menu', $menu);
    }
}