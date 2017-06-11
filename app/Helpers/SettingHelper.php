<?php

namespace App\Helpers;

use App\Models\Config\Setting;

class SettingHelper
{
    /**
     * The key to find the setting by.
     *
     * @var bool
     */
    protected $key;

    /**
     * @var Setting
     */
    private static $settings = [];

    /**
     * Get the Setting object based on a key.
     *
     * @param string $key
     * @return mixed
     */
    public function find($key)
    {
        if (!isset(self::$settings[$key])) {
            return self::$settings[$key] = Setting::key($key)->first();
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