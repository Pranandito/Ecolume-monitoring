<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DataUpdated implements ShouldBroadcast
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public int $deviceId,
        public array $payload
    ) {}

    public function broadcastOn(): Channel
    {
        return new Channel(
            'device.' . $this->deviceId
        );
    }

    public function broadcastAs(): string
    {
        return 'data.updated';
    }
}
