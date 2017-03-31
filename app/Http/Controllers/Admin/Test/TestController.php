<?php

namespace App\Http\Controllers\Admin\Test;

use App\Http\Controllers\Controller;
use App\Http\Requests\Crud\TestRequest;
use App\Http\Filters\Admin\TestFilter;
use App\Http\Sorts\Admin\TestSort;
use App\Models\Test\Test;
use App\Options\CanCrudOptions;
use App\Traits\CanCrud;
use Illuminate\Http\Request;

class TestController extends Controller
{
    use CanCrud;

    /**
     * @param Request $request
     * @param TestFilter $filter
     * @param TestSort $sort
     * @return \Illuminate\View\View
     */
    public function index(Request $request, TestFilter $filter, TestSort $sort)
    {
        return $this->_index(function () use ($request, $filter, $sort) {
            $this->items = Test::filtered($request, $filter)->sorted($request, $sort)->paginate(10);
        });
    }

    /**
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return $this->_create();
    }

    /**
     * @param TestRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(TestRequest $request)
    {
        return $this->_store(function () use ($request) {
            $this->item = Test::create($request->all());
        }, $request);
    }

    /**
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function edit($id)
    {
        /*$this->item = Test::findOrFail($id);

        return $this->item->_video->download();*/

        return $this->_edit(function () use ($id) {
            $this->item = Test::findOrFail($id);
        });
    }

    /**
     * @param TestRequest $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(TestRequest $request, $id)
    {
        return $this->_update(function () use ($request, $id) {
            $this->item = Test::findOrFail($id)->update($request->all());
        }, $request);
    }

    /**
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        return $this->_destroy(function () use ($id) {
            $this->item = Test::findOrFail($id)->delete();
        });
    }

    /**
     * @return CanCrudOptions
     */
    public function getCanCrudOptions()
    {
        return CanCrudOptions::instance()
            ->setModel(app(Test::class))
            ->setListRoute('admin.test.index')
            ->setListView('admin.test.index')
            ->setAddRoute('admin.test.create')
            ->setAddView('admin.test.add')
            ->setEditRoute('admin.test.edit')
            ->setEditView('admin.test.edit');
    }
}