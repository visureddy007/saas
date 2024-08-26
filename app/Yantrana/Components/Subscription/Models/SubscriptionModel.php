<?php

/**
 * Subscription.php - Model file
 *
 * This file is part of the Subscription component.
 *-----------------------------------------------------------------------------*/

namespace App\Yantrana\Components\Subscription\Models;

use App\Yantrana\Base\BaseModel;

class SubscriptionModel extends BaseModel
{
    /**
     * @var string - The database table used by the model.
     */
    protected $table = 'subscriptions';

    /**
     * @var array - The attributes that should be casted to native types.
     */
    protected $casts = [];

    /**
     * @var array - The attributes that are mass assignable.
     */
    protected $fillable = [];

    /**
     * @var string
     */
    protected $primaryKey = 'id';
}
