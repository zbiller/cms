<?php

namespace App\Http\Controllers\Admin\Cms;

use App\Http\Controllers\Controller;
use App\Http\Filters\Cms\BlockFilter;
use App\Http\Requests\Cms\BlockRequest;
use App\Http\Sorts\Cms\BlockSort;
use App\Models\Cms\Block;
use App\Models\Version\Draft;
use App\Models\Version\Revision;
use App\Options\DraftOptions;
use App\Options\DuplicateOptions;
use App\Options\RevisionOptions;
use App\Traits\CanCrud;
use App\Traits\CanDraft;
use App\Traits\CanDuplicate;
use App\Traits\CanRevision;
use DB;
use Exception;
use Illuminate\Http\Request;

class BlocksController extends Controller
{
    use CanCrud;
    use CanDraft;
    use CanRevision;
    use CanDuplicate;

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
            $this->items = Block::filtered($request, $filter)->sorted($request, $sort)->paginate(config('crud.per_page'));
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
                'title' => 'Add Block',
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
            $this->items = Block::onlyTrashed()->filtered($request, $filter)->sorted($request, $sort)->paginate(config('crud.per_page'));
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
     * @param Request $request
     * @return array
     */
    public function get(Request $request)
    {
        if (!$request->ajax()) {
            return response()->json([
                'error' => 'Bad request'
            ], 400);
        }

        $request->validate([
            'blockable_id' => 'required|numeric',
            'blockable_type' => 'required',
        ]);

        try {
            $class = $request->input('blockable_type');
            $id = $request->input('blockable_id');
            $model = $class::withoutGlobalScopes()->findOrFail($id);

            if ($request->filled('draft') && ($draft = Draft::find((int)$request->input('draft')))) {
                DB::beginTransaction();

                $model = $draft->draftable;
                $model->publishDraft($draft);
            }

            if ($request->filled('revision') && ($revision = Revision::find((int)$request->input('revision')))) {
                DB::beginTransaction();

                $model = $revision->revisionable;
                $model->rollbackToRevision($revision);
            }

            return response()->json([
                'status' => true,
                'html' => view('helpers::block.partials.blocks')->with([
                    'model' => $model,
                    'blocks' => $model->blocks,
                    'locations' => $model->getBlockLocations(),
                    'draft' => $draft ?? null,
                    'revision' => $revision ?? null,
                    'disabled' => $request->input('disabled') ? true : false,
                ])->render(),
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }

    /**
     * @param Request $request
     * @return array
     */
    public function row(Request $request)
    {
        if (!$request->ajax()) {
            return response()->json([
                'error' => 'Bad request'
            ], 400);
        }

        $request->validate([
            'block_id' => 'required|numeric',
        ]);

        try {
            $block = Block::findOrFail($request->input('block_id'));

            return response()->json([
                'status' => true,
                'data' => [
                    'id' => $block->id,
                    'name' => $block->name ?: 'N/A',
                    'type' => $block->type ?: 'N/A',
                    'url' => route('admin.blocks.edit', $block->id),
                ],
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Set the options for the CanDraft trait.
     *
     * @return DraftOptions
     */
    public static function getDraftOptions()
    {
        return DraftOptions::instance()
            ->setEntityModel(Block::class)
            ->setValidatorRequest(new BlockRequest)
            ->setFilterClass(new BlockFilter)
            ->setSortClass(new BlockSort)
            ->setListTitle('Drafted Blocks')
            ->setSingleTitle('Block Draft')
            ->setListView('admin.cms.blocks.drafts')
            ->setSingleView('admin.cms.blocks.draft')
            ->setLimboView('admin.cms.blocks.limbo')
            ->setRedirectUrl('admin.blocks.drafts')
            ->setViewVariables([
                'types' => Block::getTypes(),
            ]);
    }

    /**
     * Set the options for the CanRevision trait.
     *
     * @return RevisionOptions
     */
    public static function getRevisionOptions()
    {
        return RevisionOptions::instance()
            ->setPageTitle('Block Revision')
            ->setPageView('admin.cms.blocks.revision');
    }

    /**
     * Set the options for the CanDuplicate trait.
     *
     * @return DuplicateOptions
     */
    public static function getDuplicateOptions()
    {
        return DuplicateOptions::instance()
            ->setEntityModel(Block::class)
            ->setRedirectUrl('admin.blocks.edit');
    }
}