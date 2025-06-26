<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Lead extends Model
{
    use HasFactory;

    protected $fillable = [
        'store_id',
        'email',
        'name',
        'whatsapp',
        'tag',
        'source',
        'session_id',
        'latitude',
        'longitude',
        'country',
        'country_code',
        'region',
        'city',
        'postal_code',
        'timezone',
        'ip_address',
        'metadata',
        'subscribed',
        'last_interaction',
    ];

    protected $casts = [
        'metadata' => 'array',
        'subscribed' => 'boolean',
        'last_interaction' => 'datetime',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
    ];

    /**
     * Get the store that owns the lead
     */
    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    /**
     * Get newsletter sends for this lead
     */
    public function newsletterSends(): HasMany
    {
        return $this->hasMany(NewsletterSend::class);
    }

    /**
     * Scope for subscribed leads only
     */
    public function scopeSubscribed($query)
    {
        return $query->where('subscribed', true);
    }

    /**
     * Get full location string
     */
    public function getFullLocationAttribute(): ?string
    {
        $parts = array_filter([
            $this->city,
            $this->region,
            $this->country
        ]);

        return empty($parts) ? null : implode(', ', $parts);
    }

    /**
     * Check if lead has coordinates
     */
    public function hasCoordinates(): bool
    {
        return !is_null($this->latitude) && !is_null($this->longitude);
    }

    /**
     * Get Google Maps URL for location
     */
    public function getMapUrlAttribute(): ?string
    {
        if (!$this->hasCoordinates()) {
            return null;
        }

        return "https://www.google.com/maps?q={$this->latitude},{$this->longitude}";
    }

    /**
     * Calculate distance from given coordinates (in kilometers)
     */
    public function distanceFrom(float $lat, float $lng): ?float
    {
        if (!$this->hasCoordinates()) {
            return null;
        }

        $earthRadius = 6371; // km

        $latFrom = deg2rad($this->latitude);
        $lonFrom = deg2rad($this->longitude);
        $latTo = deg2rad($lat);
        $lonTo = deg2rad($lng);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $a = sin($latDelta / 2) * sin($latDelta / 2) +
             cos($latFrom) * cos($latTo) *
             sin($lonDelta / 2) * sin($lonDelta / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }

    /**
     * Scope leads within radius (km) from coordinates
     */
    public function scopeWithinRadius($query, float $lat, float $lng, float $radius)
    {
        // Using Haversine formula in SQL
        $query->whereRaw("
            (
                6371 * acos(
                    cos(radians(?))
                    * cos(radians(latitude))
                    * cos(radians(longitude) - radians(?))
                    + sin(radians(?))
                    * sin(radians(latitude))
                )
            ) <= ?
        ", [$lat, $lng, $lat, $radius]);

        return $query;
    }

    /**
     * Scope leads by country
     */
    public function scopeByCountry($query, string $country)
    {
        return $query->where('country', $country);
    }

    /**
     * Scope leads by region
     */
    public function scopeByRegion($query, string $region)
    {
        return $query->where('region', $region);
    }

    /**
     * Scope leads by city
     */
    public function scopeByCity($query, string $city)
    {
        return $query->where('city', $city);
    }
}
