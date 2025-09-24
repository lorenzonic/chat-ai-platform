<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class UpdatePlantTrends extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'trends:update
                            {--force : Forza l\'aggiornamento anche se già eseguito oggi}
                            {--show-stats : Mostra statistiche dettagliate al termine}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Aggiorna i Google Trends relativi al mondo delle piante nel database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🌱 Avvio aggiornamento Google Trends per piante...');

        try {
            // Verifica se il comando è già stato eseguito oggi (a meno che non sia forzato)
            if (!$this->option('force') && $this->wasExecutedToday()) {
                $this->warn('⚠️  Aggiornamento già eseguito oggi. Usa --force per forzare l\'esecuzione.');
                return Command::SUCCESS;
            }

            // Path dello script Python
            $scriptPath = base_path('scripts/update_plant_trends.py');

            if (!file_exists($scriptPath)) {
                $this->error("❌ Script Python non trovato: {$scriptPath}");
                Log::error('Script Python update_plant_trends.py non trovato', [
                    'path' => $scriptPath
                ]);
                return Command::FAILURE;
            }

            // Ottieni il path dell'eseguibile Python
            $pythonExecutable = $this->getPythonExecutable();

            // Crea il processo per eseguire lo script Python
            $process = new Process([
                $pythonExecutable,
                $scriptPath
            ], base_path());

            // Imposta timeout di 10 minuti
            $process->setTimeout(600);

            // Imposta le variabili d'ambiente
            $process->setEnv([
                'DB_HOST' => config('database.connections.mysql.host'),
                'DB_DATABASE' => config('database.connections.mysql.database'),
                'DB_USERNAME' => config('database.connections.mysql.username'),
                'DB_PASSWORD' => config('database.connections.mysql.password'),
                'DB_PORT' => config('database.connections.mysql.port'),
                'PLANT_TRENDS_LOG' => storage_path('logs/plant_trends.log'),
                'PLANT_TRENDS_CONFIG' => base_path('plant_trends_config.json'),
            ]);

            $this->info('🚀 Esecuzione script Python in corso...');

            // Esegui il processo
            $process->run(function ($type, $buffer) {
                if ($this->output->isVerbose()) {
                    if (Process::ERR === $type) {
                        $this->error($buffer);
                    } else {
                        $this->line($buffer);
                    }
                }
            });

            // Verifica il risultato
            if ($process->isSuccessful()) {
                $this->info('✅ Aggiornamento Google Trends completato con successo!');

                // Log del successo
                Log::info('Aggiornamento Google Trends completato con successo', [
                    'command' => 'trends:update',
                    'execution_time' => $process->getIdleTimeout(),
                    'output' => $process->getOutput()
                ]);

                // Salva il timestamp dell'ultima esecuzione
                $this->saveExecutionTimestamp();

                // Mostra sempre il riassunto finale dal log Python, se presente
                $this->showPythonLogSummary();
                // Mostra statistiche Laravel se richiesto
                if ($this->option('show-stats')) {
                    $this->showTrendsStatistics();
                }

                return Command::SUCCESS;

            } else {
                $this->error('❌ Errore durante l\'aggiornamento Google Trends');
                $this->error('Output: ' . $process->getOutput());
                $this->error('Errori: ' . $process->getErrorOutput());

                // Log dell'errore
                Log::error('Errore durante l\'aggiornamento Google Trends', [
                    'command' => 'trends:update',
                    'exit_code' => $process->getExitCode(),
                    'output' => $process->getOutput(),
                    'errors' => $process->getErrorOutput()
                ]);

                // Mostra le ultime 10 righe del log Python per debug
                $this->showPythonLogTail();

                return Command::FAILURE;
            }

        } catch (ProcessFailedException $e) {
            $this->error('❌ Errore nell\'esecuzione del processo: ' . $e->getMessage());
            Log::error('Errore ProcessFailedException nell\'aggiornamento trends', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return Command::FAILURE;

        } catch (\Exception $e) {
            $this->error('❌ Errore generico: ' . $e->getMessage());
            Log::error('Errore generico nell\'aggiornamento trends', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return Command::FAILURE;
        }
    }

    /**
     * Ottiene il path dell'eseguibile Python.
     */
    private function getPythonExecutable(): string
    {
        // Prova diversi path comuni per Python su Windows
        $possiblePaths = [
            'C:/Users/Lorenzo/AppData/Local/Programs/Python/Python313/python.exe',
            'C:/Python313/python.exe',
            'C:/Python312/python.exe',
            'C:/Python311/python.exe',
            'python',
            'python3'
        ];

        foreach ($possiblePaths as $path) {
            if (file_exists($path) || $path === 'python' || $path === 'python3') {
                return $path;
            }
        }

        // Fallback
        return 'python';
    }

    /**
     * Verifica se il comando è già stato eseguito oggi.
     */
    private function wasExecutedToday(): bool
    {
        $timestampFile = storage_path('app/trends_last_execution.txt');

        if (!file_exists($timestampFile)) {
            return false;
        }

        $lastExecution = file_get_contents($timestampFile);
        $lastExecutionDate = date('Y-m-d', strtotime($lastExecution));
        $today = date('Y-m-d');

        return $lastExecutionDate === $today;
    }

    /**
     * Salva il timestamp dell'ultima esecuzione.
     */
    private function saveExecutionTimestamp(): void
    {
        $timestampFile = storage_path('app/trends_last_execution.txt');
        $directory = dirname($timestampFile);

        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        file_put_contents($timestampFile, now()->toDateTimeString());
    }

    /**
     * Mostra statistiche sui trends raccolti.
     */
    private function showTrendsStatistics(): void
    {
        try {
            $todayCount = \DB::table('trending_keywords')
                ->whereDate('collected_at', today())
                ->count();

            $totalCount = \DB::table('trending_keywords')->count();

            $topKeywords = \DB::table('trending_keywords')
                ->whereDate('collected_at', today())
                ->orderBy('score', 'desc')
                ->limit(5)
                ->pluck('keyword', 'score');

            $this->line('');
            $this->info('📊 Statistiche aggiornamento:');
            $this->line("📈 Keywords raccolte oggi: {$todayCount}");
            $this->line("📚 Totale keywords in database: {$totalCount}");

            if ($topKeywords->isNotEmpty()) {
                $this->line('');
                $this->info('🏆 Top 5 keywords di oggi:');
                foreach ($topKeywords as $score => $keyword) {
                    $this->line("   • {$keyword} (score: {$score})");
                }
            }

        } catch (\Exception $e) {
            $this->warn('⚠️  Impossibile recuperare le statistiche: ' . $e->getMessage());
        }
    }

    /**
     * Mostra le ultime 10 righe del log Python per debug errori.
     */
    private function showPythonLogTail(): void
    {
        $logFile = storage_path('logs/plant_trends.log');
        if (file_exists($logFile)) {
            $lines = file($logFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            $tail = array_slice($lines, -10);
            $this->warn('--- Ultime righe log Python ---');
            foreach ($tail as $line) {
                $this->line($line);
            }
            $this->warn('-----------------------------');
        }
    }

    /**
     * Mostra il riassunto finale (reali/simulati) dal log Python, se presente.
     */
    private function showPythonLogSummary(): void
    {
        $logFile = storage_path('logs/plant_trends.log');
        if (file_exists($logFile)) {
            $lines = file($logFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            $summary = null;
            foreach (array_reverse($lines) as $line) {
                if (strpos($line, 'Totale reali:') !== false || strpos($line, 'totali simulati:') !== false) {
                    $summary = $line;
                    break;
                }
            }
            if ($summary) {
                $this->info($summary);
            }
        }
    }
}
