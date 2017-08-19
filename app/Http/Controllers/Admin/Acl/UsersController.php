<?php

namespace App\Http\Controllers\Admin\Acl;

use App\Http\Controllers\Controller;
use App\Http\Filters\Auth\UserFilter;
use App\Http\Requests\Auth\UserRequest;
use App\Http\Sorts\Auth\UserSort;
use App\Models\Auth\User;
use App\Traits\CanCrud;
use Illuminate\Http\Request;

class UsersController extends Controller
{
    use CanCrud;

    /**
     * @var string
     */
    protected $model = User::class;

    /**
     * @param Request $request
     * @param UserFilter $filter
     * @param UserSort $sort
     * @return \Illuminate\View\View
     */
    public function index(Request $request, UserFilter $filter, UserSort $sort)
    {
        return $this->_index(function () use ($request, $filter, $sort) {
            $this->items = User::whereType(User::TYPE_FRONT)->filtered($request, $filter)->sorted($request, $sort)->paginate(10);
            $this->title = 'Users';
            $this->view = view('admin.acl.users.index');
            $this->vars = [
                'verified' => User::$verified,
            ];
        });
    }

    /**
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return $this->_create(function () {
            $this->title = 'Add User';
            $this->view = view('admin.acl.users.add');
            $this->vars = [
                'verified' => User::$verified,
            ];
        });
    }

    /**
     * @param UserRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(UserRequest $request)
    {
        $request = $this->mergeRequest($request);

        return $this->_store(function () use ($request) {
            $this->item = User::create($request->all());
            $this->redirect = redirect()->route('admin.users.index');

            $this->item->person()->create($request->get('person'));
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
            $this->title = 'Edit User';
            $this->view = view('admin.acl.users.edit');
            $this->vars = [
                'verified' => User::$verified,
            ];
        });
    }

    /**
     * @param UserRequest $request
     * @param User $user
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function update(UserRequest $request, User $user)
    {
        $request = $this->mergeRequest($request);

        return $this->_update(function () use ($request, $user) {
            $this->item = $user;
            $this->redirect = redirect()->route('admin.users.index');

            $this->item->update($request->all());
            $this->item->person()->update($request->get('person'));
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
            $this->redirect = redirect()->route('admin.users.index');

            $this->item->delete();
        });
    }

    /**
     * @param User $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function impersonate(User $user)
    {
        auth()->guard('user')->login($user);
        flash()->error('You are now signed in as ' . $user->full_name);

        return redirect('/');
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
            $request = UserRequest::create($request->url(), $request->method(), $request->except([
                'password',
                'password_confirmation'
            ]));
        }

        $request->merge([
            'type' => User::TYPE_FRONT,
        ]);

        return $request;
    }
}