<?php

namespace App\Http\Composers;

use App\Helpers\MenuHelper;
use App\Helpers\MenuItem;
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
                $content = $item->name('Manage Content')->data('icon', 'fa-pencil-square-o')->active('admin/pages/*', 'admin/menus/*', 'admin/blocks/*', 'admin/layouts/*', 'admin/uploads/*', 'admin/emails/*');

                $menu->child($content, function (MenuItem $item) {
                    $item->name('Pages')->url(route('admin.pages.index'))->permissions('pages-list')->active('admin/pages/*');
                });

                $menu->child($content, function (MenuItem $item) {
                    $item->name('Menus')->url(route('admin.menus.locations'))->permissions('menus-list')->active('admin/menus/*');
                });

                $menu->child($content, function (MenuItem $item) {
                    $item->name('Blocks')->url(route('admin.blocks.index'))->permissions('blocks-list')->active('admin/blocks/*');
                });

                $menu->child($content, function (MenuItem $item) {
                    $item->name('Emails')->url(route('admin.emails.index'))->permissions('emails-list')->active('admin/emails/*');
                });

                $menu->child($content, function (MenuItem $item) {
                    $item->name('Layouts')->url(route('admin.layouts.index'))->permissions('layouts-list')->active('admin/layouts/*');
                });

                $menu->child($content, function (MenuItem $item) {
                    $item->name('Uploads')->url(route('admin.uploads.index'))->permissions('uploads-list')->active('admin/uploads/*');
                });
            });

            $menu->add(function ($item) use ($menu) {
                $access = $item->name('Access Control')->data('icon', 'fa-sign-in')->active('admin/admin-roles/*', 'admin/admin-users/*', 'admin/approvals/*', 'admin/activity-logs/*');

                $menu->child($access, function (MenuItem $item) {
                    $item->name('Admin Users')->url(route('admin.admin_users.index'))->permissions('admin-users-list')->active('admin/admin-users/*');
                });

                $menu->child($access, function (MenuItem $item) {
                    $item->name('Admin Roles')->url(route('admin.admin_roles.index'))->permissions('admin-roles-list')->active('admin/admin-roles/*');
                });

                $menu->child($access, function (MenuItem $item) {
                    $item->name('Activity Logs')->url(route('admin.activity_logs.index'))->permissions('activity-log-list')->active('admin/activity-logs/*');
                });
            });

            $menu->add(function (MenuItem $item) use ($menu) {
                $test = $item->name('Test')->data('icon', 'fa-text-width')->active('admin/cars/*');

                $menu->child($test, function (MenuItem $item) {
                    $item->name('Cars')->url(route('admin.cars.index'))->active('admin/cars/*');
                });
            });
        })->filter(function (MenuItem $item) use ($user) {
            return $user->isSuperUser() || $user->hasAnyPermission($item->permissions());
        });

        $view->with('menu', $menu);
    }
}