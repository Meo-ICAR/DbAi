<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\DB;

class DatabaseScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
        // Ottiene il nome del database della connessione corrente del modello
        //$databaseName = $model->getConnection()->getDatabaseName();
        $databaseName = DB::connection()->getDatabaseName();

        // Aggiunge la condizione a tutte le query per questo modello
        $builder->where($model->getTable() . '.database_name', $databaseName);
    }
}
