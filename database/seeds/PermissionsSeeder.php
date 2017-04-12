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
        'Pages' => [
            'List' => [
                'name' => 'pages-list',
                'group' => 'Pages',
                'label' => 'List',
            ],
            'Add' => [
                'name' => 'pages-add',
                'group' => 'Pages',
                'label' => 'Add',
            ],
            'Edit' => [
                'name' => 'pages-edit',
                'group' => 'Pages',
                'label' => 'Edit',
            ],
            'Delete' => [
                'name' => 'pages-delete',
                'group' => 'Pages',
                'label' => 'Delete',
            ],
        ],
        'Pages Deleted' => [
            'List' => [
                'name' => 'pages-deleted-list',
                'group' => 'Pages Deleted',
                'label' => 'List',
            ],
            'Add' => [
                'name' => 'pages-deleted-add',
                'group' => 'Pages Deleted',
                'label' => 'Add',
            ],
            'Edit' => [
                'name' => 'pages-deleted-edit',
                'group' => 'Pages Deleted',
                'label' => 'Edit',
            ],
            'Delete' => [
                'name' => 'pages-deleted-delete',
                'group' => 'Pages Deleted',
                'label' => 'Delete',
            ],
        ],
        'Layouts' => [
            'List' => [
                'name' => 'layouts-list',
                'group' => 'Layouts',
                'label' => 'List',
            ],
            'Add' => [
                'name' => 'layouts-add',
                'group' => 'Layouts',
                'label' => 'Add',
            ],
            'Edit' => [
                'name' => 'layouts-edit',
                'group' => 'Layouts',
                'label' => 'Edit',
            ],
            'Delete' => [
                'name' => 'layouts-delete',
                'group' => 'Layouts',
                'label' => 'Delete',
            ],
        ],
        'Uploads' => [
            'List' => [
                'name' => 'uploads-list',
                'group' => 'Uploads',
                'label' => 'List',
            ],
            'Add' => [
                'name' => 'uploads-add',
                'group' => 'Uploads',
                'label' => 'Add',
            ],
            'Edit' => [
                'name' => 'uploads-edit',
                'group' => 'Uploads',
                'label' => 'Edit',
            ],
            'Delete' => [
                'name' => 'uploads-delete',
                'group' => 'Uploads',
                'label' => 'Delete',
            ],
        ],
        'Admin Roles' => [
            'List' => [
                'name' => 'admin-roles-list',
                'group' => 'Admin Roles',
                'label' => 'List',
            ],
            'Add' => [
                'name' => 'admin-roles-add',
                'group' => 'Admin Roles',
                'label' => 'Add',
            ],
            'Edit' => [
                'name' => 'admin-roles-edit',
                'group' => 'Admin Roles',
                'label' => 'Edit',
            ],
            'Delete' => [
                'name' => 'admin-roles-delete',
                'group' => 'Admin Roles',
                'label' => 'Delete',
            ],
        ],
        'Admin Users' => [
            'List' => [
                'name' => 'admin-users-list',
                'group' => 'Admin Users',
                'label' => 'List',
            ],
            'Add' => [
                'name' => 'admin-users-add',
                'group' => 'Admin Users',
                'label' => 'Add',
            ],
            'Edit' => [
                'name' => 'admin-users-edit',
                'group' => 'Admin Users',
                'label' => 'Edit',
            ],
            'Delete' => [
                'name' => 'admin-users-delete',
                'group' => 'Admin Users',
                'label' => 'Delete',
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
