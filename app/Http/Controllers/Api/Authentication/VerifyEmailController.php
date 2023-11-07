<?php

namespace App\Http\Controllers\Api\Authentication;

use App\Facades\Authentication\Authentication;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class VerifyEmailController extends Controller
{
    public function __invoke(Request $request)
    {
        return Authentication::verifyEmail($request);
    }

    public function resend()
    {
        return Authentication::sendEmailVerificationNotification();
    }
}
