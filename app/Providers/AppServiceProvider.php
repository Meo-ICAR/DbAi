<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Debug logging
        $host = request()->getHost();
        /*
        Log::info('AppServiceProvider booting', [
            'host' => $host,
            'full_url' => request()->fullUrl(),
            'time' => now()->toDateTimeString()
        ]);
        */
        // Set database based on the current host
        if (str_ends_with($host, 'hassisto.com')) {
          //  Log::info('Setting database to proforma for host: ' . $host);
             $olddb = config('database.connections.mysql.database');
            // Update database configuration
            if ($olddb != 'proforma') {
            config(['database.connections.mysql.database' => 'proforma']);

            // Reconnect to the database with new settings
            DB::purge('mysql');
            DB::reconnect('mysql');
          /*
            Log::info('Database connection updated', [
                'database' => config('database.connections.mysql.database')
            ]);
            */
        }
        else {
           // Log::info('Database connection not updated', [ 'database' => $olddb ]);
        }

    }
}
}
