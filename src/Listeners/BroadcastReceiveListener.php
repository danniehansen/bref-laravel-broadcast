<?php

namespace BrefLaravelBroadcast\Listeners;

use BrefLaravelBroadcast\Events\BroadcastReceive;

class BroadcastReceiveListener
{
    public function handle(BroadcastReceive $event): void
    {
        $model = config('bref_laravel_broadcast.model');

        echo "receive";

        switch ($event->getType()) {
            case 'DISCONNECT':
                $model::query()
                    ->where('connection_id', '=', $event->getConnectionId())
                    ->delete();
                break;

            case 'MESSAGE':
                if (
                    ($body = $event->getBody()) &&
                    ($channel = $body['listen_channel'] ?? null)
                ) {
                    print_r($model);
                    $model::createListener(
                        $channel,
                        $event->getConnectionId(),
                        $event->getApiId(),
                        $event->getRegion(),
                        $event->getStage()
                    );

                } else {
                    $event->setResponseCode(500);
                    $event->setResponseText('invalid event payload');
                }
                break;
        }
    }
}
