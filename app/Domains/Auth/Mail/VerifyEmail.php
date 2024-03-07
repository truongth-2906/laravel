<?php

namespace App\Domains\Auth\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\URL;

class VerifyEmail extends Mailable
{
    use Queueable, SerializesModels;

    protected $user;
    protected $token;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($user, $token)
    {
        $this->user = $user;
        $this->token = $token;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject(__('Verify Your E-mail Address'))
            ->view('frontend.auth.verification.mail.verify')
            ->with([
                'user' => $this->user,
                'token' => $this->token,
                'url' => $this->verificationUrl($this->user->id, $this->token)
            ]);
    }

    /**
     * Get the verification URL for the given notifiable.
     *
     * @param $userId
     * @param $token
     * @return string
     */
    protected function verificationUrl($userId, $token)
    {
        return URL::temporarySignedRoute(
            'frontend.verification.verify',
            Carbon::now()->addMinutes(Config::get('auth.verification.expire', VERIFY_TIME_EXPIRATION_TOKEN)),
            [
                'id' => encrypt($userId),
                'token' => encrypt($token),
            ]
        );
    }
}
