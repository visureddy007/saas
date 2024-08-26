<?php
/**
* BotReplyRepository.php - Repository file
*
* This file is part of the BotReply component.
*-----------------------------------------------------------------------------*/

namespace App\Yantrana\Components\BotReply\Repositories;

use App\Yantrana\Base\BaseRepository;
use Illuminate\Database\Eloquent\Builder;
use App\Yantrana\Components\BotReply\Models\BotReplyModel;
use App\Yantrana\Components\BotReply\Interfaces\BotReplyRepositoryInterface;

class BotReplyRepository extends BaseRepository implements BotReplyRepositoryInterface
{
    /**
     * primary model instance
     *
     * @var  object
     */
    protected $primaryModel = BotReplyModel::class;


    /**
      * Fetch botReply datatable source
      *
      * @return  mixed
      *---------------------------------------------------------------- */
    public function fetchBotReplyDataTableSource()
    {
        // basic configurations for dataTables data
        $dataTableConfig = [
            // searchable columns
            'searchable' => [
                'name',
                'reply_text',
                'trigger_type',
                'reply_trigger'
            ]
        ];
        // get Model result for dataTables
        return BotReplyModel::where([
            'vendors__id' => getVendorId()
        ])->whereNull('bot_flows__id')->dataTables($dataTableConfig)->toArray();
    }

    /**
      * Fetch botReply count
      *
      * @return  number
      *---------------------------------------------------------------- */
      public function fetchBotReplyCount($vendorId = null)
      {
          // get Model result for dataTables
          return BotReplyModel::where([
              'vendors__id' => $vendorId ?: getVendorId()
          ])->whereNull('bot_flows__id')->count();
      }

    /**
      * Delete $botReply record and return response
      *
      * @param  object $inputData
      *
      * @return  mixed
      *---------------------------------------------------------------- */

    public function deleteBotReply($botReply)
    {
        // Check if $botReply deleted
        if ($botReply->deleteIt()) {
            // if deleted
            return true;
        }
        // if failed to delete
        return false;
    }

    /**
      * Store new botReply record and return response
      *
      * @param  array $inputData
      *
      * @return  mixed
      *---------------------------------------------------------------- */

    public function storeBotReply($inputData)
    {
        // prepare data to store
        $keyValues = [
            'name',
            'reply_text',
            'trigger_type',
            'reply_trigger',
            'vendors__id',
            'bot_flows__id',
            'status',
            '__data',
        ];
        return $this->storeIt($inputData, $keyValues);
    }

    /**
     * Update List message & buttons as our json array:extend keeps existing records even if we want to delete it
     *
     * @param int $botReplyId
     * @param array $updateData
     * @return bool|int
     */
    public function updateForListAndButtonMessage($botReplyId, $updateData)
    {
        return $this->primaryModel::where('_id', $botReplyId)->update($updateData);
    }

    /**
     * Reset bot triggers to null
     *
     * @param array $botUids - array of _uids
     * @return mixed
     */
    function resetBotTriggers($botUids = []) {
        return $this->primaryModel::whereIn('_uid', $botUids)->update([
            'reply_trigger' => null
        ]);
    }

    /**
     * Get Related Or Welcome Bots for vendor
     *
     * @param int $vendorId
     * @return Eloquent
     */
    function getRelatedOrWelcomeBots($whereConditions = []) {
        return $this->primaryModel::select([
            '_id',
            'reply_trigger',
            'reply_text',
            'trigger_type',
            'priority_index',
            '__data',
            'bot_flows__id',
            'status',
        ])->where($whereConditions)->get();
    }
}
