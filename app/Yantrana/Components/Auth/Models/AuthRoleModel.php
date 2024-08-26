<?php
/**
* AuthRole.php - Model file
*
* This file is part of the Auth component.
*-----------------------------------------------------------------------------*/

namespace App\Yantrana\Components\Auth\Models;

use App\Yantrana\Base\BaseModel;

class AuthRoleModel extends BaseModel
{
    /**
     * @var string - The database table used by the model.
     */
    protected $table = 'user_roles';

    /**
     * @var array - The attributes that should be casted to native types.
     */
    protected $casts = [];

    /**
     * @var array - The attributes that are mass assignable.
     */
    protected $fillable = [];
}
