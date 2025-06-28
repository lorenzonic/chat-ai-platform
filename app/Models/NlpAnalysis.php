<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NlpAnalysis extends Model
{
    use HasFactory;

    protected $fillable = [
        'store_id',
        'chat_log_id',
        'user_message',
        'detected_intent',
        'keywords',
        'entities',
        'suggestions',
        'source',
        'confidence',
        'metadata'
    ];

    protected $casts = [
        'keywords' => 'array',
        'entities' => 'array',
        'suggestions' => 'array',
        'metadata' => 'array',
        'confidence' => 'float'
    ];

    // Relazioni
    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    public function chatLog(): BelongsTo
    {
        return $this->belongsTo(ChatLog::class);
    }

    // Scope per filtri
    public function scopeByIntent($query, string $intent)
    {
        return $query->where('detected_intent', $intent);
    }

    public function scopeByStore($query, int $storeId)
    {
        return $query->where('store_id', $storeId);
    }

    public function scopeWithKeyword($query, string $keyword)
    {
        return $query->whereJsonContains('keywords', $keyword);
    }
}
