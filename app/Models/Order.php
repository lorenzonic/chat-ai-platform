<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'store_id',
        'client',
        'cc',
        'pia',
        'pro',
        'delivery_date',
        'status',
        'total_amount',
        'total_items',
        'notes',
        'transport',
        'transport_cost',
        'address',
        'phone',
        'is_active',
    ];

    protected $casts = [
        'delivery_date' => 'date',
        'total_amount' => 'decimal:2',
        'transport_cost' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    /**
     * Get the store that owns this order
     */
    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    /**
     * Get the products for this order (legacy)
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    /**
     * Get the order items for this order (new structure)
     */
    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Get the QR codes for this order
     */
    public function qrCodes(): HasMany
    {
        return $this->hasMany(QrCode::class, 'name', 'ORDER-' . $this->order_number);
    }

    /**
     * Calculate total items from order items
     */
    public function getTotalItemsFromOrderItemsAttribute(): int
    {
        return $this->orderItems()->sum('quantity');
    }

    /**
     * Calculate total amount from order items
     */
    public function getCalculatedTotalAttribute(): float
    {
        return (float) $this->orderItems()->sum('total_price');
    }

    /**
     * Check if order has order items (new structure)
     */
    public function hasOrderItems(): bool
    {
        return $this->orderItems()->exists();
    }

    /**
     * Get all products either from orderItems or direct products relation
     */
    public function getAllProducts()
    {
        if ($this->hasOrderItems()) {
            return $this->orderItems()->with('product')->get()->pluck('product');
        }

        return $this->products;
    }

    /**
     * Applied offers for this order
     */
    public function offers(): BelongsToMany
    {
        return $this->belongsToMany(Offer::class, 'order_offers')
                    ->withPivot('discount_amount', 'offer_code', 'offer_snapshot')
                    ->withTimestamps();
    }

    /**
     * Calculate total discount from applied offers
     */
    public function getTotalDiscountAttribute(): float
    {
        return (float) $this->offers()->sum('order_offers.discount_amount');
    }

    /**
     * Get final total after discounts
     */
    public function getFinalTotalAttribute(): float
    {
        $baseTotal = $this->total_amount ?: $this->getCalculatedTotalAttribute();
        return max(0, $baseTotal - $this->getTotalDiscountAttribute());
    }
}
