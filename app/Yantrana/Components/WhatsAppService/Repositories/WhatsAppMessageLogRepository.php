<?php
/**
* WhatsAppMessageLogRepository.php - Repository file
*
* This file is part of the WhatsAppService component.
*-----------------------------------------------------------------------------*/

namespace App\Yantrana\Components\WhatsAppService\Repositories;

use Carbon\Carbon;
use Illuminate\Support\Arr;
use App\Yantrana\Base\BaseRepository;
use App\Yantrana\Components\WhatsAppService\Models\WhatsAppMessageLogModel;
use App\Yantrana\Components\WhatsAppService\Interfaces\WhatsAppMessageLogRepositoryInterface;

class WhatsAppMessageLogRepository extends BaseRepository implements WhatsAppMessageLogRepositoryInterface
{
    /**
     * primary model instance
     *
     * @var object
     */
    protected $primaryModel = WhatsAppMessageLogModel::class;

    public function updateOrCreateWhatsAppMessageFromWebhook(
        $phoneNumberId,
        $contactId,
        $vendorId,
        $messageRecipientId,
        $messageWamid,
        $messageStatus,
        $messageEntry,
        $message = '',
        $timestamp = null,
        ?array $mediaData = null,
        ?bool $preventCreation = false,
        ?array $options = []
    ) {
        if(!empty($options)) {
            $options = array_merge([
                'bot_reply' => false,
                'interaction_message_data' => null,
            ], $options);
        }

        $findTheExistingLogEntry = [
            // 'wab_phone_number_id' => $phoneNumberId,
            'contact_wa_id' => (string) $messageRecipientId,
            // 'wamid' => $messageWamid,
            'vendors__id' => $vendorId,
        ];
        if($options['message_log_id'] ?? null) {
            $findTheExistingLogEntry['_id'] = $options['message_log_id'];
        } else  {
            $findTheExistingLogEntry['wamid'] = $messageWamid;
        }
        $messageLogModel = $this->fetchIt($findTheExistingLogEntry);
        // may the message deleted from db but webhook is received for delivery or read receipt
        // in such a case no need to record the action
        if (__isEmpty($messageLogModel) and $preventCreation) {
            return false;
        }
        $dataToUpdate = [
            // 'status' => $messageStatus,
            'is_incoming_message' => 0,
        ];
        if(!__isEmpty($messageLogModel)) {
            if($messageLogModel->status != 'read') {
                $dataToUpdate['status'] = $messageStatus;
            }
        } else {
            $dataToUpdate['status'] = $messageStatus;
        }
        if ($timestamp and ($messageStatus == 'delivered')) {
            $dataToUpdate['messaged_at'] = Carbon::createFromTimestamp($timestamp);
        }
        if ($message || $mediaData) {
            $dataToUpdate['message'] = $message;
            $dataToUpdate['wab_phone_number_id'] = $phoneNumberId;
            $dataToUpdate['__data'] = [
                'options' => Arr::only($options, [
                    'bot_reply',
                    'ai_bot_reply',
                ]),
                'interaction_message_data' => $options['interaction_message_data'] ?? null,
                'initial_response' => [
                    $messageStatus => $messageEntry,
                ],
            ];
            if (! empty($mediaData)) {
                $dataToUpdate['__data']['media_values'] = $mediaData;
            }
        }
        if (__isEmpty($messageLogModel)) {
            $dataToUpdate['contacts__id'] = $contactId;

            return $this->storeIt(arrayExtend($findTheExistingLogEntry, $dataToUpdate));
        }

        if($this->updateIt($findTheExistingLogEntry, arrayExtend($dataToUpdate, [
            '__data' => [
                'options' => $options,
                'webhook_responses' => [
                    $messageStatus => $messageEntry,
                ],
            ],
        ]))) {
            return $findTheExistingLogEntry;
        }

        return false;
    }

    public function storeIncomingMessage(
        $phoneNumberId,
        $contactId,
        $vendorId,
        $messageRecipientId,
        $messageWamid,
        $messageEntry,
        $message,
        $timestamp,
        ?array $mediaData = null,
        ?string $repliedToMessage = null,
        ?bool $isForwarded = null,
    ) {
        $additionalData = [
            'webhook_responses' => [
                'incoming' => $messageEntry,
            ],
        ];
        if (! empty($mediaData)) {
            $additionalData['media_values'] = $mediaData;
        }

        return $this->storeIt([
            'wab_phone_number_id' => $phoneNumberId,
            'contact_wa_id' => $messageRecipientId,
            'wamid' => $messageWamid,
            'status' => 'received',
            'message' => $message,
            'is_incoming_message' => 1,
            'vendors__id' => $vendorId,
            'contacts__id' => $contactId,
            '__data' => $additionalData,
            'messaged_at' => is_numeric($timestamp) ? Carbon::createFromTimestamp($timestamp) : $timestamp,
            'replied_to_whatsapp_message_logs__uid' => $repliedToMessage,
            'is_forwarded' => $isForwarded,
        ]);
    }

    /**
     * Mark unread messages as read
     *
     * @param onject $contact
     * @param integer $vendorId
     * @return mixed
     */
    public function markAsRead($contact, $vendorId = null)
    {
        $vendorId = $vendorId ?: getVendorId();
        return $this->primaryModel::where([
            'contacts__id' => $contact->_id,
            'vendors__id' => $vendorId,
            // 'wab_phone_number_id' => (string) getVendorSettings('current_phone_number_id', null, null, $vendorId),
            'is_incoming_message' => 1,
            'status' => 'received',
        ])->update([
            'status' => 'read',
        ]);
    }

    /**
     * Get the all messages of the particular contact
     *
     * @param integer $contactId
     * @return object
     */
    public function allMessagesOfContact(int $contactId)
    {
        return $this->primaryModel::where([
            'contacts__id' => $contactId,
        ])->latest()->orderBy('messaged_at', 'desc')->simplePaginate(16);
    }
    /**
     * Get the recent messages of the particular contact
     *
     * @param integer $contactId
     * @return object
     */
    public function recentMessagesOfContact(int $contactId)
    {
        return $this->allMessagesOfContact($contactId);
    }

    /**
     * Get unread count for vendor
     *
     * @param int $vendorId
     * @param int $phoneNumberId
     * @return int
     */
    public function getUnreadCount($vendorId = null, $phoneNumberId = null)
    {
        $vendorId = $vendorId ?: getVendorId();
        return $this->countIt([
            'vendors__id' => $vendorId,
            'is_incoming_message' => 1,
            [
                'contacts__id', '!=', null
            ],
            'status' => 'received',
        ]);
    }
    /**
     * Get unread count for vendor
     *
     * @param int $vendorId
     * @param int $phoneNumberId
     * @return int
     */
    public function getMyAssignedUnreadMessagesCount($vendorId = null, $phoneNumberId = null)
    {
        $vendorId = $vendorId ?: getVendorId();
        return WhatsAppMessageLogModel::leftJoin('contacts', 'whatsapp_message_logs.contacts__id', '=', 'contacts._id')->where([
            'whatsapp_message_logs.vendors__id' => $vendorId,
            'whatsapp_message_logs.is_incoming_message' => 1,
            [
                'whatsapp_message_logs.contacts__id', '!=', null
            ],
            'whatsapp_message_logs.status' => 'received',
            'contacts.assigned_users__id' => getUserID(),
        ])->count();
    }

    /**
     * Clear chat history all the messages excluding campaign messages
     *
     * @param int $contactId
     * @param int $vendorId
     * @return bool|mixed
     */
    function clearChatHistory($contactId, $vendorId) {
        return $this->primaryModel::where([
            'vendors__id' => $vendorId,
            'contacts__id' => $contactId,
        ])->whereNull('campaigns__id')->delete();
    }
}
