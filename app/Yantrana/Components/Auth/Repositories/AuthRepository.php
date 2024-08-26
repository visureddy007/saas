<?php

/**
 * AuthRepository.php - Repository file
 *
 * This file is part of the Auth component.
 *-----------------------------------------------------------------------------*/

namespace App\Yantrana\Components\Auth\Repositories;

use App\Yantrana\Base\BaseRepository;
use App\Yantrana\Components\Auth\Interfaces\AuthRepositoryInterface;
use App\Yantrana\Components\Auth\Models\AuthModel;
use App\Yantrana\Components\Vendor\Models\VendorUserModel;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use YesSecurity;

class AuthRepository extends BaseRepository implements AuthRepositoryInterface
{
        /**
     * primary model instance
     *
     * @var object
     */
    protected $primaryModel = AuthModel::class;
    protected $vendorUsersModel = VendorUserModel::class;

    /**
     * Fetch the record of Auth
     *
     * @param    int || string $idOrUid
     * @return eloquent collection object
     *---------------------------------------------------------------- */
    public function fetch($idOrUid)
    {
        if (is_numeric($idOrUid)) {
            return $this->primaryModel::where('_id', $idOrUid)->first();
        }

        return $this->primaryModel::where('_uid', $idOrUid)->first();
    }

    /**
     * Store User.
     *
     * @param  array  $storeData
     *
     *-----------------------------------------------------------------------*/
    public function storeUser($storeData, $storeAsVendorUser = false)
    {
        $keyValues = [
            'email',
            'password' => Hash::make($storeData['password']),
            'status' => $storeData['status'],
            'first_name',
            'last_name',
            'registered_via',
            'username' => Str::lower(Str::slug($storeData['username'])),
            'mobile_number' => array_get($storeData, 'mobile_number'),
            'user_roles__id' => $storeData['user_roles__id'],
            // 'vendors__id',
        ];
        if(!$storeAsVendorUser) {
            $keyValues[] = 'vendors__id';
        }

        $keyValues['remember_token'] = YesSecurity::generateUid();

        // Get Instance of user model
        $userModel = new $this->primaryModel;
        // Store New User
        if ($userModel->assignInputsAndSave($storeData, $keyValues)) {
            if($storeAsVendorUser) {
                $vendorUserModel = new $this->vendorUsersModel;
                $vendorUserModel->assignInputsAndSave([
                    'vendors__id' => $storeData['vendors__id'],
                    'users__id' => $userModel->_id,
                    '__data' => [
                        'permissions' => $storeData['permissions']
                    ]
                ], [
                    'vendors__id',
                    'users__id',
                    '__data',
                ]);
            }
            return $userModel;
        }

        return false;
    }

    public function fetchNeverActivatedUser($userUid)
    {
        return $this->primaryModel::where([
            '_uid' => $userUid,
            'status' => 4,  // never activated
        ])
            ->first();
    }

    public function updateUser($user, $updateData, $vendorUserData = null)
    {
        $isUpdated = false;
        if(isset($updateData['password']) and $updateData['password']) {
            $updateData['password'] = Hash::make($updateData['password']);
        }
        // Check if information updated
        if ($user->modelUpdate($updateData)) {
            $isUpdated = true;
        }
        // update permissions
        if($vendorUserData) {
            $vendorUserModel = new $this->vendorUsersModel;
            $vendorUserModel = $vendorUserModel->where([
                'vendors__id' => $vendorUserData['vendors__id'],
                'users__id' => $user->_id,
            ])->first();
            if($vendorUserModel->modelUpdate([
                '__data' => [
                    'permissions' => $vendorUserData['permissions']
                ]
            ])) {
                $isUpdated = true;
            }
        }
        if($isUpdated) {
            return $user;
        }

        return false;
    }
}
