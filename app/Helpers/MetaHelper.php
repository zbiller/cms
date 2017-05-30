<?php

namespace App\Helpers;

class MetaHelper
{
    /**
     * The container for all the meta keys available to render.
     * This is built using the set() method.
     * You can get the contents of this by using get(), tag(), tags().
     *
     * @var array
     */
    protected $meta = [];

    /**
     * Set a meta property using the key and value provided.
     *
     * @param string $key
     * @param string $value
     * @return string
     */
    public function set($key, $value)
    {
        $value = self::sanitize($value);

        if (strtolower($key) == 'image') {
            $this->meta['image'][] = $value;
        } else {
            $this->meta[$key] = $value;
        }
    }

    /**
     * Get a meta property by it's key.
     * If the meta property does not have any value, the default one will be returned.
     *
     * @param string $key
     * @param array|string|null $default
     * @return string
     */
    public function get($key, $default = null)
    {
        if (empty($this->meta[$key])) {
            return $default;
        }

        return $this->meta[$key];
    }

    /**
     * Get the HTML format for a meta property by it's key.
     * All property types will be built for that key: tag, name, og property, twitter card.
     * If the meta property does not have any value, it will use the default value to build the HTML.
     *
     * @param string $key
     * @param array|string|null $default
     * @return string
     */
    public function tag($key, $default = null)
    {
        if (!($values = $this->get($key, $default))) {
            return '';
        }

        if (!is_array($values)) {
            $values = [$values];
        }

        $html = '';

        foreach ($values as $value) {
            $html .= MetaTag::tag($key, $value);
            $html .= MetaName::tag($key, $value);
            $html .= MetaProperty::tag($key, $value);
            $html .= MetaTwitter::tag($key, $value);
        }

        return $html;
    }

    /**
     * Get the HTML format for multiple meta properties by their keys.
     *
     * @param ...$keys
     * @return string
     */
    public function tags(...$keys)
    {
        $keys = array_flatten($keys);
        $html = '';

        foreach ($keys as $key) {
            $html .= $this->tag($key);
        }

        return $html;
    }

    /**
     * Sanitize a string for safe usage inside <meta> tags.
     *
     * @param string $text
     * @return string
     */
    protected static function sanitize($text)
    {
        return trim(str_replace('"', '&quot;', preg_replace('/[\r\n\s]+/', ' ', strip_tags($text))));
    }
}

/**
 * -----------------------------------------------------------------------------------------------
 * Below are the helper classes used for properly generating all meta tags corresponding to a key.
 * -----------------------------------------------------------------------------------------------
 */

class MetaTag
{
    /**
     * The available meta keys to be built with this class.
     *
     * @var array
     */
    protected static $available = [
        'title',
    ];

    /**
     * The custom meta keys to be built with this class.
     * Custom properties require special treatment.
     *
     * @var array
     */
    protected static $custom = [
        'image',
    ];

    /**
     * Build the HTML for the supplied tag keys.
     *
     * @param string $key
     * @param string $value
     * @return string
     */
    public static function tag($key, $value)
    {
        if (in_array($key, self::$available, true)) {
            return '<'.$key.'>' . (strtolower($key) == 'title' ? $value . ' - ' . config('app.name') : $value) . '</'.$key.'>';
        }

        if (in_array($key, self::$custom, true)) {
            return '<link rel="image_src" href="' . $value . '" />';
        }

        return '';
    }
}

class MetaName
{
    /**
     * Build the HTML for the supplied tag keys.
     *
     * @param string $key
     * @param string $value
     * @return string
     */
    public static function tag($key, $value)
    {
        return '<meta name="' . $key . '" content="' . $value . '" />';
    }
}

class MetaProperty
{
    /**
     * The available meta keys to be built with this class.
     *
     * @var array
     */
    protected static $available = [
        'title',
        'description',
        'type',
        'url',
        'image',
        'audio',
        'video',
        'locale',
        'determiner',
        'site_name',
    ];

    /**
     * Build the HTML for the supplied tag keys.
     *
     * @param string $key
     * @param string $value
     * @return string
     */
    public static function tag($key, $value)
    {
        if (in_array($key, self::$available, true)) {
            return '<meta property="og:' . $key . '" content="' . $value . '" />';
        }

        return '';
    }
}

class MetaTwitter
{
    /**
     * The available meta keys to be built with this class.
     *
     * @var array
     */
    protected static $available = [
        'card',
        'site',
        'site:id',
        'creator',
        'creator:id',
        'description',
        'title',
        'image',
        'image:alt',
        'player',
        'player:width',
        'player:height',
        'player:stream',
        'app:name:iphone',
        'app:id:iphone',
        'app:url:iphone',
        'app:name:ipad',
        'app:id:ipad',
        'app:url:ipad',
        'app:name:googleplay',
        'app:id:googleplay',
        'app:url:googleplay'
    ];

    /**
     * Build the HTML for the supplied tag keys.
     *
     * @param string $key
     * @param string $value
     * @return string
     */
    public static function tag($key, $value)
    {
        if (in_array($key, self::$available, true)) {
            return '<meta name="twitter:' . $key . '" content="' . $value . '" />';
        }

        return '';
    }
}