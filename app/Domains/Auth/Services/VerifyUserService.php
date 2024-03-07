<?php

namespace App\Domains\Auth\Services;

use App\Domains\Auth\Models\VerifyUser;
use App\Exceptions\GeneralException;
use App\Services\BaseService;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * Class VerifyUserService.
 */
class VerifyUserService extends BaseService
{
    /**
     * @param VerifyUser $verifyUser
     */
    public function __construct(VerifyUser $verifyUser)
    {
        $this->model = $verifyUser;
    }

    /**
     * @param $userId
     * @param $token
     * @return mixed
     */
    public function getByToken($token, $userId = null)
    {
        return $this->model::where('user_id', $userId ?? Auth::user()->id)->where('token', $token)->first();
    }

    /**
     * @return mixed
     * @throws Exception
     */
    public function updateOrCreate()
    {
        return $this->model::updateOrCreate([
            'user_id' => Auth::user()->id,
        ], [
            'token' => randomNumber(VERIFY_MIN_NUMBER, VERIFY_MAX_NUMBER),
            'token_expired_at' => now()->addMinutes(VERIFY_TIME_EXPIRATION_TOKEN)
        ]);
    }

    /**
     * @param $data
     * @param $userService
     * @return Application|Factory|View|RedirectResponse
     * @throws GeneralException
     */
    public function verifyUserByToken($data, $userService)
    {
        try {
            $token = $data['digit-1'] . $data['digit-2'] . $data['digit-3'] . $data['digit-4'];
            $verifyUser = $this->getByToken($token);
            if ($verifyUser) {
                if ($verifyUser->token_expired_at > now()) {
                    DB::beginTransaction();
                    $userService->updateVerifyEmail($verifyUser->user);
                    DB::commit();
                    Auth()->logout();
                    return view('frontend.auth.verification.step3');
                } else {
                    return redirect()->route('frontend.auth.email.verification.step2')->with('error', __('Code has expired. Please enter a new code.'));
                }
            } else {
                return redirect()->route('frontend.auth.email.verification.step2')->with('error', __('Wrong code. Please re-enter the code'));
            }
        } catch (Exception $e) {
            DB::rollBack();
            throw new GeneralException(__('There was a problem verify email. Please try again.'));
        }
    }
}
