<?php

namespace App\Contracts\Payment;

use App\Exceptions\MissingPaymentException;
use Illuminate\Http\Request;

interface PaymentTransaction
{
    /**
     * Pay for the order with the given variables (variables ex: ['amount' => 100, 'user_id' => 1])
     *
     * @param array $variables
     * @return mixed
     * @throws MissingPaymentException
     */
    public function pay(array $variables);

    /**
     * Verify the payment
     *
     * @param Request $request
     * @return mixed
     */
    public function verify(Request $request);
}
