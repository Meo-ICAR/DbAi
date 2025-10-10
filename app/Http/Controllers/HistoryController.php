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
    public function display(History $history)
    {
        $results = null;
        $chartData = null;
        $error = null;

        try {
            // Execute the query - convert the raw SQL to a string
            $sql = $history->sqlstatement;
            if ($sql instanceof \Illuminate\Database\Query\Expression) {
                $sql = $sql->getValue();
            }

            \Log::info('Executing SQL:', ['sql' => $sql]);

            // Execute the query and convert results to array
            $rawResults = \DB::select($sql);
            \Log::info('Raw query results:', ['count' => count($rawResults), 'first' => $rawResults[0] ?? null]);

            $results = collect($rawResults)->map(function($item) {
                return (array)$item;
            })->toArray();

            \Log::info('Processed results:', ['count' => count($results), 'first' => $results[0] ?? null]);

            /*
            // Prepare chart data if we have results
            if (!empty($results)) {
                $firstRow = $results[0];
                $columns = array_keys($firstRow);
                \Log::info('Available columns:', $columns);

                if (count($columns) >= 2) {
                    $labels = [];
                    $data = [];

                    foreach ($results as $row) {
                        $value = $row[$columns[1]] ?? null;
                        $label = $row[$columns[0]] ?? '';
                        \Log::debug('Processing row:', [
                            'label_value' => $label,
                            'data_value' => $value,
                            'is_numeric' => is_numeric($value)
                        ]);

                        if (is_numeric($value)) {
                            $labels[] = (string)$label;
                            $data[] = (float)$value;
                        }
                    }

                    $chartData = [
                        'labels' => $labels,
                        'data' => $data,
                        'type' => strtolower(explode(' ', $history->charttype)[0] ?? 'bar')
                    ];

                    \Log::info('Generated chart data:', [
                        'labels_count' => count($labels),
                        'data_count' => count($data),
                        'chart_type' => $chartData['type']
                    ]);
                } else {
                    \Log::warning('Not enough columns for chart', ['columns_count' => count($columns)]);
                }
            }
*/
        } catch (\Exception $e) {
            $error = $e->getMessage();
            \Log::error('Error in chart method:', ['error' => $error]);
        }

       return view('history.display', [
            'results' => $results,
            'error' => $error,
            'history' => $history
        ]);
    }

    /**
     * Display only the chart for a history item.
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

        } catch (\Exception $e) {
            $error = $e->getMessage();
            \Log::error('Error in chart method:', ['error' => $error]);
        }

        // If no chart data could be generated, show an error
        if (!$chartData) {
            $error = $error ?: 'Could not generate chart data. Please ensure your query returns at least 2 columns with numeric data in the second column.';
        }

        return view('history.chart', [
            'chartData' => $chartData,
            'error' => $error,
            'history' => $history
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
                'charttype' => $chartType,
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
