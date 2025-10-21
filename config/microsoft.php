<?php

return [
    'client_id' => env('MICROSOFT_CLIENT_ID'),
    'client_secret' => env('MICROSOFT_CLIENT_SECRET'),
    'redirect' => env('APP_URL') . '/auth/microsoft/callback',
    'tenant' => env('MICROSOFT_TENANT', 'common'),
];
