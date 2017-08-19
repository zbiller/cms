<?php

namespace App\Console\Commands;

use App\Models\Shop\Currency;
use Exchanger\Exception\ChainException;
use Illuminate\Console\Command;
use Swap;

class CurrencyUpdateCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'shop:currency-update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update all currency exchange rates inside the "currencies" database table, using an external service (Fixer, Yahoo, etc.)';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        set_time_limit(0);

        $defaultCurrency = config('shop.price.default_currency');
        $count = 0;

        foreach (Currency::all() as $currency) {
            try {
                $value = Swap::latest("{$currency->code}/{$defaultCurrency}")->getValue();
                $currency->exchange_rate = $value;

                $currency->save();
                $count++;

                $this->info("{$currency->code}/{$defaultCurrency} currency exchange rate updated. Exchange rate = " . number_format($value, 4));
            } catch (ChainException $e) {
                $this->info("{$currency->code}/{$defaultCurrency} currency exchange rate update failed. The {$currency->code} is obsolete.");
            }
        }

        $this->info("{$count} currency exchange rates have been updated.");
    }
}