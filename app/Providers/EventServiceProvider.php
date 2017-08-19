<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        'Illuminate\Auth\Events\Login' => [
            'App\Listeners\MigrateUserCart',
        ],
        'App\Events\CartReminded' => [
            'App\Listeners\SendCartReminders',
        ],
        'App\Events\OrderCreated' => [
            'App\Listeners\NotifyOrderCreation',
            'App\Listeners\IncrementProductSales',
        ],
        'App\Events\OrderCompleted' => [
            'App\Listeners\NotifyOrderCompletion',
        ],
        'App\Events\OrderFailed' => [
            'App\Listeners\NotifyOrderFailure',
        ],
        'App\Events\OrderCanceled' => [
            'App\Listeners\NotifyOrderCancellation',
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();
    }
}