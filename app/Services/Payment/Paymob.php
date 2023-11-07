<?php

namespace App\Services\Payment;

use App\Contracts\Payment\PaymentTransaction;
use App\Exceptions\MissingPaymentException;
use Illuminate\Http\Request;

class Paymob extends \App\Abstractions\Payment\PaymentInit implements PaymentTransaction
{
    private string $apiKey;
    private string $iframeId;
    private string $integrationId;

    public function __construct()
    {
        $this->type          = 'paymob';
        $this->mode          = config('payment.paymob.mode');
        $this->endpoint      = config('payment.paymob.endpoint');
        $this->apiKey        = config('payment.paymob.apiKey') ?? '';
        $this->iframeId      = config('payment.paymob.iframeId') ?? '';
        $this->integrationId = config('payment.paymob.integrationId') ?? '';
    }

    /**
     * @param array $variables
     * @return mixed
     */
    public function pay(array $variables)
    {
        $this->setVariables($variables);


    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function verify(Request $request)
    {
        // TODO: Implement verify() method.
    }
}
