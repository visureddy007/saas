<?php

/**
 * VendorController.php - Controller file
 *
 * This file is part of the Vendor component.
 *-----------------------------------------------------------------------------*/

namespace App\Yantrana\Components\Vendor\Controllers;

use Illuminate\Validation\Rule;
use App\Yantrana\Base\BaseController;
use App\Yantrana\Support\CommonRequest;
use App\Yantrana\Support\CommonPostRequest;
use App\Yantrana\Components\Auth\AuthEngine;
use App\Yantrana\Components\Vendor\VendorEngine;
use App\Yantrana\Components\Auth\Models\AuthModel;
use App\Yantrana\Components\Dashboard\DashboardEngine;

class VendorController extends BaseController
{
    /**
     * @var VendorEngine - Vendor Engine
     */
    protected $vendorEngine;

    /**
     * @var AuthEngine - Auth Engine
     */
    protected $authEngine;

    /**
     * @var DashboardEngine - Dashboard Engine
     */
    protected $dashboardEngine;

    /**
     * Constructor
     *
     * @param  VendorEngine  $vendorEngine  - Vendor Engine
     * @return void
     *-----------------------------------------------------------------------*/
    public function __construct(VendorEngine $vendorEngine, AuthEngine $authEngine, DashboardEngine $dashboardEngine)
    {
        $this->vendorEngine = $vendorEngine;
        $this->authEngine = $authEngine;
        $this->dashboardEngine = $dashboardEngine;
    }

    /**
     * Manage User List.
     *
     * @return json object
     *---------------------------------------------------------------- */
    public function vendorDataTableList()
    {
        return $this->vendorEngine->prepareVendorDataTableList();
    }

    public function addVendor(CommonRequest $request)
    {
        if(str_starts_with($request->get('mobile_number'), '0') or str_starts_with($request->get('mobile_number'), '+')) {
            return $this->processResponse(2,[
                2 => __tr('mobile number should be numeric value without prefixing 0 or +.')
            ], [], true);
        }
        // Combine country code and mobile number
        $mobileNumber = $request->mobile_number;
        $request->validate([
            'vendor_title' => 'required|string|min:2|max:100',
            'username' => 'required|string|alpha_dash|min:2|max:45|unique:users,username',
            'first_name' => 'required|string|min:1|max:45',
            'last_name' => 'required|string|min:1|max:45',
            'mobile_number' => [
                'required',
                'min:9',
                'max:15',
                function ($attribute, $value, $fail) use ($mobileNumber) {
                    $exists = AuthModel::
                    where('mobile_number', $mobileNumber)
                    ->exists();
                if ($exists) {
                    $fail('The mobile number has already been taken with the given country code.');
                }
                }
            ],
            'email' => 'required|string|email|max:255|unique:users,email' . (getAppSettings('disallow_disposable_emails') ? '|indisposable' : ''),
            'password' => 'required|string|confirmed|min:8',
            'password_confirmation' => 'required',
        ]);
      
        $processReaction = $this->authEngine->processRegistration($request->all());

        return $this->processResponse($processReaction, [], [], true);
    }


    public function pwaManifest()
    {
        return response($this->loadView(
            'vendors.pwa-manifest'
        ))->header('Content-Type', 'application/manifest+json');
    }

    public function pwaServiceWorker()
    {
        return response($this->loadView(
            'vendors.pwa-service-worker-js'
        ))->header('Content-Type', 'text/javascript');
    }

    public function infoPage($vendorSlug, $pageSlug)
    {
        return $this->loadView(
            'vendors.info-page-view',
            $this->vendorEngine->pageInfo($pageSlug)
        );
    }

    /**
     * Prepare Vendor's Delete
     *
     * @param  mix  $vendorIdOrUid
     * @return json object
     */
    public function prepareVendorDelete($vendorIdOrUid)
    {
        // ask engine to process the request
        $processReaction = $this->vendorEngine->prepareVendorDelete($vendorIdOrUid);

        // get back to controller with engine response
        return $this->processResponse($processReaction, [], [], true);
    }
    /**
     * Prepare Vendor's Permanant Delete
     *
     * @param  mix  $vendorIdOrUid
     * @return json object
     */
    public function prepareVendorPermanentDelete($vendorIdOrUid)
    {
        // ask engine to process the request
        $processReaction = $this->vendorEngine->prepareVendorPermanentDelete($vendorIdOrUid);

        // get back to controller with engine response
        return $this->processResponse($processReaction, [], [], true);
    }

    /**
     * Prepare Update Vendor Data
     *
     * @param  mix  $vendorIdOrUid
     * @return json object
     */
    public function prepareUpdateVendorData($vendorIdOrUid)
    {
        // ask engine to process the request
        $processReaction = $this->vendorEngine->prepareVendorUpdateData($vendorIdOrUid);

        // get back to controller with engine response
        return $this->processResponse($processReaction, [], [], true);
    }

    /**
     * Update Vendor's Data
     *
     *
     * @return json object
     */
    public function updateVendorData(CommonRequest $request)
    {
        if(str_starts_with($request->get('mobile_number'), '0') or str_starts_with($request->get('mobile_number'), '+')) {
            return $this->processResponse(2,[
                2 => __tr('mobile number should be numeric value without prefixing 0 or +.')
            ], [], true);
        }
        // Combine country code and mobile number
        $mobileNumber = $request->mobile_number;
        // process the validation based on the provided rules
        // Get the current user Uid
        $userUid = $request->userIdOrUid;
        $request->validate([
            'vendorIdOrUid' => 'required',
            'userIdOrUid' => '',
            'title' => 'required|string|min:2|max:100',
            'username' => [
                'required',
                'string',
                'alpha_dash',
                'min:2',
                'max:45',
                Rule::unique((new AuthModel())->getTable())->ignore($request->userIdOrUid, '_uid')
            ],
            'first_name' => 'required|string|min:1|max:45',
            'last_name' => 'required|string|min:1|max:45',
            'mobile_number' => [
                'required',
                'min:9',
                'max:15',
                function ($attribute, $value, $fail) use ($mobileNumber,$userUid) {
                    $exists = AuthModel::
                    where('mobile_number', $mobileNumber)
                    ->where('_uid','!=',$userUid)
                    ->exists();
                if ($exists) {
                    $fail('The mobile number has already been taken with the given country code.');
                }
                }
            ],
            'email' => [
                'required',
                'email' ,
                (getAppSettings('disallow_disposable_emails') ? 'indisposable' : ''),
                Rule::unique((new AuthModel())->getTable())->ignore($request->userIdOrUid, '_uid')
            ],
            'status' => '',
        ]);
       
        // ask engine to process the request
        $processReaction = $this->vendorEngine->processVendorUpdate($request->all());

        // get back with response
        return $this->processResponse($processReaction, [], [], true);
    }

    /**
     * Change Password Vendor Data
     *
     * @param  mix  $vendorIdOrUid
     * @return array
     */
    public function changePasswordVendorData($vendorIdOrUid)
    {
        // ask engine to process the request
        $processReaction = $this->vendorEngine->prepareVendorPasswordData($vendorIdOrUid);

        // get back to controller with engine response
        return $this->processResponse($processReaction, [], [], true);
    }

    /**
     * Change Password Vendor
     *
     *
     * @return json object
     */
    public function changePasswordVendor(CommonPostRequest $request)
    {
        $request->validate([
            // 'current_password' =>'required','different:old_password'
            'password' => ['required', 'min:6', 'confirmed'],
            'password_confirmation' => ['required', 'min:6', 'same:password'],
        ]);
        $processReaction = $this->vendorEngine->processChangePasswordBySuperAdmin($request->all());

        //check reaction code equal to 1
        return $this->processResponse($processReaction, [], [], true);
    }

    /**
     * Vendor Dashboard
     *
     * @param int|string $vendorIdOrUid
     * @return view
     */
    public function vendorDashboard($vendorIdOrUid)
    {
        $vendorInfo = $this->vendorEngine->getBasicSettings($vendorIdOrUid);

        return $this->loadView('vendors.vendor-dashboard', array_merge($this->dashboardEngine->prepareVendorDashboardData($vendorIdOrUid), [
            'vendorViewBySuperAdmin' => true,
            'vendorIdOrUid' => $vendorIdOrUid,
            'vendorInfo' => $vendorInfo,
            'vendorSlug' => $vendorInfo['slug'],
        ]));
    }
    /**
     * Vendor Details
     *
     * @param int|string $vendorIdOrUid
     * @return view
     */
    public function vendorDetails($vendorIdOrUid)
    {
        $vendorInfo = $this->vendorEngine->getBasicSettings($vendorIdOrUid);

        return $this->loadView('vendors.vendor-subscription', array_merge($this->dashboardEngine->prepareVendorDashboardData($vendorIdOrUid), [
            'vendorViewBySuperAdmin' => true,
            'vendorIdOrUid' => $vendorIdOrUid,
            'vendorInfo' => $vendorInfo,
            'vendorSlug' => $vendorInfo['slug'],
        ]));
    }

    /**
      * User login as
      *
      * @param  object CommonRequest $request
      *
      * @return  json object
      *---------------------------------------------------------------- */

    public function loginAsVendorAdmin(CommonRequest $request, $vendorUid)
    {
        // ask engine to process the request
        $processReaction = $this->vendorEngine->processLoginAsVendorAdmin($vendorUid);
        if($processReaction->failed()) {
            return $this->processResponse($processReaction, [], [], true);
        }
        // get back with response
        return $this->responseAction(
            $this->processResponse($processReaction, [], [], true),
            $this->redirectTo(hasVendorAccess()
              ? 'vendor.console'
              : 'home')
        );
    }

    /**
    * User logout as
    *
    * @param  object CommonRequest $request
    *
    * @return  json object
    *---------------------------------------------------------------- */

    public function logoutAsVendorAdmin(CommonRequest $request)
    {
        //   validateVendorAccess('administrative');
        // ask engine to process the request
        $processReaction = $this->vendorEngine->processVendorAdminLogoutAs();
        // get back with response
        return $this->responseAction(
            $this->processResponse($processReaction, [], [], true),
            $this->redirectTo(hasVendorAccess()
              ? 'central.vendors'
              : 'home')
        );
    }
}
