<?php

use App\Models\Config\Setting;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     * @throws Exception
     */
    public function run()
    {
        DB::table('settings')->delete();

        /**
         * Create the basic settings the platform supports out of the box.
         */
        Setting::create([
            'key' => 'company-name',
            'value' => 'Example Company'
        ]);

        Setting::create([
            'key' => 'company-email',
            'value' => 'example@mail.com'
        ]);

        Setting::create([
            'key' => 'analytics-code',
            'value' => null
        ]);
    }
}
