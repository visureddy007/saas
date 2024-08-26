<?php
/**
* ContactCustomFieldController.php - Controller file
*
* This file is part of the Contact component.
*-----------------------------------------------------------------------------*/

namespace App\Yantrana\Components\Contact\Controllers;

use App\Yantrana\Base\BaseController;
use App\Yantrana\Base\BaseRequest;

use App\Yantrana\Components\Contact\ContactCustomFieldEngine;
use App\Yantrana\Support\CommonPostRequest;

class ContactCustomFieldController extends BaseController
{       /**
     * @var  ContactCustomFieldEngine $contactCustomFieldEngine - ContactCustomField Engine
     */
    protected $contactCustomFieldEngine;

    /**
      * Constructor
      *
      * @param  ContactCustomFieldEngine $contactCustomFieldEngine - ContactCustomField Engine
      *
      * @return  void
      *-----------------------------------------------------------------------*/
    public function __construct(ContactCustomFieldEngine $contactCustomFieldEngine)
    {
        $this->contactCustomFieldEngine = $contactCustomFieldEngine;
    }


    /**
      * list of CustomField
      *
      * @return  json object
      *---------------------------------------------------------------- */

    public function showCustomFieldView()
    {
        validateVendorAccess('manage_contacts');
        // load the view
        return $this->loadView('contact.contact-custom-field.custom-field-list');
    }
    /**
      * list of CustomField
      *
      * @return  json object
      *---------------------------------------------------------------- */

    public function prepareCustomFieldList()
    {
        validateVendorAccess('manage_contacts');
        // respond with dataTables preparations
        return $this->contactCustomFieldEngine->prepareCustomFieldDataTableSource();
    }

    /**
        * CustomField process delete
        *
        * @param  mix $contactCustomFieldIdOrUid
        *
        * @return  json object
        *---------------------------------------------------------------- */

    public function processCustomFieldDelete($contactCustomFieldIdOrUid, BaseRequest $request)
    {
        validateVendorAccess('manage_contacts');
        // ask engine to process the request
        $processReaction = $this->contactCustomFieldEngine->processCustomFieldDelete($contactCustomFieldIdOrUid);
        // get back to controller with engine response
        return $this->processResponse($processReaction, [], [], true);
    }

    /**
      * CustomField create process
      *
      * @param  object BaseRequest $request
      *
      * @return  json object
      *---------------------------------------------------------------- */

    public function processCustomFieldCreate(BaseRequest $request)
    {
        validateVendorAccess('manage_contacts');
        // process the validation based on the provided rules
        $request->validate([
            "input_name" => "required|alpha_dash",
            "input_type" => "required",
        ]);
        // ask engine to process the request
        $processReaction = $this->contactCustomFieldEngine->processCustomFieldCreate($request->all());
        // get back with response
        return $this->processResponse($processReaction);
    }

    /**
      * CustomField get update data
      *
      * @param  mix $contactCustomFieldIdOrUid
      *
      * @return  json object
      *---------------------------------------------------------------- */

    public function updateCustomFieldData($contactCustomFieldIdOrUid)
    {
        validateVendorAccess('manage_contacts');
        // ask engine to process the request
        $processReaction = $this->contactCustomFieldEngine->prepareCustomFieldUpdateData($contactCustomFieldIdOrUid);
        // get back to controller with engine response
        return $this->processResponse($processReaction, [], [], true);
    }

    /**
      * CustomField process update
      *
      * @param  mix @param  mix $contactCustomFieldIdOrUid
      * @param  object BaseRequest $request
      *
      * @return  json object
      *---------------------------------------------------------------- */

    public function processCustomFieldUpdate(BaseRequest $request)
    {
        validateVendorAccess('manage_contacts');
        // process the validation based on the provided rules
        $request->validate([
            'contactCustomFieldIdOrUid' => 'required',
            "input_name" => "required|alpha_dash",
            "input_type" => "required",
        ]);
        // ask engine to process the request
        $processReaction = $this->contactCustomFieldEngine->processCustomFieldUpdate($request->get('contactCustomFieldIdOrUid'), $request->all());
        // get back with response
        return $this->processResponse($processReaction, [], [], true);
    }
}
