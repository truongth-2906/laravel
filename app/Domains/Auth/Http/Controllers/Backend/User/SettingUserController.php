<?php

namespace App\Domains\Auth\Http\Controllers\Backend\User;

use App\Domains\Auth\Http\Requests\Backend\User\SettingPasswordRequest;
use App\Domains\Auth\Services\UserService;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

/**
 * Class SettingUserController.
 */
class SettingUserController
{
    /**
     * @var UserService
     */
    protected UserService $userService;

    /**
     * ChangePasswordController constructor.
     *
     * @param UserService $userService
     */
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * @return Application|Factory|View
     */
    public function settingPassword()
    {
        return view('backend.auth.setting.password');
    }

    /**
     * @param SettingPasswordRequest $request
     * @return RedirectResponse
     */
    public function update(SettingPasswordRequest $request)
    {
        $changePassword = $this->userService->changePassword($request->all());

        $message = __('The password was failed update.');
        if ($changePassword) {
            $message = __('The password was successfully update.');
        }

        return redirect()->route('admin.setting.password')->with('message', $message);
    }
}
