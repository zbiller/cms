<?php

namespace App\Http\Controllers\Admin\Shop;

use App\Http\Controllers\Controller;
use App\Http\Filters\Shop\TaxFilter;
use App\Http\Requests\Shop\TaxRequest;
use App\Http\Sorts\Shop\TaxSort;
use App\Models\Shop\Tax;
use App\Traits\CanCrud;
use Exception;
use Illuminate\Http\Request;

class TaxesController extends Controller
{
    use CanCrud;

    /**
     * @var string
     */
    protected $model = Tax::class;

    /**
     * @param Request $request
     * @param TaxFilter $filter
     * @param TaxSort $sort
     * @return \Illuminate\View\View
     */
    public function index(Request $request, TaxFilter $filter, TaxSort $sort)
    {
        return $this->_index(function () use ($request, $filter, $sort) {
            $this->items = Tax::filtered($request, $filter)->sorted($request, $sort)->paginate(10);
            $this->title = 'Taxes';
            $this->view = view('admin.shop.taxes.index');
            $this->vars = [
                'actives' => Tax::$actives,
                'types' => Tax::$types,
                'for' => Tax::$for,
            ];
        });
    }

    /**
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return $this->_create(function () {
            $this->title = 'Add Tax';
            $this->view = view('admin.shop.taxes.add');
            $this->vars = [
                'actives' => Tax::$actives,
                'types' => Tax::$types,
                'for' => Tax::$for,
            ];
        });
    }

    /**
     * @param TaxRequest $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function store(TaxRequest $request)
    {
        return $this->_store(function () use ($request) {
            $this->item = Tax::create($request->all());
            $this->redirect = redirect()->route('admin.taxes.index');
        }, $request);
    }

    /**
     * @param Tax $tax
     * @return \Illuminate\View\View
     */
    public function edit(Tax $tax)
    {
        return $this->_edit(function () use ($tax) {
            $this->item = $tax;
            $this->title = 'Edit Tax';
            $this->view = view('admin.shop.taxes.edit');
            $this->vars = [
                'actives' => Tax::$actives,
                'types' => Tax::$types,
                'for' => Tax::$for,
            ];
        });
    }

    /**
     * @param TaxRequest $request
     * @param Tax $tax
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function update(TaxRequest $request, Tax $tax)
    {
        return $this->_update(function () use ($request, $tax) {
            $this->item = $tax;
            $this->redirect = redirect()->route('admin.taxes.index');

            $this->item->update($request->all());
        }, $request);
    }

    /**
     * @param Tax $tax
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function destroy(Tax $tax)
    {
        return $this->_destroy(function () use ($tax) {
            $this->item = $tax;
            $this->redirect = redirect()->route('admin.taxes.index');

            $this->item->delete();
        });
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function load(Request $request)
    {
        if (!$request->ajax()) {
            return response()->json([
                'error' => 'Bad request'
            ], 400);
        }

        $this->validate($request, [
            'tax_id' => 'required|numeric',
        ]);

        try {
            $tax = Tax::findOrFail($request->get('tax_id'));

            return response()->json([
                'status' => true,
                'data' => [
                    'id' => $tax->id,
                    'name' => $tax->name ?: 'N/A',
                    'rate' => $tax->rate ?: 'N/A',
                    'type' => isset(Tax::$types[$tax->type]) ? Tax::$types[$tax->type] : 'N/A',
                    'url' => route('admin.taxes.edit', $tax->id),
                ],
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }
}