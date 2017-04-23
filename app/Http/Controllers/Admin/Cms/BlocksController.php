<?php

namespace App\Http\Controllers\Admin\Cms;

use App\Models\Cms\Block;
use App\Http\Controllers\Controller;
use App\Traits\CanCrud;
use App\Http\Requests\BlockRequest;
use App\Http\Filters\BlockFilter;
use App\Http\Sorts\BlockSort;
use App\Options\CrudOptions;
use Illuminate\Http\Request;

class BlocksController extends Controller
{
    use CanCrud;

    /**
     * @param Request $request
     * @param BlockFilter $filter
     * @param BlockSort $sort
     * @return \Illuminate\View\View
     */
    public function index(Request $request, BlockFilter $filter, BlockSort $sort)
    {
        return $this->_index(function () use ($request, $filter, $sort) {
            $this->items = Block::filtered($request, $filter)->sorted($request, $sort)->paginate(10);

            $this->vars['types'] = Block::types();
        });
    }

    /**
     * @param string|null $type
     * @return \Illuminate\View\View
     */
    public function create($type = null)
    {
        if (!$type || !array_key_exists($type, Block::types())) {
            return view('admin.cms.blocks.init')->with([
                'types' => Block::types(),
                'images' => Block::images(),
            ]);
        }

        return $this->_create(function () use ($type) {
            $this->vars['type'] = $type;
        });
    }

    /**
     * @param BlockRequest $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function store(BlockRequest $request)
    {
        return $this->_store(function () use ($request) {
            $this->item = Block::create($request->all());
        }, $request);
    }

    /**
     * @param Block $block
     * @return \Illuminate\View\View
     */
    public function edit(Block $block)
    {
        return $this->_edit(function () use ($block) {
            $this->item = $block;
        });
    }

    /**
     * @param BlockRequest $request
     * @param Block $block
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function update(BlockRequest $request, Block $block)
    {
        return $this->_update(function () use ($request, $block) {
            $this->item = $block;
            $this->item->update($request->all());
        }, $request);
    }

    /**
     * @param Block $block
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function destroy(Block $block)
    {
        return $this->_destroy(function () use ($block) {
            $this->item = $block;
            $this->item->delete();
        });
    }

    /**
     * @return CrudOptions
     */
    public static function getCrudOptions()
    {
        return CrudOptions::instance()
            ->setModel(app(Block::class))
            ->setListRoute('admin.blocks.index')
            ->setListView('admin.cms.blocks.index')
            ->setAddRoute('admin.blocks.create')
            ->setAddView('admin.cms.blocks.add')
            ->setEditRoute('admin.blocks.edit')
            ->setEditView('admin.cms.blocks.edit');
    }
}