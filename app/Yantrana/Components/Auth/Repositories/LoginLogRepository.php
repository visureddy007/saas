<?php

/**
 * LoginLogRepository.php - Repository file
 *
 * This file is part of the Auth component.
 *-----------------------------------------------------------------------------*/

namespace App\Yantrana\Components\Auth\Repositories;

use App\Yantrana\Base\BaseRepository;
use App\Yantrana\Components\Auth\Interfaces\LoginLogRepositoryInterface;
use App\Yantrana\Components\Auth\Models\LoginLogModel;

class LoginLogRepository extends BaseRepository implements LoginLogRepositoryInterface
{
    /**
     * primary model instance
     *
     * @var object
     */
    protected $primaryModel = LoginLogModel::class;
}
