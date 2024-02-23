<?php

namespace App\Http\Controllers;

use App\Events\UserRegistered;
use App\Http\Interfaces\UserRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class RegisterController extends Controller
{
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
     * Display the registration form.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function index()
    {
        return view('auth.register');
    }

    /**
     * Register a new user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'photo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'password' => 'required|min:6|confirmed',
        ]);

        if ($request->hasFile('photo')) {
            $picture = $request->file('photo');
            $photoName = 'photo_' . Str::random(10) . '.' . $picture->getClientOriginalExtension();

            $request->photo->move(public_path('profile_pictures'), $photoName);

            $request->merge(['photo_name' => $photoName]);
        }

        $user = $this->userRepository->create($request->only(['name', 'email', 'password', 'photo_name']));

        event(new UserRegistered($user));

        return redirect(route('login.view'))->withSuccess('You have registered, now sign in');
    }
}
