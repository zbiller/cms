<?php

use App\Models\Auth\Person;
use App\Models\Auth\Role;
use App\Models\Auth\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Seeder;

class RolesSeeder extends Seeder
{
    /**
     * @var
     */
    private $adminRoles;

    /**
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

        /**
         * Create the necessary Admin roles.
         */
        $this->adminRoles = new Collection();

        foreach ($this->adminMap as $role => $data) {
            $this->adminRoles->push(Role::create($data));
        }

        /**
         * Assign all Admin roles to the Developer user.
         */
        $user = User::where('username', 'developer')->first();
        $user->assignRoles($this->adminRoles);
    }
}
