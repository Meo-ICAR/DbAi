<?php

namespace App\Http\Controllers;

use App\Models\MedicalQueryAudit;
use App\Services\MedicalQueryGenerator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Exception\HttpException;

class MedicalQueryController extends Controller
{
    /**
     * Traduce una domanda in linguaggio naturale in SQL di sola lettura.
     * NON esegue la query: restituisce SQL, spiegazione, warning e assunzioni
     * perche' il ricercatore possa verificarli prima di chiamare execute().
     */
    public function ask(Request $request, MedicalQueryGenerator $generator)
    {
        $validated = $request->validate(['domanda' => 'required|string|max:2000']);

        $response = Http::withHeaders([
            'x-api-key' => config('services.anthropic.key'),
            'anthropic-version' => '2023-06-01',
        ])->timeout(60)->post('https://api.anthropic.com/v1/messages', [
            'model' => config('services.anthropic.model'),
            'max_tokens' => 1024,
            'system' => $generator->buildSystemPrompt(),
            'messages' => [
                ['role' => 'user', 'content' => $validated['domanda']],
            ],
        ]);

        if ($response->failed()) {
            $this->audit('ask', $validated['domanda'], null, 'error', errore: 'Anthropic HTTP '.$response->status());
            abort(502, 'Il servizio di generazione query non e\' al momento disponibile.');
        }

        $result = $this->decodeModelJson($response->json('content.0.text'));

        if (! is_array($result) || ! isset($result['sql'])) {
            $this->audit('ask', $validated['domanda'], null, 'error', errore: 'Risposta modello non interpretabile');
            abort(502, 'Risposta del modello non interpretabile.');
        }

        if ($result['richiede_chiarimento'] ?? false) {
            $this->audit('ask', $validated['domanda'], null, 'needs_clarification');

            return response()->json([
                'needs_clarification' => true,
                'message' => $result['spiegazione'] ?? '',
            ]);
        }

        // Validazione statica: se l'SQL non passa i controlli, viene loggato e rifiutato.
        try {
            $this->validateSql($result['sql'], $generator->allowedTables());
        } catch (HttpException $e) {
            $this->audit('ask', $validated['domanda'], $result['sql'], 'rejected', errore: $e->getMessage());
            throw $e;
        }

        $this->audit('ask', $validated['domanda'], $result['sql'], 'ok',
            warning: $result['warning'] ?? null,
            assunzioni: $result['assunzioni'] ?? null,
        );

        return response()->json([
            'sql' => $result['sql'],
            'spiegazione' => $result['spiegazione'] ?? null,
            'warning' => $result['warning'] ?? null,
            'assunzioni' => $result['assunzioni'] ?? null,
        ]);
    }

    /**
     * Esegue una query gia' mostrata e confermata esplicitamente dall'utente.
     * Usa una connessione di sola lettura e forza la sessione in READ ONLY.
     */
    public function execute(Request $request, MedicalQueryGenerator $generator)
    {
        $validated = $request->validate(['sql' => 'required|string']);
        $sql = $validated['sql'];

        try {
            $this->validateSql($sql, $generator->allowedTables());
        } catch (HttpException $e) {
            $this->audit('execute', null, $sql, 'rejected', errore: $e->getMessage());
            throw $e;
        }

        $connection = DB::connection($this->readonlyConnectionName());

        try {
            $connection->statement('SET SESSION TRANSACTION READ ONLY');
            $results = $connection->select($sql);
        } catch (\Throwable $e) {
            $this->audit('execute', null, $sql, 'error', errore: $e->getMessage());
            abort(422, 'Esecuzione query fallita: '.$e->getMessage());
        } finally {
            try {
                $connection->statement('SET SESSION TRANSACTION READ WRITE');
            } catch (\Throwable) {
                // best effort: sessione probabilmente gia' rilasciata
            }
        }

        $this->audit('execute', null, $sql, 'ok', righe: count($results));

        return response()->json([
            'risultati' => $results,
            'righe' => count($results),
        ]);
    }

    protected function validateSql(string $sql, array $allowedTables): void
    {
        $trimmed = ltrim($sql);

        if (! preg_match('/^\s*SELECT\s/i', $trimmed)) {
            abort(422, 'Query non valida: solo letture consentite.');
        }

        // Nessuna istruzione multipla (permesso solo un eventuale ; finale).
        if (str_contains(rtrim($trimmed, "; \n\r\t"), ';')) {
            abort(422, 'Query non valida: istruzioni multiple non consentite.');
        }

        $forbiddenKeywords = ['INSERT', 'UPDATE', 'DELETE', 'DROP', 'ALTER', 'TRUNCATE', 'GRANT', 'REVOKE', 'CREATE', 'REPLACE', 'MERGE', 'CALL', 'EXECUTE'];
        foreach ($forbiddenKeywords as $word) {
            if (preg_match('/\b'.$word.'\b/i', $sql)) {
                abort(422, "Query non valida: contiene '{$word}'.");
            }
        }

        $forbiddenFragments = ['INTO OUTFILE', 'INTO DUMPFILE', 'LOAD_FILE', ';--', 'xp_', '/*', '*/'];
        foreach ($forbiddenFragments as $fragment) {
            if (stripos($sql, $fragment) !== false) {
                abort(422, "Query non valida: contiene '{$fragment}'.");
            }
        }

        $usedTables = $this->extractTableNames($sql);
        if (array_diff($usedTables, $allowedTables)) {
            abort(422, 'Query non valida: tabelle non autorizzate.');
        }
    }

    protected function extractTableNames(string $sql): array
    {
        preg_match_all('/\b(?:FROM|JOIN)\s+`?(\w+)`?/i', $sql, $matches);

        return array_values(array_unique($matches[1] ?? []));
    }

    protected function readonlyConnectionName(): string
    {
        $name = config('medical.readonly_connection', 'mysql_readonly');

        return config("database.connections.{$name}") ? $name : 'mysql';
    }

    protected function audit(
        string $action,
        ?string $domanda,
        ?string $sql,
        string $esito,
        ?int $righe = null,
        ?string $warning = null,
        ?string $assunzioni = null,
        ?string $errore = null,
    ): void {
        try {
            MedicalQueryAudit::create([
                'user_id' => auth()->id(),
                'action' => $action,
                'domanda' => $domanda,
                'sql' => $sql,
                'esito' => $esito,
                'righe' => $righe,
                'warning' => $warning,
                'assunzioni' => $assunzioni,
                'errore' => $errore,
                'ip' => request()->ip(),
                'database_name' => DB::getDatabaseName(),
            ]);
        } catch (\Throwable $e) {
            Log::warning('MedicalQueryAudit write failed: '.$e->getMessage());
        }
    }

    /**
     * Il modello deve rispondere in JSON puro, ma tolleriamo eventuali
     * fence markdown o testo attorno all'oggetto JSON.
     */
    protected function decodeModelJson(?string $text): mixed
    {
        if ($text === null) {
            return null;
        }

        $decoded = json_decode($text, true);
        if (json_last_error() === JSON_ERROR_NONE) {
            return $decoded;
        }

        if (preg_match('/\{.*\}/s', $text, $m)) {
            return json_decode($m[0], true);
        }

        return null;
    }
}
