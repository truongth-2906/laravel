<?php

namespace App\Domains\Auth\Http\Controllers\Frontend\Auth;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

/**
 * Class ForgotPasswordController.
 */
class ForgotPasswordController
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    use SendsPasswordResetEmails;

    /**
     * Display the form to request a password reset link.
     *
     * @return Factory|View
     */
    public function showLinkRequestForm()
    {
        return view('frontend.auth.passwords.email');
    }

    /**
     * @param Request $request
     * @return JsonResponse|RedirectResponse
     * @throws ValidationException
     */
    public function sendResetLinkEmail(Request $request)
    {
        $this->validateEmail($request);

        $response = $this->broker()->sendResetLink(
            $this->credentials($request)
        );

        return $response == Password::RESET_LINK_SENT
            ? $this->sendResetLinkResponse($request, $response)
            : $this->sendResetLinkFailedResponse($request, $response);
    }

    /**
     * @param Request $request
     * @param $response
     * @return JsonResponse|RedirectResponse
     */
    protected function sendResetLinkResponse(Request $request, $response)
    {
        $email = $request->get('email');
        return $request->wantsJson()
            ? new JsonResponse(['message' => trans($response)], 200)
            : redirect()->route('frontend.auth.password.resend', ['email' => $email])->with('message', __('Email sent to your mail!'));
    }

    /**
     * @param Request $request
     * @return Application|Factory|\Illuminate\Contracts\View\View
     */
    public function showResendRequestForm(Request $request)
    {
        $email = $request->email;
        return view('frontend.auth.passwords.resend', compact('email'));
    }

    /**
     * @param Request $request
     * @param $response
     * @return RedirectResponse
     * @throws ValidationException
     */
    protected function sendResetLinkFailedResponse(Request $request, $response)
    {
        if ($request->wantsJson()) {
            throw ValidationException::withMessages([
                'email' => [trans($response)],
            ]);
        }

        return back()
            ->withInput($request->only('email'))
            ->with('error', trans($response));
    }
}
