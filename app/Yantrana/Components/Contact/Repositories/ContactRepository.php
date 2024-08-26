<?php
/**
* ContactRepository.php - Repository file
*
* This file is part of the Contact component.
*-----------------------------------------------------------------------------*/

namespace App\Yantrana\Components\Contact\Repositories;

use Illuminate\Support\Facades\DB;
use App\Yantrana\Base\BaseRepository;
use Illuminate\Database\Query\JoinClause;
use App\Yantrana\Support\Country\Models\Country;
use App\Yantrana\Components\Contact\Models\ContactModel;
use App\Yantrana\Components\Contact\Interfaces\ContactRepositoryInterface;

class ContactRepository extends BaseRepository implements ContactRepositoryInterface
{
    /**
     * primary model instance
     *
     * @var object
     */
    protected $primaryModel = ContactModel::class;

    /**
     * Fetch contact datatable source
     *
     * @return mixed
     *---------------------------------------------------------------- */
    public function fetchContactDataTableSource($groupContactIds = null, $contactGroupUid = null)
    {
        // basic configurations for dataTables data
        $dataTableConfig = [
            // searchable columns
            'searchable' => [
                'first_name',
                'last_name',
                'countries__id',
                'wa_id',
                'email',
            ],
        ];

        // get Model result for dataTables
        $query = $this->primaryModel::where([
            'vendors__id' => getVendorId()
        ]);
        if ($contactGroupUid) {
            $query->whereIn('_id', $groupContactIds);
        }
        return $query->dataTables($dataTableConfig)->toArray();
    }

    /**
     * Delete $contact record and return response
     *
     * @param  object  $inputData
     * @return mixed
     *---------------------------------------------------------------- */
    public function deleteContact($contact)
    {
        // Check if $contact deleted
        if ($contact->deleteIt()) {
            // if deleted
            return true;
        }

        // if failed to delete
        return false;
    }

    /**
     * Store new contact record and return response
     *
     * @param  array  $inputData
     * @return mixed
     *---------------------------------------------------------------- */
    public function storeContact($inputData, $vendorId = null)
    {
        if(!$vendorId) {
            $vendorId = getVendorId();
        }
        // prepare data to store
        $keyValues = [
            'first_name',
            'last_name',
            'countries__id' => $inputData['country'] ?? null,
            'email',
            'language_code',
            'whatsapp_opt_out' => (isset($inputData['whatsapp_opt_out']) and $inputData['whatsapp_opt_out']) ? 1 : null,
            'wa_id' => $inputData['phone_number'],
            'vendors__id' => $vendorId,
        ];
        if(isset($inputData['enable_ai_bot'])) {
            $keyValues['disable_ai_bot'] = ($inputData['enable_ai_bot']) ? 0 : 1;
        } else {
            $keyValues['disable_ai_bot'] = getVendorSettings('default_enable_flowise_ai_bot_for_users', null, null, $vendorId) ? 0 : 1;
        }

        return $this->storeIt($inputData, $keyValues);
    }
    /**
     * Get vendor contact based on _id,_uid or phone_number which is wa_id
     *
     * @param string|integer|null $contactIdOrUid
     * @param string|null $vendorId
     * @return Eloquent object
     */
    public function getVendorContact(string|int|null $contactIdOrUid, ?string $vendorId = null)
    {
        $findBy = [
            'vendors__id' => $vendorId ? $vendorId : getVendorId(),
        ];

        if(request()->phone_number and isExternalApiRequest()) {
            $findBy['wa_id'] = (string) request()->phone_number;
        } else {
            if (is_numeric($contactIdOrUid)) {
                $findBy['_id'] = $contactIdOrUid;
            } else {
                $findBy['_uid'] = $contactIdOrUid;
            }
        }

        return $this->with([
            'lastMessage',
            'customFieldValues'
        ])->fetchIt($findBy);
    }

    /**
     * Get contact by phone number and vendor id
     *
     * @param integer $waId
     * @param string|null $vendorId
     * @return Eloquent
     */
    public function getVendorContactByWaId(int $waId, ?string $vendorId = null)
    {
        return $this->fetchIt([
            'vendors__id' => $vendorId ? $vendorId : getVendorId(),
            'wa_id' => (string) $waId,
        ]);
    }

    /**
     * Get the contact with unread message details using contact uid and vendor uid
     *
     * @param string|null $contactUid
     * @param int|null $vendorId
     * @param string|null $assigned
     * @return Eloquent
     */
    public function getVendorContactWithUnreadDetails($contactUid = null, $vendorId = null, $assigned = null)
    {
        $whereClause = [
            'vendors__id' => $vendorId ?: getVendorId(),
        ];
        if($contactUid) {
            $whereClause['_uid'] = $contactUid;
        }
        $query = $this->primaryModel::where($whereClause)->with([
            'lastMessage',
            'lastUnreadMessage',
            'lastIncomingMessage',
            'labels'
        ])->withCount('unreadMessages');

        if($assigned) {
            $query->where('assigned_users__id', getUserID());
        }

        if(!$contactUid) {
            $query->has('lastIncomingMessage');
        }

        return $query->first();
    }

    /**
     * Get contacts by vendor id
     *
     * @param string|null $vendorId
     * @return Eloquent
     */
    public function getVendorContactsWithUnreadDetails($vendorId = null, $assigned = null)
    {
        $searchQuery = e(strip_tags(request()->search));
        $unreadOnly = (!request()->unread_only or (request()->unread_only == 'false')) ? false : true;
        $query = $this->primaryModel::where([
            'contacts.vendors__id' => $vendorId ?: getVendorId(),
        ]);
        $assigned  = $assigned ?: request()->assigned;
        if($assigned) {
            $query->where('assigned_users__id', getUserID());
        }
        $requestContactUid = request()->request_contact;
        if($requestContactUid) {
            $query->where('_uid', $requestContactUid);
        }
        $query->join(
            DB::raw('(SELECT contacts__id, MAX(messaged_at) as latest_message FROM whatsapp_message_logs GROUP BY contacts__id) as latest_messages'),
            'contacts._id',
            '=',
            'latest_messages.contacts__id'
        )
        ->orderBy('latest_messages.latest_message', 'desc')
        ->leftJoin(
            DB::raw('(SELECT contacts__id, COUNT(*) as unread_messages_count FROM whatsapp_message_logs WHERE status = "received" AND is_incoming_message = 1 GROUP BY contacts__id) as unread_counts'),
            'contacts._id',
            '=',
            'unread_counts.contacts__id'
        )
        ->leftJoin(
            DB::raw('(SELECT contacts__id, GROUP_CONCAT(labels.title) as labels FROM contact_labels INNER JOIN labels ON contact_labels.labels__id = labels._id GROUP BY contacts__id) as contact_labels_concat'),
            'contacts._id',
            '=',
            'contact_labels_concat.contacts__id'
        );
        if($unreadOnly) {
            $query->whereNotNull('unread_counts.unread_messages_count');
        }
        if($searchQuery) {
            $query->whereAny([
                DB::raw('CONCAT(first_name, " ", last_name)'),
                'wa_id',
                'contact_labels_concat.labels'
            ], 'LIKE', '%'. $searchQuery .'%');
        }
        return $query->with([
            'lastMessage',
            'labels'
            ])
            // ->withCount('unreadMessages')
            ->has('lastIncomingMessage')
            ->simplePaginate(12);
    }

    /**
     * Delete the selected contacts based on uids provided
     * for the logged in vendor
     *
     * @param array $contactUids
     * @param integer|null $vendorId
     * @return mixed
     */
    public function deleteSelectedContacts(array $contactUids, int|null $vendorId = null)
    {
        return $this->primaryModel::where([
            'vendors__id' => $vendorId ?: getVendorId()
        ])->whereIn('_uid', $contactUids)->delete();
    }
}
