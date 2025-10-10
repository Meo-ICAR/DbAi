<?php

namespace App\Console\Commands;

use App\Neuron\Agents\DataAnalystAgent;
use NeuronAI\Chat\Messages\UserMessage;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use NeuronAI\Tools\Toolkits\MySQL\MySQLSelectTool;
use NeuronAI\Tools\PropertyType;
use NeuronAI\Tools\Tool;
use NeuronAI\Tools\ToolProperty;

class DbDemo extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'neuronai:dbdemo {--interactive : Run interactive demo}';

    /**
     * The console command description.
     */
    protected $description = 'Demonstrate NeuronAI database interaction capabilities';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('🧠 NeuronAI Laravel DB Demo');
        $this->info('========================');

        if (!env('GOOGLE_API_KEY')) {
            $this->error('❌ GOOGLE_API_KEY not set in .env file');
            $this->info('Please add your Google API configuration to the .env file:');
            $this->line('GOOGLE_API_KEY=your-google-api-key');
            $this->line('GEMINI_MODEL=gemini-2.5-flash');
            return 1;
        }

        $agent = new DataAnalystAgent();

        if ($this->option('interactive')) {
            return $this->interactiveMode($agent);
        }
        return $this->runAutomatedDemo($agent);

    }

    protected function interactiveMode(DataAnalystAgent $agent): int
    {
        $this->info("🤖 Interactive Chat with Database Agent");
        $this->info("Type \"quit\" to exit");

        while (true) {
            $message= $this->ask("\nYou:");

            if (strtolower($message) === 'quit') {
                break;
            }

            $this->info("\n🤖 Agent is thinking...");

            $maxRetries = 3;
            $retryCount = 0;
            $success = false;

            while ($retryCount < $maxRetries && !$success) {
                try {
                    $response = $agent->chat(new UserMessage($message));
                    $this->line("Agent: " . $response->getContent());
                    $this->newLine();
                    $success = true;
                } catch (\Exception $e) {
                    $errorMessage = $e->getMessage();
                    $isServiceUnavailable = str_contains($errorMessage, '503') ||
                                         str_contains(strtolower($errorMessage), 'service unavailable');

                    if ($isServiceUnavailable && $retryCount < $maxRetries - 1) {
                        $retryCount++;
                        $this->warn("⚠️  Service temporarily unavailable. Retrying ({$retryCount}/{$maxRetries})...");
                        sleep(2 * $retryCount); // Exponential backoff
                        continue;
                    }

                    $this->error("Error: " . ($isServiceUnavailable ?
                        'The AI service is currently overloaded. Please try again later.' :
                        $errorMessage));

                    if (!$isServiceUnavailable) {
                        return 1;
                    }
                    break;
                }
            }
        }

        return 0;
    }


    private function runAutomatedDemo(DataAnalystAgent $agent): int
    {
        $this->info('🤖 Running Automated Demo');
        $this->newLine();

        $testMessages = [
            "How many tables there are?",
            "Summarize monthly calls?",
            "Summarize calls by esito",
            "Summarize calls with esito is wrong",
        ];

        $maxRetries = 3;
        $retryDelay = 2; // seconds

        foreach ($testMessages as $index => $message) {
            // Clear previous query log
            DataAnalystAgent::clearQueryLog();
            
            $this->info("Test " . ($index + 1) . "/" . count($testMessages));
            $this->line("User: " . $message);

            $retryCount = 0;
            $success = false;

            while ($retryCount < $maxRetries && !$success) {
                try {
                    $response = $agent->chat(new UserMessage($message));
                    $this->line("Agent: " . $response->getContent());
                    
                    // Display the executed queries
                    $queries = DataAnalystAgent::getQueryLog();
                    if (!empty($queries)) {
                        $this->info("\nExecuted Queries:");
                        foreach ($queries as $i => $query) {
                            $this->line(sprintf(
                                "%d. [%s] %s", 
                                $i + 1, 
                                $query['timestamp'],
                                $this->formatQuery($query['query'], $query['params'] ?? [])
                            ));
                        }
                    }
                    
                    $this->newLine();
                    $success = true;

                    // Add a small delay for better readability
                    sleep(1);

                } catch (\Exception $e) {
                    $errorMessage = $e->getMessage();
                    $isServiceUnavailable = str_contains($errorMessage, '503') ||
                                         str_contains(strtolower($errorMessage), 'service unavailable');

                    if ($isServiceUnavailable && $retryCount < $maxRetries - 1) {
                        $retryCount++;
                        $this->warn("⚠️  Service temporarily unavailable. Retrying ({$retryCount}/{$maxRetries})...");
                        sleep($retryDelay * $retryCount);
                        continue;
                    }

                    $this->error('Error: ' . ($isServiceUnavailable ?
                        'The AI service is currently overloaded. Please try again later.' :
                        $errorMessage));
                    $this->newLine();
                    break;
                }
            }
        }

        $this->info('✅ Demo completed successfully!');
        return 0;
    }
    
    /**
     * Format SQL query with parameters for display
     */
    private function formatQuery(string $query, array $params): string
    {
        if (empty($params)) {
            return $query;
        }
        
        // Simple parameter binding for display purposes
        foreach ($params as $param => $value) {
            $query = str_replace(":$param", "'$value'", $query);
        }
        
        return $query;
    }
}
