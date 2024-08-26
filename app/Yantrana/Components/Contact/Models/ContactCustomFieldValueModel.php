<?php
/**
* ContactCustomFieldValue.php - Model file
*
* This file is part of the Contact component.
*-----------------------------------------------------------------------------*/

namespace App\Yantrana\Components\Contact\Models;

use App\Yantrana\Base\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContactCustomFieldValueModel extends BaseModel
{
    /**
     * @var  string $table - The database table used by the model.
     */
    protected $table = "contact_custom_field_values";

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

    /**
     * Get Custom field related to Field Value
     *
     * @return BelongTo
     */
    function customField():BelongsTo  {
        return $this->belongsTo(ContactCustomFieldModel::class, 'contact_custom_fields__id', '_id');
    }
}