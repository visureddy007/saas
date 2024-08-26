<?php

/**
 * VendorEngine.php - Main component file
 *
 * This file is part of the Vendor component.
 *-----------------------------------------------------------------------------*/

namespace App\Yantrana\Components\Vendor;

use Illuminate\Support\Arr;
use App\Yantrana\Base\BaseEngine;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Yantrana\Components\Auth\Repositories\AuthRepository;
use App\Yantrana\Components\User\Repositories\UserRepository;
use App\Yantrana\Components\Vendor\Repositories\VendorRepository;
use App\Yantrana\Components\Vendor\Interfaces\VendorEngineInterface;

class VendorEngine extends BaseEngine implements VendorEngineInterface
{
    /**
     * @var VendorRepository - Vendor Repository
     */
    protected $vendorRepository;

    /**
     * @var AuthRepository - Auth Repository
     */
    protected $authRepository;

    /**
     * @var UserRepository - User Repository
     */
    protected $userRepository;

    /**
     * Constructor
     *
     * @param  VendorRepository  $vendorRepository  - Vendor Repository
     * @return void
     *-----------------------------------------------------------------------*/
    public function __construct(AuthRepository $authRepository, VendorRepository $vendorRepository, UserRepository $userRepository)
    {
        $this->authRepository = $authRepository;
        $this->vendorRepository = $vendorRepository;
        $this->userRepository = $userRepository;
    }

    /**
     * Prepare User Data table list.
     *
     * @param  int  $status
     *
     *---------------------------------------------------------------- */
    public function prepareVendorDataTableList()
    {
        $userCollection = $this->vendorRepository->fetchVendorsDataTableSource();
       
        $isDemoMode = isDemo();
        $orderStatuses = configItem('status_codes');
        $requireColumns = [
            '_id',
            '_uid',
            'title',
            'created_at' => function ($key) {
                return formatDate($key['created_at']);
            },
            'fullName' => function ($key) use (&$isDemoMode) {
                return $isDemoMode ? maskForDemo($key['fullName'], 'fullName') : $key['fullName'];
            },
            'status' => function ($key) use ($orderStatuses) {
                return Arr::get($orderStatuses, $key['status']);
            },
            'status_code' => function ($key) use ($orderStatuses) {
                return $key['status'];
            },
            'user_status' => function ($key) use ($orderStatuses) {
                return Arr::get($orderStatuses, $key['user_status']);
            },
            'email' => function ($key) use (&$isDemoMode) {
                return $isDemoMode ? maskForDemo($key['email'], 'email') : $key['email'];
            },
            'mobile_number',
            'username' => function ($key) use (&$isDemoMode) {
                return $isDemoMode ? maskForDemo($key['username'], 'username') : $key['username'];
            },
            'userId',
            'slug',
        ];

        return $this->dataTableResponse($userCollection, $requireColumns);
    }

    /**
     * Get Vendor Basic Settings
     *
     * @return array|null
     */
    public function getBasicSettings($vendorUid = null)
    {
        if (! $vendorUid) {
            $vendorUid = getVendorUid();
            abortIf(!$vendorUid);
        }
        $vendorData = $this->vendorRepository->fetchIt($vendorUid);
        if (__isEmpty($vendorData)) {
            return [
                'title' => null,
                'id' => null,
                'uid' => null,
                'slug' => null,
                'logo_image' => null,
                'logo_url' => null,
            ];
        }

        return [
            'title' => $vendorData->title,
            'id' => $vendorData->_id,
            'uid' => $vendorData->_uid,
            'slug' => $vendorData->slug,
            'status' => $vendorData->status,
            'logo_image' => $vendorData->logo_image,
            'logo_url' => getVendorSettings('logo_image_url'),
        ];
    }

    /**
     * get the page info from config
     *
     * @param  string  $pageSlug
     * @return string
     */
    public function pageInfo($pageSlug)
    {
        $pageSlug = str_slug($pageSlug, '_');
        $pageInfo = getVendorSettings($pageSlug);
        $pagesAllowed = [
            'info_terms_and_conditions',
            'info_refund_policy',
        ];
        if ($pageInfo and in_array($pageSlug, $pagesAllowed)) {
            return [
                'pageId' => $pageSlug,
                'pageData' => $pageInfo,
            ];
        }
        abort(404, __tr('Not Found'));
    }

    /**
     * Prepare Vendor Delete
     *
     * @param  mix  $vendorIdOrUid
     * @return string
     */
    public function prepareVendorDelete($vendorIdOrUid)
    {
        $vendor = $this->vendorRepository->fetchIt($vendorIdOrUid);

        // Check if $vendor not exist then throw not found
        // exception
        if (__isEmpty($vendor)) {
            return $this->engineReaction(18, null, __tr('Vendor not found.'));
        }
        // check if already soft deleted
        if ($vendor->status == 5) {
            return $this->engineReaction(18, null, __tr('Vendor already in soft deleted state.'));
        }
        $transactionResponse = $this->authRepository->processTransaction(function () use ($vendor) {
            $keyValue = [
                'status' => 5,
            ];
            // mark user as deleted
            /* if ($user = $this->userRepository->fetchIt(['vendors__id' => $vendor->_id])) {
                if (! $this->userRepository->updateIt($user, $keyValue)) {
                    return $this->authRepository->transactionResponse(2, ['show_message' => true], __tr('Failed to delete vendor user'));
                }
            } */
            // ask to delete the record
            if ($this->vendorRepository->updateIt($vendor, $keyValue)) {
                // if successful
                return $this->authRepository->transactionResponse(1, ['show_message' => true], __tr('Vendor soft deleted successfully'));
            }

            return $this->authRepository->transactionResponse(2, ['show_message' => true], __tr('Failed to delete vendor'));
        });

        return $this->engineResponse($transactionResponse);
    }

    /**
     * Prepare Vendor Update Data
     *
     * @param  mix  $vendorIdOrUid
     * @return array
     */
    public function prepareVendorUpdateData($vendorIdOrUid)
    {
        $vendor = $this->vendorRepository->fetchItVendor($vendorIdOrUid);
        // Check if $vendor not exist then throw not found
        // exception
        if (__isEmpty($vendor)) {
            return $this->engineReaction(18, null, __tr('Vendor not found.'));
        }
        $isDemoMode = isDemo();
        if($isDemoMode) {
            $vendor['username'] = maskForDemo($vendor['username']);
            $vendor['email'] = maskForDemo($vendor['email']);
            $vendor['first_name'] = maskForDemo($vendor['first_name']);
            $vendor['last_name'] = maskForDemo($vendor['last_name']);
        }

        return $this->engineReaction(1, $vendor);
    }

    /**
     * Process Vendor Update
     *
     * @param  array  $inputData
     * @return string
     */
    public function processVendorUpdate($inputData)
    {
        $vendorData = $this->vendorRepository->fetchIt($inputData['vendorIdOrUid']);
        $userData = $this->userRepository->fetchIt($inputData['userIdOrUid']);

        $requireColumnsForVendor = [
            'title' => $inputData['title'],
            'status' => formSwitchValue($inputData['store_status']),
        ];

        $requireColumnsForUser = [
            'first_name' => $inputData['first_name'],
            'last_name' => $inputData['last_name'],
            'username' => $inputData['username'],
            'email' => $inputData['email'],
            'mobile_number' => $inputData['mobile_number'],
            'status' => formSwitchValue($inputData['status']),
        ];
        $updateUserData = $this->userRepository->updateIt($userData, $requireColumnsForUser);
        $updateAccountData = $this->vendorRepository->updateIt($vendorData, $requireColumnsForVendor);
        if ($updateUserData or $updateAccountData) {
            return $this->engineReaction(1, null, __tr('Vendor updated.'));
        }

        return $this->engineReaction(14, null, __tr('Vendor not updated.'));
    }

    /**
     * Prepare Vendor Password Data
     *
     * @param  int  $inputData
     * @return array|string
     */
    public function prepareVendorPasswordData($inputData)
    {
        $user = $this->userRepository->fetchIt($inputData);
        // Check if $user not exist then throw not found
        if (__isEmpty($user)) {
            return $this->engineReaction(18, null, __tr('Vendor not found.'));
        }

        return $this->engineReaction(1, $user->toArray());
    }

    /**
     * Process Change Password By SuperAdmin
     *
     * @param  array  $inputData
     * @return string
     */
    public function processChangePasswordBySuperAdmin($inputData)
    {
        $user = $this->userRepository->fetchIt($inputData['users_id']);

        if (__isEmpty($user)) {
            return $this->engineReaction(18, null, __tr('Author not found.'));
        }
        // This variable give the update password field.
        $updateData = [
            'password' => Hash::make($inputData['password']),
        ];

        if ($this->userRepository->updateIt($user, $updateData)) {
            return $this->engineReaction(1, null, __tr('Password updated.'));
        }

        return $this->engineReaction(14, null, __tr('Password not updated.'));
    }

    /**
      * Process login as for Team Member
      *
      * @param  string $userIdOrUid
      *
      * @return  EngineResponse
      *---------------------------------------------------------------- */

    public function processLoginAsVendorAdmin($vendorUid)
    {
        // demo
        if(isDemo()) {
            return $this->engineFailedResponse([], __tr('Functionality is disabled for demo'));
        }
        //   $vendorId = getVendorId();
        $vendor = $this->vendorRepository->fetchIt($vendorUid);
        if(__isEmpty($vendor)) {
            return $this->engineFailedResponse([], __tr('Invalid vendor'));
        }
        $user = $this->userRepository->with('role')->fetchIt([
          'vendors__id' => $vendor->_id
        ]);
        if(__isEmpty($user)) {
            return $this->engineFailedResponse([], __tr('Invalid user'));
        }
        // check if the user is vendor admin
        if($user?->role->_id !== 2) {
            return $this->engineFailedResponse([], __tr('Invalid user role'));
        }

        if($user->_id == getUserID()) {
            return $this->engineFailedResponse([], __tr('You can not logged in to your own account.'));
        }
        session([
            'loggedBySuperAdmin' => [
                'id' => getUserID(),
                'name' => getUserAuthInfo('profile.full_name'),
            ]
        ]);
        Auth::loginUsingId($user->_id);
        return $this->engineSuccessResponse([
            'show_message' => true,
        ], __tr('Welcome, you are logged as __userName__ successfully.', [
            '__userName__' => $user->full_name
        ]));
    }

    /**
    * Process logout as for Vendor admin
    *
    *
    * @return  EngineResponse
    *---------------------------------------------------------------- */

    public function processVendorAdminLogoutAs()
    {
        Auth::logout();
        Auth::loginUsingId(session('loggedBySuperAdmin.id'));
        session()->forget('loggedBySuperAdmin');
        return $this->engineSuccessResponse([
            'show_message' => true,
        ], __tr('Welcome, back to your account.'));
    }
     /**
     * Prepare Vendor Delete
     *
     * @param  mix  $vendorIdOrUid
     * @return string
     */
    public function prepareVendorPermanentDelete($vendorIdOrUid)
    {
        $vendor = $this->vendorRepository->fetchIt($vendorIdOrUid);
        // Check if $vendor not exist then throw not found
        // exception
        if (__isEmpty($vendor)) {
            return $this->engineReaction(18, null, __tr('Vendor not found.'));
        }
        // disconnect vendor setup
        try {
            ignoreFacebookApiError(true);
            app()->make(\App\Yantrana\Components\WhatsAppService\WhatsAppServiceEngine::class)->processDisconnectAccount($vendor->_id);
            ignoreFacebookApiError(false);
        } catch (\Throwable $th) {
            //throw $th;
        }
        // ask to delete the record
        if ($this->vendorRepository->deleteIt($vendor)) {
            // if successful
            return $this->engineSuccessResponse([], __tr('Vendor deleted successfully'));
        }

        // if failed to delete
        return $this->engineFailedResponse([], __tr('Failed to delete Vendor'));

    }
}
