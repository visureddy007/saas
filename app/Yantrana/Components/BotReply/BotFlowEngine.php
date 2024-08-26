<?php
/**
* BotFlowEngine.php - Main component file
*
* This file is part of the BotReply component.
*-----------------------------------------------------------------------------*/

namespace App\Yantrana\Components\BotReply;

use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;
use App\Yantrana\Base\BaseEngine;
use Illuminate\Database\Query\Builder;
use App\Yantrana\Components\BotReply\Repositories\BotFlowRepository;
use App\Yantrana\Components\BotReply\Repositories\BotReplyRepository;
use App\Yantrana\Components\BotReply\Interfaces\BotFlowEngineInterface;

class BotFlowEngine extends BaseEngine implements BotFlowEngineInterface
{
    /**
     * @var  BotFlowRepository $botFlowRepository - BotFlow Repository
     */
    protected $botFlowRepository;

    /**
     * @var  BotReplyRepository $botReplyRepository - BotReply Repository
     */
    protected $botReplyRepository;

    /**
      * Constructor
      *
      * @param  BotFlowRepository $botFlowRepository - BotFlow Repository
      * @param  BotReplyRepository $botReplyRepository - Bot Reply Repository
      *
      * @return  void
      *-----------------------------------------------------------------------*/

    public function __construct(
        BotFlowRepository $botFlowRepository,
        BotReplyRepository $botReplyRepository,
    ) {
        $this->botFlowRepository = $botFlowRepository;
        $this->botReplyRepository = $botReplyRepository;
    }


    /**
      * BotFlow datatable source
      *
      * @return  array
      *---------------------------------------------------------------- */
    public function prepareBotFlowDataTableSource()
    {
        $botFlowCollection = $this->botFlowRepository->fetchBotFlowDataTableSource();
        $orderStatuses = configItem('status_codes');
        // required columns for DataTables
        $requireColumns = [
            '_id',
            '_uid',
            'title',
            'start_trigger',
            'status' => function ($key) use ($orderStatuses) {
                return Arr::get($orderStatuses, $key['status']);
            },
        ];
        // prepare data for the DataTables
        return $this->dataTableResponse($botFlowCollection, $requireColumns);
    }


    /**
      * BotFlow delete process
      *
      * @param  mix $botFlowIdOrUid
      *
      * @return  array
      *---------------------------------------------------------------- */

    public function processBotFlowDelete($botFlowIdOrUid)
    {
        // fetch the record
        $botFlow = $this->botFlowRepository->fetchIt($botFlowIdOrUid);
        // check if the record found
        if (__isEmpty($botFlow)) {
            // if not found
            return $this->engineResponse(18, null, __tr('Bot Flow not found'));
        }
        // ask to delete the record
        if ($this->botFlowRepository->deleteIt($botFlow)) {
            // if successful
            return $this->engineResponse(1, null, __tr('Bot Flow deleted successfully'));
        }
        // if failed to delete
        return $this->engineResponse(2, null, __tr('Failed to delete BotFlow'));
    }

    /**
      * BotFlow create
      *
      * @param  array $inputData
      *
      * @return  array
      *---------------------------------------------------------------- */

    public function processBotFlowCreate($inputData)
    {
        $vendorId = getVendorId();
        // check the feature limit
        $vendorPlanDetails = vendorPlanDetails('bot_flows', $this->botFlowRepository->countIt([
            'vendors__id' => $vendorId,
        ]), $vendorId);
        if (!$vendorPlanDetails['is_limit_available']) {
            return $this->engineResponse(22, null, $vendorPlanDetails['message']);
        }
        // ask to add record
        if ($this->botFlowRepository->storeBotFlow($inputData)) {
            return $this->engineResponse(1, null, __tr('Bot Flow added.'));
        }

        return $this->engineResponse(2, null, __tr('Bot Flow not added.'));
    }

    /**
      * BotFlow prepare update data
      *
      * @param  mix $botFlowIdOrUid
      *
      * @return  EngineResponse
      *---------------------------------------------------------------- */

    public function prepareBotFlowUpdateData($botFlowIdOrUid)
    {
        $botFlow = $this->botFlowRepository->fetchIt($botFlowIdOrUid);

        // Check if $botFlow not exist then throw not found
        // exception
        if (__isEmpty($botFlow)) {
            return $this->engineResponse(18, null, __tr('Bot Flow not found.'));
        }

        return $this->engineResponse(1, $botFlow->toArray());
    }

    /**
      * BotFlow process update
      *
      * @param  mixed $botFlowIdOrUid
      * @param  array $inputData
      *
      * @return  array
      *---------------------------------------------------------------- */

    public function processBotFlowUpdate($botFlowIdOrUid, $request)
    {
        $vendorId = getVendorId();
        $botFlow = $this->botFlowRepository->fetchIt([
            '_uid' => $botFlowIdOrUid,
            'vendors__id' => $vendorId,
        ]);
        // Check if $botFlow not exist then throw not found
        // exception
        if (__isEmpty($botFlow)) {
            return $this->engineResponse(18, null, __tr('Bot Flow not found.'));
        }

        // validate for uniqueness
        $request->validate([
           "title" => [
               Rule::unique('bot_flows')->where(fn (Builder $query) => $query->where('vendors__id', $vendorId))->ignore($botFlow->_id, '_id')
           ],
           'start_trigger' => [
               Rule::unique('bot_flows')->where(fn (Builder $query) => $query->where('vendors__id', $vendorId))->ignore($botFlow->_id, '_id')
           ]
        ]);

        $updateData = [
            'title' => $request->title,
            'start_trigger' => $request->start_trigger,
            'status' => $request->status ? 1 : 2,
        ];

        // Update concerned start bots if start trigger updated
        if($botFlow->start_trigger != $request->start_trigger) {
            $this->botReplyRepository->updateItAll([
                'bot_flows__id' => $botFlow->_id,
                'reply_trigger' => $botFlow->start_trigger,
                'bot_replies__id' => null,
            ], [
                'reply_trigger' => $request->start_trigger,
            ]);
        }

        // Check if BotFlow updated
        if ($this->botFlowRepository->updateIt($botFlow, $updateData)) {
            return $this->engineResponse(1, null, __tr('Bot Flow updated.'));
        }

        return $this->engineResponse(14, null, __tr('Bot Flow not updated.'));
    }

    /**
     * Process Bot flow data update
     *
     * @param BaseRequestTwo $request
     * @return EngineResponse
     */
    public function processBotFlowDataUpdate($request)
    {
        $vendorId = getVendorId();
        $botFlow = $this->botFlowRepository->fetchIt([
            '_uid' => $request->botFlowUid,
            'vendors__id' => $vendorId,
        ]);
        // Check if $botFlow not exist then throw not found
        // exception
        if (__isEmpty($botFlow)) {
            return $this->engineResponse(18, null, __tr('Bot Flow not found.'));
        }
        $updateData = [];
        $isTriggersReset = false;
        if($request->has('flow_chart_data')) {
            $flowChartData = $request->flow_chart_data;
            $flowChatLinks = $flowChartData['links'] ?? [];
            $flowChatOperators = $flowChartData['operators'] ?? [];
            $flowBots = $this->botReplyRepository->fetchItAll([
                'vendors__id' => $vendorId,
                'bot_flows__id' => $botFlow->_id,
            ]);
            if(!__isEmpty($flowBots)) {
                $flowBotsArray = $flowBots->keyBy('_uid');
                // clean up
                if(!__isEmpty($flowChatOperators)) {
                    foreach ($flowChatOperators as $flowChatOperatorKey => $flowChatOperatorValue) {
                        if(!($flowBotsArray[$flowChatOperatorKey] ?? null)) {
                            unset($flowChatOperators[$flowChatOperatorKey]);
                        }
                    }
                    $flowChartData['operators'] = $flowChatOperators;
                }
                $flowBotsForTheLinksUids = [];
                $botTriggers = [];
                foreach ($flowChatLinks as $link) {
                    $flowBotsForTheLinksUids[] = $link['toOperator'];
                    if(!isset($botTriggers[$link['toOperator']])) {
                        $botTriggers[$link['toOperator']] = [];
                    }
                    if($link['fromOperator'] != 'start') {
                        $fromBot = $flowBotsArray[$link['fromOperator']] ?? [];
                        if(!__isEmpty($fromBot)) {
                            $botButtons =  $fromBot->__data['interaction_message']['buttons'] ?? [];
                            $triggerSubject = null;
                            if(empty($botButtons)) {
                                $listDataSections =  $fromBot->__data['interaction_message']['list_data'] ?? [];
                                if(!empty($listDataSections)) {
                                    $listDataSections =  $fromBot->__data['interaction_message']['list_data'] ?? [];
                                    $triggerSubject = Arr::get($listDataSections, str_replace('___', '.', $link['fromConnector']));
                                }
                            } else {
                                $triggerSubject = Arr::get($botButtons, $link['fromConnector']);
                            }
                        }
                    } else {
                        $triggerSubject = $botFlow->start_trigger;
                    }
                    $toBot = $flowBotsArray[$link['toOperator']] ?? [];
                    if(!__isEmpty($toBot) and $triggerSubject) {
                        // collect for multiple triggers
                        $botTriggers[$link['toOperator']][] = $triggerSubject;
                        $this->botReplyRepository->updateIt($toBot, [
                            'reply_trigger' =>implode(',', ($botTriggers[$link['toOperator']] ?? [])),
                            'bot_replies__id' => $fromBot->_id ?? null,
                        ]);
                    }
                }
                $botsToResetTriggerUids = array_diff($flowBots->pluck('_uid')->toArray(), $flowBotsForTheLinksUids);
                if(!empty($botsToResetTriggerUids)) {
                    $isTriggersReset = $this->botReplyRepository->resetBotTriggers($botsToResetTriggerUids);
                }
            }
            $updateData['__data']['flow_builder_data'] = $flowChartData;
        }
        if($request->has('bot_flow_status')) {
            if($request->bot_flow_status) {
                $updateData['status'] = 1; // active
            } else {
                $updateData['status'] = 2; // inactive
            }
        }
        // Check if BotFlow updated
        if ($this->botFlowRepository->updateIt($botFlow, $updateData) or $isTriggersReset) {
            if($request->has('flow_chart_data')) {
                return $this->engineResponse(21, [
                    'reloadPage' => true,
                    'messageType' => 'success',
                ], __tr('Bot flow Data Updated'));
            }
            return $this->engineResponse(1, null, __tr('Bot flow Data Updated'));
        }
        // reloaded even not updated to prevent potential unsaved dialog issue
        return $this->engineResponse(21, [
            'reloadPage' => true,
            'messageType' => 'success',
        ], __tr('Bot flow Data Saved'));
    }

    /**
      * Prepare BotFlow Builder Data
      *
      * @param  mix $botFlowIdOrUid
      *
      * @return  EngineResponse
      *---------------------------------------------------------------- */

    public function prepareBotFlowBuilderData($botFlowIdOrUid)
    {
        $vendorId = getVendorId();
        $botFlow = $this->botFlowRepository->fetchIt([
          '_uid' => $botFlowIdOrUid,
          'vendors__id' => $vendorId,
        ]);
        // Check if $botFlow not exist then throw not found
        if (__isEmpty($botFlow)) {
            return $this->engineResponse(18, null, __tr('Bot Flow not found.'));
        }

        $flowBots = $this->botReplyRepository->fetchItAll([
              'bot_flows__id' => $botFlow->_id,
              'vendors__id' => $vendorId,
        ]);

        return $this->engineResponse(1, [
          'botFlow' => $botFlow,
          'flowBots' => $flowBots,
        ]);
    }
}
