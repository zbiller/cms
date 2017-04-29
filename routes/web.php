<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

/**
 * Admin Routes.
 */
Route::group([
    'namespace' => 'Admin',
    'prefix' => 'admin',
], function () {
    /**
     * Admin routes that don't require authentication.
     */
    Route::group([
        'namespace' => 'Auth',
        'middleware' => 'not.authenticated:admin'
    ], function () {
        Route::get('login', ['as' => 'admin.login', 'uses' => 'LoginController@show']);
        Route::post('login', ['uses' => 'LoginController@login']);
        Route::post('logout', ['as' => 'admin.logout', 'uses' => 'LoginController@logout']);
        Route::get('forgot-password', ['as' => 'admin.password.forgot', 'uses' => 'ForgotPasswordController@show']);
        Route::post('forgot-password', ['uses' => 'ForgotPasswordController@sendResetLinkEmail']);
        Route::get('reset-password/{token}', ['as' => 'admin.password.change', 'uses' => 'ResetPasswordController@show']);
        Route::post('reset-password', ['as' => 'admin.password.reset', 'uses' => 'ResetPasswordController@reset']);
    });

    /**
     * Admin routes that require authentication.
     */
    Route::group([
        'middleware' => ['authenticated:admin.login', 'check.roles:admin', 'check.permissions']
    ], function () {
        /**
         * Dashboard.
         */
        Route::group([
            'namespace' => 'Home',
        ], function () {
            Route::get('/', ['as' => 'admin', 'uses' => 'DashboardController@index']);
        });

        /**
         * Access Control Level.
         */
        Route::group([
            'namespace' => 'Acl',
        ], function () {
            /**
             * CRUD Admin Groups.
             */
            Route::group([
                'prefix' => 'admin-roles',
            ], function () {
                Route::get('/', ['as' => 'admin.admin_roles.index', 'uses' => 'AdminRolesController@index', 'permissions' => 'admin-roles-list']);
                Route::get('create', ['as' => 'admin.admin_roles.create', 'uses' => 'AdminRolesController@create', 'permissions' => 'admin-roles-add']);
                Route::get('edit/{role}', ['as' => 'admin.admin_roles.edit', 'uses' => 'AdminRolesController@edit', 'permissions' => 'admin-roles-edit']);
                Route::post('store', ['as' => 'admin.admin_roles.store', 'uses' => 'AdminRolesController@store', 'permissions' => 'admin-roles-add']);
                Route::put('update/{role}', ['as' => 'admin.admin_roles.update', 'uses' => 'AdminRolesController@update', 'permissions' => 'admin-roles-edit']);
                Route::delete('destroy/{role}', ['as' => 'admin.admin_roles.destroy', 'uses' => 'AdminRolesController@destroy', 'permissions' => 'admin-roles-delete']);
            });

            /**
             * CRUD Admin Users.
             */
            Route::group([
                'prefix' => 'admin-users',
            ], function () {
                Route::get('/', ['as' => 'admin.admin_users.index', 'uses' => 'AdminUsersController@index', 'permissions' => 'admin-users-list']);
                Route::get('create', ['as' => 'admin.admin_users.create', 'uses' => 'AdminUsersController@create', 'permissions' => 'admin-users-add']);
                Route::get('edit/{user}', ['as' => 'admin.admin_users.edit', 'uses' => 'AdminUsersController@edit', 'permissions' => 'admin-users-edit']);
                Route::post('store', ['as' => 'admin.admin_users.store', 'uses' => 'AdminUsersController@store', 'permissions' => 'admin-users-add']);
                Route::put('update/{user}', ['as' => 'admin.admin_users.update', 'uses' => 'AdminUsersController@update', 'permissions' => 'admin-users-edit']);
                Route::delete('destroy/{user}', ['as' => 'admin.admin_users.destroy', 'uses' => 'AdminUsersController@destroy', 'permissions' => 'admin-users-delete']);
            });
        });

        /**
         * Manage Content.
         */
        Route::group([
            'namespace' => 'Cms',
        ], function () {
            /**
             * CRUD Pages.
             */
            Route::group([
                'prefix' => 'pages',
            ], function () {
                Route::get('/', ['as' => 'admin.pages.index', 'uses' => 'PagesController@index', 'permissions' => 'pages-list']);
                Route::get('deleted', ['as' => 'admin.pages.deleted', 'uses' => 'PagesController@deleted', 'permissions' => 'pages-deleted-list']);
                Route::get('create/{parent?}', ['as' => 'admin.pages.create', 'uses' => 'PagesController@create', 'permissions' => 'pages-add']);
                Route::get('edit/{page}', ['as' => 'admin.pages.edit', 'uses' => 'PagesController@edit', 'permissions' => 'pages-edit']);
                Route::post('store/{parent?}', ['as' => 'admin.pages.store', 'uses' => 'PagesController@store', 'permissions' => 'pages-add']);
                Route::post('duplicate/{page}', ['as' => 'admin.pages.duplicate', 'uses' => 'PagesController@duplicate', 'permissions' => 'pages-edit']);
                Route::put('update/{page}', ['as' => 'admin.pages.update', 'uses' => 'PagesController@update', 'permissions' => 'pages-edit']);
                Route::put('restore/{id}', ['as' => 'admin.pages.restore', 'uses' => 'PagesController@restore', 'permissions' => 'pages-deleted-edit']);
                Route::delete('destroy/{page}', ['as' => 'admin.pages.destroy', 'uses' => 'PagesController@destroy', 'permissions' => 'pages-delete']);
                Route::delete('delete/{id}', ['as' => 'admin.pages.delete', 'uses' => 'PagesController@delete', 'permissions' => 'pages-deleted-delete']);

                /**
                 * Tree Actions.
                 */
                Route::group([
                    'prefix' => 'tree'
                ], function () {
                    Route::get('fix', ['as' => 'admin.pages.tree.fix', 'uses' => 'PagesController@fixTree', 'acl' => 'pages-list']);
                    Route::get('load/{parent?}', ['as' => 'admin.pages.tree.load', 'uses' => 'PagesController@loadTreeNodes', 'acl' => 'pages-list']);
                    Route::get('list/{parent?}', ['as' => 'admin.pages.tree.list', 'uses' => 'PagesController@listTreeItems', 'acl' => 'pages-list']);
                    Route::post('sort', ['as' => 'admin.pages.tree.sort', 'uses' => 'PagesController@sortTreeItems', 'acl' => 'pages-list']);
                    Route::post('url', ['as' => 'admin.pages.tree.url', 'uses' => 'PagesController@refreshTreeItemsUrls', 'acl' => 'pages-list']);
                });
            });

            /**
             * CRUD Menus.
             */
            Route::group([
                'prefix' => 'menus',
            ], function () {
                Route::get('locations', ['as' => 'admin.menus.locations', 'uses' => 'MenusController@locations', 'permissions' => 'menus-list']);
                Route::get('{location}', ['as' => 'admin.menus.index', 'uses' => 'MenusController@index', 'permissions' => 'menus-list']);
                Route::get('{location}/create/{parent?}', ['as' => 'admin.menus.create', 'uses' => 'MenusController@create', 'permissions' => 'menus-add']);
                Route::get('{location}/edit/{menu}', ['as' => 'admin.menus.edit', 'uses' => 'MenusController@edit', 'permissions' => 'menus-edit']);
                Route::post('{location}/store/{parent?}', ['as' => 'admin.menus.store', 'uses' => 'MenusController@store', 'permissions' => 'menus-add']);
                Route::put('{location}/update/{menu}', ['as' => 'admin.menus.update', 'uses' => 'MenusController@update', 'permissions' => 'menus-edit']);
                Route::delete('{location}/destroy/{menu}', ['as' => 'admin.menus.destroy', 'uses' => 'MenusController@destroy', 'permissions' => 'menus-delete']);
                Route::get('entity/{type?}', ['as' => 'admin.menus.entity', 'uses' => 'MenusController@entity', 'permissions' => 'menus-list']);

                /**
                 * Tree Actions.
                 */
                Route::group([
                    'prefix' => 'tree'
                ], function () {
                    Route::get('fix', ['as' => 'admin.menus.tree.fix', 'uses' => 'MenusController@fixTree', 'acl' => 'menus-list']);
                    Route::get('{location}/load/{parent?}', ['as' => 'admin.menus.tree.load', 'uses' => 'MenusController@loadTreeNodes', 'acl' => 'menus-list']);
                    Route::get('{location}/list/{parent?}', ['as' => 'admin.menus.tree.list', 'uses' => 'MenusController@listTreeItems', 'acl' => 'menus-list']);
                    Route::post('sort', ['as' => 'admin.menus.tree.sort', 'uses' => 'MenusController@sortTreeItems', 'acl' => 'menus-list']);
                });
            });

            /**
             * CRUD Blocks.
             */
            Route::group([
                'prefix' => 'blocks',
            ], function () {
                Route::get('/', ['as' => 'admin.blocks.index', 'uses' => 'BlocksController@index', 'permissions' => 'blocks-list']);
                Route::get('create/{type?}', ['as' => 'admin.blocks.create', 'uses' => 'BlocksController@create', 'permissions' => 'blocks-add']);
                Route::get('edit/{block}', ['as' => 'admin.blocks.edit', 'uses' => 'BlocksController@edit', 'permissions' => 'blocks-edit']);
                Route::post('store', ['as' => 'admin.blocks.store', 'uses' => 'BlocksController@store', 'permissions' => 'blocks-add']);
                Route::put('update/{block}', ['as' => 'admin.blocks.update', 'uses' => 'BlocksController@update', 'permissions' => 'blocks-edit']);
                Route::delete('destroy/{block}', ['as' => 'admin.blocks.destroy', 'uses' => 'BlocksController@destroy', 'permissions' => 'blocks-delete']);
                Route::post('assign', ['as' => 'admin.blocks.assign', 'uses' => 'BlocksController@assign', 'permissions' => 'blocks-edit']);
                Route::post('unassign', ['as' => 'admin.blocks.unassign', 'uses' => 'BlocksController@unassign', 'permissions' => 'blocks-edit']);
                Route::post('order', ['as' => 'admin.blocks.order', 'uses' => 'BlocksController@order', 'permissions' => 'blocks-list']);
            });

            /**
             * CRUD Layouts.
             */
            Route::group([
                'prefix' => 'layouts',
            ], function () {
                Route::get('/', ['as' => 'admin.layouts.index', 'uses' => 'LayoutsController@index', 'permissions' => 'layouts-list']);
                Route::get('create', ['as' => 'admin.layouts.create', 'uses' => 'LayoutsController@create', 'permissions' => 'layouts-add']);
                Route::get('edit/{layout}', ['as' => 'admin.layouts.edit', 'uses' => 'LayoutsController@edit', 'permissions' => 'layouts-edit']);
                Route::post('store', ['as' => 'admin.layouts.store', 'uses' => 'LayoutsController@store', 'permissions' => 'layouts-add']);
                Route::put('update/{layout}', ['as' => 'admin.layouts.update', 'uses' => 'LayoutsController@update', 'permissions' => 'layouts-edit']);
                Route::delete('destroy/{layout}', ['as' => 'admin.layouts.destroy', 'uses' => 'LayoutsController@destroy', 'permissions' => 'layouts-delete']);
            });

            /**
             * CRUD Uploads.
             */
            Route::group([
                'prefix' => 'uploads',
            ], function () {
                Route::get('/', ['as' => 'admin.uploads.index', 'uses' => 'UploadsController@index', 'permissions' => 'uploads-list']);
                Route::get('show/{upload}', ['as' => 'admin.uploads.show', 'uses' => 'UploadsController@show', 'permissions' => 'uploads-list']);
                Route::get('download/{upload}', ['as' => 'admin.uploads.download', 'uses' => 'UploadsController@download', 'permissions' => 'uploads-edit']);
                Route::get('get/{type?}', ['as' => 'admin.uploads.get', 'uses' => 'UploadsController@get', 'permissions' => 'uploads-list']);
                Route::get('crop', ['as' => 'admin.uploads.crop', 'uses' => 'UploadsController@crop', 'permissions' => 'uploads-edit']);
                Route::post('store', ['as' => 'admin.uploads.store', 'uses' => 'UploadsController@store', 'permissions' => 'uploads-add']);
                Route::post('upload', ['as' => 'admin.uploads.upload', 'uses' => 'UploadsController@upload', 'permissions' => 'uploads-add']);
                Route::post('set', ['as' => 'admin.uploads.set', 'uses' => 'UploadsController@set', 'permissions' => 'uploads-edit']);
                Route::post('cut', ['as' => 'admin.uploads.cut', 'uses' => 'UploadsController@cut', 'permissions' => 'uploads-edit']);
                Route::delete('destroy/{upload}', ['as' => 'admin.uploads.destroy', 'uses' => 'UploadsController@destroy', 'permissions' => 'uploads-delete']);
                Route::delete('delete', ['as' => 'admin.uploads.delete', 'uses' => 'UploadsController@delete', 'permissions' => 'uploads-delete']);
            });
        });

        /**
         * CRUD Test (Cars)
         */
        Route::group([
            'namespace' => 'Test',
            'prefix' => 'cars',
        ], function () {
            Route::get('/', ['as' => 'admin.cars.index', 'uses' => 'CarsController@index']);
            Route::get('create', ['as' => 'admin.cars.create', 'uses' => 'CarsController@create']);
            Route::get('edit/{id}', ['as' => 'admin.cars.edit', 'uses' => 'CarsController@edit']);
            Route::post('store', ['as' => 'admin.cars.store', 'uses' => 'CarsController@store']);
            Route::put('update/{id}', ['as' => 'admin.cars.update', 'uses' => 'CarsController@update']);
            Route::delete('destroy/{id}', ['as' => 'admin.cars.destroy', 'uses' => 'CarsController@destroy']);
        });
    });
});

/**
 * Page Routes.
 */
foreach (page()->query()->active()->defaultOrder()->get() as $page) {
    Route::get($page->url->url, [
        'as' => $page->routeName,
        'uses' => $page->routeController . '@' . $page->routeAction,
        'model' => $page
    ]);
}