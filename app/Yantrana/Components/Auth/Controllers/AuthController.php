<?php

/**
 * AuthController.php - Controller file
 *
 * This file is part of the Auth component.
 *-----------------------------------------------------------------------------*/

namespace App\Yantrana\Components\Auth\Controllers;

use Illuminate\Http\Request;
use Illuminate\Auth\Events\Verified;
use App\Yantrana\Base\BaseController;
use App\Providers\RouteServiceProvider;
use App\Rules\CurrentPasswordCheckRule;
use App\Yantrana\Support\CommonRequest;
use Laravel\Socialite\Facades\Socialite;
use App\Yantrana\Support\CommonPostRequest;
use App\Yantrana\Components\Auth\AuthEngine;
use App\Yantrana\Components\Auth\Requests\LoginRequest;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use App\Yantrana\Components\Auth\Requests\RegisterRequest;

class AuthController extends BaseController
{
    /**
     * @var AuthEngine - Auth Engine
     */
    protected $authEngine;

    /**
     * Constructor
     *
     * @param  AuthEngine  $authEngine  - Auth Engine
     * @return void
     *-----------------------------------------------------------------------*/
    public function __construct(AuthEngine $authEngine)
    {
        $this->authEngine = $authEngine;
    }

    /**
     * Display the login view.
     *
     * @return string
     */
    public function loginPage()
    {
        return $this->loadView('auth.login');
    }

    /**
     * Authenticate user based on post form data.
     *
     * @param object UserLoginRequest $request
     * @return json object
     *---------------------------------------------------------------- */
    public function processLogin(LoginRequest $request)
    {
        $processReaction = $this->authEngine->processLogin($request);
        //check reaction code equal to 1
        if ($processReaction['reaction_code'] === 1) {
            $userStatus = auth()->user()->status;
            if (!$userStatus) {
                $this->authEngine->processLogout($request);

                return $this->responseAction(
                    $this->processResponse(
                        $processReaction,
                        [],
                        [],
                        false
                    ),
                    $this->redirectTo('auth.login', [], [
                        __tr('Your account is not in active mode, please contact us with details.'),
                    ])
                );
            } else
            // if not activated
            if (!$userStatus or ($userStatus == 4)) {
                $this->authEngine->processLogout($request);

                return $this->responseAction(
                    $this->processResponse(
                        $processReaction,
                        [],
                        [],
                        false
                    ),
                    $this->redirectTo('auth.login', [], [
                        __tr('Account is not activated yet, please check your email to activate account.'),
                    ])
                );
            }

            return $this->responseAction(
                $this->processResponse($processReaction, [], [], true),
                $this->redirectTo((hasCentralAccess()
                    ? 'central.console'
                    : (hasVendorAccess()
                        ? 'vendor.console'
                        : 'home')))
            );
        }

        return $this->responseAction(
            $this->processResponse($processReaction, [], [], true)
        );
    }

    /**
     * Display the password reset link request view.
     *
     * @return \Illuminate\View\View
     */
    public function forgotPasswordPage()
    {
        return view('auth.forgot-password');
    }

    /**
     * Handle an incoming password reset link request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function processForgotPasswordRequest(CommonPostRequest $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);
        $processReaction = $this->authEngine->processForgotPasswordRequest(
            $request->all()
        );
        //check reaction code equal to 1
        if ($processReaction['reaction_code'] === 1) {
            return $this->responseAction(
                $this->processResponse(
                    $processReaction,
                    [],
                    [],
                    true
                ),
                $this->redirectTo('auth.login', [], [
                    __tr('Link sent successfully to reset password'),
                ])
            );
        }

        return $this->responseAction(
            $this->processResponse($processReaction, [], [], true)
        );
    }

    /**
     * Display the password reset view.
     *
     * @return \Illuminate\View\View
     */
    public function resetPasswordPage(CommonRequest $request)
    {
        return $this->loadView('auth.reset-password', ['request' => $request]);
    }

    /**
     * Handle an incoming new password request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function processPasswordReset(CommonPostRequest $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|string|confirmed|min:8',
        ]);
        $processReaction = $this->authEngine->processPasswordReset($request);
        //check reaction code equal to 1
        if ($processReaction['reaction_code'] === 1) {
            return $this->responseAction(
                $this->processResponse($processReaction, [], [], true),
                $this->redirectTo('auth.login', [], [
                    __tr('Password reset successful, you can now login with new password.'),
                    'success',
                ])
            );
        }

        return $this->responseAction(
            $this->processResponse($processReaction, [], [], true)
        );
    }

    /**
     * Show the confirm password view.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function confirmPasswordPage()
    {
        return $this->loadView('auth.confirm-password');
    }

    /**
     * Confirm the user's password.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function processConfirmPassword(CommonPostRequest $request)
    {
        $request->validate([
            'password' => 'required|string',
        ]);

        $processReaction = $this->authEngine->processConfirmPassword($request);
        //check reaction code equal to 1
        if ($processReaction['reaction_code'] === 1) {
            return $this->responseAction(
                $this->processResponse($processReaction, [], [], true),
                $this->redirectTo(redirect()->intended(RouteServiceProvider::HOME)->getTargetUrl())
            );
        }

        $request->session()->passwordConfirmed();

        return $this->responseAction(
            $this->processResponse($processReaction, [], [], true)
        );

    }

    /**
     * Show the registration view
     */
    public function registrationPage()
    {
        return $this->loadView('auth.register');
    }

    /**
     * Authenticate user based on post form data.
     *
     * @param object UserLoginRequest $request
     * @return json object
     *---------------------------------------------------------------- */
    public function register(RegisterRequest $request)
    {
        
        // if vendor registration is off
        if(!getAppSettings('enable_vendor_registration')) {
            return $this->processResponse(2, [
                2 => __tr('Vendor Registrations are closed now.')
            ], [], true);
        }
        $processReaction = $this->authEngine->processRegistration($request->toArray());
        //check reaction code equal to 1
        if ($processReaction['reaction_code'] === 1) {
            return $this->responseAction(
                $this->processResponse($processReaction, [], [], true),
                $this->redirectTo('auth.login', [], [
                    __tr('Your account has been created successfully'),
                    'success',
                ])
            );
        }

        return $this->responseAction(
            $this->processResponse($processReaction, [], [], true)
        );
    }

    /**
     * Activation Required For New Vendor Registration
     *
     *
     * @return json object
     */
    public function activationRequiredRegister(RegisterRequest $request)
    {
        $processReaction = $this->authEngine->activationRequiredForRegistration($request->toArray());

        if (($processReaction['reaction_code'] === 1)) {
            return $this->responseAction(
                $this->processResponse($processReaction, [], [], true),
                $this->redirectTo('auth.login', [], __tr('Your account has been created successfully, to activate your account please check your email.'))
            );
        } else {
            return $this->responseAction(
                $this->processResponse($processReaction, [], [], true)
            );
        }
    }

    /**
     * Account Activation Route
     *
     * @param  mix  $userUid
     * @return string
     */
    public function accountActivation(Request $request, $userUid)
    {
        if (! $request->hasValidSignature()) {
            abort(401);
        }

        $processReaction = $this->authEngine->processAccountActivation($userUid);

        // Check if account activation process succeed
        if ($processReaction['reaction_code'] === 1) {
            return redirect()->route('auth.login')
                ->with([
                    'success' => 'true',
                    'message' => __tr('Your account has been activated successfully'),
                ]);
        }

        // if activation process failed then
        return redirect()->route('auth.login')
            ->with([
                'error' => 'true',
                'message' => __tr('Account Activation fail.Please try again later.'),
            ]);
    }

    /**
     * Destroy an authenticated session.
     */
    public function logout(CommonRequest $request)
    {
        $processReaction = $this->authEngine->processLogout($request);

        return $this->responseAction(
            $this->processResponse($processReaction, [], [], true),
            $this->redirectTo('auth.login', [], [
                __tr('You have been logged out successfully'),
                'success',
            ])
        );
    }

    /**
     * Display the email verification prompt.
     *
     * @return mixed
     */
    public function verifyEmailView(CommonRequest $request)
    {
        return $request->user()->hasVerifiedEmail()
            ? redirect()->intended(RouteServiceProvider::HOME)
            : $this->loadView('auth.verify-email');
    }

    /**
     * Mark the authenticated user's email address as verified.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function verifyEmail(EmailVerificationRequest $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->intended(RouteServiceProvider::HOME.'?verified=1');
        }

        if ($request->user()->markEmailAsVerified()) {
            event(new Verified($request->user()));
        }

        return redirect()->intended(RouteServiceProvider::HOME.'?verified=1');
    }

    /**
     * Send a new email verification notification.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function emailVerificationNotification(CommonRequest $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->intended(RouteServiceProvider::HOME);
        }

        $request->user()->sendEmailVerificationNotification();

        return back()->with('status', 'verification-link-sent');
    }

    /**
     * Handle an incoming new password request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function updatePassword(CommonPostRequest $request)
    {
        $request->attributes([
            'old_password' => __tr('current password'),
        ]);
        $request->validate([
            'old_password' => ['required', 'min:6', new CurrentPasswordCheckRule],
            'password' => ['required', 'min:6', 'confirmed', 'different:old_password'],
            'password_confirmation' => ['required', 'min:6'],
        ]);

        $processReaction = $this->authEngine->processUpdatePassword($request);

        //check reaction code equal to 1
        return $this->processResponse($processReaction, [], [], true);
    }

    /**
     * Authenticate user Social login with Google.
     *
     * @param object Social login Redirect
     *
     * @return json object
     *---------------------------------------------------------------- */
    public function redirectToGoogle()
    {
        config([
            'services.google.redirect' => route('login.google.callback'),
            'services.google.client_id' => getAppSettings('google_client_id'),
            'services.google.client_secret' => getAppSettings('google_client_secret'),
        ]);
        return Socialite::driver('google')->redirect();
    }

    /**
     * Authenticate user Social login with Google.
     *
     * @param object Social login Request
     *
     * @return json object
     *---------------------------------------------------------------- */
    public function handleGoogleCallback(CommonRequest $request)
    {
        //Request deny
        $userDenyRequest = $request->input('error');
        $provider = 'google';
        // check the request is deny then redirect user on login page
        if (isset($userDenyRequest) and $userDenyRequest === 'access_denied') {
            return redirect()->route('auth.login');
        }

        try {
            config([
                'services.google.redirect' => route('login.google.callback'),
                'services.google.client_id' => getAppSettings('google_client_id'),
                'services.google.client_secret' => getAppSettings('google_client_secret'),

            ]);
            $processReaction = $this->authEngine->processCreateSocialCallBack($provider);
            if ($processReaction->success()) {
                return redirect()->route('home')->with([
                    'message' => __tr('Welcome'),
                ]);
            }
            return redirect()->route('auth.login')->with([
                'error' => 'true',
                'message' => $processReaction->message(),
            ]);
        } catch (Expression $e) {
            throw $e;
        }
    }

    /**
     * Authenticate user Social login with Facebook.
     *
     * @param object Social login Redirect
     *
     * @return json object
     *---------------------------------------------------------------- */
    public function redirectToFacebook()
    {
        try {
            config([
                'services.facebook.redirect' => route('login.facebook.callback'),
                'services.facebook.client_id' => getAppSettings('facebook_client_id'),
                'services.facebook.client_secret' => getAppSettings('facebook_client_secret'),
            ]);
            return Socialite::driver('facebook')->redirect();
        } catch (\Exception $e) {
            return redirect()->route('auth.login')->with([
                'message' => __tr('Something went wrong.'),
            ]);
        }
    }

    /**
     * Authenticate user Social login with Facebook.
     *
     * @param object Social login Request
     *
     * @return json object
     *---------------------------------------------------------------- */
    public function handleFacebookCallback(CommonRequest $request)
    {
        $userDenyRequest = $request->input('error');
        $provider = 'facebook';
        // check the request is deny then redirect user on login page
        if (isset($userDenyRequest) and $userDenyRequest === 'access_denied') {
            return redirect()->route('auth.login');
        }
        try {
            config([
                'services.facebook.redirect' => route('login.facebook.callback'),
                'services.facebook.client_id' => getAppSettings('facebook_client_id'),
                'services.facebook.client_secret' => getAppSettings('facebook_client_secret'),
            ]);
            $processReaction = $this->authEngine->processCreateSocialCallBack($provider);
            if ($processReaction->success()) {
                return redirect()->route('home')->with([
                    'message' => __tr('Welcome'),
                ]);
            }
            return redirect()->route('auth.login')->with([
                'error' => 'true',
                'message' => $processReaction->message(),
            ]);
            // return $this->processResponse($processReaction, [], [], true);
        } catch (Expression $e) {
            throw $e;
        }
    }
}
