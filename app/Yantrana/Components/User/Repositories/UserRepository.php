<?php

/**
 * UserRepository.php - Repository file
 *
 * This file is part of the User component.
 *-----------------------------------------------------------------------------*/

namespace App\Yantrana\Components\User\Repositories;

use Illuminate\Support\Facades\Auth;
use App\Yantrana\Base\BaseRepository;
use App\Yantrana\Components\Auth\Models\AuthModel;
use App\Yantrana\Components\Auth\Repositories\AuthRepository;
use App\Yantrana\Components\User\Interfaces\UserRepositoryInterface;
use App\Yantrana\Components\Vendor\Models\VendorUserModel;

class UserRepository extends AuthRepository implements UserRepositoryInterface
{
    /**
     * primary model instance
     *
     * @var object
     */
    protected $primaryModel = AuthModel::class;

    public function updateLoggedInUserProfile($updateData)
    {
        
        $user = Auth::user();
        $dataToUpdate = [
            'first_name' => $updateData['first_name'],
            'last_name' => $updateData['last_name'],
            'mobile_number'=> $updateData['mobile_number'],
        ];
        if ($user->email !== $updateData['email']) {
            $dataToUpdate['email'] = $updateData['email'];
            $dataToUpdate['email_verified_at'] = null;
        }

        return $this->updateIt($user, $dataToUpdate);
    }

    public function updateUserData($userData, $requireColumnsForUser)
    {
        // Check if page updated then return positive response
        if ($userData->modelUpdate($requireColumnsForUser)) {
            return true;
        }

        return false;
    }

    /**
      * Fetch user datatable source
      *
      * @return  mixed
      *---------------------------------------------------------------- */
    public function fetchUserDataTableSource()
    {
        // basic configurations for dataTables data
        $dataTableConfig = [
            // searchable columns
            'searchable' => [
                'first_name',
                'last_name',
                'username',
                'email',
            ]
        ];
        // get Model result for dataTables
        return $this->primaryModel::select([
            'users.*',
            'vendor_users.users__id',
            'vendor_users.vendors__id',
        ])->leftJoin('vendor_users', 'users._id', '=', 'vendor_users.users__id')
                    ->where('vendor_users.vendors__id', getVendorId())
                    ->with('role')->dataTables($dataTableConfig)
                    ->toArray();
    }

    /**
      * Delete $user record and return response
      *
      * @param  object $inputData
      *
      * @return  mixed
      *---------------------------------------------------------------- */

    public function deleteUser($user)
    {
        // Check if $user deleted
        if ($user->deleteIt()) {
            // if deleted
            return true;
        }
        // if failed to delete
        return false;
    }

    /**
     * Get vendor users count
     *
     * @param int $vendorId
     * @return number
     */
    function countVendorUsers($vendorId) {
        return VendorUserModel::where('vendors__id', $vendorId)->count();
    }
    /**
     * Get vendor active users count
     *
     * @param int $vendorId
     * @return number
     */
    function countVendorsActiveUsers($vendorId) {
        return $this->primaryModel::leftJoin('vendor_users', 'users._id', '=', 'vendor_users.users__id')
        ->where('users.status','=', 1)
        ->where('vendor_users.vendors__id', $vendorId)->count();
    }
    /**
     * check if it is a vendor user
     *
     * @param int $userId
     * @return number
     */
    function isVendorUser($userId, $vendorId = null) {
        $vendorId = $vendorId ?: getVendorId();
        return VendorUserModel::where([
            'vendors__id' => $vendorId,
            'users__id' => $userId,
        ])->count();
    }
    /**
     * Get vendor users who have the Messaging Permission
     *
     * @param int $vendorId
     * @return Eloquent Collection
     */
    function getVendorMessagingUsers($vendorId) {
        // only vendor users having messaging permission
        $vendorMessagingUserIds = VendorUserModel::where([
            'vendors__id' => $vendorId,
            '__data->permissions->messaging' => 'allow',
        ])->get()->pluck('users__id')->toArray();
        // get all the users
        $vendorUsers = $this->fetchItAll([
            'vendors__id' => $vendorId
        ]);
        if(!empty($vendorMessagingUserIds)) {
            $vendorUsers = $vendorUsers->merge($this->fetchItAll($vendorMessagingUserIds, null, '_id'));
        }
        return $vendorUsers;
    }
}
