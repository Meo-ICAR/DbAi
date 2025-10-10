<?php

declare(strict_types=1);

namespace App\Neuron\Agents;

use NeuronAI\Agent;
use NeuronAI\SystemPrompt;
use NeuronAI\Providers\AIProviderInterface;
use NeuronAI\Chat\Messages\UserMessage;
use NeuronAI\Providers\Gemini\Gemini;
use NeuronAI\Providers\HttpClientOptions;
use Illuminate\Support\Facades\DB;
use NeuronAI\Tools\Toolkits\MySQL\MySQLToolkit;
use App\Neuron\Tools\LoggingMySQLSelectTool;
use NeuronAI\Tools\Toolkits\MySQL\MySQLSchemaTool;
//use NeuronAI\Tools\Toolkits\MySQL\MySQLSelectTool;



class DataAnalystAgent extends Agent
{
     protected function provider(): AIProviderInterface
    {
         return new Gemini(
            key: env('GEMINI_API_KEY'),
            model: env('GEMINI_MODEL', 'gemini-2.5-flash'),
            parameters: [], // Add custom params (temperature, logprobs, etc)
            httpOptions: new HttpClientOptions(timeout: 30),
        );
    }

    public function instructions(): string
    {
        return (string) new SystemPrompt(
            background: [
                "You are a helpful AI assistant powered by Google's Gemini AI model.",
                "You are integrated into a Laravel 12 application using MySQL and the NeuronAI framework.",
                "You can help users with various tasks including answering questions, providing information, and assisting with problem-solving.",
                "You have access to the database schema and can run SELECT queries to fetch data.",
                "You can help users write and optimize SQL queries.",
                 "If a user asks to see data, you can query the database and display the results in a clear, readable format.",
                 "You are fluent in Italian and will respond in the same language as the user's question.",
                "If the user asks in Italian, respond in Italian. If in English, respond in English.",

            ],
        );
    }

    protected function tools(): array
    {
        $pdo = \DB::connection()->getPdo();

        // Create our custom select tool
        $selectTool = LoggingMySQLSelectTool::make($pdo);

        // Create a custom toolkit with our logging select tool
        return [
            MySQLSchemaTool::make($pdo),  // Keep the schema tool as is
            $selectTool,
        ];
    }

    public static function getQueryLog(): array
    {
        return LoggingMySQLSelectTool::getQueryLog();
    }

    public static function getLastQuery(): ?array
    {
        return LoggingMySQLSelectTool::getLastQuery();
    }

    /**
     * Clear the query log
     */
    public static function clearQueryLog(): void
    {
        LoggingMySQLSelectTool::clearQueryLog();
    }
}
