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
    'middleware' => 'auth:admin'
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
        'middleware' => ['authenticated:admin', 'check.roles:admin', 'check.permissions']
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
         * Version.
         */
        Route::group([
            'namespace' => 'Version',
        ], function () {
            /**
             * Revisions.
             */
            Route::group([
                'prefix' => 'revisions',
            ], function () {
                Route::get('/', ['as' => 'admin.revisions.get', 'uses' => 'RevisionsController@getRevisions', 'permissions' => 'revisions-list']);
                Route::match(['post', 'put'], 'rollback/{revision}', ['as' => 'admin.revisions.rollback', 'uses' => 'RevisionsController@rollbackRevision', 'permissions' => 'revisions-rollback']);
                Route::delete('destroy/{revision}', ['as' => 'admin.revisions.remove', 'uses' => 'RevisionsController@removeRevision', 'permissions' => 'revisions-delete']);
            });

            /**
             * Drafts.
             */
            Route::group([
                'prefix' => 'drafts',
            ], function () {
                Route::get('/', ['as' => 'admin.drafts.get', 'uses' => 'DraftsController@getDrafts', 'permissions' => 'drafts-list']);
                Route::match(['post', 'put'], 'save', ['as' => 'admin.drafts.save', 'uses' => 'DraftsController@saveDraft', 'permissions' => 'drafts-save']);
                Route::match(['post', 'put'], 'create/{draft}', ['as' => 'admin.drafts.create', 'uses' => 'DraftsController@createDraft', 'permissions' => 'drafts-save']);
                Route::match(['post', 'put'], 'update/{draft}', ['as' => 'admin.drafts.update', 'uses' => 'DraftsController@updateDraft', 'permissions' => 'drafts-save']);
                Route::match(['post', 'put'], 'publish/{draft}', ['as' => 'admin.drafts.publish', 'uses' => 'DraftsController@publishDraft', 'permissions' => 'drafts-publish']);
                Route::match(['post', 'put'], 'publish-limbo', ['as' => 'admin.drafts.publish_limbo', 'uses' => 'DraftsController@publishLimboDraft', 'permissions' => 'drafts-publish']);
                Route::delete('remove/{draft}', ['as' => 'admin.drafts.remove', 'uses' => 'DraftsController@removeDraft', 'permissions' => 'drafts-delete']);
                Route::delete('delete-limbo', ['as' => 'admin.drafts.delete_limbo', 'uses' => 'DraftsController@deleteLimboDraft', 'permissions' => 'drafts-delete']);
            });
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

            /**
             * CRUD Activity Log.
             */
            Route::group([
                'prefix' => 'activity-logs',
            ], function () {
                Route::get('/', ['as' => 'admin.activity_logs.index', 'uses' => 'ActivityLogsController@index', 'permissions' => 'activity-logs-list']);
                Route::delete('destroy/{activity}', ['as' => 'admin.activity_logs.destroy', 'uses' => 'ActivityLogsController@destroy', 'permissions' => 'activity-logs-delete']);
                Route::delete('clean', ['as' => 'admin.activity_logs.clean', 'uses' => 'ActivityLogsController@clean', 'permissions' => 'activity-logs-clean']);
                Route::delete('delete', ['as' => 'admin.activity_logs.delete', 'uses' => 'ActivityLogsController@delete', 'permissions' => 'activity-logs-delete']);
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
                Route::get('create/{parent?}', ['as' => 'admin.pages.create', 'uses' => 'PagesController@create', 'permissions' => 'pages-add']);
                Route::get('edit/{page}', ['as' => 'admin.pages.edit', 'uses' => 'PagesController@edit', 'permissions' => 'pages-edit']);
                Route::post('store/{parent?}', ['as' => 'admin.pages.store', 'uses' => 'PagesController@store', 'permissions' => 'pages-add']);
                Route::put('update/{page}', ['as' => 'admin.pages.update', 'uses' => 'PagesController@update', 'permissions' => 'pages-edit']);
                Route::delete('destroy/{page}', ['as' => 'admin.pages.destroy', 'uses' => 'PagesController@destroy', 'permissions' => 'pages-soft-delete']);

                /**
                 * Soft Delete Actions.
                 */
                Route::get('deleted', ['as' => 'admin.pages.deleted', 'uses' => 'PagesController@deleted', 'permissions' => 'pages-deleted']);
                Route::put('restore/{id}', ['as' => 'admin.pages.restore', 'uses' => 'PagesController@restore', 'permissions' => 'pages-restore']);
                Route::delete('delete/{id}', ['as' => 'admin.pages.delete', 'uses' => 'PagesController@delete', 'permissions' => 'pages-force-delete']);

                /**
                 * Duplicate Actions.
                 */
                Route::post('duplicate/{page}', ['as' => 'admin.pages.duplicate', 'uses' => 'PagesController@duplicate', 'permissions' => 'pages-duplicate']);

                /**
                 * Preview Actions.
                 */
                Route::match(['post', 'put'], 'preview/{page?}', ['as' => 'admin.pages.preview', 'uses' => 'PagesController@preview', 'permissions' => 'pages-preview']);

                /**
                 * Draft Actions.
                 */
                Route::get('drafts', ['as' => 'admin.pages.drafts', 'uses' => 'PagesController@drafts', 'permissions' => 'drafts-list']);
                Route::get('draft/{draft}', ['as' => 'admin.pages.draft', 'uses' => 'PagesController@draft', 'permissions' => 'drafts-publish']);
                Route::match(['get', 'put'], 'limbo/{id}', ['as' => 'admin.pages.limbo', 'uses' => 'PagesController@limbo', 'permissions' => 'drafts-save']);

                /**
                 * Revision Actions.
                 */
                Route::get('revision/{revision}', ['as' => 'admin.pages.revision', 'uses' => 'PagesController@revision', 'permissions' => 'revisions-rollback']);

                /**
                 * Ajax Actions.
                 */
                Route::get('get-layouts/{type?}', ['as' => 'admin.pages.get_layouts', 'uses' => 'PagesController@getLayouts']);

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
                Route::get('get', ['as' => 'admin.blocks.get', 'uses' => 'BlocksController@get', 'permissions' => 'blocks-list']);
                Route::get('create/{type?}', ['as' => 'admin.blocks.create', 'uses' => 'BlocksController@create', 'permissions' => 'blocks-add']);
                Route::get('edit/{block}', ['as' => 'admin.blocks.edit', 'uses' => 'BlocksController@edit', 'permissions' => 'blocks-edit']);
                Route::post('row', ['as' => 'admin.blocks.row', 'uses' => 'BlocksController@row', 'permissions' => 'blocks-list']);
                Route::post('store', ['as' => 'admin.blocks.store', 'uses' => 'BlocksController@store', 'permissions' => 'blocks-add']);
                Route::put('update/{block}', ['as' => 'admin.blocks.update', 'uses' => 'BlocksController@update', 'permissions' => 'blocks-edit']);
                Route::delete('destroy/{block}', ['as' => 'admin.blocks.destroy', 'uses' => 'BlocksController@destroy', 'permissions' => 'blocks-soft-delete']);

                /**
                 * Soft Delete Actions.
                 */
                Route::get('deleted', ['as' => 'admin.blocks.deleted', 'uses' => 'BlocksController@deleted', 'permissions' => 'blocks-deleted']);
                Route::put('restore/{id}', ['as' => 'admin.blocks.restore', 'uses' => 'BlocksController@restore', 'permissions' => 'blocks-restore']);
                Route::delete('delete/{id}', ['as' => 'admin.blocks.delete', 'uses' => 'BlocksController@delete', 'permissions' => 'blocks-force-delete']);

                /**
                 * Duplicate Actions.
                 */
                Route::post('duplicate/{block}', ['as' => 'admin.blocks.duplicate', 'uses' => 'BlocksController@duplicate', 'permissions' => 'blocks-duplicate']);

                /**
                 * Draft Actions.
                 */
                Route::get('drafts', ['as' => 'admin.blocks.drafts', 'uses' => 'BlocksController@drafts', 'permissions' => 'drafts-list']);
                Route::get('draft/{draft}', ['as' => 'admin.blocks.draft', 'uses' => 'BlocksController@draft', 'permissions' => 'drafts-publish']);
                Route::match(['get', 'put'], 'limbo/{id}', ['as' => 'admin.blocks.limbo', 'uses' => 'BlocksController@limbo', 'permissions' => 'drafts-save']);

                /**
                 * Revision Actions.
                 */
                Route::get('revision/{revision}', ['as' => 'admin.blocks.revision', 'uses' => 'BlocksController@revision', 'permissions' => 'revisions-rollback']);
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
                Route::get('download/{upload}', ['as' => 'admin.uploads.download', 'uses' => 'UploadsController@download', 'permissions' => 'uploads-download']);
                Route::get('get/{type?}', ['as' => 'admin.uploads.get', 'uses' => 'UploadsController@get', 'permissions' => 'uploads-list']);
                Route::get('crop', ['as' => 'admin.uploads.crop', 'uses' => 'UploadsController@crop', 'permissions' => 'uploads-crop']);
                Route::post('store', ['as' => 'admin.uploads.store', 'uses' => 'UploadsController@store', 'permissions' => 'uploads-upload']);
                Route::post('upload', ['as' => 'admin.uploads.upload', 'uses' => 'UploadsController@upload', 'permissions' => 'uploads-upload']);
                Route::post('set', ['as' => 'admin.uploads.set', 'uses' => 'UploadsController@set', 'permissions' => 'uploads-select']);
                Route::post('cut', ['as' => 'admin.uploads.cut', 'uses' => 'UploadsController@cut', 'permissions' => 'uploads-crop']);
                Route::delete('destroy/{upload}', ['as' => 'admin.uploads.destroy', 'uses' => 'UploadsController@destroy', 'permissions' => 'uploads-delete']);
            });

            /**
             * CRUD Emails.
             */
            Route::group([
                'prefix' => 'emails',
            ], function () {
                Route::get('/', ['as' => 'admin.emails.index', 'uses' => 'EmailsController@index', 'permissions' => 'emails-list']);
                Route::get('create/{type?}', ['as' => 'admin.emails.create', 'uses' => 'EmailsController@create', 'permissions' => 'emails-add']);
                Route::get('edit/{email}', ['as' => 'admin.emails.edit', 'uses' => 'EmailsController@edit', 'permissions' => 'emails-edit']);
                Route::post('store', ['as' => 'admin.emails.store', 'uses' => 'EmailsController@store', 'permissions' => 'emails-add']);
                Route::put('update/{email}', ['as' => 'admin.emails.update', 'uses' => 'EmailsController@update', 'permissions' => 'emails-edit']);
                Route::delete('destroy/{email}', ['as' => 'admin.emails.destroy', 'uses' => 'EmailsController@destroy', 'permissions' => 'emails-soft-delete']);

                /**
                 * Soft Delete Actions.
                 */
                Route::get('deleted', ['as' => 'admin.emails.deleted', 'uses' => 'EmailsController@deleted', 'permissions' => 'emails-deleted']);
                Route::put('restore/{id}', ['as' => 'admin.emails.restore', 'uses' => 'EmailsController@restore', 'permissions' => 'emails-restore']);
                Route::delete('delete/{id}', ['as' => 'admin.emails.delete', 'uses' => 'EmailsController@delete', 'permissions' => 'emails-force-delete']);

                /**
                 * Duplicate Actions.
                 */
                Route::post('duplicate/{email}', ['as' => 'admin.emails.duplicate', 'uses' => 'EmailsController@duplicate', 'permissions' => 'emails-duplicate']);

                /**
                 * Preview Actions.
                 */
                Route::match(['post', 'put'], 'preview/{email?}', ['as' => 'admin.emails.preview', 'uses' => 'EmailsController@preview', 'permissions' => 'emails-preview']);

                /**
                 * Draft Actions.
                 */
                Route::get('drafts', ['as' => 'admin.emails.drafts', 'uses' => 'EmailsController@drafts', 'permissions' => 'drafts-list']);
                Route::get('draft/{draft}', ['as' => 'admin.emails.draft', 'uses' => 'EmailsController@draft', 'permissions' => 'drafts-publish']);
                Route::match(['get', 'put'], 'limbo/{id}', ['as' => 'admin.emails.limbo', 'uses' => 'EmailsController@limbo', 'permissions' => 'drafts-save']);

                /**
                 * Revision Actions.
                 */
                Route::get('revision/{revision}', ['as' => 'admin.emails.revision', 'uses' => 'EmailsController@revision', 'permissions' => 'revisions-rollback']);
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
            Route::get('draft/{draft}', ['as' => 'admin.cars.draft', 'uses' => 'CarsController@draft', 'pages-edit']);
            Route::post('store', ['as' => 'admin.cars.store', 'uses' => 'CarsController@store']);
            Route::put('update/{id}', ['as' => 'admin.cars.update', 'uses' => 'CarsController@update']);
            Route::delete('destroy/{id}', ['as' => 'admin.cars.destroy', 'uses' => 'CarsController@destroy']);
            Route::get('revision/{revision}', ['as' => 'admin.cars.revision', 'uses' => 'CarsController@revision']);
        });
    });
});

/**
 * Front Routes.
 */
Route::group([
    'namespace' => 'Front',
    'middleware' => 'auth:user'
], function () {
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
     * Dynamic Routes.
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
});