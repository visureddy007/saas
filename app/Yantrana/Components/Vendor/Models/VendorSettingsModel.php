<?php

/**
 * VendorSettings.php - Model file
 *
 * This file is part of the Vendor component.
 *-----------------------------------------------------------------------------*/

namespace App\Yantrana\Components\Vendor\Models;

use App\Yantrana\Base\BaseModel;

class VendorSettingsModel extends BaseModel
{
    /**
     * @var string - The database table used by the model.
     */
    protected $table = 'vendor_settings';

    /**
     * @var array - The attributes that should be casted to native types.
     */
    protected $casts = [];

    /**
     * @var array - The attributes that are mass assignable.
     */
    protected $fillable = [];
}
