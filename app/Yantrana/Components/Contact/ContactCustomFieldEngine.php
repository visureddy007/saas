<?php
/**
* ContactCustomFieldEngine.php - Main component file
*
* This file is part of the Contact component.
*-----------------------------------------------------------------------------*/

namespace App\Yantrana\Components\Contact;

use App\Yantrana\Base\BaseEngine;
use App\Yantrana\Components\Contact\Repositories\ContactCustomFieldRepository;
use App\Yantrana\Components\Contact\Interfaces\ContactCustomFieldEngineInterface;

class ContactCustomFieldEngine extends BaseEngine implements ContactCustomFieldEngineInterface
{
    /**
     * @var  ContactCustomFieldRepository $contactCustomFieldRepository - ContactCustomField Repository
     */
    protected $contactCustomFieldRepository;

    /**
      * Constructor
      *
      * @param  ContactCustomFieldRepository $contactCustomFieldRepository - ContactCustomField Repository
      *
      * @return  void
      *-----------------------------------------------------------------------*/

    public function __construct(ContactCustomFieldRepository $contactCustomFieldRepository)
    {
        $this->contactCustomFieldRepository = $contactCustomFieldRepository;
    }


    /**
      * CustomField datatable source
      *
      * @return  array
      *---------------------------------------------------------------- */
    public function prepareCustomFieldDataTableSource()
    {
        $customFieldCollection = $this->contactCustomFieldRepository->fetchCustomFieldDataTableSource();
        // required columns for DataTables
        $requireColumns = [
            '_id',
            '_uid',
            'input_name',
            'input_type'
        ];
        // prepare data for the DataTables
        return $this->dataTableResponse($customFieldCollection, $requireColumns);
    }


    /**
      * CustomField delete process
      *
      * @param  mix $contactCustomFieldIdOrUid
      *
      * @return  array
      *---------------------------------------------------------------- */

    public function processCustomFieldDelete($contactCustomFieldIdOrUid)
    {
        // fetch the record
        $customField = $this->contactCustomFieldRepository->fetchIt($contactCustomFieldIdOrUid);
        // check if the record found
        if (__isEmpty($customField)) {
            // if not found
            return $this->engineResponse(18, null, __tr('Contact Custom Field not found'));
        }
        // ask to delete the record
        if ($this->contactCustomFieldRepository->deleteIt($customField)) {
            // if successful
            return $this->engineResponse(1, null, __tr('Contact Custom Field deleted successfully'));
        }
        // if failed to delete
        return $this->engineResponse(2, null, __tr('Failed to delete CustomField'));
    }

    /**
      * CustomField create
      *
      * @param  array $inputData
      *
      * @return  array
      *---------------------------------------------------------------- */

    public function processCustomFieldCreate($inputData)
    {
        $vendorId = getVendorId();
        // check the feature limit
        $vendorPlanDetails = vendorPlanDetails('contact_custom_fields', $this->contactCustomFieldRepository->countIt([
            'vendors__id' => $vendorId
        ]), $vendorId);
        if (!$vendorPlanDetails['is_limit_available']) {
            return $this->engineResponse(22, null, $vendorPlanDetails['message']);
        }
        // ask to add record
        if ($this->contactCustomFieldRepository
                                ->storeCustomField($inputData)) {

            return $this->engineResponse(1, null, __tr('Contact Custom Field added.'));
        }

        return $this->engineResponse(2, null, __tr('Contact Custom Field not added.'));
    }

    /**
      * CustomField prepare update data
      *
      * @param  mix $contactCustomFieldIdOrUid
      *
      * @return  array
      *---------------------------------------------------------------- */

    public function prepareCustomFieldUpdateData($contactCustomFieldIdOrUid)
    {
        $customField = $this->contactCustomFieldRepository->fetchIt($contactCustomFieldIdOrUid);

        // Check if $customField not exist then throw not found
        // exception
        if (__isEmpty($customField)) {
            return $this->engineResponse(18, null, __tr('Contact Custom Field not found.'));
        }

        return $this->engineResponse(1, $customField->toArray());
    }

    /**
      * CustomField process update
      *
      * @param  mixed $contactCustomFieldIdOrUid
      * @param  array $inputData
      *
      * @return  array
      *---------------------------------------------------------------- */

    public function processCustomFieldUpdate($contactCustomFieldIdOrUid, $inputData)
    {
        $customField = $this->contactCustomFieldRepository->fetchIt($contactCustomFieldIdOrUid);

        // Check if $customField not exist then throw not found
        // exception
        if (__isEmpty($customField)) {
            return $this->engineResponse(18, null, __tr('Contact Custom Field not found.'));
        }

        $updateData = [
            'input_name' => $inputData['input_name'],
            'input_type' => $inputData['input_type']

        ];

        // Check if CustomField updated
        if ($this->contactCustomFieldRepository->updateIt($customField, $updateData)) {

            return $this->engineResponse(1, null, __tr('Contact Custom Field updated.'));
        }

        return $this->engineResponse(14, null, __tr('Contact Custom Field not updated.'));
    }
}
