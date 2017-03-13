<?php

use App\Models\Auth\Permission;
use App\Models\Auth\Role;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Seeder;

class PermissionsSeeder extends Seeder
{
    /**
     * @var
     */
    private $adminPermissions;

    /**
     * @var array
     */
    private $adminMap = [
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

        /**
         * Create the Admin necessary permissions.
         */
        $this->adminPermissions = new Collection();

        foreach ($this->adminMap as $group => $labels) {
            foreach ($labels as $label => $data) {
                $this->adminPermissions->push(Permission::create($data));
            }
        }
    }
}
