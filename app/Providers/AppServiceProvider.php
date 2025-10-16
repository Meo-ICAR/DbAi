<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\DB;

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
        // Set database based on the current host
       // if (request()->getHost() === 'chartai.hassisto.com') {
        if (str_ends_with(request()->getHost(), 'hassisto.com')) {
            // Update database configuration
            config(['database.connections.mysql.database' => 'proforma']);
            // Uncomment and update these if you need to change username/password
            // config(['database.connections.mysql.username' => 'your_username']);
            // config(['database.connections.mysql.password' => 'your_password']);

            // Reconnect to the database with new settings
            DB::purge('mysql');
            DB::reconnect('mysql');
        }
    }
}
