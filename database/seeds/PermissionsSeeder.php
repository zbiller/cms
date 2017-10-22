<?php

use App\Models\Auth\Permission;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Seeder;

class PermissionsSeeder extends Seeder
{
    /**
     * Collection of admin permissions.
     *
     * @var Collection
     */
    private $adminPermissions;

    /**
     * Collection of front-end permissions.
     *
     * @var Collection
     */
    private $frontPermissions;

    /**
     * Mapping structure of admin permissions.
     *
     * @var array
     */
    private $adminMap = [
        'Uploads' => [
            'List' => [
                'group' => 'Uploads',
                'label' => 'List',
                'guard' => 'admin',
                'name' => 'uploads-list',
            ],
            'Select' => [
                'group' => 'Uploads',
                'label' => 'Select',
                'guard' => 'admin',
                'name' => 'uploads-select',
            ],
            'Upload' => [
                'group' => 'Uploads',
                'label' => 'Upload',
                'guard' => 'admin',
                'name' => 'uploads-upload',
            ],
            'Download' => [
                'group' => 'Uploads',
                'label' => 'Download',
                'guard' => 'admin',
                'name' => 'uploads-download',
            ],
            'Crop' => [
                'group' => 'Uploads',
                'label' => 'Crop',
                'guard' => 'admin',
                'name' => 'uploads-crop',
            ],
            'Delete' => [
                'group' => 'Uploads',
                'label' => 'Delete',
                'guard' => 'admin',
                'name' => 'uploads-delete',
            ],
        ],
        'Translations' => [
            'List' => [
                'group' => 'Translations',
                'label' => 'List',
                'guard' => 'admin',
                'name' => 'translations-list',
            ],
            'Add' => [
                'group' => 'Translations',
                'label' => 'Add',
                'guard' => 'admin',
                'name' => 'translations-add',
            ],
            'Edit' => [
                'group' => 'Translations',
                'label' => 'Edit',
                'guard' => 'admin',
                'name' => 'translations-edit',
            ],
            'Delete' => [
                'group' => 'Translations',
                'label' => 'Delete',
                'guard' => 'admin',
                'name' => 'translations-delete',
            ],
            'Import' => [
                'group' => 'Translations',
                'label' => 'Import',
                'guard' => 'admin',
                'name' => 'translations-import',
            ],
            'Export' => [
                'group' => 'Translations',
                'label' => 'Export',
                'guard' => 'admin',
                'name' => 'translations-export',
            ],
            'Sync' => [
                'group' => 'Translations',
                'label' => 'Sync',
                'guard' => 'admin',
                'name' => 'translations-sync',
            ],
            'Clear' => [
                'group' => 'Translations',
                'label' => 'Clear',
                'guard' => 'admin',
                'name' => 'translations-clear',
            ],
        ],
        'Drafts' => [
            'List' => [
                'group' => 'Drafts',
                'label' => 'List',
                'guard' => 'admin',
                'name' => 'drafts-list',
            ],
            'view' => [
                'group' => 'Drafts',
                'label' => 'View',
                'guard' => 'admin',
                'name' => 'drafts-view',
            ],
            'Save' => [
                'group' => 'Drafts',
                'label' => 'Save',
                'guard' => 'admin',
                'name' => 'drafts-save',
            ],
            'Approval' => [
                'group' => 'Drafts',
                'label' => 'Approval',
                'guard' => 'admin',
                'name' => 'drafts-approval',
            ],
            'Publish' => [
                'group' => 'Drafts',
                'label' => 'Publish',
                'guard' => 'admin',
                'name' => 'drafts-publish',
            ],
            'Delete' => [
                'group' => 'Drafts',
                'label' => 'Delete',
                'guard' => 'admin',
                'name' => 'drafts-delete',
            ],
        ],
        'Revisions' => [
            'List' => [
                'group' => 'Revisions',
                'label' => 'List',
                'guard' => 'admin',
                'name' => 'revisions-list',
            ],
            'view' => [
                'group' => 'Revisions',
                'label' => 'View',
                'guard' => 'admin',
                'name' => 'revisions-view',
            ],
            'Rollback' => [
                'group' => 'Revisions',
                'label' => 'Rollback',
                'guard' => 'admin',
                'name' => 'revisions-rollback',
            ],
            'Delete' => [
                'group' => 'Revisions',
                'label' => 'Delete',
                'guard' => 'admin',
                'name' => 'revisions-delete',
            ],
        ],
        'Pages' => [
            'List' => [
                'group' => 'Pages',
                'label' => 'List',
                'guard' => 'admin',
                'name' => 'pages-list',
            ],
            'Add' => [
                'group' => 'Pages',
                'label' => 'Add',
                'guard' => 'admin',
                'name' => 'pages-add',
            ],
            'Edit' => [
                'group' => 'Pages',
                'label' => 'Edit',
                'guard' => 'admin',
                'name' => 'pages-edit',
            ],
            'Duplicate' => [
                'group' => 'Pages',
                'label' => 'Duplicate',
                'guard' => 'admin',
                'name' => 'pages-duplicate',
            ],
            'Preview' => [
                'group' => 'Pages',
                'label' => 'Preview',
                'guard' => 'admin',
                'name' => 'pages-preview',
            ],
            'Deleted' => [
                'group' => 'Pages',
                'label' => 'Deleted',
                'guard' => 'admin',
                'name' => 'pages-deleted',
            ],
            'Restore' => [
                'group' => 'Pages',
                'label' => 'Restore',
                'guard' => 'admin',
                'name' => 'pages-restore',
            ],
            'Soft Delete' => [
                'group' => 'Pages',
                'label' => 'Soft Delete',
                'guard' => 'admin',
                'name' => 'pages-soft-delete',
            ],
            'Force Delete' => [
                'group' => 'Pages',
                'label' => 'Force Delete',
                'guard' => 'admin',
                'name' => 'pages-force-delete',
            ],
        ],
        'Menus' => [
            'List' => [
                'group' => 'Menus',
                'label' => 'List',
                'guard' => 'admin',
                'name' => 'menus-list',
            ],
            'Add' => [
                'group' => 'Menus',
                'label' => 'Add',
                'guard' => 'admin',
                'name' => 'menus-add',
            ],
            'Edit' => [
                'group' => 'Menus',
                'label' => 'Edit',
                'guard' => 'admin',
                'name' => 'menus-edit',
            ],
            'Delete' => [
                'group' => 'Menus',
                'label' => 'Delete',
                'guard' => 'admin',
                'name' => 'menus-delete',
            ],
        ],
        'Blocks' => [
            'List' => [
                'group' => 'Blocks',
                'label' => 'List',
                'guard' => 'admin',
                'name' => 'blocks-list',
            ],
            'Show' => [
                'group' => 'Blocks',
                'label' => 'Show',
                'guard' => 'admin',
                'name' => 'blocks-show',
            ],
            'Add' => [
                'group' => 'Blocks',
                'label' => 'Add',
                'guard' => 'admin',
                'name' => 'blocks-add',
            ],
            'Edit' => [
                'group' => 'Blocks',
                'label' => 'Edit',
                'guard' => 'admin',
                'name' => 'blocks-edit',
            ],
            'Assign' => [
                'group' => 'Blocks',
                'label' => 'Assign',
                'guard' => 'admin',
                'name' => 'blocks-assign',
            ],
            'Un-Assign' => [
                'group' => 'Blocks',
                'label' => 'Un-Assign',
                'guard' => 'admin',
                'name' => 'blocks-unassign',
            ],
            'Duplicate' => [
                'group' => 'Blocks',
                'label' => 'Duplicate',
                'guard' => 'admin',
                'name' => 'blocks-duplicate',
            ],
            'Deleted' => [
                'group' => 'Blocks',
                'label' => 'Deleted',
                'guard' => 'admin',
                'name' => 'blocks-deleted',
            ],
            'Restore' => [
                'group' => 'Blocks',
                'label' => 'Restore',
                'guard' => 'admin',
                'name' => 'blocks-restore',
            ],
            'Soft Delete' => [
                'group' => 'Blocks',
                'label' => 'Soft Delete',
                'guard' => 'admin',
                'name' => 'blocks-soft-delete',
            ],
            'Force Delete' => [
                'group' => 'Blocks',
                'label' => 'Force Delete',
                'guard' => 'admin',
                'name' => 'blocks-force-delete',
            ],
        ],
        'Emails' => [
            'List' => [
                'group' => 'Emails',
                'label' => 'List',
                'guard' => 'admin',
                'name' => 'emails-list',
            ],
            'Add' => [
                'group' => 'Emails',
                'label' => 'Add',
                'guard' => 'admin',
                'name' => 'emails-add',
            ],
            'Edit' => [
                'group' => 'Emails',
                'label' => 'Edit',
                'guard' => 'admin',
                'name' => 'emails-edit',
            ],
            'Duplicate' => [
                'group' => 'Emails',
                'label' => 'Duplicate',
                'guard' => 'admin',
                'name' => 'emails-duplicate',
            ],
            'Preview' => [
                'group' => 'Emails',
                'label' => 'Preview',
                'guard' => 'admin',
                'name' => 'emails-preview',
            ],
            'Deleted' => [
                'group' => 'Emails',
                'label' => 'Deleted',
                'guard' => 'admin',
                'name' => 'emails-deleted',
            ],
            'Restore' => [
                'group' => 'Emails',
                'label' => 'Restore',
                'guard' => 'admin',
                'name' => 'emails-restore',
            ],
            'Soft Delete' => [
                'group' => 'Emails',
                'label' => 'Soft Delete',
                'guard' => 'admin',
                'name' => 'emails-soft-delete',
            ],
            'Force Delete' => [
                'group' => 'Emails',
                'label' => 'Force Delete',
                'guard' => 'admin',
                'name' => 'emails-force-delete',
            ],
        ],
        'Layouts' => [
            'List' => [
                'group' => 'Layouts',
                'label' => 'List',
                'guard' => 'admin',
                'name' => 'layouts-list',
            ],
            'Add' => [
                'group' => 'Layouts',
                'label' => 'Add',
                'guard' => 'admin',
                'name' => 'layouts-add',
            ],
            'Edit' => [
                'group' => 'Layouts',
                'label' => 'Edit',
                'guard' => 'admin',
                'name' => 'layouts-edit',
            ],
            'Delete' => [
                'group' => 'Layouts',
                'label' => 'Delete',
                'guard' => 'admin',
                'name' => 'layouts-delete',
            ],
        ],
        'Orders' => [
            'List' => [
                'group' => 'Orders',
                'label' => 'List',
                'guard' => 'admin',
                'name' => 'orders-list',
            ],
            'View' => [
                'group' => 'Orders',
                'label' => 'View',
                'guard' => 'admin',
                'name' => 'orders-view',
            ],
            'Add' => [
                'group' => 'Orders',
                'label' => 'Add',
                'guard' => 'admin',
                'name' => 'orders-add',
            ],
            'Edit' => [
                'group' => 'Orders',
                'label' => 'Edit',
                'guard' => 'admin',
                'name' => 'orders-edit',
            ],
            'Duplicate' => [
                'group' => 'Orders',
                'label' => 'Duplicate',
                'guard' => 'admin',
                'name' => 'orders-duplicate',
            ],
            'Deleted' => [
                'group' => 'Orders',
                'label' => 'Deleted',
                'guard' => 'admin',
                'name' => 'orders-deleted',
            ],
            'Restore' => [
                'group' => 'Orders',
                'label' => 'Restore',
                'guard' => 'admin',
                'name' => 'orders-restore',
            ],
            'Soft Delete' => [
                'group' => 'Orders',
                'label' => 'Soft Delete',
                'guard' => 'admin',
                'name' => 'orders-soft-delete',
            ],
            'Force Delete' => [
                'group' => 'Orders',
                'label' => 'Force Delete',
                'guard' => 'admin',
                'name' => 'orders-force-delete',
            ],
        ],
        'Carts' => [
            'List' => [
                'group' => 'Carts',
                'label' => 'List',
                'guard' => 'admin',
                'name' => 'carts-list',
            ],
            'View' => [
                'group' => 'Carts',
                'label' => 'View',
                'guard' => 'admin',
                'name' => 'carts-view',
            ],
            'Delete' => [
                'group' => 'Carts',
                'label' => 'Delete',
                'guard' => 'admin',
                'name' => 'carts-delete',
            ],
            'Clean' => [
                'group' => 'Carts',
                'label' => 'Clean',
                'guard' => 'admin',
                'name' => 'carts-clean',
            ],
            'Remind' => [
                'group' => 'Carts',
                'label' => 'Remind',
                'guard' => 'admin',
                'name' => 'carts-remind',
            ],
        ],
        'Products' => [
            'List' => [
                'group' => 'Products',
                'label' => 'List',
                'guard' => 'admin',
                'name' => 'products-list',
            ],
            'Add' => [
                'group' => 'Products',
                'label' => 'Add',
                'guard' => 'admin',
                'name' => 'products-add',
            ],
            'Edit' => [
                'group' => 'Products',
                'label' => 'Edit',
                'guard' => 'admin',
                'name' => 'products-edit',
            ],
            'Duplicate' => [
                'group' => 'Products',
                'label' => 'Duplicate',
                'guard' => 'admin',
                'name' => 'products-duplicate',
            ],
            'Preview' => [
                'group' => 'Products',
                'label' => 'Preview',
                'guard' => 'admin',
                'name' => 'products-preview',
            ],
            'Deleted' => [
                'group' => 'Products',
                'label' => 'Deleted',
                'guard' => 'admin',
                'name' => 'products-deleted',
            ],
            'Restore' => [
                'group' => 'Products',
                'label' => 'Restore',
                'guard' => 'admin',
                'name' => 'products-restore',
            ],
            'Soft Delete' => [
                'group' => 'Products',
                'label' => 'Soft Delete',
                'guard' => 'admin',
                'name' => 'products-soft-delete',
            ],
            'Force Delete' => [
                'group' => 'Products',
                'label' => 'Force Delete',
                'guard' => 'admin',
                'name' => 'products-force-delete',
            ],
        ],
        'Product Categories' => [
            'List' => [
                'group' => 'Product Categories',
                'label' => 'List',
                'guard' => 'admin',
                'name' => 'product-categories-list',
            ],
            'Add' => [
                'group' => 'Product Categories',
                'label' => 'Add',
                'guard' => 'admin',
                'name' => 'product-categories-add',
            ],
            'Edit' => [
                'group' => 'Product Categories',
                'label' => 'Edit',
                'guard' => 'admin',
                'name' => 'product-categories-edit',
            ],
            'Duplicate' => [
                'group' => 'Product Categories',
                'label' => 'Duplicate',
                'guard' => 'admin',
                'name' => 'product-categories-duplicate',
            ],
            'Preview' => [
                'group' => 'Product Categories',
                'label' => 'Preview',
                'guard' => 'admin',
                'name' => 'product-categories-preview',
            ],
            'Deleted' => [
                'group' => 'Product Categories',
                'label' => 'Deleted',
                'guard' => 'admin',
                'name' => 'product-categories-deleted',
            ],
            'Restore' => [
                'group' => 'Product Categories',
                'label' => 'Restore',
                'guard' => 'admin',
                'name' => 'product-categories-restore',
            ],
            'Soft Delete' => [
                'group' => 'Product Categories',
                'label' => 'Soft Delete',
                'guard' => 'admin',
                'name' => 'product-categories-soft-delete',
            ],
            'Force Delete' => [
                'group' => 'Product Categories',
                'label' => 'Force Delete',
                'guard' => 'admin',
                'name' => 'product-categories-force-delete',
            ],
        ],
        'Attributes' => [
            'List' => [
                'group' => 'Attributes',
                'label' => 'List',
                'guard' => 'admin',
                'name' => 'attributes-list',
            ],
            'Add' => [
                'group' => 'Attributes',
                'label' => 'Add',
                'guard' => 'admin',
                'name' => 'attributes-add',
            ],
            'Edit' => [
                'group' => 'Attributes',
                'label' => 'Edit',
                'guard' => 'admin',
                'name' => 'attributes-edit',
            ],
            'Delete' => [
                'group' => 'Attributes',
                'label' => 'Delete',
                'guard' => 'admin',
                'name' => 'attributes-delete',
            ],
        ],
        'Discounts' => [
            'List' => [
                'group' => 'Discounts',
                'label' => 'List',
                'guard' => 'admin',
                'name' => 'discounts-list',
            ],
            'Add' => [
                'group' => 'Discounts',
                'label' => 'Add',
                'guard' => 'admin',
                'name' => 'discounts-add',
            ],
            'Edit' => [
                'group' => 'Discounts',
                'label' => 'Edit',
                'guard' => 'admin',
                'name' => 'discounts-edit',
            ],
            'Delete' => [
                'group' => 'Discounts',
                'label' => 'Delete',
                'guard' => 'admin',
                'name' => 'discounts-delete',
            ],
        ],
        'Taxes' => [
            'List' => [
                'group' => 'Taxes',
                'label' => 'List',
                'guard' => 'admin',
                'name' => 'taxes-list',
            ],
            'Add' => [
                'group' => 'Taxes',
                'label' => 'Add',
                'guard' => 'admin',
                'name' => 'taxes-add',
            ],
            'Edit' => [
                'group' => 'Taxes',
                'label' => 'Edit',
                'guard' => 'admin',
                'name' => 'taxes-edit',
            ],
            'Delete' => [
                'group' => 'Taxes',
                'label' => 'Delete',
                'guard' => 'admin',
                'name' => 'taxes-delete',
            ],
        ],
        'Users' => [
            'List' => [
                'group' => 'Users',
                'label' => 'List',
                'guard' => 'admin',
                'name' => 'users-list',
            ],
            'Add' => [
                'group' => 'Users',
                'label' => 'Add',
                'guard' => 'admin',
                'name' => 'users-add',
            ],
            'Edit' => [
                'group' => 'Users',
                'label' => 'Edit',
                'guard' => 'admin',
                'name' => 'users-edit',
            ],
            'Delete' => [
                'group' => 'Users',
                'label' => 'Delete',
                'guard' => 'admin',
                'name' => 'users-delete',
            ],
            'Impersonate' => [
                'group' => 'Users',
                'label' => 'Impersonate',
                'guard' => 'admin',
                'name' => 'users-impersonate',
            ],
        ],
        'Admins' => [
            'List' => [
                'group' => 'Admins',
                'label' => 'List',
                'guard' => 'admin',
                'name' => 'admins-list',
            ],
            'Add' => [
                'group' => 'Admins',
                'label' => 'Add',
                'guard' => 'admin',
                'name' => 'admins-add',
            ],
            'Edit' => [
                'group' => 'Admins',
                'label' => 'Edit',
                'guard' => 'admin',
                'name' => 'admins-edit',
            ],
            'Delete' => [
                'group' => 'Admins',
                'label' => 'Delete',
                'guard' => 'admin',
                'name' => 'admins-delete',
            ],
        ],
        'Roles' => [
            'List' => [
                'group' => 'Roles',
                'label' => 'List',
                'guard' => 'admin',
                'name' => 'roles-list',
            ],
            'Add' => [
                'group' => 'Roles',
                'label' => 'Add',
                'guard' => 'admin',
                'name' => 'roles-add',
            ],
            'Edit' => [
                'group' => 'Roles',
                'label' => 'Edit',
                'guard' => 'admin',
                'name' => 'roles-edit',
            ],
            'Delete' => [
                'group' => 'Roles',
                'label' => 'Delete',
                'guard' => 'admin',
                'name' => 'roles-delete',
            ],
        ],
        'Activity' => [
            'List' => [
                'group' => 'Activity',
                'label' => 'List',
                'guard' => 'admin',
                'name' => 'activity-list',
            ],
            'Delete' => [
                'group' => 'Activity',
                'label' => 'Delete',
                'guard' => 'admin',
                'name' => 'activity-delete',
            ],
            'Clean' => [
                'group' => 'Activity',
                'label' => 'Clean',
                'guard' => 'admin',
                'name' => 'activity-clean',
            ],
        ],
        'Languages' => [
            'List' => [
                'group' => 'Languages',
                'label' => 'List',
                'guard' => 'admin',
                'name' => 'languages-list',
            ],
            'Add' => [
                'group' => 'Languages',
                'label' => 'Add',
                'guard' => 'admin',
                'name' => 'languages-add',
            ],
            'Edit' => [
                'group' => 'Languages',
                'label' => 'Edit',
                'guard' => 'admin',
                'name' => 'languages-edit',
            ],
            'Delete' => [
                'group' => 'Languages',
                'label' => 'Delete',
                'guard' => 'admin',
                'name' => 'languages-delete',
            ],
            'Change' => [
                'group' => 'Languages',
                'label' => 'Change',
                'guard' => 'admin',
                'name' => 'languages-change',
            ],
        ],
        'Currencies' => [
            'List' => [
                'group' => 'Currencies',
                'label' => 'List',
                'guard' => 'admin',
                'name' => 'currencies-list',
            ],
            'Add' => [
                'group' => 'Currencies',
                'label' => 'Add',
                'guard' => 'admin',
                'name' => 'currencies-add',
            ],
            'Edit' => [
                'group' => 'Currencies',
                'label' => 'Edit',
                'guard' => 'admin',
                'name' => 'currencies-edit',
            ],
            'Delete' => [
                'group' => 'Currencies',
                'label' => 'Delete',
                'guard' => 'admin',
                'name' => 'currencies-delete',
            ],
            'Exchange' => [
                'group' => 'Currencies',
                'label' => 'Exchange',
                'guard' => 'admin',
                'name' => 'currencies-exchange',
            ],
        ],
        'Countries' => [
            'List' => [
                'group' => 'Countries',
                'label' => 'List',
                'guard' => 'admin',
                'name' => 'countries-list',
            ],
            'Add' => [
                'group' => 'Countries',
                'label' => 'Add',
                'guard' => 'admin',
                'name' => 'countries-add',
            ],
            'Edit' => [
                'group' => 'Countries',
                'label' => 'Edit',
                'guard' => 'admin',
                'name' => 'countries-edit',
            ],
            'Delete' => [
                'group' => 'Countries',
                'label' => 'Delete',
                'guard' => 'admin',
                'name' => 'countries-delete',
            ],
        ],
        'States' => [
            'List' => [
                'group' => 'States',
                'label' => 'List',
                'guard' => 'admin',
                'name' => 'states-list',
            ],
            'Add' => [
                'group' => 'States',
                'label' => 'Add',
                'guard' => 'admin',
                'name' => 'states-add',
            ],
            'Edit' => [
                'group' => 'States',
                'label' => 'Edit',
                'guard' => 'admin',
                'name' => 'states-edit',
            ],
            'Delete' => [
                'group' => 'States',
                'label' => 'Delete',
                'guard' => 'admin',
                'name' => 'states-delete',
            ],
        ],
        'Cities' => [
            'List' => [
                'group' => 'Cities',
                'label' => 'List',
                'guard' => 'admin',
                'name' => 'cities-list',
            ],
            'Add' => [
                'group' => 'Cities',
                'label' => 'Add',
                'guard' => 'admin',
                'name' => 'cities-add',
            ],
            'Edit' => [
                'group' => 'Cities',
                'label' => 'Edit',
                'guard' => 'admin',
                'name' => 'cities-edit',
            ],
            'Delete' => [
                'group' => 'Cities',
                'label' => 'Delete',
                'guard' => 'admin',
                'name' => 'cities-delete',
            ],
        ],
        'Addresses' => [
            'List' => [
                'group' => 'Addresses',
                'label' => 'List',
                'guard' => 'admin',
                'name' => 'addresses-list',
            ],
            'Add' => [
                'group' => 'Addresses',
                'label' => 'Add',
                'guard' => 'admin',
                'name' => 'addresses-add',
            ],
            'Edit' => [
                'group' => 'Addresses',
                'label' => 'Edit',
                'guard' => 'admin',
                'name' => 'addresses-edit',
            ],
            'Delete' => [
                'group' => 'Addresses',
                'label' => 'Delete',
                'guard' => 'admin',
                'name' => 'addresses-delete',
            ],
        ],
        'Backups' => [
            'List' => [
                'group' => 'Backups',
                'label' => 'List',
                'guard' => 'admin',
                'name' => 'backups-list',
            ],
            'Create' => [
                'group' => 'Backups',
                'label' => 'Create',
                'guard' => 'admin',
                'name' => 'backups-create',
            ],
            'Download' => [
                'group' => 'Backups',
                'label' => 'Download',
                'guard' => 'admin',
                'name' => 'backups-download',
            ],
            'Delete' => [
                'group' => 'Backups',
                'label' => 'Delete',
                'guard' => 'admin',
                'name' => 'backups-delete',
            ],
            'Clear' => [
                'group' => 'Backups',
                'label' => 'Clear',
                'guard' => 'admin',
                'name' => 'backups-clear',
            ],
        ],
        'Sitemap' => [
            'List' => [
                'group' => 'Sitemap',
                'label' => 'List',
                'guard' => 'admin',
                'name' => 'sitemap-list',
            ],
            'Generate' => [
                'group' => 'Sitemap',
                'label' => 'Generate',
                'guard' => 'admin',
                'name' => 'sitemap-generate',
            ],
            'Download' => [
                'group' => 'Sitemap',
                'label' => 'Download',
                'guard' => 'admin',
                'name' => 'sitemap-download',
            ],
            'Delete' => [
                'group' => 'Sitemap',
                'label' => 'Delete',
                'guard' => 'admin',
                'name' => 'sitemap-delete',
            ],
            'Clear' => [
                'group' => 'Sitemap',
                'label' => 'Clear',
                'guard' => 'admin',
                'name' => 'sitemap-clear',
            ],
        ],
        'Redirects' => [
            'List' => [
                'group' => 'Redirects',
                'label' => 'List',
                'guard' => 'admin',
                'name' => 'redirects-list',
            ],
            'Add' => [
                'group' => 'Redirects',
                'label' => 'Add',
                'guard' => 'admin',
                'name' => 'redirects-add',
            ],
            'Edit' => [
                'group' => 'Redirects',
                'label' => 'Edit',
                'guard' => 'admin',
                'name' => 'redirects-edit',
            ],
            'Delete' => [
                'group' => 'Redirects',
                'label' => 'Delete',
                'guard' => 'admin',
                'name' => 'redirects-delete',
            ],
            'Find Broken' => [
                'group' => 'Redirects',
                'label' => 'Find Broken',
                'guard' => 'admin',
                'name' => 'redirects-find-broken',
            ],
            'Clean' => [
                'group' => 'Redirects',
                'label' => 'Clean',
                'guard' => 'admin',
                'name' => 'redirects-clean',
            ],
            'Clear' => [
                'group' => 'Redirects',
                'label' => 'Clear',
                'guard' => 'admin',
                'name' => 'redirects-clear',
            ],
        ],
        'Settings' => [
            'General' => [
                'group' => 'Settings',
                'label' => 'General',
                'guard' => 'admin',
                'name' => 'settings-general',
            ],
            'Analytics' => [
                'group' => 'Settings',
                'label' => 'Analytics',
                'guard' => 'admin',
                'name' => 'settings-analytics',
            ],
            'Courier' => [
                'group' => 'Settings',
                'label' => 'Courier',
                'guard' => 'admin',
                'name' => 'settings-courier',
            ],
        ],
    ];

    /**
     * Mapping structure of front-end permissions.
     *
     * @var array
     */
    private $frontMap = [];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('permissions')->delete();

        $this->adminPermissions = new Collection();
        $this->frontPermissions = new Collection();

        foreach ($this->adminMap as $group => $labels) {
            foreach ($labels as $label => $data) {
                Permission::create($data);
            }
        }

        foreach ($this->frontMap as $group => $labels) {
            foreach ($labels as $label => $data) {
                Permission::create($data);
            }
        }
    }
}
