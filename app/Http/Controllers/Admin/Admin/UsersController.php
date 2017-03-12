<?php

namespace App\Http\Controllers\Admin\Admin;

use App\Http\Controllers\Controller;
use App\Http\Filters\AdminGroupFilter;
use App\Http\Filters\AdminUserFilter;
use App\Http\Requests\AdminGroupRequest;
use App\Http\Sorts\AdminGroupSort;
use App\Http\Sorts\AdminUserSort;
use App\Models\Auth\Permission;
use App\Models\Auth\Role;
use App\Models\Auth\User;
use App\Options\CrudOptions;
use App\Traits\CanCrud;

class UsersController extends Controller
{
    use CanCrud;

    /**
     * @param AdminUserFilter $filter
     * @param AdminUserSort $sort
     * @return \Illuminate\View\View
     */
    public function index(AdminUserFilter $filter, AdminUserSort $sort)
    {
        return $this->_index(function () use ($filter, $sort) {
            $this->items = User::role('admin')->filtered($filter)->sorted($sort)->paginate(10);
        });
    }

    /**
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return $this->_create(function () {

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
            $this->item->grantPermission($request->get('permissions'));
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
            $this->item->syncPermissions($request->get('permissions'));
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
            ->setListRoute('admin.admin.users.index')
            ->setListView('admin.admin.users.index')
            ->setAddRoute('admin.admin.users.create')
            ->setAddView('admin.admin.users.add')
            ->setEditRoute('admin.admin.users.edit')
            ->setEditView('admin.admin.users.edit');
    }
}