<?php

namespace App\Http\Controllers\Admin\Acl;

use App\Http\Controllers\Controller;
use App\Models\Auth\Role;
use App\Models\Auth\Permission;
use App\Traits\CanCrud;
use App\Options\CrudOptions;
use App\Http\Requests\RoleRequest;
use App\Http\Filters\RoleFilter;
use App\Http\Sorts\RoleSort;
use Illuminate\Http\Request;

class RolesController extends Controller
{
    use CanCrud;

    /**
     * @var string
     */
    protected $model = Role::class;

    /**
     * @param Request $request
     * @param RoleFilter $filter
     * @param RoleSort $sort
     * @return \Illuminate\View\View
     */
    public function index(Request $request, RoleFilter $filter, RoleSort $sort)
    {
        return $this->_index(function () use ($request, $filter, $sort) {
            $permissions = [];

            foreach (Permission::getGrouped(Permission::TYPE_ADMIN) as $group => $perms) {
                foreach ($perms as $permission) {
                    $permissions[$group][$permission->id] = $permission->label;
                }
            }

            $this->items = Role::type(Role::TYPE_ADMIN)->filtered($request, $filter)->sorted($request, $sort)->paginate(10);
            $this->title = 'Roles';
            $this->view = view('admin.acl.roles.index');
            $this->vars = [
                'permissions' => $permissions,
                'types' => Role::$types,
            ];
        });
    }

    /**
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return $this->_create(function () {
            $this->title = 'Add Role';
            $this->view = view('admin.acl.roles.add');
            $this->vars['permissions'] = Permission::getGrouped(Permission::TYPE_ADMIN);
        });
    }

    /**
     * @param RoleRequest $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function store(RoleRequest $request)
    {
        $request = $this->mergeRequest($request);

        return $this->_store(function () use ($request) {
            $this->item = Role::create($request->all());
            $this->redirect = redirect()->route('admin.roles.index');

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
            $this->title = 'Edit Role';
            $this->view = view('admin.acl.roles.edit');
            $this->vars['permissions'] = Permission::getGrouped(Permission::TYPE_ADMIN);
        });
    }

    /**
     * @param RoleRequest $request
     * @param Role $role
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function update(RoleRequest $request, Role $role)
    {
        $request = $this->mergeRequest($request);

        return $this->_update(function () use ($request, $role) {
            $this->item = $role;
            $this->redirect = redirect()->route('admin.roles.index');

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
            $this->redirect = redirect()->route('admin.roles.index');

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