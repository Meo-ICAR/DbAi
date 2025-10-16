<?php

namespace App\Http\Controllers;

use App\Helpers\DebugLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DebugController extends Controller
{
    /**
     * Handle incoming debug log messages
     */
    public function log(Request $request)
    {
        $level = $request->input('level', 'debug');
        $message = $request->input('message', '');
        $context = $request->input('context', []);
        
        if (empty($message)) {
            return response()->json(['status' => 'error', 'message' => 'No message provided'], 400);
        }
        
        // Log the message
        switch ($level) {
            case 'error':
                DebugLogger::error($message, $context);
                break;
            case 'warning':
            case 'warn':
                DebugLogger::log("[WARNING] " . $message, $context);
                break;
            default:
                DebugLogger::log($message, $context);
        }
        
        return response()->json(['status' => 'success']);
    }
}
