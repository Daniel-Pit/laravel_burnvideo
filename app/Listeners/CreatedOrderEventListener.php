<?php

namespace burnvideo\Listeners;

use burnvideo\Events\CreatedOrderEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Artisan;
use Log;

class CreatedOrderEventListener implements ShouldQueue
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
     * @param  CreatedOrderEvent  $event
     * @return void
     */
    public function handle(CreatedOrderEvent $event)
    {
        $orderId = $event->orderId;
        Artisan::call('order:awsConvert', [
            'orderId' => $orderId, '--queue' => 'default'
        ]);
        //
    }
}
