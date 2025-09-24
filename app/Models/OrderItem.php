<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'store_id',
        'product_id',
        'grower_id',
        'quantity',
        'unit_price',
        'prezzo_rivendita',
        'ean',
        'total_price',
        'product_snapshot',
        'sku',
        'notes',
        'is_active'
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'prezzo_rivendita' => 'decimal:2',
        'total_price' => 'decimal:2',
        'product_snapshot' => 'array',
        'quantity' => 'integer',
        'is_active' => 'boolean'
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    public function grower(): BelongsTo
    {
        return $this->belongsTo(Grower::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($orderItem) {
            $orderItem->total_price = $orderItem->quantity * $orderItem->unit_price;
        });
    }

    public function getProductInfoAttribute(): array
    {
        if ($this->product_snapshot) {
            return $this->product_snapshot;
        }

        if ($this->product) {
            return [
                'name' => $this->product->name,
                'code' => $this->product->code,
                'ean' => $this->product->ean,
                'description' => $this->product->description,
                'category' => $this->product->category
            ];
        }

        return ['name' => 'Product Deleted'];
    }

    public function getFormattedUnitPriceAttribute(): string
    {
        return '€' . number_format((float) $this->unit_price, 2, ',', '.');
    }

    public function getFormattedTotalPriceAttribute(): string
    {
        return '€' . number_format((float) $this->total_price, 2, ',', '.');
    }

    public function getFormattedPrezzoRivenditaAttribute(): string
    {
        return '€' . number_format((float) $this->prezzo_rivendita, 2, ',', '.');
    }

    public function getMarginAttribute(): float
    {
        if ($this->unit_price > 0) {
            return (($this->prezzo_rivendita - $this->unit_price) / $this->unit_price) * 100;
        }
        return 0.0;
    }

    public function getFormattedMarginAttribute(): string
    {
        return number_format($this->margin, 1) . '%';
    }
}
