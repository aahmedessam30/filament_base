<?php

namespace App\Abstractions\Payment;

abstract class PaymentConnection
{
    protected string $type;
    protected string $mode;
    protected string $endpoint;

    /**
     * Check if the payment gateway is in testing mode
     *
     * @return bool
     */
    protected function inTesting(): bool
    {
        return $this->mode === 'test';
    }

    /**
     * Check if the payment gateway is in production mode
     *
     * @return bool
     */
    protected function inProduction(): bool
    {
        return in_array($this->mode, ['production', 'live']);
    }

    /**
     * Get the base path for the payment gateway
     *
     * @return string
     */
    protected function basePath(): string
    {
        if ($this->inTesting()) {
            return rtrim(config("payment.$this->type.test_url"), '/');
        }
        return rtrim(config("payment.$this->type.live_url"), '/');
    }

    /**
     * Get the payment path for the payment gateway (Full path)
     *
     * @return string
     */
    protected function paymentPath(): string
    {
        return $this->basePath() . '/' . ltrim($this->endpoint, '/');
    }
}
