<?php

namespace App\Http\Controllers\Admin\Acl;

use App\Http\Controllers\Controller;
use App\Models\Auth\User;
use App\Models\Auth\Role;
use App\Traits\CanCrud;
use App\Http\Requests\AdminRequest;
use App\Http\Filters\AdminFilter;
use App\Http\Sorts\AdminSort;
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
            $this->items = User::type(User::TYPE_ADMIN)->not('developer')->filtered($request, $filter)->sorted($request, $sort)->paginate(10);
            $this->title = 'Admins';
            $this->view = view('admin.acl.admins.index');
            $this->vars = [
                'roles' => Role::type(Role::TYPE_ADMIN)->get(),
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
            $this->view = view('admin.acl.admins.add');
            $this->vars = [
                'roles' => Role::type(Role::TYPE_ADMIN)->get(),
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
            $this->title = 'Edit Admin';
            $this->view = view('admin.acl.admins.edit');
            $this->vars = [
                'roles' => Role::type(Role::TYPE_ADMIN)->get(),
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
        if ($request->has('password')) {
            $request->merge([
                'password' => bcrypt($request->get('password')),
            ]);
        } else {
            $request = AdminRequest::create(
                $request->url(),
                $request->method(),
                $request->except([
                    'password',
                    'password_confirmation'
                ])
            );
        }

        $request->merge([
            'type' => User::TYPE_ADMIN,
        ]);

        return $request;
    }
}