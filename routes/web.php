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

Route::group([
    'namespace' => 'Admin',
    'prefix' => 'admin',
], function () {
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

    Route::group([
        'middleware' => ['authenticated:admin.login', 'check.roles:admin', 'check.permissions']
    ], function () {
        Route::group([
            'namespace' => 'Home',
        ], function () {
            Route::get('', ['as' => 'admin', 'uses' => 'DashboardController@index']);
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