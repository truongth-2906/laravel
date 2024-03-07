<?php

namespace App\Http\Controllers\Frontend\User;

use App\Domains\Auth\Http\Requests\Frontend\Auth\ChangePasswordRequest;
use App\Domains\Auth\Services\UserService;

/**
 * Class AccountController.
 */
class AccountController
{
    /**
     * @var UserService
     */
    protected $userService;

    /**
     * @param UserService $userService
     */
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function index()
    {
        return view('frontend.user.account');
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function settingPassword()
    {
        return view('frontend.includes.setting-password');
    }

    /**
     * @param ChangePasswordRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function changePassword(ChangePasswordRequest $request)
    {
        $changePassword = $this->userService->changePassword($request->all());

        $message = __('The password was failed update.');
        if ($changePassword) {
            $message = __('The password was successfully update.');
        }

        return redirect()->route('frontend.user.setting.settingPassword')->with('message', $message);
    }
}
