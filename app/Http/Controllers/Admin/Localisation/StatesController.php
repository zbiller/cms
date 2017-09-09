<?php

namespace App\Http\Controllers\Admin\Localisation;

use App\Http\Controllers\Controller;
use App\Http\Filters\Localisation\StateFilter;
use App\Http\Requests\Localisation\StateRequest;
use App\Http\Sorts\Localisation\StateSort;
use App\Models\Localisation\Country;
use App\Models\Localisation\State;
use App\Traits\CanCrud;
use Illuminate\Http\Request;

class StatesController extends Controller
{
    use CanCrud;

    /**
     * @var string
     */
    protected $model = State::class;

    /**
     * @param Request $request
     * @param StateFilter $filter
     * @param StateSort $sort
     * @return \Illuminate\View\View
     */
    public function index(Request $request, StateFilter $filter, StateSort $sort)
    {
        return $this->_index(function () use ($request, $filter, $sort) {
            $this->items = State::with('country')->filtered($request, $filter)->sorted($request, $sort)->paginate(config('crud.per_page'));
            $this->title = 'States';
            $this->view = view('admin.localisation.states.index');
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
            $this->title = 'Add State';
            $this->view = view('admin.localisation.states.add');
            $this->vars = [
                'countries' => Country::inAlphabeticalOrder()->get(),
            ];
        });
    }

    /**
     * @param StateRequest $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function store(StateRequest $request)
    {
        return $this->_store(function () use ($request) {
            $this->item = State::create($request->all());
            $this->redirect = redirect()->route('admin.states.index');
        }, $request);
    }

    /**
     * @param State $state
     * @return \Illuminate\View\View
     */
    public function edit(State $state)
    {
        return $this->_edit(function () use ($state) {
            $this->item = $state;
            $this->title = 'Edit State';
            $this->view = view('admin.localisation.states.edit');
            $this->vars = [
                'countries' => Country::inAlphabeticalOrder()->get(),
            ];
        });
    }

    /**
     * @param StateRequest $request
     * @param State $state
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function update(StateRequest $request, State $state)
    {
        return $this->_update(function () use ($request, $state) {
            $this->item = $state;
            $this->redirect = redirect()->route('admin.states.index');

            $this->item->update($request->all());
        }, $request);
    }

    /**
     * @param State $state
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function destroy(State $state)
    {
        return $this->_destroy(function () use ($state) {
            $this->item = $state;
            $this->redirect = redirect()->route('admin.states.index');

            $this->item->delete();
        });
    }

    /**
     * @param Request $request
     * @param Country|null $country
     * @return array
     */
    public function get(Request $request, Country $country = null)
    {
        if (!$request->ajax()) {
            return response()->json([
                'error' => 'Bad request'
            ], 400);
        }

        $query = State::inAlphabeticalOrder();

        if ($country && $country->exists) {
            $query->where('country_id', $country->id);
        }

        $states = $cities = [];

        foreach ($query->get() as $index => $state) {
            $states[] = [
                'id' => $state->id,
                'name' => $state->name,
                'code' => $state->code,
            ];

            if ($index == 0) {
                foreach ($state->cities as $city) {
                    $cities[] = [
                        'id' => $city->id,
                        'name' => $city->name,
                    ];
                }
            }
        }

        return response()->json([
            'status' => true,
            'states' => $states,
            'cities' => $cities,
        ]);
    }
}