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
        foreach (Layout::getFiles() as $key => $file) {
            if (!$key) {
                throw new Exception(
                    'Please create at least one layout file in "resources/layouts/default" directory'
                );
            }

            Layout::create([
                'name' => title_case(trim(str_replace(['.blade.php', '_', '-', '.'], ' ', $file))),
                'identifier' => str_slug(trim(str_replace(['.blade.php', '_', '-', '.'], ' ', $file))),
                'file' => $file
            ]);
        }
    }
}
