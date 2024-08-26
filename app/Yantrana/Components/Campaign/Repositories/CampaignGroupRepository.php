<?php
/**
* CampaignGroupRepository.php - Repository file
*
* This file is part of the Campaign component.
*-----------------------------------------------------------------------------*/

namespace App\Yantrana\Components\Campaign\Repositories;

use App\Yantrana\Base\BaseRepository;
use App\Yantrana\Components\Campaign\Interfaces\CampaignGroupRepositoryInterface;
use App\Yantrana\Components\Campaign\Models\CampaignGroupModel;

class CampaignGroupRepository extends BaseRepository implements CampaignGroupRepositoryInterface
{
    /**
     * primary model instance
     *
     * @var object
     */
    protected $primaryModel = CampaignGroupModel::class;
}
