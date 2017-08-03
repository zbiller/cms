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

        Schema::create('products', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('category_id')->unsigned()->index();
            $table->integer('currency_id')->unsigned()->index()->nullable();

            $table->string('sku')->unique();
            $table->string('name')->unique();
            $table->string('slug')->unique();
            $table->text('content')->nullable();

            $table->float('price')->default(0);
            $table->integer('quantity')->default(0);

            $table->integer('views')->default(0);
            $table->integer('sales')->default(0);

            $table->text('metadata')->nullable();
            $table->tinyInteger('active')->default(1);
            $table->integer('ord')->default(0);

            $table->timestamps();
            $table->softDeletes();
            Draft::column($table);

            $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('currency_id')->references('id')->on('currencies')->onDelete('set null')->onUpdate('set null');
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

            $table->string('name');
            $table->string('slug');
            $table->integer('ord')->default(0);

            $table->timestamps();

            $table->foreign('set_id')->references('id')->on('sets')->onDelete('cascade')->onUpdate('cascade');
        });

        Schema::create('values', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('attribute_id')->unsigned()->index();

            $table->text('value');
            $table->string('slug');
            $table->integer('ord')->default(0);

            $table->timestamps();

            $table->foreign('attribute_id')->references('id')->on('attributes')->onDelete('cascade')->onUpdate('cascade');
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

        Schema::create('product_attribute', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('product_id')->unsigned()->index();
            $table->integer('attribute_id')->unsigned()->index();
            $table->integer('value_id')->unsigned()->index()->nullable();
            $table->text('value')->nullable();
            $table->integer('ord')->default(0);

            $table->timestamps();

            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('attribute_id')->references('id')->on('attributes')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('value_id')->references('id')->on('values')->onDelete('set null');
        });

        Schema::create('product_discount', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('product_id')->unsigned()->index();
            $table->integer('discount_id')->unsigned()->index();
            $table->integer('ord')->default(0);

            $table->timestamps();

            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('discount_id')->references('id')->on('discounts')->onDelete('cascade')->onUpdate('cascade');
        });

        Schema::create('product_tax', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('product_id')->unsigned()->index();
            $table->integer('tax_id')->unsigned()->index();
            $table->integer('ord')->default(0);

            $table->timestamps();

            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('tax_id')->references('id')->on('taxes')->onDelete('cascade')->onUpdate('cascade');
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

        Schema::dropIfExists('product_tax');
        Schema::dropIfExists('product_discount');
        Schema::dropIfExists('product_attribute');
        Schema::dropIfExists('taxes');
        Schema::dropIfExists('discounts');
        Schema::dropIfExists('values');
        Schema::dropIfExists('attributes');
        Schema::dropIfExists('sets');
        Schema::dropIfExists('products');
        Schema::dropIfExists('categories');
        Schema::dropIfExists('currencies');

        Schema::enableForeignKeyConstraints();
    }
}
