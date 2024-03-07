<?php

namespace App\Domains\Auth\Http\Controllers\Frontend\Auth;

use App\Domains\Auth\Http\Requests\Frontend\Auth\RegisterRequest;
use App\Domains\Auth\Mail\VerifyEmail;
use App\Domains\Auth\Services\UserService;
use App\Domains\Auth\Services\VerifyUserService;
use App\Exceptions\GeneralException;
use Illuminate\Auth\Events\Registered;
use Illuminate\Contracts\View\Factory;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Mail;

/**
 * Class RegisterController.
 */
class RegisterController
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * @var UserService
     */
    protected UserService $userService;

    /**
     * @var VerifyUserService
     */
    protected VerifyUserService $verifyUserService;

    /**
     * RegisterController constructor.
     *
     * @param UserService $userService
     * @param VerifyUserService $verifyUserService
     */
    public function __construct(
        UserService       $userService,
        VerifyUserService $verifyUserService
    ) {
        $this->userService = $userService;
        $this->verifyUserService = $verifyUserService;
    }

    /**
     * Where to redirect users after registration.
     *
     * @return string
     */
    public function redirectPath()
    {
        return route(homeRoute());
    }

    /**
     * Show the application registration form.
     *
     * @return Factory|\Illuminate\View\View
     */
    public function showRegistrationForm()
    {
        abort_unless(config('base.access.user.registration'), 404);

        return view('frontend.auth.register');
    }

    /**
     * @param RegisterRequest $request
     * @return RedirectResponse
     * @throws GeneralException
     */
    public function register(RegisterRequest $request): RedirectResponse
    {
        event(new Registered($user = $this->userService->registerUser($request->all())));

        $this->guard()->login($user);

        $verifyUser = $this->verifyUserService->updateOrCreate();

        Mail::to($user->email)->send(new VerifyEmail($user, $verifyUser->token));

        return redirect()->route('frontend.auth.email.verification.step1');
    }
}
