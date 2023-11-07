<?php

namespace App\Services\Authentication;

use App\Notifications\ResetPassword;
use App\Http\Requests\Api\Authentication\{ResetPasswordRequest, ResetPasswordTokenRequest};
use App\Http\Resources\Api\Shared\{ErrorResource, SuccessResource};
use App\Models\User;
use Illuminate\Support\{Facades\DB, Facades\Log, Facades\Mail, Facades\Password, Str};

class ResetPasswordService
{
    public function createResetPasswordToken(ResetPasswordTokenRequest $request)
    {
        try {
            if (!$user = User::whereEmail($request->email)->first()) {
                return ErrorResource::make(__('auth.email_not_found'), 404);
            }

            $token = Str::random(20);

            DB::table('password_reset_tokens')->updateOrInsert(['email' => $user->email], ['token' => $token, 'created_at' => now()]);

            $user->notify(new ResetPassword($token));
            return SuccessResource::make(__('auth.reset_password_link_sent'));
        } catch (\Exception $e) {
            Log::channel('auth')->error("Error while creating reset password token with message: {$e->getMessage()} in file {$e->getFile()} on line {$e->getLine()}");
            return ErrorResource::make(__('auth.reset_password_link_failed'));
        }
    }

    public function resetPassword(ResetPasswordRequest $request)
    {
        if (!$passwordReset = DB::table('password_reset_tokens')->where('token', $request->token)->first()) {
            return ErrorResource::make(__('auth.invalid_token'), 404);
        }

        if (!$user = User::whereEmail($passwordReset->email)->first()) {
            return ErrorResource::make(__('auth.email_not_found'), 404);
        }

        if (!$user->hasVerifiedEmail()) {
            return ErrorResource::make(__('auth.email_not_verified'), 403);
        }

        if (strtotime($passwordReset->created_at) < strtotime('-' . config('auth.passwords.' . config('auth.defaults.passwords') . '.expire') . ' minutes')) {
            return ErrorResource::make(__('auth.reset_password_link_expired'), 403);
        }

        $user->update(['password' => $request->password]);

        DB::table('password_reset_tokens')->whereEmail($user->email)->delete();

        return SuccessResource::make(__('auth.password_reset_success'));
    }
}
