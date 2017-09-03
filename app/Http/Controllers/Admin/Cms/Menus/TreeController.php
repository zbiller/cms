<?php

namespace App\Http\Controllers\Admin\Cms\Menus;

use App\Http\Controllers\Admin\Cms\MenusController;
use App\Http\Filters\Cms\MenuFilter;
use App\Http\Sorts\Cms\MenuSort;
use App\Models\Cms\Menu;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class TreeController extends MenusController
{
    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    public function fix()
    {
        Menu::fixTree();

        return back();
    }

    /**
     * @param string $location
     * @param int|null $parent
     * @return array
     * @throws \Exception
     */
    public function loadNodes($location, $parent = null)
    {
        $data = [];
        $query = Menu::whereLocation($location);

        if ($parent) {
            $items = $query->whereDescendantOf($parent)->defaultOrder()->get()->toTree();
        } elseif (cache()->has('first_tree_load')) {
            $items = $query->whereIsRoot()->defaultOrder()->get();

            cache()->forget('first_tree_load');
        } else {
            cache()->forever('first_tree_load', true);

            $data[] = [
                'id' => 'root_id',
                'text' => title_case($location) . ' Menu',
                'children' => true,
                'type' => 'root',
            ];
        }

        if (isset($items)) {
            foreach ($items as $item) {
                $data[] = [
                    'id' => $item->id,
                    'text' => $item->name,
                    'children' => $item->children->count() > 0 ? true : false,
                    'type' => 'child',
                ];
            }
        }

        return $data;
    }

    /**
     * @param Request $request
     * @param MenuFilter $filter
     * @param MenuSort $sort
     * @param string $location
     * @param int|null $parent
     * @return \Illuminate\View\View
     */
    public function listItems(Request $request, MenuFilter $filter, MenuSort $sort, $location, $parent = null)
    {
        $query = Menu::whereLocation($location)->filtered($request, $filter);

        if ($request->filled('sort')) {
            $query->sorted($request, $sort);
        } else {
            $query->defaultOrder();
        }

        try {
            $parent = Menu::findOrFail($parent);

            $query->whereParent($parent->id);
        } catch (ModelNotFoundException $e) {
            $query->whereIsRoot();
        }

        $items = $query->get();

        return view('admin.cms.menus._table')->with([
            'items' => $items,
            'parent' => $parent,
            'location' => $location,
            'types' => Menu::$types,
            'actives' => Menu::$actives,
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

        return Menu::rebuildTree($tree);
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
}