<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class TrendingKeyword extends Model
{
    protected $fillable = [
        'keyword',
        'score',
        'region',
        'collected_at',
    ];

    protected $casts = [
        'collected_at' => 'datetime',
        'score' => 'integer',
    ];

    /**
     * Scope per ottenere le keyword di oggi
     */
    public function scopeToday($query, $region = 'IT')
    {
        return $query->where('region', $region)
                    ->whereDate('collected_at', Carbon::today());
    }

    /**
     * Scope per ottenere le keyword degli ultimi N giorni
     */
    public function scopeLastDays($query, $days = 7, $region = 'IT')
    {
        return $query->where('region', $region)
                    ->where('collected_at', '>=', Carbon::now()->subDays($days));
    }

    /**
     * Scope per ottenere le keyword più popolari
     */
    public function scopeTopTrending($query, $limit = 10, $region = 'IT')
    {
        return $query->where('region', $region)
                    ->orderBy('score', 'desc')
                    ->orderBy('collected_at', 'desc')
                    ->limit($limit);
    }

    /**
     * Check se una keyword esiste già per la data corrente
     */
    public static function existsToday($keyword, $region = 'IT')
    {
        return static::where('keyword', $keyword)
                    ->where('region', $region)
                    ->whereDate('collected_at', Carbon::today())
                    ->exists();
    }
}
