<?php

namespace App\Services\Payment;

use App\Abstractions\Payment\PaymentInit;
use App\Contracts\Payment\PaymentTransaction;
use App\Exceptions\MissingPaymentException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Stripe\Stripe;
use Stripe\StripeClient;

class StripePayment extends PaymentInit implements PaymentTransaction
{
    protected $stripe;
    protected $tokenRes;
    protected $response;
    protected $secretKey;

    public function __construct()
    {
        Stripe::setApiKey(config('payment.stripe.secret_key'));
        $this->secretKey = config('payment.stripe.secret_key');
        $this->stripe = new StripeClient($this->secretKey);
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
            if (auth()->user()->stripeId()->doesntExist()) {
                $customer = $this->stripe->customers->create(array(
                    "address" => [
                        "line1" => "Virani Chowk",
                        "postal_code" => "360001",
                        "city" => "Rajkot",
                        "state" => "Gujarat",
                        "country" => auth()->user()->country->name,
                    ],
                    "email" => auth()->user()->email,
                    "name" => auth()->user()->first_name . ' ' . auth()->user()->last_name,
                    "source" => "tok_visa",
                ));
                auth()->user()->stripeId()->create([
                        'stripe_id' => $customer->id
                    ]
                );
            }
            $this->response = $this->stripe->charges->create([
                'amount' => 500,
                'customer' => auth()->user()->stripeId->stripe_id,
                'currency' => 'USD',
                'description' => 'My First Test Charge (created for API docs)',
            ]);
            return $this->response;
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
            $this->setVariables(request()->all());

            $data = [

            ];

            return Http::post($this->paymentPath(), $this->mergeData($data))->body();
        } catch (\Exception $e) {
            Log::channel('payment')->error("Error in Urway@verify: {$e->getMessage()} at Line: {$e->getLine()} in File: {$e->getFile()}");
            throw new MissingPaymentException($e->getMessage(), $e->getCode());
        }
    }
}
