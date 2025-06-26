<?php

namespace App\Services;

use App\Models\Store;
use App\Models\StoreKnowledge;

class KnowledgeSearchService
{
    /**
     * Cerca una risposta nella knowledge base dello store
     */
    public function searchKnowledge(Store $store, string $query): ?StoreKnowledge
    {
        $query = strtolower(trim($query));

        // Prima cerca una corrispondenza esatta nella domanda
        $exactMatch = $store->knowledgeItems()
            ->active()
            ->whereRaw('LOWER(question) LIKE ?', ['%' . $query . '%'])
            ->byPriority()
            ->first();

        if ($exactMatch) {
            return $exactMatch;
        }

        // Poi cerca nelle keywords
        $keywordMatch = $store->knowledgeItems()
            ->active()
            ->byPriority()
            ->get()
            ->filter(function ($item) use ($query) {
                if (!$item->keywords) return false;

                foreach ($item->keywords as $keyword) {
                    if (stripos($query, strtolower($keyword)) !== false ||
                        stripos(strtolower($keyword), $query) !== false) {
                        return true;
                    }
                }
                return false;
            })
            ->first();

        if ($keywordMatch) {
            return $keywordMatch;
        }

        // Infine cerca una corrispondenza parziale nell'answer
        $answerMatch = $store->knowledgeItems()
            ->active()
            ->whereRaw('LOWER(answer) LIKE ?', ['%' . $query . '%'])
            ->byPriority()
            ->first();

        return $answerMatch;
    }

    /**
     * Cerca multiple risposte nella knowledge base
     */
    public function searchMultipleKnowledge(Store $store, string $query, int $limit = 3): array
    {
        $query = strtolower(trim($query));
        $results = [];

        // Cerca corrispondenze nelle domande
        $questionMatches = $store->knowledgeItems()
            ->active()
            ->whereRaw('LOWER(question) LIKE ?', ['%' . $query . '%'])
            ->byPriority()
            ->limit($limit)
            ->get();

        foreach ($questionMatches as $match) {
            $results[] = [
                'item' => $match,
                'relevance' => $this->calculateRelevance($query, $match),
                'match_type' => 'question'
            ];
        }

        // Se non abbiamo abbastanza risultati, cerca nelle keywords
        if (count($results) < $limit) {
            $keywordMatches = $store->knowledgeItems()
                ->active()
                ->byPriority()
                ->get()
                ->filter(function ($item) use ($query) {
                    if (!$item->keywords) return false;

                    foreach ($item->keywords as $keyword) {
                        if (stripos($query, strtolower($keyword)) !== false ||
                            stripos(strtolower($keyword), $query) !== false) {
                            return true;
                        }
                    }
                    return false;
                })
                ->take($limit - count($results));

            foreach ($keywordMatches as $match) {
                $results[] = [
                    'item' => $match,
                    'relevance' => $this->calculateRelevance($query, $match),
                    'match_type' => 'keyword'
                ];
            }
        }

        // Ordina per rilevanza
        usort($results, function ($a, $b) {
            return $b['relevance'] <=> $a['relevance'];
        });

        return array_slice($results, 0, $limit);
    }

    /**
     * Calcola la rilevanza di una corrispondenza
     */
    private function calculateRelevance(string $query, StoreKnowledge $item): float
    {
        $relevance = 0;
        $query = strtolower($query);

        // Rilevanza basata sulla domanda
        $question = strtolower($item->question);
        if (stripos($question, $query) !== false) {
            $relevance += 10;
            // Bonus se la query è all'inizio della domanda
            if (stripos($question, $query) === 0) {
                $relevance += 5;
            }
        }

        // Rilevanza basata sulle keywords
        if ($item->keywords) {
            foreach ($item->keywords as $keyword) {
                $keyword = strtolower($keyword);
                if (stripos($query, $keyword) !== false || stripos($keyword, $query) !== false) {
                    $relevance += 3;
                    // Bonus per corrispondenza esatta
                    if ($keyword === $query) {
                        $relevance += 5;
                    }
                }
            }
        }

        // Bonus per priorità
        $relevance += $item->priority * 0.5;

        return $relevance;
    }
}
