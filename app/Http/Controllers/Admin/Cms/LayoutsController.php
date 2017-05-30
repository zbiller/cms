<?php

namespace App\Http\Controllers\Admin\Cms;

use App\Http\Controllers\Controller;
use App\Models\Cms\Layout;
use App\Models\Cms\Block;
use App\Http\Filters\LayoutFilter;
use App\Http\Requests\LayoutRequest;
use App\Http\Sorts\LayoutSort;
use App\Traits\CanCrud;
use Illuminate\Http\Request;

class LayoutsController extends Controller
{
    use CanCrud;

    /**
     * @var string
     */
    protected $model = Layout::class;

    /**
     * @param Request $request
     * @param LayoutFilter $filter
     * @param LayoutSort $sort
     * @return \Illuminate\View\View
     */
    public function index(Request $request, LayoutFilter $filter, LayoutSort $sort)
    {
        return $this->_index(function () use ($request, $filter, $sort) {
            $this->items = Layout::filtered($request, $filter)->sorted($request, $sort)->paginate(10);
            $this->title = 'Layouts';
            $this->view = view('admin.cms.layouts.index');
            $this->vars['types'] = Layout::$types;
        });
    }

    /**
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return $this->_create(function () {
            $this->title = 'Add Layout';
            $this->view = view('admin.cms.layouts.add');
            $this->vars = [
                'types' => Layout::$types,
                'locations' => Block::getLocations(),
            ];
        });
    }

    /**
     * @param LayoutRequest $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function store(LayoutRequest $request)
    {
        return $this->_store(function () use ($request) {
            $this->item = Layout::create($request->all());
            $this->redirect = redirect()->route('admin.layouts.index');
        }, $request);
    }

    /**
     * @param Layout $layout
     * @return \Illuminate\View\View
     */
    public function edit(Layout $layout)
    {
        return $this->_edit(function () use ($layout) {
            $this->item = $layout;
            $this->title = 'Edit Layout';
            $this->view = view('admin.cms.layouts.edit');
            $this->vars = [
                'types' => Layout::$types,
                'locations' => Block::getLocations(),
            ];
        });
    }

    /**
     * @param LayoutRequest $request
     * @param Layout $layout
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function update(LayoutRequest $request, Layout $layout)
    {
        return $this->_update(function () use ($request, $layout) {
            $this->item = $layout;
            $this->redirect = redirect()->route('admin.layouts.index');

            $this->item->update($request->all());
        }, $request);
    }

    /**
     * @param Layout $layout
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function destroy(Layout $layout)
    {
        return $this->_destroy(function () use ($layout) {
            $this->item = $layout;
            $this->redirect = redirect()->route('admin.layouts.index');

            $this->item->delete();
        });
    }
}