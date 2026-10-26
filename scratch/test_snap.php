<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$serverKey = env('MIDTRANS_SERVER_KEY');
\Midtrans\Config::$serverKey = $serverKey;
\Midtrans\Config::$isProduction = true; // Testing Production API
\Midtrans\Config::$isSanitized = true;
\Midtrans\Config::$is3ds = true;

// Bypass SSL peer verification on local environment
\Midtrans\Config::$curlOptions[CURLOPT_SSL_VERIFYPEER] = false;
\Midtrans\Config::$curlOptions[CURLOPT_SSL_VERIFYHOST] = 0;

echo "Testing Production URL with SSL bypass: https://app.midtrans.com/snap/v1/transactions\n";
echo "Server Key: " . $serverKey . "\n";

try {
    $token = \Midtrans\Snap::getSnapToken([
        'transaction_details' => [
            'order_id' => 'TRX-PROD-' . time(),
            'gross_amount' => 10000,
        ]
    ]);
    echo "PRODUCTION SUCCESS! Snap Token: " . $token . "\n";
} catch (\Exception $e) {
    echo "PRODUCTION ERROR: " . $e->getMessage() . "\n";
}
