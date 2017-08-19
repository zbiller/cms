<?php

namespace App\Models\Shop\Cart;

use App\Models\Model;
use App\Models\Shop\Cart;
use App\Models\Shop\Product;
use App\Traits\IsCacheable;

class Item extends Model
{
    use IsCacheable;

    /**
     * The database table.
     *
     * @var string
     */
    protected $table = 'cart_items';

    /**
     * The attributes that mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'cart_id',
        'product_id',
        'quantity',
    ];

    /**
     * The relations that are eager-loaded.
     *
     * @var array
     */
    protected $with = [
        'cart',
        'product',
    ];

    /**
     * Cart item belongs to cart.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function cart()
    {
        return $this->belongsTo(Cart::class, 'cart_id');
    }

    /**
     * Cart item belongs to product.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}