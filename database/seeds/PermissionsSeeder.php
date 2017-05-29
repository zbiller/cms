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
     * Mapping structure of admin permissions.
     *
     * @var array
     */
    private $adminMap = [
        'Activity Logs' => [
            'List' => [
                'group' => 'Activity Logs',
                'label' => 'List',
                'name' => 'activity-logs-list',
            ],
            'Clean' => [
                'group' => 'Activity Logs',
                'label' => 'Clean',
                'name' => 'activity-logs-clean',
            ],
            'Delete' => [
                'group' => 'Activity Logs',
                'label' => 'Delete',
                'name' => 'activity-logs-delete',
            ],
        ],
        'Drafts' => [
            'List' => [
                'group' => 'Drafts',
                'label' => 'List',
                'name' => 'drafts-list',
            ],
            'Save' => [
                'group' => 'Drafts',
                'label' => 'Save',
                'name' => 'drafts-save',
            ],
            'Publish' => [
                'group' => 'Drafts',
                'label' => 'Publish',
                'name' => 'drafts-publish',
            ],
            'Delete' => [
                'group' => 'Drafts',
                'label' => 'Delete',
                'name' => 'drafts-delete',
            ],
        ],
        'Revisions' => [
            'List' => [
                'group' => 'Revisions',
                'label' => 'List',
                'name' => 'revisions-list',
            ],
            'Rollback' => [
                'group' => 'Revisions',
                'label' => 'Rollback',
                'name' => 'revisions-rollback',
            ],
            'Delete' => [
                'group' => 'Revisions',
                'label' => 'Delete',
                'name' => 'revisions-delete',
            ],
        ],
        'Uploads' => [
            'List' => [
                'group' => 'Uploads',
                'label' => 'List',
                'name' => 'uploads-list',
            ],
            'Upload' => [
                'group' => 'Uploads',
                'label' => 'Upload',
                'name' => 'uploads-upload',
            ],
            'Select' => [
                'group' => 'Uploads',
                'label' => 'Select',
                'name' => 'uploads-select',
            ],
            'Download' => [
                'group' => 'Uploads',
                'label' => 'Download',
                'name' => 'uploads-download',
            ],
            'Crop' => [
                'group' => 'Uploads',
                'label' => 'Crop',
                'name' => 'uploads-crop',
            ],
            'Delete' => [
                'group' => 'Uploads',
                'label' => 'Delete',
                'name' => 'uploads-delete',
            ],
        ],
        'Pages' => [
            'List' => [
                'group' => 'Pages',
                'label' => 'List',
                'name' => 'pages-list',
            ],
            'Add' => [
                'group' => 'Pages',
                'label' => 'Add',
                'name' => 'pages-add',
            ],
            'Edit' => [
                'group' => 'Pages',
                'label' => 'Edit',
                'name' => 'pages-edit',
            ],
            'Duplicate' => [
                'group' => 'Pages',
                'label' => 'Duplicate',
                'name' => 'pages-duplicate',
            ],
            'Preview' => [
                'group' => 'Pages',
                'label' => 'Preview',
                'name' => 'pages-preview',
            ],
            'Deleted' => [
                'group' => 'Pages',
                'label' => 'Deleted',
                'name' => 'pages-deleted',
            ],
            'Restore' => [
                'group' => 'Pages',
                'label' => 'Restore',
                'name' => 'pages-restore',
            ],
            'Soft Delete' => [
                'group' => 'Pages',
                'label' => 'Soft Delete',
                'name' => 'pages-soft-delete',
            ],
            'Force Delete' => [
                'group' => 'Pages',
                'label' => 'Force Delete',
                'name' => 'pages-force-delete',
            ],
        ],
        'Blocks' => [
            'List' => [
                'group' => 'Blocks',
                'label' => 'List',
                'name' => 'blocks-list',
            ],
            'Add' => [
                'group' => 'Blocks',
                'label' => 'Add',
                'name' => 'blocks-add',
            ],
            'Edit' => [
                'group' => 'Blocks',
                'label' => 'Edit',
                'name' => 'blocks-edit',
            ],
            'Assign' => [
                'group' => 'Blocks',
                'label' => 'Assign',
                'name' => 'blocks-assign',
            ],
            'Un-Assign' => [
                'group' => 'Blocks',
                'label' => 'Un-Assign',
                'name' => 'blocks-unassign',
            ],
            'Duplicate' => [
                'group' => 'Blocks',
                'label' => 'Duplicate',
                'name' => 'blocks-duplicate',
            ],
            'Deleted' => [
                'group' => 'Blocks',
                'label' => 'Deleted',
                'name' => 'blocks-deleted',
            ],
            'Restore' => [
                'group' => 'Blocks',
                'label' => 'Restore',
                'name' => 'blocks-restore',
            ],
            'Soft Delete' => [
                'group' => 'Blocks',
                'label' => 'Soft Delete',
                'name' => 'blocks-soft-delete',
            ],
            'Force Delete' => [
                'group' => 'Blocks',
                'label' => 'Force Delete',
                'name' => 'blocks-force-delete',
            ],
        ],
        'Menus' => [
            'List' => [
                'group' => 'Menus',
                'label' => 'List',
                'name' => 'menus-list',
            ],
            'Add' => [
                'group' => 'Menus',
                'label' => 'Add',
                'name' => 'menus-add',
            ],
            'Edit' => [
                'group' => 'Menus',
                'label' => 'Edit',
                'name' => 'menus-edit',
            ],
            'Delete' => [
                'group' => 'Menus',
                'label' => 'Delete',
                'name' => 'menus-delete',
            ],
        ],
        'Layouts' => [
            'List' => [
                'group' => 'Layouts',
                'label' => 'List',
                'name' => 'layouts-list',
            ],
            'Add' => [
                'group' => 'Layouts',
                'label' => 'Add',
                'name' => 'layouts-add',
            ],
            'Edit' => [
                'group' => 'Layouts',
                'label' => 'Edit',
                'name' => 'layouts-edit',
            ],
            'Delete' => [
                'group' => 'Layouts',
                'label' => 'Delete',
                'name' => 'layouts-delete',
            ],
        ],
        'Admin Roles' => [
            'List' => [
                'group' => 'Admin Roles',
                'label' => 'List',
                'name' => 'admin-roles-list',
            ],
            'Add' => [
                'group' => 'Admin Roles',
                'label' => 'Add',
                'name' => 'admin-roles-add',
            ],
            'Edit' => [
                'group' => 'Admin Roles',
                'label' => 'Edit',
                'name' => 'admin-roles-edit',
            ],
            'Delete' => [
                'group' => 'Admin Roles',
                'label' => 'Delete',
                'name' => 'admin-roles-delete',
            ],
        ],
        'Admin Users' => [
            'List' => [
                'group' => 'Admin Users',
                'label' => 'List',
                'name' => 'admin-users-list',
            ],
            'Add' => [
                'group' => 'Admin Users',
                'label' => 'Add',
                'name' => 'admin-users-add',
            ],
            'Edit' => [
                'group' => 'Admin Users',
                'label' => 'Edit',
                'name' => 'admin-users-edit',
            ],
            'Delete' => [
                'group' => 'Admin Users',
                'label' => 'Delete',
                'name' => 'admin-users-delete',
            ],
        ],
    ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('permissions')->delete();

        $this->adminPermissions = new Collection();

        foreach ($this->adminMap as $group => $labels) {
            foreach ($labels as $label => $data) {
                Permission::create($data);
            }
        }
    }
}
