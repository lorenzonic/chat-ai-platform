<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'store_id',
        'order_id',
        'grower_id',
        'name',
        'code',
        'ean',
        'description',
        'quantity',
        'height',
        'price',
        'category',
        'transport', // Keep transport as it might be product-specific
        'is_active',
    ];

    protected $casts = [
        'height' => 'decimal:2',
        'price' => 'decimal:2',
        'quantity' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Get the store that owns this product
     */
    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    /**
     * Get the order that this product belongs to
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get the grower for this product
     */
    public function grower(): BelongsTo
    {
        return $this->belongsTo(Grower::class);
    }

    /**
     * Get the order items for this product
     */
    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
}
