<?php

/**
 * SubscriptionController.php - Controller file
 *
 * This file is part of the Subscription component.
 *-----------------------------------------------------------------------------*/

namespace App\Yantrana\Components\Subscription\Controllers;

use App\Yantrana\Base\BaseController;
use App\Yantrana\Base\BaseRequest;
use App\Yantrana\Components\Subscription\SubscriptionEngine;
use Illuminate\Support\Facades\Redirect;

class SubscriptionController extends BaseController
{
    /**
     * @var SubscriptionEngine - Subscription Engine
     */
    protected $subscriptionEngine;

    /**
     * Constructor
     *
     * @param  SubscriptionEngine  $subscriptionEngine  - Subscription Engine
     * @return void
     *-----------------------------------------------------------------------*/
    public function __construct(SubscriptionEngine $subscriptionEngine)
    {
        $this->subscriptionEngine = $subscriptionEngine;
    }

    /**
     * Show the subscription page
     *
     * @return view
     */
    public function show()
    {
        validateVendorAccess('administrative');
        // prepare data
        $initialData = $this->subscriptionEngine->prepareData();

        return $this->loadView('vendor.subscription', $initialData);
    }

    /**
     * Cancel subscription
     *
     * @return response
     */
    public function cancel()
    {
        validateVendorAccess('administrative');
        $processReaction = $this->subscriptionEngine->processCancellation();
        // get back to controller with engine response

        if ($processReaction['reaction_code'] === 1) {
            return $this->responseAction(
                $this->processResponse($processReaction, [], [], false),
                $this->redirectTo('subscription.read.show')
            );
        }

        return $this->processResponse($processReaction, [], [], false);
    }
    /**
     * Cancel & Discard subscription by super-admin
     *
     * @return response
     */
    public function cancelAndDiscard($vendorUid)
    {
        $processReaction = $this->subscriptionEngine->processCancellation($vendorUid, true);
        // get back to controller with engine response
        if ($processReaction['reaction_code'] === 1) {
            return $this->processResponse(21, [], [
                'reloadPage' => true
            ], false);
        }

        return $this->processResponse($processReaction, [], [], false);
    }

    /**
     * Resume Subscription
     *
     * @return response
     */
    public function resume()
    {
        validateVendorAccess('administrative');
        // ask engine to process the request
        $processReaction = $this->subscriptionEngine->processResume();
        // get back to controller with engine response
        if ($processReaction['reaction_code'] === 1) {
            return $this->responseAction(
                $this->processResponse($processReaction, [], [], false),
                $this->redirectTo('subscription.read.show')
            );
        }

        return $this->processResponse($processReaction, [], [], false);
    }

    /**
     * Resume Subscription
     *
     * @return response
     */
    public function changePlan(BaseRequest $request)
    {
        validateVendorAccess('administrative');
        $request->validate([
            'plan' => 'required',
        ]);
        // ask engine to process the request
        $processReaction = $this->subscriptionEngine->processChangePlan($request);
        if ($processReaction['reaction_code'] === 1) {
            return $this->responseAction(
                $this->processResponse($processReaction, [], [], false),
                $this->redirectTo('subscription.read.show')
            );
        }

        return $this->processResponse($processReaction, [], [], true);
    }

    /**
     * Billing Portal Redirect
     *
     * @return redirect
     */
    public function billingPortal()
    {
        validateVendorAccess('administrative');
        return $this->subscriptionEngine->processRedirectToBillingPortal();
    }

    /**
     * Download Invoice
     *
     * @param  int|string  $invoiceId
     * @return download
     */
    public function downloadInvoice($invoiceId)
    {
        validateVendorAccess('administrative');
        // ask engine to process the request
        return $this->subscriptionEngine->processDownloadInvoice($invoiceId);
    }

    /**
     * Create New Subscription
     *
     * @return redirect
     */
    public function create(BaseRequest $request)
    {
        validateVendorAccess('administrative');
        // ask engine to process the request
        return $this->subscriptionEngine->processCreate($request);
    }

    public function subscriptionList()
    {
        return $this->subscriptionEngine->prepareSubscriptionDataTableList();
    }

    /**
     * Delete all the subscription entries
     *
     * @return json
     */
    public function deleteSubscriptionEntries()
    {
        $processReaction = $this->subscriptionEngine->processDeleteSubscriptionEntries();
        // get back to controller with engine response
        return $this->processResponse($processReaction, [], [], false);
    }
}
