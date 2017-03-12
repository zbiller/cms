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
            Route::get('', ['as' => 'admin', 'uses' => 'DashboardController@index']);
        });

        /**
         * CRUD admin users.
         */
        Route::group([
            'namespace' => 'Admin',
        ], function () {
            Route::group([
                'prefix' => 'admin-groups',
            ], function () {
                Route::get('/', ['as' => 'admin.admin.groups.index', 'uses' => 'GroupsController@index', 'permissions' => 'admin-groups-list']);
                Route::get('create', ['as' => 'admin.admin.groups.create', 'uses' => 'GroupsController@create', 'permissions' => 'admin-groups-add']);
                Route::get('edit/{id}', ['as' => 'admin.admin.groups.edit', 'uses' => 'GroupsController@edit', 'permissions' => 'admin-groups-edit']);
                Route::post('store', ['as' => 'admin.admin.groups.store', 'uses' => 'GroupsController@store', 'permissions' => 'admin-groups-add']);
                Route::put('update/{id}', ['as' => 'admin.admin.groups.update', 'uses' => 'GroupsController@update', 'permissions' => 'admin-groups-edit']);
                Route::delete('destroy/{id}', ['as' => 'admin.admin.groups.destroy', 'uses' => 'GroupsController@destroy', 'permissions' => 'admin-groups-delete']);
            });

            Route::group([
                'prefix' => 'admin-users',
            ], function () {
                Route::get('/', ['as' => 'admin.admin.users.index', 'uses' => 'UsersController@index', 'permissions' => 'admin-users-list']);
                Route::get('create', ['as' => 'admin.admin.users.create', 'uses' => 'UsersController@create', 'permissions' => 'admin-users-add']);
                Route::get('edit/{id}', ['as' => 'admin.admin.users.edit', 'uses' => 'UsersController@edit', 'permissions' => 'admin-users-edit']);
                Route::post('store', ['as' => 'admin.admin.users.store', 'uses' => 'UsersController@store', 'permissions' => 'admin-users-add']);
                Route::put('update/{id}', ['as' => 'admin.admin.users.update', 'uses' => 'UsersController@update', 'permissions' => 'admin-users-edit']);
                Route::delete('destroy/{id}', ['as' => 'admin.admin.users.destroy', 'uses' => 'UsersController@destroy', 'permissions' => 'admin-users-delete']);
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