<?php

namespace App\Http\Controllers\Admin\Shop;

use Exception;
use App\Http\Controllers\Controller;
use App\Models\Shop\Discount;
use App\Traits\CanCrud;
use App\Http\Requests\DiscountRequest;
use App\Http\Filters\DiscountFilter;
use App\Http\Sorts\DiscountSort;
use Illuminate\Http\Request;

class DiscountsController extends Controller
{
    use CanCrud;

    /**
     * @var string
     */
    protected $model = Discount::class;

    /**
     * @param Request $request
     * @param DiscountFilter $filter
     * @param DiscountSort $sort
     * @return \Illuminate\View\View
     */
    public function index(Request $request, DiscountFilter $filter, DiscountSort $sort)
    {
        return $this->_index(function () use ($request, $filter, $sort) {
            $this->items = Discount::filtered($request, $filter)->sorted($request, $sort)->paginate(10);
            $this->title = 'Discounts';
            $this->view = view('admin.shop.discounts.index');
            $this->vars = [
                'actives' => Discount::$actives,
                'types' => Discount::$types,
                'for' => Discount::$for,
            ];
        });
    }

    /**
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return $this->_create(function () {
            $this->title = 'Add Discount';
            $this->view = view('admin.shop.discounts.add');
            $this->vars = [
                'actives' => Discount::$actives,
                'types' => Discount::$types,
                'for' => Discount::$for,
            ];
        });
    }

    /**
     * @param DiscountRequest $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function store(DiscountRequest $request)
    {
        return $this->_store(function () use ($request) {
            $this->item = Discount::create($request->all());
            $this->redirect = redirect()->route('admin.discounts.index');
        }, $request);
    }

    /**
     * @param Discount $discount
     * @return \Illuminate\View\View
     */
    public function edit(Discount $discount)
    {
        return $this->_edit(function () use ($discount) {
            $this->item = $discount;
            $this->title = 'Edit Discount';
            $this->view = view('admin.shop.discounts.edit');
            $this->vars = [
                'actives' => Discount::$actives,
                'types' => Discount::$types,
                'for' => Discount::$for,
            ];
        });
    }

    /**
     * @param DiscountRequest $request
     * @param Discount $discount
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function update(DiscountRequest $request, Discount $discount)
    {
        return $this->_update(function () use ($request, $discount) {
            $this->item = $discount;
            $this->redirect = redirect()->route('admin.discounts.index');

            $this->item->update($request->all());
        }, $request);
    }

    /**
     * @param Discount $discount
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function destroy(Discount $discount)
    {
        return $this->_destroy(function () use ($discount) {
            $this->item = $discount;
            $this->redirect = redirect()->route('admin.discounts.index');

            $this->item->delete();
        });
    }
}