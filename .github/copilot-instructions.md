# ChatAI Plants - AI Coding Agent Instructions

## Project Overview
Multi-tenant SaaS platform for plant garden centers featuring AI chatbots with QR code integration, lead generation, and analytics. **Strategic evolution toward a virtual marketplace/catalog** where growers showcase their available products to stores. Laravel 12 backend with Vue 3 frontend, Gemini AI integration, and Python NLP analysis.

## Architecture Patterns

### Multi-Auth System
- **3 separate auth guards**: `admin` (super admin), `store` (garden centers), `user` (end customers)
- **Route organization**: Separate route files (`admin.php`, `store.php`, `auth.php`) with guard-specific middleware
- **Controller structure**: Namespaced by role (`Admin/`, `Store/`, `Api/`)
- **Model inheritance**: Store extends `Authenticatable`, not User model

### Database Design
```
stores (multi-tenant) → qr_codes → chat_logs
stores → leads → newsletters → newsletter_sends  
stores → orders → order_items → products
growers → authenticate + manage their products (marketplace catalog)
qr_codes → can link to orders OR products (polymorphic-like)
admin → manages all stores + bulk import system
products → belong to growers (marketplace inventory)
```

### Future Marketplace Vision
- **Growers** authenticate and manage their product catalogs
- **Stores** browse and order from grower catalogs via the platform
- **Product discovery** through AI-powered search and recommendations
- **Market analytics** for pricing trends, demand forecasting
- **Automated reordering** based on QR scan analytics and inventory levels

### AI Integration Architecture
- **Gemini API**: Primary AI responses via Laravel HTTP client
- **Python NLP**: spaCy processing for intent detection, entity extraction, sentiment analysis
- **Service pattern**: `NLPService.php` bridges Laravel ↔ Python via Process::run()
- **Caching**: Redis/database cache for NLP results (1hr TTL)
- **Market Intelligence**: E-commerce scraping scripts for pricing analysis and trend detection
- **Future AI Features**: Product recommendations, demand forecasting, automated catalog matching

## Development Workflows

### Order Import & Management System
```bash
# Core business workflow: CSV/Excel order imports
# Admin uploads order files → validates mapping → creates orders/products/QR codes
# Routes: /admin/import/orders, /admin/import/products
# Auto-generates: order numbers, QR codes, barcode labels

# CSV structure expected:
# order_id, product_name, quantity, price, store_code, grower_id, ean_code
```

### QR Code & Barcode Generation
```bash
# QR code generation with multiple formats:
# - Standard QR (store chatbot links)
# - GS1 QR codes (EAN-13 compatible for retail scanners)
# - Printable labels with QR + barcode + product info

# Commands:
php artisan qr:fix-urls --regenerate  # Regenerate all QR images
```

### Market Intelligence & Python Integration
```bash
# Setup NLP environment
php artisan setup:nlp

# Manual Python script execution
.\python.bat scripts\spacy_nlp.py "testo da analizzare"

# E-commerce market analysis
.\python.bat scripts\real_ecommerce_scraper.py --validate-only
.\python.bat scripts\marketplace_analyzer.py --days=30
.\python.bat scripts\marketplace_api_collector.py

# Plant trends and pricing data
.\update_plant_data.bat
.\plant_scraping_manager.bat
```

### Asset Management
```bash
# Development (Vite + Vue 3)
npm run dev  # Starts on port 5173+ (auto-increments if occupied)

# Production build
npm run build

# Laravel server
php artisan serve --host=localhost --port=8000
```

### Database Operations
```bash
# Migrations with 27 tables completed
php artisan migrate:status

# Key seeders
php artisan db:seed --class=AdminSeeder
php artisan db:seed --class=TestStoreSeeder
```

## Project-Specific Patterns

### View Layout Strategy
- **Standard layouts**: `admin.blade.php`, `app.blade.php`, `guest.blade.php` use @vite()
- **Fallback layouts**: `admin-simple-test.blade.php`, `admin-fixed.blade.php` use CDN assets
- **Vue components**: Embedded in Blade with `<div id="vue-app">` pattern

### Store Slug System
- **URL pattern**: `/{store:slug}` for public chatbot access
- **QR redirect**: `/qr/{qrCode}` → store chatbot with tracking
- **Route binding**: Uses `slug` field, not `id`

### JSON Cast Handling
```php
// Store model pattern for JSON attributes
protected $casts = [
    'chat_suggestions' => 'array',
    'opening_hours' => 'array'
];

// Safe JSON access with type checking
public function getChatSuggestionsAttribute($value) {
    if (is_string($value)) {
        return json_decode($value, true) ?: [];
    }
    return $value ?: [];
}
```

### Python Script Integration
- **NLPService pattern**: Wraps Python calls with timeout and error handling
- **Script location**: `scripts/` directory with `.bat` wrappers for Windows
- **Error handling**: Fallback analysis when Python fails
- **Unicode support**: Windows-specific encoding handling in Python scripts

### QR Code & Order Integration
- **QR tracking**: Each QR links to store chatbot with `ref_code` parameter
- **Order-QR linking**: QR codes can reference specific orders or products
- **Barcode compatibility**: EAN-13 support for retail scanners
- **Label printing**: Generate printable labels with QR + barcode + product info

### API Response Patterns
```php
// Chatbot API standard response
return response()->json([
    'response' => $aiResponse,
    'suggestions' => $suggestions,
    'metadata' => [
        'intent' => $nlpAnalysis['intent'],
        'confidence' => $nlpAnalysis['intent_confidence']
    ]
]);
```

## Critical File Dependencies

### Order Import & QR System
- `app/Http/Controllers/Admin/ImportController.php`: CSV/Excel order processing
- `app/Http/Controllers/Admin/QrCodeController.php`: QR generation, barcode labels
- `resources/views/admin/qr-codes/label.blade.php`: Printable label template
- `app/Console/Commands/FixQrCodeUrls.php`: QR regeneration command

### Frontend Assets
- `resources/js/components/`: Vue 3 chatbot components (CompactChatbot, ModernChatbot)
- `vite.config.js`: Vue 3 + custom elements configuration
- `resources/views/layouts/`: Multi-layout strategy for asset loading

### Python Integration
- `scripts/spacy_nlp.py`: Core NLP analysis with Italian model
- `app/Services/NLPService.php`: Laravel ↔ Python bridge
- `requirements.txt` + `ml_requirements.txt`: Python dependencies

### Business Logic
- `app/Models/Store.php`: Multi-tenant core with JSON attributes
- `app/Models/QrCode.php`: QR tracking with order/product relationships
- `routes/admin.php`: Admin panel with order management
- `app/Http/Controllers/Api/ChatbotController.php`: AI chat API

### Configuration
- `.env`: Multi-database support (SQLite dev, MySQL/PostgreSQL prod)
- `routes/`: Separate files by user role and functionality
- `composer.json`: Includes QR, barcode, Excel libraries

## Environment-Specific Notes
- **Windows development**: Uses `.bat` files for Python script execution
- **Production**: Railway.app deployment with PostgreSQL
- **Local development**: XAMPP/Laravel serve with MySQL/SQLite
- **Asset serving**: Vite dev server (port 5173+) or compiled assets for production
