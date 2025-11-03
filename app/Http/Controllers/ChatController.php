<?php

namespace App\Http\Controllers;

use App\Neuron\Agents\DataAnalystAgent;
use App\Models\History;
use App\Http\Controllers\HistoryController;
use Illuminate\Http\Request;
use NeuronAI\Chat\Messages\UserMessage;
use NeuronAI\Chat\Enums\MessageRole;
use NeuronAI\Chat\Messages\Message;
use NeuronAI\Chat\History\ChatHistoryInterface;
use NeuronAI\Chat\History\SQLChatHistory;
use Illuminate\Support\Str;

class ChatController extends Controller
{
    protected $agent;
    protected $threadId;

    public function __construct(DataAnalystAgent $agent)
    {
        $this->agent = $agent;
        
        // Use Laravel's session ID as the thread ID
        $this->threadId = session()->getId();
        
        // Set the thread ID for the agent
        $this->agent->setThreadId($this->threadId);
    }

    public function index()
    {
        return view('chat.index');
    }

    public function sendMessage(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        try {
            $response = $this->agent->chat(new UserMessage($request->message));

            // Get the last executed query for debugging/display
            $lastQuery = DataAnalystAgent::getLastQuery();

            // Get the query results if available
            $results = [];
            $isGroupByQuery = false;

            if ($lastQuery) {
                try {
                    // Check if this is a GROUP BY query
                    $isGroupByQuery = stripos($lastQuery['query'], 'GROUP BY') !== false;

                    // Execute the query to get results
                    $pdo = \DB::connection()->getPdo();
                    $stmt = $pdo->prepare($lastQuery['query']);
                    $stmt->execute($lastQuery['params'] ?? []);
                    $results = $stmt->fetchAll(\PDO::FETCH_ASSOC);

                    // Log the query to history
                    if (!empty($results)) {
                        HistoryController::logQuery(
                            $request->message,
                            $lastQuery['query'],
                            $isGroupByQuery ? 'Pie Chart' : 'Table'
                        );
                    }
                } catch (\Exception $e) {
                    // If there's an error executing the query, just return empty results
                    $results = [];
                }
            }

            $responseData = [
                'status' => 'success',
                'response' => $response->getContent(),
                'is_group_by' => $isGroupByQuery,
                'query' => $lastQuery ? [
                    'sql' => $lastQuery['query'],
                    'params' => $lastQuery['params'] ?? [],
                    'timestamp' => $lastQuery['timestamp']
                ] : null,
                'results' => $results
            ];

            // For AJAX requests, return JSON response
            if ($request->ajax()) {
                return response()->json($responseData);
            }

            // For non-AJAX requests, return the chat view
            return view('chat.index', [
                'response' => $response->getContent(),
                'query' => $lastQuery,
                'results' => $results,
                'isGroupByQuery' => $isGroupByQuery
            ]);

        } catch (\GuzzleHttp\Exception\ClientException $e) {
            $statusCode = $e->getResponse()->getStatusCode();
            $errorMessage = match($statusCode) {
                302 => 'The request was redirected. Please try again or contact support if the issue persists.',
                400 => 'Bad request. Please check your input and try again.',
                401 => 'Authentication required. Please log in again.',
                403 => 'You do not have permission to perform this action.',
                404 => 'The requested resource was not found.',
                default => 'A client error occurred. Status code: ' . $statusCode
            };

            if ($request->ajax()) {
                return response()->json([
                    'status' => 'error',
                    'message' => $errorMessage,
                    'code' => $statusCode
                ], $statusCode);
            }

            return back()->with('error', $errorMessage);
        } catch (\GuzzleHttp\Exception\ServerException $e) {
            $statusCode = $e->getResponse()->getStatusCode();
            $errorMessage = $statusCode === 503
                ? 'The service is currently unavailable. Please try again later. Status code: ' . $statusCode
                : 'A server error occurred. Status code: ' . $statusCode;

            if ($request->ajax()) {
                return response()->json([
                    'status' => 'error',
                    'message' => $errorMessage,
                    'code' => $statusCode
                ], $statusCode);
            }

            return back()->with('error', $errorMessage);
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'status' => 'error',
                    'message' => $e->getMessage()
                ], 500);
            }

            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Show the chart view for a GROUP BY query
     */
    public function showChart(Request $request)
    {
        try {
            $query = base64_decode($request->query('query'));
            $params = json_decode(base64_decode($request->query('params')), true) ?? [];

            if (empty($query)) {
                throw new \Exception('No query provided');
            }

            // Execute the query to get results
            $pdo = \DB::connection()->getPdo();
            $stmt = $pdo->prepare($query);
            $stmt->execute($params);
            $results = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            return view('chat.chart', [
                'chartData' => $results,
                'query' => $query
            ]);

        } catch (\Exception $e) {
            return back()->with('error', 'Could not generate chart: ' . $e->getMessage());
        }
    }
}
