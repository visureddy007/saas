<?php
/**
* ManualSubscription.php - Model file
*
* This file is part of the Subscription component.
*-----------------------------------------------------------------------------*/

namespace App\Yantrana\Components\Subscription\Models;

use App\Yantrana\Base\BaseModel;
use App\Yantrana\Components\Vendor\Models\VendorModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ManualSubscriptionModel extends BaseModel
{
    /**
     * @var  string $table - The database table used by the model.
     */
    protected $table = "manual_subscriptions";

    /**
     * @var  array $casts - The attributes that should be casted to native types.
     */
    protected $casts = [
        'ends_at' => 'datetime',
        '__data' => 'array'
    ];

    /**
     * Let the system knows Text columns treated as JSON
     *
     * @var array
     *----------------------------------------------------------------------- */
    protected $jsonColumns = [
        '__data' => [
            'prepared_plan_details' => 'array',
            'manual_txn_details' => 'array:extend',
        ],
    ];

    /**
     * @var  array $fillable - The attributes that are mass assignable.
     */
    protected $fillable = [
    ];

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(VendorModel::class, 'vendors__id', '_id');
    }
}
