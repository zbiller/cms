<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use App\Http\Filters\Auth\AdminFilter;
use App\Http\Requests\Auth\AdminRequest;
use App\Http\Sorts\Auth\AdminSort;
use App\Models\Auth\Role;
use App\Models\Auth\User;
use App\Traits\CanCrud;
use Illuminate\Http\Request;

class AdminsController extends Controller
{
    use CanCrud;

    /**
     * @var string
     */
    protected $model = User::class;

    /**
     * @param Request $request
     * @param AdminFilter $filter
     * @param AdminSort $sort
     * @return \Illuminate\View\View
     */
    public function index(Request $request, AdminFilter $filter, AdminSort $sort)
    {
        return $this->_index(function () use ($request, $filter, $sort) {
            $this->items = User::notDeveloper()->whereType(User::TYPE_ADMIN)->filtered($request, $filter)->sorted($request, $sort)->paginate(config('crud.per_page'));
            $this->title = 'Admins';
            $this->view = view('admin.auth.admins.index');
            $this->vars = [
                'roles' => Role::whereType(Role::TYPE_ADMIN)->get(),
            ];
        });
    }

    /**
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return $this->_create(function () {
            $this->title = 'Add Admin';
            $this->view = view('admin.auth.admins.add');
            $this->vars = [
                'roles' => Role::whereType(Role::TYPE_ADMIN)->get(),
            ];
        });
    }

    /**
     * @param AdminRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(AdminRequest $request)
    {
        $request = $this->mergeRequest($request);

        return $this->_store(function () use ($request) {
            $this->item = User::create($request->all());
            $this->redirect = redirect()->route('admin.admins.index');

            $this->item->person()->create($request->input('person'));
            $this->item->roles()->attach($request->input('roles'));
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
            $this->title = 'Edit Admin';
            $this->view = view('admin.auth.admins.edit');
            $this->vars = [
                'roles' => Role::whereType(Role::TYPE_ADMIN)->get(),
            ];
        });
    }

    /**
     * @param AdminRequest $request
     * @param User $user
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function update(AdminRequest $request, User $user)
    {
        $request = $this->mergeRequest($request);

        return $this->_update(function () use ($request, $user) {
            $this->item = $user;
            $this->redirect = redirect()->route('admin.admins.index');

            $this->item->update($request->all());
            $this->item->person()->update($request->input('person'));
            $this->item->roles()->sync($request->input('roles'));
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
            $this->redirect = redirect()->route('admin.admins.index');

            $this->item->delete();
        });
    }

    /**
     * @param Request $request
     * @return Request
     */
    protected function mergeRequest(Request $request)
    {
        if ($request->filled('password')) {
            $request->merge([
                'password' => bcrypt($request->input('password')),
            ]);
        } else {
            $request = AdminRequest::create($request->url(), $request->method(), $request->except([
                'password',
                'password_confirmation'
            ]));
        }

        $request->merge([
            'type' => User::TYPE_ADMIN,
        ]);

        return $request;
    }
}