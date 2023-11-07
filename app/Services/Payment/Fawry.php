<?php

namespace App\Services\Payment;

use App\Contracts\Payment\{App\Abstractions\Payment\PaymentInit, PaymentTransaction};
use App\Exceptions\MissingPaymentException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class Fawry extends \App\Abstractions\Payment\PaymentInit implements PaymentTransaction
{
    public $secret;
    public $merchant;
    public $verify_route_name;
    public $display_mode;
    public $pay_mode;

    public function __construct()
    {
        $this->type              = 'fawry';
        $this->mode              = config('payment.fawry.mode');
        $this->secret            = config('payment.fawry.secret');
        $this->endpoint          = config('payment.fawry.endpoint');
        $this->merchant          = config('payment.fawry.merchant');
        $this->verify_route_name = config('payment.fawry.verify_route_name');
        $this->display_mode      = config('payment.fawry.display_mode');
        $this->pay_mode          = config('payment.fawry.pay_mode');
    }

    private function generate_html($data): string
    {
        return view('payment.fawry', ['model' => $this, 'data' => $data])->render();
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
        $this->setVariables($variables);

        $unique_id = uniqid('', true);
        $data      = [
            'url'           => $this->paymentPath(),
            'merchant'      => $this->merchant,
            'secret'        => $this->secret,
            'user_id'       => $this->required_fields['user_id'],
            'user_name'     => "{$this->required_fields['first_name']} {$this->required_fields['last_name']}",
            'user_email'    => $this->required_fields['email'],
            'user_phone'    => $this->required_fields['phone'],
            'unique_id'     => $unique_id,
            'item_id'       => 1,
            'item_quantity' => 1,
            'amount'        => $this->required_fields['amount'],
            'payment_id'    => $unique_id
        ];

        $secret         = $data['merchant'] . $data['unique_id'] . $data['user_id'] . $data['item_id'] . $data['item_quantity'] . $data['amount'] . $data['secret'];
        $data['secret'] = $secret;

        return [
            'payment_id'   => $unique_id,
            'html'         => $this->generate_html($data),
            'redirect_url' => ""
        ];
    }

    /**
     * Verify the payment
     *
     * @param Request $request
     * @return mixed
     */
    public function verify(Request $request)
    {
        $res      = json_decode($request['chargeResponse'], true);
        $hash     = hash('sha256', $this->merchant . $res['merchantRefNumber'] . $this->secret);
        $response = Http::get("{$this->paymentPath()}?merchantCode=$this->merchant&merchantRefNumber={$res['merchantRefNumber']}&signature=$hash");

        if ($response->offsetGet('statusCode') === 200 && $response->offsetGet('paymentStatus') === "PAID") {
            return [
                'success'      => true,
                'message'      => __('messages.payment.done'),
                'payment_id'   => $res['merchantRefNumber'],
                'process_data' => $request->all()
            ];
        }

        if ($response->offsetGet('statusCode') !== 200) {
            return [
                'success'      => false,
                'message'      => __('messages.payment.failed'),
                'payment_id'   => $res['merchantRefNumber'],
                'process_data' => $request->all()
            ];
        }
    }
}
