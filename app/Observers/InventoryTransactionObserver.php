<?php

namespace App\Observers;

use App\Models\InventoryTransaction;
use App\Models\Product;
use App\Models\User;
use App\Notifications\LowStockNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Validation\ValidationException;

class InventoryTransactionObserver
{
    /**
     * Handle the InventoryTransaction "creating" event.
     * Validate stock availability before the record is saved.
     */
    public function creating(InventoryTransaction $transaction): void
    {
        if ($transaction->type === InventoryTransaction::TYPE_STOCK_OUT) {
            $product = Product::find($transaction->product_id);

            if (!$product) {
                throw ValidationException::withMessages([
                    'product_id' => 'Selected product does not exist.',
                ]);
            }

            if ($transaction->quantity > $product->quantity) {
                throw ValidationException::withMessages([
                    'quantity' => "Insufficient stock. Available: {$product->quantity}, requested: {$transaction->quantity}.",
                ]);
            }
        }
    }

    /**
     * Handle the InventoryTransaction "created" event.
     * Update product quantity inside a database transaction and
     * dispatch low-stock notifications when applicable.
     */
    public function created(InventoryTransaction $transaction): void
    {
        DB::transaction(function () use ($transaction) {
            // Lock the product row to prevent race conditions
            $product = Product::where('id', $transaction->product_id)->lockForUpdate()->first();

            if (!$product) {
                throw new \RuntimeException('Product not found while updating stock.');
            }

            $oldQuantity = $product->quantity;
            $delta = $this->resolveDelta($transaction);

            $newQuantity = $oldQuantity + $delta;

            // Safety net: never allow negative stock
            if ($newQuantity < 0) {
                throw ValidationException::withMessages([
                    'quantity' => 'Operation would result in negative inventory. Transaction rolled back.',
                ]);
            }

            $product->quantity = $newQuantity;
            $product->save();

            // Low-stock alert (only when crossing or already at/below reorder level)
            if ($product->isLowStock()) {
                $this->sendLowStockAlert($product);
            }

            Log::info('Inventory updated via transaction', [
                'transaction_id' => $transaction->id,
                'product_id' => $product->id,
                'type' => $transaction->type,
                'delta' => $delta,
                'old_qty' => $oldQuantity,
                'new_qty' => $newQuantity,
                'user_id' => $transaction->user_id,
            ]);
        });
    }

    /**
     * Calculate the quantity change based on transaction type.
     * For adjustments the quantity field stores the absolute difference;
     * the sign is stored in notes as "direction:increase" or "direction:decrease".
     */
    protected function resolveDelta(InventoryTransaction $transaction): int
    {
        return match ($transaction->type) {
            InventoryTransaction::TYPE_STOCK_IN => $transaction->quantity,
            InventoryTransaction::TYPE_STOCK_OUT => -$transaction->quantity,
            InventoryTransaction::TYPE_ADJUSTMENT => $this->resolveAdjustmentDelta($transaction),
            default => 0,
        };
    }

    /**
     * Parse adjustment direction from notes (set by controller).
     */
    protected function resolveAdjustmentDelta(InventoryTransaction $transaction): int
    {
        $notes = $transaction->notes ?? '';
        if (str_contains($notes, 'direction:decrease')) {
            return -$transaction->quantity;
        }
        // Default to increase
        return $transaction->quantity;
    }

    /**
     * Notify all admin/manager users about low stock.
     * Uses the database notification channel (and optionally mail).
     */
    protected function sendLowStockAlert(Product $product): void
    {
        // Avoid spamming: skip if a recent unread low-stock notification already exists
        // for this product (simple check via data payload)
        $recentExists = DB::table('notifications')
            ->where('type', LowStockNotification::class)
            ->whereNull('read_at')
            ->where('data', 'like', '%"product_id":' . $product->id . '%')
            ->where('created_at', '>=', now()->subHours(6))
            ->exists();

        if ($recentExists) {
            return;
        }

        // Notify every authenticated user who can manage inventory
        // (in a real app you would filter by role; here we notify all users)
        $users = User::all();

        if ($users->isNotEmpty()) {
            Notification::send($users, new LowStockNotification($product));
        }
    }
}
