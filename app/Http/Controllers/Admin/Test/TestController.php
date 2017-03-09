<?php

namespace App\Http\Controllers\Admin\Test;

use App\Http\Controllers\Controller;
use App\Http\Requests\TestRequest;
use App\Http\Filters\TestFilter;
use App\Http\Sorts\TestSort;
use App\Models\Test\Test;
use App\Options\CrudOptions;
use App\Traits\CanCrud;

class TestController extends Controller
{
    use CanCrud;

    /**
     * @param TestFilter $filter
     * @param TestSort $sort
     * @return \Illuminate\View\View
     */
    public function index(TestFilter $filter, TestSort $sort)
    {
        return $this->_index(function () use ($filter, $sort) {
            $this->items = Test::filtered($filter)->sorted($sort)->paginate(10);
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
     * @return CrudOptions
     */
    public function getCrudOptions()
    {
        return CrudOptions::instance()
            ->setModel(app(Test::class))
            ->setListRoute('admin.test.index')
            ->setListView('admin.test.index')
            ->setAddRoute('admin.test.create')
            ->setAddView('admin.test.add')
            ->setEditRoute('admin.test.edit')
            ->setEditView('admin.test.edit');
    }
}