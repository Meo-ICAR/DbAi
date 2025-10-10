<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Illware\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    $results = DB::select('SELECT utente, COUNT(*) AS numero_chiamate FROM calls GROUP BY utente ORDER BY numero_chiamate DESC');
    
    echo "\n=== Call Counts by User ===\n\n";
    echo str_pad("User", 20) . " | " . str_pad("Call Count", 10) . "\n";
    echo str_repeat("-", 34) . "\n";
    
    foreach ($results as $row) {
        echo str_pad($row->utente, 20) . " | " . str_pad($row->numero_chiamate, 10) . "\n";
    }
    
    echo "\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
