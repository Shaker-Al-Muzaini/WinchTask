<?php

namespace Src\Domain\Orders\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Src\Domain\Orders\Models\Entities\Order;
use Illuminate\Database\Eloquent\Builder;
class OrderAssignedEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Order $order;

    public function __construct(Order $order)
    {
        // شحن الطلب مع السائق المرتبط به (Eager Loading) ليرسله Reverb كاملا ل  Vue
        $this->order = $order->load('driver');
    }

    public function broadcastOn(): array
    {
        return [
            new Channel('admin-orders'),
        ];
    }

    public function broadcastAs(): string
    {
        return 'order.assigned';
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', 'pending');
    }
}
