<?php

namespace App\Listeners;

use App\Models\Auth\User;
use App\Models\Shop\Cart;
use App\Models\Shop\Cart\Item;
use DB;
use Illuminate\Auth\Events\Login;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class MigrateCartFromGuestToUser implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * @var Cart
     */
    protected $guestCart;

    /**
     * @var Cart
     */
    protected $userCart;

    /**
     * Handle the event.
     *
     * @param  Login  $event
     * @return void
     */
    public function handle(Login $event)
    {
        $this->setGuestCart();

        if ($this->hasGuestCart()) {
            $this->setUserCart($event->user);

            DB::transaction(function () use ($event) {
                if ($this->hasUserCart()) {
                    $this->updateUserCart();
                } else {
                    $this->createUserCart($event->user);
                }

                $this->deleteGuestCart();
            });
        }
    }

    /**
     * Verify if the guest had a cart before authenticating.
     *
     * @return bool
     */
    protected function hasGuestCart()
    {
        return $this->guestCart && $this->guestCart instanceof Cart && $this->guestCart->exists;
    }

    /**
     * Verify if the authenticated user already has a cart entry inside the database.
     *
     * @return bool
     */
    protected function hasUserCart()
    {
        return $this->userCart && $this->userCart instanceof Cart && $this->userCart->exists;
    }

    /**
     * Set the guest cart based on the "cart_user_token" cookie.
     * If the guest has a cart already, this cookie will be present.
     *
     * @return void
     */
    protected function setGuestCart()
    {
        if (isset($_COOKIE['cart_user_token']) && ($token = $_COOKIE['cart_user_token'])) {
            $this->guestCart = Cart::where('user_token', $token)->first();
        }
    }

    /**
     * Set the user cart if one already exists inside the database.
     *
     * @param User $user
     */
    protected function setUserCart(User $user)
    {
        $this->userCart = Cart::where('user_id', $user->id)->first();
    }

    /**
     * Create a fresh cart instance for the just authenticated user.
     *
     * @param User $user
     */
    protected function createUserCart(User $user)
    {
        $this->userCart = $this->guestCart;

        $this->userCart->user_id = $user->id;
        $this->userCart->user_token = null;

        $this->userCart->save();
    }

    /**
     * @return void
     */
    protected function updateUserCart()
    {
        $this->guestCart->items()->each(function ($guestItem) {
            try {
                $userItem = Item::where([
                    'cart_id' => $this->userCart->id,
                    'product_id' => $guestItem->product_id,
                ])->firstOrFail();

                $userItem->quantity += $guestItem->quantity;
                $userItem->save();
            } catch (ModelNotFoundException $e) {
                $this->userCart->items()->save($guestItem);
            }
        });
    }

    /**
     * Remove the old guest's cart from both cookie storage and database.
     *
     * @return void
     */
    protected function deleteGuestCart()
    {
        unset($_COOKIE['cart_user_token']);
        setcookie('cart_user_token', '', time() - 3600, '/');

        $this->guestCart->delete();
    }
}
