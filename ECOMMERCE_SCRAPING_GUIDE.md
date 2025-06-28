# 🛍️ E-commerce Web Scraping Implementation Guide

## Overview
Questa guida illustra come implementare uno scraping responsabile di siti e-commerce per raccogliere dati di mercato sulle piante.

## ⚠️ Importante: Scraping Responsabile

### Principi Etici
1. **Rispetta robots.txt** - Sempre controllare `/robots.txt` del sito
2. **Rate limiting** - Non sovraccaricare i server con troppe richieste
3. **User-Agent appropriato** - Identificarsi correttamente
4. **Termini di servizio** - Leggere e rispettare i ToS
5. **Fair use** - Utilizzare i dati solo per analisi legittime

### Rate Limiting Raccomandato
```python
# Delay tra richieste
time.sleep(random.uniform(2, 5))  # 2-5 secondi

# Delay tra categorie
time.sleep(random.uniform(10, 15))  # 10-15 secondi
```

## 🔧 Implementazione Tecnica

### 1. Setup Base
```bash
pip install requests beautifulsoup4 selenium lxml
```

### 2. Headers Responsabili
```python
headers = {
    'User-Agent': 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
    'Accept': 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
    'Accept-Language': 'it-IT,it;q=0.8,en;q=0.3',
    'Connection': 'keep-alive',
}
```

### 3. Gestione Errori
```python
try:
    response = session.get(url, timeout=10)
    response.raise_for_status()
except requests.RequestException as e:
    logger.error(f"Request failed: {e}")
    return fallback_data
```

## 🎯 Siti Target Italiani

### Garden Center Online
- **Viridea** - https://www.viridea.it
- **Floricoltura Quaiato** - https://www.floricolturaquaiato.it
- **Mondo Piante** - https://www.mondopiante.it
- **Bakker Italia** - https://www.bakker.com/it

### Marketplace
- **Amazon Italia** - Categoria Giardino e Giardinaggio
- **eBay Italia** - Sezione Casa e Giardino
- **Subito.it** - Categoria Giardino e Fai da te

## 📊 Dati da Raccogliere

### Informazioni Prodotto
```json
{
  "name": "Monstera Deliciosa 40cm",
  "price": 29.90,
  "original_price": 39.90,
  "discount_percentage": 25,
  "availability": "Disponibile",
  "rating": 4.5,
  "reviews_count": 127,
  "category": "piante da interno",
  "seller": "Viridea",
  "shipping_cost": 9.90,
  "shipping_time": "2-3 giorni lavorativi"
}
```

### Metadati
```json
{
  "scraped_at": "2025-06-27T10:30:00Z",
  "source_url": "https://example.com/product/123",
  "scraper_version": "1.0",
  "data_quality": "high"
}
```

## 🔄 Integrazione con Laravel

### 1. Controller Method
```php
private function getEcommerceData($days = 30)
{
    $cacheKey = "ecommerce_data_{$days}";
    
    return Cache::remember($cacheKey, 7200, function() {
        try {
            $result = Process::run([
                'python',
                base_path('scripts/ecommerce_scraper.py'),
                '--output', storage_path('app/temp/ecommerce_data.json')
            ]);

            if ($result->successful()) {
                return json_decode(file_get_contents(
                    storage_path('app/temp/ecommerce_data.json')
                ), true);
            }

            return $this->getFallbackEcommerceData();
        } catch (\Exception $e) {
            Log::error('Scraping failed: ' . $e->getMessage());
            return $this->getFallbackEcommerceData();
        }
    });
}
```

### 2. Scheduled Scraping
```php
// In app/Console/Kernel.php
protected function schedule(Schedule $schedule)
{
    $schedule->call(function () {
        Cache::forget('ecommerce_data_30');
        // Trigger new scraping
    })->daily();
}
```

## 📈 Analisi Dati

### Price Monitoring
```python
def track_price_changes(products, historical_data):
    price_changes = []
    for product in products:
        historical = find_historical_price(product['name'], historical_data)
        if historical:
            change = product['price'] - historical['price']
            percentage = (change / historical['price']) * 100
            if abs(percentage) > 5:  # Significant change
                price_changes.append({
                    'product': product['name'],
                    'old_price': historical['price'],
                    'new_price': product['price'],
                    'change_percentage': round(percentage, 2)
                })
    return price_changes
```

### Market Insights
```python
def generate_market_insights(products):
    insights = {
        'price_ranges': analyze_price_distribution(products),
        'availability_trends': analyze_availability(products),
        'popular_categories': find_trending_categories(products),
        'competitor_analysis': compare_retailers(products),
        'seasonal_patterns': detect_seasonal_changes(products)
    }
    return insights
```

## 🚀 Deployment Considerations

### 1. Proxy Rotation
```python
proxies = [
    'http://proxy1:port',
    'http://proxy2:port',
    'http://proxy3:port'
]

def get_random_proxy():
    return random.choice(proxies)
```

### 2. User-Agent Rotation
```python
user_agents = [
    'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/119.0.0.0',
    'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) Safari/605.1.15',
    'Mozilla/5.0 (X11; Linux x86_64) Firefox/120.0'
]
```

### 3. Monitoring & Alerts
```python
def monitor_scraping_health():
    metrics = {
        'success_rate': calculate_success_rate(),
        'data_quality': assess_data_quality(),
        'response_times': measure_response_times(),
        'error_frequency': count_recent_errors()
    }
    
    if metrics['success_rate'] < 80:
        send_alert("Low scraping success rate")
```

## 📋 Best Practices

### 1. Data Storage
- **Raw data**: Store in JSON with timestamps
- **Processed data**: Normalize and store in database
- **Historical data**: Keep for trend analysis
- **Backup**: Regular backups of collected data

### 2. Error Handling
- **Graceful degradation**: Use fallback data
- **Retry logic**: Exponential backoff
- **Circuit breaker**: Stop on repeated failures
- **Logging**: Comprehensive error logging

### 3. Performance
- **Async processing**: Use queues for heavy scraping
- **Caching**: Cache processed results
- **Compression**: Compress stored data
- **Cleanup**: Regular cleanup of old data

## 🔒 Legal & Compliance

### 1. Data Protection
- **GDPR compliance**: If handling personal data
- **Data retention**: Clear retention policies
- **Access control**: Restrict data access
- **Anonymization**: Remove identifying information

### 2. Terms of Service
- **Review regularly**: ToS can change
- **Compliance monitoring**: Automated checks
- **Documentation**: Keep compliance records
- **Legal review**: Regular legal consultation

## 📊 Expected Results

### 1. Market Intelligence
- **Price trends**: Track pricing across retailers
- **Availability patterns**: Monitor stock levels
- **Competitive landscape**: Compare offerings
- **Demand indicators**: Identify popular products

### 2. Business Value
- **Pricing strategy**: Optimize pricing decisions
- **Inventory planning**: Better stock management
- **Market opportunities**: Identify gaps
- **Customer insights**: Understand preferences

## 🛠️ Tools & Libraries

### Python Libraries
```bash
pip install requests beautifulsoup4 selenium lxml pandas numpy scrapy
```

### Alternative Solutions
- **Scrapy**: For large-scale scraping
- **Selenium**: For JavaScript-heavy sites
- **Playwright**: Modern browser automation
- **APIs**: Official APIs when available

## 📞 Support & Maintenance

### 1. Monitoring Dashboard
- Real-time scraping status
- Success/failure rates
- Data quality metrics
- Performance indicators

### 2. Maintenance Schedule
- **Daily**: Monitor health checks
- **Weekly**: Review data quality
- **Monthly**: Update selectors
- **Quarterly**: Legal compliance review

---

**Nota**: Questa implementazione è per scopi educativi e di ricerca. Assicurarsi sempre di rispettare i termini di servizio dei siti web e le leggi locali prima di implementare qualsiasi soluzione di web scraping.
