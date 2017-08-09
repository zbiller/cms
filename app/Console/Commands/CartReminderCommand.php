<?php

namespace App\Console\Commands;

use App\Mail\UserCartReminder;
use App\Models\Shop\Cart;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Mail;

class CartReminderCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'shop:cart-reminder';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send email reminders to every user that has an ongoing old cart.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        set_time_limit(0);

        $days = (int)config('shop.cart.remind_only_older_than');

        $date = Carbon::now()->subDays($days)->format('Y-m-d H:i:s');
        $count = 0;

        foreach (Cart::onlyUsers()->where('carts.created_at', '<', $date)->get() as $cart) {
            $this->info("Sending ongoing shopping cart reminder to: {$cart->user->email}");

            Mail::to($cart->user)->queue(
                new UserCartReminder('cart-reminder', $cart->user, $cart)
            );

            $count++;
        }


        $this->info("{$count} users have been reminded of their ongoing shopping carts via email.");
    }
}
