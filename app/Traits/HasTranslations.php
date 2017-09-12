<?php

namespace App\Traits;

use App\Events\TranslationSet;
use App\Exceptions\TranslationException;

trait HasTranslations
{
    /**
     * @param string $key
     * @return string
     */
    public function getAttribute($key)
    {
        if ($this->isTranslatableAttribute($key)) {
            return $this->getTranslation($key, app()->getLocale());
        }

        return parent::getAttribute($key);
    }

    /**
     * @param string $key
     * @param array|string $value
     * @return $this
     */
    public function setAttribute($key, $value)
    {
        if ($this->isTranslatableAttribute($key) && $this->exists === true) {
            $value = is_json_format($value) ? $this->fromJson($value) : $value[app()->getLocale()] ?? '';

            !is_null($value) && !empty($value) ?
                $this->setTranslation($key, $value, app()->getLocale()) :
                $this->forgetTranslation($key, app()->getLocale());

            return $this;
        }

        return parent::setAttribute($key, $value);
    }

    /**
     * @param string $key
     * @param string|null $locale
     * @return string
     */
    public function translate($key, $locale)
    {
        return $this->getTranslation($key, $locale);
    }

    /**
     * @param string $key
     * @param string $locale
     * @param bool $useFallbackLocale
     * @return string
     */
    public function getTranslation($key, $locale, $useFallbackLocale = true)
    {
        $locale = $this->normalizeLocale($key, $locale, $useFallbackLocale);
        $translation = $this->getTranslations($key)[$locale] ?? '';

        if ($this->hasGetMutator($key)) {
            return $this->mutateAttribute($key, $translation);
        }

        return $translation;
    }

    /**
     * @param string $key
     * @return mixed
     * @throws TranslationException
     */
    public function getTranslations($key)
    {
        $this->guardAgainstUntranslatableAttribute($key);

        return $this->fromJson($this->attributes[$key] ?? '' ?: '{}');
    }

    /**
     * @param string $key
     * @param string $locale
     * @param string $value
     * @return $this
     * @throws TranslationException
     */
    public function setTranslation($key, $value, $locale)
    {
        $this->guardAgainstUntranslatableAttribute($key);

        $translations = $this->getTranslations($key);
        $old = $translations[$locale] ?? '';

        if ($this->hasSetMutator($key)) {
            $value = $this->{'set' . studly_case($key) . 'Attribute'}($value) ?: $value;
        }

        $translations[$locale] = $value;

        $this->attributes[$key] = $this->asJson($translations);

        event(new TranslationSet($this, $key, $locale, $old, $value));

        return $this;
    }

    /**
     * @param string $key
     * @param array $translations
     * @return $this
     * @throws TranslationException
     */
    public function setTranslations($key, array $translations)
    {
        $this->guardAgainstUntranslatableAttribute($key);

        foreach ($translations as $locale => $translation) {
            $this->setTranslation($key, $translation, $locale);
        }

        return $this;
    }

    /**
     * @param string $key
     * @param string $locale
     * @return $this
     */
    public function forgetTranslation($key, $locale)
    {
        $translations = $this->getTranslations($key);

        unset($translations[$locale]);
        parent::setAttribute($key, $translations);

        return $this;
    }

    /**
     * @param string $locale
     * @return $this
     */
    public function forgetAllTranslations($locale)
    {
        foreach ($this->getTranslatableAttributes() as $attribute) {
            $this->forgetTranslation($attribute, $locale);
        }

        return $this;
    }

    /**
     * @param string $key
     * @return bool
     */
    public function isTranslatableAttribute($key)
    {
        return in_array($key, $this->getTranslatableAttributes());
    }

    /**
     * @return array
     */
    public function getTranslatableAttributes()
    {
        return is_array($this->translatable) ? $this->translatable : [];
    }

    /**
     * @param string $key
     * @return array
     */
    public function getTranslatedLocales($key)
    {
        return array_keys($this->getTranslations($key));
    }

    /**
     * @return array
     */
    public function getCasts()
    {
        return array_merge(parent::getCasts(), array_fill_keys(
            $this->getTranslatableAttributes(), 'array'
        ));
    }

    /**
     * @param string $key
     * @throws TranslationException
     */
    protected function guardAgainstUntranslatableAttribute($key)
    {
        if (!$this->isTranslatableAttribute($key)) {
            throw TranslationException::attributeIsNotTranslatable($key);
        }
    }

    /**
     * @param string $key
     * @param string $locale
     * @param bool $useFallbackLocale
     * @return mixed
     */
    protected function normalizeLocale($key, $locale = null, $useFallbackLocale = true)
    {
        if (in_array($locale, $this->getTranslatedLocales($key))) {
            return $locale;
        }

        if (!$useFallbackLocale) {
            return $locale;
        }

        if (!is_null($fallbackLocale = config('app.fallback_locale'))) {
            return $fallbackLocale;
        }

        return $locale;
    }
}