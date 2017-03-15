<?php

use App\Models\Auth\User;
use App\Models\Auth\Role;
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

        $user = User::where('username', 'developer')->first();
        $user->assignRoles($this->adminRoles);
    }
}
