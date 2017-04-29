<?php

namespace App\Http\Controllers\Admin\Cms;

use DB;
use Exception;
use App\Models\Cms\Block;
use App\Http\Controllers\Controller;
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

            session()->flash('flash_success', 'Record duplicated successfully! You have been redirected to the newly duplicated record.');
            return redirect()->route('admin.blocks.edit', $duplicated->id);
        } catch (DuplicateException $e) {
            session()->flash('flash_error', 'Failed duplicating the record! Please try again');
            return back();
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * @param Request $request
     * @return array
     */
    public function assign(Request $request)
    {
        $this->validate($request, [
            'block_id' => 'required|numeric',
            'blockable_id' => 'required|numeric',
            'blockable_type' => 'required',
            'location' => 'required',
        ]);

        try {
            $data = $request->all();
            $block = Block::findOrFail($data['block_id']);
            $model = $data['blockable_type']::findOrFail($data['blockable_id']);

            $model->assignBlock($block, $data['location']);

            return [
                'status' => true,
                'html' => view('helpers::block.table')->with([
                    'model' => $model,
                    'location' => $data['location'],
                ])->render(),
            ];
        } catch (Exception $e) {
            return [
                'status' => false
            ];
        }
    }

    /**
     * @param Request $request
     * @return array
     */
    public function unassign(Request $request)
    {
        $this->validate($request, [
            'block_id' => 'required|numeric',
            'pivot_id' => 'required|numeric',
            'location' => 'required ',
        ]);

        try {
            $data = $request->all();
            $block = Block::findOrFail($data['block_id']);
            $model = $data['blockable_type']::findOrFail($data['blockable_id']);

            $model->unassignBlock($block, $data['pivot_id'], $data['location']);

            return [
                'status' => true,
                'html' => view('helpers::block.table')->with([
                    'model' => $model,
                    'location' => $data['location'],
                ])->render(),
            ];
        } catch (Exception $e) {
            return [
                'status' => false
            ];
        }
    }

    /**
     * @param Request $request
     * @return void
     */
    public function order(Request $request)
    {
        if ($request->has('items')) {
            foreach ($request->get('items') as $ord => $id) {
                DB::table('blockables')->where('id', $id)->update([
                    'ord' => $ord
                ]);
            }
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
}