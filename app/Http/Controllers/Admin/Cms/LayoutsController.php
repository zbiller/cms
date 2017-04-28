<?php

namespace App\Http\Controllers\Admin\Cms;

use App\Http\Controllers\Controller;
use App\Http\Filters\LayoutFilter;
use App\Http\Requests\LayoutRequest;
use App\Http\Sorts\LayoutSort;
use App\Models\Cms\Block;
use App\Models\Cms\Layout;
use App\Traits\CanCrud;
use App\Options\CrudOptions;
use Illuminate\Http\Request;

class LayoutsController extends Controller
{
    use CanCrud;

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

            $this->vars['types'] = Layout::$types;
        });
    }

    /**
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return $this->_create(function () {
            $this->vars['types'] = Layout::$types;
            $this->vars['locations'] = Block::getLocations();
        });
    }

    /**
     * @param LayoutRequest $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function store(LayoutRequest $request)
    {
        $request = $this->mergeRequest($request);

        return $this->_store(function () use ($request) {
            $this->item = Layout::create($request->all());
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
            $this->vars['types'] = Layout::$types;
            $this->vars['locations'] = Block::getLocations();
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
        $request = $this->mergeRequest($request);

        return $this->_update(function () use ($request, $layout) {
            $this->item = $layout;
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
            $this->item->delete();
        });
    }

    /**
     * @return CrudOptions
     */
    public static function getCrudOptions()
    {
        return CrudOptions::instance()
            ->setModel(app(Layout::class))
            ->setListRoute('admin.layouts.index')
            ->setListView('admin.cms.layouts.index')
            ->setAddRoute('admin.layouts.create')
            ->setAddView('admin.cms.layouts.add')
            ->setEditRoute('admin.layouts.edit')
            ->setEditView('admin.cms.layouts.edit');
    }

    /**
     * @param LayoutRequest $request
     * @return LayoutRequest
     */
    private function mergeRequest(LayoutRequest $request)
    {
        $request->merge([
            'block_locations' => $request->has('block_locations') ? implode(',' , $request->get('block_locations')) : null
        ]);

        return $request;
    }
}