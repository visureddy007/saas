<?php
/**
* LabelRepository.php - Repository file
*
* This file is part of the Contact component.
*-----------------------------------------------------------------------------*/

namespace App\Yantrana\Components\Contact\Repositories;

use App\Yantrana\Base\BaseRepository;
use App\Yantrana\Components\Contact\Models\LabelModel;
use App\Yantrana\Components\Contact\Interfaces\LabelRepositoryInterface;

class LabelRepository extends BaseRepository
                          implements LabelRepositoryInterface
{
    /**
     * primary model instance
     *
     * @var  object
     */
        protected $primaryModel = LabelModel::class;
     }