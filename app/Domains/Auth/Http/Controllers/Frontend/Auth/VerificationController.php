<?php

namespace App\Domains\Auth\Http\Controllers\Frontend\Auth;

use App\Domains\Auth\Mail\VerifyEmail;
use App\Domains\Auth\Services\UserService;
use App\Domains\Auth\Services\VerifyUserService;
use App\Exceptions\GeneralException;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\VerifiesEmails;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

/**
 * Class VerificationController.
 */
class VerificationController
{
    /*
    |--------------------------------------------------------------------------
    | Email Verification Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling email verification for any
    | user that recently registered with the application. Emails may also
    | be re-sent if the user didn't receive the original email message.
    |
    */

    use VerifiesEmails;

    /**
     * @var VerifyUserService
     */
    protected VerifyUserService $verifyUserService;

    /**
     * @var UserService
     */
    protected UserService $userService;

    /**
     * @param VerifyUserService $verifyUserService
     * @param UserService $userService
     */
    public function __construct
    (
        VerifyUserService $verifyUserService,
        UserService       $userService
    )
    {
        $this->verifyUserService = $verifyUserService;
        $this->userService = $userService;
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
     * Show the email verification notice.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        return $request->user()->hasVerifiedEmail()
            ? redirect($this->redirectPath())
            : view('frontend.auth.verify');
    }

    /**
     * @return Application|Factory|View|RedirectResponse|Redirector
     */
    public function step1()
    {
        return !Auth::user()->isVerified() ? view('frontend.auth.verification.step1') : redirect($this->redirectPath());
    }

    /**
     * @return Application|Factory|View|RedirectResponse|Redirector
     */
    public function step2()
    {
        return !Auth::user()->isVerified() ? view('frontend.auth.verification.step2') : redirect($this->redirectPath());
    }

    /**
     * @param Request $request
     * @return Application|Factory|View|RedirectResponse
     * @throws GeneralException
     */
    public function step3(Request $request)
    {
        return $this->verifyUserService->verifyUserByToken($request->all() , $this->userService);
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     * @throws Exception
     */
    public function resend(Request $request): RedirectResponse
    {
        $verifyUser = $this->verifyUserService->updateOrCreate();

        Mail::to(Auth::user()->email)->send(new VerifyEmail(Auth::user(), $verifyUser->token));
        return redirect()->route('frontend.auth.email.verification.step2')->with('message', __('Resend verify mail success!'));
    }

    /**
     * @param Request $request
     * @return Application|Factory|View|RedirectResponse|Redirector
     * @throws GeneralException
     */
    public function verify(Request $request)
    {
        try {
            $verifyUser = $this->verifyUserService->getByToken(decrypt($request->route('token')), decrypt($request->route('id')));
            if ($verifyUser) {
                $this->userService->updateVerifyEmail($verifyUser->user);
                if (Auth::user()) {
                    Auth()->logout();
                }
                return view('frontend.auth.verification.step3');
            } else {
                return redirect($this->redirectPath());
            }
        } catch (\Exception $e) {
            throw new GeneralException(__('There was a problem verify email. Please try again.'));
        }
    }
}
