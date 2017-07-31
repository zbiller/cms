<?php

namespace App\Http\Controllers\Admin\Shop;

use App\Http\Controllers\Controller;
use App\Models\Shop\Discount;
use App\Models\Shop\Product;
use App\Models\Version\Draft;
use App\Models\Version\Revision;
use App\Traits\CanCrud;
use App\Http\Requests\DiscountRequest;
use App\Http\Filters\DiscountFilter;
use App\Http\Sorts\DiscountSort;
use DB;
use Exception;
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

    /**
     * @param Request $request
     * @return array
     */
    public function get(Request $request)
    {
        $this->validate($request, [
            'product_id' => 'required|numeric',
        ]);

        try {
            $id = $request->get('product_id');
            $product = Product::withoutGlobalScopes()->findOrFail($id);

            if ($request->has('draft') && ($draft = Draft::find((int)$request->get('draft')))) {
                DB::beginTransaction();

                $product = $draft->draftable;
                $product->publishDraft($draft);
            }

            if ($request->has('revision') && ($revision = Revision::find((int)$request->get('revision')))) {
                DB::beginTransaction();

                $product = $revision->revisionable;
                $product->rollbackToRevision($revision);
            }

            return [
                'status' => true,
                'html' => view('admin.shop.discounts.assign.discounts')->with([
                    'product' => $product,
                    'discounts' => Discount::alphabetically()->active()->forProduct()->get(),
                    'draft' => isset($draft) ? $draft : null,
                    'revision' => isset($revision) ? $revision : null,
                    'disabled' => $request->get('disabled') ? true : false,
                ])->render(),
            ];
        } catch (Exception $e) {
            dd($e);
            return [
                'status' => false
            ];
        }
    }

    /**
     * @param Request $request
     * @return array
     */
    public function row(Request $request)
    {
        $this->validate($request, [
            'discount_id' => 'required|numeric',
        ]);

        try {
            $discount = Discount::findOrFail($request->get('discount_id'));

            return [
                'status' => true,
                'data' => [
                    'id' => $discount->id,
                    'name' => $discount->name ?: 'N/A',
                    'rate' => $discount->rate ?: 'N/A',
                    'type' => isset(Discount::$types[$discount->type]) ? Discount::$types[$discount->type] : 'N/A',
                    'url' => route('admin.discounts.edit', $discount->id),
                ],
            ];
        } catch (Exception $e) {
            return [
                'status' => false
            ];
        }
    }
}