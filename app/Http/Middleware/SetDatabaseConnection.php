<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log; // For logging
// use Symfony\Component\HttpFoundation\Response;
use App\Models\Company;              // 1. Import your Company model
use Illuminate\Support\Facades\Auth;  // 2. Import the Auth facade
use Illuminate\Support\Facades\Cache; // 2. Import the Cache facade

/*
class SetDatabaseConnection
{

    //  Handle an incoming request.

    public function handle(Request $request, Closure $next): Response
    {
    // 4. Get the authenticated user
        $user = Auth::user();

        // 5. Check if a user is authenticated and has a company_id
        if ($user && $user->company_id) {

            $companyId = $user->company_id;

            // 6. Find the company by its ID, using cache for performance
            // Caches the company details for 60 minutes to reduce DB queries.
            $company = Cache::remember("company:{$companyId}", 3600, function () use ($companyId) {
                return Company::find($companyId);
            });

        // 4. If a company is found, switch the database connection
        if ($company) {
            // *** IMPORTANT ASSUMPTION ***
            // This code assumes your 'companies' table has a column named 'database_name'
            // If your column is named differently (e.g., 'db_name'), change it below.
            $newDatabase = $company->db_database;

            $currentDatabase = config('database.connections.mysql.database');

            // Only update if the database is different
            if ($currentDatabase !== $newDatabase) {
                // Log::info("Switching database from {$currentDatabase} to {$newDatabase} for host: {$host}");

                config(['database.connections.mysql.database' => $newDatabase]);

                // Reconnect to the database with new settings
                DB::purge('mysql');
                DB::reconnect('mysql');

                // Log::info('Database connection updated', ['new_database' => $newDatabase]);
            }

         } else {
                // 8. (Critical) If the user's company_id doesn't match a real company.
                // This indicates a data problem. We should block the request.
                Log::warning("Invalid company_id: {$companyId} for user: {$user->id}");
                abort(403, 'Invalid company configuration.');
            }
        }

        // 9. If the user is not logged in (e.g., on the login page),
        // let the request continue using the default database connection.
        return $next($request);
    }

}
*/

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
