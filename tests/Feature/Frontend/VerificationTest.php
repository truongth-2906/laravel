<?php

namespace Tests\Feature\Frontend;

use App\Domains\Auth\Models\User;
use App\Domains\Auth\Models\VerifyUser;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

/**
 * Class VerificationTest.
 */
class VerificationTest extends TestCase
{
    /** @test */
    public function an_unverified_user_cannot_access_dashboard()
    {
        $user = User::factory()->unconfirmed()->create();

        $this->actingAs($user);

        $this->get('/account')->assertRedirect('/email/verification/step1');
    }

    /** @test */
    public function an_unverified_user_cannot_access_account()
    {
        $user = User::factory()->unconfirmed()->create();

        $this->actingAs($user);

        $this->get('/account')->assertRedirect('/email/verification/step1');
    }

    /** @test
     * @throws \Exception
     */
    public function a_user_can_resend_the_verification_email()
    {
        Notification::fake();

        $user = User::factory()->unconfirmed()->create();

        $this->actingAs($user);

        $this->get('/account')->assertRedirect('/email/verification/step1');

        $this->post('/email/verification/resend');
        $verifyUser = VerifyUser::updateOrCreate([
            'user_id' => $user->id,
        ], [
            'token' => randomNumber(VERIFY_MIN_NUMBER, VERIFY_MAX_NUMBER),
            'token_expired_at' => now()->addMinutes(VERIFY_TIME_EXPIRATION_TOKEN)
        ]);
        Mail::to($user->email)->send(new \App\Domains\Auth\Mail\VerifyEmail($user, $verifyUser->token));
    }
}
