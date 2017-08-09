<?php

use App\Models\Auth\User;
use Illuminate\Database\Seeder;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->delete();

        /**
         * Create the developer user.
         */
        $developer = User::create([
            'username' => 'developer',
            'password' => bcrypt('iwtfki01'),
            'type' => User::TYPE_ADMIN,
            'super' => User::SUPER_YES,
        ]);

        $developer->person()->create([
            'first_name' => 'Developer',
            'last_name' => 'User',
            'email' => 'zbiller@gmail.com',
            'phone' => '+40726583992',
        ]);

        /**
         * Create the owner user.
         */
        $owner = User::create([
            'username' => 'owner',
            'password' => bcrypt('pa55word'),
            'type' => User::TYPE_ADMIN,
        ]);

        $owner->person()->create([
            'first_name' => 'Owner',
            'last_name' => 'User',
            'email' => 'mail@domain.com',
        ]);

        /**
         * Assign "owner" role to the "owner" user
         */
        $owner->assignRoles('owner');
    }
}
