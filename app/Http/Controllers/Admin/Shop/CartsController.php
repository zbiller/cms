<?php

namespace App\Http\Controllers\Admin\Shop;

use App\Models\Auth\Activity;
use DB;
use Exception;
use App\Http\Controllers\Controller;
use App\Models\Auth\User;
use App\Models\Shop\Cart;
use App\Traits\CanCrud;
use App\Http\Filters\CartFilter;
use App\Http\Sorts\CartSort;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;

class CartsController extends Controller
{
    use CanCrud;

    /**
     * @var string
     */
    protected $model = Cart::class;

    /**
     * @param Request $request
     * @param CartFilter $filter
     * @param CartSort $sort
     * @return \Illuminate\View\View
     */
    public function index(Request $request, CartFilter $filter, CartSort $sort)
    {
        return $this->_index(function () use ($request, $filter, $sort) {
            $query = Cart::query();
            $paginate = true;

            if (($totalFrom = $request->get('total')[0]) && $totalFrom > 0) {
                $query->having('total', '>=', $totalFrom);
                $paginate = false;
            }

            if (($totalTo = $request->get('total')[1]) && $totalTo > 0) {
                $query->having('total', '<=', $totalTo);
                $paginate = false;
            }

            if (($countFrom = $request->get('count')[0]) && $countFrom > 0) {
                $query->having('count', '>=', $countFrom);
                $paginate = false;
            }

            if (($countTo = $request->get('count')[1]) && $countTo > 0) {
                $query->having('count', '<=', $countTo);
                $paginate = false;
            }

            $query->filtered($request, $filter)->sorted($request, $sort);

            $this->items = $paginate ? $query->paginate(10) : $query->get();
            $this->title = 'Carts';
            $this->view = view('admin.shop.carts.index');
            $this->vars = [
                'users' => User::alphabetically()->get(),
                'paginate' => $paginate,
            ];
        });
    }

    /**
     * @param Cart $cart
     * @return \Illuminate\View\View
     */
    public function view(Cart $cart)
    {
        return $this->_edit(function () use ($cart) {
            $this->item = $cart;
            $this->title = 'Edit Cart';
            $this->view = view('admin.shop.carts.view');
            $this->vars = [
                'users' => User::alphabetically()->get(),
                'items' => $this->item->items()->with('product')->get(),
            ];
        });
    }

    /**
     * @param Cart $cart
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function destroy(Cart $cart)
    {
        return $this->_destroy(function () use ($cart) {
            $this->item = $cart;
            $this->redirect = redirect()->route('admin.carts.index');

            $this->item->delete();
        });
    }

    /**
     * @return \Illuminate\Http\RedirectResponse
     * @throws Exception
     */
    public function clean()
    {
        try {
            Cart::cleanOld();

            flash()->success('The records were successfully cleaned up!');
        } catch (Exception $e) {
            flash()->error('Could not clean up the records! Please try again.');
        }

        return back();
    }
}