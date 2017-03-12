<?php

namespace App\Http\Controllers\Admin\Admin;

use App\Http\Controllers\Controller;
use App\Http\Filters\AdminGroupFilter;
use App\Http\Requests\AdminGroupRequest;
use App\Http\Sorts\AdminGroupSort;
use App\Models\Auth\Permission;
use App\Models\Auth\Role;
use App\Options\CrudOptions;
use App\Traits\CanCrud;

class GroupsController extends Controller
{
    use CanCrud;

    /**
     * @param AdminGroupFilter $filter
     * @param AdminGroupSort $sort
     * @return \Illuminate\View\View
     */
    public function index(AdminGroupFilter $filter, AdminGroupSort $sort)
    {
        return $this->_index(function () use ($filter, $sort) {
            $this->items = Role::exclude('admin')->filtered($filter)->sorted($sort)->paginate(10);
        });
    }

    /**
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return $this->_create(function () {
            $this->vars['permissions'] = Permission::getGrouped();
        });
    }

    /**
     * @param AdminGroupRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(AdminGroupRequest $request)
    {
        return $this->_store(function () use ($request) {
            $this->item = Role::create($request->all());
            $this->item->permissions()->attach((array)$request->get('permissions'));
        }, $request);
    }

    /**
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function edit($id)
    {
        return $this->_edit(function () use ($id) {
            $this->item = Role::findOrFail($id);
            $this->vars['permissions'] = Permission::getGrouped();
        });
    }

    /**
     * @param AdminGroupRequest $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(AdminGroupRequest $request, $id)
    {
        return $this->_update(function () use ($request, $id) {
            $this->item = Role::findOrFail($id);
            $this->item->update($request->all());
            $this->item->permissions()->sync((array)$request->get('permissions'));
        }, $request);
    }

    /**
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        return $this->_destroy(function () use ($id) {
            $this->item = Role::findOrFail($id)->delete();
        });
    }

    /**
     * @return CrudOptions
     */
    public function getCrudOptions()
    {
        return CrudOptions::instance()
            ->setModel(app(Role::class))
            ->setListRoute('admin.admin.groups.index')
            ->setListView('admin.admin.groups.index')
            ->setAddRoute('admin.admin.groups.create')
            ->setAddView('admin.admin.groups.add')
            ->setEditRoute('admin.admin.groups.edit')
            ->setEditView('admin.admin.groups.edit');
    }
}