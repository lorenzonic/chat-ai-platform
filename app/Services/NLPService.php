<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Cache;

class NLPService
{
    private const CACHE_TTL = 3600; // 1 ora di cache per analisi identiche
    private const PYTHON_TIMEOUT = 30; // Timeout per script Python

    /**
     * Analizza il testo dell'utente con spaCy avanzato e caching
     */
    public function analyzeText(string $text): array
    {
        // Genera chiave cache basata sul testo
        $cacheKey = 'nlp_analysis_' . md5(trim(strtolower($text)));

        // Prova a recuperare dalla cache
        $cachedResult = Cache::get($cacheKey);
        if ($cachedResult) {
            Log::info('NLP analysis from cache', ['cache_key' => $cacheKey]);
            return $cachedResult;
        }

        try {
            $scriptPath = base_path('scripts/spacy_nlp.py');

            if (!file_exists($scriptPath)) {
                Log::warning('spaCy script not found', ['path' => $scriptPath]);
                return $this->getFallbackAnalysis($text);
            }

            // Esegui script Python con timeout
            $pythonPath = env('PYTHON_EXECUTABLE_PATH', 'python');
            $result = Process::timeout(self::PYTHON_TIMEOUT)->run([
                $pythonPath,
                $scriptPath,
                $text
            ]);

            if ($result->successful()) {
                $analysis = json_decode($result->output(), true);

                if ($analysis && !isset($analysis['error'])) {
                    // Aggiungi metadati
                    $analysis['processing_time'] = microtime(true);
                    $analysis['cache_key'] = $cacheKey;

                    Log::info('spaCy analysis completed', [
                        'intent' => $analysis['intent'] ?? 'unknown',
                        'intent_confidence' => $analysis['intent_confidence'] ?? 0,
                        'sentiment' => $analysis['sentiment']['sentiment'] ?? 'neutral',
                        'keywords_count' => count($analysis['keywords'] ?? []),
                        'entities_count' => count($analysis['entities'] ?? []),
                        'text_length' => strlen($text)
                    ]);

                    // Salva in cache solo se l'analisi è di buona qualità
                    if (($analysis['intent_confidence'] ?? 0) > 0.3) {
                        Cache::put($cacheKey, $analysis, self::CACHE_TTL);
                    }

                    return $analysis;
                }
            }

            Log::warning('spaCy analysis failed', [
                'exit_code' => $result->exitCode(),
                'error_output' => $result->errorOutput(),
                'text_preview' => substr($text, 0, 100)
            ]);

            return $this->getFallbackAnalysis($text);

        } catch (\Exception $e) {
            Log::error('spaCy analysis exception', [
                'error' => $e->getMessage(),
                'text_preview' => substr($text, 0, 100)
            ]);

            return $this->getFallbackAnalysis($text);
        }
    }

    /**
     * Analisi di fallback potenziata quando spaCy non è disponibile
     */
    private function getFallbackAnalysis(string $text): array
    {
        $text_lower = strtolower($text);

        // Pattern più sofisticati per il fallback
        $intentAnalysis = $this->detectIntentFallback($text_lower);
        $sentiment = $this->analyzeSentimentFallback($text_lower);
        $keywords = $this->extractKeywordsFallback($text);
        $entities = $this->extractEntitiesFallback($text);

        return [
            'entities' => $entities,
            'keywords' => $keywords,
            'intent' => $intentAnalysis['intent'],
            'intent_confidence' => $intentAnalysis['confidence'],
            'sentiment' => $sentiment,
            'suggestions' => $this->generateSuggestions($intentAnalysis['intent'], $keywords),
            'text' => $text,
            'text_length' => strlen($text),
            'token_count' => str_word_count($text),
            'source' => 'fallback_advanced'
        ];
    }

    /**
     * Rilevamento intent avanzato per fallback
     */
    private function detectIntentFallback(string $text_lower): array
    {
        $patterns = [
            'cura' => [
                'keywords' => ['cura', 'curare', 'malattia', 'foglie gialle', 'parassiti', 'concime', 'annaffiare'],
                'patterns' => ['/come.*cur/', '/problemi.*con/', '/foglie.*gial/', '/sta.*morendo/'],
                'weight' => 1.0
            ],
            'acquisto' => [
                'keywords' => ['comprare', 'acquistare', 'prezzo', 'vendita', 'negozio', 'ordinare'],
                'patterns' => ['/quanto.*cost/', '/dove.*compr/', '/prezz/', '/acquist/'],
                'weight' => 1.0
            ],
            'identificazione' => [
                'keywords' => ['che pianta', 'identifica', 'nome', 'specie', 'tipo'],
                'patterns' => ['/che.*piant/', '/identific/', '/nome.*piant/'],
                'weight' => 1.0
            ],
            'consiglio' => [
                'keywords' => ['consiglio', 'suggerisci', 'quale', 'migliore', 'adatta'],
                'patterns' => ['/quale.*piant/', '/consigli/', '/suggerisc/'],
                'weight' => 1.0
            ],
            'problemi' => [
                'keywords' => ['aiuto', 'problema', 'urgente', 'emergenza', 'salvare'],
                'patterns' => ['/aiut/', '/problem/', '/urgent/', '/sta.*morend/'],
                'weight' => 1.2
            ]
        ];

        $scores = [];
        foreach ($patterns as $intent => $config) {
            $score = 0;

            // Score da keywords
            foreach ($config['keywords'] as $keyword) {
                if (strpos($text_lower, $keyword) !== false) {
                    $score += $config['weight'];
                }
            }

            // Score da pattern
            foreach ($config['patterns'] as $pattern) {
                if (preg_match($pattern, $text_lower)) {
                    $score += $config['weight'] * 1.5;
                }
            }

            $scores[$intent] = $score;
        }

        $bestIntent = array_keys($scores, max($scores))[0];
        $confidence = max($scores) > 0 ? min(max($scores) / 3, 1.0) : 0.0;

        return [
            'intent' => $bestIntent ?: 'altro',
            'confidence' => round($confidence, 2)
        ];
    }

    /**
     * Analisi sentiment per fallback
     */
    private function analyzeSentimentFallback(string $text_lower): array
    {
        $positive = ['bene', 'bello', 'perfetto', 'grazie', 'ottimo', 'eccellente', 'fantastico', 'felice'];
        $negative = ['male', 'brutto', 'problema', 'aiuto', 'morendo', 'malata', 'triste', 'urgent'];
        $neutral = ['come', 'cosa', 'dove', 'quando', 'perché', 'quale'];

        $pos_count = 0;
        $neg_count = 0;
        $neu_count = 0;

        foreach ($positive as $word) {
            if (strpos($text_lower, $word) !== false) $pos_count++;
        }
        foreach ($negative as $word) {
            if (strpos($text_lower, $word) !== false) $neg_count++;
        }
        foreach ($neutral as $word) {
            if (strpos($text_lower, $word) !== false) $neu_count++;
        }

        $total = $pos_count + $neg_count + $neu_count;

        if ($total == 0) {
            return [
                'sentiment' => 'neutral',
                'confidence' => 0.5,
                'scores' => ['positive' => 0.33, 'negative' => 0.33, 'neutral' => 0.34]
            ];
        }

        $pos_score = $pos_count / $total;
        $neg_score = $neg_count / $total;
        $neu_score = $neu_count / $total;

        if ($pos_score > $neg_score && $pos_score > $neu_score) {
            $sentiment = 'positive';
            $confidence = $pos_score;
        } elseif ($neg_score > $pos_score && $neg_score > $neu_score) {
            $sentiment = 'negative';
            $confidence = $neg_score;
        } else {
            $sentiment = 'neutral';
            $confidence = $neu_score;
        }

        return [
            'sentiment' => $sentiment,
            'confidence' => round($confidence, 2),
            'scores' => [
                'positive' => round($pos_score, 2),
                'negative' => round($neg_score, 2),
                'neutral' => round($neu_score, 2)
            ]
        ];
    }

    /**
     * Estrazione keywords per fallback
     */
    private function extractKeywordsFallback(string $text): array
    {
        // Rimuovi parole vuote italiane
        $stopWords = ['il', 'la', 'di', 'che', 'e', 'a', 'un', 'una', 'per', 'con', 'su', 'da', 'come', 'ma', 'se', 'anche', 'più', 'nella', 'delle', 'del'];

        $words = str_word_count(strtolower($text), 1, 'àèéìíîòóùú');
        $keywords = array_filter($words, function($word) use ($stopWords) {
            return !in_array($word, $stopWords) && strlen($word) > 2;
        });

        return array_slice(array_unique($keywords), 0, 10);
    }

    /**
     * Estrazione entità per fallback
     */
    private function extractEntitiesFallback(string $text): array
    {
        $entities = [];
        $text_lower = strtolower($text);

        // Entità piante comuni
        $plants = ['ficus', 'monstera', 'pothos', 'orchidea', 'cactus', 'basilico', 'rosmarino'];
        foreach ($plants as $plant) {
            if (strpos($text_lower, $plant) !== false) {
                $entities[] = [
                    'text' => $plant,
                    'label' => 'PLANT_NAME',
                    'confidence' => 0.7
                ];
            }
        }

        return $entities;
    }

    /**
     * Genera suggerimenti basati sull'intent rilevato (versione potenziata)
     */
    public function generateSuggestions(string $intent, array $keywords = []): array
    {
        $suggestions = [];

        switch ($intent) {
            case 'cura':
                $suggestions = [
                    '💧 Come innaffiare correttamente?',
                    '🌱 Problemi con concimazione?',
                    '🍃 Foglie che ingialliscono?',
                    '🐛 Parassiti e malattie?',
                    '✂️ Quando e come potare?'
                ];
                break;

            case 'acquisto':
                $suggestions = [
                    '🛒 Catalogo piante disponibili',
                    '💰 Prezzi e offerte speciali',
                    '🚚 Spedizione e consegna',
                    '🎁 Pacchetti regalo',
                    '📞 Contatti per ordini'
                ];
                break;

            case 'identificazione':
                $suggestions = [
                    '📸 Carica una foto della pianta',
                    '📝 Descrivi foglie e fiori',
                    '📏 Dimensioni della pianta',
                    '🏡 Dove l\'hai vista?',
                    '🔍 Cerca nel nostro database'
                ];
                break;

            case 'consiglio':
                $suggestions = [
                    '🏠 Piante per interno o esterno?',
                    '☀️ Che luce è disponibile?',
                    '⏰ Tempo disponibile per la cura?',
                    '🐕 Hai animali domestici?',
                    '🌡️ Temperatura della casa?'
                ];
                break;

            case 'ambiente':
                $suggestions = [
                    '☀️ Piante per pieno sole',
                    '🌑 Piante per zone ombreggiate',
                    '🏡 Perfette per appartamento',
                    '🌿 Ideali per balconi',
                    '💧 Che amano l\'umidità'
                ];
                break;

            case 'problemi':
                $suggestions = [
                    '🆘 Descrivi il problema',
                    '📸 Mostra una foto',
                    '⏰ Da quando lo noti?',
                    '🔍 Sintomi specifici?',
                    '📞 Contatto urgente'
                ];
                break;

            default:
                $suggestions = [
                    '🌱 Cura delle piante',
                    '🛒 Acquisto e prezzi',
                    '🔍 Identificazione piante',
                    '💡 Consigli personalizzati',
                    '🌿 Piante per la tua casa'
                ];
        }

        // Suggerimenti basati su keywords specifiche
        foreach ($keywords as $keyword) {
            $plantSuggestions = $this->getPlantSpecificSuggestions($keyword);
            if (!empty($plantSuggestions)) {
                $suggestions = array_merge($suggestions, $plantSuggestions);
                break; // Solo uno per evitare troppe opzioni
            }
        }

        return array_slice(array_unique($suggestions), 0, 5);
    }

    /**
     * Suggerimenti specifici per pianta
     */
    private function getPlantSpecificSuggestions(string $keyword): array
    {
        $plantSuggestions = [
            'ficus' => ['🌿 Tutto sul Ficus', '💧 Come innaffiare il Ficus', '☀️ Posizione ideale per Ficus'],
            'monstera' => ['🍃 Cura della Monstera', '🌱 Propagazione Monstera', '✂️ Potatura Monstera'],
            'orchidea' => ['🌺 Cura delle Orchidee', '💧 Innaffiatura Orchidee', '🌸 Fioritura Orchidee'],
            'cactus' => ['🌵 Cura dei Cactus', '☀️ Luce per Cactus', '💧 Innaffiatura Cactus'],
            'basilico' => ['🌿 Coltivare Basilico', '✂️ Raccolta Basilico', '🌱 Basilico in vaso'],
        ];

        foreach ($plantSuggestions as $plant => $suggestions) {
            if (strpos(strtolower($keyword), $plant) !== false) {
                return $suggestions;
            }
        }

        return [];
    }

    /**
     * Analizza le tendenze delle domande per miglioramenti futuri
     */
    public function analyzeQuestionTrends(int $storeId, int $days = 7): array
    {
        try {
            // Query per analizzare le domande più frequenti
            $trends = \DB::table('chat_logs')
                ->where('store_id', $storeId)
                ->where('created_at', '>=', now()->subDays($days))
                ->whereNotNull('metadata->nlp_analysis->intent')
                ->selectRaw('
                    JSON_EXTRACT(metadata, "$.nlp_analysis.intent") as intent,
                    COUNT(*) as count,
                    AVG(JSON_EXTRACT(metadata, "$.nlp_analysis.intent_confidence")) as avg_confidence
                ')
                ->groupBy('intent')
                ->orderBy('count', 'desc')
                ->get();

            return [
                'period_days' => $days,
                'total_questions' => $trends->sum('count'),
                'intent_distribution' => $trends->toArray(),
                'top_intent' => $trends->first()?->intent ?? 'cura',
                'generated_at' => now()->toISOString()
            ];

        } catch (\Exception $e) {
            Log::error('Question trends analysis failed', ['error' => $e->getMessage()]);
            return [
                'period_days' => $days,
                'total_questions' => 0,
                'intent_distribution' => [],
                'top_intent' => 'cura',
                'error' => 'Analysis failed'
            ];
        }
    }

    /**
     * Salva l'analisi per miglioramenti futuri
     */
    public function saveAnalysis(string $text, array $analysis, ?int $storeId = null): void
    {
        try {
            // Potresti salvare in una tabella nlp_analyses per training futuro
            Log::info('NLP Analysis saved', [
                'store_id' => $storeId,
                'intent' => $analysis['intent'],
                'keywords_count' => count($analysis['keywords'] ?? []),
                'has_entities' => count($analysis['entities'] ?? []) > 0,
                'text_length' => strlen($text)
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to save NLP analysis', ['error' => $e->getMessage()]);
        }
    }
}
