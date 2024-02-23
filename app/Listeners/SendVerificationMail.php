<?php

namespace App\Listeners;

use App\Events\UserRegistered;
use App\Http\Traits\Mailer;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SendVerificationMail implements ShouldQueue
{
    use Mailer;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        // Constructor logic, if any.
    }

    /**
     * Handle the event.
     *
     * @param  \App\Events\UserRegistered  $event The event instance.
     * @return void
     */
    public function handle(UserRegistered $event)
    {
        if ($this->sendVerifyMail($event->user)) {
            info('Verification sent successfully');
            return;
        }

        info('Error while sending verification link');
    }

    /**
     * Send a verification email to the user.
     *
     * @param  \App\Models\User  $user The user to send the verification email to.
     * @return bool True if the verification email was sent successfully; otherwise, false.
     */
    public function sendVerifyMail(User $user)
    {
        try {
            $token = Str::random(20);
            $message = $this->setMessageForEmailVerify($token);

            if ($this->sendMail($message, 'Verify your Account', $user->email)) {
                $this->storeToken($token, $user->email);

                return true;
            }
        } catch (\Exception $e) {
            info($e);

            return false;
        }

        return false;
    }

    /**
     * Generate the message body for the email verification.
     *
     * @param  string  $token The verification token.
     * @return string The message body for the email verification.
     */
    public function setMessageForEmailVerify($token)
    {
        $link = url('/users/verified') . "?token=$token";

        return "Hi,<br/>please use this link to verify your account <a href=\"{$link}\">HERE</a>.<br/>Ignore this email if you didn't request for it.";
    }

    /**
     * Store the verification token in the database.
     *
     * @param  string  $token The verification token.
     * @param  string  $email The email address associated with the token.
     * @return bool True if the token was stored successfully; otherwise, false.
     */
    public function storeToken($token, $email)
    {
        return DB::table('password_resets')->insert([
            'email' => $email,
            'token' => $token,
            'created_at' => now(),
        ]);
    }
}
