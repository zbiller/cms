<?php

use App\Models\Auth\Person;
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
         * Create the developer admin user.
         */
        $user = User::create([
            'username' => 'developer',
            'password' => bcrypt('iwtfki01'),
        ]);

        $user->person()->create([
            'first_name' => 'Developer',
            'last_name' => 'User',
            'email' => 'zbiller@gmail.com',
            'phone' => '+40726583992',
        ]);
    }
}
