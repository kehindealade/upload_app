<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\ResetPasswordController;
use App\Http\Controllers\VerificationController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::prefix('users')->group(function () {
    //---------- Register ---------//
    Route::get('register', [RegisterController::class, 'index'])->name('register.view');
    Route::post('register', [RegisterController::class, 'register'])->name('register');

    //---------- Login -----------//
    Route::get('login', [LoginController::class, 'index'])->name('login.view');
    Route::post('login', [LoginController::class, 'login'])->name('login');

    //--------- Reset Passsword --------//
    Route::get('reset', [ResetPasswordController::class, 'resetForm'])->name('password.form.reset');
    Route::post('reset', [ResetPasswordController::class, 'sendResetLink'])->name('password.reset');

    //--------- Reset Password -----------//
    Route::get('verify-password', [ResetPasswordController::class, 'verifyToken']);
    Route::get('change-password', [ResetPasswordController::class, 'showResetForm'])->name('password.reset.form');
    Route::post('change-password', [ResetPasswordController::class, 'updatePassword'])->name('password.update');

    Route::middleware(['auth'])->group(function () {
        Route::post('logout', [LoginController::class, 'logout'])->name('logout');

        //---------- Email Verification ------------//
        Route::get('verify-account', [VerificationController::class, 'showVerifyForm'])->name('verify.account');
        Route::get('verify', [VerificationController::class, 'sendVerifyMail'])->name('email.verify');
        Route::get('verified', [VerificationController::class, 'verifyToken'])->name('verified');

        Route::middleware(['must.verify'])->group(function () {
            Route::get('home', [HomeController::class, 'index'])->name('home');
        });
    });
});

// Auth::routes();

// Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
