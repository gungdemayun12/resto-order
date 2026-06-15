<?php

namespace App\Observers;

use App\Events\DashboardUpdated;
use App\Events\OrderUpdated;
use App\Models\Notification;
use App\Models\Order;
use App\Models\User;

class OrderObserver
{
    public function created(Order $order): void
    {
        $this->broadcast($order, 'created');
        $this->createNotification($order, 'created');
    }

    public function updated(Order $order): void
    {
        $this->broadcast($order, 'updated');

        // Only create notification on status change
        if ($order->isDirty('status')) {
            $this->createNotification($order, 'status_changed');
        }
    }

    public function deleted(Order $order): void
    {
        $this->broadcast($order, 'deleted');
    }

    private function broadcast(Order $order, string $action): void
    {
        try {
            broadcast(new OrderUpdated($order, $action));
            broadcast(new DashboardUpdated());
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::warning('Broadcasting failed (Websocket/Reverb offline): ' . $e->getMessage());
        }
    }

    private function createNotification(Order $order, string $action): void
    {
        try {
            if ($action === 'created') {
                $statusLabel = $order->status_label;
                $body = "Pesanan baru #{$order->order_number} dari Meja " . ($order->table?->number ?? '-') . " - {$statusLabel}";

                // Notify owners and cashiers
                $users = User::whereIn('role', ['owner', 'cashier'])->get();
                foreach ($users as $user) {
                    Notification::create([
                        'user_id' => $user->id,
                        'type' => 'order_new',
                        'title' => 'Pesanan Baru!',
                        'body' => $body,
                        'icon' => 'cart',
                        'color' => 'amber',
                        'action_url' => role_route('admin.orders.index'),
                    ]);
                }
            } elseif ($action === 'status_changed') {
                $oldStatus = $order->getOriginal('status');
                $newStatus = $order->status;
                $statusLabel = $order->status_label;

                $title = match($newStatus) {
                    'processing' => 'Pesanan Diproses',
                    'ready' => 'Pesanan Siap!',
                    'completed' => 'Pesanan Selesai',
                    'cancelled' => 'Pesanan Dibatalkan',
                    default => 'Status Diperbarui',
                };

                $icon = match($newStatus) {
                    'processing' => 'cooking',
                    'ready' => 'check',
                    'completed' => 'check',
                    'cancelled' => 'x',
                    default => 'bell',
                };

                $color = match($newStatus) {
                    'processing' => 'blue',
                    'ready' => 'emerald',
                    'completed' => 'slate',
                    'cancelled' => 'red',
                    default => 'amber',
                };

                $body = "Pesanan #{$order->order_number} sekarang: {$statusLabel}";

                // Notify relevant users based on status
                $roles = match($newStatus) {
                    'ready' => ['owner', 'cashier'],
                    'completed', 'cancelled' => ['owner'],
                    default => ['owner', 'cashier', 'kitchen'],
                };

                $users = User::whereIn('role', $roles)->get();
                foreach ($users as $user) {
                    Notification::create([
                        'user_id' => $user->id,
                        'type' => 'order_status',
                        'title' => $title,
                        'body' => $body,
                        'icon' => $icon,
                        'color' => $color,
                        'action_url' => role_route('admin.orders.index'),
                    ]);
                }
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::warning('Failed to create notification: ' . $e->getMessage());
        }
    }
}
