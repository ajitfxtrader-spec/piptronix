<?php

namespace App\Events;

use App\Models\Drawdown;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DrawdownUpdated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $drawdown;

    /**
     * Create a new event instance.
     */
    public function __construct(Drawdown $drawdown)
    {
        $this->drawdown = $drawdown;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('drawdowns'),
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'updated';
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'id' => $this->drawdown->id,
            'symbol' => $this->drawdown->symbol,
            'drawdown_amount' => $this->drawdown->drawdown_amount,
            'drawdown_percent' => $this->drawdown->drawdown_percent,
            'event_date' => $this->drawdown->event_date->toIso8601String(),
            'martingle_cycle' => $this->drawdown->martingle_cycle,
            'current_lot' => $this->drawdown->current_lot,
            'total_lots' => $this->drawdown->total_lots,
            'balance' => $this->drawdown->balance,
            'equity' => $this->drawdown->equity,
        ];
    }
}
