<?php

namespace App\Notifications;

use App\Models\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LowStockNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public Product $product
    ) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        // Database for in-app bell; mail for email alerts
        return ['database', 'mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Low Stock Alert: ' . $this->product->name)
            ->greeting('Hello ' . ($notifiable->name ?? 'Team') . ',')
            ->line('The following product has reached or fallen below its reorder level.')
            ->line('**Product:** ' . $this->product->name)
            ->line('**SKU:** ' . $this->product->sku)
            ->line('**Current Quantity:** ' . $this->product->quantity)
            ->line('**Reorder Level:** ' . $this->product->reorder_level)
            ->action('View Product', url('/products/' . $this->product->id))
            ->line('Please consider placing a purchase order soon.');
    }

    /**
     * Get the array representation of the notification (database channel).
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'product_id' => $this->product->id,
            'product_name' => $this->product->name,
            'sku' => $this->product->sku,
            'quantity' => $this->product->quantity,
            'reorder_level' => $this->product->reorder_level,
            'message' => sprintf(
                '%s (SKU: %s) is low on stock. Current: %d, Reorder level: %d.',
                $this->product->name,
                $this->product->sku,
                $this->product->quantity,
                $this->product->reorder_level
            ),
            'url' => '/products/' . $this->product->id,
        ];
    }
}
