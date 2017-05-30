<?php

namespace App\Http\Controllers\Admin\Cms;

use DB;
use Exception;
use App\Http\Controllers\Controller;
use App\Models\Cms\Block;
use App\Models\Version\Draft;
use App\Models\Version\Revision;
use App\Traits\CanCrud;
use App\Http\Requests\BlockRequest;
use App\Http\Filters\BlockFilter;
use App\Http\Sorts\BlockSort;
use Illuminate\Http\Request;

class BlocksController extends Controller
{
    use CanCrud;

    /**
     * @var string
     */
    protected $model = Block::class;

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
            $this->title = 'Blocks';
            $this->view = view('admin.cms.blocks.index');
            $this->vars = [
                'types' => Block::getTypes(),
            ];
        });
    }

    /**
     * @param string|null $type
     * @return \Illuminate\View\View
     */
    public function create($type = null)
    {
        if (!$type || !array_key_exists($type, Block::getTypes())) {
            $this->setMeta('title', 'Admin - Add Block');
            return view('admin.cms.blocks.init')->with([
                'types' => Block::getTypes(),
                'images' => Block::getImages(),
            ]);
        }

        return $this->_create(function () use ($type) {
            $this->title = 'Add Block';
            $this->view = view('admin.cms.blocks.add');
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
            $this->redirect = redirect()->route('admin.blocks.index');
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
            $this->title = 'Edit Block';
            $this->view = view('admin.cms.blocks.edit');
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
            $this->redirect = redirect()->route('admin.blocks.index');

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
            $this->redirect = redirect()->route('admin.blocks.index');

            $this->item->delete();
        });
    }

    /**
     * @param Request $request
     * @param BlockFilter $filter
     * @param BlockSort $sort
     * @return \Illuminate\View\View
     */
    public function deleted(Request $request, BlockFilter $filter, BlockSort $sort)
    {
        return $this->_deleted(function () use ($request, $filter, $sort) {
            $this->items = Block::onlyTrashed()->filtered($request, $filter)->sorted($request, $sort)->paginate(10);
            $this->title = 'Deleted Blocks';
            $this->view = view('admin.cms.blocks.deleted');
            $this->vars = [
                'types' => Block::getTypes(),
            ];
        });
    }

    /**
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function restore($id)
    {
        return $this->_restore(function () use ($id) {
            $this->item = Block::onlyTrashed()->findOrFail($id);
            $this->redirect = redirect()->route('admin.blocks.deleted');

            $this->item->restore();
        });
    }

    /**
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function delete($id)
    {
        return $this->_delete(function () use ($id) {
            $this->item = Block::onlyTrashed()->findOrFail($id);
            $this->redirect = redirect()->route('admin.blocks.deleted');

            $this->item->forceDelete();
        });
    }

    /**
     * @param Block $block
     * @return \Illuminate\Http\RedirectResponse
     * @throws Exception
     */
    public function duplicate(Block $block)
    {
        return $this->_duplicate(function () use ($block) {
            $this->item = $block->saveAsDuplicate();
            $this->redirect = redirect()->route('admin.blocks.edit', $this->item->id);
        });
    }

    /**
     * @param Request $request
     * @param BlockFilter $filter
     * @param BlockSort $sort
     * @return \Illuminate\View\View
     */
    public function drafts(Request $request, BlockFilter $filter, BlockSort $sort)
    {
        return $this->_drafts(function () use ($request, $filter, $sort) {
            $this->items = Block::onlyDrafts()->filtered($request, $filter)->sorted($request, $sort)->paginate(10);
            $this->title = 'Drafted Blocks';
            $this->view = view('admin.cms.blocks.drafts');
            $this->vars = [
                'types' => Block::getTypes(),
            ];
        });
    }

    /**
     * @param Draft $draft
     * @return \Illuminate\View\View
     */
    public function draft(Draft $draft)
    {
        return $this->_draft(function () use ($draft) {
            $this->item = $draft->draftable;
            $this->item->publishDraft($draft);

            $this->title = 'Block Draft';
            $this->view = view('admin.cms.blocks.draft');
        }, $draft);
    }

    /**
     * @param BlockRequest $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws Exception
     */
    public function limbo(BlockRequest $request, $id)
    {
        return $this->_limbo(function () {
            $this->title = 'Block Draft';
            $this->view = view('admin.cms.blocks.limbo');
        }, function () use ($request) {
            $this->item->saveAsDraft($request->all());
            $this->redirect = redirect()->route('admin.blocks.drafts');
        }, $id, $request);
    }

    /**
     * @param Revision $revision
     * @return \Illuminate\View\View
     */
    public function revision(Revision $revision)
    {
        return $this->_revision(function () use ($revision) {
            $this->item = $revision->revisionable;
            $this->item->rollbackToRevision($revision);

            $this->title = 'Block Revision';
            $this->view = view('admin.cms.blocks.revision');
        }, $revision);
    }

    /**
     * @param Request $request
     * @return array
     */
    public function get(Request $request)
    {
        $this->validate($request, [
            'blockable_id' => 'required|numeric',
            'blockable_type' => 'required',
        ]);

        try {
            $class = $request->get('blockable_type');
            $id = $request->get('blockable_id');
            $model = $class::withoutGlobalScopes()->findOrFail($id);

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

    /**
     * @param Request $request
     * @return array
     */
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