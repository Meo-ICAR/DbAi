<?php

namespace App\Http\Controllers;

use App\Http\Requests\ExecuteDashboardQueryRequest;
use App\Models\History;
use App\Services\DateFieldSemanticsMap;
use App\Services\QueryFilterService;
use App\Services\ResearchDateRangeResolver;
use App\Services\SqlTableExtractor;
use App\Services\TableSchemaInspector;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DashboardQueryFilterController extends Controller
{
    public function __construct(
        protected TableSchemaInspector $inspector,
        protected DateFieldSemanticsMap $semanticsMap,
        protected ResearchDateRangeResolver $rangeResolver,
        protected SqlTableExtractor $tableExtractor,
        protected QueryFilterService $filterService,
    ) {}

    /**
     * Espone al frontend le colonne data filtrabili della query salvata,
     * ciascuna con la propria categoria semantica e i preset di range suggeriti.
     */
    public function index(History $history)
    {
        $columns = collect($this->tablesFor($history))
            ->flatMap(fn ($table) => collect($this->dateColumnsFor($table))
                ->map(fn ($col) => array_merge($col, ['table' => $table])));

        $result = $columns->map(function ($col) {
            $category = $this->semanticsMap->categoryFor($col['table'], $col['column']);

            return array_merge($col, [
                'category' => $category?->value,
                'presets' => $category ? $this->rangeResolver->presetsFor($category) : [],
            ]);
        });

        return response()->json($result->values());
    }

    /**
     * Esegue la query salvata applicando i filtri data scelti dall'utente.
     * Lo statement salvato viene incapsulato come sub-select e i filtri sono
     * applicati dal query builder (parametrizzati), non concatenati come stringa.
     */
    public function execute(ExecuteDashboardQueryRequest $request, History $history)
    {
        $filters = $request->input('filters', []);
        $tables = $this->tablesFor($history);

        // Whitelist: ogni colonna richiesta deve essere una colonna data nota
        // di una delle tabelle referenziate dalla query salvata.
        foreach ($filters as $filter) {
            $this->assertFilterableColumn($tables, $filter['column']);
        }

        $base = DB::connection($history->getConnectionName() ?? 'mysql')
            ->query()
            ->fromRaw('('.$this->stripTrailingSemicolon($history->sqlstatement).') as filtered_query');

        $this->filterService->applyDateFilters($base, $filters);

        $rows = $base->limit(config('medical.default_row_limit', 1000))->get();

        return response()->json([
            'righe' => $rows->count(),
            'risultati' => $rows,
        ]);
    }

    /**
     * @return string[]
     */
    protected function tablesFor(History $history): array
    {
        $allowed = config('medical.allowed_tables', []);
        $used = $this->tableExtractor->fromSql($history->sqlstatement);

        // Se la whitelist e' configurata, limita a quelle; altrimenti usa tutte
        // le tabelle referenziate che esistono davvero nello schema.
        return collect($used)
            ->filter(fn ($t) => empty($allowed) ? $this->tableExists($t) : in_array($t, $allowed, true))
            ->values()
            ->all();
    }

    protected function dateColumnsFor(string $table): array
    {
        try {
            return $this->inspector->getDateFilterableColumns($table);
        } catch (\Throwable) {
            return [];
        }
    }

    protected function tableExists(string $table): bool
    {
        try {
            return Schema::hasTable($table);
        } catch (\Throwable) {
            return false;
        }
    }

    protected function assertFilterableColumn(array $tables, string $column): void
    {
        foreach ($tables as $table) {
            $allowed = collect($this->dateColumnsFor($table))->pluck('column');
            if ($allowed->contains($column)) {
                return;
            }
        }

        abort(422, "Colonna '{$column}' non filtrabile o non appartenente alla query.");
    }

    protected function stripTrailingSemicolon(string $sql): string
    {
        return rtrim(trim($sql), ';');
    }
}
