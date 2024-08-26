<?php
/**
* ManualSubscriptionEngine.php - Main component file
*
* This file is part of the Subscription component.
*-----------------------------------------------------------------------------*/

namespace App\Yantrana\Components\Subscription;

use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use App\Yantrana\Base\BaseEngine;
use App\Yantrana\Base\BaseMailer;
use Illuminate\Support\Facades\URL;
use App\Yantrana\Components\Dashboard\DashboardEngine;
use App\Yantrana\Components\Auth\Repositories\AuthRepository;
use App\Yantrana\Components\Vendor\Repositories\VendorRepository;
use App\Yantrana\Components\Subscription\PaymentEngines\PaypalEngine;
use App\Yantrana\Components\Subscription\Repositories\ManualSubscriptionRepository;
use App\Yantrana\Components\Subscription\Interfaces\ManualSubscriptionEngineInterface;

class ManualSubscriptionEngine extends BaseEngine implements ManualSubscriptionEngineInterface
{
    /**
    * @var AuthRepository - Auth Repository
    */
    protected $authRepository;
    /**
     * @var Base Mailer
     */
    protected $baseMailer;
    /**
     * @var  ManualSubscriptionRepository $manualSubscriptionRepository - ManualSubscription Repository
     */
    protected $manualSubscriptionRepository;

    /**
     * @var  PaypalEngine $paypalEngine - PaypalEngine Engine
     */
    protected $paypalEngine;

    /**
     * @var VendorRepository - Vendor Repository
     */
    protected $vendorRepository;

    /**
     * @var DashboardEngine - Dashboard Engine
     */
    protected $dashboardEngine;

    /**
      * Constructor
      *
      * @param  ManualSubscriptionRepository $manualSubscriptionRepository - ManualSubscription Repository
      * @param  VendorRepository $vendorRepository - Vendor Repository
      *
      * @return  void
      *-----------------------------------------------------------------------*/

    public function __construct(
        ManualSubscriptionRepository $manualSubscriptionRepository,
        VendorRepository $vendorRepository,
        PaypalEngine $paypalEngine,
        AuthRepository $authRepository,
        BaseMailer $baseMailer,
        DashboardEngine $dashboardEngine
    ) {

        $this->manualSubscriptionRepository = $manualSubscriptionRepository;
        $this->vendorRepository = $vendorRepository;
        $this->paypalEngine = $paypalEngine;
        $this->authRepository = $authRepository;
        $this->baseMailer = $baseMailer;
        $this->dashboardEngine = $dashboardEngine;
    }


    /**
      * ManualSubscription datatable source
      *
      * @return  array
      *---------------------------------------------------------------- */
    public function prepareManualSubscriptionDataTableSource($vendorUid = null)
    {
        $vendorId = null;
        if($vendorUid) {
            $vendor = $this->vendorRepository->fetchIt($vendorUid);
            abortIf(__isEmpty($vendor));
            $vendorId = $vendor->_id;
        }
        $manualSubscriptionCollection = $this->manualSubscriptionRepository->fetchManualSubscriptionDataTableSource($vendorId);
        $subscriptionPlans = getPaidPlans();
        $subscriptionStatus = configItem('subscription_status');
        // required columns for DataTables
        $requireColumns = [
            '_id',
            '_uid',
            'plan_id',
            'charges_frequency',
            'vendor_title' => function ($rowData) {
                return $rowData['vendor']['title'] ?? '';
            },
            'vendor_uid' => function ($rowData) {
                return $rowData['vendor']['_uid'] ?? '';
            },
            'options' => function ($rowData) {
                return [
                    'is_expired' => $rowData['ends_at'] < now()
                ];
            },
            'charges' => function ($rowData) {
                return formatAmount($rowData['charges'], true, true);
            },
            'plan_id' => function ($rowData) use (&$subscriptionPlans) {
                return Arr::get($subscriptionPlans, $rowData['plan_id'] . '.title');
            },
            'created_at' => function ($rowData) {
                return formatDate($rowData['created_at']);
            },
            'ends_at' => function ($rowData) {
                return formatDate($rowData['ends_at']);
            },
            'status' => function ($rowData) use ($subscriptionStatus) {
                return Arr::get($subscriptionStatus, $rowData['status']);
            },
            'remarks',
        ];
        // prepare data for the DataTables
        return $this->dataTableResponse($manualSubscriptionCollection, $requireColumns);
    }


    /**
      * ManualSubscription delete process
      *
      * @param  mix $manualSubscriptionIdOrUid
      *
      * @return  EngineResponse
      *---------------------------------------------------------------- */

    public function processManualSubscriptionDelete($manualSubscriptionIdOrUid)
    {
        // fetch the record
        $manualSubscription = $this->manualSubscriptionRepository->fetchIt($manualSubscriptionIdOrUid);
        // check if the record found
        if (__isEmpty($manualSubscription)) {
            // if not found
            return $this->engineResponse(18, null, __tr('Manual Subscription not found'));
        }
        // ask to delete the record
        if ($this->manualSubscriptionRepository->deleteIt($manualSubscription)) {
            // if successful
            return $this->engineResponse(1, null, __tr('Manual Subscription deleted successfully'));
        }
        // if failed to delete
        return $this->engineResponse(2, null, __tr('Failed to delete ManualSubscription'));
    }

    /**
      * ManualSubscription create
      *
      * @param  BaseRequest $request
      *
      * @return  EngineResponse
      *---------------------------------------------------------------- */

    public function processManualSubscriptionCreate($request)
    {
        $vendor = $this->vendorRepository->fetchIt($request->vendor_uid);
        $planRequest = explode('___', $request->plan);
        abortIf(__isEmpty($vendor) or (!isset($planRequest[0]) or !isset($planRequest[1])));
        // ask to add record
        $engineResponse = $this->manualSubscriptionRepository->processTransaction(function () use (&$planRequest, &$vendor, &$request) {
            $subscriptionPlans = getPaidPlans();
            $planId = $planRequest[0];
            $planFrequencyKey = $planRequest[1];
            $getPlanDetails = Arr::get($subscriptionPlans, $planId);
            $planCharge = Arr::get($getPlanDetails, 'charges.'.$planRequest[1].'.charge');
            // set the existing subscription as cancelled
            $this->manualSubscriptionRepository->updateItAll([
                'status' => 'active',
                'vendors__id' => $vendor->_id,
            ], [
                'status' => 'cancelled',
            ]);
            if ($this->manualSubscriptionRepository->storeIt([
                'plan_id' => $planId,
                'charges_frequency' => $planFrequencyKey,
                'charges' => $planCharge,
                'remarks' => $request->remarks,
                'ends_at' => $request->ends_at,
                'status' => 'active',
                'vendors__id' => $vendor->_id,
            ])) {
                return $this->manualSubscriptionRepository->transactionResponse(1, [], __tr('Manual Subscription added.'));
            }
            return $this->manualSubscriptionRepository->transactionResponse(2, [], __tr('Manual Subscription not added.'));
        });
        return $this->engineResponse($engineResponse);
    }

    /**
      * ManualSubscription prepare update data
      *
      * @param  mix $manualSubscriptionIdOrUid
      *
      * @return  array
      *---------------------------------------------------------------- */

    public function prepareManualSubscriptionUpdateData($manualSubscriptionIdOrUid)
    {
        $manualSubscription = $this->manualSubscriptionRepository->fetchIt($manualSubscriptionIdOrUid);

        // Check if $manualSubscription not exist then throw not found
        // exception
        if (__isEmpty($manualSubscription)) {
            return $this->engineResponse(18, null, __tr('Manual Subscription not found.'));
        }
        $manualSubscriptionArray = $manualSubscription->toArray();
        $manualSubscriptionArray['transactionDate'] = '';
        if($manualSubscriptionArray['__data']['manual_txn_details']['txn_date'] ?? null)
        {
        $manualSubscriptionArray['transactionDate'] = formatDateTime($manualSubscriptionArray['__data']['manual_txn_details']['txn_date']);
        }
        $manualSubscriptionArray['ends_at'] = Carbon::parse($manualSubscriptionArray['ends_at'])->format('Y-m-d');
        return $this->engineResponse(1, $manualSubscriptionArray);
    }

    /**
      * ManualSubscription process update
      *
      * @param  mixed $manualSubscriptionIdOrUid
      * @param  array $inputData
      *
      * @return  EngineResponse
      *---------------------------------------------------------------- */

    public function processManualSubscriptionUpdate($manualSubscriptionIdOrUid, $inputData)
    {
        $manualSubscription = $this->manualSubscriptionRepository->fetchIt($manualSubscriptionIdOrUid);

        // Check if $manualSubscription not exist then throw not found
        // exception
        if (__isEmpty($manualSubscription)) {
            return $this->engineResponse(18, null, __tr('Manual Subscription not found.'));
        }

        $updateData = [
            'ends_at' => $inputData['ends_at'],
            'status' => $inputData['status'],
            'remarks' => $inputData['remarks'],
            'charges' => $inputData['charges'],
        ];

        // Check if ManualSubscription updated
        if ($this->manualSubscriptionRepository->updateIt($manualSubscription, $updateData)) {
            return $this->engineResponse(1, null, __tr('Manual Subscription updated.'));
        }
        return $this->engineResponse(14, null, __tr('Manual Subscription not updated.'));
    }

    public function prepareSelectedPlanDetails($request)
    {
        $planRequest = explode('___', $request->selected_plan);
        abortIf(!isset($planRequest[0]) or !isset($planRequest[1]), null, __tr('Invalid Plan or Frequency'));
        $planFrequencyKey = $planRequest[1];
        $planDetails = getPaidPlans($planRequest[0]);
        $planCharges = formatAmount($planDetails['charges'][$planFrequencyKey]['charge'], true, true);
        $endsAt = $planFrequencyKey == 'monthly' ? now()->addMonth() : now()->addYear();
        updateClientModels([
            'calculated_ends_at' => $endsAt->format('Y-m-d')
        ]);
        return $this->engineSuccessResponse();
    }

    /**
    * Process Manual Pay and prepaid
    *
    * @return  json   object
    */
    public function processManualPayPreparation($request)
    {
        $planRequest = explode('___', $request->selected_plan);
        abortIf(!isset($planRequest[0]) or !isset($planRequest[1]), null, __tr('Invalid Plan or Frequency'));
        $planFrequencyKey = $planRequest[1];
        $planDetails = getPaidPlans($planRequest[0]);
        abortIf(!$planDetails, null, __tr('Invalid Plan or Frequency'));
        $planCharges = $planDetails['charges'][$planFrequencyKey]['charge'];
        $planChargesFormatted = formatAmount($planCharges, true, true);
        $planFrequencyTitle = $planDetails['charges'][$planFrequencyKey]['title'];
        $endsAt = now();
        $daysForCalculation = 0;
        switch ($planFrequencyKey) {
            case 'monthly':
                $endsAt = now()->addMonth();
                $daysForCalculation = now()->daysInMonth;
                break;
            case 'yearly':
                $endsAt = now()->addYear();
                $daysForCalculation = now()->daysInYear;
                break;
        }
        $vendorId = getVendorId();
        $existingRequestExist = false;
        $preparePlanDetails = [
            'plan_id' => $planDetails['id'],
            'plan_features' => $planDetails['features'],
            'plan_charges' => $planCharges,
            'plan_frequency' => $planFrequencyKey,
            // may prorated based on current plan etc
            'prorated_remaining_balance_days' => 0,
            'prorated_remaining_balance_amount' => 0,
            'existing_plan_days_adjusted' => 0
        ];
        // get the current subscription
        $currentActiveSubscription = $this->manualSubscriptionRepository->fetchIt([
            'vendors__id' => $vendorId,
            'status' => 'active',
        ]);
        $existingPlanDaysAdjustments = false;
        $checkPlanUsages = $this->dashboardEngine->checkPlanUsages($planDetails, $vendorId);
        if($checkPlanUsages) {
            return $this->engineFailedResponse([
                'show_message' => true,
                'planDetails' => $planDetails,
                'existingRequestExist' => $existingRequestExist,
                'checkPlanUsages' => $checkPlanUsages,
            ], 'overused features'
        );
        }
        // prorated adjustments
        if(!__isEmpty($currentActiveSubscription) and $planCharges and $currentActiveSubscription->charges and $currentActiveSubscription->ends_at) {
            $existingCreatedAt = Carbon::parse($currentActiveSubscription->created_at);
            $existingEndsAt = Carbon::parse($currentActiveSubscription->ends_at);
            $existingPlanCharges = $currentActiveSubscription->charges;
            // Calculate the total number of days in the billing period (from created_at to ends_at)
            $existingTotalDays = $existingCreatedAt->diffInDays($existingEndsAt);
            // Calculate the remaining days from today until ends_at
            $remainingDays = Carbon::now()->diffInDays($existingEndsAt, false);
            // Calculate daily charge
            $dailyCharge = 0;
            $proratedBalance = 0;
            if($existingTotalDays) {
                $dailyCharge = $existingPlanCharges / $existingTotalDays;
                // Calculate prorated balance
                $proratedBalance = round($dailyCharge * $remainingDays, 2);
            }
            if($proratedBalance > 0) {
                $perDaysValueForNewPlan = $planCharges / $daysForCalculation;
                $daysForRemainingAmount = floor($proratedBalance / $perDaysValueForNewPlan);
                $endsAt = $endsAt->addDays($daysForRemainingAmount);
                $preparePlanDetails = array_merge($preparePlanDetails, [
                    // may prorated charges based on current plan etc
                    'prorated_remaining_balance_days' => $remainingDays,
                    'prorated_remaining_balance_amount' => $proratedBalance,
                    'existing_plan_days_adjusted' => 1,
                ]);
                $existingPlanDaysAdjustments = true;
            }
        }

        // existing pending request
        $subscriptionRequestRecord = $this->manualSubscriptionRepository->fetchIt([
            'vendors__id' => $vendorId,
            'status' => 'initiated',
        ]);

        if(!__isEmpty($subscriptionRequestRecord)) {
            $this->manualSubscriptionRepository->deleteIt([
                'vendors__id' => $vendorId,
                'status' => 'initiated',
            ]);
            $subscriptionRequestRecord = null;
        }

        if(__isEmpty($subscriptionRequestRecord)) {
            $subscriptionRequestRecord = $this->manualSubscriptionRepository->fetchIt([
                'vendors__id' => $vendorId,
                'status' => 'pending',
            ]);
        }
        if(__isEmpty($subscriptionRequestRecord)) {
            $subscriptionRequestRecord = $this->manualSubscriptionRepository->storeIt([
                'plan_id' => $planDetails['id'],
                'charges_frequency' => $planFrequencyKey,
                'charges' => $planCharges,
                'remarks' => '',
                'ends_at' => $endsAt,
                'status' => 'initiated',
                'vendors__id' => $vendorId,
                '__data' => [
                    'prepared_plan_details' => $preparePlanDetails,
                    'manual_txn_details' => [
                        'selected_payment_method' => $request->payment_method
                    ]
                ],
            ]);
            abortIf(!$subscriptionRequestRecord, null, __tr('Failed to create subscription'));
        } else {
            $existingRequestExist = true;
            $planCharges = $subscriptionRequestRecord->charges;
            $planDetails['id'] = $subscriptionRequestRecord->plan_id;
            $planDetails['charges'][$planFrequencyKey]['charge'] = $planCharges;
            $planChargesFormatted = formatAmount($subscriptionRequestRecord->charges, true, true);
        }
        $upiId = getAppSettings('payment_upi_address');
        $payeeName = getAppSettings('name');
        $transactionRef = 'txn_ref_' . $subscriptionRequestRecord->_id;
        $transactionNote = "$payeeName-{$planDetails['id']}-$planFrequencyTitle-Subscription-" . $subscriptionRequestRecord->_id;
        $upiPaymentLink = createUpiLink($upiId, $payeeName, $planCharges, $transactionRef, $transactionNote);
        $paypalResponse = '';
        // check payment method is paypal
        if($request->payment_method == 'paypal') {
            //paypal create order response
            $paypalResponse = $this->paypalEngine->paypalOrderCreate($planCharges, $subscriptionRequestRecord->_uid);
            if ($paypalResponse->failed()) {
                return $this->engineFailedResponse([
                        'show_message' => true
                    ], $paypalResponse->message()
                );
            }
        }
        return $this->engineSuccessResponse([
            'subscriptionRequestRecord' => $subscriptionRequestRecord,
            'existingRequestExist' => $existingRequestExist,
            'expiryDate' => $endsAt->format('Y-m-d'),
            'expiryDateFormatted' => formatDate($endsAt),
            'planChargesFormatted' => $planChargesFormatted,
            'existingPlanDaysAdjustments' => $existingPlanDaysAdjustments,
            'planDetails' => $planDetails,
            'planFrequencyTitle' => $planFrequencyTitle,
            'planCharges' => $planCharges,
            'paypalResponse' => $paypalResponse,
            'upiPaymentQRImageUrl' => route('vendor.generate.upi_payment_request', [
                'url' => base64_encode($upiPaymentLink)
            ]),
            'checkPlanUsages' => null,
        ]);
    }

    /**
    * Process record PaymentDetails
    *
    * @return  json   object
    */
    public function recordSentPaymentDetails($request)
    {
        $vendorId = getVendorId();
        //get vendor details
        $vendorData = $this->vendorRepository->fetchIt($vendorId);
        $vendorUserData = $this->authRepository->fetchIt([
            'vendors__id' =>  $vendorId
        ]);
        //current time
        $now = formatDate(Carbon::now());
        $subscriptionRequestRecord = $this->manualSubscriptionRepository->fetchIt([
            'vendors__id' => $vendorId,
            'status' => 'initiated',
            '_uid' => $request['manual_subscription_uid'],
        ]);
        if (__isEmpty($subscriptionRequestRecord)) {
            return $this->engineFailedResponse([], __tr('Invalid Subscription Request'));
        }

        $isTxnReferenceExists = $this->manualSubscriptionRepository->countIt([
            'vendors__id' => $vendorId,
            '__data->manual_txn_details->txn_reference' => $request['txn_reference'],
        ]);
        if($isTxnReferenceExists) {
            return $this->engineFailedResponse([], __tr('Transaction already been processed'));
        }

        // check payment method is paypal
        if($subscriptionRequestRecord->__data['manual_txn_details']['selected_payment_method'] == 'paypal') {
            // deactivate existing active plans
            $this->manualSubscriptionRepository->updateItAll([
                'status' => 'active',
                'vendors__id' => $vendorId,
            ], [
                'status' => 'cancelled',
            ]);
            //update subscription request record
            if($this->manualSubscriptionRepository->updateIt($subscriptionRequestRecord, [
                'status' => 'active',
                '__data' => [
                    'manual_txn_details' => [
                        'txn_reference' => $request['txn_reference'],
                        'txn_date' => now(),
                    ]
                ]
            ])) {
                return $this->engineSuccessResponse([
                    'txn_reference' => $request['txn_reference'],
                    'redirectRoute' => route('payment.success.page', ['txnId' => $request['txn_reference']]),
                ]);
            }
        }
        // if manual subscription request
        else {
            //fetch plan details
            $planStructure = getPaidPlans($subscriptionRequestRecord['plan_id']);
            //subscription mail data
            $emailData = [
               'adminName' => $vendorUserData['first_name'].' '.$vendorUserData['last_name'],
               'userName' => $vendorData['title'],
               'senderEmail' => $vendorUserData['email'],
               'toEmail' => getAppSettings('contact_email'),
               'subject' => "Manual subscription request mail",
               'requested_at' => $now,
               'planTitle' => $planStructure['title'],
               'planCharges' => $subscriptionRequestRecord['charges'],
               'planFrequency' => $subscriptionRequestRecord['charges_frequency'],
               'txnReference' => $request->txn_reference,
               'txnDate' => formatDate($request->txn_date),
               'subscriptionPageUrl' => URL::route('central.vendor.details', ['vendorIdOrUid' => $vendorData['_uid']]),
         ];
            if($this->manualSubscriptionRepository->updateIt($subscriptionRequestRecord, [
                'status' => 'pending',
                '__data' => [
                    'manual_txn_details' => [
                        'txn_reference' => $request->txn_reference,
                        'txn_date' => $request->txn_date,
                    ]
                ]
            ])) {
                //send mail to admin of manual subscription request.
                $this->baseMailer->notifyAdmin($emailData['subject'], 'manual-subscription-request', $emailData, 2);
                return $this->engineSuccessResponse();
            }
        }
        return $this->engineFailedResponse([], __tr('Failed to record your payment details'));
    }

    /**
     * Delete Vendor Manual Subscription Request
     *
     * @param BaseRequest $request
     * @return EngineResponse
     */
    public function processDeleteRequest($request)
    {
        $vendorId = getVendorId();
        $subscriptionRequestDeleted = $this->manualSubscriptionRepository->deleteItAll([
            'vendors__id' => $vendorId,
            'status' => 'initiated',
        ]);

        if ($subscriptionRequestDeleted) {
            return $this->engineSuccessResponse();
        }

        return $this->engineFailedResponse([], __tr('Failed to delete your request'));
    }


    /**
     * Process Capture Paypal Order
     *
     * @param   array  $inputData
     *
     * @return  json   object
     */
    public function processCapturePaypalOrder($inputData)
    {
        $request = $this->paypalEngine->paypalCaptureOrder($inputData);

        if (!__isEmpty($request)) {
            //update the data in table
            return $this->recordSentPaymentDetails($request);

        }
        return $this->engineFailedResponse(['show_message' => true], __tr('Purchased failed'));

    }
}
