<?php

use App\Models\Version\Draft;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Kalnoy\Nestedset\NestedSet;

class CreateShopTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('currencies', function (Blueprint $table) {
            $table->increments('id');

            $table->string('name')->unique();
            $table->string('code')->unique();

            $table->timestamps();
        });

        Schema::create('categories', function (Blueprint $table) {
            $table->increments('id');
            NestedSet::columns($table);

            $table->string('name')->unique();
            $table->string('slug')->unique();

            $table->longText('metadata');
            $table->tinyInteger('active')->default(1);

            $table->timestamps();
            $table->softDeletes();
            Draft::column($table);
        });

        Schema::create('sets', function (Blueprint $table) {
            $table->increments('id');

            $table->string('name')->unique();
            $table->string('slug')->unique();
            $table->integer('ord')->default(0);

            $table->timestamps();
        });

        Schema::create('attributes', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('set_id')->unsigned()->index();

            $table->string('name')->unique();
            $table->string('slug')->unique();
            $table->text('value');

            $table->tinyInteger('type')->default(1);
            $table->integer('ord')->default(0);

            $table->timestamps();

            $table->foreign('set_id')->references('id')->on('sets')->onDelete('cascade')->onUpdate('cascade');
        });

        Schema::create('discounts', function (Blueprint $table) {
            $table->increments('id');

            $table->string('name')->unique();
            $table->float('rate');

            $table->integer('uses')->nullable();
            $table->tinyInteger('type')->default(1);
            $table->tinyInteger('for')->default(1);
            $table->tinyInteger('active')->default(1);

            $table->timestamp('start_date')->nullable();
            $table->timestamp('end_date')->nullable();
            $table->integer('min_val')->nullable();

            $table->timestamps();
        });

        Schema::create('taxes', function (Blueprint $table) {
            $table->increments('id');

            $table->string('name')->unique();
            $table->float('rate');

            $table->integer('uses')->nullable();
            $table->tinyInteger('type')->default(1);
            $table->tinyInteger('for')->default(1);
            $table->tinyInteger('active')->default(1);

            $table->timestamp('start_date')->nullable();
            $table->timestamp('end_date')->nullable();
            $table->integer('max_val')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('taxes');
        Schema::dropIfExists('discounts');
        Schema::dropIfExists('attributes');
        Schema::dropIfExists('sets');
        Schema::dropIfExists('categories');
        Schema::dropIfExists('currencies');
    }
}
