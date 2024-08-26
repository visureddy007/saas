<?php
/**
* ContactLabelRepository.php - Repository file
*
* This file is part of the Contact component.
*-----------------------------------------------------------------------------*/

namespace App\Yantrana\Components\Contact\Repositories;

use App\Yantrana\Base\BaseRepository;
use App\Yantrana\Components\Contact\Models\ContactLabelModel;
use App\Yantrana\Components\Contact\Interfaces\ContactLabelRepositoryInterface;

class ContactLabelRepository extends BaseRepository implements ContactLabelRepositoryInterface
{
    /**
     * primary model instance
     *
     * @var  object
     */
    protected $primaryModel = ContactLabelModel::class;

    /**
     * Delete Selected Assigned labels from contact
     *
     * @param array $labelIds
     * @param int $contactId
     * @return mixed
     */
    public function deleteAssignedLabels($labelIds, $contactId)
    {
        return $this->primaryModel::whereIn('labels__id', $labelIds)->where([
            'contacts__id' => $contactId
        ])->deleteIt();
    }
}
