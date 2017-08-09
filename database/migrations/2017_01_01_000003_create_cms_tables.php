<?php

use App\Models\Version\Draft;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Kalnoy\Nestedset\NestedSet;

class CreateCmsTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('uploads', function (Blueprint $table) {
            $table->increments('id');

            $table->string('name');
            $table->string('original_name');
            $table->string('path');
            $table->string('full_path')->index()->unique();
            $table->string('extension');
            $table->integer('size');
            $table->string('mime');
            $table->tinyInteger('type')->default(0);

            $table->timestamps();
        });

        Schema::create('layouts', function (Blueprint $table) {
            $table->increments('id');

            $table->string('name')->unique();
            $table->string('identifier')->unique()->nullable();
            $table->tinyInteger('type');

            $table->timestamps();
        });

        Schema::create('pages', function (Blueprint $table) {
            $table->increments('id');
            NestedSet::columns($table);
            $table->integer('layout_id')->unsigned()->index();

            $table->string('name');
            $table->string('slug')->unique();
            $table->string('identifier')->unique()->nullable();

            $table->longText('metadata');
            $table->string('canonical')->nullable();

            $table->tinyInteger('active')->default(1);
            $table->tinyInteger('type')->default(1);

            $table->timestamps();
            $table->softDeletes();
            Draft::column($table);

            $table->foreign('layout_id')->references('id')->on('layouts')->onDelete('cascade')->onUpdate('cascade');
        });

        Schema::create('blocks', function (Blueprint $table) {
            $table->increments('id');

            $table->string('name')->unique();
            $table->string('type');
            $table->string('anchor')->nullable();
            $table->longText('metadata');

            $table->timestamps();
            $table->softDeletes();
            Draft::column($table);
        });

        Schema::create('menus', function (Blueprint $table) {
            $table->increments('id');
            NestedSet::columns($table);
            $table->nullableMorphs('menuable');

            $table->string('name');
            $table->string('url')->nullable();
            $table->string('type');
            $table->string('location');
            $table->longText('metadata');
            $table->tinyInteger('active')->default(1);

            $table->timestamps();
        });

        Schema::create('emails', function (Blueprint $table) {
            $table->increments('id');

            $table->string('name')->unique();
            $table->string('identifier')->unique()->nullable();
            $table->tinyInteger('type')->default(1);
            $table->longText('metadata');

            $table->timestamps();
            $table->softDeletes();
            Draft::column($table);
        });

        Schema::create('urls', function (Blueprint $table) {
            $table->increments('id');
            $table->string('url')->unique();

            $table->morphs('urlable');
            $table->timestamps();
        });

        Schema::create('blockables', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('block_id')->unsigned()->index();
            $table->morphs('blockable');
            $table->string('location');
            $table->integer('ord');

            $table->timestamps();

            $table->foreign('block_id')->references('id')->on('blocks')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::disableForeignKeyConstraints();

        Schema::dropIfExists('blockables');
        Schema::dropIfExists('urls');
        Schema::dropIfExists('emails');
        Schema::dropIfExists('menus');
        Schema::dropIfExists('blocks');
        Schema::dropIfExists('pages');
        Schema::dropIfExists('layouts');
        Schema::dropIfExists('uploads');

        Schema::enableForeignKeyConstraints();
    }
}
