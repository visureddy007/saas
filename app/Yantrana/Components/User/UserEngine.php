<?php

/**
 * UserEngine.php - Main component file
 *
 * This file is part of the User component.
 *-----------------------------------------------------------------------------*/

namespace App\Yantrana\Components\User;

use Illuminate\Validation\Rule;
use App\Yantrana\Base\BaseEngine;
use App\Yantrana\Components\User\Repositories\UserRepository;
use App\Yantrana\Components\User\Interfaces\UserEngineInterface;
use Illuminate\Support\Facades\Auth;

class UserEngine extends BaseEngine implements UserEngineInterface
{
    /**
     * @var UserRepository - User Repository
     */
    protected $userRepository;

    /**
     * Constructor
     *
     * @param  UserRepository  $userRepository  - User Repository
     * @return void
     *-----------------------------------------------------------------------*/
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Process profile update request
     *
     * @param  array  $requestData
     * @return array|mixed
     */
    public function processUpdateProfile($requestData)
    {
        //fetch active user by email
        if ($this->userRepository->updateLoggedInUserProfile($requestData)) {
            return $this->engineResponse(21, [
                'messageType' => 'success',
                'reloadPage' => true,
            ], __tr('Your Profile has been updated successfully'));
        }

        return $this->engineResponse(14, null, __tr('Nothing to update'));
    }


    /**
      * User datatable source
      *
      * @return  array
      *---------------------------------------------------------------- */
    public function prepareUserDataTableSource()
    {
        $userCollection = $this->userRepository->fetchUserDataTableSource();
      
        // required columns for DataTables
        $requireColumns = [
            '_id',
            '_uid',
            'first_name',
            'last_name',
            'username',
            'email',
            'mobile_number',
            'status'=>function ($data) {
                if($data['status']==0){
                 return 'Inactive';
                }else{
                    return configItem('status_codes', $data['status']);
                }
            },
            'user_roles__id',
            'user_role' => function ($row) {
                return $row['role']['title'];
            },
            'created_at' => function ($row) {
                return formatDate($row['created_at']);
            },
        ];
        // prepare data for the DataTables
        return $this->dataTableResponse($userCollection, $requireColumns);
    }


    /**
      * User delete process
      *
      * @param  mix $userIdOrUid
      *
      * @return  array
      *---------------------------------------------------------------- */

    public function processUserDelete($userIdOrUid)
    {
        // fetch the record
        $user = $this->userRepository->fetchIt($userIdOrUid);
        // check if the record found
        if (__isEmpty($user)) {
            // if not found
            return $this->engineResponse(18, null, __tr('User not found'));
        }
        $vendorId = getVendorId();
        // check if the user belongs to the current vendor
        if(!$this->userRepository->isVendorUser($user->_id, $vendorId)) {
            return $this->engineFailedResponse([], __tr('Invalid user'));
        }
        // ask to delete the record
        if ($this->userRepository->deleteIt($user)) {
            // if successful
            return $this->engineResponse(1, null, __tr('User deleted successfully'));
        }
        // if failed to delete
        return $this->engineResponse(2, null, __tr('Failed to delete User'));
    }

    /**
      * Process logout as for Team Member
      *
      *
      * @return  EngineResponse
      *---------------------------------------------------------------- */

    public function processLogoutAs()
    {
        Auth::logout();
        Auth::loginUsingId(session('loggedByVendor.id'));
        $hasSuperAdminLogin = session('loggedBySuperAdmin');
        session()->forget('loggedByVendor');
        if($hasSuperAdminLogin) {
            session([
                'loggedBySuperAdmin' => $hasSuperAdminLogin
            ]);
        }
        return $this->engineSuccessResponse([
            'show_message' => true,
        ], __tr('Welcome, back to your account.'));
    }
    /**
      * Process login as for Team Member
      *
      * @param  string $userIdOrUid
      *
      * @return  EngineResponse
      *---------------------------------------------------------------- */

    public function processLoginAs($userIdOrUid)
    {
        // demo
        if(isDemo() and isDemoVendorAccount()) {
            return $this->engineFailedResponse([], __tr('Functionality is disabled for demo'));
        }

        $vendorId = getVendorId();
        $user = $this->userRepository->fetchIt($userIdOrUid);
        // check if the user belongs to the current vendor
        if(!$this->userRepository->isVendorUser($user->_id, $vendorId)) {
            return $this->engineFailedResponse([], __tr('Invalid user'));
        }

        if($user->_id == getUserID()) {
            return $this->engineFailedResponse([], __tr('You can not logged in to your own account.'));
        }
        session([
            'loggedByVendor' => [
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
      * User create
      *
      * @param  array $inputData
      *
      * @return  array
      *---------------------------------------------------------------- */

    public function processUserCreate($inputData)
    {
        $vendorId = getVendorId();
        // check the feature limit
        $vendorPlanDetails = vendorPlanDetails('system_users', $this->userRepository->countVendorUsers($vendorId), $vendorId);
        if (!$vendorPlanDetails['is_limit_available']) {
            return $this->engineResponse(22, null, $vendorPlanDetails['message']);
        }

        $inputData['status'] = 1; // Active
        $inputData['user_roles__id'] = 3; //vendor agent
        $inputData['vendors__id'] = $vendorId;
        $inputData['permissions'] = $inputData['permissions'] ?? [];
        $permissions = [];
        // assign permissions
        foreach (getListOfPermissions() as $permissionKey => $permission) {
            if(array_key_exists($permissionKey, $inputData['permissions'])) {
                $permissions[$permissionKey] = 'allow';
            } else {
                $permissions[$permissionKey] = 'deny';
            }
        }
        $inputData['permissions'] = $permissions;
        $transactionResponse = $this->userRepository->processTransaction(function () use ($inputData) {
            // ask to add record
            if ($newUser = $this->userRepository->storeUser($inputData, true)) {
                return $this->userRepository->transactionResponse(1, ['show_message' => true], __tr('User created'));
            }
            return $this->userRepository->transactionResponse(2, ['show_message' => true], __tr('Failed to create user'));
        });
        return $this->engineResponse($transactionResponse);
    }

    /**
      * User prepare update data
      *
      * @param  mix $userIdOrUid
      *
      * @return  array
      *---------------------------------------------------------------- */

    public function prepareUserUpdateData($userIdOrUid)
    {
        $user = $this->userRepository->with('vendorUserDetails')->fetchIt($userIdOrUid);
        // Check if $user not exist then throw not found
        // exception
        if (__isEmpty($user)) {
            return $this->engineResponse(18, null, __tr('User not found.'));
        }

        $vendorId = getVendorId();
        // check if the user belongs to the current vendor
        if(!$this->userRepository->isVendorUser($user->_id, $vendorId)) {
            return $this->engineFailedResponse([], __tr('Invalid user'));
        }

        return $this->engineResponse(1, $user->toArray());
    }

    /**
      * User process update
      *
      * @param  mixed $userIdOrUid
      * @param  array $inputData
      *
      * @return  array
      *---------------------------------------------------------------- */
    public function processUserUpdate($userIdOrUid, $request)
    {
        $user = $this->userRepository->fetchIt($userIdOrUid);
        $request->validate([
            'email' => Rule::unique('users', 'email')->ignore($user, 'email'),
        ]);
        $inputData = $request->all();
        // Check if $user not exist then throw not found
        // exception
        if (__isEmpty($user)) {
            return $this->engineResponse(18, null, __tr('User not found.'));
        }

        $vendorId = getVendorId();
        // check if the user belongs to the current vendor
        if(!$this->userRepository->isVendorUser($user->_id, $vendorId)) {
            return $this->engineFailedResponse([], __tr('Invalid user'));
        }

        $updateData = [
            'first_name' => $inputData['first_name'],
            'last_name' => $inputData['last_name'],
            'mobile_number' => $inputData['mobile_number'],
            'email' => $inputData['email'],
            'status'=>formSwitchValue($inputData['status']),
        ];
        if($inputData['password']) {
            $updateData['password'] = $inputData['password'];
        }

        $inputData['permissions'] = $inputData['permissions'] ?? [];
        $permissions = [];
        // assign permissions
        foreach (getListOfPermissions() as $permissionKey => $permission) {
            if(array_key_exists($permissionKey, $inputData['permissions'])) {
                $permissions[$permissionKey] = 'allow';
            } else {
                $permissions[$permissionKey] = 'deny';
            }
        }
        $inputData['permissions'] = $permissions;
        $inputData['vendors__id'] = getVendorId();
        $transactionResponse = $this->userRepository->processTransaction(function () use ($user, $updateData, $inputData) {
            // ask to add record
            if ($this->userRepository->updateUser($user, $updateData, $inputData)) {
                return $this->userRepository->transactionResponse(1, ['show_message' => true], __tr('User updated'));
            }
            return $this->userRepository->transactionResponse(14, ['show_message' => true], __tr('No updates'));
        });
        return $this->engineResponse($transactionResponse);

        // Check if User updated
        if ($this->userRepository->updateIt($user, $updateData)) {

            return $this->engineResponse(1, null, __tr('User updated.'));
        }

        return $this->engineResponse(14, null, __tr('User not updated.'));
    }
}
