<?php
/**
* ManualSubscriptionController.php - Controller file
*
* This file is part of the Subscription component.
*-----------------------------------------------------------------------------*/

namespace App\Yantrana\Components\Subscription\Controllers;

use App\Yantrana\Base\BaseController;
use App\Yantrana\Base\BaseRequest;

use App\Yantrana\Components\Subscription\ManualSubscriptionEngine;
use Illuminate\Validation\Rule;

class ManualSubscriptionController extends BaseController
{       /**
     * @var  ManualSubscriptionEngine $manualSubscriptionEngine - ManualSubscription Engine
     */
    protected $manualSubscriptionEngine;

    /**
      * Constructor
      *
      * @param  ManualSubscriptionEngine $manualSubscriptionEngine - ManualSubscription Engine
      *
      * @return  void
      *-----------------------------------------------------------------------*/
    public function __construct(ManualSubscriptionEngine $manualSubscriptionEngine)
    {
        $this->manualSubscriptionEngine = $manualSubscriptionEngine;
    }


    /**
      * list of ManualSubscription
      *
      * @return  json object
      *---------------------------------------------------------------- */

    public function showManualSubscriptionView()
    {
        // load the view
        return $this->loadView('subscription.manual-subscription.list');
    }
    /**
      * list of ManualSubscription
      *
      * @return  json object
      *---------------------------------------------------------------- */

    public function prepareManualSubscriptionList($vendorUid = null)
    {
        // respond with dataTables preparations
        return $this->manualSubscriptionEngine->prepareManualSubscriptionDataTableSource($vendorUid);
    }

    /**
        * ManualSubscription process delete
        *
        * @param  mix $manualSubscriptionIdOrUid
        *
        * @return  json object
        *---------------------------------------------------------------- */

    public function processManualSubscriptionDelete($manualSubscriptionIdOrUid, BaseRequest $request)
    {

        // ask engine to process the request
        $processReaction = $this->manualSubscriptionEngine->processManualSubscriptionDelete($manualSubscriptionIdOrUid);
        if($processReaction->success()) {
            return $this->processResponse(21, [], [
                'reloadPage' => true
            ], true);
        }
        // get back to controller with engine response
        return $this->processResponse($processReaction, [], [], true);
    }

    /**
      * ManualSubscription create process
      *
      * @param  object BaseRequest $request
      *
      * @return  json object
      *---------------------------------------------------------------- */

    public function processManualSubscriptionCreate(BaseRequest $request)
    {
        // process the validation based on the provided rules
        $request->validate([
            "plan" => "required",
            "ends_at" => "required|date_format:Y-m-d", // native date picker format
            "vendor_uid" => "required|uuid",
        ]);
        // ask engine to process the request
        $processReaction = $this->manualSubscriptionEngine->processManualSubscriptionCreate($request);
        // get back with response
        if($processReaction->success()) {
            return $this->processResponse(21, [], [
                'reloadPage' => true,
                'messageType' => 'success',
            ]);
        }
        return $this->processResponse($processReaction);
    }

    /**
      * ManualSubscription get update data
      *
      * @param  mix $manualSubscriptionIdOrUid
      *
      * @return  json object
      *---------------------------------------------------------------- */

    public function updateManualSubscriptionData($manualSubscriptionIdOrUid)
    {
        // ask engine to process the request
        $processReaction = $this->manualSubscriptionEngine->prepareManualSubscriptionUpdateData($manualSubscriptionIdOrUid);
        // get back to controller with engine response
        return $this->processResponse($processReaction, [], [], true);
    }

    /**
      * ManualSubscription process update
      *
      * @param  mix @param  mix $manualSubscriptionIdOrUid
      * @param  object BaseRequest $request
      *
      * @return  json object
      *---------------------------------------------------------------- */

    public function processManualSubscriptionUpdate(BaseRequest $request)
    {
        // process the validation based on the provided rules
        $request->validate([
            'manualSubscriptionIdOrUid' => 'required',
            "ends_at" => "required",
            "charges" => "required|numeric|min:0",
            "status" => [
                Rule::in(array_keys(configItem('subscription_status')))
            ],
        ]);
        // ask engine to process the request
        $processReaction = $this->manualSubscriptionEngine->processManualSubscriptionUpdate($request->get('manualSubscriptionIdOrUid'), $request->all());
        if($processReaction->success()) {
            return $this->processResponse(21, [], [
                'reloadPage' => true
            ], true);
        }
        // get back with response
        return $this->processResponse($processReaction, [], [], true);
    }

    /**
     * Get the Selected Plan details
     *
     * @return void
     */
    public function getSelectedPlanDetails(BaseRequest $request)
    {
        $processReaction = $this->manualSubscriptionEngine->prepareSelectedPlanDetails($request);
        // get back with response
        return $this->processResponse($processReaction, [], [], true);
    }
    /**
     * Prepare Manual Payment Page
     *
     * @return void
     */
    public function prepareManualPay(BaseRequest $request)
    {
        validateVendorAccess('administrative');
        $request->validate([
            'selected_plan' => 'required',
            'payment_method' => [
                'required',
                Rule::in([
                    'upi',
                    'bank_transfer',
                    'paypal',
                ])
            ],
        ]);
        $processReaction = $this->manualSubscriptionEngine->processManualPayPreparation($request);
        return $this->loadView('subscription.manual-subscription.manual-pay', $processReaction->data(), [
            'compress_page' => false
        ]);
    }
    /**
     * Prepare Manual Payment Page
     *
     * @return void
     */
    public function deleteRequest(BaseRequest $request)
    {
        validateVendorAccess('administrative');
        $processReaction = $this->manualSubscriptionEngine->processDeleteRequest($request);
        if ($processReaction->success()) {
            return $this->responseAction(
                $this->processResponse($processReaction, [], [], true),
                $this->redirectTo('subscription.read.show', [], [
                    __tr('Your request has been deleted you can start from new.'),
                    'success',
                ])
            );
        }
        // get back with response
        return $this->processResponse($processReaction, [], [], true);
    }

    /**
     * Record vendor manual payment details
     *
     * @return response
     */
    public function sendPaymentDetails(BaseRequest $request)
    {
        validateVendorAccess('administrative');
        $request->validate([
            'manual_subscription_uid' => 'required|uuid',
            'txn_reference' => 'required',
            'txn_date' => "required|date_format:Y-m-d", // native date picker format
        ]);
        $processReaction = $this->manualSubscriptionEngine->recordSentPaymentDetails($request);

        if ($processReaction->success()) {
            return $this->responseAction(
                $this->processResponse($processReaction, [], [], true),
                $this->redirectTo('subscription.read.show', [], [
                    __tr('Your details has been recorded, your plan will be activated, once we verified your payment details.'),
                    'success',
                ])
            );
        }
        // get back with response
        return $this->processResponse($processReaction, [], [], true);
    }

        /**
     * Process Capture Paypal Order
     *
     * @param   object  $request
     *
     * @return  json   object
     */
    public function capturePaypalOrder(BaseRequest $request)
    {
        $processReaction = $this->manualSubscriptionEngine->processCapturePaypalOrder($request->all());
        // check reaction code is 1
        if($processReaction->reaction_code == 1)
        {
            return $this->processResponse($processReaction, [], [], true);

        }
    }

       /**
      * payment success
      *
      * @return  json object
      *---------------------------------------------------------------- */

      public function paymentSuccess($txnReferenceId)
      {
          // load the view
          return $this->loadView('payment-success',['txnReferenceId' => $txnReferenceId]);
      }

}
