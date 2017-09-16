<?php

use App\Models\Auth\Permission;
use App\Models\Auth\Role;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Seeder;

class RolesSeeder extends Seeder
{
    /**
     * Collection of admin roles.
     *
     * @var Collection
     */
    private $adminRoles;

    /**
     * Collection of front-end roles.
     *
     * @var Collection
     */
    private $frontRoles;

    /**
     * Mapping structure of admin roles.
     *
     * @var array
     */
    private $adminMap = [
        'Owner' => [
            'name' => 'owner',
            'guard' => 'admin',
        ],
    ];

    /**
     * Mapping structure of front-end roles.
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
        DB::table('roles')->delete();

        $this->adminRoles = new Collection();
        $this->frontRoles = new Collection();

        foreach ($this->adminMap as $role => $data) {
            $this->adminRoles->push(Role::create($data));
        }

        foreach ($this->frontMap as $role => $data) {
            $this->frontRoles->push(Role::create($data));
        }

        /**
         * Assign all permissions to the "owner" role.
         */
        $role = Role::findByName('owner');
        $role->grantPermission(Permission::whereGuard('admin')->get());
    }
}
