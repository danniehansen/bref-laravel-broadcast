<?php

namespace BrefLaravelBroadcast\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * Class BroadcastListener
 * @package BrefLaravelBroadcast\Models
 *
 * @property int $listener_id
 * @property string $channel
 * @property string $connection_id
 * @property string $api_id
 * @property string $region
 * @property string $stage
 */
final class BroadcastListener extends Model
{
    protected $primaryKey = 'listener_id';
    protected $table = 'broadcast_listener';
    protected $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
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
        $row->channel = $channel;
        $row->connection_id = $connectionId;
        $row->api_id = $apiId;
        $row->region = $region;
        $row->stage = $stage;
        $row->save();

        return $row;
    }
}
