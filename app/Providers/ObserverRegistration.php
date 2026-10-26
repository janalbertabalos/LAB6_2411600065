<?php

/**
 * HOW TO REGISTER THE OBSERVER (Lab 6)
 * =====================================
 *
 * Option A – AppServiceProvider (works on all Laravel versions)
 * -------------------------------------------------------------
 * Open app/Providers/AppServiceProvider.php and add inside boot():
 *
 *   use App\Models\InventoryTransaction;
 *   use App\Observers\InventoryTransactionObserver;
 *
 *   public function boot(): void
 *   {
 *       InventoryTransaction::observe(InventoryTransactionObserver::class);
 *   }
 *
 * Option B – Attribute (Laravel 10+)
 * ----------------------------------
 * Add to the InventoryTransaction model:
 *
 *   use Illuminate\Database\Eloquent\Attributes\ObservedBy;
 *
 *   #[ObservedBy([InventoryTransactionObserver::class])]
 *   class InventoryTransaction extends Model { ... }
 *
 * Option C – EventServiceProvider
 * -------------------------------
 * In the $observers array (if present) or boot method.
 *
 * Also ensure the User model uses the Notifiable trait
 * (already present when using Laravel Breeze):
 *
 *   use Illuminate\Notifications\Notifiable;
 *   class User extends Authenticatable {
 *       use Notifiable;
 *   }
 *
 * Queue (optional but recommended for LowStockNotification which implements ShouldQueue):
 *   php artisan queue:table && php artisan migrate
 *   # set QUEUE_CONNECTION=database in .env
 *   php artisan queue:work
 */

namespace App\Providers;

// This file is documentation only – do not autoload it.
// Copy the registration code into your AppServiceProvider.
EOF
