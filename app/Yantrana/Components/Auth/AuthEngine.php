<?php

/**
 * AuthEngine.php - Main component file
 *
 * This file is part of the Auth component.
 *-----------------------------------------------------------------------------*/

namespace App\Yantrana\Components\Auth;

use Carbon\Carbon;
use Illuminate\Support\Str;
use App\Yantrana\Base\BaseEngine;
use App\Yantrana\Base\BaseMailer;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Auth\Events\PasswordReset;
use App\Yantrana\Components\Auth\Repositories\AuthRepository;
use App\Yantrana\Components\Auth\Interfaces\AuthEngineInterface;
use App\Yantrana\Components\Auth\Repositories\LoginLogRepository;
use App\Yantrana\Components\Vendor\Repositories\VendorRepository;
use App\Yantrana\Components\Auth\Notifications\ResetPassword as ResetPasswordMail;

class AuthEngine extends BaseEngine implements AuthEngineInterface
{
    /**
     * @var AuthRepository - Auth Repository
     */
    protected $authRepository;

    /**
     * @var VendorRepository - Vendor Repository
     */
    protected $vendorRepository;

    /**
     * @var BaseMailer
     */
    protected $baseMailer;

    /**
     * @var LoginLogRepository
     */
    protected $loginLogRepository;

    /**
     * Constructor
     *
     * @param  AuthRepository  $authRepository  - Auth Repository
     * @return void
     *-----------------------------------------------------------------------*/
    public function __construct(AuthRepository $authRepository, VendorRepository $vendorRepository, BaseMailer $baseMailer, LoginLogRepository $loginLogRepository)
    {
        $this->authRepository = $authRepository;
        $this->vendorRepository = $vendorRepository;
        $this->baseMailer = $baseMailer;
        $this->loginLogRepository = $loginLogRepository;
    }

    /**
     * Authenticate user based on post form data.
     *
     * @param object UserLoginRequest $request
     * @return json object
     *---------------------------------------------------------------- */
    public function processLogin($request)
    {
        $request->authenticate();
        $request->session()->regenerate();
        // check vendor and user account status
        if((getVendorUid() and ((getUserAuthInfo('vendor_status') != 1) or (getUserAuthInfo('status') != 1)))) {
            Auth::logout();
            $request->session()->invalidate();
            return $this->engineFailedResponse([
                'show_message' => true,
            ], __tr('Vendor/User account is not in active state'));
        }
        if((getUserAuthInfo('status') != 1)) {
            Auth::logout();
            return $this->engineFailedResponse([
                'show_message' => true,
            ], __tr('Your account is in inactive status'));
        }
        $user = auth()->user();
        // store login log
        $this->loginLogRepository->storeIt([
            'role' => $user['user_roles__id'],
            'email' => $user['email'],
            'user_id' => $user['_id'],
            'ip_address' => $request->ip(),
        ]);

        return $this->engineSuccessResponse([
            // 'auth_info'     => getUserAuthInfo(1),
            // 'intendedUrl' => Session::get('intendedUrl'),
            'show_message' => true,
        ], __tr('Welcome, you are logged in successfully.'));
    }

    /**
     * Authenticate user based on post form data.
     *
     * @param object UserLoginRequest $request
     * @return json object
     *---------------------------------------------------------------- */
    public function processLogout($request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return $this->engineSuccessResponse([
            // 'auth_info'     => getUserAuthInfo(1),
            // 'intendedUrl' => Session::get('intendedUrl'),
            'show_message' => true,
        ], __tr('You are logged out successfully.'));
    }

    /**
     * Handle an incoming password reset link request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function processForgotPasswordRequest(array $request)
    {
        // We will send the password reset link to this user. Once we have attempted
        // to send the link, we will examine the response then see the message we
        // need to show to the user. Finally, we'll send out a proper response.
        $status = Password::sendResetLink([
            'email' => $request['email'],
        ], function ($user, $token) {
            $user->notify(new ResetPasswordMail($token));
        });
        /* $status == Password::RESET_LINK_SENT
                    ? back()->with('status', __tr($status))
                    : back()->withErrors(['email' => __tr($status)]); */
        if ($status == Password::RESET_LINK_SENT) {
            return $this->engineSuccessResponse([
                'status' => $status,
                'show_message' => true,
            ], __tr('Link sent successfully to reset password.'));
        }

        return $this->engineFailedResponse([
            'errors' => [
                'email' => __($status),
            ],
            'status' => $status,
            'show_message' => true,
        ], __tr('Sending a link is failed'));
    }

    /**
     * Handle an incoming password reset link request.
     *
     * @param  \Illuminate\Http\Request  $request
     */
    public function processPasswordReset($request)
    {
        // Here we will attempt to reset the user's password. If it is successful we
        // will update the password on an actual user model and persist it to the
        // database. Otherwise we will parse the error and return the response.
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user) use ($request) {
                $user->forceFill([
                    'password' => Hash::make($request->password),
                    'remember_token' => Str::random(60),
                ])->save();
                event(new PasswordReset($user));
            }
        );

        // If the password was successfully reset, we will redirect the user back to
        // the application's home authenticated view. If there is an error we can
        // redirect them back to where they came from with their error message.
        /*  return $status == Password::PASSWORD_RESET
                    ? redirect()->route('login')->with('status', __tr($status))
                    : back()->withInput($request->only('email'))
                            ->withErrors(['email' => __tr($status)]); */

        if ($status == Password::PASSWORD_RESET) {
            return $this->engineSuccessResponse([
                'status' => $status,
                'show_message' => true,
            ], __tr('Password Reset successfully'));
        }

        return $this->engineFailedResponse([
            'email' => __tr($status),
            'status' => $status,
            'show_message' => true,
        ], __tr('Failed to reset password'));
    }

    /**
     * Handle an incoming password reset link request.
     *
     * @param  \Illuminate\Http\Request  $request
     */
    public function processConfirmPassword($request)
    {
        if (! Auth::validate([
            'email' => $request->user()->email,
            'password' => $request->password,
        ])) {
            /* return back()->withErrors([
                'password' => __('auth.password'),
            ]); */
            return $this->engineFailedResponse([
                'errors' => [
                    'password' => __('auth.password'),
                ],
                'show_message' => true,
            ], __tr('Password Verification failed'));
        }

        return $this->engineSuccessResponse([
            'show_message' => true,
        ], __tr('Password verified successfully'));
    }

    /**
     * User Sign Process.
     *
     * @param  array  $inputData
     *
     *-----------------------------------------------------------------------*/
    public function processRegistration($inputData)
    {
        $transactionResponse = $this->authRepository->processTransaction(function () use ($inputData) {
            // add to to title
            $vendor = $this->vendorRepository->storeVendor([
                'title' => $inputData['vendor_title'],
                'slug' => Str::lower(Str::slug($inputData['username'],'_')),
                'status' => 1, // Active
                'type' => 1, // types of vendor restaurant etc
            ]);
            if (! $vendor) {
                return $this->authRepository->transactionResponse(2, ['show_message' => true], __tr('Failed to register user'));
            }
            $inputData['status'] = 1; // Active
            $inputData['user_roles__id'] = 2; //vendor admin
            $inputData['vendors__id'] = $vendor->_id;
            // Store user
            $newUser = $this->authRepository->storeUser($inputData);
            // Check if user not stored successfully
            if ($newUser) {
                 //check if welcome email setting active
                if (getAppSettings('send_welcome_email')) {
                    $this->userWelcomeNotifyMail($newUser);
                }
                    return $this->authRepository->transactionResponse(1, array_merge(['show_message' => true], $newUser->toArray()), __tr('Your account created successfully.'));
            }

            // Send failed server error message
            return $this->authRepository->transactionResponse(2, ['show_message' => true], __tr('Something went wrong on server, please contact to administrator.'));
        });

        return $this->engineResponse($transactionResponse);
    }
     /**
     * prepare notify welcome email
     *
     * @return bool
     *----------------------------------------------------------------*/
    public function userWelcomeNotifyMail($newUser)
    {
        //prepare emailData
        $emailData = [
            'fullName' => $newUser->first_name,
            'email' => $newUser->email,
            'welcomeEmailContent'=>getAppSettings('welcome_email_content'),
        ];
        $welcomeEmailSubject = "Welcome to " . getAppSettings('name');
        //notify to user
        if ($this->baseMailer->notifyToUser($welcomeEmailSubject,'user.account.welcome', $emailData, $newUser->email)) {
            return true;
        }
    }

    public function processUpdatePassword($request)
    {
        if ($this->authRepository->updateIt(auth()->user(), ['password' => Hash::make($request->get('password'))])) {
            return $this->engineResponse(21, [
                'show_message' => true,
                'messageType' => 'success',
                'reloadPage' => true,
            ], __tr('Password updated successfully'));
        }

        return $this->engineFailedResponse([
            'show_message' => true,
        ], __tr('Failed to update password.'));
    }

    /**
     * Activation Required For New Vendor Registration
     *
     * @param  array  $inputData
     * @return string
     */
    public function activationRequiredForRegistration($inputData)
    {
        $transactionResponse = $this->authRepository->processTransaction(function () use ($inputData) {
            // add to title
            $vendor = $this->vendorRepository->storeVendor([
                'title' => $inputData['vendor_title'],
                'slug' => $inputData['username'],
                'status' => 1, // Active
                'type' => 1, // types of vendor restaurant etc
            ]);
            if (! $vendor) {
                return $this->authRepository->transactionResponse(2, ['show_message' => true], __tr('Failed to register user'));
            }
            $inputData['status'] = 4; // Never Active
            $inputData['user_roles__id'] = 2; //vendor admin
            $inputData['vendors__id'] = $vendor->_id;
            // Store user
            $newUser = $this->authRepository->storeUser($inputData);
            // Check if user not stored successfully
            $emailData = [
                'fullName' => $newUser->first_name,
                'email' => $newUser->email,
                'expirationTime' => configItem('account.expiry'),
                'activation_url' => URL::temporarySignedRoute('user.account.activation', Carbon::now()->addHours(configItem('account.expiry')), ['userUid' => $newUser->_uid]),
            ];
            if ($this->baseMailer->notifyToUser('Your account registered successfully.', 'user.account.activation', $emailData, $newUser->email)) {
                return $this->authRepository->transactionResponse(1, [
                    'show_message' => true,
                    'activation_required' => true,
                ], __tr('Your account has been created successfully, to activate your account please check your email.'));
            }

            return $this->authRepository->transactionResponse(2, ['show_message' => true], __tr('Something went wrong on server, please contact to administrator.'));
        });

        return $this->engineResponse($transactionResponse);
    }

    /**
     * Process Account Activation
     *
     * @param  mix  $userUid
     * @return string
     */
    public function processAccountActivation($userUid)
    {
        $neverActivatedUser = $this->authRepository->fetchNeverActivatedUser($userUid);
        // Check if never activated user exist or not
        if (__isEmpty($neverActivatedUser)) {
            return $this->engineResponse(18, null, __tr('Account Activation fail.'));
        }

        $updateData = [
            'status' => 1, // Active
            'email_verified_at' => now(),
        ];
        // Check if user activated successfully
        if ($this->authRepository->updateUser($neverActivatedUser, $updateData)) {
            //check if welcome email setting active
               if (getAppSettings('send_welcome_email')) {
                    $this->userWelcomeNotifyMail($neverActivatedUser);
                }
            return $this->engineSuccessResponse([], __tr('Your account has been activated successfully.'));
        }

        return $this->engineFailedResponse([], __tr('Account Activation fail.'));
    }

    /**
     * User Sign Process with social accounts.
     *
     * @param array $inputData
     *
     *-----------------------------------------------------------------------*/
    public function processCreateSocialCallBack($provider)
    {
        try {
            $socialLogin = Socialite::driver($provider)->user();
        } catch (\Exception $e) {
            return $this->engineResponse(18, null, $e->getMessage());
        }
        // check the record is empty
        if (__isEmpty($socialLogin)) {
            return $this->engineFailedResponse([], __tr('Authentication failed.'));
        }
        $userEmail = trim($socialLogin->getEmail());

        if (empty($userEmail)) {
            return $this->engineFailedResponse([], __tr('Email is required'));
        }
        // Already register
        $findUserFacebook = $this->authRepository->fetchIt([
            'email' =>  $userEmail
        ]);
        if (__isEmpty($findUserFacebook)) {
            // check if new registrations are closed
            if(!getAppSettings('enable_vendor_registration')) {
                return $this->engineFailedResponse([], __tr("Registrations are closed now."));
            }
            // prepare vendor information for registration
            $fullName = explode(" ", $socialLogin->getName());
            $socialUser = [
                'first_name'        => $fullName[0] ?? '',
                'last_name'        => $fullName[1] ?? '',
                'email'      =>     $userEmail,
                'registered_via'   => $provider,
                'username'   => uniqid(($fullName[0] ?? '') . '_')
            ];
            $keyValues = [
                'username' => $socialUser['username'],
                'vendor_title' => $socialUser['first_name'],
                'email' => $socialUser['email'],
                'registered_via' => $socialUser['registered_via'],
                'password' => ('NO_PASSWORD'),
                'status' => 1,
                'first_name' =>  $socialUser['first_name'],
                'last_name' => $socialUser['last_name'],
                'user_roles__id' => 2, // vendor admin
            ];
            // User Registration with vendor
           $userRegistration = $this->processRegistration($keyValues);
            if ($userRegistration->success()) {
                return $this->processLoginForUser($userRegistration->data());
            }
        } else {
            // redirect to processLoginForUser function
            return $this->processLoginForUser($findUserFacebook);
        }
    }

    /**
     * Process user login as required
     *
     * @param array $userData
     * @return EngineReaction
     */
    function processLoginForUser($userData)
    {
        if (!isset($userData['_id']) or empty($userData['_id'])) {
            return $this->engineFailedResponse([], __tr('User not exists.'));
        }

        //fetch user
        $user = $this->authRepository->fetch($userData['_id']);

        //check user is empty
        if (__isEmpty($user)) {
            return $this->engineFailedResponse([], __tr('User not exists.'));
        }

        // Get logged in if credentials valid
        if (Auth::loginUsingId($user->_id)) {
            //success response
            return $this->engineSuccessResponse([
                'show_message' => true
            ], __tr('Welcome, you are logged in successfully.'));
        }

        //error response
        return $this->engineFailedResponse([], __tr("Invalid request."));
    }
}
