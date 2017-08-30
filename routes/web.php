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
 * Auth routes.
 */
Route::group([
    'namespace' => 'Auth',
    'middleware' => 'not.authenticated:user'
], function () {
    Route::get('login', ['as' => 'login', 'uses' => 'LoginController@show']);
    Route::post('login', ['uses' => 'LoginController@login']);
    Route::post('logout', ['as' => 'logout', 'uses' => 'LoginController@logout']);

    Route::get('register', ['as' => 'register', 'uses' => 'RegisterController@show']);
    Route::post('register', ['uses' => 'RegisterController@register']);
    Route::get('register/verify/{token}/{email}', ['as' => 'register.verify', 'uses' => 'RegisterController@verify']);

    Route::get('forgot-password', ['as' => 'password.forgot', 'uses' => 'ForgotPasswordController@show']);
    Route::post('forgot-password', ['uses' => 'ForgotPasswordController@sendResetLinkEmail']);

    Route::get('reset-password/{token}', ['as' => 'password.change', 'uses' => 'ResetPasswordController@show']);
    Route::post('reset-password', ['as' => 'password.reset', 'uses' => 'ResetPasswordController@reset']);
});

/**
 * Dynamic routes.
 */
Route::get('{url}', function ($url = '/') {
    try {
        $url = \App\Models\Cms\Url::whereUrl($url)->firstOrFail();

        if ($model = $url->urlable) {
            return (new Illuminate\Routing\ControllerDispatcher(app()))->dispatch(app(Illuminate\Routing\Route::class)->setAction([
                'model' => $model
            ]), app($model->getUrlOptions()->routeController), $model->getUrlOptions()->routeAction);
        } else {
            abort(404);
        }
    } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
        abort(404);
    }
})->where('url', '(.*)');