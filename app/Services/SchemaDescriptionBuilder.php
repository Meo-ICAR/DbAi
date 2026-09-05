<?php

namespace App\Services;

use Illuminate\Support\Facades\Schema;

class SchemaDescriptionBuilder
{
    public function __construct(protected TableSchemaInspector $inspector) {}

    public function buildForTables(array $tables): string
    {
        $description = '';

        foreach ($tables as $table) {
            $columns = $this->inspector->getAllColumnsWithComments($table);
            $foreignKeys = $this->getForeignKeys($table);

            $description .= "## Tabella: {$table}\n";
            foreach ($columns as $col) {
                $description .= sprintf(
                    "- %s (%s%s): %s\n",
                    $col['name'],
                    $col['type'],
                    $col['nullable'] ? ', nullable' : '',
                    $col['comment'] ?? '(nessuna descrizione)'
                );
            }

            if ($foreignKeys) {
                $description .= "Relazioni:\n";
                foreach ($foreignKeys as $fk) {
                    $description .= "- {$fk['column']} -> {$fk['foreign_table']}.{$fk['foreign_column']}\n";
                }
            }
            $description .= "\n";
        }

        return $description;
    }

    protected function getForeignKeys(string $table): array
    {
        return collect(Schema::getForeignKeys($table))
            ->map(fn ($fk) => [
                'column' => $fk['columns'][0],
                'foreign_table' => $fk['foreign_table'],
                'foreign_column' => $fk['foreign_columns'][0],
            ])
            ->all();
    }
}
