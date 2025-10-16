<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Log;

class DebugLogger
{
    /**
     * Log a debug message
     */
    public static function log($message, $context = [])
    {
        if (is_array($message) || is_object($message)) {
            $message = json_encode($message, JSON_PRETTY_PRINT);
        }
        
        // Log to the debug channel
        Log::channel('debug')->debug($message, $context);
        
        // Also log to the default channel
        Log::debug($message, $context);
    }
    
    /**
     * Log an error message
     */
    public static function error($message, $context = [])
    {
        if (is_array($message) || is_object($message)) {
            $message = json_encode($message, JSON_PRETTY_PRINT);
        }
        
        Log::channel('debug')->error($message, $context);
        Log::error($message, $context);
    }
}
