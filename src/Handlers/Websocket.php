<?php

namespace BrefLaravelBroadcast\Handlers;

use Bref\Context\Context;
use Bref\Event\ApiGateway\WebsocketEvent;
use Bref\Event\Http\HttpResponse;
use BrefLaravelBroadcast\Events\BroadcastReceive;
use Bref\Event\ApiGateway\WebsocketHandler;

class Websocket extends WebsocketHandler
{
    public function handleWebsocket(WebsocketEvent $event, Context $context): HttpResponse
    {
        $eventReceive = new BroadcastReceive(
            $event->getEventType(),
            $event->getConnectionId(),
            $event->getApiId(),
            $event->getRegion(),
            $event->getStage(),
            (($body = $event->getBody()) ?
                json_decode($body, true, 512, JSON_THROW_ON_ERROR) :
                null
            ),
        );

        event($eventReceive);

        return new HttpResponse($eventReceive->getResponseText(), [], $eventReceive->getResponseCode());
    }
}
