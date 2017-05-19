<?php

namespace App\Http\Controllers\Admin\Cms;

use App\Exceptions\DraftException;
use DB;
use Exception;
use App\Http\Controllers\Controller;
use App\Models\Cms\Page;
use App\Models\Cms\Layout;
use App\Models\Version\Draft;
use App\Models\Version\Revision;
use App\Traits\CanCrud;
use App\Http\Requests\PageRequest;
use App\Http\Filters\PageFilter;
use App\Http\Sorts\PageSort;
use App\Options\CrudOptions;
use App\Exceptions\DuplicateException;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class PagesController extends Controller
{
    use CanCrud;

    /**
     * @param Request $request
     * @param PageFilter $filter
     * @param PageSort $sort
     * @return \Illuminate\View\View
     * @throws \Exception
     */
    public function index(Request $request, PageFilter $filter, PageSort $sort)
    {
        cache()->forget('first_tree_load');

        return $this->_index(function () use ($request, $filter, $sort) {
            $query = Page::whereIsRoot()->filtered($request, $filter);
            $request->has('sort') ? $query->sorted($request, $sort) : $query->defaultOrder();

            $this->items = $query->get();

            $this->vars = [
                'layouts' => Layout::all(),
                'types' => Page::$types,
                'actives' => Page::$actives,
            ];
        });
    }

    /**
     * @param Request $request
     * @param PageFilter $filter
     * @param PageSort $sort
     * @return \Illuminate\View\View
     */
    public function deleted(Request $request, PageFilter $filter, PageSort $sort)
    {
        return $this->_deleted(function () use ($request, $filter, $sort) {
            $this->items = Page::onlyTrashed()->filtered($request, $filter)->sorted($request, $sort)->paginate(10);

            $this->vars = [
                'layouts' => Layout::all(),
                'types' => Page::$types,
                'actives' => Page::$actives,
            ];
        });
    }

    /**
     * @param Page $parent
     * @return \Illuminate\View\View
     */
    public function create(Page $parent = null)
    {
        return $this->_create(function () use ($parent) {
            $this->vars = [
                'parent' => $parent->exists ? $parent : null,
                'layouts' => Layout::all(),
                'types' => Page::$types,
                'actives' => Page::$actives,
            ];
        });
    }

    /**
     * @param PageRequest $request
     * @param Page $parent
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function store(PageRequest $request, Page $parent = null)
    {
        return $this->_store(function () use ($request, $parent) {
            $this->item = Page::create(
                $request->all(), $parent->exists ? $parent : null
            );
        }, $request);
    }

    /**
     * @param Page $page
     * @return \Illuminate\View\View
     */
    public function edit(Page $page)
    {
        return $this->_edit(function () use ($page) {
            $this->item = $page;

            $this->vars = [
                'layouts' => Layout::all(),
                'types' => Page::$types,
                'actives' => Page::$actives,
            ];
        });
    }

    /**
     * @param Page $page
     * @param PageRequest $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function update(Page $page, PageRequest $request)
    {
        return $this->_update(function () use ($page, $request) {
            $this->item = $page;
            $this->item->update($request->all());
        }, $request);
    }

    /**
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function restore($id)
    {
        return $this->_restore(function () use ($id) {
            $this->item = Page::onlyTrashed()->findOrFail($id);
            $this->item->doNotGenerateUrl()->restore();
        });
    }

    /**
     * @param Page $page
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function destroy(Page $page)
    {
        return $this->_destroy(function () use ($page) {
            $this->item = $page;
            $this->item->delete();
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
            $this->item = Page::onlyTrashed()->findOrFail($id);
            $this->item->forceDelete();
        });
    }

    public function drafts(Request $request, PageFilter $filter, PageSort $sort)
    {
        $items = Page::onlyDrafts()->filtered($request, $filter)->sorted($request, $sort)->paginate(10);

        return view('admin.cms.pages.drafts')->with([
            'items' => $items,
            'layouts' => Layout::all(),
            'types' => Page::$types,
            'actives' => Page::$actives,
        ]);
    }

    public function limbo(PageRequest $request, $id)
    {
        try {
            $item = Page::onlyDrafts()->findOrFail($id);

            switch ($request->method()) {
                case 'GET':
                    return view('admin.cms.pages.limbo')->with([
                        'item' => $item,
                        'layouts' => Layout::all(),
                        'types' => Page::$types,
                        'actives' => Page::$actives,
                    ]);

                    break;
                case 'PUT':
                    try {
                        $item->saveAsDraft($request->all());

                        session()->flash('flash_success', 'The draft was successfully saved!');
                        return redirect()->route('admin.pages.drafts');
                    } catch (DraftException $e) {
                        session()->flash('flash_error', $e->getMessage());
                        return redirect()->route('admin.pages.drafts');
                    } catch (Exception $e) {
                        throw new Exception($e->getMessage());
                    }

                    break;
            }
        } catch (ModelNotFoundException $e) {
            session()->flash('flash_error', 'You are trying to access a draft that does not exist!');
            return redirect()->route('admin.pages.drafts');
        }
    }

    /**
     * @param Draft $draft
     * @return \Illuminate\View\View
     */
    public function draft(Draft $draft)
    {
        if (!session('draft_back_url_' . $draft->id)) {
            session()->put('draft_back_url_' . $draft->id, url()->previous());
        }

        DB::beginTransaction();

        $item = $draft->draftable;
        $item->publishDraft($draft);

        return view('admin.cms.pages.draft')->with([
            'item' => $item,
            'draft' => $draft,
            'layouts' => Layout::all(),
            'types' => Page::$types,
            'actives' => Page::$actives,
        ]);
    }

    /**
     * @param Revision $revision
     * @return \Illuminate\View\View
     */
    public function revision(Revision $revision)
    {
        if (!session('revision_back_url_' . $revision->id)) {
            session()->put('revision_back_url_' . $revision->id, url()->previous());
        }

        DB::beginTransaction();

        $item = $revision->revisionable;
        $item->rollbackToRevision($revision);

        return view('admin.cms.pages.revision')->with([
            'item' => $item,
            'revision' => $revision,
            'layouts' => Layout::all(),
            'types' => Page::$types,
            'actives' => Page::$actives,
        ]);
    }

    /**
     * @param Page $page
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function duplicate(Page $page)
    {
        try {
            $duplicate = $page->saveAsDuplicate();

            session()->flash('flash_success', 'The record was successfully duplicated! You have been redirected to the newly duplicated record.');
            return redirect()->route('admin.pages.edit', $duplicate->id);
        } catch (DuplicateException $e) {
            session()->flash('flash_error', 'Failed duplicating the record! Please try again');
            return back();
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    public function fixTree()
    {
        app(Page::class)->doNotGenerateUrl()->fixTree();

        return back();
    }

    /**
     * @param int|null $parent
     * @return array
     * @throws \Exception
     */
    public function loadTreeNodes($parent = null)
    {
        $data = [];

        if ($parent) {
            $items = Page::whereDescendantOf($parent)->defaultOrder()->get()->toTree();
        } elseif (cache()->has('first_tree_load')) {
            $items = Page::whereIsRoot()->defaultOrder()->get();
            cache()->forget('first_tree_load');
        } else {
            cache()->forever('first_tree_load', true);

            $data[] = [
                'id' => 'root_id',
                'text' => 'Pages',
                'children' => true,
                'type' => 'root',
                'icon' => 'jstree-folder'
            ];
        }

        if (isset($items)) {
            foreach ($items as $item) {
                $data[] = [
                    'id' => $item->id,
                    'text' => $item->name,
                    'children' => $item->children->count() > 0 ? true : false,
                    'type' => 'child',
                    'icon' => 'jstree-folder'
                ];
            }
        }

        return $data;
    }

    /**
     * @param Request $request
     * @param PageFilter $filter
     * @param PageSort $sort
     * @param int|null $parent
     * @return \Illuminate\View\View
     */
    public function listTreeItems(Request $request, PageFilter $filter, PageSort $sort, $parent = null)
    {
        $query = Page::filtered($request, $filter);

        if ($request->has('sort')) {
            $query->sorted($request, $sort);
        } else {
            $query->defaultOrder();
        }

        try {
            $parent = Page::findOrFail($parent);

            $query->whereParent($parent->id);
        } catch (ModelNotFoundException $e) {
            $query->whereIsRoot();
        }

        $items = $query->get();

        return view('admin.cms.pages._table')->with([
            'items' => $items,
            'parent' => $parent,
            'actives' => Page::$actives,
        ]);
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function sortTreeItems(Request $request)
    {
        $tree = [];
        $branch = head($request->input('tree'))['children'];

        $this->rebuildTreeBranch($branch, $tree);

        return app(Page::class)->doNotGenerateUrl()->rebuildTree($tree);
    }

    /**
     * @param Request $request
     * @return void
     */
    public function refreshTreeItemsUrls(Request $request)
    {
        $data = $request->input('data');

        if ((int)$data['parent'] != (int)$data['old_parent']) {
            $parent = Page::find($data['parent']);
            $page = Page::find($data['page']);

            $page->url()->update([
                'url' => trim(($parent ? $parent->url->url . '/' : '') . $page->slug, '/')
            ]);

            $this->rebuildChildrenUrls($page->fresh(['url']));
        }
    }

    /**
     * @param array $items
     * @param array $array
     * @return void
     */
    private function rebuildTreeBranch(array $items, array &$array)
    {
        foreach ($items as $item) {
            if (!is_numeric($item['id'])) {
                continue;
            }

            $_item = [
                'id' => $item['id'],
                'name' => $item['text'],
            ];

            if (isset($item['children']) && is_array($item['children'])) {
                $_item['children'] = [];

                $this->rebuildTreeBranch($item['children'], $_item['children']);
            }

            $array[] = $_item;
        }
    }

    /**
     * @param Page $parent
     * @return void
     */
    private function rebuildChildrenUrls(Page $parent)
    {
        foreach ($parent->children as $child) {
            $child->url()->update([
                'url' => trim(($parent ? $parent->url->url . '/' : '') . $child->slug, '/')
            ]);

            $this->rebuildChildrenUrls($child);
        }
    }

    /**
     * @return CrudOptions
     */
    public static function getCrudOptions()
    {
        return CrudOptions::instance()
            ->setModel(app(Page::class))
            ->setListRoute('admin.pages.index')
            ->setListView('admin.cms.pages.index')
            ->setAddRoute('admin.pages.create')
            ->setAddView('admin.cms.pages.add')
            ->setEditRoute('admin.pages.edit')
            ->setEditView('admin.cms.pages.edit')
            ->setDeletedRoute('admin.pages.deleted')
            ->setDeletedView('admin.cms.pages.deleted');
    }
}