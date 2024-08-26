<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\VerifyEmailController;
use App\Yantrana\Components\{
    Auth\Controllers\AuthController
};
use Illuminate\Support\Facades\Route;

Route::group([
    'prefix' => 'auth',
], function () {
    Route::group([
        'middleware' => ['guest'],
    ], function () {
        // login page
        Route::get('/login', [
            AuthController::class,
            'loginPage',
        ])->name('auth.login');
        // login process
        Route::post('/login', [
            AuthController::class,
            'processLogin',
        ])->name('auth.login.process');
        //forgot password page
        Route::get('/forgot-password', [
            AuthController::class,
            'forgotPasswordPage',
        ])->name('auth.password.request');
        // forgot password process
        Route::post('/forgot-password', [
            AuthController::class,
            'processForgotPasswordRequest',
        ])->name('auth.password.request.process');

        Route::get('/reset-password/{token}', [
            AuthController::class, 'resetPasswordPage',
        ])->name('auth.password.reset');

        Route::post('/reset-password', [
            AuthController::class, 'processPasswordReset',
        ])->name('auth.password.reset.process');

        Route::get('/register/vendor', [
            AuthController::class,
            'registrationPage',
        ])->name('auth.register');

        Route::post('/register/vendor', [
            AuthController::class,
            'register',
        ])->name('auth.register.process');

        Route::post('/register/vendor/activation', [
            AuthController::class,
            'activationRequiredRegister',
        ])->name('activation_required.auth.register.process');

        // Account Activation
        Route::get('/{userUid}/account-activation', [
            AuthController::class,
            'accountActivation',
        ])->name('user.account.activation');

        /**
         * Social Logins
         */
        // Google login
        Route::get('/login-google/redirect', [
            AuthController::class,
            'redirectToGoogle'
        ])->name('login.google');

        Route::get('/login/callback/google', [
            AuthController::class,
            'handleGoogleCallback'
        ])->name('login.google.callback');


        // Facebook  login
        Route::get('/login-facebook/redirect', [
            AuthController::class,
            'redirectToFacebook'
        ])->name('login.facebook');

        Route::get('/login/callback/facebook', [
            AuthController::class,
            'handleFacebookCallback'
        ])->name('login.facebook.callback');

    });

    Route::group([
        'middleware' => ['auth', 'throttle:6,1'],
    ], function () {
        Route::get('/confirm-password', [
            AuthController::class,
            'confirmPasswordPage',
        ])->name('auth.password.confirm');

        Route::post('/confirm-password', [
            AuthController::class,
            'processConfirmPassword',
        ])->name('auth.password.confirm.process');

        Route::post('/logout', [
            AuthController::class,
            'logout',
        ])->name('auth.logout');

        Route::get('/verify-email', [
            AuthController::class,
            'verifyEmailView',
        ])->name('verification.notice');

        Route::get('/verify-email/{id}/{hash}', [
            AuthController::class,
            'verifyEmail',
        ])->middleware(['signed'])->name('verification.verify');

        Route::post('/email/verification-notification', [
            AuthController::class,
            'emailVerificationNotification',
        ])->name('verification.send');

        Route::post('/update-password', [
            AuthController::class,
            'updatePassword',
        ])->name('auth.password.update.process');
    });
});
/*
Route::get('/register', [RegisteredUserController::class, 'create'])
    ->middleware('guest')
    ->name('register');

Route::post('/register', [RegisteredUserController::class, 'store'])
    ->middleware('guest');

Route::get('/login', [AuthenticatedSessionController::class, 'create'])
    ->middleware('guest')
    ->name('login');

Route::post('/login', [AuthenticatedSessionController::class, 'store'])
    ->middleware('guest');

Route::get('/forgot-password', [PasswordResetLinkController::class, 'create'])
    ->middleware('guest')
    ->name('password.request');

Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])
    ->middleware(['guest'])
    ->name('password.email');

Route::get('/reset-password/{token}', [NewPasswordController::class, 'create'])
    ->middleware(['guest'])
    ->name('password.reset');

Route::post('/reset-password', [NewPasswordController::class, 'store'])
    ->middleware(['guest'])
    ->name('password.update'); */

/* Route::get('/verify-email', [EmailVerificationPromptController::class, '__invoke'])
    ->middleware(['auth'])
    ->name('verification.notice'); */

/* Route::get('/verify-email/{id}/{hash}', [VerifyEmailController::class, '__invoke'])
    ->middleware(['auth', 'signed', 'throttle:6,1'])
    ->name('verification.verify'); */

/* Route::post('/email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
    ->middleware(['auth', 'throttle:6,1'])
    ->name('verification.send'); */
/*
Route::get('/confirm-password', [ConfirmablePasswordController::class, 'show'])
    ->middleware(['auth'])
    ->name('password.confirm');

Route::post('/confirm-password', [ConfirmablePasswordController::class, 'store'])
    ->middleware(['auth']); */

/* Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->name('logout')
    ->middleware('auth');
 */
