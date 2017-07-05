<?php

namespace App\Http\Controllers\Admin\Location;

use App\Http\Controllers\Controller;
use App\Models\Location\Country;
use App\Traits\CanCrud;
use App\Http\Requests\CountryRequest;
use App\Http\Filters\CountryFilter;
use App\Http\Sorts\CountrySort;
use Illuminate\Http\Request;

class CountriesController extends Controller
{
    use CanCrud;

    /**
     * @var string
     */
    protected $model = Country::class;

    /**
     * @param Request $request
     * @param CountryFilter $filter
     * @param CountrySort $sort
     * @return \Illuminate\View\View
     */
    public function index(Request $request, CountryFilter $filter, CountrySort $sort)
    {
        return $this->_index(function () use ($request, $filter, $sort) {
            $this->items = Country::filtered($request, $filter)->sorted($request, $sort)->paginate(10);
            $this->title = 'Countries';
            $this->view = view('admin.location.countries.index');
        });
    }

    /**
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return $this->_create(function () {
            $this->title = 'Add Country';
            $this->view = view('admin.location.countries.add');
        });
    }

    /**
     * @param CountryRequest $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function store(CountryRequest $request)
    {
        return $this->_store(function () use ($request) {
            $this->item = Country::create($request->all());
            $this->redirect = redirect()->route('admin.countries.index');
        }, $request);
    }

    /**
     * @param Country $country
     * @return \Illuminate\View\View
     */
    public function edit(Country $country)
    {
        return $this->_edit(function () use ($country) {
            $this->item = $country;
            $this->title = 'Edit Country';
            $this->view = view('admin.location.countries.edit');
        });
    }

    /**
     * @param CountryRequest $request
     * @param Country $country
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function update(CountryRequest $request, Country $country)
    {
        return $this->_update(function () use ($request, $country) {
            $this->item = $country;
            $this->redirect = redirect()->route('admin.countries.index');

            $this->item->update($request->all());
        }, $request);
    }

    /**
     * @param Country $country
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function destroy(Country $country)
    {
        return $this->_destroy(function () use ($country) {
            $this->item = $country;
            $this->redirect = redirect()->route('admin.countries.index');

            $this->item->delete();
        });
    }
}