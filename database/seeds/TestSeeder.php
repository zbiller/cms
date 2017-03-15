<?php

use Carbon\Carbon;
use App\Models\Test\Test;
use App\Models\Test\TestHasOne1;
use App\Models\Test\TestHasOne2;
use App\Models\Test\TestHasMany1;
use App\Models\Test\TestHasMany2;
use App\Models\Test\TestHabtm;
use Illuminate\Database\Seeder;

class TestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('test_test_habtm_ring')->delete();
        DB::table('test_habtm')->delete();
        DB::table('test_hasmany_2')->delete();
        DB::table('test_hasmany_1')->delete();
        DB::table('test_hasone_2')->delete();
        DB::table('test_hasone_1')->delete();
        DB::table('test')->delete();

        Test::create(['name' => 'Sample name', 'content' => 'content', 'type' => '2']);
        Test::create(['name' => 'Type 3', 'content' => 'sample content', 'type' => '3']);

        $test = Test::create(['name' => 'Test Name', 'content' => 'content', 'type' => '1']);

        TestHasOne1::create(['test_id' => $test->id, 'name' => 'Test Has One 1 Name 1']);
        TestHasOne1::create(['test_id' => $test->id, 'name' => 'Test Has One 1 Name 2']);

        TestHasOne2::create(['test_id' => $test->id, 'name' => 'Test Has One 2 Name 1']);
        TestHasOne2::create(['test_id' => $test->id, 'name' => 'Test Has One 2 Name 2']);

        TestHasMany1::create(['test_id' => $test->id, 'name' => 'Test Has Many 1 Name 1']);
        TestHasMany1::create(['test_id' => $test->id, 'name' => 'Test Has Many 1 Name 2']);

        TestHasMany2::create(['test_id' => $test->id, 'name' => 'Test Has Many 2 Name 1']);
        TestHasMany2::create(['test_id' => $test->id, 'name' => 'Test Has Many 2 Name 2']);

        $testHabtm1 = TestHabtm::create(['name' => 'Test Habtm Name 1', 'email' => 'mail_1@mail.com', 'date' => Carbon::now()]);
        $testHabtm2 = TestHabtm::create(['name' => 'Test Habtm Name 2', 'email' => 'mail_2@mail.com', 'date' => Carbon::now()]);

        $test->testHabtm()->attach([$testHabtm1->id, $testHabtm2->id]);
    }
}
