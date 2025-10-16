<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class SetDatabaseConnection
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $host = $request->getHost();

        /*
        Log::info('SetDatabaseConnection middleware running', [
            'host' => $host,
            'current_database' => config('database.connections.mysql.database'),
            'time' => now()->toDateTimeString()
        ]);
*/
        if (str_ends_with($host, 'hassisto.com')) {
            $newDatabase = 'proforma';
            $currentDatabase = config('database.connections.mysql.database');

            // Only update if the database is different
            if ($currentDatabase !== $newDatabase) {
                /*
                Log::info("Switching database from {$currentDatabase} to {$newDatabase} for host: {$host}");
                 */
                config(['database.connections.mysql.database' => $newDatabase]);

                // Reconnect to the database with new settings
                DB::purge('mysql');
                DB::reconnect('mysql');
                /*
                Log::info('Database connection updated', [
                    'new_database' => config('database.connections.mysql.database')
                ]);
                */
            } else {
                /*
                Log::debug('Database already set to ' . $newDatabase . ', skipping reconnection');
                */
            }
        }

        return $next($request);
    }
}
