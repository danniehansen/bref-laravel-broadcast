<?php

namespace BrefLaravelBroadcast\Broadcasters;

use Bref\Websocket\SimpleWebsocketClient;
use Illuminate\Broadcasting\Broadcasters\Broadcaster;
use Illuminate\Broadcasting\BroadcastException;
use Illuminate\Http\Request;
use JsonException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class BrefBroadcaster extends Broadcaster
{
    /**
     * Authenticate the incoming request for a given channel.
     *
     * @param Request $request
     *
     * @return mixed
     * @throws AccessDeniedHttpException
     */
    public function auth($request)
    {
        return $this->verifyUserCanAccessChannel(
            $request,
            $request->channel_name
        );
    }

    /**
     * Return the valid authentication response.
     *
     * @param Request $request
     * @param mixed $result
     *
     * @return mixed
     */
    public function validAuthenticationResponse($request, $result)
    {
        return null;
    }

    /**
     * Broadcast the given event.
     *
     * @param array $channels
     * @param string $event
     * @param array $payload
     *
     * @return void
     *
     * @throws BroadcastException
     * @throws JsonException
     */
    public function broadcast(array $channels, $event, array $payload = [])
    {
        $model = config('bref_laravel_broadcast.model');
        $clientCache = [];

        $model::query()
            ->whereIn('channel', $channels)
            ->get()
            ->each(
                static function ($listener) use (&$event, &$payload, &$clientCache) {
                    $cacheKey = $listener->api_id . $listener->stage . $listener->region;

                    $client = $clientCache[$cacheKey] = $clientCache[$cacheKey] ?? SimpleWebsocketClient::create(
                        $listener->api_id,
                        $listener->region,
                        $listener->stage
                    );

                    $client->message(
                        $listener->connection_id,
                        json_encode(
                            [
                                'channel' => $listener->channel,
                                'event' => $event,
                                'payload' => $payload,
                            ],
                            JSON_THROW_ON_ERROR
                        )
                    );
                }
            );
    }
}
