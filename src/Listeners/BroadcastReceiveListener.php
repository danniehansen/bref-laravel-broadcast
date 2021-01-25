<?php

namespace BrefLaravelBroadcast\Listeners;

use BrefLaravelBroadcast\Events\BroadcastReceive;

class BroadcastReceiveListener
{
    public function handle(BroadcastReceive $event): void
    {
        $model = config('bref_laravel_broadcast.model');

        switch ($event->getType()) {
            case 'DISCONNECT':
                $model::query()
                    ->where('connection_id', '=', $event->getConnectionId())
                    ->get()
                    ->each(
                        static function ($model) {
                            $model->delete();
                        }
                    );
                break;

            case 'MESSAGE':
                if (
                    ($body = $event->getBody()) &&
                    ($channel = $body['listen_channel'] ?? null)
                ) {
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
