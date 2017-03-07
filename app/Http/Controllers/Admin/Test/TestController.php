<?php

namespace App\Http\Controllers\Admin\Test;

use App\Http\Controllers\AdminController;
use App\Http\Filters\TestFilter;
use App\Http\Requests\TestRequest;
use App\Models\Test\Test;

class TestController extends AdminController
{
    /**
     * @var string
     */
    protected $model = Test::class;

    /**
     * @var array
     */
    protected $list = [
        'route' => 'admin.test.index',
        'view' => 'admin.test.index'
    ];

    /**
     * @var array
     */
    protected $add = [
        'route' => 'admin.test.create',
        'view' => 'admin.test.add'
    ];

    /**
     * @var array
     */
    protected $edit = [
        'route' => 'admin.test.edit',
        'view' => 'admin.test.edit'
    ];

    /**
     * @param TestFilter $filter
     * @return \Illuminate\View\View
     */
    public function index(TestFilter $filter)
    {
        return $this->_index(function () use ($filter) {
            $this->items = $this->model->filtered($filter)->paginate(10);
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
            $this->item = $this->model->create($request->all());
        }, $request);
    }

    /**
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function edit($id)
    {
        return $this->_edit(function () use ($id) {
            $this->item = $this->model->findOrFail($id);
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
            $this->item = $this->model->findOrFail($id)->update($request->all());
        }, $request);
    }

    /**
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        return $this->_destroy(function () use ($id) {
            $this->item = $this->model->findOrFail($id)->delete();
        });
    }
}