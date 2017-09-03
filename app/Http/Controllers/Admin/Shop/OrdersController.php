<?php

namespace App\Http\Controllers\Admin\Shop;

use App\Http\Controllers\Controller;
use App\Http\Filters\Shop\OrderFilter;
use App\Http\Requests\Shop\OrderRequest;
use App\Http\Sorts\Shop\OrderSort;
use App\Models\Shop\Order;
use App\Models\Shop\Product;
use App\Traits\CanCrud;
use Exception;
use Illuminate\Http\Request;

class OrdersController extends Controller
{
    use CanCrud;

    /**
     * @var string
     */
    protected $model = Order::class;

    /**
     * @param Request $request
     * @param OrderFilter $filter
     * @param OrderSort $sort
     * @return \Illuminate\View\View
     */
    public function index(Request $request, OrderFilter $filter, OrderSort $sort)
    {
        return $this->_index(function () use ($request, $filter, $sort) {
            $query = Order::filtered($request, $filter);

            if ($request->filled('sort')) {
                $query->sorted($request, $sort);
            } else {
                $query->latest();
            }

            $this->items = $query->paginate(config('crud.per_page'));
            $this->title = 'Orders';
            $this->view = view('admin.shop.orders.index');
            $this->vars = [
                'statuses' => Order::$statuses,
                'views' => Order::$views,
                'payments' => Order::$payments,
                'shippings' => Order::$shippings,
            ];
        });
    }

    /**
     * @param Order $order
     * @return \Illuminate\View\View
     */
    public function view(Order $order)
    {
        if ($order->viewed == Order::VIEWED_NO) {
            $order->viewed = Order::VIEWED_YES;
            $order->save();
        }

        return $this->_edit(function () use ($order) {
            $this->item = $order;
            $this->title = 'View Order';
            $this->view = view('admin.shop.orders.view');
            $this->vars = [
                'items' => $order->items,
                'products' => Product::inAlphabeticalOrder()->get(),
                'statuses' => Order::$statuses,
                'views' => Order::$views,
                'payments' => Order::$payments,
                'shippings' => Order::$shippings,
            ];
        });
    }

    /**
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return $this->_create(function () {
            $this->title = 'Add Order';
            $this->view = view('admin.shop.orders.add');
            $this->vars = [
                'items' => collect(),
                'products' => Product::inAlphabeticalOrder()->get(),
                'statuses' => Order::$statuses,
                'views' => Order::$views,
                'payments' => Order::$payments,
                'shippings' => Order::$shippings,
            ];
        });
    }

    /**
     * @param OrderRequest $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function store(OrderRequest $request)
    {
        return $this->_store(function () use ($request) {
            $data = [
                'identifier' => $request->input('identifier') ?: null,
                'payment' => $request->input('payment') ?: null,
                'shipping' => $request->input('shipping') ?: null,
                'status' => $request->input('status') ?: null,
            ];

            $customer = $request->input('customer');
            $addresses = $request->input('addresses');
            $items = $request->input('items');

            $this->item = Order::createOrder($data, $customer, $addresses, $items);
            $this->redirect = redirect()->route('admin.orders.index');
        }, $request);
    }

    /**
     * @param Order $order
     * @return \Illuminate\View\View
     */
    public function edit(Order $order)
    {
        if ($order->viewed == Order::VIEWED_NO) {
            $order->viewed = Order::VIEWED_YES;
            $order->save();
        }

        return $this->_edit(function () use ($order) {
            $this->item = $order;
            $this->title = 'Edit Order';
            $this->view = view('admin.shop.orders.edit');
            $this->vars = [
                'items' => $order->items,
                'products' => Product::inAlphabeticalOrder()->get(),
                'statuses' => Order::$statuses,
                'views' => Order::$views,
                'payments' => Order::$payments,
                'shippings' => Order::$shippings,
            ];
        });
    }

    /**
     * @param OrderRequest $request
     * @param Order $order
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function update(OrderRequest $request, Order $order)
    {
        return $this->_update(function () use ($request, $order) {
            $this->item = $order;
            $this->redirect = redirect()->route('admin.orders.index');

            $data = [
                'identifier' => $request->input('identifier') ?: null,
                'payment' => $request->input('payment') ?: null,
                'shipping' => $request->input('shipping') ?: null,
                'status' => $request->input('status') ?: null,
            ];

            $customer = $request->input('customer');
            $addresses = $request->input('addresses');
            $items = $request->input('items');

            Order::updateOrder($order->id, $data, $customer, $addresses, $items);
        }, $request);
    }

    /**
     * @param Order $order
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function destroy(Order $order)
    {
        return $this->_destroy(function () use ($order) {
            $this->item = $order;
            $this->redirect = redirect()->route('admin.orders.index');

            $this->item->delete();
        });
    }

    /**
     * @param Request $request
     * @param OrderFilter $filter
     * @param OrderSort $sort
     * @return \Illuminate\View\View
     */
    public function deleted(Request $request, OrderFilter $filter, OrderSort $sort)
    {
        return $this->_deleted(function () use ($request, $filter, $sort) {
            $this->items = Order::onlyTrashed()->filtered($request, $filter)->sorted($request, $sort)->paginate(config('crud.per_page'));
            $this->title = 'Deleted Orders';
            $this->view = view('admin.shop.orders.deleted');
            $this->vars = [
                'statuses' => Order::$statuses,
                'views' => Order::$views,
                'payments' => Order::$payments,
                'shippings' => Order::$shippings,
            ];
        });
    }

    /**
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function restore($id)
    {
        return $this->_restore(function () use ($id) {
            $this->item = Order::onlyTrashed()->findOrFail($id);
            $this->redirect = redirect()->route('admin.orders.deleted');

            $this->item->restore();
        });
    }

    /**
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function delete($id)
    {
        return $this->_delete(function () use ($id) {
            $this->item = Order::onlyTrashed()->findOrFail($id);
            $this->redirect = redirect()->route('admin.orders.deleted');

            $this->item->forceDelete();
        });
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function loadItem(Request $request)
    {
        if (!$request->ajax()) {
            return response()->json([
                'error' => 'Bad request'
            ], 400);
        }

        $request->validate([
            'product_id' => 'required|numeric',
            'quantity' => 'required|numeric',
        ]);

        try {
            $product = Product::findOrFail($request->input('product_id'));
            $quantity = $request->input('quantity');

            return response()->json([
                'status' => true,
                'data' => [
                    'id' => $product->id,
                    'name' => $product->name,
                    'quantity' => $quantity,
                    'currency' => $product->currency->code,
                    'raw_price' => $product->price,
                    'sub_price' => $product->price_with_discounts,
                    'grand_price' => $product->final_price,
                    'price_formatted' => number_format($product->final_price, 2),
                    'raw_total' => $product->price * $quantity,
                    'sub_total' => $product->price_with_discounts * $quantity,
                    'grand_total' => $product->final_price * $quantity,
                    'total_formatted' => number_format($product->final_price * $quantity, 2),
                    'product_url' => route('admin.products.edit', $product->id),
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