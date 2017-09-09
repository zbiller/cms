<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(SettingsSeeder::class);
        $this->call(PermissionsSeeder::class);
        $this->call(RolesSeeder::class);
        $this->call(UsersSeeder::class);
        $this->call(LayoutsSeeder::class);
        $this->call(PagesSeeder::class);
        $this->call(EmailsSeeder::class);
        $this->call(LanguagesSeeder::class);
        $this->call(CurrenciesSeeder::class);
        $this->call(CountriesSeeder::class);
        $this->call(ShopSeeder::class);
    }
}