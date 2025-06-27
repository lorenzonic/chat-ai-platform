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
     * Generate the QR code URL.
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
        
        $url = "{$baseUrl}/{$this->store->slug}";

        if ($this->question) {
            $url .= '?question=' . urlencode($this->question);
        }

        $url .= ($this->question ? '&' : '?') . 'ref=' . $this->ref_code;

        return $url;
    }
}
