<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Kalnoy\Nestedset\NestedSet;

class CreatePagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pages', function (Blueprint $table) {
            $table->increments('id');

            NestedSet::columns($table);

            $table->integer('layout_id')->unsigned()->index();

            $table->string('name');
            $table->string('slug')->unique();
            $table->string('identifier')->unique()->nullable();

            $table->text('metadata');

            $table->string('canonical');
            $table->tinyInteger('active')->default(1);
            $table->tinyInteger('type')->default(1);

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('layout_id')->references('id')->on('layouts')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pages');
    }
}
