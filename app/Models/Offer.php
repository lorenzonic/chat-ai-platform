<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Carbon\Carbon;
use App\Models\Grower;
use App\Models\Order;

class Offer extends Model
{
    protected $fillable = [
        'title',
        'description',
        'type',
        'discount_value',
        'buy_quantity',
        'get_quantity',
        'minimum_amount',
        'usage_limit',
        'usage_count',
        'is_active',
        'start_date',
        'end_date',
        'code',
        'grower_id',
        'applicable_products',
        'applicable_categories',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'is_active' => 'boolean',
        'applicable_products' => 'array',
        'applicable_categories' => 'array',
        'discount_value' => 'decimal:2',
        'minimum_amount' => 'decimal:2',
    ];

    /**
     * Grower relationship
     */
    public function grower(): BelongsTo
    {
        return $this->belongsTo(Grower::class);
    }

    /**
     * Orders that have used this offer
     */
    public function orders(): BelongsToMany
    {
        return $this->belongsToMany(Order::class, 'order_offers')
                    ->withPivot('discount_amount', 'offer_code', 'offer_snapshot')
                    ->withTimestamps();
    }

    /**
     * Check if offer is currently valid
     */
    public function isValid(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        $now = Carbon::now();
        if ($now->lt($this->start_date) || $now->gt($this->end_date)) {
            return false;
        }

        if ($this->usage_limit && $this->usage_count >= $this->usage_limit) {
            return false;
        }

        return true;
    }

    /**
     * Check if offer is applicable to given products
     */
    public function isApplicableToProducts(array $productIds): bool
    {
        if (empty($this->applicable_products)) {
            return true; // No restriction
        }

        return !empty(array_intersect($productIds, $this->applicable_products));
    }

    /**
     * Check if offer is applicable to given categories
     */
    public function isApplicableToCategories(array $categories): bool
    {
        if (empty($this->applicable_categories)) {
            return true; // No restriction
        }

        return !empty(array_intersect($categories, $this->applicable_categories));
    }

    /**
     * Calculate discount amount for given order total
     */
    public function calculateDiscount(float $orderTotal, int $quantity = 1): float
    {
        if (!$this->isValid()) {
            return 0;
        }

        if ($this->minimum_amount && $orderTotal < $this->minimum_amount) {
            return 0;
        }

        switch ($this->type) {
            case 'percentage':
                return ($orderTotal * $this->discount_value) / 100;

            case 'fixed_amount':
                return min($this->discount_value, $orderTotal);

            case 'buy_x_get_y':
                if ($quantity >= $this->buy_quantity) {
                    $freeItems = intval($quantity / $this->buy_quantity) * $this->get_quantity;
                    $averageItemPrice = $quantity > 0 ? $orderTotal / $quantity : 0;
                    return $freeItems * $averageItemPrice;
                }
                return 0;

            default:
                return 0;
        }
    }

    /**
     * Scope for active offers
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
                     ->where('start_date', '<=', Carbon::now())
                     ->where('end_date', '>=', Carbon::now());
    }

    /**
     * Scope for offers with available usage
     */
    public function scopeAvailable($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('usage_limit')
              ->orWhereRaw('usage_count < usage_limit');
        });
    }
}
