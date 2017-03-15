<?php

use App\Models\Auth\Role;
use App\Models\Auth\Permission;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Collection;

class RolesSeeder extends Seeder
{
    /**
     * Collection of admin roles.
     *
     * @var Collection
     */
    private $adminRoles;

    /**
     * Mapping structure of admin roles.
     *
     * @var array
     */
    private $adminMap = [
        'Admin' => [
            'name' => 'admin',
            'type' => Role::TYPE_ADMIN,
        ],
        'Owner' => [
            'name' => 'owner',
            'type' => Role::TYPE_ADMIN,
        ],
    ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('roles')->delete();

        $this->adminRoles = new Collection();

        foreach ($this->adminMap as $role => $data) {
            $this->adminRoles->push(Role::create($data));
        }

        /**
         * Assign all permissions to the "owner" role.
         */
        $role = Role::findByName('owner');
        $role->grantPermission(Permission::all());
    }
}
