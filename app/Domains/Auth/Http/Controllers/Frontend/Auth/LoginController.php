<?php

namespace App\Domains\Auth\Http\Controllers\Frontend\Auth;

use App\Domains\Auth\Events\User\LeaveChannelSameApp;
use App\Domains\Auth\Events\User\UserLoggedIn;
use App\Domains\Auth\Mail\VerifyEmail;
use App\Domains\Auth\Services\VerifyUserService;
use App\Rules\Captcha;
use Exception;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use LangleyFoxall\LaravelNISTPasswordRules\PasswordRules;
use Illuminate\Http\JsonResponse;
use Log;
use Str;

/**
 * Class LoginController.
 */
class LoginController
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * @var VerifyUserService
     */
    protected VerifyUserService $verifyUserService;

    /**
     * @param VerifyUserService $verifyUserService
     */
    public function __construct(
        VerifyUserService $verifyUserService
    ) {
        $this->verifyUserService = $verifyUserService;
    }

    /**
     * Where to redirect users after login.
     *
     * @return string
     */
    public function redirectPath()
    {
        return route(homeRoute());
    }

    /**
     * Show the application's login form.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showLoginForm()
    {
        return view('frontend.auth.login');
    }

    /**
     * Validate the user login request.
     *
     * @param \Illuminate\Http\Request $request
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function validateLogin(Request $request)
    {
        $request->validate([
            $this->username() => ['required', 'max:255', 'string'],
            'password' => array_merge(['max:100'], PasswordRules::login()),
            'g-recaptcha-response' => ['required_if:captcha_status,true', new Captcha],
        ], [
            'g-recaptcha-response.required_if' => __('validation.required', ['attribute' => 'captcha']),
        ]);
    }

    /**
     * Overidden for 2FA
     * https://github.com/DarkGhostHunter/Laraguard#protecting-the-login.
     *
     * Attempt to log the user into the application.
     *
     * @param \Illuminate\Http\Request $request
     * @return bool
     */
    protected function attemptLogin(Request $request)
    {
        try {
            return $this->guard()->attempt(
                $this->credentials($request),
                $request->filled('remember')
            );
        } catch (HttpResponseException $exception) {
            $this->incrementLoginAttempts($request);

            throw $exception;
        }
    }

    /**
     * The user has been authenticated.
     *
     * @param Request $request
     * @param $user
     * @return mixed
     */
    protected function authenticated(Request $request, $user)
    {
        if (!$user->isVerified()) {
            $verifyUser = $this->verifyUserService->updateOrCreate();
            Mail::to($user->email)->send(new VerifyEmail($user, $verifyUser->token));
            return redirect()->route('frontend.auth.email.verification.step1');
        }

        event(new UserLoggedIn($user));

        if (config('base.access.user.single_login')) {
            auth()->logoutOtherDevices($request->password);
        }
    }

    /**
     * Log the user out of the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        $token = echo_token();

        $this->guard()->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        $this->handleLeaveChannelsAfterLoggedOut($token);

        if ($response = $this->loggedOut($request)) {
            return $response;
        }

        return $request->wantsJson()
            ? new JsonResponse([], 204)
            : redirect('/');
    }

    /**
     * @param string $echoToken
     * @return void
     */
    protected function handleLeaveChannelsAfterLoggedOut(string $echoToken)
    {
        try {
            event(new LeaveChannelSameApp($echoToken));
        } catch (Exception $e) {
            Log::info($e->getMessage());
        }
    }

    /**
     * Send the response after the user was authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    protected function sendLoginResponse(Request $request)
    {
        $request->session()->regenerate();
        $request->session()->put('echo_token', Str::random(40));

        $this->clearLoginAttempts($request);

        if ($response = $this->authenticated($request, $this->guard()->user())) {
            return $response;
        }

        return $request->wantsJson()
            ? new JsonResponse([], 204)
            : redirect()->intended($this->redirectPath());
    }
}
