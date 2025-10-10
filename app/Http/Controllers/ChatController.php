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
            
            return response()->json([
                'status' => 'success',
                'response' => $response->getContent(),
                'query' => $lastQuery ? [
                    'sql' => $lastQuery['query'],
                    'params' => $lastQuery['params'] ?? [],
                    'timestamp' => $lastQuery['timestamp']
                ] : null
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
