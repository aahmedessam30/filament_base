<?php

namespace App\Http\Controllers\Api\Authentication;

use App\Facades\Authentication\ResetPassword;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Facades\Authentication\Authentication;
use App\Http\Requests\Api\Authentication\{LoginRequest,
    RegisterRequest,
    ResetPasswordRequest,
    ResetPasswordTokenRequest};

class AuthController extends Controller
{
    public function register(RegisterRequest $request)
    {
        return Authentication::register($request);
    }

    public function login(LoginRequest $request)
    {
        return Authentication::login($request);
    }

    public function logout(Request $request)
    {
        return Authentication::logout($request);
    }

    public function forgotPassword(ResetPasswordTokenRequest $request)
    {
        return ResetPassword::createResetPasswordToken($request);
    }

    public function resetPassword(ResetPasswordRequest $request)
    {
        return ResetPassword::resetPassword($request);
    }
}
