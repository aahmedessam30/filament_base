<?php

namespace App\Services\Payment;

use App\Abstractions\Payment\PaymentInit;
use App\Contracts\Payment\PaymentTransaction;
use App\Exceptions\MissingPaymentException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class Urway extends PaymentInit implements PaymentTransaction
{
    private string $password;
    private string $merchantIp;
    private string $terminalId;

    public function __construct()
    {
        $this->type       = 'urway';
        $this->mode       = config('payment.urway.mode');
        $this->endpoint   = config('payment.urway.endpoint');
        $this->password   = config('payment.urway.password') ?? '';
        $this->merchantIp = config('payment.urway.merchantIp') ?? '';
        $this->terminalId = config('payment.urway.terminalId') ?? '';
    }

    /**
     * Pay for the order with the given variables (variables ex: ['amount' => 100, 'user_id' => 1])
     *
     * @param array $variables
     * @return mixed
     * @throws MissingPaymentException
     */
    public function pay(array $variables)
    {
        try {
            $this->setVariables($variables)->hashRequest();

            $data = [
                'currency'   => 'SAR',
                'action'     => '1',
                'country'    => 'SA',
                'terminalId' => $this->terminalId,
                'password'   => $this->password,
                'merchantIp' => $this->merchantIp
            ];

            return Http::post($this->paymentPath(), $this->mergeData($data))->body();
        } catch (\Exception $e) {
            Log::channel('payment')->error("Error in Urway@pay: {$e->getMessage()} at Line: {$e->getLine()} in File: {$e->getFile()}");
            throw new MissingPaymentException($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Verify the payment
     *
     * @param Request $request
     * @return mixed
     * @throws MissingPaymentException
     */
    public function verify(Request $request)
    {
        try {
            $this->setVariables($request->all())->hashRequest();

            $data = [
                'currency'   => 'SAR',
                'action'     => '10',
                'country'    => 'SA',
                'terminalId' => $this->terminalId,
                'password'   => $this->password,
                'merchantIp' => $this->merchantIp
            ];

            return Http::post($this->paymentPath(), $this->mergeData($data))->body();
        } catch (\Exception $e) {
            Log::channel('payment')->error("Error in Urway@verify: {$e->getMessage()} at Line: {$e->getLine()} in File: {$e->getFile()}");
            throw new MissingPaymentException($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Hash request
     *
     * @return self
     */
    protected function hashRequest(): self
    {
        $requestHash = $this->required_fields['trackid'] . '|' . config('payment.urway.terminal_id') . '|' . config('payment.urway.password') . '|' . config('payment.urway.merchant_key') . '|' . $this->required_fields['amount'] . '|' . $this->required_fields['currency'];
        $this->required_fields['requestHash'] = hash('sha256', $requestHash);
        return $this;
    }
}
