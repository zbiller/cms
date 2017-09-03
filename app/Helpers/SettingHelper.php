<?php

namespace App\Helpers;

use App\Models\Config\Setting;

class SettingHelper
{
    /**
     * @var Setting
     */
    private static $settings = [];

    /**
     * Get the Setting model based on a key.
     *
     * @param string $key
     * @return mixed
     */
    public function find($key)
    {
        return self::$settings[$key] ?? Setting::byKey($key)->first();
    }

    /**
     * Get the setting's value based on a key.
     *
     * @param string $key
     * @return null
     */
    public function value($key)
    {
        return optional($this->find($key))->value ?: null;
    }
}