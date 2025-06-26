<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Newsletter extends Model
{
    use HasFactory;

    protected $fillable = [
        'store_id',
        'title',
        'content',
        'images',
        'cta_text',
        'cta_url',
        'status',
        'scheduled_at',
        'sent_at',
        'recipients_count',
        'opens_count',
        'clicks_count',
        'metadata',
    ];

    protected $casts = [
        'images' => 'array',
        'metadata' => 'array',
        'scheduled_at' => 'datetime',
        'sent_at' => 'datetime',
    ];

    /**
     * Get the store that owns the newsletter
     */
    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    /**
     * Get newsletter sends
     */
    public function newsletterSends(): HasMany
    {
        return $this->hasMany(NewsletterSend::class);
    }

    /**
     * Scope for sent newsletters
     */
    public function scopeSent($query)
    {
        return $query->where('status', 'sent');
    }

    /**
     * Scope for draft newsletters
     */
    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    /**
     * Get open rate percentage
     */
    public function getOpenRateAttribute(): float
    {
        if ($this->recipients_count === 0) {
            return 0;
        }
        return round(($this->opens_count / $this->recipients_count) * 100, 2);
    }

    /**
     * Get click rate percentage
     */
    public function getClickRateAttribute(): float
    {
        if ($this->recipients_count === 0) {
            return 0;
        }
        return round(($this->clicks_count / $this->recipients_count) * 100, 2);
    }
}
