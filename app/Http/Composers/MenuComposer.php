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
                $shop = $item->name('Shop Panel')->data('icon', 'fa-shopping-cart')->active('admin/products/*', 'admin/categories/*', 'admin/sets/*', 'admin/attributes/*', 'admin/discounts/*', 'admin/taxes/*');

                $menu->child($shop, function (MenuItem $item) {
                    $item->name('Products')->url(route('admin.products.index'))->permissions('products-list')->active('admin/products/*');
                });

                $menu->child($shop, function (MenuItem $item) {
                    $item->name('Categories')->url(route('admin.categories.index'))->permissions('categories-list')->active('admin/categories/*');
                });

                $menu->child($shop, function (MenuItem $item) {
                    $item->name('Attributes')->url(route('admin.sets.index'))->permissions('sets-list')->active('admin/sets/*', 'admin/attributes/*');
                });

                $menu->child($shop, function (MenuItem $item) {
                    $item->name('Discounts')->url(route('admin.discounts.index'))->permissions('discounts-list')->active('admin/discounts/*');
                });

                $menu->child($shop, function (MenuItem $item) {
                    $item->name('Taxes')->url(route('admin.taxes.index'))->permissions('taxes-list')->active('admin/taxes/*');
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
                $access = $item->name('Geo Location')->data('icon', 'fa-globe')->active('admin/countries/*', 'admin/states/*', 'admin/cities/*');

                $menu->child($access, function (MenuItem $item) {
                    $item->name('Countries')->url(route('admin.countries.index'))->permissions('countries-list')->active('admin/countries/*');
                });

                $menu->child($access, function (MenuItem $item) {
                    $item->name('States')->url(route('admin.states.index'))->permissions('states-list')->active('admin/states/*');
                });

                $menu->child($access, function (MenuItem $item) {
                    $item->name('Cities')->url(route('admin.cities.index'))->permissions('cities-list')->active('admin/cities/*');
                });
            });

            $menu->add(function ($item) use ($menu) {
                $access = $item->name('System Settings')->data('icon', 'fa-cog')->active('admin/settings/*');

                $menu->child($access, function (MenuItem $item) {
                    $item->name('General')->url(route('admin.settings.general'))->permissions('settings-general')->active('admin/settings/general/*');
                });

                $menu->child($access, function (MenuItem $item) {
                    $item->name('Analytics')->url(route('admin.settings.analytics'))->permissions('settings-analytics')->active('admin/settings/analytics/*');
                });
            });
        })->filter(function (MenuItem $item) use ($user) {
            return $user->isSuper() || $user->hasAnyPermission($item->permissions());
        });

        $view->with('menu', $menu);
    }
}