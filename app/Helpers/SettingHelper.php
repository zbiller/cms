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
        if (!isset(self::$settings[$key])) {
            return self::$settings[$key] = Setting::byKey($key)->first();
        }

        return self::$settings[$key];
    }

    /**
     * Get the setting's value based on a key.
     *
     * @param string $key
     * @return null
     */
    public function value($key)
    {
        if ($setting = $this->find($key)) {
            return $setting->value;
        }

        return null;
    }
}