<?php

namespace App\Http\Controllers\Admin\Cms\Pages;

use App\Http\Controllers\Admin\Cms\PagesController;
use App\Http\Filters\Cms\PageFilter;
use App\Http\Sorts\Cms\PageSort;
use App\Models\Cms\Page;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class TreeController extends PagesController
{
    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    public function fix()
    {
        app(Page::class)->doNotGenerateUrl()->doNotSaveBlocks()->fixTree();

        return back();
    }

    /**
     * @param int|null $parent
     * @return array
     * @throws \Exception
     */
    public function loadNodes($parent = null)
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
    public function listItems(Request $request, PageFilter $filter, PageSort $sort, $parent = null)
    {
        $query = Page::filtered($request, $filter);

        if ($request->filled('sort')) {
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
    public function sortItems(Request $request)
    {
        $tree = [];
        $branch = head($request->input('tree'))['children'];

        $this->rebuildBranch($branch, $tree);

        return app(Page::class)->doNotGenerateUrl()->doNotSaveBlocks()->rebuildTree($tree);
    }

    /**
     * @param Request $request
     * @return void
     */
    public function refreshUrls(Request $request)
    {
        $data = $request->input('data');

        if ((int)$data['parent'] != (int)$data['old_parent']) {
            $parent = Page::find($data['parent']);
            $page = Page::find($data['node']);

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
    private function rebuildBranch(array $items, array &$array)
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

                $this->rebuildBranch($item['children'], $_item['children']);
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
}