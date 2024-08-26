<?php
/**
* BotReplyEngine.php - Main component file
*
* This file is part of the BotReply component.
*-----------------------------------------------------------------------------*/

namespace App\Yantrana\Components\BotReply;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use App\Yantrana\Base\BaseEngine;
use Illuminate\Database\Query\Builder;
use App\Yantrana\Components\Media\MediaEngine;
use App\Yantrana\Components\WhatsAppService\WhatsAppServiceEngine;
use App\Yantrana\Components\BotReply\Repositories\BotFlowRepository;
use App\Yantrana\Components\BotReply\Repositories\BotReplyRepository;
use App\Yantrana\Components\BotReply\Interfaces\BotReplyEngineInterface;
use App\Yantrana\Components\Contact\Repositories\ContactCustomFieldRepository;

class BotReplyEngine extends BaseEngine implements BotReplyEngineInterface
{
    /**
     * @var  BotReplyRepository $botReplyRepository - BotReply Repository
     */
    protected $botReplyRepository;

    /**
     * @var  ContactCustomFieldRepository $contactCustomFieldRepository - ContactCustomField Repository
     */
    protected $contactCustomFieldRepository;

    /**
     * @var MediaEngine - Media Engine
     */
    protected $mediaEngine;

    /**
     * @var WhatsAppServiceEngine - WhatsApp Service Engine
     */
    protected $whatsAppServiceEngine;

    /**
     * @var  BotFlowRepository $botFlowRepository - BotFlow Repository
     */
    protected $botFlowRepository;

    /**
      * Constructor
      *
      * @param  BotReplyRepository $botReplyRepository - BotReply Repository
      * @param  ContactCustomFieldRepository $contactCustomFieldRepository - BotReply Repository
      * @param  MediaEngine $mediaEngine
      * @param  WhatsAppServiceEngine $whatsAppServiceEngine
      * @param  BotFlowRepository $botFlowRepository
      *
      * @return  void
      *-----------------------------------------------------------------------*/

    public function __construct(
        BotReplyRepository $botReplyRepository,
        ContactCustomFieldRepository $contactCustomFieldRepository,
        MediaEngine $mediaEngine,
        WhatsAppServiceEngine $whatsAppServiceEngine,
        BotFlowRepository $botFlowRepository,
    ) {
        $this->botReplyRepository = $botReplyRepository;
        $this->contactCustomFieldRepository = $contactCustomFieldRepository;
        $this->mediaEngine = $mediaEngine;
        $this->whatsAppServiceEngine = $whatsAppServiceEngine;
        $this->botFlowRepository = $botFlowRepository;
    }

    /**
     * Get contact dynamic fields and custom fields
     *
     * @return EngineResponse
     */
    public function preDataForBots()
    {
        $vendorId = getVendorId();

        $dynamicFieldsToReplace = [
            '{first_name}',
            '{last_name}',
            '{full_name}',
            '{phone_number}',
            '{email}',
            '{country}',
            '{language_code}',
        ];

        $customFields = $this->contactCustomFieldRepository->fetchItAll([
            'vendors__id' => $vendorId
        ]);

        foreach ($customFields as $customField) {
            $dynamicFieldsToReplace[] = "{{$customField->input_name}}";
        }

        return $this->engineSuccessResponse([
            'dynamicFields' => $dynamicFieldsToReplace
        ]);
    }

    /**
      * BotReply datatable source
      *
      * @return  array
      *---------------------------------------------------------------- */
    public function prepareBotReplyDataTableSource()
    {
        $botReplyCollection = $this->botReplyRepository->fetchBotReplyDataTableSource();
        $orderStatuses = configItem('status_codes');
        $botTriggerTypes = configItem('bot_reply_trigger_types');
        // required columns for DataTables
        $requireColumns = [
            '_id',
            '_uid',
            'name',
            'reply_text',
            'trigger_type',
            'trigger_type' => function ($rowData) use(&$botTriggerTypes) {
                return $botTriggerTypes[$rowData['trigger_type']]['title'] ?? '';
            },
            'reply_trigger' => function ($rowData) {
                return ($rowData['trigger_type'] != 'welcome') ? $rowData['reply_trigger'] : '';
            },
            'created_at' => function ($rowData) {
                return formatDateTime($rowData['created_at']);
            },
            'status' => function ($key) use (&$orderStatuses) {
                if($key['status'] === null) {
                    $key['status'] = 1; // active
                }
                return Arr::get($orderStatuses, $key['status']);
            },
            'bot_type' => function ($rowData) {
                $botReplyType = __tr('Simple');
                if($rowData['__data']['media_message'] ?? null) {
                    $botReplyType = __tr('Media');
                } elseif($rowData['__data']['interaction_message'] ?? null) {
                    $botReplyType = __tr('Interactive/Buttons');
                    ;
                }
                return $botReplyType;
            },
        ];
        // prepare data for the DataTables
        return $this->dataTableResponse($botReplyCollection, $requireColumns);
    }


    /**
      * BotReply delete process
      *
      * @param  mix $botReplyIdOrUid
      *
      * @return  EngineResponse
      *---------------------------------------------------------------- */

    public function processBotReplyDelete($botReplyIdOrUid)
    {
        $vendorId = getVendorId();
        // fetch the record
        $botReply = $this->botReplyRepository->fetchIt([
            '_uid' => $botReplyIdOrUid,
            'vendors__id' => $vendorId,
        ]);
        // check if the record found
        if (__isEmpty($botReply)) {
            // if not found
            return $this->engineResponse(18, [
                'botReplyUid' => $botReplyIdOrUid
            ], __tr('Bot Reply not found'));
        }

        // demo bot delete protection
        if(isDemo() and in_array($botReply->_id, explode(',', config('__tech.demo_protected_bots', )))) {
            return $this->engineResponse(2, null, __tr('Your are not allowed to delete this bot in DEMO.'));
        }
        // ask to delete the record
        if ($this->botReplyRepository->deleteIt($botReply)) {
            // delete the links
            $this->botReplyRepository->updateItAll([
                'bot_replies__id' => $botReply->_id
            ], [
                'reply_trigger' => null
            ]);
            // if successful
            return $this->engineResponse(1, [
                'botReplyUid' => $botReply->_uid
            ], __tr('Bot Reply deleted successfully'));
        }
        // if failed to delete
        return $this->engineResponse(2, [
            'botReplyUid' => $botReplyIdOrUid
        ], __tr('Failed to delete BotReply'));
    }
    /**
      * BotReply duplicate process
      *
      * @param  mix $botReplyIdOrUid
      *
      * @return  EngineResponse
      *---------------------------------------------------------------- */

    public function processBotReplyDuplicate($botReplyIdOrUid)
    {
        $vendorId = getVendorId();
        // fetch the record
        $botReply = $this->botReplyRepository->fetchIt([
            '_uid' => $botReplyIdOrUid,
            'vendors__id' => $vendorId,
        ]);
        // check if the record found
        if (__isEmpty($botReply)) {
            // if not found
            return $this->engineResponse(18, [
                'botReplyUid' => $botReplyIdOrUid
            ], __tr('Bot Reply not found'));
        }
        // do not apply plan restriction if bot is getting added for bot flow
        // as there no limit for flows
        if(!$botReply->bot_flows__id) {
            // check the feature limit
            $vendorPlanDetails = vendorPlanDetails('bot_replies', $this->botReplyRepository->countIt([
                'vendors__id' => $vendorId,
                'bot_flows__id' => null,
            ]), $vendorId);
            if (!$vendorPlanDetails['is_limit_available']) {
                return $this->engineResponse(22, null, $vendorPlanDetails['message']);
            }
        }
        // ask to duplicate the record
        $newBotUid = Str::uuid();
        $newBotReply = $botReply->replicate();
        $newBotReply->name = $botReply->name . '-' . uniqid();
        $newBotReply->_uid = $newBotUid;
        $botData = $botReply->__data;
        // unset buttons
        if(isset($botData['interaction_message']['buttons'])) {
            $botData['interaction_message']['buttons'] = [];
        }
        // unset list data
        if(isset($botData['interaction_message']['list_data'])) {
            $botData['interaction_message']['list_data'] = [
                'button_text' => $botData['interaction_message']['list_data']['button_text'],
            ];
        }
        if($botReply->bot_flows__id) {
            $newBotReply->reply_trigger = null;
            $newBotReply->status = 2; // it always be inactive for bot flow
            $newBotReply->__data = $botData;
        }
        if ($newBotReply->save()) {
            if($botReply->bot_flows__id) {
                return $this->engineResponse(21, [
                    'reloadPage' => true,
                    'messageType' => 'success',
                ], __tr('Bot Reply duplicated'));
            }
            // if successful
            return $this->engineResponse(1, [
                'botReplyUid' => $newBotUid
            ], __tr('Bot Reply duplicated'));
        }
        // if failed to delete
        return $this->engineResponse(2, [
            'botReplyUid' => $botReplyIdOrUid
        ], __tr('Failed to duplicate Bot Reply'));
    }

    /**
      * BotReply create
      *
      * @param  array $inputData
      *
      * @return  array
      *---------------------------------------------------------------- */

    public function processBotReplyCreate($request)
    {
        $inputData = $request->all();
        $vendorId = getVendorId();
        $inputData['status'] = ($inputData['status'] ?? null) ? 1 : 2;
        // if bot flow
        if(isset($inputData['bot_flow_uid']) and $inputData['bot_flow_uid']) {
            $botFlow = $this->botFlowRepository->fetchIt($inputData['bot_flow_uid']);
            if (__isEmpty($botFlow)) {
                return $this->engineResponse(2, null, __tr('Invalid bot flow'));
            }
            $inputData['bot_flows__id'] = $botFlow->_id;

            $request->validate([
                'name' => [
                    "required",
                    "max:200",
                    Rule::unique('bot_replies')->where(fn (Builder $query) => $query->where([
                        'vendors__id' => $vendorId,
                        'bot_flows__id' => $botFlow->_id,
                    ]))
                ]
            ]);
            // bot flow bot always in inactive state, its state will be depend on flow status
            $inputData['status'] = 2;
        }
        $messageType = $inputData['message_type'] ?? 'simple';
        // do not apply plan restriction if bot is getting added for bot flow
        // as there no limit for flows
        if(!isset($inputData['bot_flow_uid']) or !$inputData['bot_flow_uid']) {
            // check the feature limit
            $vendorPlanDetails = vendorPlanDetails('bot_replies', $this->botReplyRepository->countIt([
                'vendors__id' => $vendorId,
                'bot_flows__id' => null,
            ]), $vendorId);
            if (!$vendorPlanDetails['is_limit_available']) {
                return $this->engineResponse(22, null, $vendorPlanDetails['message']);
            }
        }
        $inputData['vendors__id'] = $vendorId;
        if($messageType == 'interactive') {
            $interactiveType = $inputData['interactive_type'] ?? 'button';
            $mediaLink = '';
            if($inputData['header_type'] and ($inputData['header_type'] != 'text')) {
                $isProcessed = $this->mediaEngine->whatsappMediaUploadProcess(['filepond' => $inputData['uploaded_media_file_name']], 'whatsapp_' . $inputData['header_type']);
                if ($isProcessed->failed()) {
                    return $isProcessed;
                }
                $mediaLink = $isProcessed->data('path');
            }
            $ctaUrlButton = null;
            $listData = null;
            if($interactiveType == 'cta_url') {
                $ctaUrlButton = [
                    'display_text' => $inputData['button_display_text'],
                    'url' => $inputData['button_url'],
                ];
            }
            if($interactiveType == 'list') {
                $listData = [
                    'button_text' => $inputData['list_button_text'],
                    'sections' => array_filter($inputData['sections'] ?? []),
                ];
            }
            $inputData['__data'] = [
                'interaction_message' => [
                    'interactive_type' => $interactiveType,
                    'media_link' => $mediaLink,
                    'header_type' => $inputData['header_type'], // "text", "image", or "video"
                    'header_text' => $inputData['header_text'] ?? '',
                    'body_text' => $inputData['reply_text'],
                    'footer_text' => $inputData['footer_text'] ?? '',
                    'buttons' => array_filter($inputData['buttons'] ?? []),
                    'cta_url' => $ctaUrlButton,
                    'list_data' => $listData,
                ]
            ];
        } elseif($messageType == 'media') {
            $inputData['header_type'] = $inputData['media_header_type'];
            $mediaLink = '';
            $isProcessed = $this->mediaEngine->whatsappMediaUploadProcess(['filepond' => $inputData['uploaded_media_file_name']], 'whatsapp_' . $inputData['header_type']);
            if ($isProcessed->failed()) {
                return $isProcessed;
            }
            $mediaLink = $isProcessed->data('path');
            $inputData['reply_text'] = '';
            $inputData['__data'] = [
                'media_message' => [
                    'media_link' => $mediaLink,
                    'header_type' => $inputData['header_type'], // "text", "image", "audio or "video"
                    'caption' => $inputData['caption'] ?? '',
                    'file_name' => $isProcessed->data('fileName'),
                ]
            ];
        }
        // ask to add record
        $engineResponse = $this->botReplyRepository->processTransaction(function () use (&$inputData, &$vendorId) {
            if ($botReply = $this->botReplyRepository->storeBotReply($inputData)) {
                // if needs to validate message using by sending test message
                if($inputData['validate_bot_reply'] ?? null) {
                    $validateTestBotReply = $this->whatsAppServiceEngine->validateTestBotReply($botReply->_id);
                    if($validateTestBotReply->success()) {
                        return $this->botReplyRepository->transactionResponse(1, [], __tr('Bot Reply Created'));
                    }
                    // if got any errors etc
                    return $this->botReplyRepository->transactionResponse($validateTestBotReply->reaction(), [], $validateTestBotReply->message());
                }
                // success
                return $this->botReplyRepository->transactionResponse(1, [], __tr('Bot Reply Created'));
            }
            // failed for any other reason
            return $this->botReplyRepository->transactionResponse(2, [], __tr('Failed to create Bot Reply'));
        });
        // if bot flow
        if(isset($inputData['bot_flow_uid']) and $inputData['bot_flow_uid'] and ($engineResponse[0] == 1)) {
            $flowBots = $this->botReplyRepository->fetchItAll([
                'bot_flows__id' => $inputData['bot_flows__id'],
                'vendors__id' => $vendorId,
            ]);
            updateClientModels([
                'flowBots' => $flowBots,
            ]);
        }
        return $this->engineResponse($engineResponse);

    }

    /**
      * BotReply prepare update data
      *
      * @param  mix $botReplyIdOrUid
      *
      * @return  array
      *---------------------------------------------------------------- */

    public function prepareBotReplyUpdateData($botReplyIdOrUid)
    {
        $vendorId = getVendorId();
        // fetch the record
        $botReply = $this->botReplyRepository->fetchIt([
            '_uid' => $botReplyIdOrUid,
            'vendors__id' => $vendorId,
        ]);
        // Check if $botReply not exist then throw not found
        // exception
        if (__isEmpty($botReply)) {
            return $this->engineResponse(18, null, __tr('Bot Reply not found.'));
        }

        return $this->engineResponse(1, $botReply->toArray());
    }

    /**
      * BotReply process update
      *
      * @param  mixed $botReplyIdOrUid
      * @param  object $request
      *
      * @return  array
      *---------------------------------------------------------------- */

    public function processBotReplyUpdate($botReplyIdOrUid, $request)
    {
        $vendorId = getVendorId();
        // fetch the record
        $botReply = $this->botReplyRepository->fetchIt([
            '_uid' => $botReplyIdOrUid,
            'vendors__id' => $vendorId,
        ]);
        // Check if $botReply not exist then throw not found
        // exception
        if (__isEmpty($botReply)) {
            return $this->engineResponse(18, null, __tr('Bot Reply not found.'));
        }
        // demo bot edit protection
        if(isDemo() and in_array($botReply->_id, explode(',', config('__tech.demo_protected_bots', )))) {
            return $this->engineResponse(2, null, __tr('Your are not allowed to edit this bot in DEMO.'));
        }
        $botFlowId = null;
        $inputData = $request->all();
        // if bot flow
        if(isset($inputData['bot_flow_uid']) and $inputData['bot_flow_uid']) {
            $botFlow = $this->botFlowRepository->fetchIt($inputData['bot_flow_uid']);
            if (__isEmpty($botFlow)) {
                return $this->engineResponse(2, null, __tr('Invalid bot flow'));
            }
            $botFlowId = $botFlow->_id;
        }
        $currentInputSectionsData = array_filter($inputData['sections'] ?? []);
        $currentInputButtonsData = array_filter($inputData['buttons'] ?? []);
        if($botFlowId) {
            // validate for uniqueness
            $request->validate([
                "name" => [
                    "required",
                    "max:200",
                    Rule::unique('bot_replies')->where(fn (Builder $query) => $query->where([
                        'vendors__id' => $vendorId,
                        'bot_flows__id'  => $botFlowId,
                    ]))->ignore($botReply->_id, '_id')
                ],
            ]);
        } else {
            $request->validate([
                "name" => [
                    "required",
                    "max:200",
                    Rule::unique('bot_replies')->where(fn (Builder $query) => $query->where([
                        'vendors__id' => $vendorId,
                        'bot_flows__id'  => null,
                    ]))->ignore($botReply->_id, '_id')
                ],
            ]);
        }

        $messageType = $inputData['message_type'] ?? 'simple';
        $updateData = [
            'name' => $inputData['name'],
            'reply_text' => $inputData['reply_text'] ?? '',
        ];
        if(!$botFlowId) {
            $updateData['trigger_type'] = $request->trigger_type;
            $updateData['reply_trigger'] = $request->reply_trigger;
            $updateData['status'] = $request->status ? 1 : 2;
        }
        // media message
        if($messageType == 'media') {
            $updateData['__data'] = [
                'media_message' => [
                    'caption' => $inputData['caption'] ?? '',
                ]
            ];
        } elseif($messageType == 'interactive') {
            $interactiveType = $inputData['interactive_type'] ?? 'button';
            $ctaUrlButton = null;
            if($interactiveType == 'cta_url') {
                $ctaUrlButton = [
                    'display_text' => $inputData['button_display_text'],
                    'url' => $inputData['button_url'],
                ];
            }
            $listData = null;
            if($interactiveType == 'list') {
                $listData = [
                    'button_text' => $inputData['list_button_text'],
                    'sections' => $currentInputSectionsData,
                ];
            }
            $updateData['__data'] = [
                'interaction_message' => [
                    'interactive_type' => $interactiveType,
                    'header_text' => $inputData['header_text'] ?? '',
                    'body_text' => $inputData['reply_text'],
                    'footer_text' => $inputData['footer_text'] ?? '',
                    'buttons' => $currentInputButtonsData,
                    'cta_url' => $ctaUrlButton,
                    'list_data' => $listData,
                ]
            ];
        }
        // update process
        $engineResponse = $this->botReplyRepository->processTransaction(function () use (&$botReply, &$updateData, &$request, &$interactiveType, &$currentInputButtonsData, &$botFlowId, &$botFlow, $currentInputSectionsData, &$inputData) {
            $isUpdated = false;
            $botFlowData = $botFlow->__data ?? [];
            $botData = $botReply->__data;
            $existingLinks = $botFlowData['flow_builder_data']['links'] ?? [];
            if($interactiveType == 'button') {
                // update links etc for bot flow
                if($botFlowId) {
                    $existingButtons = $botReply->__data['interaction_message']['buttons'] ?? [];
                    $buttonsRemoved = array_diff($existingButtons, $currentInputButtonsData);
                    $buttonsAdded = array_diff($currentInputButtonsData, $existingButtons);
                    $existingBotsForTrigger = $this->botReplyRepository->fetchItAll($existingButtons, [], 'reply_trigger', [
                        'where' => [
                            'bot_flows__id' => $botFlowId,
                            'bot_replies__id' => $botReply->_id,
                        ]
                    ]);
                    foreach ($existingBotsForTrigger as $existingBotForTrigger) {
                        $itemIndexInRemoved = array_search($existingBotForTrigger->reply_trigger, $buttonsRemoved);
                        if($itemIndexInRemoved !== false) {
                            $newReplyTrigger = $buttonsAdded[$itemIndexInRemoved] ?? null;
                            $isUpdated = $this->botReplyRepository->updateIt($existingBotForTrigger, [
                                'reply_trigger' => $newReplyTrigger
                            ]);
                            if(!$newReplyTrigger) {
                                if(!empty($existingLinks)) {
                                    $existingLinks = Arr::where($existingLinks, function ($value, $key) use (&$existingBotForTrigger) {
                                        return $value['toOperator'] != $existingBotForTrigger['_uid'];
                                    });
                                }
                            }
                        }
                    }
                    // update flow links
                    // following type of method to update JSON creates problem for mariadb 
                    //  '__data->flow_builder_data->links' => $existingLinks
                    // instead we have used it
                    Arr::set($botFlowData, 'flow_builder_data.links', $existingLinks);
                    $isUpdated = $this->botFlowRepository->updateBotFlowData($botFlowId, [
                        '__data' => json_encode($botFlowData)
                    ]);
                }
                // update buttons
                // following type of method to update JSON creates problem for mariadb 
                //  '__data->interaction_message->buttons' => $updateData['__data']['interaction_message']['buttons']
                // instead we have used it
                Arr::set($botData, 'interaction_message.buttons', $updateData['__data']['interaction_message']['buttons']);
                unset($updateData['__data']);
                if($request->has('footer_text') and !$request->footer_text) {
                    Arr::set($botData, 'interaction_message.footer_text', '');
                }
                $updateData['__data'] = json_encode($botData);
                $isUpdated = $this->botReplyRepository->updateForListAndButtonMessage($botReply->_id, $updateData);
            } elseif($interactiveType == 'list') {
                $listDataSections =  $botReply->__data['interaction_message']['list_data']['sections'] ?? [];
                $existingRowSubjects = [];
                foreach ($listDataSections as $listDataSection) {
                    foreach ($listDataSection['rows'] as $listDataSectionRow) {
                        $existingRowSubjects[] = $listDataSectionRow['title'];
                    }
                }
                $currentRowSubjects = [];
                foreach ($currentInputSectionsData as $currentInputSection) {
                    foreach ($currentInputSection['rows'] as $currentInputSectionRow) {
                        $currentRowSubjects[] = $currentInputSectionRow['title'];
                    }
                }

                $rowsRemoved = array_diff($existingRowSubjects, $currentRowSubjects);
                $rowsAdded = array_diff($currentRowSubjects, $existingRowSubjects);
                $existingBotsForTrigger = $this->botReplyRepository->fetchItAll($existingRowSubjects, [], 'reply_trigger', [
                    'where' => [
                        'bot_flows__id' => $botFlowId,
                        'bot_replies__id' => $botReply->_id,
                    ]
                ]);
                foreach ($existingBotsForTrigger as $existingBotForTrigger) {
                    $itemIndexInRemoved = array_search($existingBotForTrigger->reply_trigger, $rowsRemoved);
                    if($itemIndexInRemoved !== false) {
                        $newReplyTrigger = $rowsAdded[$itemIndexInRemoved] ?? null;
                        $isUpdated = $this->botReplyRepository->updateIt($existingBotForTrigger, [
                            'reply_trigger' => $newReplyTrigger
                        ]);
                        if(!$newReplyTrigger) {
                            if(!empty($existingLinks)) {
                                $existingLinks = Arr::where($existingLinks, function ($value, $key) use (&$existingBotForTrigger) {
                                    return $value['toOperator'] != $existingBotForTrigger['_uid'];
                                });
                            }
                        }
                    }
                }
                // update flow links
                // following type of method to update JSON creates problem for mariadb 
                //  '__data->flow_builder_data->links' => $existingLinks
                // instead we have used it
                Arr::set($botFlowData, 'flow_builder_data.links', $existingLinks);
                $isUpdated = $this->botFlowRepository->updateBotFlowData($botFlowId, [
                    '__data' => json_encode($botFlowData)
                ]);
                // following type of method to update JSON creates problem for mariadb 
                //  '__data->interaction_message->buttons' => $updateData['__data']['interaction_message']['buttons']
                // instead we have used it
                Arr::set($botData, 'interaction_message', $updateData['__data']['interaction_message']);
                unset($updateData['__data']);
                if($request->has('footer_text') and !$request->footer_text) {
                    Arr::set($botData, 'interaction_message.footer_text', '');
                }
                $updateData['__data'] = json_encode($botData);
                $isUpdated = $this->botReplyRepository->updateForListAndButtonMessage($botReply->_id, $updateData);
            } else {
                $isUpdated = $this->botReplyRepository->updateIt($botReply, $updateData);
                if($request->has('footer_text') and !$request->footer_text) {
                    $isUpdated = $this->botReplyRepository->updateForListAndButtonMessage($botReply->_id, [
                        '__data->interaction_message->footer_text' => ''
                    ]);
                }
            }
            if ($isUpdated) {
                if($request->validate_bot_reply) {
                    $validateTestBotReply = $this->whatsAppServiceEngine->validateTestBotReply($botReply->_id);
                    if($validateTestBotReply->success()) {
                        return $this->botReplyRepository->transactionResponse(1, [], __tr('Bot Reply updated.'));
                    }
                    return $this->botReplyRepository->transactionResponse($validateTestBotReply->reaction(), [], $validateTestBotReply->message());
                }
                return $this->botReplyRepository->transactionResponse(1, [], __tr('Bot Reply updated.'));
            }
            return $this->botReplyRepository->transactionResponse(2, [], __tr('Bot Reply Not updated.'));
        });
        // if bot flow
        if($botFlowId and ($engineResponse[0] == 1)) {
            // reloadPage
            return $this->engineResponse(21, [
                'reloadPage' => true
            ]);
        }
        return $this->engineResponse($engineResponse);
    }
}
