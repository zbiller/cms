<?php

namespace App\Http\Controllers\Admin\Acl;

use App\Http\Controllers\Controller;
use App\Models\Auth\User;
use App\Models\Auth\Role;
use App\Traits\CanCrud;
use App\Options\CrudOptions;
use App\Http\Requests\AdminUserRequest;
use App\Http\Filters\AdminUserFilter;
use App\Http\Sorts\AdminUserSort;
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
            $this->items = User::notDeveloper()->only('admin')->filtered($request, $filter)->sorted($request, $sort)->paginate(10);
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
     * @param User $user
     * @return \Illuminate\View\View
     */
    public function edit(User $user)
    {
        return $this->_edit(function () use ($user) {
            $this->item = $user;
            $this->vars['roles'] = Role::only(Role::TYPE_ADMIN)->exclude('admin')->get();
        });
    }

    /**
     * @param AdminUserRequest $request
     * @param User $user
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function update(AdminUserRequest $request, User $user)
    {
        return $this->_update(function () use ($request, $user) {
            $request = self::mergeRequest($request);

            $this->item = $user;
            $this->item->update($request->all());
            $this->item->person()->update($request->get('person'));
            $this->item->roles()->sync($request->get('roles'));
        }, $request);
    }

    /**
     * @param User $user
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function destroy(User $user)
    {
        return $this->_destroy(function () use ($user) {
            $this->item = $user;
            $this->item->delete();
        });
    }

    /**
     * @return CrudOptions
     */
    public static function getCrudOptions()
    {
        return CrudOptions::instance()
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