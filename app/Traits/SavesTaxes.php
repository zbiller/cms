<?php

namespace App\Traits;

use App\Models\Model;
use App\Models\Shop\Tax;

trait SavesTaxes
{
    /**
     * Boot the trait.
     */
    public static function bootSavesTaxes()
    {
        static::saved(function (Model $model) {
            if (request()->has('touch_taxes')) {
                $model->touchTaxes();
            }
        });
    }

    /**
     * Save the taxes many to many relation.
     *
     * @return void
     */
    protected function touchTaxes()
    {
        $taxes = request()->get('taxes');

        $this->taxes()->detach();

        if ($taxes && is_array($taxes) && !empty($taxes)) {
            ksort($taxes);

            foreach ($taxes as $data) {
                foreach ($data as $id => $attributes) {
                    if ($id && ($tax = Tax::find($id))) {
                        $this->taxes()->save($tax, $attributes);
                    }
                }
            }
        }
    }
}
