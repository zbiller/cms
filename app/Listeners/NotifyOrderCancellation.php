<?php

namespace App\Listeners;

use App\Events\OrderCanceled;
use App\Mail\OrderCanceled as OrderCanceledMail;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Mail;

class NotifyOrderCancellation implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     *
     * @param OrderCanceled $event
     * @return void
     */
    public function handle(OrderCanceled $event)
    {
        if (isset($event->order->customer->email)) {
            Mail::to($event->order->customer->email)->queue(
                new OrderCanceledMail('order-canceled', $event->order)
            );
        }
    }
}
