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
        $currentDatabase = config('database.connections.mysql.database');
        $background = [
            "You are a helpful AI assistant powered by Google's Gemini AI model.",
            "You are integrated into a Laravel 12 application using MySQL and the NeuronAI framework.",
            "You can help users with various tasks including answering questions, providing information, and assisting with problem-solving.",
            "You have access to the database schema and can run SELECT queries to fetch data.",
            "You can help users write and optimize SQL queries.",
            "If a user asks to see data, you can query the database and display the results in a clear, readable format.",
            "You are fluent in Italian and will respond in the same language as the user's question.",
            "If the user asks in Italian, respond in Italian. If in English, respond in English.",
        ];

        if ($currentDatabase === 'proforma') {
            $more[] = "You are working with the 'proforma' database. This is a financial database containing invoice and accounting data. Business Logic Summary Agents (from the fornitoris table) submit loan applications (stored in the pratiches table). Banks (from the clientis table) pay our agency for these applications.
Commissions (in the provvigioni table) are generated. This table is linked to agents via the fornitori_id field and to banks via the clienti_id field. We (the agency) calculate the commissions for our agents and send them a proforma (from the proformas table). Based on our proforma, the agents send us their invoice (an incoming/passive invoice). This invoice is stored in the invoices table and is linked to the agent using the fornitori_id field.
Separately, the banks send us their proformas. We (the agency) then issue our invoice to them (an outgoing/active invoice). This invoice is also stored in the invoices table, but it is linked to the bank using the clienti_id field.
Key Data Tables: clientis: Represents the Banks (our clients). fornitoris: Represents the Agents (our suppliers/collaborators). provvigioni: The commissions generated from loan applications. invoices: This table holds both active (outgoing) invoices to the banks (linked by clienti_id) and passive (incoming) invoices from our agents (linked by fornitori_id). Instructions for Analysis Other tables are primarily for lookup purposes and have secondary indexes (foreign keys) linking to these main tables. Please ignore any system tables that have the word Laravel in their comments (e.g., cache, jobs, migrations).";
        } else {
            $more[] = "You are working with the '$currentDatabase' database. This is a medical database containing patientd and visits data.";
        }

        // Merge background and more arrays
        $allInstructions = array_merge($background, $more);

        return (string) new SystemPrompt(background: $allInstructions);
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
