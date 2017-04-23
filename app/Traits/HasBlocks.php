<?php

namespace App\Traits;

use App\Models\Cms\Block;

trait HasBlocks
{
    /**
     * Get all of the blocks for this model instance.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    public function blocks()
    {
        return $this->morphToMany(Block::class, 'blockable')->withPivot(['location', 'ord']);
    }
}