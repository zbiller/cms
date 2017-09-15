<?php

namespace App\Http\Controllers\Admin\Auth\Users;

use App\Http\Controllers\Controller;
use App\Http\Filters\Auth\AddressFilter;
use App\Http\Requests\Auth\AddressRequest;
use App\Http\Sorts\Auth\AddressSort;
use App\Models\Auth\User;
use App\Models\Auth\User\Address;
use App\Models\Localisation\City;
use App\Models\Localisation\Country;
use App\Models\Localisation\State;
use App\Traits\CanCrud;
use Illuminate\Http\Request;

class AddressesController extends Controller
{
    use CanCrud;

    /**
     * @var string
     */
    protected $model = Address::class;

    /**
     * @param Request $request
     * @param AddressFilter $filter
     * @param AddressSort $sort
     * @param User $user
     * @return \Illuminate\View\View
     */
    public function index(Request $request, AddressFilter $filter, AddressSort $sort, User $user)
    {
        return $this->_index(function () use ($request, $filter, $sort, $user) {
            $this->items = Address::whereUser($user->id)->filtered($request, $filter)->sorted($request, $sort)->paginate(config('crud.per_page'));
            $this->title = 'Addresses';
            $this->view = view('admin.auth.users.addresses.index');
            $this->vars = [
                'user' => $user,
                'countries' => Country::inAlphabeticalOrder()->get(),
            ];
        });
    }

    /**
     * @param User $user
     * @return \Illuminate\View\View
     */
    public function create(User $user)
    {
        return $this->_create(function () use ($user) {
            $this->title = 'Add Address';
            $this->view = view('admin.auth.users.addresses.add');
            $this->vars = [
                'user' => $user,
                'countries' => Country::inAlphabeticalOrder()->get(),
            ];
        });
    }

    /**
     * @param AddressRequest $request
     * @param User $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(AddressRequest $request, User $user)
    {
        return $this->_store(function () use ($request, $user) {
            $this->item = Address::create($request->all());
            $this->redirect = redirect()->route('admin.addresses.index', $user->id);
        }, $request);
    }

    /**
     * @param User $user
     * @param Address $address
     * @return \Illuminate\View\View
     */
    public function edit(User $user, Address $address)
    {
        return $this->_edit(function () use ($user, $address) {
            $this->item = $address;
            $this->title = 'Edit Address';
            $this->view = view('admin.auth.users.addresses.edit');
            $this->vars = [
                'user' => $user,
                'countries' => Country::inAlphabeticalOrder()->get(),
                'states' => State::inAlphabeticalOrder()->whereCountry($this->item->country_id)->get(),
                'cities' => City::inAlphabeticalOrder()->where('country_id', $this->item->country_id)->whereState($this->item->state_id)->get(),
            ];
        });
    }

    /**
     * @param AddressRequest $request
     * @param Address $address
     * @param User $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(AddressRequest $request, User $user, Address $address)
    {
        return $this->_update(function () use ($request, $user, $address) {
            $this->item = $address;
            $this->redirect = redirect()->route('admin.addresses.index', $user->id);

            $this->item->update($request->all());
        }, $request);
    }

    /**
     * @param Address $address
     * @param User $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(User $user, Address $address)
    {
        return $this->_destroy(function () use ($user, $address) {
            $this->item = $address;
            $this->redirect = redirect()->route('admin.addresses.index', $user->id);

            $this->item->delete();
        });
    }
}