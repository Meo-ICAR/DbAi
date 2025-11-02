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
        $this->app->singleton(\App\Neuron\Agents\DataAnalystAgent::class, function ($app) {
            return new \App\Neuron\Agents\DataAnalystAgent();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Set the default connection for all models to 'dbai'
        $this->app->resolving('db', function ($db) {
            $db->setDefaultConnection('dbai');
        });

        // Debug logging
        $host = request()->getHost();
        
        // Set database based on the current host
        if (str_ends_with($host, 'hassisto.com')) {
            $olddb = config('database.connections.mysql.database');
            // Update database configuration
            if ($olddb != 'proforma') {
                config(['database.connections.mysql.database' => 'proforma']);
                // Reconnect to the database with new settings
                DB::purge('mysql');
                DB::reconnect('mysql');
            }
        } else {
            // Log::info('Database connection not updated', ['database' => $olddb]);
        }
    }
