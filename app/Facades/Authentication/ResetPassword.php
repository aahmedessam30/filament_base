<?php

namespace App\Facades\Authentication;

use Illuminate\Support\Facades\Facade;

/**
 * @method static createResetPasswordToken()
 * @method static resetPassword(\App\Http\Requests\Api\Authentication\ResetPasswordRequest $request)
 */
class ResetPassword extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \App\Services\Authentication\ResetPasswordService::class;
    }
}
