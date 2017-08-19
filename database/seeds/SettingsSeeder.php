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
         * Create the generic company settings.
         */
        Setting::create([
            'key' => 'company-name',
            'value' => 'Example Company'
        ]);

        Setting::create([
            'key' => 'company-email',
            'value' => 'example@mail.com'
        ]);

        /**
         * Create the setting for analytics integration.
         */
        Setting::create([
            'key' => 'analytics-code',
            'value' => null
        ]);

        /**
         * Create the settings defining a order's transport cost.
         */
        Setting::create([
            'key' => 'courier-price',
            'value' => null
        ]);

        Setting::create([
            'key' => 'courier-threshold',
            'value' => null
        ]);
    }
}
