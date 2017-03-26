<?php

namespace App\Http\Composers;

use App\Helpers\MenuItem;
use App\Helpers\MenuHelper;
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
        $menu = menu()->make(function (MenuHelper $menu) {
            $menu->add(function (MenuItem $item) {
                $item->name('Dashboard')->url(route('admin'))->data('icon', 'fa-home')->active('admin');
            });

            $menu->add(function ($item) use ($menu) {
                $content = $item->name('Manage Content')->data('icon', 'fa-pencil-square-o')->active('admin/library/*');

                $menu->child($content, function (MenuItem $item) {
                    $item->name('Library')->url(route('admin.library.index'))->permissions('library-list')->active('admin/library/*');
                });
            });

            $menu->add(function ($item) use ($menu) {
                $access = $item->name('Access Control')->data('icon', 'fa-sign-in')->active('admin/admin-roles/*', 'admin/admin-users/*');

                $menu->child($access, function (MenuItem $item) {
                    $item->name('Admin Roles')->url(route('admin.admin_roles.index'))->permissions('admin-roles-list')->active('admin/admin-roles/*');
                });

                $menu->child($access, function (MenuItem $item) {
                    $item->name('Admin Users')->url(route('admin.admin_users.index'))->permissions('admin-users-list')->active('admin/admin-users/*');
                });
            });


            $menu->add(function (MenuItem $item) use ($menu) {
                $test = $item->name('Test')->data('icon', 'fa-text-width')->active('admin/test/*');

                $menu->child($test, function (MenuItem $item) {
                    $item->name('Test')->url(route('admin.test.index'))->active('admin/test/*');
                });
            });
        })->filter(function (MenuItem $item) use ($user) {
            return $user->isSuperUser() || $user->hasAnyPermission($item->permissions());
        });

        $view->with('menu', $menu);
    }
}