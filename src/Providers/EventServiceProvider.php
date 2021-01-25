<?php

namespace BrefLaravelBroadcast\Providers;

use BrefLaravelBroadcast\Events\BroadcastReceive;
use BrefLaravelBroadcast\Listeners\BroadcastReceiveListener;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        BroadcastReceive::class => [
            BroadcastReceiveListener::class,
        ],
    ];
}
