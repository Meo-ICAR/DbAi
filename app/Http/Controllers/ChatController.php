<?php

namespace App\Http\Controllers;

use App\Neuron\Agents\DataAnalystAgent;
use Illuminate\Http\Request;
use NeuronAI\Chat\Messages\UserMessage;

class ChatController extends Controller
{
    protected $agent;

    public function __construct()
    {
        $this->agent = new DataAnalystAgent();
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
            if ($lastQuery) {
                try {
                    // Execute the query to get results
                    $pdo = \DB::connection()->getPdo();
                    $stmt = $pdo->prepare($lastQuery['query']);
                    $stmt->execute($lastQuery['params'] ?? []);
                    $results = $stmt->fetchAll(\PDO::FETCH_ASSOC);
                } catch (\Exception $e) {
                    // If there's an error executing the query, just return empty results
                    $results = [];
                }
            }

            return response()->json([
                'status' => 'success',
                'response' => $response->getContent(),
                'query' => $lastQuery ? [
                    'sql' => $lastQuery['query'],
                    'params' => $lastQuery['params'] ?? [],
                    'timestamp' => $lastQuery['timestamp']
                ] : null,
                'results' => $results
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
