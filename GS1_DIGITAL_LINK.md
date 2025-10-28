# 🏷️ GS1 Digital Link Implementation

## 📋 Overview
Il sistema di QR code ora supporta il formato **GS1 Digital Link**, uno standard internazionale per collegare prodotti fisici a contenuti digitali tramite URL strutturati.

## 🔗 Formato URL GS1 Digital Link

### Standard Implementato
```
https://{domain}/{store-slug}/01/{GTIN}?question={encoded_question}&ref={ref_code}
```

### Componenti
- **Domain**: URL base dell'applicazione
- **Store Slug**: Identificatore univoco del negozio
- **01**: Application Identifier (AI) GS1 per GTIN
- **GTIN**: Global Trade Item Number (EAN-13, 13 cifre)
- **Query Parameters**:
  - `question`: Domanda pre-compilata per il chatbot
  - `ref`: Reference code per tracking

### Esempio Completo
```
https://chatai-plants.com/garden-center-roma/01/8012345678901?question=Come+si+cura+Rosa+rossa?&ref=qr_abc12345_1234567890
```

## 🎯 Funzionalità

### Generazione Automatica
Il sistema genera automaticamente URL in formato GS1 quando:
- ✅ Il prodotto ha un codice EAN valido (13 cifre)
- ✅ Il QR code è collegato a un prodotto specifico
- ✅ Lo store è configurato correttamente

### Fallback
Se il prodotto non ha EAN, il sistema usa il formato standard:
```
https://{domain}/{store-slug}?question={encoded_question}&ref={ref_code}
```

## 📐 Implementazione Tecnica

### 1. Model QrCode (`app/Models/QrCode.php`)
```php
public function getQrUrl(): string
{
    // GS1 Digital Link se prodotto ha EAN
    if ($this->product && $this->product->ean) {
        $url = "{$baseUrl}/{$this->store->slug}/01/{$this->product->ean}";
    } else {
        // Formato standard
        $url = "{$baseUrl}/{$this->store->slug}";
    }
    
    // Aggiungi parametri
    if ($this->question) {
        $url .= '?question=' . urlencode($this->question);
    }
    $url .= ($this->question ? '&' : '?') . 'ref=' . $this->ref_code;
    
    return $url;
}
```

### 2. LabelService (`app/Services/LabelService.php`)
```php
public function generateCareQRCode($product, $store)
{
    $careQuestion = "Come si cura " . $product->name . "?";
    
    // GS1 Digital Link se EAN disponibile
    if ($product->ean && strlen($product->ean) === 13) {
        $chatUrl = url("/{$store->slug}/01/{$product->ean}") 
                 . "?question=" . urlencode($careQuestion);
    } else {
        $chatUrl = url("/{$store->slug}") 
                 . "?q=" . urlencode($careQuestion);
    }
    
    return [
        'url' => $chatUrl,
        'svg' => QrCode::format('svg')->size(200)->generate($chatUrl),
        'question' => $careQuestion,
        'gs1_format' => $product->ean && strlen($product->ean) === 13
    ];
}
```

### 3. Routing (`routes/store.php`)
```php
// GS1 Digital Link Route
Route::get('/{store:slug}/01/{gtin}', function($store, $gtin) {
    // Validazione GTIN (13 cifre)
    if (!preg_match('/^\d{13}$/', $gtin)) {
        abort(404, 'Invalid GTIN format');
    }
    
    return view('store.frontend.chatbot-vue', [
        'store' => $store,
        'gtin' => $gtin,
        'gs1_digital_link' => true
    ]);
})->name('store.chatbot.gs1');
```

## 🔍 Application Identifiers (AI) GS1

### AI Principali Supportati
| AI | Descrizione | Formato | Esempio |
|----|-------------|---------|---------|
| 01 | GTIN (EAN-13) | N13 | 8012345678901 |
| 10 | Batch/Lot | AN..20 | LOT123 |
| 17 | Expiry Date | N6 (YYMMDD) | 251231 |
| 21 | Serial Number | AN..20 | SN123456 |

### Estensioni Future
Il sistema può essere esteso per supportare altri AI:
```
/{store}/01/{GTIN}/10/{batch}/21/{serial}?question=...
```

## 📱 Vantaggi GS1 Digital Link

### 1. **Compatibilità Scanner Retail**
- I QR code sono leggibili da scanner EAN standard
- Compatibilità con sistemi POS esistenti

### 2. **Standard Globale**
- Riconosciuto internazionalmente
- Interoperabilità con sistemi terzi

### 3. **Tracciabilità**
- Link diretto prodotto-digitale
- Tracking completo del ciclo di vita

### 4. **Flessibilità**
- Supporta multiple query string
- Estensibile con altri AI GS1

### 5. **SEO & Analytics**
- URL strutturati e semantici
- Migliore indicizzazione prodotti

## 🧪 Test & Validazione

### Test URL Generazione
```php
// Con EAN
$product = Product::where('ean', '8012345678901')->first();
$qrCode = QrCode::where('product_id', $product->id)->first();
$url = $qrCode->getQrUrl();
// Output: https://domain/store-slug/01/8012345678901?question=...&ref=...

// Senza EAN
$product = Product::whereNull('ean')->first();
$qrCode = QrCode::where('product_id', $product->id)->first();
$url = $qrCode->getQrUrl();
// Output: https://domain/store-slug?question=...&ref=...
```

### Validazione GTIN
```php
// Route valida solo GTIN a 13 cifre
preg_match('/^\d{13}$/', $gtin); // true/false
```

## 🎨 Esempio Etichetta con GS1

```
┌─────────────────────────────────────────────┐
│ ┌────┐  Rosa Rossa                         │
│ │ QR │  € 12,50                            │
│ │GS1 │                                     │
│ └────┘                                      │
│ URL: store.com/roma/01/8012345678901       │
│ ══════════════════════════════════════      │
│ EAN: 8012345678901        Garden Roma       │
└─────────────────────────────────────────────┘
```

## 📊 Analytics & Tracking

### Parametri Tracciabili
- **GTIN**: Identifica il prodotto specifico
- **ref**: Reference code univoco per scan tracking
- **question**: Intent del cliente (domanda pre-compilata)
- **store**: Punto vendita di origine

### Query Analytics
```sql
-- Scan per prodotto (via GTIN)
SELECT COUNT(*) 
FROM chat_logs 
WHERE url LIKE '%/01/8012345678901%';

-- Conversioni da GS1 Digital Link
SELECT COUNT(*) 
FROM chat_logs 
WHERE url LIKE '%/01/%' 
  AND created_at > '2025-01-01';
```

## 🔧 Configurazione

### Requisiti
- [x] Prodotti devono avere EAN-13 valido (13 cifre)
- [x] QR code collegati a prodotto specifico
- [x] Store con slug configurato
- [x] Route GS1 registrata prima di route generica

### Verifica Funzionamento
1. Crea prodotto con EAN valido
2. Genera etichetta con QR code
3. Scansiona QR code
4. Verifica URL nel formato GS1
5. Conferma redirect a chatbot store

## 🌐 Risorse

### Standard GS1
- **GS1 Digital Link Standard**: https://www.gs1.org/standards/gs1-digital-link
- **URI Syntax**: https://www.gs1.org/standards/Digital-Link/1-0
- **Application Identifiers**: https://www.gs1.org/standards/barcodes/application-identifiers

### Validatori Online
- GS1 Digital Link Toolkit: https://digital-link-toolkit.gs1.org/
- GTIN Validator: https://www.gs1.org/services/check-digit-calculator

---

**Implementato**: Ottobre 2025  
**Standard**: GS1 Digital Link v1.2  
**Compatibilità**: EAN-13, UPC-A, QR Code
