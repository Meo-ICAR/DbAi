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
        
        Log::info('SetDatabaseConnection middleware running', [
            'host' => $host,
            'time' => now()->toDateTimeString()
        ]);

        if (str_ends_with($host, 'hassisto.com')) {
            Log::info('Switching to proforma database for host: ' . $host);
            
            config(['database.connections.mysql.database' => 'proforma']);
            
            // Reconnect to the database with new settings
            DB::purge('mysql');
            DB::reconnect('mysql');
            
            Log::info('Database connection updated', [
                'database' => config('database.connections.mysql.database')
            ]);
        }

        return $next($request);
    }
}
