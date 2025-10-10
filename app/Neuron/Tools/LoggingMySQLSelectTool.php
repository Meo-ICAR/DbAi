<?php

declare(strict_types=1);

namespace App\Neuron\Tools;

use NeuronAI\Tools\Toolkits\MySQL\MySQLSelectTool as BaseMySQLSelectTool;
use PDO;
use Illuminate\Support\Facades\Log;
use NeuronAI\Exceptions\ToolException;

class LoggingMySQLSelectTool extends BaseMySQLSelectTool
{
    protected static array $queryLog = [];

    /**
     * Get all logged queries
     */
    public static function getQueryLog(): array
    {
        return self::$queryLog;
    }

    /**
     * Clear the query log
     */
    public static function clearQueryLog(): void
    {
        self::$queryLog = [];
    }
    
    /**
     * Get the last executed query
     */
    public static function getLastQuery(): ?array
    {
        return !empty(self::$queryLog) ? end(self::$queryLog) : null;
    }
    
    /**
     * Execute a query with logging
     * 
     * @param string $query The SQL query to execute
     * @param array|null $parameters The query parameters
     * @return string|array The query results
     * @throws ToolException
     */
    public function __invoke(string $query, ?array $parameters = []): string|array
    {
        // Log the query before execution
        $logEntry = [
            'query' => $query,
            'params' => $parameters ?? [],
            'timestamp' => now()->toDateTimeString(),
        ];
        
        self::$queryLog[] = $logEntry;
        
        // Log to Laravel's log
        Log::debug('MySQL Query', $logEntry);
        
        // Call the parent implementation
        return parent::__invoke($query, $parameters);
    }
}
