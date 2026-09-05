<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Tabelle autorizzate per il text-to-SQL assistito
    |--------------------------------------------------------------------------
    |
    | Whitelist di tabelle su cui il generatore di query mediche puo' operare.
    | Nessuna query generata potra' fare riferimento a tabelle fuori da questo
    | elenco. Sono anche le tabelle di cui viene ispezionato lo schema
    | (colonne data, commenti DDL) e per cui va invalidata la cache dopo le
    | migration (comando: php artisan schema:clear-cache).
    |
    */

    'allowed_tables' => [
        'patients',
        'patient_visits',
    ],

    /*
    |--------------------------------------------------------------------------
    | Connessione di sola lettura per l'esecuzione delle query generate
    |--------------------------------------------------------------------------
    |
    | Nome della connessione (config/database.php) usata da
    | MedicalQueryController::execute(). Idealmente puntata a un utente MySQL
    | con solo privilegi SELECT. Se la connessione non e' configurata con
    | credenziali dedicate, ricade sulle stesse della connessione 'mysql'.
    |
    */

    'readonly_connection' => env('MEDICAL_READONLY_CONNECTION', 'mysql_readonly'),

    /*
    |--------------------------------------------------------------------------
    | Limite di righe applicato d'ufficio alle query a livello di record
    |--------------------------------------------------------------------------
    */

    'default_row_limit' => 1000,

];
