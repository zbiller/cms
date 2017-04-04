<?php

namespace App\Http\Controllers\Admin\Acl;

use App\Http\Controllers\Controller;
use App\Models\Auth\User;
use App\Models\Auth\Role;
use App\Traits\CanCrud;
use App\Options\CanCrudOptions;
use App\Http\Requests\Crud\AdminUserRequest;
use App\Http\Filters\Admin\AdminUserFilter;
use App\Http\Sorts\Admin\AdminUserSort;
use Illuminate\Http\Request;

class AdminUsersController extends Controller
{
    use CanCrud;

    /**
     * @param Request $request
     * @param AdminUserFilter $filter
     * @param AdminUserSort $sort
     * @return \Illuminate\View\View
     */
    public function index(Request $request, AdminUserFilter $filter, AdminUserSort $sort)
    {
        return $this->_index(function () use ($request, $filter, $sort) {
            $this->items = User::only('admin')->notDeveloper()->filtered($request, $filter)->sorted($request, $sort)->paginate(10);
        });
    }

    /**
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return $this->_create(function () {
            $this->vars['roles'] = Role::only(Role::TYPE_ADMIN)->exclude('admin')->get();
        });
    }

    /**
     * @param AdminUserRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(AdminUserRequest $request)
    {
        return $this->_store(function () use ($request) {
            $request = self::mergeRequest($request);

            $this->item = User::create($request->all());
            $this->item->person()->create($request->get('person'));
            $this->item->roles()->attach($request->get('roles'));
        }, $request);
    }

    /**
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function edit($id)
    {
        return $this->_edit(function () use ($id) {
            $this->item = User::findOrFail($id);
            $this->vars['roles'] = Role::only(Role::TYPE_ADMIN)->exclude('admin')->get();
        });
    }

    /**
     * @param AdminUserRequest $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(AdminUserRequest $request, $id)
    {
        return $this->_update(function () use ($request, $id) {
            $request = self::mergeRequest($request);

            $this->item = User::findOrFail($id);
            $this->item->update($request->all());
            $this->item->person()->update($request->get('person'));
            $this->item->roles()->sync($request->get('roles'));
        }, $request);
    }

    /**
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        return $this->_destroy(function () use ($id) {
            $this->item = User::findOrFail($id)->delete();
        });
    }

    /**
     * @return CanCrudOptions
     */
    public function getCanCrudOptions()
    {
        return CanCrudOptions::instance()
            ->setModel(app(Role::class))
            ->setListRoute('admin.admin_users.index')
            ->setListView('admin.acl.admin_users.index')
            ->setAddRoute('admin.admin_users.create')
            ->setAddView('admin.acl.admin_users.add')
            ->setEditRoute('admin.admin_users.edit')
            ->setEditView('admin.acl.admin_users.edit');
    }

    /**
     * @param Request $request
     * @return Request
     */
    protected static function mergeRequest(Request $request)
    {
        $request->merge([
            'password' => bcrypt($request->get('password')),
            'roles' => array_merge((array)Role::findByName('admin')->id, $request->get('roles')),
        ]);

        return $request;
    }
}