<?php

namespace BrefLaravelBroadcast\Tests;

use BrefLaravelBroadcast\Events\BroadcastReceive;
use BrefLaravelBroadcast\Models\BroadcastListener;

class ListenerTest extends BaseCase
{
    public function testChannelListen(): void
    {
        self::assertEquals(
            0,
            BroadcastListener::query()->count()
        );

        event(
            new BroadcastReceive(
                'MESSAGE',
                'xyz-connectionId-1',
                'xyz-apiId',
                'eu-west-1',
                'dev',
                [
                    'listen_channel' => 'test',
                ]
            )
        );

        self::assertEquals(
            1,
            BroadcastListener::query()->count()
        );

        event(
            new BroadcastReceive(
                'MESSAGE',
                'xyz-connectionId-2',
                'xyz-apiId',
                'eu-west-1',
                'dev',
                [
                    'listen_channel' => 'test',
                ]
            )
        );

        self::assertEquals(
            2,
            BroadcastListener::query()->count()
        );
    }
}
