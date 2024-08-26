<?php
/**
* Campaign.php - Model file
*
* This file is part of the Campaign component.
*-----------------------------------------------------------------------------*/

namespace App\Yantrana\Components\Campaign\Models;

use App\Yantrana\Base\BaseModel;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Yantrana\Components\WhatsAppService\Models\WhatsAppMessageLogModel;
use App\Yantrana\Components\WhatsAppService\Models\WhatsAppMessageQueueModel;

class CampaignModel extends BaseModel
{
    /**
     * @var string - The database table used by the model.
     */
    protected $table = 'campaigns';

    /**
     * @var array - The attributes that should be casted to native types.
     */
    protected $casts = [
        'scheduled_at' => 'datetime',
        '__data' => 'array',
    ];

    /**
     * @var array - The attributes that are mass assignable.
     */
    protected $fillable = [
    ];

    /**
     * Let the system knows Text columns treated as JSON
     *
     * @var array
     *----------------------------------------------------------------------- */
    protected $jsonColumns = [
        '__data' => [
            'total_contacts' => 'integer',
            'is_all_contacts' => 'boolean',
            'is_for_template_language_only' => 'boolean',
            'selected_groups' => 'array:extend',
        ],
    ];

    /**
     * Get log messages
     */
    public function messageLog(): HasMany
    {
        return $this->hasMany(WhatsAppMessageLogModel::class, 'campaigns__id', '_id');
    }

    /**
     * Get log messages
     */
    public function queueMessages(): HasMany
    {
        return $this->hasMany(WhatsAppMessageQueueModel::class, 'campaigns__id', '_id');
    }
    /**
     * Get queued pending messages
     */
    public function queuePendingMessages(): HasMany
    {
        return $this->hasMany(WhatsAppMessageQueueModel::class, 'campaigns__id', '_id')->where([
            'status' => 1 // in queue
        ]);
    }
    /**
     * Get queued processing messages
     */
    public function queueProcessingMessages(): HasMany
    {
        return $this->hasMany(WhatsAppMessageQueueModel::class, 'campaigns__id', '_id')->where([
            'status' => 3 // processing
        ]);
    }
}
