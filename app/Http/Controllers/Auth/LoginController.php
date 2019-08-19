<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Exceptions\VerifyEmailException;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Validation\ValidationException;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

use App\User;
use Carbon\Carbon;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * Attempt to log the user into the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    protected function attemptLogin(Request $request)
    {
        $token = $this->guard()->attempt($this->credentials($request));

        if (! $token) {
            return false;
        }

        $user = $this->guard()->user();
        if ($user instanceof MustVerifyEmail && ! $user->hasVerifiedEmail()) {
            return false;
        }

        $this->guard()->setToken($token);

        return true;
    }

    /**
     * Attempt to log the user into the application via his PAN
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    protected function attemptLoginPan(Request $req)
    {
        // Validate Request
        $this->validate(
            $req, 
            [
                'pan' => 'required',
                'pin' => 'required',
            ],
            [
                'required' => 'schlamm'
            ]
        );

        // 1. Check PAN!
        // Try to Find the User
        $now = Carbon::now();
        $user = User::where('pan', $req->pan)->first();

        if(!$user) {
            // Found no User
            return $this->loginPanErrorResponse(
                "PAN & PIN Combination failed", 
                [
                    "pan" => ["auth.failed"],
                    "pin" => ["auth.failed"]
                ]
            );
        }

        // 2. Check dates "locked_until"
        if($user->locked_until) {

            // If Account is still Locked for XX Hours
            if($now <= $user->locked_until) {

                // Display time to wait. Should be 24 Hours
                return $this->sendLockedResponse($user, $now);
            }
        }

        // 3. Check "Failed Login Attempts"!
        if($user->failed_logins) {

            // if more than 15
            if($user->failed_logins >= 15) {
                // Add 24 Hours and Lock user, but reset failed attempts to 0
                $this->lockAccount($user, 24);

                // Display time to wait. Should be 24 Hours
                return $this->sendLockedResponse($user, $now);
            }
        }

        // 4. Check PAN && PIN
        if($req->pan != $user->pan || $req->pin != $user->pin)
        {
            // Login Failed
            $user->failed_logins += 1;
            $user->save();

            return $this->loginPanErrorResponse(
                "PAN & PIN Combination failed",
                [
                    "pan" => ["auth.failed"],
                    "pin" => ["auth.failed"]
                ]
            );
        } else {
            // Login Success
            \Auth::login($user);

            // Send PAN Login Credentials
            $token = (string) $this->guard()->getToken();
            $expiration = $this->guard()->getPayload()->get('exp');
            return response()->json([
                'token' => $token,
                'token_type' => 'bearer',
                'expires_in' => $expiration - time(),
            ]);
        }
    }

    /**
     * Lock the Users Account
     *
     * @param \App\User $user
     */
    protected function lockAccount($user, $hours) {
        // Lock Account
        $user->locked_until = Carbon::now()->copy()->addHours($hours);
        $user->failed_logins = 0;
        $user->save();
    }

    /**
     * Send the User is locked Response
     *
     * @param \App\User $user
     * @return \Illuminate\Http\JsonResponse
     */
    protected function sendLockedResponse($user, $now) {
        $diff = $now->diffInHours($user->locked_until);
        return $this->loginPanErrorResponse(
            __("auth.acc_locked", ['hours' => $diff]), [], $status=423
        );
    }

    /**
     * Return the Error-Response for failed Pan-Login
     *
     * @param  string $message
     * @param  array  $errors
     * @param  int    $status
     * @return \Illuminate\Http\JsonResponse
     */
    protected function loginPanErrorResponse($message, $errors = [], $status = 400) {
        return response()->json([
            "message" => $message,
            "errors" => $errors
        ], $status);
    }

    /**
     * Send the response after the user was authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    protected function sendLoginResponse(Request $request)
    {
        $this->clearLoginAttempts($request);

        $token = (string) $this->guard()->getToken();
        $expiration = $this->guard()->getPayload()->get('exp');

        return response()->json([
            'token' => $token,
            'token_type' => 'bearer',
            'expires_in' => $expiration - time(),
        ]);
    }

    /**
     * Get the failed login response instance.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function sendFailedLoginResponse(Request $request)
    {
        $user = $this->guard()->user();
        if ($user instanceof MustVerifyEmail && ! $user->hasVerifiedEmail()) {
            throw VerifyEmailException::forUser($user);
        }

        throw ValidationException::withMessages([
            $this->username() => [trans('auth.failed')],
        ]);
    }

    /**
     * Log the user out of the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request)
    {
        $this->guard()->logout();
    }
}
