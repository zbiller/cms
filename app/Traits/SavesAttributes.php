<?php

namespace App\Traits;

use App\Models\Model;
use App\Models\Shop\Attribute;

trait SavesAttributes
{
    /**
     * Boot the trait.
     */
    public static function bootSavesAttributes()
    {
        static::saved(function (Model $model) {
            if (request()->has('touch_attributes')) {
                $model->touchAttributes();
            }
        });
    }

    /**
     * Save the attributes many to many relation.
     *
     * @return void
     */
    protected function touchAttributes()
    {
        $attributes = request()->get('attributes');

        $this->attributes()->detach();

        if ($attributes && is_array($attributes) && !empty($attributes)) {
            ksort($attributes);

            foreach ($attributes as $data) {
                foreach ($data as $id => $attr) {
                    if ($id && ($attribute = Attribute::find($id))) {
                        $this->attributes()->save($attribute, $attr);
                    }
                }
            }
        }
    }
}
