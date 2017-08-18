<?php

namespace App\Listeners;

use App\Events\CartReminded;
use App\Mail\CartReminder;
use App\Models\Shop\Cart;
use Carbon\Carbon;
use Exception;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Mail;

class SendCartReminders implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * @param CartReminded $event
     * @return void
     * @throws Exception
     */
    public function handle(CartReminded $event)
    {
        set_time_limit(0);

        $days = (int)config('shop.cart.remind_only_older_than');

        try {
            $date = Carbon::now()->subDays($days)->format('Y-m-d H:i:s');

            foreach (Cart::onlyUsers()->where('carts.created_at', '<', $date)->get() as $cart) {
                Mail::to($cart->user)->queue(
                    new CartReminder('cart-reminder', $cart->user, $cart)
                );
            }
        } catch (Exception $e) {
            throw $e;
        }
    }
}
