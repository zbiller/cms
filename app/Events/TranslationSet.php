<?php

namespace App\Events;

use Illuminate\Database\Eloquent\Model;

class TranslationSet
{
    /**
     * @var Model
     */
    public $model;

    /**
     * @var string
     */
    public $key;

    /**
     * @var string
     */
    public $locale;

    /**
     * @var array|string
     */
    public $oldValue;

    /**
     * @var array|string
     */
    public $newValue;

    /**
     * @param Model $model
     * @param string $key
     * @param string $locale
     * @param array|string $oldValue
     * @param array|string $newValue
     */
    public function __construct(Model $model, $key, $locale, $oldValue, $newValue)
    {
        $this->model = $model;
        $this->attribute = $key;
        $this->locale = $locale;

        $this->oldValue = $oldValue;
        $this->newValue = $newValue;
    }
}
