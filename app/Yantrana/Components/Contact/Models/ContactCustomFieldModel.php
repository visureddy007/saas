<?php
/**
* ContactCustomField.php - Model file
*
* This file is part of the Contact component.
*-----------------------------------------------------------------------------*/

namespace App\Yantrana\Components\Contact\Models;

use App\Yantrana\Base\BaseModel;
use App\Yantrana\Components\Contact\Models\ContactCustomFieldValueModel;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ContactCustomFieldModel extends BaseModel
{
    /**
     * @var  string $table - The database table used by the model.
     */
    protected $table = "contact_custom_fields";

    /**
     * @var  array $casts - The attributes that should be casted to native types.
     */
    protected $casts = [
    ];

    /**
     * @var  array $fillable - The attributes that are mass assignable.
     */
    protected $fillable = [
    ];

    public function userValue():HasOne
    {
        return $this->hasOne(ContactCustomFieldValueModel::class, 'contact_custom_fields__id');
    }
}
