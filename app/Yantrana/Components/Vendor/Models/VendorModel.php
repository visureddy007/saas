<?php

/**
 * Vendor.php - Model file
 *
 * This file is part of the Vendor component.
 *-----------------------------------------------------------------------------*/

namespace App\Yantrana\Components\Vendor\Models;

use App\Yantrana\Base\BaseModel;
use Laravel\Cashier\Billable;

use function Illuminate\Events\queueable;

class VendorModel extends BaseModel
{
    use Billable;

    /**
     * @var string - The database table used by the model.
     */
    protected $table = 'vendors';

    /**
     * @var array - The attributes that should be casted to native types.
     */
    protected $casts = [
        'trial_ends_at' => 'datetime',
        'ends_at' => 'datetime',
    ];

    /**
     * @var array - The attributes that are mass assignable.
     */
    protected $fillable = [];
}
