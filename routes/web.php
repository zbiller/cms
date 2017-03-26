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
 * All of the admin routes.
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
                Route::get('edit/{id}', ['as' => 'admin.admin_roles.edit', 'uses' => 'AdminRolesController@edit', 'permissions' => 'admin-roles-edit']);
                Route::post('store', ['as' => 'admin.admin_roles.store', 'uses' => 'AdminRolesController@store', 'permissions' => 'admin-roles-add']);
                Route::put('update/{id}', ['as' => 'admin.admin_roles.update', 'uses' => 'AdminRolesController@update', 'permissions' => 'admin-roles-edit']);
                Route::delete('destroy/{id}', ['as' => 'admin.admin_roles.destroy', 'uses' => 'AdminRolesController@destroy', 'permissions' => 'admin-roles-delete']);
            });

            /**
             * CRUD Admin Users
             */
            Route::group([
                'prefix' => 'admin-users',
            ], function () {
                Route::get('/', ['as' => 'admin.admin_users.index', 'uses' => 'AdminUsersController@index', 'permissions' => 'admin-users-list']);
                Route::get('create', ['as' => 'admin.admin_users.create', 'uses' => 'AdminUsersController@create', 'permissions' => 'admin-users-add']);
                Route::get('edit/{id}', ['as' => 'admin.admin_users.edit', 'uses' => 'AdminUsersController@edit', 'permissions' => 'admin-users-edit']);
                Route::post('store', ['as' => 'admin.admin_users.store', 'uses' => 'AdminUsersController@store', 'permissions' => 'admin-users-add']);
                Route::put('update/{id}', ['as' => 'admin.admin_users.update', 'uses' => 'AdminUsersController@update', 'permissions' => 'admin-users-edit']);
                Route::delete('destroy/{id}', ['as' => 'admin.admin_users.destroy', 'uses' => 'AdminUsersController@destroy', 'permissions' => 'admin-users-delete']);
            });
        });

        /**
         * Manage Content.
         */
        Route::group([
            'namespace' => 'Cms',
        ], function () {
            /**
             * CRUD Library
             */
            Route::group([
                'prefix' => 'library',
            ], function () {
                Route::get('/', ['as' => 'admin.library.index', 'uses' => 'LibraryController@index', 'permissions' => 'library-list']);
                Route::get('show/{id}', ['as' => 'admin.library.show', 'uses' => 'LibraryController@show', 'permissions' => 'library-list']);
                Route::get('download/{id}', ['as' => 'admin.library.download', 'uses' => 'LibraryController@download', 'permissions' => 'library-edit']);
                Route::post('store', ['as' => 'admin.library.store', 'uses' => 'LibraryController@store', 'permissions' => 'library-add']);
                Route::delete('destroy/{id}', ['as' => 'admin.library.destroy', 'uses' => 'LibraryController@destroy', 'permissions' => 'library-delete']);

                /*Route::get('edit/{id}', ['as' => 'admin.admin_users.edit', 'uses' => 'AdminUsersController@edit', 'permissions' => 'admin-users-edit']);
                Route::post('store', ['as' => 'admin.admin_users.store', 'uses' => 'AdminUsersController@store', 'permissions' => 'admin-users-add']);
                Route::put('update/{id}', ['as' => 'admin.admin_users.update', 'uses' => 'AdminUsersController@update', 'permissions' => 'admin-users-edit']);
                Route::delete('destroy/{id}', ['as' => 'admin.admin_users.destroy', 'uses' => 'AdminUsersController@destroy', 'permissions' => 'admin-users-delete']);*/
            });
        });









        Route::group([
            'namespace' => 'Test',
            'prefix' => 'test',
        ], function () {
            Route::get('/', ['as' => 'admin.test.index', 'uses' => 'TestController@index']);
            Route::get('create', ['as' => 'admin.test.create', 'uses' => 'TestController@create']);
            Route::get('edit/{id}', ['as' => 'admin.test.edit', 'uses' => 'TestController@edit']);
            Route::post('store', ['as' => 'admin.test.store', 'uses' => 'TestController@store']);
            Route::put('update/{id}', ['as' => 'admin.test.update', 'uses' => 'TestController@update']);
            Route::delete('destroy/{id}', ['as' => 'admin.test.destroy', 'uses' => 'TestController@destroy']);
        });
    });
});

Route::get('/', function () {
    return '';
})->name('home');