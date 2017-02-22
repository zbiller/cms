<?php

use App\Models\Auth\Person;
use App\Models\Auth\User;
use Illuminate\Database\Seeder;

class AdminUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->delete();

        $user = User::create([
            'username' => 'developer',
            'password' => bcrypt('iwtfki01'),
        ]);

        $person = Person::create([
            'user_id' => $user->id,
            'first_name' => 'Developer',
            'last_name' => 'User',
            'email' => 'zbiller@gmail.com',
            'phone' => '+40726583992',
        ]);
    }
}
