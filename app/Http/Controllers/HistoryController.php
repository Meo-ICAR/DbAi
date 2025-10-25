<?php

namespace App\Http\Controllers;

use App\Exports\HistoryExport;
use App\Models\History;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class HistoryController extends Controller
{
    /**
     * Display a listing of the history entries with search and sorting.
     */
    public function index(Request $request)
    {
        $query = History::query();

        // Search functionality
        if ($search = $request->input('search')) {
            $query->where(function($q) use ($search) {
                $q->where('message', 'like', "%{$search}%")
                  ->orWhere('sqlstatement', 'like', "%{$search}%")
                  ->orWhere('charttype', 'like', "%{$search}%");
            });
        }

        // Sorting
        $sortField = $request->input('sort', 'submission_date');
        $sortDirection = $request->input('direction', 'desc');

        // Validate sort direction
        if (!in_array(strtolower($sortDirection), ['asc', 'desc'])) {
            $sortDirection = 'desc';
        }

        // Apply sorting
        $query->orderBy($sortField, $sortDirection);

        $histories = $query->paginate(10)->withQueryString();

        return view('history.index', [
            'histories' => $histories,
            'sortField' => $sortField,
            'sortDirection' => $sortDirection,
            'search' => $request->input('search', '')
        ]);
    }

    /**
     * Show the form for creating a new history entry.
     */
    public function create()
    {
        return view('history.create');
    }

    /**
     * Store a newly created history entry in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'message' => 'required|string|max:1000',
            'sqlstatement' => 'required|string',
            'charttype' => 'nullable|string|max:50',
        ]);

        History::create([
            'message' => $validated['message'],
            'sqlstatement' => $validated['sqlstatement'],
            'charttype' => $validated['charttype'] ?? 'Pie Chart',
            'submission_date' => now(),
            'database_name' => DB::getDatabaseName()
        ]);

        return redirect()->route('history.index')
            ->with('success', 'History entry created successfully.');
    }

    /**
     * Display the specified history entry.
     */
    public function show(History $history)
    {
        return view('history.show', compact('history'));
    }

    /**
     * Update the dashboard order of the specified resource.
     */
    public function updateOrder(Request $request, History $history)
    {
        $request->validate([
            'change' => 'required|integer'
        ]);

        $history->update([
            'dashboardorder' => $history->dashboardorder + $request->change
        ]);

        return response()->json(['success' => true]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(History $history)
    {
        return view('history.edit', compact('history'));
    }

    /**
     * Update the specified history entry in storage.
     */
    public function update(Request $request, History $history)
    {
        $validated = $request->validate([
            'message' => 'required|string|max:1000',
            'sqlstatement' => 'required|string',
            'charttype' => 'nullable|string|max:50',
            'dashboardorder' => 'nullable|integer',
            'masterquery' => 'nullable|exists:histories,id',
            'slavedashboard' => 'nullable|integer|min:0|max:100',
        ]);

        $updateData = [
            'message' => $validated['message'],
            'sqlstatement' => $validated['sqlstatement'],
            'charttype' => $validated['charttype'] ?? 'Pie Chart',
            'submission_date' => now(),
            'masterquery' => $validated['masterquery'] ?? null,
            'slavedashboard' => $validated['slavedashboard'] ?? 0,
            'database_name' => DB::getDatabaseName()
        ];

        // Only update dashboardorder if it's present in the request
        if (array_key_exists('dashboardorder', $validated)) {
            $history->dashboardorder = $validated['dashboardorder'];
        }

        $history->update($updateData);

        return redirect()->route('history.index')
            ->with('success', 'History entry updated successfully.');
    }

    /**
     */
    public function destroy(History $history)
    {
        $history->delete();
        return redirect()->route('history.index')
            ->with('success', 'History entry deleted successfully');
    }

    /**
     * Display and execute a history query.
     */
    public function display(History $history, Request $request, $filter_column = null, $filter_value = null)
    {
        // Increment view counter
        $history->increment('nviewed');

        // Check for export request first
        if ($request->has('export')) {
            return $this->exportToExcel($history, $request,$filter_column , $filter_value);
        }

        $results = [];
        $error = null;
        $sortColumn = $request->input('sort');
        $sortDirection = $request->input('direction', 'asc');

        // Get filter values from parameters or query string
        $filter_column = $request->query('filter_column', $filter_column);
        $filter_value = $request->query('filter_value', $filter_value);

        try {
            // Execute the query
            $sql = $history->sqlstatement;
            if ($sql instanceof \Illuminate\Database\Query\Expression) {
                $sql = $sql->getValue();
            }

            // Apply filter_value if provided
            if ($request->has('filter_value')) {
                $filterValue = $request->query('filter_value');
                $filterColumn = $request->query('filter_column', 'status'); // Default column if not specified

                // Add WHERE clause if not already present
                if (!empty($filter_value)) {
                    // Escape single quotes in the filter value
                    $quotedValue = "'" .  $filter_value . "'";
                    // Replace the first question mark with the quoted value
                    $sql = preg_replace('/\?/', $quotedValue, $sql, 1);
                }

                \Log::info('Executing filtered SQL:', ['sql' => $sql, 'filter_value' => $filterValue]);
                $results = collect(DB::select($sql, [$filterValue]))->map(fn($item) => (array)$item);
            } else {
                \Log::info('Executing SQL:', ['sql' => $sql]);
                $results = collect(DB::select($sql))->map(fn($item) => (array)$item);
            }

            // Apply sorting if needed
            if ($sortColumn && $results->isNotEmpty() && array_key_exists($sortColumn, $results->first())) {
                $sortDirection = strtolower($sortDirection) === 'desc' ? 'desc' : 'asc';
                $results = $sortDirection === 'asc'
                    ? $results->sortBy($sortColumn, SORT_REGULAR)
                    : $results->sortByDesc($sortColumn, SORT_REGULAR);
                $results = $results->values();
            }

        } catch (\Exception $e) {
            $error = $e->getMessage();
            \Log::error('Error in display method:', ['error' => $error]);
        }

        return view('history.display', [
            'results' => $results,
            'error' => $error,
            'history' => $history,
            'sortColumn' => $sortColumn,
            'sortDirection' => $sortDirection
        ]);
    }

    private function exportToExcel($history, $request,$filter_column = null, $filter_value = null)
    {
        try {
            // Execute the query
            $sql = $history->sqlstatement;
            if ($sql instanceof \Illuminate\Database\Query\Expression) {
                $sql = $sql->getValue();
            }
            if ($request->has('filter_value')) {
                $filterValue = $request->query('filter_value');
                $filterColumn = $request->query('filter_column', 'status'); // Default column if not specified

                // Add WHERE clause if not already present
                if (!empty($filter_value)) {
                    // Escape single quotes in the filter value
                    $quotedValue = "'" .  $filter_value . "'";
                    // Replace the first question mark with the quoted value
                    $sql = preg_replace('/\?/', $quotedValue, $sql, 1);
                }

                \Log::info('Executing filtered SQL:', ['sql' => $sql, 'filter_value' => $filterValue]);
                $results = collect(DB::select($sql, [$filterValue]))->map(fn($item) => (array)$item);
            } else {
                \Log::info('Executing SQL:', ['sql' => $sql]);
                $results = collect(DB::select($sql))->map(fn($item) => (array)$item);
            }

            // Apply sorting if needed
            if ($sortColumn = $request->input('sort')) {
                $sortDirection = strtolower($request->input('direction', 'asc')) === 'desc' ? 'desc' : 'asc';
                $results = $sortDirection === 'asc'
                    ? $results->sortBy($sortColumn, SORT_REGULAR)
                    : $results->sortByDesc($sortColumn, SORT_REGULAR);
                $results = $results->values();
            }

            // Prepare export
            $headings = $results->isNotEmpty() ? array_keys($results->first()) : [];
            $filename = 'export_' . \Illuminate\Support\Str::slug($history->message, '_') . '_' . now()->format('Y-m-d_His') . '.xlsx';

            return Excel::download(new HistoryExport($results, $headings), $filename);

        } catch (\Exception $e) {
            \Log::error('Export error:', ['error' => $e->getMessage()]);
            return back()->with('error', 'Error generating Excel file: ' . $e->getMessage());
        }
    }

    /**
     * Clone a history entry.
     */
    public function clone(History $history)
    {
        $clonedHistory = $history->replicate();
        $clonedHistory->save();
        $clonedHistory->message = $history->message . ' (' . $history->id . ')';
        if($history->masterquery === null){
          $clonedHistory->masterquery = $history->id;
        }
        $clonedHistory->slavedashboard  = $clonedHistory->id;
        $clonedHistory->save();
        return redirect()->route('history.index')
            ->with('success', 'History entry cloned successfully.');
    }

    /**
     * Display a chart for the history query.
     */
    public function chart(History $history, Request $request, $filter_column = null, $filter_value = null)
    {
        // Increment view counter
        $history->increment('nviewed');

        $chartData = null;
        $error = null;

        // Get filter values from parameters or query string
        $filter_column = $request->query('filter_column', $filter_column);
        $filter_value = $request->query('filter_value', $filter_value);

        try {
            // Execute the query - convert the raw SQL to a string
            $sql = $history->sqlstatement;
            if ($sql instanceof \Illuminate\Database\Query\Expression) {
                $sql = $sql->getValue();
            }

            // Apply filter if provided
            if ($filter_value !== null) {
                $sql = rtrim(trim($sql), ';');
                if (!empty($filter_value)) {
                    // Escape single quotes in the filter value
                    $quotedValue = "'" .  $filter_value . "'";
                    // Replace the first question mark with the quoted value
                    $sql = preg_replace('/\?/', $quotedValue, $sql, 1);
                }
                $results = collect(DB::select($sql, [$filter_value]))
                    ->map(fn($item) => (array)$item)
                    ->toArray();
            } else {
                $results = collect(DB::select($sql))
                    ->map(fn($item) => (array)$item)
                    ->toArray();
            }

            \Log::info('Executing SQL for chart:', [
                'sql' => $sql,
                'filter_column' => $filter_column,
                'filter_value' => $filter_value
            ]);

            // Prepare chart data if we have results
            if (!empty($results)) {
                $firstRow = $results[0];
                $columns = array_keys($firstRow);

                if (count($columns) >= 2) {
                    $labels = [];
                    $data = [];

                    foreach ($results as $row) {
                        $value = $row[$columns[1]] ?? null;
                        $label = $row[$columns[0]] ?? '';

                        if (is_numeric($value)) {
                            $labels[] = (string)$label;
                            $data[] = (float)$value;
                        }
                    }

                    if (!empty($labels) && !empty($data)) {
                        $chartData = [
                            'labels' => $labels,
                            'data' => $data,
                            'type' => strtolower(explode(' ', $history->charttype)[0] ?? 'bar'),
                            'title' => $history->message
                        ];
                    }
                }
            }

            if (!$chartData) {
                throw new \Exception('Could not generate chart data. Please ensure your query returns at least 2 columns with numeric data in the second column.');
            }

        } catch (\Exception $e) {
            $error = $e->getMessage();
            \Log::error('Error in chart method:', ['error' => $error]);
        }

        return view('history.chart', [
            'chartData' => $chartData,
            'error' => $error,
            'history' => $history,
            'filter_column' => $filter_column,
            'filter_value' => $filter_value
        ]);
    }

    /**
     * Display the dashboard with all charts that have dashboardorder > 0
     */
    public function dashboard()
    {
        // Get all history items with dashboardorder > 0 and masterslave is null, ordered by dashboardorder
        $historiesQuery = History::where('dashboardorder', '>', 0)
            ->orderBy('dashboardorder');

        // Get the SQL query with bindings
        $historiesSql = $historiesQuery->toSql();
        $bindings = $historiesQuery->getBindings();

        // Replace placeholders with actual values
        foreach ($bindings as $binding) {
            $value = is_numeric($binding) ? $binding : "'" . $binding . "'";
            $historiesSql = preg_replace('/\?/', $value, $historiesSql, 1);
        }

        $histories = $historiesQuery->get();
        $charts = [];
        $errors = [];
        $debug_queries = [
            'histories_query' => $historiesSql
        ];

        foreach ($histories as $history) {
            try {
                // Execute the query - convert the raw SQL to a string
                $sql = $history->sqlstatement;
                if ($sql instanceof \Illuminate\Database\Query\Expression) {
                    $sql = $sql->getValue();
                }

                // Execute the query and convert results to array
                $results = collect(\DB::select($sql))->map(function($item) {
                    return (array)$item;
                })->toArray();

                // Prepare chart data if we have results
                if (!empty($results)) {
                    $firstRow = $results[0];
                    $columns = array_keys($firstRow);

                    if (count($columns) >= 2) {
                        $labels = [];
                        $data = [];

                        foreach ($results as $row) {
                            $value = $row[$columns[1]] ?? null;
                            $label = $row[$columns[0]] ?? '';

                            if (is_numeric($value)) {
                                $labels[] = (string)$label;
                                $data[] = (float)$value;
                            }
                        }

                        if (!empty($labels) && !empty($data)) {
                            $charts[] = [
                                'id' => 'chart-' . $history->id,
                                'title' => $history->message,
                                'type' => strtolower(explode(' ', $history->charttype)[0] ?? 'bar'),
                                'labels' => $labels,
                                'data' => $data,
                                'history' => $history,
                                'data-has-slaves' => $history->slaves()->exists()
                            ];
                            continue;
                        }
                    }
                }

                $errors[] = "Could not generate chart for: " . $history->message;

            } catch (\Exception $e) {
                $errors[] = "Error processing '{$history->message}': " . $e->getMessage();
                \Log::error('Error in dashboard chart generation:', [
                    'history_id' => $history->id,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
            }
        }

        return view('history.dashboard', [
            'charts' => $charts,
            'errors' => $errors,
            'debug_queries' => $debug_queries
        ]);
    }

    /**
     * Display the subdashboard with all charts that have dashboardorder > 0
     */
    public function subdashboard($historyId, $filter_column = null, $filter_value = null)
    {
        // If filter parameters are in the query string, use those instead
        $filter_column = request()->query('filter_column', $filter_column);
        $filter_value = request()->query('filter_value', $filter_value);

        // Get all history items with masterquery = $history, ordered by slavedashboard
        $histories = History::where('masterquery', '=', $historyId)
        ->where('slavedashboard', '>', 0)
            ->orderBy('slavedashboard');
        // Log the SQL query
        $historymaster = History::where('id', '=', $historyId)->first();
        $titolo =  $historymaster->message.' - '.$filter_value;
        \Log::info('Subdashboard query:', [

            'bindings' =>   $histories->getBindings() ,
            'filter_column' => $filter_column,
            'filter_value' => $filter_value
        ]);
        /*
        $sqlx = $histories->toRawSql();
        \Log::info('Complete SQL: ' . $sqlx);
        */
        $histories = $histories->get();

        // If no histories found, redirect to dashboard
        if ($histories->isEmpty()) {
            return redirect()->route('dashboard');
        }

        $charts = [];
        $errors = [];

        foreach ($histories as $history) {
            try {
                // Execute the query - convert the raw SQL to a string
                $sql = $history->sqlstatement;
                if ($sql instanceof \Illuminate\Database\Query\Expression) {
                    $sql = $sql->getValue();
                }
                // Replace the parameter in the SQL with the filter value
                if (!empty($filter_value)) {
                    // Escape single quotes in the filter value
                    $quotedValue = "'" .  $filter_value . "'";
                    // Replace the first question mark with the quoted value
                    $sql = preg_replace('/\?/', $quotedValue, $sql, 1);
                }
                /*
                // Add WHERE condition if filter parameters are provided
                if (!empty($filter_column) && $filter_value !== null) {
                    $sql = rtrim(trim($sql), ';');

                    // Check if the SQL already has a WHERE clause
                    if (stripos($sql, 'WHERE') !== false) {
                        // If WHERE exists, add AND condition
                        $sql = "$sql AND $filter_column = ?";
                    } else {
                        // If no WHERE exists, add WHERE clause
                        $sql = "$sql WHERE $filter_column = ?";
                    }

                    // Execute with parameter binding for security
                    $results = collect(\DB::select($sql, [$filter_value]))
                        ->map(function($item) {
                            return (array)$item;
                        })->toArray();
                } else {

                }
                */
                // Execute without filtering if no filter parameters provided
                $results = collect(\DB::select($sql))
                ->map(function($item) {
                    return (array)$item;
                })->toArray();
                // Prepare chart data if we have results
                if (!empty($results)) {
                    $firstRow = $results[0];
                    $columns = array_keys($firstRow);

                    if (count($columns) >= 2) {
                        $labels = [];
                        $data = [];

                        foreach ($results as $row) {
                            $value = $row[$columns[1]] ?? null;
                            $label = $row[$columns[0]] ?? '';

                            if (is_numeric($value)) {
                                $labels[] = (string)$label;
                                $data[] = (float)$value;
                            }
                        }

                        if (!empty($labels) && !empty($data)) {
                            $charts[] = [
                                'id' => 'chart-' . $history->id,
                                'title' => $history->message,
                                'type' => strtolower(explode(' ', $history->charttype)[0] ?? 'bar'),
                                'labels' => $labels,
                                'data' => $data,
                                'history' => $history,
                                'data-has-slaves' => $history->slaves()->exists()
                            ];
                            continue;
                        }
                    }
                }

                $errors[] = "Could not generate chart for: " . $history->message;

            } catch (\Exception $e) {
                $errors[] = "Error processing '{$history->message}': " . $e->getMessage();
                \Log::error('Error in dashboard chart generation:', [
                    'history_id' => $history->id,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
            }
        }

        return view('history.dashboard', [
            'charts' => $charts,
            'errors' => $errors,
            'debug_queries' => $debug_queries,
            'title' => $titolo
        ]);
    }

    /**
     * Log or update a query in the history.
     */
    public static function logQuery($message, $sqlStatement, $chartType = 'Pie Chart')
    {
        $history = History::where('sqlstatement', $sqlStatement)->first();

        if ($history) {
            $history->update([
                'submission_date' => now(),
                'charttype' => $chartType,
                'submission_date' => now(),
                'database_name' => DB::getDatabaseName()
            ]);
        } else {
            History::create([
                'message' => $message,
                'sqlstatement' => $sqlStatement,
                'charttype' => $chartType,
                'submission_date' => now(),
                'database_name' => DB::getDatabaseName()
            ]);
        }
    }

    /**
     * Display detailed chart data when a chart segment is clicked
     */
    public function chartDetails(History $history, Request $request)
    {
        $filterValue = $request->input('filter_value');
        $filterColumn = $request->input('filter_column');

        // Find the first child history record that matches the filter
        $detailHistory = History::where('masterquery', $history->id)
            ->where('message', 'like', "%{$filterColumn}: {$filterValue}%")
            ->first();

        if (!$detailHistory) {
            return back()->with('error', 'No detailed data found for this selection.');
        }

        // Execute the query to get the detailed data
        try {
            $results = DB::select(DB::raw($detailHistory->sqlstatement));
            $results = json_decode(json_encode($results), true);
        } catch (\Exception $e) {
            return back()->with('error', 'Error executing query: ' . $e->getMessage());
        }

        return view('history.chart-details', [
            'history' => $detailHistory,
            'results' => $results,
            'parentHistory' => $history,
            'filterValue' => $filterValue,
            'filterColumn' => $filterColumn
        ]);
    }
}
