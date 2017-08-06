<?php

namespace App\Scopes;

use DB;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class WithCartTotalScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     *
     * @param Builder $builder
     * @param Model $model
     * @return void
     */
    public function apply(Builder $builder, Model $model)
    {
        // 4600 * 7 + 3000 * 10


        $builder
            ->leftJoin('cart_items', 'carts.id', '=', 'cart_items.cart_id')
            ->leftJoin('products', 'cart_items.product_id', '=', 'products.id')
            ->selectRaw('carts.*, count(cart_items.id) as count, sum(products.price * cart_items.quantity) as total')
            ->groupBy('carts.id');
    }
}