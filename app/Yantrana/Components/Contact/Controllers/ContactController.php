<?php
/**
* ContactController.php - Controller file
*
* This file is part of the Contact component.
*-----------------------------------------------------------------------------*/

namespace App\Yantrana\Components\Contact\Controllers;

use Illuminate\Validation\Rule;
use App\Yantrana\Base\BaseRequest;
use Illuminate\Support\Facades\Gate;
use App\Yantrana\Base\BaseController;
use App\Yantrana\Base\BaseRequestTwo;
use Illuminate\Database\Query\Builder;
use App\Yantrana\Components\Contact\ContactEngine;


class ContactController extends BaseController
{
    /**
     * @var ContactEngine - Contact Engine
     */
    protected $contactEngine;

    /**
     * Constructor
     *
     * @param  ContactEngine  $contactEngine  - Contact Engine
     * @return void
     *-----------------------------------------------------------------------*/
    public function __construct(ContactEngine $contactEngine)
    {
        $this->contactEngine = $contactEngine;
    }

    /**
     * list of Contact
     *
     * @return json object
     *---------------------------------------------------------------- */
    public function showContactView($groupUid = null)
    {
        validateVendorAccess('manage_contacts');
        $contactsRequiredEngineResponse = $this->contactEngine->prepareContactRequiredData($groupUid);

       // print_r($contactsRequiredEngineResponse);exit;

        // load the view
        return $this->loadView('contact.list', $contactsRequiredEngineResponse->data());
    }

    /**
     * list of Contact
     *
     * @return json object
     *---------------------------------------------------------------- */
    public function prepareContactList($groupUid = null)
    {
        validateVendorAccess('manage_contacts');
        // respond with dataTables preparations
        return $this->contactEngine->prepareContactDataTableSource($groupUid);
    }

    /**
     * Contact process delete
     *
     * @param  mix  $contactIdOrUid
     * @return json object
     *---------------------------------------------------------------- */
    public function processContactDelete($contactIdOrUid, BaseRequest $request)
    {
        validateVendorAccess('manage_contacts');
        // ask engine to process the request
        $processReaction = $this->contactEngine->processContactDelete($contactIdOrUid);

        // get back to controller with engine response
        return $this->processResponse($processReaction, [], [], true);
    }
    /**
     * Contact process remove from group
     *
     * @param  mix  $contactIdOrUid
     * @return json object
     *---------------------------------------------------------------- */
    public function processContactRemoveFromGroup($contactIdOrUid,$groupUid, BaseRequest $request)
    {
        
        validateVendorAccess('manage_contacts');
        // ask engine to process the request
        $processReaction = $this->contactEngine->processContactRemove($contactIdOrUid,$groupUid);

        // get back to controller with engine response
        return $this->processResponse($processReaction, [], [], true);
    }

    /**
     * Selected Contacts delete process
     *
     * @param  BaseRequest  $request
     *
     * @return json object
     *---------------------------------------------------------------- */
    public function selectedContactsDelete(BaseRequest $request)
    {
        validateVendorAccess('manage_contacts');

        // restrict demo user
        if(isDemo() and isDemoVendorAccount()) {
            return $this->processResponse(22, [
                22 => __tr('Functionality is disabled in this demo.')
            ], [], true);
        }

        $request->validate([
            'selected_contacts' => 'required|array'
        ]);
        // ask engine to process the request
        $processReaction = $this->contactEngine->processSelectedContactsDelete($request);

        // get back to controller with engine response
        return $this->processResponse($processReaction, [], [], true);
    }
    /**
     * Selected Contacts delete process
     *
     * @param  BaseRequest  $request
     *
     * @return json object
     *---------------------------------------------------------------- */
    public function assignGroupsToSelectedContacts(BaseRequest $request)
    {
        validateVendorAccess('manage_contacts');
        $request->validate([
            'selected_contacts' => 'required|array',
            'selected_groups' => 'required|array'
        ]);
        // ask engine to process the request
        $processReaction = $this->contactEngine->processAssignGroupsToSelectedContacts($request);

        // get back to controller with engine response
        return $this->processResponse($processReaction, [], [], true);
    }

    /**
     * Contact create process
     *
     * @param  object BaseRequest $request
     * @return json object
     *---------------------------------------------------------------- */
    public function processContactCreate(BaseRequest $request)
    {
        validateVendorAccess('manage_contacts');
        // process the validation based on the provided rules
        $request->validate([
            'language_code' => 'nullable|alpha_dash',
            "phone_number" => [
                'required',
                'numeric',
                'min_digits:9',
                'min:1',
                'doesnt_start_with:+,0',
                Rule::unique('contacts', 'wa_id')->where(fn (Builder $query) => $query->where('vendors__id', getVendorId()))
            ],
            'email' => 'nullable|email',
        ]);

        if(str_starts_with($request->get('phone_number'), '0') or str_starts_with($request->get('phone_number'), '+')) {
            return $this->processResponse(2, __tr('Mobile number should be numeric value without prefixing 0 or +'));
        }

        // ask engine to process the request
        $processReaction = $this->contactEngine->processContactCreate($request->all());

        // get back with response
        return $this->processResponse($processReaction);
    }
    /**
     * Contact create process by API
     *
     * @param  object BaseRequest $request
     * @return json object
     *---------------------------------------------------------------- */
    public function apiProcessContactCreate(BaseRequest $request)
    {
        validateVendorAccess('manage_contacts');
        // process the validation based on the provided rules
        $request->validate([
            'first_name' => 'required',
            'last_name' => 'required',
            'country' => 'required',
            'language_code' => 'nullable|alpha_dash',
            "phone_number" => [
                'required',
                'numeric',
                'min_digits:9',
                'min:1',
                'doesnt_start_with:+,0',
                Rule::unique('contacts', 'wa_id')->where(fn (Builder $query) => $query->where('vendors__id', getVendorId()))
            ],
            'email' => 'nullable|email',
        ]);
        abortIf(str_starts_with($request->get('phone_number'), '0') or str_starts_with($request->get('phone_number'), '+'), null, 'phone number should be numeric value without prefixing 0 or +');
        // ask engine to process the request
        $inputData = $request->all();
        $inputData['country'] = getCountryIdByName($inputData['country'] ?? null);
        $processReaction = $this->contactEngine->processContactCreate($inputData);
        $contact = $processReaction->data('contact');
        return $this->processApiResponse($processReaction, [
            'contact_uid' => $contact?->_uid,
            'first_name' => $contact?->first_name,
            'last_name' => $contact?->last_name,
            'phone_number' => $contact?->wa_id,
            'language_code' => $contact?->language_code,
            'country' => $contact?->country?->name,
        ]);
    }

    /**
     * Contact get update data
     *
     * @param  mix  $contactIdOrUid
     * @return json object
     *---------------------------------------------------------------- */
    public function updateContactData($contactIdOrUid)
    {
        validateVendorAccess('manage_contacts');
        // ask engine to process the request
        $processReaction = $this->contactEngine->prepareContactUpdateData($contactIdOrUid);

        // get back to controller with engine response
        return $this->processResponse($processReaction, [], [], true);
    }

    /**
     * Contact process update
     *
     * @param  mix @param  mix $contactIdOrUid
     * @param  object BaseRequest $request
     * @return json object
     *---------------------------------------------------------------- */
    public function processContactUpdate(BaseRequest $request)
    {
        validateVendorAccess('manage_contacts');
        // process the validation based on the provided rules
        $request->validate([
            'contactIdOrUid' => 'required',
            'email' => 'nullable|email',
        ]);
        // ask engine to process the request
        $processReaction = $this->contactEngine->processContactUpdate($request->get('contactIdOrUid'), $request->all());

        // get back with response
        return $this->processResponse($processReaction, [], [], true);
    }
    /**
     * Toggle AI Bot for COntact
     *
     * @param  int|string $contactIdOrUid
     * @return json object
     *---------------------------------------------------------------- */
    public function toggleAiBot(BaseRequest $request, $contactIdOrUid)
    {
        validateVendorAccess('messaging');
        // ask engine to process the request
        $processReaction = $this->contactEngine->processToggleAiBot($contactIdOrUid);
        // get back with response
        return $this->processResponse($processReaction, [], [], true);
    }

    /**
     * Export Contacts
     *
     * @param string $exportType
     * @return file
     */
    public function exportContacts($exportType = null)
    {
        
        validateVendorAccess('manage_contacts');
        return $this->contactEngine->processExportContacts($exportType);
    }

    /**
     * Import Contacts
     *
     * @param BaseRequestTwo $request
     * @return json
     */
    public function importContacts(BaseRequestTwo $request)
    {
        validateVendorAccess('manage_contacts');
        // restrict demo user
        if(isDemo() and isDemoVendorAccount()) {
            return $this->processResponse(22, [
                22 => __tr('Functionality is disabled in this demo.')
            ], [], true);
        }

        $request->validate([
            'document_name' => 'required'
        ]);
        return $this->processResponse(
            $this->contactEngine->processImportContacts($request),
            [],
            [],
            true
        );
    }

    /**
     * Contact process update
     *
     * @param  object BaseRequest $request
     * @return json object
     *---------------------------------------------------------------- */
    public function assignChatUser(BaseRequest $request)
    {
        validateVendorAccess('messaging');
        // process the validation based on the provided rules
        $request->validate([
            'contactIdOrUid' => 'required|uuid',
        ]);
        // ask engine to process the request
        $processReaction = $this->contactEngine->processAssignChatUser($request);
        // get back with response
        return $this->processResponse($processReaction, [], [], true);
    }

    /**
     * Contact notes process update
     *
     * @param  object BaseRequest $request
     * @return json object
     *---------------------------------------------------------------- */
    public function updateNotes(BaseRequest $request)
    {
        validateVendorAccess('messaging');
        // process the validation based on the provided rules
        $request->validate([
            'contactIdOrUid' => 'required|uuid',
            // 'contact_notes' => 'nullable',
        ]);
        // ask engine to process the request
        $processReaction = $this->contactEngine->processUpdateNotes($request);
        // get back with response
        return $this->processResponse($processReaction, [], [], true);
    }

    /**
     * Get all the labels
     *
     * @param [type] $contactUid
     * @return void
     */
    public function getLabels($contactUid)
    {
        validateVendorAccess('messaging');
        $processReaction = $this->contactEngine->getLabelsData($contactUid);
        return $this->processResponse($processReaction, [], [], true);
    }
    /**
     * Create new label for vendor
     *
     * @param BaseRequestTwo $request
     * @return void
     */
    public function createLabel(BaseRequestTwo $request)
    {
        validateVendorAccess('messaging');
        $request->validate([
            'title' => [
                'required',
                'max:45',
                Rule::unique('labels')->where(fn (Builder $query) => $query->where('vendors__id', getVendorId()))
            ],
            'text_color' => [
                'nullable',
                'string',
                'max:10',
            ],
            'bg_color' => [
                'nullable',
                'string',
                'max:10',
            ],
        ]);
        $processReaction = $this->contactEngine->createLabelProcess($request);
        return $this->processResponse($processReaction, [], [], true);
    }
    /**
     * Update label for vendor
     *
     * @param BaseRequestTwo $request
     * @return void
     */
    public function updateLabel(BaseRequestTwo $request)
    {
        validateVendorAccess('messaging');
        $request->validate([
            'labelUid' => [
                'required',
                'uuid'
            ],
            'title' => [
                'required',
                'max:45',
                Rule::unique('labels')->where(fn (Builder $query) => $query->where('vendors__id', getVendorId()))->ignore($request->labelUid, '_uid')
            ],
            'text_color' => [
                'nullable',
                'string',
                'max:10',
            ],
            'bg_color' => [
                'nullable',
                'string',
                'max:10',
            ],
        ]);
        $processReaction = $this->contactEngine->processUpdateLabel($request);
        return $this->processResponse($processReaction, [], [], true);
    }
    /**
     * Assign labels to contact
     *
     * @param BaseRequestTwo $request
     * @return void
     */
    public function assignContactLabels(BaseRequestTwo $request)
    {
        validateVendorAccess('messaging');
        $request->validate([
            'contactUid' => [
                'required',
                'uuid',
            ],
            'contact_labels' => [
                'nullable',
                'array',
                // 'max:10',
            ],
        ]);
        $processReaction = $this->contactEngine->assignContactLabelsProcess($request);
        return $this->processResponse($processReaction, [], [], true);
    }
    /**
     * Delete label
     *
     * @param BaseRequestTwo $request
     * @return json
     */
    public function deleteLabelProcess(BaseRequestTwo $request, $labelUid)
    {
        validateVendorAccess('messaging');
        $request->merge([
            'labelUid' => $request->labelUid
        ]);
        $request->validate([
            'labelUid' => [
                'required',
                'uuid',
            ],
        ]);
        $processReaction = $this->contactEngine->processDeleteLabel($labelUid);
        return $this->processResponse($processReaction, [], [], true);
    }
}
