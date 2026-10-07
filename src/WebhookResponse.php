<?php

declare(strict_types=1);

namespace Voybit\PaymentGateway\Laravel;

use Voybit\PaymentGateway\Webhook;

final class WebhookResponse
{
    public static function status(string $secret, string $id, string $timestamp, string $signature, string $rawBody): int
    {
        try {
            Webhook::verify($secret, $id, $timestamp, $signature, $rawBody);
        } catch (\InvalidArgumentException) {
            return 401;
        }
        return 204;
    }
}
