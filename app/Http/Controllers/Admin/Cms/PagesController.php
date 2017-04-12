<?php

namespace App\Http\Controllers\Admin\Cms;

use App\Http\Controllers\Controller;
use App\Models\Cms\Page;
use App\Models\Cms\Layout;
use App\Traits\CanCrud;
use App\Http\Requests\PageRequest;
use App\Http\Filters\PageFilter;
use App\Http\Sorts\PageSort;
use App\Options\CanCrudOptions;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class PagesController extends Controller
{
    use CanCrud;
    //use CanHandleTree;

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
            $query = Page::with('url')->whereIsRoot()->filtered($request, $filter);
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
                $request->all(),
                $parent->exists ? $parent : null
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
     * @param Request $request
     * @param PageFilter $filter
     * @param PageSort $sort
     * @return \Illuminate\View\View
     */
    public function deleted(Request $request, PageFilter $filter, PageSort $sort)
    {
        return $this->_deleted(function () use ($request, $filter, $sort) {
            $this->items = Page::with('url')->onlyTrashed()->filtered($request, $filter)->sorted($request, $sort)->paginate(10);

            $this->vars = [
                'layouts' => Layout::all(),
                'types' => Page::$types,
                'actives' => Page::$actives,
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
            $this->item = Page::onlyTrashed()->findOrFail($id);
            $this->item->doNotGenerateUrl()->restore();
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
            $this->forceDelete();
        });
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
     * @param null $parent
     * @return array
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
     * @param Page $parent
     * @return \Illuminate\Contracts\View\View
     */
    public function listTreeItems(Request $request, PageFilter $filter, PageSort $sort, Page $parent = null)
    {
        $query = Page::with('url')->filtered($request, $filter);

        $request->has('sort') ? $query->sorted($request, $sort) : $query->defaultOrder();
        $parent->exists ? $query->whereParent($parent->id) : $query->whereIsRoot();

        $items = $query->get();

        return view('admin.cms.pages._table')->with([
            'items' => $items,
            'parent' => $parent,
            'actives' => Page::$actives,
        ]);
    }

    /**
     * @return mixed
     */
    public function sortTreeItems()
    {
        $tree = [];
        $branch = head(request()->input('tree'))['children'];

        $this->rebuildTreeBranch($branch, $tree);

        return app(Page::class)->doNotGenerateUrl()->rebuildTree($tree);
    }

    /**
     * @return void
     */
    public function refreshTreeItemsUrls()
    {
        $data = request()->input('data');

        if ((int)$data['parent'] != (int)$data['old_parent']) {
            $parent = Page::find($data['parent']);
            $page = Page::find($data['page']);

            $page->url()->update([
                'url' => trim(($parent ? $parent->url->url . '/' : '') . $page->slug, '/')
            ]);

            $this->rebuildChildrenUrls($page);
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
     * @param Page $page
     * @return void
     */
    private function rebuildChildrenUrls(Page $page)
    {
        foreach ($page->children as $child) {
            $child->url()->update([
                'url' => trim(($page ? $page->url->url . '/' : '') . $child->slug, '/')
            ]);

            $this->rebuildChildrenUrls($child);
        }
    }




















    /**
     * @return CanCrudOptions
     */
    public function getCanCrudOptions()
    {
        return CanCrudOptions::instance()
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