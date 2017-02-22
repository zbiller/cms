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
    'prefix' => 'admin',
    'namespace' => 'Admin',
], function () {
    Route::group([
        'namespace' => 'Auth'
    ], function () {
        Route::get('login', ['as' => 'admin.show', 'uses' => 'LoginController@show']);
        Route::post('login', ['as' => 'admin.login', 'uses' => 'LoginController@login']);
        Route::post('logout', ['as' => 'admin.logout', 'uses' => 'LoginController@logout']);
    });
});