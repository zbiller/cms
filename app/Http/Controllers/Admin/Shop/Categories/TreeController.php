<?php

namespace App\Http\Controllers\Admin\Shop\Categories;

use App\Http\Controllers\Admin\Shop\CategoriesController;
use App\Http\Filters\Shop\CategoryFilter;
use App\Http\Sorts\Shop\CategorySort;
use App\Models\Shop\Category;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class TreeController extends CategoriesController
{
    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    public function fix()
    {
        app(Category::class)->doNotGenerateUrl()->doNotSaveBlocks()->fixTree();

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
            $items = Category::whereDescendantOf($parent)->defaultOrder()->get()->toTree();
        } elseif (cache()->has('first_tree_load')) {
            $items = Category::whereIsRoot()->defaultOrder()->get();
            cache()->forget('first_tree_load');
        } else {
            cache()->forever('first_tree_load', true);

            $data[] = [
                'id' => 'root_id',
                'text' => 'Categories',
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
     * @param CategoryFilter $filter
     * @param CategorySort $sort
     * @param int|null $parent
     * @return \Illuminate\View\View
     */
    public function listItems(Request $request, CategoryFilter $filter, CategorySort $sort, $parent = null)
    {
        $query = Category::filtered($request, $filter);

        if ($request->filled('sort')) {
            $query->sorted($request, $sort);
        } else {
            $query->defaultOrder();
        }

        try {
            $parent = Category::findOrFail($parent);

            $query->whereParent($parent->id);
        } catch (ModelNotFoundException $e) {
            $query->whereIsRoot();
        }

        $items = $query->get();

        return view('admin.shop.categories._table')->with([
            'items' => $items,
            'parent' => $parent,
            'actives' => Category::$actives,
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

        return app(Category::class)->doNotGenerateUrl()->doNotSaveBlocks()->rebuildTree($tree);
    }

    /**
     * @param Request $request
     * @return void
     */
    public function refreshUrls(Request $request)
    {
        $data = $request->input('data');

        if ((int)$data['parent'] != (int)$data['old_parent']) {
            $parent = Category::find($data['parent']);
            $category = Category::find($data['node']);

            $category->url()->update([
                'url' => trim(($parent ? $parent->url->url . '/' : page()->find('shop')->url->url . '/') . $category->slug, '/')
            ]);

            $category = $category->fresh();
            $category->products->each(function ($product) use ($category) {
                $product->url()->update([
                    'url' => trim($category->url->url . '/' . $product->slug, '/')
                ]);
            });

            $this->rebuildChildrenUrls($category->fresh(['url']));
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
     * @param Category $parent
     * @return void
     */
    private function rebuildChildrenUrls(Category $parent)
    {
        foreach ($parent->children as $child) {
            $child->url()->update([
                'url' => trim(($parent ? $parent->url->url . '/' : '') . $child->slug, '/')
            ]);

            $this->rebuildChildrenUrls($child);
        }
    }
}