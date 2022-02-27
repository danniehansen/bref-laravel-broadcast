# Bref laravel broadcast driver
[![codecov](https://codecov.io/gh/danniehansen/bref-laravel-broadcast/branch/master/graph/badge.svg)](https://codecov.io/gh/danniehansen/bref-laravel-broadcast)
![](https://github.com/danniehansen/bref-laravel-broadcast/workflows/Test%20workflow/badge.svg?branch=master)

# NOT YET PRODUCTION READY

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
php artisan vendor:publish --provider="BrefLaravelBroadcast\Providers\BroadcastServiceProvider" --tag=migrations --force
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

---

## Custom model

You're also able to supply your own custom model. This enables for some interesting implementations. Such as this DynamoDB implementation:

First publish the package config.

```bash
php artisan vendor:publish --provider="BrefLaravelBroadcast\Providers\BroadcastServiceProvider" --tag=config --force
```

Afterward change ``config/bref_laravel_broadcast.php`` to:

```php
<?php

return [
    'model' => \App\Models\BroadcastListener::class,
];
```

Then create the file ``app/Models/BroadcastListener.php`` with the following content:

```php
<?php

namespace App\Models;

use BaoPham\DynamoDb\DynamoDbModel;
use Illuminate\Support\Str;

/**
 * Class BroadcastListener
 * @package App\Models
 *
 * @property string $listener_id
 * @property string $channel
 * @property string $connection_id
 * @property string $api_id
 * @property string $region
 * @property string $stage
 */
final class BroadcastListener extends DynamoDbModel
{
    protected $primaryKey = 'listener_id';
    protected $table = 'BroadcastListener';
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'listener_id',
        'channel',
        'connection_id',
        'api_id',
        'region',
        'stage',
    ];

    /**
     * @param string $channel
     * @param string $connectionId
     * @param string $apiId
     * @param string $region
     * @param string $stage
     *
     * @return static
     */
    public static function createListener(
        string $channel,
        string $connectionId,
        string $apiId,
        string $region,
        string $stage
    ): self {
        $row = new self();
        $row->listener_id = Str::uuid()->toString();
        $row->channel = $channel;
        $row->connection_id = $connectionId;
        $row->api_id = $apiId;
        $row->region = $region;
        $row->stage = $stage;
        $row->save();

        return $row;
    }
}
```

This implementation relies on the https://github.com/baopham/laravel-dynamodb package. Go ahead and install it.

Last change we need is a ``serverless.yml`` that is configured with a DynamoDB table for our model:

```yml
service: ....

provider:
    name: aws
    region: ....
    runtime: provided.al2
    iamRoleStatements:
      - Effect: Allow
        Action:
          - dynamodb:Query
          - dynamodb:Scan
          - dynamodb:GetItem
          - dynamodb:PutItem
          - dynamodb:DeleteItem
        Resource: "*"

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

resources:
  Resources:
    BroadcastListener:
      Type: 'AWS::DynamoDB::Table'
      DeletionPolicy: Retain
      Properties:
        AttributeDefinitions:
          - AttributeName: "listener_id"
            AttributeType: "S"
        KeySchema:
          - AttributeName: "listener_id"
            KeyType: "HASH"
        ProvisionedThroughput:
          ReadCapacityUnits: 1
          WriteCapacityUnits: 1
        StreamSpecification:
          StreamViewType: "NEW_AND_OLD_IMAGES"
        TableName: "BroadcastListener"
```

& you're done! enjoy serverless webosckets powered by Api Gateway, Lambda & DynamoDB!
