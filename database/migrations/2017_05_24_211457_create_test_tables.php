<?php

use App\Models\Upload\Upload;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTestTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cars_owners', function (Blueprint $table) {
            $table->increments('id');
            $table->string('first_name');
            $table->string('last_name');

            $table->timestamps();
        });

        Schema::create('cars_brands', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');

            $table->timestamps();
        });

        Schema::create('cars', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('owner_id')->unsigned()->index();
            $table->integer('brand_id')->unsigned()->index();

            $table->string('name')->unique();
            $table->string('slug')->unique();

            Upload::column('image', $table);
            Upload::column('video', $table);
            Upload::column('audio', $table);
            Upload::column('file', $table);

            $table->longText('metadata');

            $table->timestamps();

            $table->foreign('owner_id')->references('id')->on('cars_owners')->onDelete('cascade');
            $table->foreign('brand_id')->references('id')->on('cars_brands')->onDelete('cascade');
        });

        Schema::create('cars_books', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('car_id')->unsigned()->index();

            $table->string('name');

            $table->timestamps();

            $table->foreign('car_id')->references('id')->on('cars')->onDelete('cascade');
        });

        Schema::create('cars_pieces', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('car_id')->unsigned()->index();

            $table->string('name');

            $table->timestamps();

            $table->foreign('car_id')->references('id')->on('cars')->onDelete('cascade');
        });

        Schema::create('cars_mechanics', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');

            $table->timestamps();
        });

        Schema::create('cars_mechanics_ring', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('car_id')->unsigned()->index();
            $table->integer('mechanic_id')->unsigned()->index();

            $table->foreign('car_id')->references('id')->on('cars')->onDelete('cascade');
            $table->foreign('mechanic_id')->references('id')->on('cars_mechanics')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cars_mechanics_ring');
        Schema::dropIfExists('cars_mechanics');
        Schema::dropIfExists('cars_pieces');
        Schema::dropIfExists('cars_books');
        Schema::dropIfExists('cars');
        Schema::dropIfExists('cars_brands');
        Schema::dropIfExists('cars_owners');
    }
}
