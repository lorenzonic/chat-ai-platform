<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class NewsletterSend extends Model
{
    use HasFactory;

    protected $fillable = [
        'newsletter_id',
        'lead_id',
        'sent_at',
        'opened_at',
        'clicked_at',
        'bounced',
        'tracking_token',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
        'opened_at' => 'datetime',
        'clicked_at' => 'datetime',
        'bounced' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->tracking_token)) {
                $model->tracking_token = Str::random(32);
            }
        });
    }

    /**
     * Get the newsletter
     */
    public function newsletter(): BelongsTo
    {
        return $this->belongsTo(Newsletter::class);
    }

    /**
     * Get the lead
     */
    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }

    /**
     * Mark as opened
     */
    public function markAsOpened(): void
    {
        if (!$this->opened_at) {
            $this->update([
                'opened_at' => now()
            ]);

            // Increment newsletter opens count
            $this->newsletter->increment('opens_count');
        }
    }

    /**
     * Mark as clicked
     */
    public function markAsClicked(): void
    {
        if (!$this->clicked_at) {
            $this->update([
                'clicked_at' => now()
            ]);

            // Increment newsletter clicks count
            $this->newsletter->increment('clicks_count');
        }
    }
}
