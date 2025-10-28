<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class QrCode extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'store_id',
        'name',
        'question',
        'qr_code_image',
        'ref_code',
        'is_active',
        'ean_code',
        'order_id',   // Add order_id for order-specific QR codes
        'product_id', // Add product_id for product-specific QR codes
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get the store that owns the QR code.
     */
    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    /**
     * Get the order that this QR code belongs to (if any).
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get the product that this QR code belongs to (if any).
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the scans for this QR code.
     */
    public function scans(): HasMany
    {
        return $this->hasMany(QrScan::class);
    }

    /**
     * Get the chat logs for this QR code.
     */
    public function chatLogs(): HasMany
    {
        return $this->hasMany(ChatLog::class);
    }

    /**
     * Generate the QR code URL with GS1 Digital Link format.
     * Format: https://domain/{store-slug}/01/{GTIN}?question={encoded_question}&ref={ref_code}
     */
    public function getQrUrl(): string
    {
        // Get base URL with fallback for production
        $baseUrl = config('app.url');

        // If APP_URL is not properly set or still contains variable, use request URL
        if (empty($baseUrl) || str_contains($baseUrl, '${') || $baseUrl === 'http://localhost') {
            $baseUrl = request()->getSchemeAndHttpHost();
        }

        // Ensure HTTPS in production
        if (app()->environment('production')) {
            $baseUrl = str_replace('http://', 'https://', $baseUrl);
        }

        // GS1 Digital Link format: /{store-slug}/01/{GTIN}
        // If product has EAN/GTIN, use GS1 Digital Link format
        if ($this->product && $this->product->ean) {
            // GS1 Digital Link with GTIN (AI 01)
            $url = "{$baseUrl}/{$this->store->slug}/01/{$this->product->ean}";
        } else {
            // Fallback to standard format if no EAN
            $url = "{$baseUrl}/{$this->store->slug}";
        }

        // Add question parameter
        if ($this->question) {
            $url .= '?question=' . urlencode($this->question);
        }

        // Add reference code for tracking
        $url .= ($this->question ? '&' : '?') . 'ref=' . $this->ref_code;

        return $url;
    }

    /**
     * Get total scan count for this QR code.
     */
    public function getTotalScansAttribute(): int
    {
        return $this->scans()->count();
    }

    /**
     * Get unique visitor count (by IP).
     */
    public function getUniqueVisitorsAttribute(): int
    {
        return $this->scans()->distinct('ip_address')->count('ip_address');
    }

    /**
     * Get mobile scan count.
     */
    public function getMobileScansAttribute(): int
    {
        return $this->scans()->where('device_type', 'mobile')->count();
    }

    /**
     * Get desktop scan count.
     */
    public function getDesktopScansAttribute(): int
    {
        return $this->scans()->where('device_type', 'desktop')->count();
    }

    /**
     * Get recent scans count (last 7 days).
     */
    public function getRecentScansAttribute(): int
    {
        return $this->scans()->where('created_at', '>=', now()->subDays(7))->count();
    }

    /**
     * Get scan statistics.
     */
    public function getStatsAttribute(): array
    {
        return [
            'total_scans' => $this->total_scans,
            'unique_ips' => $this->unique_visitors,
            'mobile_scans' => $this->mobile_scans,
            'desktop_scans' => $this->desktop_scans,
            'recent_scans' => $this->recent_scans,
        ];
    }
}
