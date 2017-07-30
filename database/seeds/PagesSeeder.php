<?php

use Carbon\Carbon;
use App\Models\Cms\Page;
use App\Models\Cms\Layout;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class PagesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('pages')->delete();
        DB::table('urls')->where('urlable_type', Page::class)->delete();

        /**
         * Get the default or first layout.
         */
        try {
            $layout = Layout::whereIdentifier('default')->firstOrFail();
        } catch (ModelNotFoundException $e) {
            $layout = Layout::first();
        }

        /**
         * Create the home page.
         */
        Page::create([
            'layout_id' => $layout->id,
            'name' => 'Home',
            'slug' => '/',
            'identifier' => 'home',
            'active' => Page::ACTIVE_YES,
            'type' => Page::TYPE_DEFAULT,
        ]);

        /**
         * Create the shop page.
         */
        Page::create([
            'layout_id' => $layout->id,
            'name' => 'Shop',
            'slug' => 'shop',
            'identifier' => 'shop',
            'active' => Page::ACTIVE_YES,
            'type' => Page::TYPE_DEFAULT,
        ]);
    }
}
