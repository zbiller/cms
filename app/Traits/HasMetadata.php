<?php

namespace App\Traits;

trait HasMetadata
{
    /**
     * Get the json decoded representation of the "metadata" column.
     *
     * @return mixed
     */
    public function getMetadataAttribute()
    {
        return json_decode($this->attributes['metadata']);
    }

    /**
     * Set the json encoded data for the "metadata" column.
     *
     * @param $value
     */
    public function setMetadataAttribute($value)
    {
        $this->attributes['metadata'] = $value ? (
            $this->isJsonFormat($value) ? $value : json_encode($value)
        ) : null;
    }

    /**
     * Get a metadata property from a raw field representation.
     * It can get the json representation from something like: metadata[items][0][name]
     *
     * metadata[items][0][name] to metadata->items->0->name
     *
     * @param string $raw
     * @return mixed
     */
    public function metadata($raw)
    {
        $metadata = get_object_vars_recursive($this->metadata);
        $field = str_replace('][', '.', trim(str_replace('metadata', '', $raw), '.[]'));

        return array_get($metadata, $field);
    }

    /**
     * Verify if the given string is of json format.
     *
     * @param array|string $string
     * @return bool
     */
    protected function isJsonFormat($string)
    {
        if (!is_string($string)) {
            return false;
        }

        json_decode($string);

        return (json_last_error() == JSON_ERROR_NONE);
    }
}
