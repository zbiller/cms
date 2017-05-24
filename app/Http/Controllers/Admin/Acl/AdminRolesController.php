<?php

namespace App\Http\Controllers\Admin\Acl;

use App\Http\Controllers\Controller;
use App\Models\Auth\Role;
use App\Models\Auth\Permission;
use App\Traits\CanCrud;
use App\Options\CrudOptions;
use App\Http\Requests\AdminRoleRequest;
use App\Http\Filters\AdminRoleFilter;
use App\Http\Sorts\AdminRoleSort;
use Illuminate\Http\Request;

class AdminRolesController extends Controller
{
    use CanCrud;

    /**
     * @var string
     */
    protected $model = Role::class;

    /**
     * @param Request $request
     * @param AdminRoleFilter $filter
     * @param AdminRoleSort $sort
     * @return \Illuminate\View\View
     */
    public function index(Request $request, AdminRoleFilter $filter, AdminRoleSort $sort)
    {
        return $this->_index(function () use ($request, $filter, $sort) {
            $permissions = [];

            foreach (Permission::getGrouped() as $group => $perms) {
                foreach ($perms as $permission) {
                    $permissions[$group][$permission->id] = $permission->label;
                }
            }

            $this->items = Role::only(Role::TYPE_ADMIN)->exclude('admin')->filtered($request, $filter)->sorted($request, $sort)->paginate(10);
            $this->view = view('admin.acl.admin_roles.index');
            $this->vars = [
                'permissions' => $permissions,
            ];
        });
    }

    /**
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return $this->_create(function () {
            $this->view = view('admin.acl.admin_roles.add');
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
        $request = $this->mergeRequest($request);

        return $this->_store(function () use ($request) {
            $this->item = Role::create($request->all());
            $this->redirect = redirect()->route('admin.admin_roles.index');

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
            $this->view = view('admin.acl.admin_roles.edit');
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
        $request = $this->mergeRequest($request);

        return $this->_update(function () use ($request, $role) {
            $this->item = $role;
            $this->redirect = redirect()->route('admin.admin_roles.index');

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
            $this->redirect = redirect()->route('admin.admin_roles.index');

            $this->item->delete();
        });
    }

    /**
     * @param Request $request
     * @return Request
     */
    protected function mergeRequest(Request $request)
    {
        $request->merge([
            'type' => Role::TYPE_ADMIN
        ]);

        return $request;
    }
}