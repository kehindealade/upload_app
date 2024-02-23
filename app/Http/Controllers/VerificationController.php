<?php

namespace App\Http\Controllers;

use App\Http\Interfaces\UserRepositoryInterface;
use App\Http\Traits\Mailer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class VerificationController extends Controller
{
    use Mailer;

    protected $userRepository;

    /**
     * Create a new controller instance.
     *
     * @param  \App\Http\Interfaces\UserRepositoryInterface  $userRepository The user repository interface implementation.
     * @return void
     */
    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Display the email verification form.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function showVerifyForm()
    {
        return view('email_verify');
    }

    /**
     * Send the email verification link.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function sendVerifyMail()
    {
        try {
            $token = Str::random(20);
            $message = $this->setMessageForEmailVerify($token);

            if ($this->sendMail($message, 'Verify your Account', auth()->user()->email)) {
                $this->storeToken($token, auth()->user()->email);

                return redirect(route('verify.account'))->withSuccess('Email Verification Link Sent Successfully!');
            }
        } catch (\Exception $e) {
            info($e);
            return redirect(route('verify.account'))->withError('Can\'t Send Email Verification Link');
        }

        return redirect(route('verify.account'))->withError('Can\'t Send Email Verification Link');
    }

    /**
     * Set the message for email verification.
     *
     * @param  string  $token
     * @return string
     */
    public function setMessageForEmailVerify($token)
    {
        $link = url('/users/verified') . "?token=$token";

        return "Hi,<br/>please use this link to verify your account <a href=\"{$link}\">HERE</a>.<br/>Ignore this email if you didn't request for it.";
    }

    /**
     * Store the email verification token.
     *
     * @param  string  $token
     * @param  string  $email
     * @return bool
     */
    public function storeToken($token, $email)
    {
        return DB::table('password_resets')->insert([
            'email' => $email,
            'token' => $token,
            'created_at' => now(),
        ]);
    }

    /**
     * Verify the email verification token.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function verifyToken()
    {
        $token = request()->query('token');

        $resetToken = DB::table('password_resets')
            ->where('token', $token)
            ->where('created_at', '>=', now()->subMinutes(20))
            ->orderBy('created_at', 'desc')->first();

        if (!$resetToken) {
            return redirect(route('verify.account'))->withError('Link expired, Click Reset Link Again.');
        }

        $user = $this->userRepository->findByEmail(auth()->user()->email);
        $user->email_verified_at = now();
        $user->save();

        return redirect()->route('home')->withSuccess('Account verified successfully');
    }
}
