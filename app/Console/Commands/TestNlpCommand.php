<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\NLPService;

class TestNlpCommand extends Command
{
    protected $signature = 'nlp:test {text?}';
    protected $description = 'Test the NLP service with sample or custom text';

    public function handle(NLPService $nlpService)
    {
        $testTexts = [
            'La mia monstera ha le foglie gialle',
            'Dove posso comprare un ficus?',
            'Che pianta è questa?',
            'Consigli per piante da appartamento?',
            'Aiuto! La mia pianta sta morendo!',
            'Grazie per i bellissimi consigli!'
        ];

        $customText = $this->argument('text');

        if ($customText) {
            $testTexts = [$customText];
        }

        $this->info('🧠 Testing NLP Service...');
        $this->newLine();

        foreach ($testTexts as $text) {
            $this->info("Testing: \"{$text}\"");

            $startTime = microtime(true);
            $analysis = $nlpService->analyzeText($text);
            $endTime = microtime(true);

            $this->line("📊 Results:");
            $this->line("  Intent: {$analysis['intent']} (confidence: {$analysis['intent_confidence']})");

            if (isset($analysis['sentiment'])) {
                $sentiment = $analysis['sentiment'];
                $this->line("  Sentiment: {$sentiment['sentiment']} (confidence: {$sentiment['confidence']})");
            }

            $this->line("  Keywords: " . implode(', ', array_slice($analysis['keywords'], 0, 5)));
            $this->line("  Entities: " . count($analysis['entities']));
            $this->line("  Source: {$analysis['source']}");
            $this->line("  Processing time: " . round(($endTime - $startTime) * 1000, 2) . "ms");

            if (!empty($analysis['suggestions'])) {
                $this->line("  Suggestions:");
                foreach (array_slice($analysis['suggestions'], 0, 3) as $suggestion) {
                    $this->line("    - {$suggestion}");
                }
            }

            $this->newLine();
        }

        // Test question trends if we have data
        $this->info('📈 Testing question trends analysis...');
        $trends = $nlpService->analyzeQuestionTrends(1, 7); // Store ID 1, last 7 days

        $this->line("Total questions analyzed: {$trends['total_questions']}");
        $this->line("Top intent: {$trends['top_intent']}");

        if (!empty($trends['intent_distribution'])) {
            $this->line("Intent distribution:");
            foreach (array_slice($trends['intent_distribution'], 0, 5) as $intent) {
                $this->line("  - {$intent->intent}: {$intent->count} questions");
            }
        }

        $this->newLine();
        $this->info('✅ NLP Service test completed!');
    }
}
