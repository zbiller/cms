<?php

namespace App\Http\Controllers\Admin\Localisation;

use App\Http\Controllers\Controller;
use App\Http\Filters\Localisation\CurrencyFilter;
use App\Http\Requests\Localisation\CurrencyRequest;
use App\Http\Sorts\Localisation\CurrencySort;
use App\Models\Localisation\Currency;
use App\Traits\CanCrud;
use Exception;
use Illuminate\Http\Request;

class CurrenciesController extends Controller
{
    use CanCrud;

    /**
     * @var string
     */
    protected $model = Currency::class;

    /**
     * @param Request $request
     * @param CurrencyFilter $filter
     * @param CurrencySort $sort
     * @return \Illuminate\View\View
     */
    public function index(Request $request, CurrencyFilter $filter, CurrencySort $sort)
    {
        return $this->_index(function () use ($request, $filter, $sort) {
            $this->items = Currency::filtered($request, $filter)->sorted($request, $sort)->paginate(config('crud.per_page'));
            $this->title = 'Currencies';
            $this->view = view('admin.shop.currencies.index');
        });
    }

    /**
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return $this->_create(function () {
            $this->title = 'Add Currency';
            $this->view = view('admin.shop.currencies.add');
        });
    }

    /**
     * @param CurrencyRequest $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function store(CurrencyRequest $request)
    {
        return $this->_store(function () use ($request) {
            $this->item = Currency::create($request->all());
            $this->redirect = redirect()->route('admin.currencies.index');
        }, $request);
    }

    /**
     * @param Currency $currency
     * @return \Illuminate\View\View
     */
    public function edit(Currency $currency)
    {
        return $this->_edit(function () use ($currency) {
            $this->item = $currency;
            $this->title = 'Edit currency';
            $this->view = view('admin.shop.currencies.edit');
        });
    }

    /**
     * @param CurrencyRequest $request
     * @param Currency $currency
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function update(CurrencyRequest $request, Currency $currency)
    {
        return $this->_update(function () use ($request, $currency) {
            $this->item = $currency;
            $this->redirect = redirect()->route('admin.currencies.index');

            $this->item->update($request->all());
        }, $request);
    }

    /**
     * @param Currency $currency
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function destroy(Currency $currency)
    {
        return $this->_destroy(function () use ($currency) {
            $this->item = $currency;
            $this->redirect = redirect()->route('admin.currencies.index');

            $this->item->delete();
        });
    }

    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    public function exchange()
    {
        try {
            Currency::updateExchangeRates();

            flash()->success('The exchange rates for all of the currencies have been successfully updated!');
        } catch (Exception $e) {
            flash()->error('Could not update the exchange rates! Please try again.');
        }

        return back();
    }
}