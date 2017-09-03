<?php

namespace App\Traits;

use App\Exceptions\BlockException;
use App\Models\Cms\Block;
use App\Models\Model;
use App\Options\BlockOptions;
use DB;
use Exception;
use Illuminate\Http\Request;
use ReflectionMethod;

trait HasBlocks
{
    /**
     * The container for all the options necessary for this trait.
     * Options can be viewed in the App\Options\ResetPasswordOptions file.
     *
     * @var BlockOptions
     */
    protected static $blockOptions;

    /**
     * Flag to manually enable/disable the blocks savings only for the current request.
     *
     * @var bool
     */
    protected static $shouldSaveBlocks = true;

    /**
     * Boot the trait.
     * Remove blocks on save and delete if one or many locations from model's instance have been changed/removed.
     */
    public static function bootHasBlocks()
    {
        self::checkBlockOptions();

        self::$blockOptions = self::getBlockOptions();

        static::saved(function (Model $model) {
            if (self::$shouldSaveBlocks === true) {
                $model->saveBlocks();
            }
        });

        static::deleted(function (Model $model) {
            if ($model->forceDeleting !== false) {
                $model->blocks()->detach();
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
     * Disable the url generation manually only for the current request.
     *
     * @return static
     */
    public function doNotSaveBlocks()
    {
        self::$shouldSaveBlocks = false;

        return $this;
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

        foreach (Block::inAlphabeticalOrder()->get() as $block) {
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
        if (!self::$blockOptions->inherit) {
            return $this->getBlocksInLocation($location);
        }

        $inheritor = null;

        if (is_string(self::$blockOptions->inherit)) {
            if ($this->{self::$blockOptions->inherit} instanceof Model && $this->{self::$blockOptions->inherit}->exists) {
                $inheritor = $this->{self::$blockOptions->inherit};
            }
        } elseif (self::$blockOptions->inherit instanceof Model && self::$blockOptions->inherit->exists) {
            $inheritor = self::$blockOptions->inherit;
        }

        if ($inheritor instanceof Model) {
            $blocks = $inheritor->getBlocksInLocation($location);

            if ($blocks->count() > 0) {
                return $blocks;
            }

            if (
                is_string($inheritor->getBlockOptions()->inherit) &&
                $inheritor->{$inheritor->getBlockOptions()->inherit} instanceof Model &&
                $inheritor->{$inheritor->getBlockOptions()->inherit}->exists
            ) {
                return $inheritor->{$inheritor->getBlockOptions()->inherit}->getInheritedBlocks($location);
            }

            if (
                $inheritor->getBlockOptions()->inherit instanceof Model &&
                get_class($inheritor->getBlockOptions()->inherit) != get_class($this)
            ) {
                return $inheritor->getInheritedBlocks($location);
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
        if (is_array(self::$blockOptions->locations)) {
            return self::$blockOptions->locations;
        };

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
     * Save all of the blocks of a model instance.
     * Saving is done on a provided or existing request object.
     * The logic of this method will look for the "blocks" key in the request.
     * Mandatory request format is an array of keys with their values composed of the block id and location.
     * [0 => [id => 1, location => header], 1 => [id => 1, location => footer]...]
     *
     * @param Request|null $request
     * @return bool|void
     * @throws BlockException
     */
    public function saveBlocks(Request $request = null)
    {
        $request = $request ?: request();
        $blocks = $request->input('blocks');

        try {
            DB::transaction(function () use ($blocks) {
                $this->blocks()->detach();

                if ($blocks && is_array($blocks) && !empty($blocks)) {
                    ksort($blocks);

                    foreach ($blocks as $data) {
                        foreach ($data as $id => $attributes) {
                            if (($id && isset($attributes['location'])) && ($block = Block::find($id))) {
                                $this->assignBlock($block, $attributes['location'], isset($attributes['ord']) ? $attributes['ord'] : null);
                            }

                        }
                    }
                }
            });

            return true;
        } catch (Exception $e) {
            throw new BlockException(
                $e->getMessage(), $e->getCode(), $e
            );
        }
    }

    /**
     * Assign a block to this model instance, matching the given location.
     *
     * @param Block $block
     * @param string $location
     * @param int|null $order
     * @return bool
     * @throws BlockException
     */
    public function assignBlock(Block $block, $location, $order = null)
    {
        if (!$order || !is_numeric($order)) {
            $order = 1;

            if ($last = $this->getBlocksInLocation($location)->last()) {
                if ($last->pivot && $last->pivot->ord) {
                    $order = $last->pivot->ord + 1;
                }
            }
        }

        try {
            $this->blocks()->save($block, [
                'location' => $location,
                'ord' => (int)$order
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
     * @param string $location
     * @param int $pivot
     * @return bool
     * @throws BlockException
     */
    public function unassignBlock(Block $block, $location, $pivot)
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

    /**
     * Verify if the getResetPasswordOptions() method for setting the trait options exists and is public and static.
     *
     * @throws Exception
     */
    private static function checkBlockOptions()
    {
        if (!method_exists(self::class, 'getBlockOptions')) {
            throw new Exception(
                'The "' . self::class . '" must define the public static "getBlockOptions()" method.'
            );
        }

        $reflection = new ReflectionMethod(self::class, 'getBlockOptions');

        if (!$reflection->isPublic() || !$reflection->isStatic()) {
            throw new Exception(
                'The method "getBlockOptions()" from the class "' . self::class . '" must be declared as both "public" and "static".'
            );
        }
    }
}