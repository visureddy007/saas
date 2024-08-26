<?php
/**
* ContactGroupEngine.php - Main component file
*
* This file is part of the Contact component.
*-----------------------------------------------------------------------------*/

namespace App\Yantrana\Components\Contact;

use App\Yantrana\Base\BaseEngine;
use App\Yantrana\Components\Contact\Interfaces\ContactGroupEngineInterface;
use App\Yantrana\Components\Contact\Repositories\ContactGroupRepository;

class ContactGroupEngine extends BaseEngine implements ContactGroupEngineInterface
{
    /**
     * @var ContactGroupRepository - ContactGroup Repository
     */
    protected $contactGroupRepository;

    /**
     * Constructor
     *
     * @param  ContactGroupRepository  $contactGroupRepository  - ContactGroup Repository
     * @return void
     *-----------------------------------------------------------------------*/
    public function __construct(ContactGroupRepository $contactGroupRepository)
    {
        $this->contactGroupRepository = $contactGroupRepository;
    }

    /**
     * Group datatable source
     *
     * @return array
     *---------------------------------------------------------------- */
    public function prepareGroupDataTableSource($status)
    {
        $groupCollection = $this->contactGroupRepository->fetchGroupDataTableSource($status);
        // required columns for DataTables
        $requireColumns = [
            '_id',
            '_uid',
            'title',
            'description',
            'status',
        ];

        // prepare data for the DataTables
        return $this->dataTableResponse($groupCollection, $requireColumns);
    }

    /**
     * Group delete process
     *
     * @param  mix  $contactGroupIdOrUid
     * @return array
     *---------------------------------------------------------------- */
    public function processGroupDelete($contactGroupIdOrUid)
    {
        // fetch the record
        $group = $this->contactGroupRepository->fetchIt($contactGroupIdOrUid);
        // check if the record found
        if (__isEmpty($group)) {
            // if not found
            return $this->engineResponse(18, null, __tr('Group not found'));
        }
        // ask to delete the record
        if ($this->contactGroupRepository->deleteIt($group)) {
            // if successful
            return $this->engineSuccessResponse([], __tr('Group deleted successfully'));
        }

        // if failed to delete
        return $this->engineFailedResponse([], __tr('Failed to delete Group'));
    }
    /**
     * Group archive process
     *
     * @param  mix  $contactGroupIdOrUid
     * @return array
     *---------------------------------------------------------------- */
    public function processGroupArchive($contactGroupIdOrUid)
    {
        // fetch the record
        $group = $this->contactGroupRepository->fetchIt($contactGroupIdOrUid);
        // check if the record found
        if (__isEmpty($group)) {
            // if not found
            return $this->engineResponse(18, null, __tr('Group not found'));
        }
         // Prepare Update Package data
         $updateData = [
            'status' => 5,
        ];
        //Check if package archive
        if ($this->contactGroupRepository->updateIt($group,$updateData)) {
            return $this->engineSuccessResponse([], __tr('Group Archived successfully'));
        }

        // if failed to archive
        return $this->engineFailedResponse([], __tr('Failed to Archive Group'));
    }
 /**
     * Group archive process
     *
     * @param  mix  $contactGroupIdOrUid
     * @return array
     *---------------------------------------------------------------- */
    public function processGroupUnarchive($contactGroupIdOrUid)
    {
        // fetch the record
        $group = $this->contactGroupRepository->fetchIt($contactGroupIdOrUid);
        // check if the record found
        if (__isEmpty($group)) {
            // if not found
            return $this->engineResponse(18, null, __tr('Group not found'));
        }
         // Prepare Update Package data
         $updateData = [
            'status' => 1,
        ];
        //Check if package unarchive
        if ($this->contactGroupRepository->updateIt($group,$updateData)) {
            return $this->engineSuccessResponse([], __tr('Group Unarchived successfully'));
        }

        // if failed to unarchive
        return $this->engineFailedResponse([], __tr('Failed to Unarchive Group'));
    }

    /**
     * Group create
     *
     * @param  array  $inputData
     * @return array
     *---------------------------------------------------------------- */
    public function processGroupCreate($inputData)
    {
        // ask to add record
        if ($this->contactGroupRepository
            ->storeGroup($inputData)) {

            return $this->engineSuccessResponse([], __tr('Group added.'));
        }

        return $this->engineFailedResponse([], __tr('Group not added.'));
    }

    /**
     * Group prepare update data
     *
     * @param  mix  $contactGroupIdOrUid
     * @return array
     *---------------------------------------------------------------- */
    public function prepareGroupUpdateData($contactGroupIdOrUid)
    {
        $group = $this->contactGroupRepository->fetchIt($contactGroupIdOrUid);

        // Check if $group not exist then throw not found
        // exception
        if (__isEmpty($group)) {
            return $this->engineResponse(18, null, __tr('Group not found.'));
        }

        return $this->engineSuccessResponse($group->toArray());
    }

    /**
     * Group process update
     *
     * @param  mixed  $contactGroupIdOrUid
     * @param  array  $inputData
     * @return array
     *---------------------------------------------------------------- */
    public function processGroupUpdate($contactGroupIdOrUid, $inputData)
    {
        $group = $this->contactGroupRepository->fetchIt($contactGroupIdOrUid);

        // Check if $group not exist then throw not found
        // exception
        if (__isEmpty($group)) {
            return $this->engineResponse(18, null, __tr('Group not found.'));
        }

        $updateData = [
            'title' => $inputData['title'],
            'description' => $inputData['description'],

        ];

        // Check if Group updated
        if ($this->contactGroupRepository->updateIt($group, $updateData)) {

            return $this->engineSuccessResponse([], __tr('Group updated.'));
        }

        return $this->engineResponse(14, null, __tr('Group not updated.'));
    }
     /**
     * Contact group delete process
     *
     * @param  BaseRequest  $request
     *
     * @return array
     *---------------------------------------------------------------- */
    public function processSelectedContactGroupsDelete($request)
    {
        $selectedContactGroupsUids = $request->get('selected_groups');

        $message = '';

        if(empty($selectedContactGroupsUids)) {
            return $this->engineFailedResponse([], __tr('Nothing to delete'));
        }
        // ask to delete the record
        if ($this->contactGroupRepository->deleteSelectedContactGroups($selectedContactGroupsUids)) {
            // if successful
            return $this->engineSuccessResponse([
                'reloadDatatableId' => '#lwGroupList'
            ], __tr('Groups deleted successfully.') . $message);
        }
        // if failed to delete
        return $this->engineFailedResponse([], __tr('Failed to delete Groups'));
    }

     /**
     * Contact group archive process
     *
     * @param  BaseRequest  $request
     *
     * @return array
     *---------------------------------------------------------------- */
    public function processSelectedContactGroupsArchive($request)
    {
        $selectedContactGroupsUids = $request->get('selected_groups');
        $contactGroups = $this->contactGroupRepository->fetchItAll($request->get('selected_groups'), [], '_uid');

        $message = '';
        if(empty($selectedContactGroupsUids)) {
            return $this->engineFailedResponse([], __tr('Nothing to archive'));
        }
        $contactGroupsToUpdate = [];
        // Prepare Update Package data
        foreach ($contactGroups as $newGroup) {
            $contactGroupsToUpdate[] = [
                '_uid' => $newGroup['_uid'],
                'title'=> $newGroup['title'],
                'status' => 5,
            ];
        }
        //process to archived groups
        if(!empty($contactGroupsToUpdate)) {
            $this->contactGroupRepository->bunchInsertOrUpdate($contactGroupsToUpdate, '_uid');
            return $this->engineSuccessResponse([
                'reloadDatatableId' => '#lwGroupList'
            ], __tr('Groups archived successfully.'). $message);
        }
        // if failed to delete
        return $this->engineFailedResponse([], __tr('Failed to archive Groups'));
    }
    /**
     * Contact group unarchive process
     *
     * @param  BaseRequest  $request
     *
     * @return array
     *---------------------------------------------------------------- */
    public function processSelectedContactGroupsUnarchive($request)
    {
        $selectedContactGroupsUids = $request->get('selected_groups');
        $contactGroups = $this->contactGroupRepository->fetchItAll($request->get('selected_groups'), [], '_uid');
        $message = '';
        if(empty($selectedContactGroupsUids)) {
            return $this->engineFailedResponse([], __tr('Nothing to unarchive'));
        }
        $contactGroupsToUpdate = [];
       // Prepare Update Package data
       foreach ($contactGroups as $newGroup) {
        $contactGroupsToUpdate[] = [
            '_uid' => $newGroup['_uid'],
            'title'=> $newGroup['title'],
            'status' => 1,
        ];
    }
         
        //process to archived groups
        if(!empty($contactGroupsToUpdate)) {
            $this->contactGroupRepository->bunchInsertOrUpdate($contactGroupsToUpdate, '_uid');
            return $this->engineSuccessResponse([
                'reloadDatatableId' => '#lwGroupList'
            ], __tr('Groups unarchived successfully.'). $message);
        }
        // if failed to delete
        return $this->engineFailedResponse([], __tr('Failed to unarchive Groups'));
    }
}
