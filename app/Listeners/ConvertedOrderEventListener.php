<?php

namespace burnvideo\Listeners;

use burnvideo\Events\ConvertedOrderEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Artisan;

class ConvertedOrderEventListener implements ShouldQueue
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  ConvertedOrderEvent  $event
     * @return void
     */
    public function handle(ConvertedOrderEvent $event)
    {
        //
        $orderId = $event->orderId;
        $status = $event->status;

        Artisan::call('order:awsCompleteOrder', [
            'orderId' => $orderId,
            'status' => $status,
            '--queue' => 'default'
        ]);

    }
}
