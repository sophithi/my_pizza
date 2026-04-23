<?php

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\BroadcastMessage;

class NewOrderNotification extends Notification
{
    use Queueable;
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('message');
            $table->boolean('read')->default(false);
            $table->string('type')->nullable();
            $table->json('data')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'read', 'created_at']);
        });
    }

    public function __construct(public $order) {}

    public function via($notifiable)
    {
        return ['database', 'broadcast']; // 🔥 important
    }

    public function toArray($notifiable)
    {
        return [
            'type' => 'order',
            'title' => 'New Order #' . $this->order->code,
            'message' => 'Order for $' . $this->order->total,
        ];
    }

    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
            'id' => $this->id,
            'type' => 'order',
            'title' => 'New Order #' . $this->order->code,
            'message' => 'Order for $' . $this->order->total,
            'created_at' => now()->toDateTimeString(),
        ]);
    }
}

