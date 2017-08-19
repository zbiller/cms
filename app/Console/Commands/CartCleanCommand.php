<?php

namespace App\Console\Commands;

use App\Models\Shop\Cart;
use Carbon\Carbon;
use DB;
use Illuminate\Console\Command;

class CartCleanCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'shop:cart-clean';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up old shopping carts';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        set_time_limit(0);

        $days = (int)config('shop.cart.delete_records_older_than');

        if (!($days > 0)) {
            $this->info("Could not clean up shopping carts because the key \"cart.delete_records_older_than\" is not set in the config/shop.php file.");
            return;
        }

        $number = DB::transaction(function () use ($days) {
            $date = Carbon::now()->subDays($days)->format('Y-m-d H:i:s');
            $number = 0;

            foreach (Cart::where('carts.created_at', '<', $date)->get() as $cart) {
                $cart->delete();
                $number++;
            }

            return $number;
        });

        $this->info("Shopping carts cleaned up. {$number} record(s) were removed.");
    }
}