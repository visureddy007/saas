<?php
/**
* CampaignGroup.php - Model file
*
* This file is part of the Campaign component.
*-----------------------------------------------------------------------------*/

namespace App\Yantrana\Components\Campaign\Models;

use App\Yantrana\Base\BaseModel;

class CampaignGroupModel extends BaseModel
{
    /**
     * @var string - The database table used by the model.
     */
    protected $table = 'campaign_groups';

    /**
     * @var array - The attributes that should be casted to native types.
     */
    protected $casts = [
    ];

    /**
     * @var array - The attributes that are mass assignable.
     */
    protected $fillable = [
    ];
}
