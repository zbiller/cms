<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTestTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('test', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->unique();
            $table->longText('content');
            $table->tinyInteger('type');

            $table->timestamps();
        });

        Schema::create('test_hasone_1', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('test_id')->unsigned()->index();
            $table->string('name')->unique();

            $table->timestamps();

            $table->foreign('test_id')->references('id')->on('test')->onDelete('cascade')->onUpdate('cascade');
        });

        Schema::create('test_hasone_2', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('test_id')->unsigned()->index();
            $table->string('name')->unique();

            $table->timestamps();

            $table->foreign('test_id')->references('id')->on('test')->onDelete('cascade')->onUpdate('cascade');
        });

        Schema::create('test_hasmany_1', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('test_id')->unsigned()->index();
            $table->string('name')->unique();

            $table->timestamps();

            $table->foreign('test_id')->references('id')->on('test')->onDelete('cascade')->onUpdate('cascade');
        });

        Schema::create('test_hasmany_2', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('test_id')->unsigned()->index();
            $table->string('name')->unique();

            $table->timestamps();

            $table->foreign('test_id')->references('id')->on('test')->onDelete('cascade')->onUpdate('cascade');
        });

        Schema::create('test_habtm', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('date');

            $table->timestamps();
        });

        Schema::create('test_test_habtm_ring', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('test_id')->unsigned()->index();
            $table->integer('test_habtm_id')->unsigned()->index();

            $table->foreign('test_id')->references('id')->on('test')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('test_habtm_id')->references('id')->on('test_habtm')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('test_test_habtm_ring');
        Schema::dropIfExists('test_habtm');
        Schema::dropIfExists('test_hasmany_2');
        Schema::dropIfExists('test_hasmany_1');
        Schema::dropIfExists('test_hasone_2');
        Schema::dropIfExists('test_hasone_1');
        Schema::dropIfExists('test');
    }
}
