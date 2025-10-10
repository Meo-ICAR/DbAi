<?php

return [
    'accepted' => 'Il campo :attribute deve essere accettato.',
    'active_url' => 'Il campo :attribute non è un URL valido.',
    'after' => 'Il campo :attribute deve essere una data successiva a :date.',
    'after_or_equal' => 'Il campo :attribute deve essere una data successiva o uguale a :date.',
    'alpha' => 'Il campo :attribute può contenere solo lettere.',
    'alpha_dash' => 'Il campo :attribute può contenere solo lettere, numeri, trattini e trattini bassi.',
    'alpha_num' => 'Il campo :attribute può contenere solo lettere e numeri.',
    'array' => 'Il campo :attribute deve essere un array.',
    'before' => 'Il campo :attribute deve essere una data precedente a :date.',
    'before_or_equal' => 'Il campo :attribute deve essere una data precedente o uguale a :date.',
    'between' => [
        'numeric' => 'Il campo :attribute deve essere compreso tra :min e :max.',
        'file' => 'Il campo :attribute deve essere compreso tra :min e :max kilobyte.',
        'string' => 'Il campo :attribute deve essere compreso tra :min e :max caratteri.',
        'array' => 'Il campo :attribute deve avere tra :min e :max elementi.',
    ],
    'boolean' => 'Il campo :attribute deve essere vero o falso.',
    'confirmed' => 'La conferma del campo :attribute non corrisponde.',
    'date' => 'Il campo :attribute non è una data valida.',
    'date_format' => 'Il campo :attribute non corrisponde al formato :format.',
    'different' => 'Il campo :attribute e :other devono essere diversi.',
    'digits' => 'Il campo :attribute deve essere di :digits cifre.',
    'digits_between' => 'Il campo :attribute deve essere compreso tra :min e :max cifre.',
    'dimensions' => 'Il campo :attribute ha dimensioni di immagine non valide.',
    'distinct' => 'Il campo :attribute ha un valore duplicato.',
    'email' => 'Il campo :attribute deve essere un indirizzo email valido.',
    'exists' => 'Il campo :attribute selezionato non è valido.',
    'file' => 'Il campo :attribute deve essere un file.',
    'filled' => 'Il campo :attribute è obbligatorio.',
    'image' => 'Il campo :attribute deve essere un\'immagine.',
    'in' => 'Il campo :attribute selezionato non è valido.',
    'in_array' => 'Il campo :attribute non esiste in :other.',
    'integer' => 'Il campo :attribute deve essere un numero intero.',
    'ip' => 'Il campo :attribute deve essere un indirizzo IP valido.',
    'ipv4' => 'Il campo :attribute deve essere un indirizzo IPv4 valido.',
    'ipv6' => 'Il campo :attribute deve essere un indirizzo IPv6 valido.',
    'json' => 'Il campo :attribute deve essere una stringa JSON valida.',
    'max' => [
        'numeric' => 'Il campo :attribute non può essere superiore a :max.',
        'file' => 'Il campo :attribute non può essere superiore a :max kilobyte.',
        'string' => 'Il campo :attribute non può contenere più di :max caratteri.',
        'array' => 'Il campo :attribute non può avere più di :max elementi.',
    ],
    'mimes' => 'Il campo :attribute deve essere un file di tipo: :values.',
    'mimetypes' => 'Il campo :attribute deve essere un file di tipo: :values.',
    'min' => [
        'numeric' => 'Il campo :attribute deve essere almeno :min.',
        'file' => 'Il campo :attribute deve essere di almeno :min kilobyte.',
        'string' => 'Il campo :attribute deve contenere almeno :min caratteri.',
        'array' => 'Il campo :attribute deve avere almeno :min elementi.',
    ],
    'not_in' => 'Il campo :attribute selezionato non è valido.',
    'numeric' => 'Il campo :attribute deve essere un numero.',
    'present' => 'Il campo :attribute deve essere presente.',
    'regex' => 'Il formato del campo :attribute non è valido.',
    'required' => 'Il campo :attribute è obbligatorio.',
    'required_if' => 'Il campo :attribute è obbligatorio quando :other è :value.',
    'required_unless' => 'Il campo :attribute è obbligatorio a meno che :other non sia in :values.',
    'required_with' => 'Il campo :attribute è obbligatorio quando :values è presente.',
    'required_with_all' => 'Il campo :attribute è obbligatorio quando :values sono presenti.',
    'required_without' => 'Il campo :attribute è obbligatorio quando :values non è presente.',
    'required_without_all' => 'Il campo :attribute è obbligatorio quando nessuno di :values è presente.',
    'same' => 'Il campo :attribute e :other devono corrispondere.',
    'size' => [
        'numeric' => 'Il campo :attribute deve essere :size.',
        'file' => 'Il campo :attribute deve essere di :size kilobyte.',
        'string' => 'Il campo :attribute deve contenere :size caratteri.',
        'array' => 'Il campo :attribute deve contenere :size elementi.',
    ],
    'string' => 'Il campo :attribute deve essere una stringa.',
    'timezone' => 'Il campo :attribute deve essere un fuso orario valido.',
    'unique' => 'Il campo :attribute è già stato utilizzato.',
    'uploaded' => 'Il caricamento del campo :attribute non è riuscito.',
    'url' => 'Il formato del campo :attribute non è valido.',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap attribute place-holders
    | with something more reader friendly such as E-Mail Address instead
    | of "email". This simply helps us make messages a little cleaner.
    |
    */

    'attributes' => [],
];
