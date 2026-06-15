<?php

namespace App\Events;

use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderUpdated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public Order $order,
        public string $action = 'updated'
    ) {
        $this->order->loadMissing(['table', 'items.menuItem']);
    }

    public function broadcastOn(): array
    {
        return [
            new Channel('admin-orders'),
        ];
    }

    public function broadcastAs(): string
    {
        return 'OrderUpdated';
    }

    public function broadcastWith(): array
    {
        $order = $this->order;

        return [
            'action' => $this->action,
            'order' => [
                'id' => $order->id,
                'order_number' => $order->order_number,
                'status' => $order->status,
                'status_label' => $order->status_label,
                'customer_name' => $order->customer_name,
                'payment_method' => $order->payment_method,
                'formatted_total' => $order->formatted_total,
                'table_number' => $order->table?->number,
                'items_count' => $order->items->count(),
                'created_at' => $order->created_at->format('H:i'),
            ],
            'pending_count' => Order::whereDate('created_at', Carbon::today())
                ->where('status', 'pending')
                ->count(),
        ];
    }
}
