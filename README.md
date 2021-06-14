# Bref laravel broadcast driver
[![codecov](https://codecov.io/gh/danniehansen/bref-laravel-broadcast/branch/master/graph/badge.svg)](https://codecov.io/gh/danniehansen/bref-laravel-broadcast)
![](https://github.com/danniehansen/bref-laravel-broadcast/workflows/Test%20workflow/badge.svg?branch=master)

## Introduction

This package implements the `bref` broadcast driver & allows you to utilize [bref.sh](https://bref.sh/) to build a serverless Websocket implementation with API Gateway Websockets & Lambda.

## Disclaimer
Private channels is NOT yet implemented. Please do not use this functionality just yet - only public ones.

## Installation

```bash
composer install danniehansen/bref-laravel-websocket
```

Package heaverly relies on [bref.sh](https://bref.sh/) & thus implemented using the [typed events](https://bref.sh/docs/function/handlers.html#websocket-events) built right in.

To get started first create a handler php file. Example (app/Handlers/Broadcast.php):

```php
<?php

require __DIR__ . '/../../vendor/autoload.php';

use BrefLaravelBroadcast\Handlers\Websocket;
use Illuminate\Foundation\Application;

/** @var Application $app */
$app = require __DIR__ . '/../../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

return $app->makeWith(Websocket::class);
```

After this you'll need to update your serverless.yml with a new websocket event that uses the new handler. Here is a full example of a serverless.yml with the handler:

```yml
service: ....

provider:
    name: aws
    region: ....
    runtime: provided.al2

plugins:
    - ./vendor/bref/bref

functions:
    websocket:
        handler: app/BrefHandlers/Broadcast.php
        layers:
            - ${bref:layer.php-74}
        events:
            - websocket: $connect
            - websocket: $disconnect
            - websocket: $default
```

All that is left now is deciding on where to store your broadcast listeners. If you're already using a database connection with your laravel installation - then you'd be able to utilize this right off the bat. To get started you need to publish the migration files:

```bash
php artisan vendor:publish --provider="BrefLaravelBroadcast\Providers\BroadcastServiceProvider" --tag=public --force
```

After this run ``php artisan migrate`` & add the driver in ``config/broadcasting.php`` under ``connections``:

```php
    'connections' => [
        'bref' => [
            'driver' => 'bref',
        ],
        
        ....
    ],
```

To finish it all off add ``BROADCAST_DRIVER=bref`` to your ``.env`` & you're all set!
