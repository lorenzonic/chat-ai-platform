<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderOffer extends Model
{
    protected $fillable = [
        'order_id',
        'offer_id',
        'discount_amount',
        'offer_code',
        'offer_snapshot',
    ];

    protected $casts = [
        'discount_amount' => 'decimal:2',
        'offer_snapshot' => 'array',
    ];

    /**
     * Order relationship
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Offer relationship
     */
    public function offer(): BelongsTo
    {
        return $this->belongsTo(Offer::class);
    }
}
