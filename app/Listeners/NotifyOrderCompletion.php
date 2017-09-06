<?php

namespace App\Listeners;

use App\Events\OrderCompleted;
use App\Mail\OrderCompleted as OrderCompletedMail;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Mail;

class NotifyOrderCompletion implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     *
     * @param OrderCompleted $event
     * @return void
     */
    public function handle(OrderCompleted $event)
    {
        if (isset($event->order->customer['email'])) {
            Mail::to($event->order->customer['email'])->queue(
                new OrderCompletedMail('order-completed', $event->order)
            );
        }
    }
}
