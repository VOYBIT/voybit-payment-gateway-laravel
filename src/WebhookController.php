<?php

declare(strict_types=1);

namespace Voybit\PaymentGateway\Laravel;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Voybit\PaymentGateway\Webhook;

final class WebhookController
{
    public function __invoke(Request $request): Response
    {
        $raw = $request->getContent();
        $status = WebhookResponse::status(
            (string) config('voybit.webhook_secret'),
            self::header($request, 'Voybit-Webhook-Id'),
            self::header($request, 'Voybit-Webhook-Timestamp'),
            self::header($request, 'Voybit-Webhook-Signature'),
            $raw,
        );
        if ($status !== 204) {
            return response('', 401);
        }
        try {
            $event = Webhook::parse($raw);
        } catch (\Throwable) {
            return response('', 400);
        }
        event(new WebhookReceived($event));
        return response('', 204);
    }

    private static function header(Request $request, string $name): string
    {
        $value = $request->headers->get($name, '');
        return is_string($value) ? $value : '';
    }
}
