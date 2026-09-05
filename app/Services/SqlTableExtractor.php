<?php

namespace App\Services;

class SqlTableExtractor
{
    /**
     * Estrae i nomi di tabella referenziati dopo FROM / JOIN in uno statement SQL.
     *
     * @return string[] elenco di nomi tabella, senza duplicati
     */
    public function fromSql(?string $sql): array
    {
        if (blank($sql)) {
            return [];
        }

        preg_match_all('/\b(?:FROM|JOIN)\s+`?([a-zA-Z_][a-zA-Z0-9_]*)`?/i', $sql, $matches);

        return array_values(array_unique($matches[1] ?? []));
    }
}
