<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewOrderNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(private Order $order)
    {
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $this->order->loadMissing('customer', 'invoice');
        $url = route('orders.show', $this->order);

        if (($notifiable->role ?? null) === 'staff_inventory') {
            $url = route('packing.index');
        }

        return [
            'type' => 'order',
            'title' => 'New Order #' . $this->order->id,
            'message' => 'Order for $' . number_format((float) $this->order->total_amount, 2)
                . ' by ' . ($this->order->customer?->name ?? 'Unknown customer'),
            'url' => $url,
            'order_id' => $this->order->id,
            'invoice_id' => $this->order->invoice?->id,
            'customer_id' => $this->order->customer_id,
            'amount' => (float) $this->order->total_amount,
        ];
    }
}
