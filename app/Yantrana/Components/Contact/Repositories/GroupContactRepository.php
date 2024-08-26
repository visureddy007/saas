<?php
/**
* GroupContactRepository.php - Repository file
*
* This file is part of the Contact component.
*-----------------------------------------------------------------------------*/

namespace App\Yantrana\Components\Contact\Repositories;

use App\Yantrana\Base\BaseRepository;
use App\Yantrana\Components\Contact\Interfaces\GroupContactRepositoryInterface;
use App\Yantrana\Components\Contact\Models\GroupContactModel;

class GroupContactRepository extends BaseRepository implements GroupContactRepositoryInterface
{
    /**
     * primary model instance
     *
     * @var object
     */
    protected $primaryModel = GroupContactModel::class;

    /**
     * Delete Selected Assigned groups from contacts
     *
     * @param array $groupIds
     * @param int $contactId
     * @return mixed
     */
    function deleteAssignedGroups($groupIds, $contactId) {
        return $this->primaryModel::whereIn('contact_groups__id', $groupIds)->where([
            'contacts__id' => $contactId
        ])->deleteIt();
    }
    /**
     * Remove Selected contact  from assign group
     *
     * @param array $groupIds
     * @param int $contactId
     * @return mixed
     */
    function removeFromAssignedGroup($contactId,$groupId) {
        return $this->primaryModel::where('contact_groups__id', $groupId)->where([
            'contacts__id' => $contactId
        ])->deleteIt();
    }
}
