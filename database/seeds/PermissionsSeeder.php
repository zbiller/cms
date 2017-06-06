<?php

use App\Models\Auth\Permission;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Collection;

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
        'Activity' => [
            'List' => [
                'group' => 'Activity',
                'label' => 'List',
                'type' => Permission::TYPE_ADMIN,
                'name' => 'activity-list',
            ],
            'Clean' => [
                'group' => 'Activity',
                'label' => 'Clean',
                'type' => Permission::TYPE_ADMIN,
                'name' => 'activity-clean',
            ],
            'Delete' => [
                'group' => 'Activity',
                'label' => 'Delete',
                'type' => Permission::TYPE_ADMIN,
                'name' => 'activity-delete',
            ],
        ],
        'Drafts' => [
            'List' => [
                'group' => 'Drafts',
                'label' => 'List',
                'type' => Permission::TYPE_ADMIN,
                'name' => 'drafts-list',
            ],
            'Save' => [
                'group' => 'Drafts',
                'label' => 'Save',
                'type' => Permission::TYPE_ADMIN,
                'name' => 'drafts-save',
            ],
            'Publish' => [
                'group' => 'Drafts',
                'label' => 'Publish',
                'type' => Permission::TYPE_ADMIN,
                'name' => 'drafts-publish',
            ],
            'Delete' => [
                'group' => 'Drafts',
                'label' => 'Delete',
                'type' => Permission::TYPE_ADMIN,
                'name' => 'drafts-delete',
            ],
        ],
        'Revisions' => [
            'List' => [
                'group' => 'Revisions',
                'label' => 'List',
                'type' => Permission::TYPE_ADMIN,
                'name' => 'revisions-list',
            ],
            'Rollback' => [
                'group' => 'Revisions',
                'label' => 'Rollback',
                'type' => Permission::TYPE_ADMIN,
                'name' => 'revisions-rollback',
            ],
            'Delete' => [
                'group' => 'Revisions',
                'label' => 'Delete',
                'type' => Permission::TYPE_ADMIN,
                'name' => 'revisions-delete',
            ],
        ],
        'Uploads' => [
            'List' => [
                'group' => 'Uploads',
                'label' => 'List',
                'type' => Permission::TYPE_ADMIN,
                'name' => 'uploads-list',
            ],
            'Upload' => [
                'group' => 'Uploads',
                'label' => 'Upload',
                'type' => Permission::TYPE_ADMIN,
                'name' => 'uploads-upload',
            ],
            'Select' => [
                'group' => 'Uploads',
                'label' => 'Select',
                'type' => Permission::TYPE_ADMIN,
                'name' => 'uploads-select',
            ],
            'Download' => [
                'group' => 'Uploads',
                'label' => 'Download',
                'type' => Permission::TYPE_ADMIN,
                'name' => 'uploads-download',
            ],
            'Crop' => [
                'group' => 'Uploads',
                'label' => 'Crop',
                'type' => Permission::TYPE_ADMIN,
                'name' => 'uploads-crop',
            ],
            'Delete' => [
                'group' => 'Uploads',
                'label' => 'Delete',
                'type' => Permission::TYPE_ADMIN,
                'name' => 'uploads-delete',
            ],
        ],
        'Pages' => [
            'List' => [
                'group' => 'Pages',
                'label' => 'List',
                'type' => Permission::TYPE_ADMIN,
                'name' => 'pages-list',
            ],
            'Add' => [
                'group' => 'Pages',
                'label' => 'Add',
                'type' => Permission::TYPE_ADMIN,
                'name' => 'pages-add',
            ],
            'Edit' => [
                'group' => 'Pages',
                'label' => 'Edit',
                'type' => Permission::TYPE_ADMIN,
                'name' => 'pages-edit',
            ],
            'Duplicate' => [
                'group' => 'Pages',
                'label' => 'Duplicate',
                'type' => Permission::TYPE_ADMIN,
                'name' => 'pages-duplicate',
            ],
            'Preview' => [
                'group' => 'Pages',
                'label' => 'Preview',
                'type' => Permission::TYPE_ADMIN,
                'name' => 'pages-preview',
            ],
            'Deleted' => [
                'group' => 'Pages',
                'label' => 'Deleted',
                'type' => Permission::TYPE_ADMIN,
                'name' => 'pages-deleted',
            ],
            'Restore' => [
                'group' => 'Pages',
                'label' => 'Restore',
                'type' => Permission::TYPE_ADMIN,
                'name' => 'pages-restore',
            ],
            'Soft Delete' => [
                'group' => 'Pages',
                'label' => 'Soft Delete',
                'type' => Permission::TYPE_ADMIN,
                'name' => 'pages-soft-delete',
            ],
            'Force Delete' => [
                'group' => 'Pages',
                'label' => 'Force Delete',
                'type' => Permission::TYPE_ADMIN,
                'name' => 'pages-force-delete',
            ],
        ],
        'Blocks' => [
            'List' => [
                'group' => 'Blocks',
                'label' => 'List',
                'type' => Permission::TYPE_ADMIN,
                'name' => 'blocks-list',
            ],
            'Add' => [
                'group' => 'Blocks',
                'label' => 'Add',
                'type' => Permission::TYPE_ADMIN,
                'name' => 'blocks-add',
            ],
            'Edit' => [
                'group' => 'Blocks',
                'label' => 'Edit',
                'type' => Permission::TYPE_ADMIN,
                'name' => 'blocks-edit',
            ],
            'Assign' => [
                'group' => 'Blocks',
                'label' => 'Assign',
                'type' => Permission::TYPE_ADMIN,
                'name' => 'blocks-assign',
            ],
            'Un-Assign' => [
                'group' => 'Blocks',
                'label' => 'Un-Assign',
                'type' => Permission::TYPE_ADMIN,
                'name' => 'blocks-unassign',
            ],
            'Duplicate' => [
                'group' => 'Blocks',
                'label' => 'Duplicate',
                'type' => Permission::TYPE_ADMIN,
                'name' => 'blocks-duplicate',
            ],
            'Deleted' => [
                'group' => 'Blocks',
                'label' => 'Deleted',
                'type' => Permission::TYPE_ADMIN,
                'name' => 'blocks-deleted',
            ],
            'Restore' => [
                'group' => 'Blocks',
                'label' => 'Restore',
                'type' => Permission::TYPE_ADMIN,
                'name' => 'blocks-restore',
            ],
            'Soft Delete' => [
                'group' => 'Blocks',
                'label' => 'Soft Delete',
                'type' => Permission::TYPE_ADMIN,
                'name' => 'blocks-soft-delete',
            ],
            'Force Delete' => [
                'group' => 'Blocks',
                'label' => 'Force Delete',
                'type' => Permission::TYPE_ADMIN,
                'name' => 'blocks-force-delete',
            ],
        ],
        'Menus' => [
            'List' => [
                'group' => 'Menus',
                'label' => 'List',
                'type' => Permission::TYPE_ADMIN,
                'name' => 'menus-list',
            ],
            'Add' => [
                'group' => 'Menus',
                'label' => 'Add',
                'type' => Permission::TYPE_ADMIN,
                'name' => 'menus-add',
            ],
            'Edit' => [
                'group' => 'Menus',
                'label' => 'Edit',
                'type' => Permission::TYPE_ADMIN,
                'name' => 'menus-edit',
            ],
            'Delete' => [
                'group' => 'Menus',
                'label' => 'Delete',
                'type' => Permission::TYPE_ADMIN,
                'name' => 'menus-delete',
            ],
        ],
        'Layouts' => [
            'List' => [
                'group' => 'Layouts',
                'label' => 'List',
                'type' => Permission::TYPE_ADMIN,
                'name' => 'layouts-list',
            ],
            'Add' => [
                'group' => 'Layouts',
                'label' => 'Add',
                'type' => Permission::TYPE_ADMIN,
                'name' => 'layouts-add',
            ],
            'Edit' => [
                'group' => 'Layouts',
                'label' => 'Edit',
                'type' => Permission::TYPE_ADMIN,
                'name' => 'layouts-edit',
            ],
            'Delete' => [
                'group' => 'Layouts',
                'label' => 'Delete',
                'type' => Permission::TYPE_ADMIN,
                'name' => 'layouts-delete',
            ],
        ],
        'Roles' => [
            'List' => [
                'group' => 'Roles',
                'label' => 'List',
                'type' => Permission::TYPE_ADMIN,
                'name' => 'roles-list',
            ],
            'Add' => [
                'group' => 'Roles',
                'label' => 'Add',
                'type' => Permission::TYPE_ADMIN,
                'name' => 'roles-add',
            ],
            'Edit' => [
                'group' => 'Roles',
                'label' => 'Edit',
                'type' => Permission::TYPE_ADMIN,
                'name' => 'roles-edit',
            ],
            'Delete' => [
                'group' => 'Roles',
                'label' => 'Delete',
                'type' => Permission::TYPE_ADMIN,
                'name' => 'roles-delete',
            ],
        ],
        'Admins' => [
            'List' => [
                'group' => 'Admins',
                'label' => 'List',
                'type' => Permission::TYPE_ADMIN,
                'name' => 'admins-list',
            ],
            'Add' => [
                'group' => 'Admins',
                'label' => 'Add',
                'type' => Permission::TYPE_ADMIN,
                'name' => 'admins-add',
            ],
            'Edit' => [
                'group' => 'Admins',
                'label' => 'Edit',
                'type' => Permission::TYPE_ADMIN,
                'name' => 'admins-edit',
            ],
            'Delete' => [
                'group' => 'Admins',
                'label' => 'Delete',
                'type' => Permission::TYPE_ADMIN,
                'name' => 'admins-delete',
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
