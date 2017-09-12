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
        return $this->fromJson($this->attributes['metadata']);
    }

    /**
     * Set the json encoded data for the "metadata" column.
     *
     * @param $value
     */
    public function setMetadataAttribute($value)
    {
        $this->attributes['metadata'] = $value ? (
            is_json_format($value) ? $value : $this->asJson($value)
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
        return $this->metadata ? array_get(
            get_object_vars_recursive($this->metadata),
            str_replace('][', '.', trim(str_replace('metadata', '', $raw), '.[]'))
        ) : null;
    }
}
