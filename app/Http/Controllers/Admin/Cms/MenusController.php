<?php

namespace App\Http\Controllers\Admin\Cms;

use App\Http\Controllers\Controller;
use App\Http\Filters\Cms\MenuFilter;
use App\Http\Requests\Cms\MenuRequest;
use App\Http\Sorts\Cms\MenuSort;
use App\Models\Cms\Menu;
use App\Traits\CanCrud;
use Illuminate\Http\Request;

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

        return response()->json([
            'status' => true,
            'attributes' => $result,
        ]);
    }
}