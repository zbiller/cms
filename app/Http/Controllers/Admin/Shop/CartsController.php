<?php

namespace App\Http\Controllers\Admin\Shop;

use Exception;
use App\Http\Controllers\Controller;
use App\Models\Auth\User;
use App\Models\Shop\Cart;
use App\Traits\CanCrud;
use App\Http\Filters\CartFilter;
use App\Http\Sorts\CartSort;
use Illuminate\Http\Request;

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

            if ($request->has('total_from')) {
                $query->having('total', '>=', (float)$request->get('total_from'));
                $paginate = false;
            }

            if ($request->has('total_to')) {
                $query->having('total', '<=', (float)$request->get('total_to'));
                $paginate = false;
            }

            if ($request->has('count_from')) {
                $query->having('count', '>=', (int)$request->get('count_from'));
                $paginate = false;
            }

            if ($request->has('count_to')) {
                $query->having('count', '<=', (int)$request->get('count_to'));
                $paginate = false;
            }

            if ($request->has('user') && $request->get('user') == 'guests_only') {
                $query->whereNull('user_id');
                $request = new Request($request->except('user'));
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
                'items' => $this->item->items()->get(),
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

    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    public function remind()
    {
        try {
            Cart::sendReminders();

            flash()->success('The reminders have been successfully sent!');
        } catch (Exception $e) {
            flash()->error('Could not send the reminders to every user! Please try again.');
        }

        return back();
    }
}