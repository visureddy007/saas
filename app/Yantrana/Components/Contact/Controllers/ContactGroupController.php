<?php
/**
* ContactGroupController.php - Controller file
*
* This file is part of the Contact component.
*-----------------------------------------------------------------------------*/

namespace App\Yantrana\Components\Contact\Controllers;

use App\Yantrana\Base\BaseController;
use App\Yantrana\Base\BaseRequest;
use App\Yantrana\Components\Contact\ContactGroupEngine;

class ContactGroupController extends BaseController
{
    /**
     * @var ContactGroupEngine - ContactGroup Engine
     */
    protected $contactGroupEngine;

    /**
     * Constructor
     *
     * @param  ContactGroupEngine  $contactGroupEngine  - ContactGroup Engine
     * @return void
     *-----------------------------------------------------------------------*/
    public function __construct(ContactGroupEngine $contactGroupEngine)
    {
        $this->contactGroupEngine = $contactGroupEngine;
    }

    /**
     * list of Group
     *
     * @return json object
     *---------------------------------------------------------------- */
    public function showGroupView()
    {
        validateVendorAccess('manage_contacts');
        // load the view
        return $this->loadView('contact.contact-group.group-list');
    }

    /**
     * list of Group
     *
     * @return json object
     *---------------------------------------------------------------- */
    public function prepareGroupList($status)
    {
        validateVendorAccess('manage_contacts');
        // respond with dataTables preparations
        return $this->contactGroupEngine->prepareGroupDataTableSource($status);
    }

    /**
     * Group process delete
     *
     * @param  mix  $contactGroupIdOrUid
     * @return json object
     *---------------------------------------------------------------- */
    public function processGroupDelete($contactGroupIdOrUid, BaseRequest $request)
    {
        validateVendorAccess('manage_contacts');
        // ask engine to process the request
        $processReaction = $this->contactGroupEngine->processGroupDelete($contactGroupIdOrUid);

        // get back to controller with engine response
        return $this->processResponse($processReaction, [], [], true);
    }

     /**
     * Group process archive
     *
     * @param  mix  $contactGroupIdOrUid
     * @return json object
     *---------------------------------------------------------------- */
    public function processGroupArchive($contactGroupIdOrUid, BaseRequest $request)
    {
        validateVendorAccess('manage_contacts');
        // ask engine to process the request
        $processReaction = $this->contactGroupEngine->processGroupArchive($contactGroupIdOrUid);

        // get back to controller with engine response
        return $this->processResponse($processReaction, [], [], true);
    }
    /**
     * Group process unarchive
     *
     * @param  mix  $contactGroupIdOrUid
     * @return json object
     *---------------------------------------------------------------- */
    public function processGroupUnarchive($contactGroupIdOrUid, BaseRequest $request)
    {
        validateVendorAccess('manage_contacts');
        // ask engine to process the request
        $processReaction = $this->contactGroupEngine->processGroupUnarchive($contactGroupIdOrUid);

        // get back to controller with engine response
        return $this->processResponse($processReaction, [], [], true);
    }

    /**
     * Group create process
     *
     * @param  object BaseRequest $request
     * @return json object
     *---------------------------------------------------------------- */
    public function processGroupCreate(BaseRequest $request)
    {
        validateVendorAccess('manage_contacts');
        // process the validation based on the provided rules
        $request->validate([
            'title' => 'required',
        ]);
        // ask engine to process the request
        $processReaction = $this->contactGroupEngine->processGroupCreate($request->all());

        // get back with response
        return $this->processResponse($processReaction);
    }

    /**
     * Group get update data
     *
     * @param  mix  $contactGroupIdOrUid
     * @return json object
     *---------------------------------------------------------------- */
    public function updateGroupData($contactGroupIdOrUid)
    {
        validateVendorAccess('manage_contacts');
        // ask engine to process the request
        $processReaction = $this->contactGroupEngine->prepareGroupUpdateData($contactGroupIdOrUid);

        // get back to controller with engine response
        return $this->processResponse($processReaction, [], [], true);
    }

    /**
     * Group process update
     *
     * @param  mix @param  mix $contactGroupIdOrUid
     * @param  object BaseRequest $request
     * @return json object
     *---------------------------------------------------------------- */
    public function processGroupUpdate(BaseRequest $request)
    {
        validateVendorAccess('manage_contacts');
        // process the validation based on the provided rules
        $request->validate([
            'contactGroupIdOrUid' => 'required',
            'title' => 'required',
        ]);
        // ask engine to process the request
        $processReaction = $this->contactGroupEngine->processGroupUpdate($request->get('contactGroupIdOrUid'), $request->all());

        // get back with response
        return $this->processResponse($processReaction, [], [], true);
    }
     /**
     * Selected Contacts group delete process
     *
     * @param  BaseRequest  $request
     *
     * @return json object
     *---------------------------------------------------------------- */
    public function selectedContactGroupsDelete(BaseRequest $request)
    {
        validateVendorAccess('manage_contacts');

        // restrict demo user
        if(isDemo() and isDemoVendorAccount()) {
            return $this->processResponse(22, [
                22 => __tr('Functionality is disabled in this demo.')
            ], [], true);
        }

        $request->validate([
            'selected_groups' => 'required|array'
        ]);
        // ask engine to process the request
        $processReaction = $this->contactGroupEngine->processSelectedContactGroupsDelete($request);

        // get back to controller with engine response
        return $this->processResponse($processReaction, [], [], true);
    }
     /**
     * Selected Contacts group arhive process
     *
     * @param  BaseRequest  $request
     *
     * @return json object
     *---------------------------------------------------------------- */
    public function selectedContactGroupsArchive(BaseRequest $request)
    {
        validateVendorAccess('manage_contacts');

        // restrict demo user
        if(isDemo() and isDemoVendorAccount()) {
            return $this->processResponse(22, [
                22 => __tr('Functionality is disabled in this demo.')
            ], [], true);
        }

        $request->validate([
            'selected_groups' => 'required|array'
        ]);
        // ask engine to process the request
        $processReaction = $this->contactGroupEngine->processSelectedContactGroupsArchive($request);

        // get back to controller with engine response
        return $this->processResponse($processReaction, [], [], true);
    }
    /**
     * Selected Contacts group unarhive process
     *
     * @param  BaseRequest  $request
     *
     * @return json object
     *---------------------------------------------------------------- */
    public function selectedContactGroupsUnarchive(BaseRequest $request)
    {
        validateVendorAccess('manage_contacts');

        // restrict demo user
        if(isDemo() and isDemoVendorAccount()) {
            return $this->processResponse(22, [
                22 => __tr('Functionality is disabled in this demo.')
            ], [], true);
        }

        $request->validate([
            'selected_groups' => 'required|array'
        ]);
        // ask engine to process the request
        $processReaction = $this->contactGroupEngine->processSelectedContactGroupsUnarchive($request);

        // get back to controller with engine response
        return $this->processResponse($processReaction, [], [], true);
    }
}
