<?php

/**
 * ActivityLog.php - Model file
 *
 * This file is part of the User component.
 *-----------------------------------------------------------------------------*/

namespace App\Yantrana\Components\User\Models;

use App\Yantrana\Base\BaseModel;
use Illuminate\Database\Eloquent\Casts\AsArrayObject;

class ActivityLogModel extends BaseModel
{
    /**
     * @var string - The database table used by the model.
     */
    protected $table = 'activity_logs';

    /**
     * @var array - The attributes that should be casted to native types.
     */
    protected $casts = [
        'activity' => AsArrayObject::class,
    ];

    /**
     * @var array - The attributes that are mass assignable.
     */
    protected $fillable = [];

    /**
     * The generate UID or not
     *
     * @var string
     *----------------------------------------------------------------------- */
    protected $isGenerateUID = false;

    /**
     * Let the system knows Text columns treated as JSON
     *
     * @var array
     *----------------------------------------------------------------------- */
    protected $jsonColumns = [
        'activity' => [
            'message' => 'string',
            'data' => 'array:extend',
        ],
    ];

    /**
     * Disable Updated at
     *
     * @param  value  $value
     * @return void
     */
    public function setUpdatedAt($value)
    {
        //Do-nothing
    }
}
