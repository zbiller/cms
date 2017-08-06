<?php

namespace App\Scopes;

use DB;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class WithCartTotalAndCountScope implements Scope
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
        $builder
            ->select('carts.*')
            ->leftJoin('cart_items', 'carts.id', '=', 'cart_items.cart_id')
            ->leftJoin('products', 'cart_items.product_id', '=', 'products.id')
            ->leftJoin('currencies', 'products.currency_id', '=', 'currencies.id')
            ->groupBy('carts.id')
            ->addSelect([
                DB::raw('count(cart_items.id) as count'),
                DB::raw('sum(products.price * currencies.exchange_rate * cart_items.quantity) as total'),
            ]);
    }
}