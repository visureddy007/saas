<?php

/**
 * SubscriptionEngine.php - Main component file
 *
 * This file is part of the Subscription component.
 *-----------------------------------------------------------------------------*/

namespace App\Yantrana\Components\Subscription;

use Throwable;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Laravel\Cashier\Cashier;
use App\Yantrana\Base\BaseEngine;
use Laravel\Cashier\Subscription;
use Stripe\Exception\AuthenticationException;
use Laravel\Cashier\Exceptions\IncompletePayment;
use App\Yantrana\Components\Dashboard\DashboardEngine;
use App\Yantrana\Components\Vendor\Repositories\VendorRepository;
use App\Yantrana\Components\Subscription\Repositories\SubscriptionRepository;
use App\Yantrana\Components\Subscription\Interfaces\SubscriptionEngineInterface;
use App\Yantrana\Components\Subscription\Repositories\ManualSubscriptionRepository;

class SubscriptionEngine extends BaseEngine implements SubscriptionEngineInterface
{
    /**
     * @var SubscriptionRepository - Subscription Repository
     */
    protected $subscriptionRepository;

    /**
     * @var VendorRepository - Vendor Repository
     */
    protected $vendorRepository;

    /**
     * @var  ManualSubscriptionRepository $manualSubscriptionRepository - ManualSubscription Repository
     */
    protected $manualSubscriptionRepository;

    /**
     * @var object - Holds subscriber eloquent object
     */
    protected $subscriber;

   /**
     * @var DashboardEngine - Dashboard Engine
     */
    protected $dashboardEngine;

    /**
     * Constructor
     *
     * @param  SubscriptionRepository  $subscriptionRepository  - Subscription Repository
     * @return void
     *-----------------------------------------------------------------------*/
    public function __construct(
        SubscriptionRepository $subscriptionRepository,
        VendorRepository $vendorRepository,
        ManualSubscriptionRepository $manualSubscriptionRepository,
        DashboardEngine $dashboardEngine,
        )
    {
        $this->subscriptionRepository = $subscriptionRepository;
        $this->vendorRepository = $vendorRepository;
        $this->manualSubscriptionRepository = $manualSubscriptionRepository;
        $this->dashboardEngine = $dashboardEngine;
    }

    public function subscriber($vendorUid = null)
    {
        if (!$vendorUid and ! __isEmpty($this->subscriber)) {
            return $this->subscriber;
        }
        $this->subscriber = $this->vendorRepository->fetchIt($vendorUid ?? getUserAuthInfo('vendor_id'));
        return $this->subscriber;
    }

    public function getCurrentPlan($planId = null, $withSubscription = false, $vendorId = null)
    {
        // if plan id not sent search for current active subscription
        // to get the plan id
        if (! $planId) {
            $subscription = getVendorCurrentActiveSubscription($vendorId ?? getVendorId());
            // $subscription = Subscription::query()->where(['vendor_model__id' => getVendorId()])->active()->first();
            if (__isEmpty($subscription)) {
                return null;
            }
            // if found active subscription
            if($subscription->plan_id) {
                $planId = $subscription->plan_id;
            } else {
                $planId = $subscription->type;
            }
        }
        $subscriptionPlans = getPaidPlans();
        if (__isEmpty($subscriptionPlans)) {
            return null;
        }
        $subscriptionPlan = Arr::get($subscriptionPlans, $planId);
        if (__isEmpty($subscriptionPlan)) {
            return null;
        }
        $hasPlan = Str::contains(json_encode($subscriptionPlan), $planId); // subscription->stripe_plan

        if ($hasPlan) {
            if($withSubscription) {
                return [
                    'subscription' => $subscription,
                    'plan' => $subscriptionPlan,
                ];
            }
            return $subscriptionPlan;
        }

        return null;
    }

    public function prepareData()
    {
        $isValidStripeKeys = false;
        if (getAppSettings('enable_stripe')) {
            if($this->validateStripeApiKey()) {
                $isValidStripeKeys = true;
                try {
                    $stripeCustomer = $this->subscriber()->createOrGetStripeCustomer([
                        'name' => getUserAuthInfo('profile.full_name'),
                        'email' => getUserAuthInfo('profile.email'),
                        'address' => [
                            'line1' => getVendorSettings('address'),
                            'postal_code' => getVendorSettings('postal_code'),
                            'city' => getVendorSettings('city'),
                            'state' => getVendorSettings('state'),
                            'country' => getVendorSettings('country_code'),
                        ],
                    ]);

                } catch (\Throwable $th) {

                    //ALL SUBSCRIPTION MARK AS CANCELLED
                    if ($this->getCurrentPlan() != null) {
                        $this->subscriber()?->subscription($this->getCurrentPlan()['id'] ?? null)?->markAsCanceled();
                    }

                    //FORCEFULLY UPDATE VALUES
                    $this->subscriber()->forceFill([
                        'stripe_id' => null,
                        'trial_ends_at' => null,
                        'pm_type' => null,
                        'pm_last_four' => null,
                    ])->update();

                    $this->prepareData();
                } catch (\Throwable $th) {
                    return ['message' => $th->getMessage()];
                }
            }
        }
        $currentPlanDetails = $this->getCurrentPlan(null, true);
        $currentPlan = null;
        $currentSubscription = null;
        if (! __isEmpty($currentPlanDetails)) {
            $currentPlan = $currentPlanDetails['plan'];
            $currentSubscription = $currentPlanDetails['subscription'];
        }
        $planSelectorId = null;
        if ($currentSubscription and ((Arr::get($currentPlan, 'charges.monthly.price_id') === $currentSubscription->stripe_price) or ($currentSubscription->charges_frequency == 'monthly'))) {
            $planSelectorId = $currentPlan['id'].'___monthly';
        } elseif ($currentSubscription and ((Arr::get($currentPlan, 'charges.yearly.price_id') === $currentSubscription->stripe_price) or ($currentSubscription->charges_frequency == 'yearly'))) {
            $planSelectorId = $currentPlan['id'].'___yearly';
        }
        $configPlans = getConfigPaidPlans();
        $vendorId = getVendorId();
        // exiting manual request if any
        $existingManualSubscriptionPendingRequest = $this->manualSubscriptionRepository->fetchIt([
            'vendors__id' => $vendorId,
            'status' => 'pending',
        ]);

        $dataToReturn = [
            'intent' => null,
            'currentPlan' => $currentPlan,
            'invoices' => [],
            'planSelectorId' => $planSelectorId,
            'currentSubscription' => $currentSubscription,
            'subscriber' => $this->subscriber(),
            'planDetails' => getPaidPlans(),
            'planStructure' => $configPlans,
            'freePlanDetails' => getFreePlan(),
            'freePlanStructure' => getConfigFreePlan(),
            'isValidStripeKeys' => $isValidStripeKeys,
            'existingManualSubscriptionPendingRequest' => $existingManualSubscriptionPendingRequest,
        ];

        if (getAppSettings('enable_stripe') and $isValidStripeKeys) {
            $dataToReturn['intent'] = $this->subscriber()->createSetupIntent();
            $dataToReturn['invoices'] = $this->subscriber()->invoices();
        }

        return $dataToReturn;
    }

    /**
     * Cancel the Subscription for logged in vendor OR requested
     *
     * @param string $vendorUid
     * @param bool $discardGracePeriod
     * @return void
     */
    public function processCancellation($vendorUid = null, $discardGracePeriod = false)
    {
        // request to
        try {
            $subscriberVendor = $this->subscriber($vendorUid);
            if($discardGracePeriod) {
                $subscriberVendor->subscription($this->getCurrentPlan(null, false, $subscriberVendor->_id)['id'])->cancelNow();
            } else {
                $subscriberVendor->subscription($this->getCurrentPlan(null, false, $subscriberVendor->_id)['id'])->cancel();
            }
        } catch (Throwable $th) {

            return $this->engineFailedResponse([], $th->getMessage());
        }

        return $this->engineSuccessResponse([], __tr('Your subscription has been cancelled'));
    }

    public function processResume()
    {
        try {
            $processedData = $this->subscriber()->subscription($this->getCurrentPlan()['id'])->resume();
        } catch (Throwable $th) {

            return $this->engineFailedResponse([], $th->getMessage());
        }

        return $this->engineSuccessResponse([], __tr('Your subscription has been resumed'));
    }

    public function processChangePlan($request)
    {
        try {
            $planRequest = explode('___', $request->plan);
            $getPlanDetails = $this->getCurrentPlan($planRequest[0]);
            $planPriceId = Arr::get($getPlanDetails, 'charges.'.$planRequest[1].'.price_id');
            if (! $planPriceId) {
                return $this->engineFailedResponse([], __tr('This plan is not available for selected payment method.'));
            }
            $checkPlanUsages = $this->dashboardEngine->checkPlanUsages($getPlanDetails, getVendorId());
            if($checkPlanUsages) {
                return $this->engineFailedResponse([], __tr('Due to the over use of following features __checkPlanUsages__ as per the selected plan so this plan can not be subscribed as it has lower limits, Please choose different plan OR reduce your usages.', [
                    '__checkPlanUsages__' => "$checkPlanUsages"
                ]));
            }
            $processedData = $this->subscriber()
                ->subscription(
                    $this->getCurrentPlan()['id']
                )->allowPaymentFailures()->swap($planPriceId);

            // In Laravel Cashier, when user change plans for a subscription, the local subscription name is not automatically updated. The subscription name in the database is typically set when the subscription is created and does not change automatically when the plan is updated.To update the local subscription name after changing the plan, Then our system can update the subscription record in your database.
            if (! __isEmpty($processedData)) {
                $processedData->update(['type' => $getPlanDetails['id']]);
            }
        } catch (IncompletePayment $exception) {
            return $this->engineResponse(21, [
                'redirect_to' => route(
                    'cashier.payment',
                    [$exception->payment->id, 'redirect' => route('subscription.read.show')]
                ),
            ]);
        } catch (Throwable $th) {
            return $this->engineFailedResponse([], $th->getMessage());
        }

        return $this->engineSuccessResponse([], __tr('Your subscription has been changed'));
    }

    public function processRedirectToBillingPortal()
    {
        return $this->subscriber()->redirectToBillingPortal(route('subscription.read.show'));
    }

    public function processDownloadInvoice($invoiceId)
    {
        return $this->subscriber()->downloadInvoice($invoiceId, [
            'vendor' => getVendorSettings('name'),
            'product' => getVendorSettings('name').__tr(' Subscription'),
        ]);
    }

    public function processCreate($request)
    {
        try {
            $planRequest = explode('___', $request->plan);
            $getPlanDetails = $this->getCurrentPlan($planRequest[0]);
            if(!isset($planRequest[1])) {
                setRedirectAlertMessage(__tr('Invalid Plan'), 'error');
                return redirect()->route('subscription.read.show');
            }
            $planPriceId = Arr::get($getPlanDetails, 'charges.'.$planRequest[1].'.price_id');
            if(!$planPriceId) {
                setRedirectAlertMessage(__tr('Plan not available to subscribe'), 'error');
                return redirect()->route('subscription.read.show');
            }
            $checkPlanUsages = $this->dashboardEngine->checkPlanUsages($getPlanDetails, getVendorId());
            if($checkPlanUsages) {
                setRedirectAlertMessage(__tr('Due to the over use of following features __checkPlanUsages__ as per the selected plan so this plan can not be subscribed as it has lower limits, Please choose different plan OR reduce your usages.', [
                    '__checkPlanUsages__' => "$checkPlanUsages"
                ]), 'error');
                return redirect()->route('subscription.read.show');
            }
            $trialDays = Arr::get($getPlanDetails, 'trial_days');
            $planId = Arr::get($getPlanDetails, 'id');
            $subscription = $this->subscriber()->newSubscription($planId, $planPriceId);
            if ($trialDays) {
                $subscription->trialDays($trialDays);
            }
            $subscription->allowPaymentFailures()->create($request->paymentMethod);
        } catch (IncompletePayment $exception) {
            return redirect()->route(
                'cashier.payment',
                [$exception->payment->id, 'redirect' => route('subscription.read.show')]
            );
        } catch(\Exception $e) {
            setRedirectAlertMessage($e->getMessage(), 'error');
            return redirect()->route('subscription.read.show');
        }

        return redirect(route('subscription.read.show'));
    }

    // use Laravel\Cashier\Cashier;
    // use Stripe\Exception\AuthenticationException;

    public function validateStripeApiKey(): bool
    {
        try {
            // Assuming you've set your Stripe keys in Cashier's configuration or .env
            $stripe = Cashier::stripe();

            // Attempt to retrieve Stripe account details
            $stripe->accounts->retrieve();

            return true; // If no exception is thrown, the API key is valid
        } catch (AuthenticationException $e) {
            return false; // Authentication with Stripe's API failed
        } catch (\Exception $e) {
            // Handle other potential exceptions
            return false;
        }
    }


    /**
     * Delete all existing subscription entries.
     *
     * @return void
     */
    public function processDeleteSubscriptionEntries()
    {
        $this->subscriptionRepository->deleteItAll([
            [
                'id', '!=', null
            ]
        ]);
        return $this->engineSuccessResponse([], __tr('All the subscription entries has been cleared'));
    }

    public function prepareSubscriptionDataTableList()
    {
        $subscriptionCollection = $this->subscriptionRepository->fetchSubscriptionDataTableSource();
        $subscriptionPlans = getPaidPlans();
        $stripeStatus = configItem('subscription_status');
        $requireColumns = [
            'title',
            '_uid',
            'vendor_model__id',
            // 'plan_type',
            'plan_type' => function ($rowData) use (&$subscriptionPlans) {
                return Arr::get($subscriptionPlans, $rowData['plan_type'] . '.title');
            },
            'stripe_id' => function ($keyItem) {
                if (__isEmpty($keyItem['stripe_id'])) {
                    return 'NA';
                }

                return $keyItem['stripe_id'];
            },
            'stripe_status' => function ($keyItem) use ($stripeStatus) {
                return Arr::get($stripeStatus, $keyItem['stripe_status'], $keyItem['stripe_status']);
            },
            'stripe_price',
            // 'trial_ends_at',
            'ends_at' => function ($keyItem) {
                if (! ($keyItem['ends_at'])) {
                    return 'NA';
                } else {
                    return formatDate($keyItem['ends_at']);
                }
            },
            'created_at' => function ($keyItem) {
                if (__isEmpty($keyItem['created_at'])) {
                    return 'NA';
                }

                return formatDate($keyItem['created_at']);
            },
        ];
        return $this->dataTableResponse($subscriptionCollection, $requireColumns);
    }
}
