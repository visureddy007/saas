<?php

/**
 * ActivityLogRepository.php - Repository file
 *
 * This file is part of the User component.
 *-----------------------------------------------------------------------------*/

namespace App\Yantrana\Components\User\Repositories;

use App\Yantrana\Base\BaseRepository;
use App\Yantrana\Components\User\Interfaces\ActivityLogRepositoryInterface;
use App\Yantrana\Components\User\Models\ActivityLogModel;

class ActivityLogRepository extends BaseRepository implements ActivityLogRepositoryInterface
{
    /**
     * primary model instance
     *
     * @var object
     */
    protected $primaryModel = ActivityLogModel::class;
}
