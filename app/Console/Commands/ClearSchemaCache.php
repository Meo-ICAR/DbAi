<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class ClearSchemaCache extends Command
{
    protected $signature = 'schema:clear-cache {tables?* : Tabelle da invalidare (default: config medical.allowed_tables)}';

    protected $description = 'Invalida la cache dei metadati di schema (colonne data, comment DDL)';

    public function handle(): void
    {
        $tables = $this->argument('tables') ?: config('medical.allowed_tables', []);

        foreach ($tables as $table) {
            Cache::forget("schema.date_columns.{$table}");
        }

        $this->info('Schema cache invalidata per: '.implode(', ', $tables));
    }
}
