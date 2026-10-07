<?php

declare(strict_types=1);

require dirname(__DIR__, 2) . '/payment-gateway-php/src/Webhook.php';
require dirname(__DIR__) . '/src/WebhookResponse.php';

use Voybit\PaymentGateway\Laravel\WebhookResponse;

$secret = 'whsec_test';
$id = 'evt_1';
$timestamp = (string) time();
$body = '{"id":"evt_1","type":"payment.paid","status":"paid"}';
$signature = 'v1=' . hash_hmac('sha256', $id . '.' . $timestamp . '.' . $body, $secret);

if (WebhookResponse::status($secret, $id, $timestamp, $signature, $body) !== 204) {
    fwrite(STDERR, "expected 204\n");
    exit(1);
}
$bad = 'v1=' . str_repeat('ab', 32);
if (WebhookResponse::status($secret, $id, $timestamp, $bad, $body) !== 401) {
    fwrite(STDERR, "expected 401\n");
    exit(1);
}
echo "laravel ok\n";
