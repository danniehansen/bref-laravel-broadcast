<?php

namespace BrefLaravelBroadcast\Providers;

use BrefLaravelBroadcast\Broadcasters\BrefBroadcaster;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\ServiceProvider;

class BroadcastServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../Migrations');

        Broadcast::extend('bref', function () {
            return new BrefBroadcaster();
        });
    }
}
