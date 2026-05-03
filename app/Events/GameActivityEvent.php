<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class GameActivityEvent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public readonly string $message,
        public readonly string $emoji,
        public readonly string $type, // 'started' | 'progress' | 'won' | 'lost'
    ) {}

    public function broadcastOn(): Channel
    {
        return new Channel('game-activity');
    }

    public function broadcastAs(): string
    {
        return 'activity';
    }
}
