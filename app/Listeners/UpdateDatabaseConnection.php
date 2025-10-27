<?php

namespace App\Listeners;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use Illuminate\Auth\Events\Login;

class UpdateDatabaseConnection
{
    /**
     * Handle the event.
     *
     * @param  Login  $event
     * @return void
     */
    public function handle(Login $event)
    {
        $user = $event->user;

        // Check if user has a company with database settings
        if ($user->company && $user->company->db_database) {
            // Store the current connection name to revert later if needed
            session(['original_connection' => config('database.default')]);

            // Update the database configuration
            $driver = 'mysql'; //$user->company->db_connection ?? config("database.connections.driver");
            $connection = [
                'driver' => 'mysql' ,//$user->company->db_connection   ?? config("database.connections.driver"),
                'host' => $user->company->db_host ?? config("database.connections.mysql.host"),
                'port' => $user->company->db_port ?? config("database.connections.mysql.port"),
                'database' => $user->company->db_database,
                'username' => $user->company->db_username ?? config("database.connections.mysql.username"),
                'password' => $user->company->db_password ?? config("database.connections.mysql.password"),
                'charset' => 'utf8mb4',
                'collation' => 'utf8mb4_unicode_ci',
                'prefix' => '',
                'strict' => true,
                'engine' => null,
            ];

            // Set the new connection
            config(['database.connections.company' => $connection]);
            config(['database.default' => 'company']);

            // Reconnect to the database with new settings
            DB::purge( $driver);
            DB::reconnect( $driver);
        }
    }
}
