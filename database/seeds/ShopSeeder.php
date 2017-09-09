<?php

use App\Models\Shop\Attribute;
use App\Models\Shop\Attribute\Set;
use App\Models\Shop\Attribute\Value;
use App\Models\Shop\Cart;
use App\Models\Shop\Category as ProductCategory;
use App\Models\Localisation\Currency;
use App\Models\Shop\Discount;
use App\Models\Shop\Product;
use App\Models\Shop\Tax;
use Illuminate\Database\Seeder;

class ShopSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('product_categories')->delete();
        DB::table('products')->delete();
        DB::table('discounts')->delete();
        DB::table('taxes')->delete();
        DB::table('attribute_sets')->delete();
        DB::table('attributes')->delete();

        DB::table('urls')->whereNotIn('url', [
            '/', 'account', 'shop'
        ])->delete();

        /**
         * Create shop categories.
         */
        $category_1 = ProductCategory::create([
            'name' => 'Laptops',
            'slug' => 'laptops',
            'active' => ProductCategory::ACTIVE_YES,
        ]);

        $category_1_1 = ProductCategory::create([
            'name' => 'Mac',
            'slug' => 'mac',
            'active' => ProductCategory::ACTIVE_YES,
        ], $category_1);

        $category_1_2 = ProductCategory::create([
            'name' => 'Asus',
            'slug' => 'asus',
            'active' => ProductCategory::ACTIVE_YES,
        ], $category_1);

        $category_1_3 = ProductCategory::create([
            'name' => 'Acer',
            'slug' => 'acer',
            'active' => ProductCategory::ACTIVE_YES,
        ], $category_1);

        $category_1_4 = ProductCategory::create([
            'name' => 'Lenovo',
            'slug' => 'Lenovo',
            'active' => ProductCategory::ACTIVE_YES,
        ], $category_1);

        $category_1_5 = ProductCategory::create([
            'name' => 'HP',
            'slug' => 'hp',
            'active' => ProductCategory::ACTIVE_YES,
        ], $category_1);

        $category_2 = ProductCategory::create([
            'name' => 'Phones',
            'slug' => 'phones',
            'active' => ProductCategory::ACTIVE_YES,
        ]);

        $category_2_1 = ProductCategory::create([
            'name' => 'iPhone',
            'slug' => 'iphone',
            'active' => ProductCategory::ACTIVE_YES,
        ], $category_2);

        $category_2_2 = ProductCategory::create([
            'name' => 'Samsung',
            'slug' => 'samsung',
            'active' => ProductCategory::ACTIVE_YES,
        ], $category_2);

        $category_2_3 = ProductCategory::create([
            'name' => 'HTC',
            'slug' => 'htc',
            'active' => ProductCategory::ACTIVE_YES,
        ], $category_2);

        /**
         * Create shop discounts.
         */
        $discount_product_1 = Discount::create([
            'name' => '20% off',
            'rate' => '20',
            'type' => Discount::TYPE_PERCENT,
            'for' => Discount::FOR_PRODUCT,
            'active' => Discount::ACTIVE_YES,
        ]);

        $discount_product_2 = Discount::create([
            'name' => '50% off',
            'rate' => '50',
            'type' => Discount::TYPE_PERCENT,
            'for' => Discount::FOR_PRODUCT,
            'active' => Discount::ACTIVE_YES,
        ]);

        $discount_product_3 = Discount::create([
            'name' => 'Minus 30',
            'rate' => '30',
            'type' => Discount::TYPE_FIXED,
            'for' => Discount::FOR_PRODUCT,
            'active' => Discount::ACTIVE_YES,
        ]);

        $discount_product_4 = Discount::create([
            'name' => 'Minus 70',
            'rate' => '70',
            'type' => Discount::TYPE_FIXED,
            'for' => Discount::FOR_PRODUCT,
            'active' => Discount::ACTIVE_YES,
        ]);

        $discount_order_1 = Discount::create([
            'name' => 'All around 25%',
            'rate' => '25',
            'type' => Discount::TYPE_PERCENT,
            'for' => Discount::FOR_ORDER,
            'active' => Discount::ACTIVE_YES,
        ]);

        $discount_order_2 = Discount::create([
            'name' => 'All around minus 85',
            'rate' => '82',
            'type' => Discount::TYPE_FIXED,
            'for' => Discount::FOR_ORDER,
            'active' => Discount::ACTIVE_YES,
        ]);

        /**
         * Create shop discounts.
         */
        $tax_order_1 = Tax::create([
            'name' => 'TVA',
            'rate' => '24',
            'type' => Tax::TYPE_PERCENT,
            'for' => Tax::FOR_ORDER,
            'active' => Tax::ACTIVE_YES,
        ]);

        $tax_product_1 = Tax::create([
            'name' => '20% over',
            'rate' => '20',
            'type' => Tax::TYPE_PERCENT,
            'for' => Tax::FOR_PRODUCT,
            'active' => Tax::ACTIVE_YES,
        ]);

        $tax_product_2 = Tax::create([
            'name' => '50 over',
            'rate' => '50',
            'type' => Tax::TYPE_FIXED,
            'for' => Tax::FOR_PRODUCT,
            'active' => Tax::ACTIVE_YES,
        ]);

        /**
         * Create shop sets.
         */
        $set_1 = Set::create([
            'name' => 'Producer',
            'slug' => 'producer',
        ]);

        $set_2 = Set::create([
            'name' => 'Processor',
            'slug' => 'processor',
        ]);

        $set_3 = Set::create([
            'name' => 'Memory',
            'slug' => 'memory',
        ]);

        /**
         * Create shop attributes.
         */
        $attribute_1 = Attribute::create([
            'set_id' => $set_1->id,
            'name' => 'Producer',
            'slug' => 'producer',
        ]);

        $attribute_2 = Attribute::create([
            'set_id' => $set_2->id,
            'name' => 'Socket',
            'slug' => 'socket',
        ]);

        $attribute_3 = Attribute::create([
            'set_id' => $set_3->id,
            'name' => 'Capacity',
            'slug' => 'capacity',
        ]);

        $attribute_4 = Attribute::create([
            'set_id' => $set_3->id,
            'name' => 'Frequency',
            'slug' => 'frequency',
        ]);

        /**
         * Create shop attribute values.
         */
        $value_1_1 = Value::create([
            'attribute_id' => $attribute_1->id,
            'value' => 'Apple',
            'slug' => 'apple',
        ]);

        $value_1_2 = Value::create([
            'attribute_id' => $attribute_1->id,
            'value' => 'Samsung',
            'slug' => 'samsung',
        ]);

        $value_1_3 = Value::create([
            'attribute_id' => $attribute_1->id,
            'value' => 'Hewllet Packard',
            'slug' => 'hewllet-packard',
        ]);

        $value_2_1 = Value::create([
            'attribute_id' => $attribute_2->id,
            'value' => '2066',
            'slug' => '2066',
        ]);

        $value_2_2 = Value::create([
            'attribute_id' => $attribute_2->id,
            'value' => '4018',
            'slug' => '4018',
        ]);

        $value_3_1 = Value::create([
            'attribute_id' => $attribute_3->id,
            'value' => '200 GB',
            'slug' => '200-gb',
        ]);

        $value_3_2 = Value::create([
            'attribute_id' => $attribute_3->id,
            'value' => '500 GB',
            'slug' => '500-gb',
        ]);

        $value_3_3 = Value::create([
            'attribute_id' => $attribute_3->id,
            'value' => '1 TB',
            'slug' => '1-tb',
        ]);

        $value_4_1 = Value::create([
            'attribute_id' => $attribute_4->id,
            'value' => '2400 Mhz',
            'slug' => '2400-mhz',
        ]);

        $value_4_2 = Value::create([
            'attribute_id' => $attribute_4->id,
            'value' => '1 Ghz',
            'slug' => '1-ghz',
        ]);

        /**
         * Create shop products.
         */
        $product_1 = Product::create([
            'category_id' => $category_1_1->id,
            'currency_id' => Currency::whereCode('USD')->first()->id,
            'sku' => 'ZBL-MAC-1',
            'name' => 'Macbook Pro 15',
            'slug' => 'macbook-pro-15',
            'price' => '3000',
            'quantity' => '15',
            'content' => '<p>Some content here</p>',
            'active' => Product::ACTIVE_YES,
        ]);

        $product_2 = Product::create([
            'category_id' => $category_2_1->id,
            'currency_id' => Currency::whereCode('RON')->first()->id,
            'sku' => 'ZBL-IPHONE-1',
            'name' => 'iPhone 7S 32GB',
            'slug' => 'iphone-7s-32gb',
            'price' => '4600',
            'quantity' => '50',
            'content' => '<p>Some content here</p>',
            'active' => Product::ACTIVE_YES,
        ]);

        /**
         * Create cart entry
         */
        auth()->loginUsingId(1);
        $product_1->addToCart(3);
        $product_2->addToCart(5);

        /**
         * Create order entry
         */
        Cart::placeOrder(
            [],
            [
                'first_name' => 'Andrei',
                'last_name' => 'Badea',
                'email' => 'zbiller@gmail.com',
                'phone' => '0726583992',
            ],
            [
                'shipping' => [
                    'country' => 'Romania',
                    'state' => 'Bucharest',
                    'city' => 'Bucharest',
                    'address' => 'str. Adrian Carstea, nr. 13, bl. P37, sc. 1, et. 1, ap. 159',
                ],
                'billing' => [
                    'country' => 'Romania',
                    'state' => 'Dambovita',
                    'city' => 'Targoviste',
                    'address' => 'bld. Unirii, nr. 10, bl. A, sc. 1, et. 1, ap. 5',
                ]
            ]
        );

    }
}
