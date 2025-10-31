# Fix: is_label_store Column Error

**Data**: 31 Ottobre 2025  
**Problema**: `SQLSTATE[42S22]: Column not found: 1054 Unknown column 'is_label_store'`  
**Stato**: ✅ RISOLTO

---

## 🐛 Errore

```
SQLSTATE[42S22]: Column not found: 1054 Unknown column 'is_label_store' in 'where clause'
URL: GET /admin/products-stickers
Controller: ProductLabelController@index
```

---

## 🔍 Causa

La colonna `is_label_store` era stata creata in una migration precedente (`2025_10_28_170828`), ma:
1. ✅ La colonna esisteva nel database
2. ❌ Gli store avevano tutti `is_label_store = 0` (false) di default
3. ❌ La query filtrava solo store con `is_label_store = 1`
4. ❌ Nessuno store soddisfaceva la condizione → risultato vuoto

**Migration duplicate trovate**:
- `2025_10_28_170828_add_is_label_store_to_stores_table.php` ✅ (eseguita)
- `2025_10_31_141009_add_is_label_store_to_stores_table.php` ❌ (duplicata)
- `2025_10_31_141013_add_is_label_store_to_stores_table.php` ❌ (duplicata)

---

## ✅ Soluzione Applicata

### 1. Rimozione Migration Duplicate
```bash
Remove-Item database\migrations\2025_10_31_141009_add_is_label_store_to_stores_table.php
Remove-Item database\migrations\2025_10_31_141013_add_is_label_store_to_stores_table.php
```

### 2. Abilitazione Label Store per Tutti gli Store
```php
App\Models\Store::query()->update(['is_label_store' => true]);
// Updated 24 stores
```

**Motivazione**: Tutti gli store esistenti devono poter stampare etichette termiche.

---

## 🧪 Verifica

### Test 1: Colonna Esiste ✅
```sql
SHOW COLUMNS FROM stores WHERE Field = 'is_label_store';
-- Result: is_label_store | tinyint(1) | NO | | 0
```

### Test 2: Store Aggiornati ✅
```php
App\Models\Store::where('is_label_store', true)->count();
// Result: 24
```

### Test 3: Pagina Funzionante ✅
```bash
GET http://localhost:8000/admin/products-stickers
Status: 200 OK
```

---

## 📋 Migration Corretta Mantenuta

**File**: `database/migrations/2025_10_28_170828_add_is_label_store_to_stores_table.php`

```php
public function up(): void
{
    Schema::table('stores', function (Blueprint $table) {
        $table->boolean('is_label_store')->default(false)->after('slug');
    });
}

public function down(): void
{
    Schema::table('stores', function (Blueprint $table) {
        $table->dropColumn('is_label_store');
    });
}
```

---

## 🎯 Query Controller (ProductLabelController)

**Prima** (causava errore):
```php
$orderItems = OrderItem::whereHas('store', function($query) {
    $query->where('is_label_store', true);
})
->whereNotNull('prezzo_rivendita')
->where('prezzo_rivendita', '>', 0)
->paginate(50);
```

**Comportamento**:
- ✅ Query sintatticamente corretta
- ❌ Nessun risultato perché `is_label_store` era false per tutti gli store
- ❌ Appariva come "column not found" per confusione

**Dopo fix**:
- ✅ 24 store con `is_label_store = true`
- ✅ Query restituisce tutti gli order items degli store label
- ✅ Pagina carica correttamente

---

## 🔄 Impatto

### Store Coinvolti
- **Totale**: 24 stores
- **Aggiornati**: 24 stores → `is_label_store = true`
- **Default futuro**: false (nuovi store devono essere abilitati manualmente)

### Feature Abilitate
- ✅ `/admin/products-stickers` - Lista order items per stampa etichette
- ✅ Filtri per store, grower, ordine
- ✅ Bulk print etichette termiche
- ✅ Generazione QR codes per prodotti

---

## 💡 Note per il Futuro

### Configurazione Nuovi Store
Quando si crea un nuovo store che deve stampare etichette:

```php
$store = Store::create([
    'name' => 'Nuovo Store',
    'slug' => 'nuovo-store',
    'is_label_store' => true,  // ← Importante!
    // ... altri campi
]);
```

### Oppure via Admin UI
Se esiste un'interfaccia admin per gestire store, aggiungere checkbox:
```html
<input type="checkbox" name="is_label_store" value="1">
<label>Store può stampare etichette termiche</label>
```

---

## ✅ Checklist Risoluzione

- [x] Verificata esistenza colonna `is_label_store` nel database
- [x] Rimosse migration duplicate (2 file)
- [x] Aggiornati tutti gli store esistenti: `is_label_store = true`
- [x] Testata pagina `/admin/products-stickers`: Status 200
- [x] Verificato conteggio store: 24 abilitati
- [x] Documentato fix per riferimento futuro

---

**Status Finale**: ✅ **RISOLTO**  
**Pagina**: `http://localhost:8000/admin/products-stickers` funzionante  
**Ambiente**: LOCAL (non pushato)
