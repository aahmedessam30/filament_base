<?php

namespace App\Services\Authentication;

use App\Http\Resources\Api\V1\UserResource;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\Api\Shared\{ErrorResource, SuccessResource};
use Illuminate\Support\Facades\Log;
use App\Http\Requests\Api\Authentication\{LoginRequest, RegisterRequest};

class AuthenticationService
{
    /**
     * Register a new user.
     *
     * @param RegisterRequest $request
     * @return ErrorResource|SuccessResource
     */
    public function register(RegisterRequest $request): ErrorResource|SuccessResource
    {
        try {
            if ($user = User::create($request->safe()->all())) {

                try {
                    $user->sendEmailVerificationNotification();
                    event(new Registered($user));
                } catch (\Exception $e) {
                    Log::channel('auth')->error("Error sending welcome email to user $user->id - $user->email ,in File: {$e->getFile()} ,Line: {$e->getLine()} ,Message: {$e->getMessage()}");
                }

                return SuccessResource::make([
                    'message' => __('auth.register_success_check_email_for_verification'),
                    'token'   => $user->createToken('authToken')->plainTextToken,
                    'user'    => UserResource::make($user)->resolve(),
                ], 201)->withWrappData();
            }
            return ErrorResource::make(__('auth.register_failed'));
        } catch (\Exception $e) {
            Log::channel('auth')->error("Error registering user, in File: {$e->getFile()} ,Line: {$e->getLine()} ,Message: {$e->getMessage()}");
            return ErrorResource::make(__('auth.register_failed'));
        }
    }

    /**
     * Login the user and create a token.
     *
     * @param LoginRequest $request
     * @return ErrorResource|SuccessResource
     */
    public function login(LoginRequest $request): ErrorResource|SuccessResource
    {
        if (Auth::attempt($request->safe()->only('email', 'password'))) {
            if (is_null(Auth::user()->email_verified_at)) {
                return ErrorResource::make(__('auth.verify_email'), 401);
            }

            return SuccessResource::make([
                'message' => __('auth.login_success'),
                'token'   => Auth::user()->createToken('authToken')->plainTextToken,
                'user'    => UserResource::make(Auth::user())->resolve(),
            ])->withWrappData();
        }

        return ErrorResource::make(__('auth.login_failed'), 401);
    }

    /**
     * Logout the user and delete the token.
     *
     * @param $request
     * @return SuccessResource
     */
    public function logout($request): SuccessResource
    {
        $validated = $request->validate(['logout_all' => ['sometimes', 'boolean']]);

        if ($request->has('logout_all') && $validated['logout_all']) {
            Auth::user()->tokens()->delete();
            return SuccessResource::make(__('auth.logout_all_success'));
        }

        Auth::user()->currentAccessToken()->delete();
        return SuccessResource::make(__('auth.logout_success'));
    }

    /**
     * Verify the user email.
     *
     * @param Request $request
     * @return SuccessResource
     */
    public function verifyEmail(Request $request): SuccessResource
    {
        if ($request->user()->hasVerifiedEmail()) {
            return SuccessResource::make(__('auth.email_already_verified'));
        }

        if ($request->user()->markEmailAsVerified()) {
            event(new Verified($request->user()));
        }

        return SuccessResource::make(__('auth.verify_email_success'));
    }

    /**
     * Send Email Verification Notification.
     *
     * @param Request $request
     * @return SuccessResource
     */
    public function sendEmailVerificationNotification(Request $request): SuccessResource
    {
        if ($request->user()->hasVerifiedEmail()) {
            return SuccessResource::make(__('auth.email_already_verified'));
        }

        $request->user()->sendEmailVerificationNotification();
        return SuccessResource::make(__('auth.verify_email_link_sent'));
    }
}
