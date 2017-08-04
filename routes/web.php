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
        'middleware' => ['authenticated:admin', 'check.roles', 'check.permissions']
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
         * Manage Content.
         */
        Route::group([
            'namespace' => 'Cms',
        ], function () {
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
         * Shop Panel.
         */
        Route::group([
            'namespace' => 'Shop',
        ], function () {
            /**
             * CRUD Product.
             */
            Route::group([
                'prefix' => 'products',
            ], function () {
                Route::get('/', ['as' => 'admin.products.index', 'uses' => 'ProductsController@index', 'permissions' => 'products-list']);
                Route::get('create', ['as' => 'admin.products.create', 'uses' => 'ProductsController@create', 'permissions' => 'products-add']);
                Route::get('edit/{product}', ['as' => 'admin.products.edit', 'uses' => 'ProductsController@edit', 'permissions' => 'products-edit']);
                Route::post('store', ['as' => 'admin.products.store', 'uses' => 'ProductsController@store', 'permissions' => 'products-add']);
                Route::put('update/{product}', ['as' => 'admin.products.update', 'uses' => 'ProductsController@update', 'permissions' => 'products-edit']);
                Route::delete('destroy/{product}', ['as' => 'admin.products.destroy', 'uses' => 'ProductsController@destroy', 'permissions' => 'products-soft-delete']);

                /**
                 * Image Upload.
                 */
                Route::post('upload', ['as' => 'admin.products.upload', 'uses' => 'ProductsController@upload', 'permissions' => 'uploads-upload']);

                /**
                 * Soft Delete Actions.
                 */
                Route::get('deleted', ['as' => 'admin.products.deleted', 'uses' => 'ProductsController@deleted', 'permissions' => 'products-deleted']);
                Route::put('restore/{id}', ['as' => 'admin.products.restore', 'uses' => 'ProductsController@restore', 'permissions' => 'products-restore']);
                Route::delete('delete/{id}', ['as' => 'admin.products.delete', 'uses' => 'ProductsController@delete', 'permissions' => 'products-force-delete']);

                /**
                 * Duplicate Actions.
                 */
                Route::post('duplicate/{product}', ['as' => 'admin.products.duplicate', 'uses' => 'ProductsController@duplicate', 'permissions' => 'products-duplicate']);

                /**
                 * Preview Actions.
                 */
                Route::match(['post', 'put'], 'preview/{product?}', ['as' => 'admin.products.preview', 'uses' => 'ProductsController@preview', 'permissions' => 'products-preview']);

                /**
                 * Order Actions.
                 */
                Route::patch('order', ['as' => 'admin.products.order', 'uses' => 'ProductsController@order']);

                /**
                 * Draft Actions.
                 */
                Route::get('drafts', ['as' => 'admin.products.drafts', 'uses' => 'ProductsController@drafts', 'permissions' => 'drafts-list']);
                Route::get('draft/{draft}', ['as' => 'admin.products.draft', 'uses' => 'ProductsController@draft', 'permissions' => 'drafts-publish']);
                Route::match(['get', 'put'], 'limbo/{id}', ['as' => 'admin.products.limbo', 'uses' => 'ProductsController@limbo', 'permissions' => 'drafts-save']);

                /**
                 * Revision Actions.
                 */
                Route::get('revision/{revision}', ['as' => 'admin.products.revision', 'uses' => 'ProductsController@revision', 'permissions' => 'revisions-rollback']);

                /**
                 * Assignment Actions.
                 */
                Route::post('save-custom-attribute-value', ['as' => 'admin.products.save_custom_attribute_value', 'uses' => 'ProductsController@saveCustomAttributeValue', 'permissions' => 'products-edit']);
                Route::post('load-one-attribute', ['as' => 'admin.products.load_one_attribute', 'uses' => 'ProductsController@loadOneAttribute', 'permissions' => 'products-edit']);
                Route::post('load-one-discount', ['as' => 'admin.products.load_one_discount', 'uses' => 'ProductsController@loadOneDiscount', 'permissions' => 'products-edit']);
                Route::post('load-one-tax', ['as' => 'admin.products.load_one_tax', 'uses' => 'ProductsController@loadOneTax', 'permissions' => 'products-edit']);
            });

            /**
             * CRUD Categories.
             */
            Route::group([
                'prefix' => 'categories',
            ], function () {
                Route::get('/', ['as' => 'admin.categories.index', 'uses' => 'CategoriesController@index', 'permissions' => 'categories-list']);
                Route::get('create/{parent?}', ['as' => 'admin.categories.create', 'uses' => 'CategoriesController@create', 'permissions' => 'categories-add']);
                Route::get('edit/{category}', ['as' => 'admin.categories.edit', 'uses' => 'CategoriesController@edit', 'permissions' => 'categories-edit']);
                Route::post('store/{parent?}', ['as' => 'admin.categories.store', 'uses' => 'CategoriesController@store', 'permissions' => 'categories-add']);
                Route::put('update/{category}', ['as' => 'admin.categories.update', 'uses' => 'CategoriesController@update', 'permissions' => 'categories-edit']);
                Route::delete('destroy/{category}', ['as' => 'admin.categories.destroy', 'uses' => 'CategoriesController@destroy', 'permissions' => 'categories-delete']);

                /**
                 * Soft Delete Actions.
                 */
                Route::get('deleted', ['as' => 'admin.categories.deleted', 'uses' => 'CategoriesController@deleted', 'permissions' => 'categories-deleted']);
                Route::put('restore/{id}', ['as' => 'admin.categories.restore', 'uses' => 'CategoriesController@restore', 'permissions' => 'categories-restore']);
                Route::delete('delete/{id}', ['as' => 'admin.categories.delete', 'uses' => 'CategoriesController@delete', 'permissions' => 'categories-force-delete']);

                /**
                 * Duplicate Actions.
                 */
                Route::post('duplicate/{category}', ['as' => 'admin.categories.duplicate', 'uses' => 'CategoriesController@duplicate', 'permissions' => 'categories-duplicate']);

                /**
                 * Preview Actions.
                 */
                Route::match(['post', 'put'], 'preview/{category?}', ['as' => 'admin.categories.preview', 'uses' => 'CategoriesController@preview', 'permissions' => 'categories-preview']);

                /**
                 * Draft Actions.
                 */
                Route::get('drafts', ['as' => 'admin.categories.drafts', 'uses' => 'CategoriesController@drafts', 'permissions' => 'drafts-list']);
                Route::get('draft/{draft}', ['as' => 'admin.categories.draft', 'uses' => 'CategoriesController@draft', 'permissions' => 'drafts-publish']);
                Route::match(['get', 'put'], 'limbo/{id}', ['as' => 'admin.categories.limbo', 'uses' => 'CategoriesController@limbo', 'permissions' => 'drafts-save']);

                /**
                 * Revision Actions.
                 */
                Route::get('revision/{revision}', ['as' => 'admin.categories.revision', 'uses' => 'CategoriesController@revision', 'permissions' => 'revisions-rollback']);

                /**
                 * Tree Actions.
                 */
                Route::group([
                    'prefix' => 'tree'
                ], function () {
                    Route::get('fix', ['as' => 'admin.categories.tree.fix', 'uses' => 'CategoriesController@fixTree', 'acl' => 'pages-list']);
                    Route::get('load/{parent?}', ['as' => 'admin.categories.tree.load', 'uses' => 'CategoriesController@loadTreeNodes', 'acl' => 'pages-list']);
                    Route::get('list/{parent?}', ['as' => 'admin.categories.tree.list', 'uses' => 'CategoriesController@listTreeItems', 'acl' => 'pages-list']);
                    Route::post('sort', ['as' => 'admin.categories.tree.sort', 'uses' => 'CategoriesController@sortTreeItems', 'acl' => 'pages-list']);
                    Route::post('url', ['as' => 'admin.categories.tree.url', 'uses' => 'CategoriesController@refreshTreeItemsUrls', 'acl' => 'pages-list']);
                });
            });

            /**
             * CRUD Sets.
             */
            Route::group([
                'prefix' => 'sets',
            ], function () {
                Route::get('/', ['as' => 'admin.sets.index', 'uses' => 'SetsController@index', 'permissions' => 'sets-list']);
                Route::get('create', ['as' => 'admin.sets.create', 'uses' => 'SetsController@create', 'permissions' => 'sets-add']);
                Route::get('edit/{set}', ['as' => 'admin.sets.edit', 'uses' => 'SetsController@edit', 'permissions' => 'sets-edit']);
                Route::post('store', ['as' => 'admin.sets.store', 'uses' => 'SetsController@store', 'permissions' => 'sets-add']);
                Route::put('update/{set}', ['as' => 'admin.sets.update', 'uses' => 'SetsController@update', 'permissions' => 'sets-edit']);
                Route::delete('destroy/{set}', ['as' => 'admin.sets.destroy', 'uses' => 'SetsController@destroy', 'permissions' => 'sets-delete']);

                /**
                 * Order Actions.
                 */
                Route::patch('order', ['as' => 'admin.sets.order', 'uses' => 'SetsController@order']);

                /**
                 * Fetch attributes & values endpoints.
                 */
                Route::get('attributes/get/{set?}', ['as' => 'admin.attributes.get', 'uses' => 'AttributesController@get', 'permissions' => 'attributes-list']);
                Route::get('values/get/{set?}/{attribute?}', ['as' => 'admin.values.get', 'uses' => 'ValuesController@get', 'permissions' => 'values-edit']);

                /**
                 * CRUD Attributes.
                 */
                Route::group([
                    'prefix' => 'edit/{set}/attributes',
                ], function () {
                    Route::get('/', ['as' => 'admin.attributes.index', 'uses' => 'AttributesController@index', 'permissions' => 'attributes-list']);
                    Route::get('create', ['as' => 'admin.attributes.create', 'uses' => 'AttributesController@create', 'permissions' => 'attributes-add']);
                    Route::get('edit/{attribute}', ['as' => 'admin.attributes.edit', 'uses' => 'AttributesController@edit', 'permissions' => 'attributes-edit']);
                    Route::post('store', ['as' => 'admin.attributes.store', 'uses' => 'AttributesController@store', 'permissions' => 'attributes-add']);
                    Route::put('update/{attribute}', ['as' => 'admin.attributes.update', 'uses' => 'AttributesController@update', 'permissions' => 'attributes-edit']);
                    Route::delete('destroy/{attribute}', ['as' => 'admin.attributes.destroy', 'uses' => 'AttributesController@destroy', 'permissions' => 'attributes-delete']);

                    /**
                     * Order Actions.
                     */
                    Route::patch('order', ['as' => 'admin.attributes.order', 'uses' => 'AttributesController@order']);

                    /**
                     * CRUD Values.
                     */
                    Route::group([
                        'prefix' => 'edit/{attribute}/values',
                    ], function () {
                        Route::get('/', ['as' => 'admin.values.index', 'uses' => 'ValuesController@index', 'permissions' => 'attributes-edit']);
                        Route::get('create', ['as' => 'admin.values.create', 'uses' => 'ValuesController@create', 'permissions' => 'attributes-edit']);
                        Route::get('edit/{value}', ['as' => 'admin.values.edit', 'uses' => 'ValuesController@edit', 'permissions' => 'attributes-edit']);
                        Route::post('store', ['as' => 'admin.values.store', 'uses' => 'ValuesController@store', 'permissions' => 'attributes-edit']);
                        Route::put('update/{value}', ['as' => 'admin.values.update', 'uses' => 'ValuesController@update', 'permissions' => 'attributes-edit']);
                        Route::delete('destroy/{value}', ['as' => 'admin.values.destroy', 'uses' => 'ValuesController@destroy', 'permissions' => 'attributes-edit']);

                        /**
                         * Order Actions.
                         */
                        Route::patch('order', ['as' => 'admin.values.order', 'uses' => 'ValuesController@order']);
                    });
                });
            });

            /**
             * CRUD Discounts.
             */
            Route::group([
                'prefix' => 'discounts',
            ], function () {
                Route::get('/', ['as' => 'admin.discounts.index', 'uses' => 'DiscountsController@index', 'permissions' => 'discounts-list']);
                Route::get('create', ['as' => 'admin.discounts.create', 'uses' => 'DiscountsController@create', 'permissions' => 'discounts-add']);
                Route::get('edit/{discount}', ['as' => 'admin.discounts.edit', 'uses' => 'DiscountsController@edit', 'permissions' => 'discounts-edit']);
                Route::post('store', ['as' => 'admin.discounts.store', 'uses' => 'DiscountsController@store', 'permissions' => 'discounts-add']);
                Route::put('update/{discount}', ['as' => 'admin.discounts.update', 'uses' => 'DiscountsController@update', 'permissions' => 'discounts-edit']);
                Route::delete('destroy/{discount}', ['as' => 'admin.discounts.destroy', 'uses' => 'DiscountsController@destroy', 'permissions' => 'discounts-delete']);
            });

            /**
             * CRUD Taxes.
             */
            Route::group([
                'prefix' => 'taxes',
            ], function () {
                Route::get('/', ['as' => 'admin.taxes.index', 'uses' => 'TaxesController@index', 'permissions' => 'taxes-list']);
                Route::get('create', ['as' => 'admin.taxes.create', 'uses' => 'TaxesController@create', 'permissions' => 'taxes-add']);
                Route::get('edit/{tax}', ['as' => 'admin.taxes.edit', 'uses' => 'TaxesController@edit', 'permissions' => 'taxes-edit']);
                Route::post('store', ['as' => 'admin.taxes.store', 'uses' => 'TaxesController@store', 'permissions' => 'taxes-add']);
                Route::put('update/{tax}', ['as' => 'admin.taxes.update', 'uses' => 'TaxesController@update', 'permissions' => 'taxes-edit']);
                Route::delete('destroy/{tax}', ['as' => 'admin.taxes.destroy', 'uses' => 'TaxesController@destroy', 'permissions' => 'taxes-delete']);
            });
        });

        /**
         * Access Control Level.
         */
        Route::group([
            'namespace' => 'Acl',
        ], function () {
            /**
             * CRUD Users.
             */
            Route::group([
                'prefix' => 'users',
            ], function () {
                Route::get('/', ['as' => 'admin.users.index', 'uses' => 'UsersController@index', 'permissions' => 'users-list']);
                Route::get('create', ['as' => 'admin.users.create', 'uses' => 'UsersController@create', 'permissions' => 'users-add']);
                Route::get('edit/{user}', ['as' => 'admin.users.edit', 'uses' => 'UsersController@edit', 'permissions' => 'users-edit']);
                Route::post('store', ['as' => 'admin.users.store', 'uses' => 'UsersController@store', 'permissions' => 'users-add']);
                Route::put('update/{user}', ['as' => 'admin.users.update', 'uses' => 'UsersController@update', 'permissions' => 'users-edit']);
                Route::delete('destroy/{user}', ['as' => 'admin.users.destroy', 'uses' => 'UsersController@destroy', 'permissions' => 'users-delete']);
                Route::post('impersonate/{user}', ['as' => 'admin.users.impersonate', 'uses' => 'UsersController@impersonate', 'permissions' => 'users-impersonate']);
            });

            /**
             * CRUD Admins.
             */
            Route::group([
                'prefix' => 'admins',
            ], function () {
                Route::get('/', ['as' => 'admin.admins.index', 'uses' => 'AdminsController@index', 'permissions' => 'admins-list']);
                Route::get('create', ['as' => 'admin.admins.create', 'uses' => 'AdminsController@create', 'permissions' => 'admins-add']);
                Route::get('edit/{user}', ['as' => 'admin.admins.edit', 'uses' => 'AdminsController@edit', 'permissions' => 'admins-edit']);
                Route::post('store', ['as' => 'admin.admins.store', 'uses' => 'AdminsController@store', 'permissions' => 'admins-add']);
                Route::put('update/{user}', ['as' => 'admin.admins.update', 'uses' => 'AdminsController@update', 'permissions' => 'admins-edit']);
                Route::delete('destroy/{user}', ['as' => 'admin.admins.destroy', 'uses' => 'AdminsController@destroy', 'permissions' => 'admins-delete']);
            });

            /**
             * CRUD Roles.
             */
            Route::group([
                'prefix' => 'roles',
            ], function () {
                Route::get('/', ['as' => 'admin.roles.index', 'uses' => 'RolesController@index', 'permissions' => 'roles-list']);
                Route::get('create', ['as' => 'admin.roles.create', 'uses' => 'RolesController@create', 'permissions' => 'roles-add']);
                Route::get('edit/{role}', ['as' => 'admin.roles.edit', 'uses' => 'RolesController@edit', 'permissions' => 'roles-edit']);
                Route::post('store', ['as' => 'admin.roles.store', 'uses' => 'RolesController@store', 'permissions' => 'roles-add']);
                Route::put('update/{role}', ['as' => 'admin.roles.update', 'uses' => 'RolesController@update', 'permissions' => 'roles-edit']);
                Route::delete('destroy/{role}', ['as' => 'admin.roles.destroy', 'uses' => 'RolesController@destroy', 'permissions' => 'roles-delete']);
            });

            /**
             * CRUD Activity.
             */
            Route::group([
                'prefix' => 'activity',
            ], function () {
                Route::get('/', ['as' => 'admin.activity.index', 'uses' => 'ActivityController@index', 'permissions' => 'activity-list']);
                Route::delete('destroy/{activity}', ['as' => 'admin.activity.destroy', 'uses' => 'ActivityController@destroy', 'permissions' => 'activity-delete']);
                Route::delete('clean', ['as' => 'admin.activity.clean', 'uses' => 'ActivityController@clean', 'permissions' => 'activity-clean']);
                Route::delete('delete', ['as' => 'admin.activity.delete', 'uses' => 'ActivityController@delete', 'permissions' => 'activity-delete']);
            });
        });

        /**
         * Geo Location.
         */
        Route::group([
            'namespace' => 'Location',
        ], function () {
            /**
             * CRUD Countries.
             */
            Route::group([
                'prefix' => 'countries',
            ], function () {
                Route::get('/', ['as' => 'admin.countries.index', 'uses' => 'CountriesController@index', 'permissions' => 'countries-list']);
                Route::get('create', ['as' => 'admin.countries.create', 'uses' => 'CountriesController@create', 'permissions' => 'countries-add']);
                Route::get('edit/{country}', ['as' => 'admin.countries.edit', 'uses' => 'CountriesController@edit', 'permissions' => 'countries-edit']);
                Route::post('store', ['as' => 'admin.countries.store', 'uses' => 'CountriesController@store', 'permissions' => 'countries-add']);
                Route::put('update/{country}', ['as' => 'admin.countries.update', 'uses' => 'CountriesController@update', 'permissions' => 'countries-edit']);
                Route::delete('destroy/{country}', ['as' => 'admin.countries.destroy', 'uses' => 'CountriesController@destroy', 'permissions' => 'countries-delete']);
            });

            /**
             * CRUD States.
             */
            Route::group([
                'prefix' => 'states',
            ], function () {
                Route::get('/', ['as' => 'admin.states.index', 'uses' => 'StatesController@index', 'permissions' => 'states-list']);
                Route::get('create', ['as' => 'admin.states.create', 'uses' => 'StatesController@create', 'permissions' => 'states-add']);
                Route::get('edit/{state}', ['as' => 'admin.states.edit', 'uses' => 'StatesController@edit', 'permissions' => 'states-edit']);
                Route::post('store', ['as' => 'admin.states.store', 'uses' => 'StatesController@store', 'permissions' => 'states-add']);
                Route::put('update/{state}', ['as' => 'admin.states.update', 'uses' => 'StatesController@update', 'permissions' => 'states-edit']);
                Route::delete('destroy/{state}', ['as' => 'admin.states.destroy', 'uses' => 'StatesController@destroy', 'permissions' => 'states-delete']);
            });

            /**
             * CRUD Cities.
             */
            Route::group([
                'prefix' => 'cities',
            ], function () {
                Route::get('/', ['as' => 'admin.cities.index', 'uses' => 'CitiesController@index', 'permissions' => 'cities-list']);
                Route::get('create', ['as' => 'admin.cities.create', 'uses' => 'CitiesController@create', 'permissions' => 'cities-add']);
                Route::get('edit/{city}', ['as' => 'admin.cities.edit', 'uses' => 'CitiesController@edit', 'permissions' => 'cities-edit']);
                Route::post('store', ['as' => 'admin.cities.store', 'uses' => 'CitiesController@store', 'permissions' => 'cities-add']);
                Route::put('update/{city}', ['as' => 'admin.cities.update', 'uses' => 'CitiesController@update', 'permissions' => 'cities-edit']);
                Route::delete('destroy/{city}', ['as' => 'admin.cities.destroy', 'uses' => 'CitiesController@destroy', 'permissions' => 'cities-delete']);
                Route::get('get-states/{country?}', ['as' => 'admin.cities.get_states', 'uses' => 'CitiesController@getStates']);
            });
        });

        /**
         * CRUD Test (Cars)
         */
        Route::group([
            'namespace' => 'Config',
            'prefix' => 'settings',
        ], function () {
            Route::match(['get', 'post'], 'general', ['as' => 'admin.settings.general', 'uses' => 'SettingsController@general', 'permissions' => 'settings-general']);
            Route::match(['get', 'post'], 'analytics', ['as' => 'admin.settings.analytics', 'uses' => 'SettingsController@analytics', 'permissions' => 'settings-analytics']);
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