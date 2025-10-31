# Analytics Enhancement - Real Data Implementation

**Data:** 2025-01-31  
**Ambiente:** LOCAL (no git push)  
**Stato:** ✅ COMPLETATO

---

## Obiettivo
Potenziare la pagina analytics dello store con **dati reali** invece di placeholder/sample data per:
- ❓ Domande più frequenti
- 🌱 Piante più ricercate

---

## Modifiche Implementate

### 1. Controller: `app/Http/Controllers/Store/AnalyticsController.php`

#### Metodo `index()` - Aggiornato
```php
public function index(Request $request)
{
    $store = auth('store')->user();
    
    // Get real analytics data
    $frequentQuestions = $this->getFrequentQuestions($store);
    $popularPlants = $this->getPopularPlants($store);
    
    return view('store.analytics.cdn-simple', compact(
        'store',
        'frequentQuestions',
        'popularPlants'
    ));
}
```

#### Nuovo Metodo: `getFrequentQuestions($store)`
- Query sulle **interactions**
- Group by `question`, count, order by count DESC
- Limit 10 domande più frequenti
- Return: array con `question` e `count`

```php
private function getFrequentQuestions($store)
{
    $questions = Interaction::where('store_id', $store->id)
        ->whereNotNull('question')
        ->where('question', '!=', '')
        ->select('question', DB::raw('count(*) as count'))
        ->groupBy('question')
        ->orderByDesc('count')
        ->limit(10)
        ->get();
    
    return $questions->map(function($q) {
        return [
            'question' => $q->question,
            'count' => $q->count
        ];
    });
}
```

#### Nuovo Metodo: `getPopularPlants($store)`
- Cerca **47 keywords di piante** in:
  - `Interaction`: campi `question` e `answer`
  - `ChatLog`: campi `user_message` e `ai_response`
- Conta le menzioni per ogni pianta
- Return: top 8 piante con emoji

**Plant Keywords:**
```php
'rosa', 'rose', 'basilico', 'lavanda', 'geranio', 'cactus', 'orchidea',
'ficus', 'pothos', 'succulenta', 'succulente', 'petunia', 'begonia',
'ciclamino', 'azalea', 'camelia', 'ibisco', 'gelsomino', 'gardenia',
'margherita', 'tulipano', 'narciso', 'giglio', 'iris', 'viola',
'primula', 'dalia', 'zinnia', 'salvia', 'rosmarino', 'timo',
'menta', 'prezzemolo', 'ortensia', 'aloe', 'agave', 'yucca',
'palma', 'felce', 'edera', 'monstera', 'sansevieria', 'croton',
'dipladenia', 'mandevilla', 'bouganville', 'gerbera', 'anthurium'
```

**Emoji Mapping:**
```php
'rosa' => '🌹', 'basilico' => '🌿', 'lavanda' => '💜', 
'geranio' => '🌺', 'cactus' => '🌵', 'orchidea' => '🌸',
// ... +40 emoji mappings
```

---

### 2. Vista: `resources/views/store/analytics/cdn-simple.blade.php`

#### Funzione JavaScript `loadFrequentQuestions()` - Aggiornata
**Prima:**
```javascript
// Sample data hardcoded
const sampleQuestions = [
    { question: "Come curare le piante grasse?", count: 15 },
    // ...
];
displayFrequentQuestions(sampleQuestions);
```

**Dopo:**
```javascript
// Real data from controller
const questions = @json($frequentQuestions ?? []);

if (questions.length === 0) {
    // Show empty state
} else {
    displayFrequentQuestions(questions);
}
```

#### Funzione JavaScript `loadPopularPlants()` - Aggiornata
**Prima:**
```javascript
// Sample data hardcoded
const samplePlants = [
    { name: "Rosa", count: 25, emoji: "🌹" },
    // ...
];
displayPopularPlants(samplePlants);
```

**Dopo:**
```javascript
// Real data from controller
const plants = @json($popularPlants ?? []);

if (plants.length === 0) {
    // Show empty state
} else {
    displayPopularPlants(plants);
}
```

---

## Struttura Dati

### Frequent Questions Array
```json
[
  {
    "question": "Come si cura una rosa?",
    "count": 2
  },
  {
    "question": "Quando annaffiare il basilico?",
    "count": 2
  }
]
```

### Popular Plants Array
```json
[
  {
    "name": "Basilico",
    "count": 3,
    "emoji": "🌿"
  },
  {
    "name": "Rosa",
    "count": 2,
    "emoji": "🌹"
  }
]
```

---

## Database Schema Utilizzato

### Tabella: `interactions`
- `store_id` - Filtro per store
- `question` - Testo domanda utente (nullable)
- `answer` - Risposta AI (nullable)
- `created_at` - Timestamp

### Tabella: `chat_logs`
- `store_id` - Filtro per store
- `user_message` - Messaggio utente
- `ai_response` - Risposta AI
- `created_at` - Timestamp

---

## Testing

### Script di Test Creati

#### 1. `test-analytics-data.php`
Verifica i dati generati dai nuovi metodi:
```bash
php test-analytics-data.php
```

Output:
```
🧪 TEST ANALYTICS DATA
🏪 Store: Store 01 (ID: 1)

📋 TEST 1: Domande Frequenti
Totale domande uniche: 9
  • Come si cura una rosa? (2 volte)
  • Quando annaffiare il basilico? (2 volte)

🌱 TEST 2: Piante Più Ricercate
Totale piante trovate: 8
  • Basilico (3 menzioni)
  • Rosa (2 menzioni)
```

#### 2. `generate-analytics-test-data.php`
Genera dati di esempio per testing:
```bash
php generate-analytics-test-data.php
```

Crea:
- 10 interactions con domande diverse (2 duplicate per test)
- 8 chat logs con menzioni di piante

---

## Risultati Test

### Database Counts (dopo test data generation)
- **Total Interactions**: 11
- **Total ChatLogs**: 9
- **Domande Uniche**: 9
- **Piante Trovate**: 8

### Top Questions
1. Come si cura una rosa? (2)
2. Quando annaffiare il basilico? (2)
3. Quali sono le piante grasse più facili? (1)
4. Come potare la lavanda? (1)

### Top Plants
1. Basilico (3 menzioni) 🌿
2. Rosa (2 menzioni) 🌹
3. Lavanda (2 menzioni) 💜
4. Orchidea (2 menzioni) 🌸

---

## Vantaggi Implementazione

✅ **Dati Reali**: Niente più placeholder hardcoded  
✅ **Doppia Ricerca**: Query su interactions + chat_logs per massima copertura  
✅ **Case Insensitive**: LIKE queries per matching flessibile  
✅ **Emoji Visual**: 47 piante mappate con emoji per UI accattivante  
✅ **Top N**: Limit a 10 domande e 8 piante per performance  
✅ **Graceful Fallback**: Empty state se nessun dato disponibile  

---

## Note Tecniche

### Performance
- Query con `groupBy` e `orderByDesc` ottimizzate con indici su `store_id`
- Limit implicito (10 questions, 8 plants) previene query lunghe
- LIKE queries potrebbero essere ottimizzate con full-text search in futuro

### Future Improvements
- ⏳ Full-text search invece di LIKE queries
- ⏳ Cache dei risultati (Redis) con TTL 1 ora
- ⏳ Sinonimi piante (es. "rose" = "rosa")
- ⏳ Stemming/lemmatization per keyword matching migliore
- ⏳ Chart.js visualizzazioni per trend temporali

---

## Accesso

**URL**: `http://localhost:8000/store/analytics`  
**Auth**: Store guard (login come store)

---

**Status**: ✅ Pronto per test in local  
**Git**: 🚫 Non pushato (come da richiesta utente)
