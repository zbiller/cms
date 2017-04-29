<?php

namespace App\Traits;

use Exception;
use App\Models\Model;
use App\Models\Cms\Block;
use App\Exceptions\BlockException;

trait HasBlocks
{
    /**
     * Boot the trait.
     * Remove blocks on save and delete if one or many locations from model's instance have been changed/removed.
     */
    public static function bootHasBlocks()
    {
        static::saved(function (Model $model) {
            $model->syncBlocks($model->getBlockLocations());
        });

        static::deleted(function (Model $model) {
            if ($model->forceDeleting !== false) {
                $model->syncBlocks();
            }
        });
    }

    /**
     * Get all of the blocks for this model instance.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    public function blocks()
    {
        return $this->morphToMany(Block::class, 'blockable')->withPivot([
            'id', 'location', 'ord'
        ])->withTimestamps();
    }

    /**
     * Get all blocks assigned to this model instance from a given location in order.
     *
     * @param string $location
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getBlocksInLocation($location)
    {
        return $this->blocks()->where('location', $location)->orderBy('ord', 'asc')->get();
    }

    /**
     * Get all blocks from database that can belong to the given location.
     *
     * @param string $location
     * @return \Illuminate\Support\Collection
     */
    public function getBlocksOfLocation($location)
    {
        $blocks = collect();

        foreach (Block::alphabetically()->get() as $block) {
            if (
                isset(Block::$blocks[$block->type]['composer_class']) &&
                ($class = Block::$blocks[$block->type]['composer_class']) &&
                in_array($location, $class::$locations)
            ) {
                $blocks->push($block);
            }
        }

        return $blocks;
    }

    /**
     * Get the inherited blocks for a model instance.
     * Inherited blocks can come from page or layout (recursively).
     *
     * @param string|$location
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getInheritedBlocks($location)
    {
        if (!method_exists($this, 'getBlockOptions')) {
            return null;
        }

        $options = static::getBlockOptions();

        if ($options->inherit && $options->inherit instanceof Model && $options->inherit->exists) {
            $blocks = $options->inherit->getBlocksInLocation($location);

            if ($blocks->count() > 0) {
                return $blocks;
            }

            if (
                method_exists($options->inherit, 'getBlockOptions') &&
                (
                    is_string($options->inherit->getBlockOptions()->inherit) ||
                    (
                        $options->inherit->getBlockOptions()->inherit instanceof Model &&
                        get_class($options->inherit->getBlockOptions()->inherit) != get_class($this)
                    )
                )
            ) {
                return $options->inherit->getInheritedBlocks($location);
            }
        }

        return collect();
    }

    /**
     * Get all block locations for the given model instance.
     *
     * @return array|null
     */
    public function getBlockLocations()
    {
        if (method_exists($this, 'getBlockOptions')) {
            return static::getBlockOptions()->locations;
        }

        return null;
    }

    /**
     * Get a list with all of the block locations currently assigned in database for this model instance.
     *
     * @return array
     */
    public function getExistingBlockLocations()
    {
        return $this->blocks()->newPivotStatement()->select('location')->where([
            'blockable_id' => $this->id,
            'blockable_type' => static::class,
        ])->distinct()->get()->pluck('location')->toArray();
    }

    /**
     * Assign a block to this model instance, matching the given location.
     *
     * @param Block $block
     * @param string $location
     * @return bool
     * @throws BlockException
     */
    public function assignBlock(Block $block, $location)
    {
        $order = 1;

        if ($last = $this->getBlocksInLocation($location)->last()) {
            if ($last->pivot && $last->pivot->ord) {
                $order = $last->pivot->ord + 1;
            }
        }

        try {
            $this->blocks()->save($block, [
                'location' => $location,
                'ord' => $order
            ]);

            return true;
        } catch (Exception $e) {
            throw new BlockException(
                $e->getMessage(), $e->getCode(), $e
            );
        }
    }

    /**
     * Un-assign a block matching the pivot table id and location.
     * Delete the record from "blockables" table.
     *
     * @param Block $block
     * @param int $pivot
     * @param string $location
     * @return bool
     * @throws BlockException
     */
    public function unassignBlock(Block $block, $pivot, $location)
    {
        try {
            $this->blocks()
                ->newPivotStatementForId($block->id)
                ->where('location', $location)
                ->delete($pivot);

            return true;
        } catch (Exception $e) {
            throw new BlockException(
                $e->getMessage(), $e->getCode(), $e
            );
        }
    }

    /**
     * Sync a loaded model instance's assigned blocks from different locations with given locations.
     * The $locations parameter should represent the actual model instance's available block locations.
     *
     * @param array $locations
     * @return void
     * @throws BlockException
     */
    public function syncBlocks(array $locations = [])
    {
        foreach ($this->getExistingBlockLocations() as $location) {
            if (in_array($location, $locations)) {
                continue;
            }

            try {
                $this->blocks()->newPivotStatement()->where([
                    'blockable_id' => $this->id,
                    'blockable_type' => static::class,
                    'location' => $location,
                ])->delete();
            } catch (Exception $e) {
                throw new BlockException(
                    $e->getMessage(), $e->getCode(), $e
                );
            }
        }
    }
}