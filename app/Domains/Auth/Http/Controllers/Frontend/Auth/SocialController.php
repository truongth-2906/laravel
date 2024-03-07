<?php

namespace App\Domains\Auth\Http\Controllers\Frontend\Auth;

use App\Domains\Auth\Events\User\UserLoggedIn;
use App\Domains\Auth\Mail\VerifyEmail;
use App\Domains\Auth\Services\UserService;
use App\Domains\Auth\Services\VerifyUserService;
use Exception;
use Illuminate\Http\Response;
use Laravel\Socialite\Facades\Socialite;
use Mail;

/**
 * Class SocialController.
 */
class SocialController
{
    /**
     * @param $provider
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function redirect($provider)
    {
        return Socialite::driver($provider)->redirect();
    }

    /**
     * @param $provider
     * @param UserService $userService
     * @param VerifyUserService $verifyUserService
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \App\Exceptions\GeneralException
     */
    public function callback($provider, UserService $userService, VerifyUserService $verifyUserService)
    {
        try {
            $user = $userService->registerProvider(Socialite::driver($provider)->user(), $provider);
            auth()->login($user);

            if (request()->hasSession()) {
                request()->session()->put('auth.password_confirmed_at', time());
            }

            if (!$user->isVerified()) {
                $verifyUser = $verifyUserService->updateOrCreate();
                Mail::to($user->email)->send(new VerifyEmail($user, $verifyUser->token));
                return redirect()->route('frontend.auth.email.verification.step1');
            }

            event(new UserLoggedIn($user));

            return redirect()->intended($this->redirectPath());
        } catch (Exception $e) {
            if (auth()->check()) {
                auth()->logout();
            }

            return redirect()->route('frontend.auth.login')
                ->with('error', $e->getCode() == Response::HTTP_NOT_FOUND ? __('auth.failed') : __('Login failed.'));
        }
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
}
