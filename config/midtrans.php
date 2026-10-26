<?php

$serverKey = env('MIDTRANS_SERVER_KEY', env('MIDTRANS_SERVEY_KEY'));
$clientKey = env('MIDTRANS_CLIENT_KEY');
$isProdEnv = env('MIDTRANS_IS_PRODUCTION', false);

$isProd = filter_var($isProdEnv, FILTER_VALIDATE_BOOLEAN);

return [
    'serveyKey' => $serverKey,
    'serverKey' => $serverKey,
    'server_key' => $serverKey,
    'clientKey' => $clientKey,
    'client_key' => $clientKey,
    'isProduction' => $isProd,
    'is_production' => $isProd,
    'isSanitized' => env('MIDTRANS_IS_SANITIZED', true),
    'is_sanitized' => env('MIDTRANS_IS_SANITIZED', true),
    'is3ds' => env('MIDTRANS_IS_3DS', true),
    'is_3ds' => env('MIDTRANS_IS_3DS', true),
];