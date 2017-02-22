<?php

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
        User::create([
            'username' => 'developer',
            'password' => bcrypt('iwtfki01')
        ]);
    }
}
