<?php

namespace App\Http\Controllers\Admin\Acl;

use App\Http\Controllers\Controller;
use App\Models\Auth\Role;
use App\Models\Auth\Permission;
use App\Traits\CanCrud;
use App\Options\CanCrudOptions;
use App\Http\Requests\AdminRoleRequest;
use App\Http\Filters\Admin\AdminRoleFilter;
use App\Http\Sorts\Admin\AdminRoleSort;
use Illuminate\Http\Request;

class AdminRolesController extends Controller
{
    use CanCrud;

    /**
     * @param Request $request
     * @param AdminRoleFilter $filter
     * @param AdminRoleSort $sort
     * @return \Illuminate\View\View
     */
    public function index(Request $request, AdminRoleFilter $filter, AdminRoleSort $sort)
    {
        return $this->_index(function () use ($request, $filter, $sort) {
            $this->items = Role::only(Role::TYPE_ADMIN)->exclude('admin')->filtered($request, $filter)->sorted($request, $sort)->paginate(10);
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
     * @throws \Exception
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
     * @param Role $role
     * @return \Illuminate\View\View
     */
    public function edit(Role $role)
    {
        return $this->_edit(function () use ($role) {
            $this->item = $role;
            $this->vars['permissions'] = Permission::getGrouped();
        });
    }

    /**
     * @param AdminRoleRequest $request
     * @param Role $role
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function update(AdminRoleRequest $request, Role $role)
    {
        return $this->_update(function () use ($request, $role) {
            $request = self::mergeRequest($request);

            $this->item = $role;
            $this->item->update($request->all());
            $this->item->permissions()->sync((array)$request->get('permissions'));
        }, $request);
    }

    /**
     * @param Role $role
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function destroy(Role $role)
    {
        return $this->_destroy(function () use ($role) {
            $this->item = $role;
            $this->item->delete();
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