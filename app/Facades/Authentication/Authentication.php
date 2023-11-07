<?php

namespace App\Facades\Authentication;

use Illuminate\Support\Facades\Facade;

/**
 * @method static register()
 * @method static login()
 * @method static logout()
 * @method static createResetPasswordToken()
 * @method static resetPassword()
 * @method static verifyEmail(\Illuminate\Http\Request $request)
 * @method static sendEmailVerificationNotification(\Illuminate\Http\Request $request)
 */
class Authentication extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \App\Services\Authentication\AuthenticationService::class;
    }
}
