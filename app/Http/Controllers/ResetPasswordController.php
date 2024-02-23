<?php

namespace App\Http\Controllers;

use App\Http\Interfaces\UserRepositoryInterface;
use App\Http\Traits\Mailer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ResetPasswordController extends Controller
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
     * Display the reset password form.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function resetForm()
    {
        return view('auth.passwords.email');
    }

    /**
     * Send the reset password link.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function sendResetLink(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email|exists:users,email'
            ]);

            $token = Str::random(20);
            $message = $this->setMessageForPasswordReset($token);

            if ($this->sendMail($message, 'Password Reset', $request->email)) {
                $this->storeToken($token, $request->email);

                return redirect(route('password.form.reset'))->withSuccess('Password Reset Link Sent Successfully!');
            }
        } catch (\Exception $e) {
            info($e);
            return redirect(route('password.form.reset'))->withError('Can\'t Reset Password');
        }

        return redirect(route('password.form.reset'))->withError('Can\'t Reset Password');
    }

    /**
     * Set the message for password reset.
     *
     * @param  string  $token
     * @return string
     */
    public function setMessageForPasswordReset($token)
    {
        $link = url('/users/verify-password') . "?token=$token";

        return "Hi,<br/>please use this link to reset your password <a href=\"{$link}\">HERE</a>.<br/>Ignore this email if you didn't request for it.";
    }

    /**
     * Store the reset password token.
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
     * Verify the reset password token.
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
            return redirect(route('password.form.reset'))->withError('Link expired, request for a new link.');
        }

        return redirect()->route('password.reset.form', ['token' => $token, 'email' => $resetToken->email]);
    }

    /**
     * Show the reset password form.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function showResetForm()
    {
        return view('auth.passwords.reset', ['token' => request()->query('token'), 'email' => request()->query('email')]);
    }

    /**
     * Update the user password.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'email' => 'exists:users,email',
            'password' => 'required|min:6|confirmed',
        ]);

        $resetTokenValid = DB::table('password_resets')
            ->where('token', $request->token)
            ->where('email', $request->email)
            ->where('created_at', '>=', now()->subMinutes(20))
            ->orderBy('created_at', 'desc')->first();

        if (!$resetTokenValid) {
            return redirect(route('password.form.reset'))->withError('Can\'t Reset Password, Try Again');
        }

        $user = $this->userRepository->findByEmail($request->email);
        $user->password = bcrypt($request->password);
        $user->save();

        return redirect(route('login.view'))->withSuccess('Password Reset Successfully, Login with it');
    }
}
