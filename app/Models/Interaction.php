<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Interaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'store_id',
        'session_id',
        'question',
        'answer',
        'ip',
        'user_agent',
        'utm_source',
        'utm_medium',
        'utm_campaign',
        'ref_code',
        'qr_code_id',
        'duration',
        'device_type',
        'browser',
        'os',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
        'duration' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the store that owns the interaction
     */
    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    /**
     * Get the QR code associated with the interaction
     */
    public function qrCode(): BelongsTo
    {
        return $this->belongsTo(QrCode::class);
    }

    /**
     * Scope per filtrare per date range
     */
    public function scopeDateRange($query, $from = null, $to = null)
    {
        if ($from) {
            $query->where('created_at', '>=', $from);
        }

        if ($to) {
            $query->where('created_at', '<=', $to);
        }

        return $query;
    }

    /**
     * Scope per filtrare per store
     */
    public function scopeForStore($query, $storeId)
    {
        return $query->where('store_id', $storeId);
    }

    /**
     * Get device type from user agent
     */
    public function getDeviceTypeAttribute($value)
    {
        if ($value) {
            return $value;
        }

        if (!$this->user_agent) {
            return 'unknown';
        }

        $userAgent = strtolower($this->user_agent);

        if (strpos($userAgent, 'mobile') !== false || strpos($userAgent, 'android') !== false || strpos($userAgent, 'iphone') !== false) {
            return 'mobile';
        } elseif (strpos($userAgent, 'tablet') !== false || strpos($userAgent, 'ipad') !== false) {
            return 'tablet';
        } else {
            return 'desktop';
        }
    }

    /**
     * Get browser from user agent
     */
    public function getBrowserAttribute($value)
    {
        if ($value) {
            return $value;
        }

        if (!$this->user_agent) {
            return 'unknown';
        }

        $userAgent = strtolower($this->user_agent);

        if (strpos($userAgent, 'chrome') !== false) {
            return 'Chrome';
        } elseif (strpos($userAgent, 'firefox') !== false) {
            return 'Firefox';
        } elseif (strpos($userAgent, 'safari') !== false) {
            return 'Safari';
        } elseif (strpos($userAgent, 'edge') !== false) {
            return 'Edge';
        } else {
            return 'Other';
        }
    }
}
