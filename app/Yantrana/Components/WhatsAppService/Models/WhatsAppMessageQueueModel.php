<?php
/**
* WhatsAppMessageQueue.php - Model file
*
* This file is part of the WhatsAppService component.
*-----------------------------------------------------------------------------*/

namespace App\Yantrana\Components\WhatsAppService\Models;

use Illuminate\Support\Arr;
use App\Yantrana\Base\BaseModel;
use Illuminate\Database\Eloquent\Casts\Attribute;

class WhatsAppMessageQueueModel extends BaseModel
{
    /**
     * @var string - The database table used by the model.
     */
    protected $table = 'whatsapp_message_queue';

    /**
     * Let the system knows Text columns treated as JSON
     *
     * @var array
     *----------------------------------------------------------------------- */
    protected $jsonColumns = [
        '__data' => [
            'process_response' => 'array:extend',
            'contact_data' => 'array:extend',
            'campaign_data' => 'array:extend',
        ],
    ];

    /**
     * @var array - The attributes that should be casted to native types.
     */
    protected $casts = [
        '__data' => 'array',
        'scheduled_at' => 'datetime'
    ];

    /**
     * @var array - The attributes that are mass assignable.
     */
    protected $fillable = [
    ];

    protected $appends = [
        'whatsapp_message_error',
        'formatted_updated_time',
    ];

    /**
     * error message if any
     */
    protected function whatsappMessageError(): Attribute
    {
        return Attribute::make(
            get: function (mixed $value, array $attributes) {
                return Arr::get(json_decode($attributes['__data'], true), 'process_response.error_message');
            }
        );
    }

        /**
     * formatted updated at
     */
    protected function formattedUpdatedTime(): Attribute
    {
        return Attribute::make(
            get: fn (mixed $value, array $attributes) => formatDateTime($attributes['updated_at'], null, $attributes['vendors__id']),
        );
    }
}
