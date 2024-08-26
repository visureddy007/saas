<?php
/**
* VendorUser.php - Model file
*
* This file is part of the Vendor component.
*-----------------------------------------------------------------------------*/

namespace App\Yantrana\Components\Vendor\Models;

use App\Yantrana\Base\BaseModel;

class VendorUserModel extends BaseModel
{
    /**
     * @var  string $table - The database table used by the model.
     */
    protected $table = "vendor_users";

/**
     * @var array - The attributes that should be casted to native types.
     */
    protected $casts = [
        '__data' => 'array'
    ];

    /**
     * Let the system knows Text columns treated as JSON
     *
     * @var array
     *----------------------------------------------------------------------- */
    protected $jsonColumns = [
        '__data' => [
            'permissions' => 'array:extend',
        ],
    ];

    /**
     * @var  array $fillable - The attributes that are mass assignable.
     */
    protected $fillable = [
    ];
}