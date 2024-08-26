<?php
/**
* ContactGroup.php - Model file
*
* This file is part of the Contact component.
*-----------------------------------------------------------------------------*/

namespace App\Yantrana\Components\Contact\Models;

use App\Yantrana\Base\BaseModel;

class ContactGroupModel extends BaseModel
{
    /**
     * @var string - The database table used by the model.
     */
    protected $table = 'contact_groups';

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
