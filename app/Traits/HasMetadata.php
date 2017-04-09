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
        $this->attributes['metadata'] = $value ? json_encode($value) : null;
    }
}
