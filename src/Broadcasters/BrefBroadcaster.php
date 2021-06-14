<?php

namespace BrefLaravelBroadcast\Broadcasters;

use Bref\Websocket\SimpleWebsocketClient;
use Illuminate\Broadcasting\Broadcasters\Broadcaster;
use Illuminate\Broadcasting\Broadcasters\UsePusherChannelConventions;
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
        $channelName = str_replace($this->prefix, '', $request->channel_name);

        return parent::verifyUserCanAccessChannel(
            $request,
            $channelName
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
        if (is_bool($result)) {
            return json_encode($result);
        }

        $channelName = $request->channel_name;

        return json_encode([
            'channel_data' => [
                'user_id' => $this->retrieveUser($request, $channelName)->getAuthIdentifier(),
                'user_info' => $result,
            ]
        ]);
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
        if (empty($channels)) {
            return;
        }

        $model = config('bref_laravel_broadcast.model');
        $clientCache = [];

        $model::query()
            ->whereIn('channel', $this->formatChannels($channels))
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
                                'data' => $payload,
                            ],
                            JSON_THROW_ON_ERROR
                        )
                    );
                }
            );
    }

    /**
     * Format the channel array into an array of strings.
     *
     * @param array $channels
     *
     * @return array
     */
    protected function formatChannels(array $channels)
    {
        return array_map(function ($channel) {
            return $this->prefix . $channel;
        }, parent::formatChannels($channels));
    }
}
