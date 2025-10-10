<?php

namespace App\Http\Controllers;

use App\Models\History;
use Illuminate\Http\Request;

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
     * Show the form for editing the specified history entry.
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
        ]);

        $history->update([
            'message' => $validated['message'],
            'sqlstatement' => $validated['sqlstatement'],
            'charttype' => $validated['charttype'] ?? 'Pie Chart',
            'submission_date' => now(),
        ]);

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
    public function display(History $history, Request $request)
    {
        $results = [];
        $error = null;
        $sortColumn = $request->input('sort');
        $sortDirection = $request->input('direction', 'asc');

        try {
            // Execute the query - convert the raw SQL to a string
            $sql = $history->sqlstatement;
            if ($sql instanceof \Illuminate\Database\Query\Expression) {
                $sql = $sql->getValue();
            }

            \Log::info('Executing SQL:', ['sql' => $sql]);

            // Execute the query and convert results to collection
            $results = collect(\DB::select($sql))->map(function($item) {
                return (array)$item;
            });

            // Sort the results if a sort column is specified
            if ($sortColumn && $results->isNotEmpty() && array_key_exists($sortColumn, $results->first())) {
                $sortDirection = strtolower($sortDirection) === 'desc' ? 'desc' : 'asc';

                $results = $results->sortBy(function($item) use ($sortColumn) {
                    $value = $item[$sortColumn] ?? null;
                    // Convert to string for case-insensitive comparison
                    return is_string($value) ? strtolower($value) : $value;
                }, SORT_REGULAR, $sortDirection === 'desc');
            }

            $results = $results->values()->all();

            \Log::info('Processed results:', ['count' => count($results), 'first' => $results[0] ?? null]);

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

    /**
     * Display a chart for the history query.
     */
    public function chart(History $history)
    {
        $chartData = null;
        $error = null;

        try {
            // Execute the query - convert the raw SQL to a string
            $sql = $history->sqlstatement;
            if ($sql instanceof \Illuminate\Database\Query\Expression) {
                $sql = $sql->getValue();
            }

            \Log::info('Executing SQL for chart:', ['sql' => $sql]);

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
            'history' => $history
        ]);
    }

    /**
     * Display the dashboard with all charts that have dashboardorder > 0
     */
    public function dashboard()
    {
        // Get all history items with dashboardorder > 0, ordered by dashboardorder
        $histories = History::where('dashboardorder', '>', 0)
            ->orderBy('dashboardorder')
            ->get();

        $charts = [];
        $errors = [];

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
                                'history' => $history
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
            'errors' => $errors
        ]);
    }

    /**
     * Log or update a query in the history.
     */
    public static function logQuery($message, $sqlStatement, $chartType = 'Pie Chart') {
        $history = History::where('sqlstatement', $sqlStatement)->first();

        if ($history) {
            $history->update([
                'submission_date' => now(),
              //  'charttype' => $chartType,
            ]);
        } else {
            History::create([
                'message' => $message,
                'sqlstatement' => $sqlStatement,
                'charttype' => $chartType,
                'submission_date' => now(),
            ]);
        }
    }
}
