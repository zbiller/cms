<?php

use App\Models\Cms\Layout;
use Illuminate\Database\Seeder;

class LayoutsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     * @throws Exception
     */
    public function run()
    {
        DB::table('layouts')->delete();

        /**
         * Create layouts for all files inside the resources/layouts/default directory.
         */
        foreach (Layout::$map as $type => $options) {
            Layout::create([
                'name' => Layout::$types[$type],
                'identifier' => str_slug(strtolower(Layout::$types[$type])),
                'type' => $type,
            ]);
        }
    }
}
