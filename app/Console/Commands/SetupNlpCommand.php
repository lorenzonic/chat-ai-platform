<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Process;

class SetupNlpCommand extends Command
{
    protected $signature = 'nlp:setup';
    protected $description = 'Setup and configure NLP dependencies';

    public function handle()
    {
        $this->info('🔧 Setting up NLP environment...');
        $this->newLine();

        // Find Python executable
        $pythonPaths = [
            'python',
            'python3',
            'C:/Users/Lorenzo/AppData/Local/Programs/Python/Python313/python.exe',
            'C:/Python313/python.exe',
            'C:/Python/python.exe'
        ];

        $workingPython = null;
        foreach ($pythonPaths as $pythonPath) {
            try {
                $result = Process::run([$pythonPath, '--version']);
                if ($result->successful()) {
                    $this->info("✅ Found Python at: {$pythonPath}");
                    $this->line("   Version: " . trim($result->output()));
                    $workingPython = $pythonPath;
                    break;
                }
            } catch (\Exception $e) {
                continue;
            }
        }

        if (!$workingPython) {
            $this->error('❌ Python not found. Please install Python 3.8+');
            return 1;
        }

        // Test spaCy installation
        $this->info('🧠 Testing spaCy installation...');
        try {
            $result = Process::run([
                $workingPython,
                '-c',
                'import spacy; nlp = spacy.load("it_core_news_sm"); print("✅ spaCy OK")'
            ]);

            if ($result->successful()) {
                $this->info('✅ spaCy with Italian model is working');
            } else {
                $this->warn('⚠️  spaCy test failed:');
                $this->line($result->errorOutput());

                $this->info('Installing spaCy and Italian model...');
                Process::run([$workingPython, '-m', 'pip', 'install', 'spacy']);
                Process::run([$workingPython, '-m', 'spacy', 'download', 'it_core_news_sm']);
            }
        } catch (\Exception $e) {
            $this->error('❌ Error testing spaCy: ' . $e->getMessage());
        }

        // Test our NLP script
        $this->info('🧪 Testing NLP script...');
        $scriptPath = base_path('scripts/spacy_nlp.py');

        if (!file_exists($scriptPath)) {
            $this->error("❌ NLP script not found at: {$scriptPath}");
            return 1;
        }

        try {
            $result = Process::run([
                $workingPython,
                $scriptPath,
                'Test della mia orchidea con problemi alle foglie'
            ]);

            if ($result->successful()) {
                $analysis = json_decode($result->output(), true);
                if ($analysis && isset($analysis['source'])) {
                    $this->info("✅ NLP script working! Source: {$analysis['source']}");
                    $this->line("   Intent: {$analysis['intent']}");
                    $this->line("   Keywords: " . implode(', ', array_slice($analysis['keywords'], 0, 3)));
                } else {
                    $this->warn('⚠️  NLP script returned invalid JSON');
                    $this->line($result->output());
                }
            } else {
                $this->error('❌ NLP script failed:');
                $this->line($result->errorOutput());
            }
        } catch (\Exception $e) {
            $this->error('❌ Error running NLP script: ' . $e->getMessage());
        }

        // Write Python path to config
        $envPath = base_path('.env');
        $pythonConfigLine = "PYTHON_EXECUTABLE_PATH=\"{$workingPython}\"";

        if (file_exists($envPath)) {
            $envContent = file_get_contents($envPath);
            if (strpos($envContent, 'PYTHON_EXECUTABLE_PATH') !== false) {
                $envContent = preg_replace('/PYTHON_EXECUTABLE_PATH=.*/', $pythonConfigLine, $envContent);
            } else {
                $envContent .= "\n# NLP Configuration\n{$pythonConfigLine}\n";
            }
            file_put_contents($envPath, $envContent);
            $this->info("✅ Python path saved to .env");
        }

        $this->newLine();
        $this->info('🎉 NLP setup completed!');
        $this->line('You can now use advanced NLP features in your chatbot.');

        return 0;
    }
}
