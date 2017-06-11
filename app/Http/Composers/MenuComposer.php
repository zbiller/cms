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
                $access = $item->name('Access Control')->data('icon', 'fa-sign-in')->active('admin/users/*', 'admin/admins/*', 'admin/roles/*', 'admin/activity/*');

                $menu->child($access, function (MenuItem $item) {
                    $item->name('Users')->url(route('admin.users.index'))->permissions('users-list')->active('admin/users/*');
                });

                $menu->child($access, function (MenuItem $item) {
                    $item->name('Admins')->url(route('admin.admins.index'))->permissions('admins-list')->active('admin/admins/*');
                });

                $menu->child($access, function (MenuItem $item) {
                    $item->name('Roles')->url(route('admin.roles.index'))->permissions('roles-list')->active('admin/roles/*');
                });

                $menu->child($access, function (MenuItem $item) {
                    $item->name('Activity')->url(route('admin.activity.index'))->permissions('activity-list')->active('admin/activity/*');
                });
            });

            $menu->add(function ($item) use ($menu) {
                $access = $item->name('System Settings')->data('icon', 'fa-cog')->active('admin/settings/*');

                $menu->child($access, function (MenuItem $item) {
                    $item->name('Company')->url(route('admin.settings.company'))->permissions('settings-company')->active('admin/settings/company/*');
                });

                $menu->child($access, function (MenuItem $item) {
                    $item->name('Analytics')->url(route('admin.settings.analytics'))->permissions('settings-analytics')->active('admin/settings/analytics/*');
                });
            });

            /*$menu->add(function (MenuItem $item) use ($menu) {
                $test = $item->name('Test')->data('icon', 'fa-text-width')->active('admin/cars/*');

                $menu->child($test, function (MenuItem $item) {
                    $item->name('Cars')->url(route('admin.cars.index'))->active('admin/cars/*');
                });
            });*/
        })->filter(function (MenuItem $item) use ($user) {
            return $user->isSuper() || $user->hasAnyPermission($item->permissions());
        });

        $view->with('menu', $menu);
    }
}