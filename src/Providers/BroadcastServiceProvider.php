<?php

namespace BrefLaravelBroadcast\Providers;

use BrefLaravelBroadcast\Broadcasters\BrefBroadcaster;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\ServiceProvider;

class BroadcastServiceProvider extends ServiceProvider
{
    public function register()
    {
        parent::register();

        $this->mergeConfigFrom(
            __DIR__ . '/../../config.php',
            'bref_laravel_broadcast'
        );
    }

    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/../../config.php' => config_path('bref_laravel_broadcast.php'),
        ], 'config');

        $this->publishes([
            __DIR__ . '/../Migrations/' => database_path('/migrations')
        ], 'migrations');

        Broadcast::extend('bref', function () {
            return new BrefBroadcaster();
        });
    }
}
