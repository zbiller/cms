<?php

namespace App\Http\Controllers\Admin\Cms;

use App\Models\Version\Draft;
use App\Models\Version\Revision;
use DB;
use Exception;
use App\Http\Controllers\Controller;
use App\Models\Cms\Block;
use App\Traits\CanCrud;
use App\Http\Requests\BlockRequest;
use App\Http\Filters\BlockFilter;
use App\Http\Sorts\BlockSort;
use App\Options\CrudOptions;
use App\Exceptions\DuplicateException;
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

            $this->vars['types'] = Block::getTypes();
        });
    }

    /**
     * @param string|null $type
     * @return \Illuminate\View\View
     */
    public function create($type = null)
    {
        if (!$type || !array_key_exists($type, Block::getTypes())) {
            return view('admin.cms.blocks.init')->with([
                'types' => Block::getTypes(),
                'images' => Block::getImages(),
            ]);
        }

        return $this->_create(function () use ($type) {
            $this->vars['type'] = $type;
        });
    }

    /**
     * @param BlockRequest $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws Exception
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
     * @throws Exception
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
     * @throws Exception
     */
    public function destroy(Block $block)
    {
        return $this->_destroy(function () use ($block) {
            $this->item = $block;
            $this->item->delete();
        });
    }

    /**
     * @param Block $block
     * @return \Illuminate\Http\RedirectResponse
     * @throws Exception
     */
    public function duplicate(Block $block)
    {
        try {
            $duplicated = $block->saveAsDuplicate();

            session()->flash('flash_success', 'The record was successfully duplicated! You have been redirected to the newly duplicated record.');
            return redirect()->route('admin.blocks.edit', $duplicated->id);
        } catch (DuplicateException $e) {
            session()->flash('flash_error', 'Failed duplicating the record! Please try again');
            return back();
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
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









    public function get(Request $request)
    {
        $this->validate($request, [
            'blockable_id' => 'required|numeric',
            'blockable_type' => 'required',
        ]);

        try {
            $class = $request->get('blockable_type');
            $id = $request->get('blockable_id');
            $model = $class::findOrFail($id);

            if ($request->has('draft') && ($draft = Draft::find((int)$request->get('draft')))) {
                DB::beginTransaction();

                $model = $draft->draftable;
                $model->publishDraft($draft);
            }

            if ($request->has('revision') && ($revision = Revision::find((int)$request->get('revision')))) {
                DB::beginTransaction();

                $model = $revision->revisionable;
                $model->rollbackToRevision($revision);
            }

            return [
                'status' => true,
                'html' => view('helpers::block.partials.blocks')->with([
                    'model' => $model,
                    'blocks' => $model->blocks,
                    'locations' => $model->getBlockLocations(),
                    'disabled' => $request->get('disabled') ? true : false,
                ])->render(),
            ];
        } catch (Exception $e) {
            return [
                'status' => false
            ];
        }
    }

    public function row(Request $request)
    {
        $this->validate($request, [
            'block_id' => 'required|numeric',
        ]);

        try {
            $block = Block::findOrFail($request->get('block_id'));

            return [
                'status' => true,
                'data' => [
                    'id' => $block->id,
                    'name' => $block->name ?: 'N/A',
                    'type' => $block->type ?: 'N/A',
                    'url' => route('admin.blocks.edit', $block->id),
                ],
            ];
        } catch (Exception $e) {
            return [
                'status' => false
            ];
        }
    }
}