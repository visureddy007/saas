<?php
/**
* WhatsAppMessageQueueRepository.php - Repository file
*
* This file is part of the WhatsAppService component.
*-----------------------------------------------------------------------------*/

namespace App\Yantrana\Components\WhatsAppService\Repositories;

use App\Yantrana\Base\BaseRepository;
use App\Yantrana\Components\WhatsAppService\Interfaces\WhatsAppMessageQueueRepositoryInterface;
use App\Yantrana\Components\WhatsAppService\Models\WhatsAppMessageQueueModel;

class WhatsAppMessageQueueRepository extends BaseRepository implements WhatsAppMessageQueueRepositoryInterface
{
    /**
     * primary model instance
     *
     * @var object
     */
    protected $primaryModel = WhatsAppMessageQueueModel::class;

    /**
     * Take the items from database for message process
     *
     * @return Eloquent Objects
     */
    public function getQueueItemsForProcess()
    {
        return $this->primaryModel::select([
            '_id',
            'status',
            'scheduled_at',
        ])->where([
            // waiting for processing
            'status' => 1,
            [
                // time has passed on
                'scheduled_at', '<=', now()
            ],
        ])->take(75)->get();
    }
    /**
     * Queued messages count
     * $campaignId - Campaign Id
     *
     * @return int
     */
    public function campaignQueueItemsCount($campaignId)
    {
        return $this->primaryModel::where([
            'status' => 1,
            'campaigns__id' => $campaignId,
        ])->count();
    }
}
