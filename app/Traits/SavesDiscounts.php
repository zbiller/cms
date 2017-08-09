<?php

namespace App\Traits;

use App\Models\Model;
use App\Models\Shop\Discount;

trait SavesDiscounts
{
    /**
     * Boot the trait.
     */
    public static function bootSavesDiscounts()
    {
        static::saved(function (Model $model) {
            if (request()->has('touch_discounts')) {
                $model->touchDiscounts();
            }
        });
    }

    /**
     * Save the discounts many to many relation.
     *
     * @return void
     */
    protected function touchDiscounts()
    {
        $discounts = request()->get('discounts');

        $this->discounts()->detach();

        if ($discounts && is_array($discounts) && !empty($discounts)) {
            ksort($discounts);

            foreach ($discounts as $data) {
                foreach ($data as $id => $attributes) {
                    if ($id && ($discount = Discount::find($id))) {
                        $this->discounts()->save($discount, $attributes);
                    }
                }
            }
        }
    }
}
