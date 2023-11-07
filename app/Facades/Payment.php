<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class Payment extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'payment';
    }

    public static function init(string $gateway)
    {
        switch ($gateway) {
            case str_contains($gateway, '_'):
                $gateway = str_replace(' ', '', ucwords(str_replace('_', ' ', $gateway)));
                break;
            case str_contains($gateway, '-'):
                $gateway = str_replace(' ', '', ucwords(str_replace('-', ' ', $gateway)));
                break;
            case str_contains($gateway, ' '):
                $gateway = str_replace(' ', '', ucwords(str_replace(' ', ' ', $gateway)));
                break;
            case str_contains($gateway, '.'):
                $gateway = str_replace(' ', '', ucwords(str_replace('.', ' ', $gateway)));
                break;
            case ctype_lower($gateway[0]):
                $gateway = ucfirst($gateway);
                break;
            default:
                break;
        }

        $class = "App\Services\Payment\\$gateway";
        if (!class_exists($class)) {
            throw new \Exception("Payment gateway $gateway not found");
        }
        return new $class;
    }
}
