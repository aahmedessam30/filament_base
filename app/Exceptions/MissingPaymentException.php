<?php

namespace App\Exceptions;

use Exception;

class MissingPaymentException extends Exception
{
    public function __construct($parameter, $provider)
    {
        parent::__construct("$parameter is required to use $provider");
    }
}
