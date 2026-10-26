<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InventoryTransaction extends Model
{
    use HasFactory;

    public const TYPE_STOCK_IN = 'stock_in';
    public const TYPE_STOCK_OUT = 'stock_out';
    public const TYPE_ADJUSTMENT = 'adjustment';

    public const TYPES = [
        self::TYPE_STOCK_IN,
        self::TYPE_STOCK_OUT,
        self::TYPE_ADJUSTMENT,
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'product_id',
        'user_id',
        'type',
        'quantity',
        'reference_number',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'quantity' => 'integer',
    ];

    /**
     * Transaction belongs to a product.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Transaction belongs to the user who performed it (audit trail).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope: stock-in transactions only.
     */
    public function scopeStockIn($query)
    {
        return $query->where('type', self::TYPE_STOCK_IN);
    }

    /**
     * Scope: stock-out transactions only.
     */
    public function scopeStockOut($query)
    {
        return $query->where('type', self::TYPE_STOCK_OUT);
    }

    /**
     * Scope: adjustment transactions only.
     */
    public function scopeAdjustment($query)
    {
        return $query->where('type', self::TYPE_ADJUSTMENT);
    }

    /**
     * Scope: most recent first.
     */
    public function scopeRecent($query)
    {
        return $query->orderByDesc('created_at');
    }

    /**
     * Human-readable type label.
     */
    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            self::TYPE_STOCK_IN => 'Stock In',
            self::TYPE_STOCK_OUT => 'Stock Out',
            self::TYPE_ADJUSTMENT => 'Adjustment',
            default => ucfirst(str_replace('_', ' ', $this->type)),
        };
    }

    /**
     * Bootstrap badge color class for the type.
     */
    public function getTypeBadgeAttribute(): string
    {
        return match ($this->type) {
            self::TYPE_STOCK_IN => 'success',
            self::TYPE_STOCK_OUT => 'danger',
            self::TYPE_ADJUSTMENT => 'warning',
            default => 'secondary',
        };
    }

    /**
     * Signed quantity for display (+ for in, - for out).
     * For adjustments the stored quantity is the absolute difference;
     * the sign is determined by the notes / context when created.
     */
    public function getSignedQuantityAttribute(): int
    {
        return match ($this->type) {
            self::TYPE_STOCK_IN => $this->quantity,
            self::TYPE_STOCK_OUT => -$this->quantity,
            default => $this->quantity, // adjustment handled specially
        };
    }
}
