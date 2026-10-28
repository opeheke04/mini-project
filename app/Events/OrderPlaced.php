<?php

namespace App\Events;

use App\Models\Sale;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderPlaced implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public Sale $order,
        public User $user,
    ) {
    }

    public function broadcastOn(): PrivateChannel
    {
        return new PrivateChannel('user.' . $this->user->id);
    }

    public function broadcastAs(): string
    {
        return 'order.placed';
    }

    /**
     * @return array{order_id: int, total: string, user_name: string}
     */
    public function broadcastWith(): array
    {
        return [
            'order_id' => $this->order->id,
            'total' => $this->order->total,
            'user_name' => $this->user->name,
        ];
    }
}
