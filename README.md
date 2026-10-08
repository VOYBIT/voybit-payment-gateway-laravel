# Voybit payment gateway for Laravel

## Get an API key

1. Create an account at [dashboard.voybit.com](https://dashboard.voybit.com).
2. Open **Gateways**, create a payment gateway, enable the assets customers may choose, and store its webhook secret as `VOYBIT_WEBHOOK_SECRET`.
3. Open **API keys**, choose **Create secret key**, and bind it to that gateway. Copy the full `vb_live_…` value once and store it as `VOYBIT_API_KEY` on your server.

Create a buyer-choice checkout session and verify its webhook with the [PHP library](https://github.com/VOYBIT/voybit-payment-gateway-php). The API key and webhook secret stay in `.env`.

```bash
composer config repositories.voybit-php vcs https://github.com/VOYBIT/voybit-payment-gateway-php
composer config repositories.voybit-laravel vcs https://github.com/VOYBIT/voybit-payment-gateway-laravel
composer require voybit/payment-gateway:dev-main voybit/payment-gateway-laravel:dev-main
```

The application `composer.json` needs `"minimum-stability": "dev"` and `"prefer-stable": true`. Not published to Packagist.

`VOYBIT_API_KEY` and `VOYBIT_WEBHOOK_SECRET` are read from the environment. Inject `Voybit\PaymentGateway\Client` where you create the checkout session, then send the payer to `checkout_url`.

```php
Route::post('/webhooks/voybit', \Voybit\PaymentGateway\Laravel\WebhookController::class);
```

Exclude that path from CSRF verification. The controller checks the signature, then dispatches `WebhookReceived`. Fulfil only when `status` is `paid` or `overpaid`, and ignore a repeated `Voybit-Webhook-Id`.

```php
Event::listen(WebhookReceived::class, function (WebhookReceived $webhook) {
    if (!in_array($webhook->event['status'] ?? '', ['paid', 'overpaid'], true)) {
        return;
    }
});
```
