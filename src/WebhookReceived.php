<?php

declare(strict_types=1);

namespace Voybit\PaymentGateway\Laravel;

final class WebhookReceived
{
    /** @param array<string, mixed> $event */
    public function __construct(public readonly array $event)
    {
    }
}
