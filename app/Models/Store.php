<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Store extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'logo',
        'slug',
        'email',
        'password',
        'description',
        'phone',
        'address',
        'city',
        'state',
        'postal_code',
        'country',
        'website',
        'is_active',
        'is_premium',
        'is_label_store',
        'assistant_name',
        'chat_context',
        'opening_hours',
        'chat_theme_color',
        'chat_enabled',
        'chat_font_family',
        'chat_ai_tone',
        'chat_avatar_image',
        'chat_suggestions',
        'chat_opening_message',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'is_premium' => 'boolean',
            'is_label_store' => 'boolean',
            'opening_hours' => 'array',
            'chat_enabled' => 'boolean',
            'chat_suggestions' => 'array',
        ];
    }

    /**
     * Get the QR codes for this store.
     */
    public function qrCodes(): HasMany
    {
        return $this->hasMany(QrCode::class);
    }

    /**
     * Get the chat logs for this store.
     */
    public function chatLogs(): HasMany
    {
        return $this->hasMany(ChatLog::class);
    }

    /**
     * Get the QR scans for this store.
     */
    public function qrScans(): HasMany
    {
        return $this->hasMany(QrScan::class);
    }

    /**
     * Get the knowledge items for this store.
     */
    public function knowledgeItems(): HasMany
    {
        return $this->hasMany(StoreKnowledge::class);
    }

    /**
     * Get the leads for this store.
     */
    public function leads(): HasMany
    {
        return $this->hasMany(\App\Models\Lead::class);
    }

    /**
     * Get the newsletters for this store.
     */
    public function newsletters(): HasMany
    {
        return $this->hasMany(\App\Models\Newsletter::class);
    }

    /**
     * Get the interactions for this store.
     */
    public function interactions(): HasMany
    {
        return $this->hasMany(\App\Models\Interaction::class);
    }

    /**
     * Get the products for this store.
     */
    public function products(): HasMany
    {
        return $this->hasMany(\App\Models\Product::class);
    }

    /**
     * Get the order items for this store.
     */
    public function orderItems(): HasMany
    {
        return $this->hasMany(\App\Models\OrderItem::class);
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * Get chat suggestions or default ones
     */
    public function getChatSuggestions(): array
    {
        // Se chat_suggestions è null o vuoto, usa i default
        if (!$this->chat_suggestions || empty($this->chat_suggestions)) {
            return $this->getDefaultSuggestions();
        }

        // Il campo è già castato come array da Laravel, quindi restituiscilo direttamente
        return $this->chat_suggestions;
    }

    /**
     * Get default chat suggestions
     */
    public function getDefaultSuggestions(): array
    {
        return [
            "Quali sono i vostri orari?",
            "Dove vi trovate?",
            "Che servizi offrite?",
            "Come posso contattarvi?"
        ];
    }

    /**
     * Get available AI tones
     */
    public static function getAvailableAiTones(): array
    {
        return [
            'professional' => 'Professionale',
            'friendly' => 'Amichevole',
            'cheerful' => 'Allegro',
            'green_passion' => 'Verde Passion (ambientalista)',
        ];
    }

    /**
     * Get available Google Fonts
     */
    public static function getAvailableFonts(): array
    {
        return [
            'Inter' => 'Inter',
            'Roboto' => 'Roboto',
            'Open Sans' => 'Open Sans',
            'Lato' => 'Lato',
            'Montserrat' => 'Montserrat',
            'Poppins' => 'Poppins',
            'Source Sans Pro' => 'Source Sans Pro',
            'Nunito' => 'Nunito',
            'Raleway' => 'Raleway',
            'Ubuntu' => 'Ubuntu',
        ];
    }

    /**
     * Get default opening message
     */
    public function getDefaultOpeningMessage(): string
    {
        $assistantName = $this->assistant_name ?? 'Assistente';
        $storeName = $this->name ?? 'il nostro negozio';

        return "Ciao! 👋 Sono {$assistantName}, il tuo assistente virtuale per {$storeName}. Come posso aiutarti oggi?";
    }

    /**
     * Get the opening message (custom or default)
     */
    public function getOpeningMessage(): string
    {
        return $this->chat_opening_message ?: $this->getDefaultOpeningMessage();
    }
}
