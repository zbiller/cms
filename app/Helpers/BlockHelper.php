<?php

namespace App\Helpers;

use App\Models\Model;
use App\Models\Version\Draft;
use App\Models\Version\Revision;
use Illuminate\Database\Eloquent\Collection;

class BlockHelper
{
    /**
     * The exiting blocks for a loaded model instance from a given location.
     *
     * @var Collection
     */
    protected $blocksInLocation;

    /**
     * The inherited blocks for a loaded model instance from a given location.
     *
     * @var Collection
     */
    protected $inheritedBlocks;

    /**
     * Set the $blocksInLocation property, only if not set.
     *
     * @param Model $model
     * @param string $location
     * @return $this
     */
    public function setBlocksInLocation(Model $model, $location)
    {
        if (!$this->blocksInLocation) {
            $this->blocksInLocation = $model->getBlocksInLocation($location);
        }

        return $this;
    }

    /**
     * Get the blocks from a location.
     *
     * @return Collection
     */
    public function getBlocksInLocation()
    {
        return $this->blocksInLocation;
    }

    /**
     * Set the $inheritedBlocks property, only if not set.
     *
     * @param Model $model
     * @param string $location
     * @return $this
     */
    public function setInheritedBlocks(Model $model, $location)
    {
        if (!$this->inheritedBlocks) {
            $this->inheritedBlocks = $model->getInheritedBlocks($location);
        }

        return $this;
    }

    /**
     * Get the inherited blocks.
     *
     * @return Collection
     */
    public function getInheritedBlocks()
    {
        return $this->inheritedBlocks;
    }

    /**
     * Render the blocks from a given location for a loaded model instance.
     * Inheriting functionality is also available.
     * If the model instance does not have any blocks assigned, but it inherits blocks, those will be rendered.
     *
     * @param Model $model
     * @param string $location
     * @param bool $inherits
     * @return null|void
     */
    public function holder(Model $model, $location, $inherits = true)
    {
        if (!$model->exists) {
            return null;
        }

        $this->setBlocksInLocation($model, $location);

        if ($this->getBlocksInLocation()->count() > 0) {
            foreach ($this->getBlocksInLocation() as $block) {
                echo view()->make("blocks_{$block->type}::front")->with([
                    'model' => $block
                ])->render();
            }

            return;
        }

        if ($inherits === true) {
            $this->setInheritedBlocks($model, $location);

            if ($this->getInheritedBlocks()->count() > 0) {
                foreach ($this->getInheritedBlocks() as $block) {
                    echo view()->make("blocks_{$block->type}::front")->with([
                        'model' => $block
                    ])->render();
                }

                return;
            }
        }

        return null;
    }

    /**
     * Build the block tabs html.
     *
     * @param Model $model
     * @return \Illuminate\View\View
     */
    public function tab(Model $model)
    {
        return view('helpers::block.tab')->with([
            'model' => $model,
        ]);
    }

    /**
     * Build the block containers html.
     *
     * @param Model $model
     * @param Draft $draft
     * @param Revision $revision
     * @param bool $disabled
     * @return \Illuminate\View\View
     */
    public function container(Model $model, Draft $draft = null, Revision $revision = null, $disabled = false)
    {
        return view('helpers::block.container')->with([
            'model' => $model,
            'draft' => $draft,
            'revision' => $revision,
            'disabled' => $disabled,
        ]);
    }

    /**
     * Render the buttons for "items" block types.
     * Remove | Move Up | Move Down.
     *
     * @return \Illuminate\View\View
     */
    public function buttons()
    {
        return view('helpers::block.buttons');
    }
}