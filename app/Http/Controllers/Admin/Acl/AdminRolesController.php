<?php

namespace App\Http\Controllers\Admin\Acl;

use App\Http\Controllers\Controller;
use App\Models\Auth\Role;
use App\Models\Auth\Permission;
use App\Traits\CanCrud;
use App\Options\CanCrudOptions;
use App\Http\Requests\Crud\AdminRoleRequest;
use App\Http\Filters\Admin\AdminRoleFilter;
use App\Http\Sorts\Admin\AdminRoleSort;
use Illuminate\Http\Request;

class AdminRolesController extends Controller
{
    use CanCrud;

    /**
     * @param AdminRoleFilter $filter
     * @param AdminRoleSort $sort
     * @return \Illuminate\View\View
     */
    public function index(AdminRoleFilter $filter, AdminRoleSort $sort)
    {
        return $this->_index(function () use ($filter, $sort) {
            $this->items = Role::only(Role::TYPE_ADMIN)->exclude('admin')->filtered($filter)->sorted($sort)->paginate(10);
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
     * @param AdminRoleRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(AdminRoleRequest $request)
    {
        return $this->_store(function () use ($request) {
            $request = self::mergeRequest($request);

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
     * @param AdminRoleRequest $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(AdminRoleRequest $request, $id)
    {
        return $this->_update(function () use ($request, $id) {
            $request = self::mergeRequest($request);

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
     * @return CanCrudOptions
     */
    public function getCanCrudOptions()
    {
        return CanCrudOptions::instance()
            ->setModel(app(Role::class))
            ->setListRoute('admin.admin_roles.index')
            ->setListView('admin.acl.admin_roles.index')
            ->setAddRoute('admin.admin_roles.create')
            ->setAddView('admin.acl.admin_roles.add')
            ->setEditRoute('admin.admin_roles.edit')
            ->setEditView('admin.acl.admin_roles.edit');
    }

    /**
     * @param Request $request
     * @return Request
     */
    protected static function mergeRequest(Request $request)
    {
        $request->merge([
            'type' => Role::TYPE_ADMIN
        ]);

        return $request;
    }
}