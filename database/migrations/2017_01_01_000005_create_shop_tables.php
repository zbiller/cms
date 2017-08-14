<?php

use App\Models\Version\Draft;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
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
            $table->string('code')->index()->unique();
            $table->string('symbol')->nullable();
            $table->string('format')->nullable();
            $table->float('exchange_rate', 8, 4)->default(0);

            $table->timestamps();
        });

        Schema::create('product_categories', function (Blueprint $table) {
            $table->increments('id');
            NestedSet::columns($table);

            $table->string('name')->unique();
            $table->string('slug')->unique();

            $table->longText('metadata')->nullable();
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
            $table->longText('content')->nullable();

            $table->float('price')->default(0);
            $table->integer('quantity')->default(0);

            $table->integer('views')->default(0);
            $table->integer('sales')->default(0);

            $table->longText('metadata')->nullable();
            $table->tinyInteger('active')->default(1);
            $table->tinyInteger('inherit_discounts')->default(1);
            $table->tinyInteger('inherit_taxes')->default(1);
            $table->integer('ord')->default(0);

            $table->timestamps();
            $table->softDeletes();
            Draft::column($table);

            $table->foreign('category_id')->references('id')->on('product_categories')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('currency_id')->references('id')->on('currencies')->onDelete('set null')->onUpdate('set null');
        });

        Schema::create('orders', function (Blueprint $table) {
            $table->increments('id');

            $table->string('identifier')->unique();
            $table->string('currency');

            $table->float('raw_total')->default(0);
            $table->float('sub_total')->default(0);
            $table->float('grand_total')->default(0);

            $table->longText('customer')->nullable();
            $table->longText('addresses')->nullable();

            $table->tinyInteger('payment')->default(1);
            $table->tinyInteger('shipping')->default(1);
            $table->tinyInteger('status')->default(1);
            $table->tinyInteger('viewed')->default(0);

            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('order_items', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('order_id')->unsigned()->index();
            $table->integer('product_id')->unsigned()->index()->nullable();

            $table->string('name');
            $table->string('currency');
            $table->integer('quantity');

            $table->float('raw_price');
            $table->float('sub_price');
            $table->float('grand_price');

            $table->float('raw_total');
            $table->float('sub_total');
            $table->float('grand_total');

            $table->timestamps();

            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('set null');
        });

        Schema::create('carts', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned()->index()->nullable();

            $table->string('user_token')->unique()->nullable();
            $table->string('identifier')->unique();

            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
        });

        Schema::create('cart_items', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('cart_id')->unsigned()->index()->nullable();
            $table->integer('product_id')->unsigned()->index()->nullable();

            $table->integer('quantity')->default(1);

            $table->timestamps();

            $table->foreign('cart_id')->references('id')->on('carts')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade')->onUpdate('cascade');
        });

        Schema::create('attribute_sets', function (Blueprint $table) {
            $table->increments('id');

            $table->string('name')->unique();
            $table->string('slug')->unique()->nullable();
            $table->integer('ord')->default(0);

            $table->timestamps();
        });

        Schema::create('attributes', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('set_id')->unsigned()->index();

            $table->string('name');
            $table->string('slug')->nullable();

            $table->tinyInteger('filterable')->default(0);
            $table->integer('ord')->default(0);

            $table->timestamps();

            $table->foreign('set_id')->references('id')->on('attribute_sets')->onDelete('cascade')->onUpdate('cascade');
        });

        Schema::create('attribute_values', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('attribute_id')->unsigned()->index();

            $table->text('value');
            $table->string('slug')->nullable();
            $table->integer('ord')->default(0);

            $table->timestamps();

            $table->foreign('attribute_id')->references('id')->on('attributes')->onDelete('cascade')->onUpdate('cascade');
        });

        Schema::create('discounts', function (Blueprint $table) {
            $table->increments('id');

            $table->string('name')->unique();
            $table->float('rate')->default(0);

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
            $table->float('rate')->default(0);

            $table->tinyInteger('type')->default(1);
            $table->tinyInteger('for')->default(1);
            $table->tinyInteger('active')->default(1);

            $table->timestamp('start_date')->nullable();
            $table->timestamp('end_date')->nullable();
            $table->integer('max_val')->nullable();

            $table->timestamps();
        });

        Schema::create('product_category', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('product_id')->unsigned()->index();
            $table->integer('category_id')->unsigned()->index();

            $table->timestamps();

            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('category_id')->references('id')->on('product_categories')->onDelete('cascade')->onUpdate('cascade');
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
            $table->foreign('value_id')->references('id')->on('attribute_values')->onDelete('set null');
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

        Schema::create('category_attribute', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('category_id')->unsigned()->index();
            $table->integer('attribute_id')->unsigned()->index();

            $table->timestamps();

            $table->foreign('category_id')->references('id')->on('product_categories')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('attribute_id')->references('id')->on('attributes')->onDelete('cascade')->onUpdate('cascade');
        });

        Schema::create('category_discount', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('category_id')->unsigned()->index();
            $table->integer('discount_id')->unsigned()->index();
            $table->integer('ord')->default(0);

            $table->timestamps();

            $table->foreign('category_id')->references('id')->on('product_categories')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('discount_id')->references('id')->on('discounts')->onDelete('cascade')->onUpdate('cascade');
        });

        Schema::create('category_tax', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('category_id')->unsigned()->index();
            $table->integer('tax_id')->unsigned()->index();
            $table->integer('ord')->default(0);

            $table->timestamps();

            $table->foreign('category_id')->references('id')->on('product_categories')->onDelete('cascade')->onUpdate('cascade');
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

        Schema::dropIfExists('category_tax');
        Schema::dropIfExists('category_discount');
        Schema::dropIfExists('category_attribute');
        Schema::dropIfExists('product_tax');
        Schema::dropIfExists('product_discount');
        Schema::dropIfExists('product_attribute');
        Schema::dropIfExists('product_category');
        Schema::dropIfExists('taxes');
        Schema::dropIfExists('discounts');
        Schema::dropIfExists('attribute_values');
        Schema::dropIfExists('attributes');
        Schema::dropIfExists('attribute_sets');
        Schema::dropIfExists('cart_items');
        Schema::dropIfExists('carts');
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
        Schema::dropIfExists('products');
        Schema::dropIfExists('product_categories');
        Schema::dropIfExists('currencies');

        Schema::enableForeignKeyConstraints();
    }
}