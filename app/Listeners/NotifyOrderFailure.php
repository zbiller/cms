<?php

namespace App\Listeners;

use App\Events\OrderFailed;
use App\Mail\OrderFailed as OrderFailedMail;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Mail;

class NotifyOrderFailure implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     *
     * @param OrderFailed $event
     * @return void
     */
    public function handle(OrderFailed $event)
    {
        if (isset($event->order->customer->email)) {
            Mail::to($event->order->customer->email)->queue(
                new OrderFailedMail('order-failed', $event->order)
            );
        }
    }
}
