<?php

namespace App\Http\Controllers\Admin\Location;

use App\Http\Controllers\Controller;
use App\Http\Filters\Location\CityFilter;
use App\Http\Requests\Location\CityRequest;
use App\Http\Sorts\Location\CitySort;
use App\Models\Location\City;
use App\Models\Location\Country;
use App\Models\Location\State;
use App\Traits\CanCrud;
use Illuminate\Http\Request;

class CitiesController extends Controller
{
    use CanCrud;

    /**
     * @var string
     */
    protected $model = City::class;

    /**
     * @param Request $request
     * @param CityFilter $filter
     * @param CitySort $sort
     * @return \Illuminate\View\View
     */
    public function index(Request $request, CityFilter $filter, CitySort $sort)
    {
        return $this->_index(function () use ($request, $filter, $sort) {
            $this->items = City::with(['country', 'state'])->filtered($request, $filter)->sorted($request, $sort)->paginate(10);
            $this->title = 'Cities';
            $this->view = view('admin.location.cities.index');
            $this->vars = [
                'countries' => Country::inAlphabeticalOrder()->get(),
            ];
        });
    }

    /**
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return $this->_create(function () {
            $this->title = 'Add City';
            $this->view = view('admin.location.cities.add');
            $this->vars = [
                'countries' => Country::inAlphabeticalOrder()->get(),
            ];
        });
    }

    /**
     * @param CityRequest $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function store(CityRequest $request)
    {
        return $this->_store(function () use ($request) {
            $this->item = City::create($request->all());
            $this->redirect = redirect()->route('admin.cities.index');
        }, $request);
    }

    /**
     * @param City $city
     * @return \Illuminate\View\View
     */
    public function edit(City $city)
    {
        return $this->_edit(function () use ($city) {
            $this->item = $city;
            $this->title = 'Edit City';
            $this->view = view('admin.location.cities.edit');
            $this->vars = [
                'countries' => Country::inAlphabeticalOrder()->get(),
                'states' => State::where('country_id', $this->item->country_id)->get(),
            ];
        });
    }

    /**
     * @param CityRequest $request
     * @param City $city
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function update(CityRequest $request, City $city)
    {
        return $this->_update(function () use ($request, $city) {
            $this->item = $city;
            $this->redirect = redirect()->route('admin.cities.index');

            $this->item->update($request->all());
        }, $request);
    }

    /**
     * @param City $city
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function destroy(City $city)
    {
        return $this->_destroy(function () use ($city) {
            $this->item = $city;
            $this->redirect = redirect()->route('admin.cities.index');

            $this->item->delete();
        });
    }

    /**
     * @param Request $request
     * @param Country|null $country
     * @param State|null $state
     * @return array
     */
    public function get(Request $request, Country $country = null, State $state = null)
    {
        if (!$request->ajax()) {
            return response()->json([
                'error' => 'Bad request'
            ], 400);
        }

        $query = City::inAlphabeticalOrder();

        if ($country && $country->exists) {
            $query->where('country_id', $country->id);
        }

        if ($country && $country->exists) {
            $query->where('state_id', $state->id);
        }

        $cities = [];

        foreach ($query->get() as $index => $city) {
            $cities[] = [
                'id' => $city->id,
                'name' => $city->name,
            ];
        }

        return response()->json([
            'status' => true,
            'cities' => $cities,
        ]);
    }
}