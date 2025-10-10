<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class RunQuery extends Command
{
    protected $signature = 'query:run';
    protected $description = 'Run a predefined query and display results';

    public function handle()
    {
        try {
            $results = DB::select('SELECT utente, COUNT(*) AS numero_chiamate FROM calls GROUP BY utente ORDER BY numero_chiamate DESC');
            
            $this->info("\n=== Call Counts by User ===\n");
            
            $headers = ['User', 'Call Count'];
            $rows = [];
            
            foreach ($results as $row) {
                $rows[] = [
                    $row->utente,
                    $row->numero_chiamate
                ];
            }
            
            $this->table($headers, $rows);
            
            return 0;
            
        } catch (\Exception $e) {
            $this->error("Error: " . $e->getMessage());
            return 1;
        }
    }
}
