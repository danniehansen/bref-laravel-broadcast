<?php

namespace BrefLaravelBroadcast\Tests;

use BrefLaravelBroadcast\Providers\BroadcastServiceProvider;
use BrefLaravelBroadcast\Providers\EventServiceProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Orchestra\Testbench\TestCase;

class BaseCase extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $this->loadMigrationsFrom(__DIR__ . '/../src/Migrations');
    }

    protected function getPackageProviders($app)
    {
        return [
            BroadcastServiceProvider::class,
            EventServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('database.default', 'testbench');
        $app['config']->set('database.connections.testbench', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);
    }
}
