<?php

namespace App\Http\Controllers\Admin\Cms;

use App\Models\Cms\Page;
use App\Models\Cms\Layout;
use App\Http\Controllers\Controller;
use App\Traits\CanCrud;
use App\Traits\CanHandleTree;
use App\Http\Requests\PageRequest;
use App\Http\Filters\PageFilter;
use App\Http\Sorts\PageSort;
use App\Options\CrudOptions;
use App\Options\TreeOptions;
use Illuminate\Http\Request;

class PagesController extends Controller
{
    use CanCrud;
    use CanHandleTree {
        CanCrud::checkOptionsMethodDeclaration insteadof CanHandleTree;
    }

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

    /**
     * @return TreeOptions
     */
    public static function getTreeOptions()
    {
        return TreeOptions::instance()
            ->setModel(app(Page::class))
            ->setFilter(app(PageFilter::class))
            ->setSort(app(PageSort::class))
            ->setName('Pages')
            ->setView('admin.cms.pages._table')
            ->setVars([
                'actives' => Page::$actives
            ]);
    }
}