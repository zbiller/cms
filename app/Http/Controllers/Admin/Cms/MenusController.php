<?php

namespace App\Http\Controllers\Admin\Cms;

use App\Models\Cms\Menu;
use App\Http\Controllers\Controller;
use App\Traits\CanCrud;
use App\Http\Requests\MenuRequest;
use App\Http\Filters\MenuFilter;
use App\Http\Sorts\MenuSort;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class MenusController extends Controller
{
    use CanCrud;

    /**
     * @var string
     */
    protected $model = Menu::class;

    /**
     * @return \Illuminate\View\View
     */
    public function locations()
    {
        $this->setMeta('title', 'Admin - Menu Locations');

        return view('admin.cms.menus.locations')->with([
            'title' => 'Menus',
            'locations' => Menu::$locations
        ]);
    }

    /**
     * @param Request $request
     * @param MenuFilter $filter
     * @param MenuSort $sort
     * @param string $location
     * @return \Illuminate\View\View
     * @throws \Exception
     */
    public function index(Request $request, MenuFilter $filter, MenuSort $sort, $location)
    {
        cache()->forget('first_tree_load');

        return $this->_index(function () use ($request, $filter, $sort, $location) {
            $query = Menu::whereIsRoot()->whereLocation($location)->filtered($request, $filter);

            if ($request->has('sort')) {
                $query->sorted($request, $sort);
            } else {
                $query->defaultOrder();
            }

            $this->items = $query->get();
            $this->title = 'Menus';
            $this->view = view('admin.cms.menus.index');
            $this->vars = [
                'location' => $location,
                'types' => Menu::$types,
                'actives' => Menu::$actives,
            ];
        });
    }

    /**
     * @param string $location
     * @param Menu $parent
     * @return \Illuminate\View\View
     */
    public function create($location, Menu $parent = null)
    {
        return $this->_create(function () use ($location, $parent) {
            $this->title = 'Add Menu';
            $this->view = view('admin.cms.menus.add');
            $this->vars = [
                'location' => $location,
                'parent' => $parent->exists ? $parent : null,
                'types' => Menu::$types,
                'actives' => Menu::$actives,
                'windows' => Menu::$windows,
            ];
        });
    }

    /**
     * @param MenuRequest $request
     * @param string $location
     * @param Menu $parent
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function store(MenuRequest $request, $location, Menu $parent = null)
    {
        $request->merge([
            'location' => $location
        ]);

        return $this->_store(function () use ($request, $location, $parent) {
            $this->item = Menu::create($request->all(), $parent->exists ? $parent : null);
            $this->redirect = redirect()->route('admin.menus.index', $location);
        }, $request);
    }

    /**
     * @param string $location
     * @param Menu $menu
     * @return \Illuminate\View\View
     * @throws \Exception
     */
    public function edit($location, Menu $menu)
    {
        return $this->_edit(function () use ($location, $menu) {
            $this->item = $menu;
            $this->title = 'Edit Menu';
            $this->view = view('admin.cms.menus.edit');
            $this->vars = [
                'location' => $location,
                'types' => Menu::$types,
                'actives' => Menu::$actives,
                'windows' => Menu::$windows,
            ];
        });
    }

    /**
     * @param string $location
     * @param Menu $menu
     * @param MenuRequest $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function update($location, Menu $menu, MenuRequest $request)
    {
        return $this->_update(function () use ($location, $menu, $request) {
            $this->item = $menu;
            $this->redirect = redirect()->route('admin.menus.index', $location);

            $this->item->update($request->all());
        }, $request);
    }

    /**
     * @param string $location
     * @param Menu $menu
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function destroy($location, Menu $menu)
    {
        return $this->_destroy(function () use ($location, $menu) {
            $this->item = $menu;
            $this->redirect = redirect()->route('admin.menus.index', $location);

            $this->item->delete();
        });
    }

    /**
     * @param string $type
     * @return string
     */
    public function entity($type)
    {
        @$class = Menu::$map['types'][$type];
        $model = $class && class_exists($class) ? app($class) : null;
        $result = [];

        if (!$model) {
            return json_encode([
                'status' => true,
            ]);
        }

        foreach ($model->get() as $item) {
            $result[] = [
                'value' => $item->id,
                'name' => $item->name,
            ];
        }

        return json_encode([
            'status' => true,
            'attributes' => $result,
        ]);
    }

    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    public function fixTree()
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
    public function loadTreeNodes($location, $parent = null)
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
    public function listTreeItems(Request $request, MenuFilter $filter, MenuSort $sort, $location, $parent = null)
    {
        $query = Menu::whereLocation($location)->filtered($request, $filter);

        if ($request->has('sort')) {
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
    public function sortTreeItems(Request $request)
    {
        $tree = [];
        $branch = head($request->input('tree'))['children'];

        $this->rebuildTreeBranch($branch, $tree);

        return Menu::rebuildTree($tree);
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
}