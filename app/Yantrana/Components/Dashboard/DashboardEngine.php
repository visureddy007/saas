<?php

/**
 * DashboardEngine.php - Main component file
 *
 * This file is part of the Dashboard component.
 *-----------------------------------------------------------------------------*/

namespace App\Yantrana\Components\Dashboard;

use Illuminate\Support\Carbon;
use App\Yantrana\Base\BaseEngine;
use App\Yantrana\Components\User\Repositories\UserRepository;
use App\Yantrana\Components\Vendor\Repositories\VendorRepository;

use App\Yantrana\Components\Contact\Repositories\ContactRepository;
use App\Yantrana\Components\BotReply\Repositories\BotFlowRepository;
use App\Yantrana\Components\BotReply\Repositories\BotReplyRepository;
use App\Yantrana\Components\Campaign\Repositories\CampaignRepository;
use App\Yantrana\Components\Contact\Repositories\ContactGroupRepository;
use App\Yantrana\Components\Contact\Repositories\GroupContactRepository;
use App\Yantrana\Components\WhatsAppService\Services\WhatsAppApiService;
use App\Yantrana\Components\Dashboard\Interfaces\DashboardEngineInterface;
use App\Yantrana\Components\Contact\Repositories\ContactCustomFieldRepository;
use App\Yantrana\Components\WhatsAppService\Repositories\WhatsAppTemplateRepository;
use App\Yantrana\Components\WhatsAppService\Repositories\WhatsAppMessageLogRepository;
use App\Yantrana\Components\WhatsAppService\Repositories\WhatsAppMessageQueueRepository;

class DashboardEngine extends BaseEngine implements DashboardEngineInterface
{
    /**
     * @var VendorRepository - Vendor Repository
     */
    protected $vendorRepository;
    /**
     * @var UserRepository - User Repository
     */
    protected $userRepository;

    /**
         * @var ContactRepository - Contact Repository
         */
    protected $contactRepository;

    /**
     * @var ContactGroupRepository - ContactGroup Repository
     */
    protected $contactGroupRepository;

    /**
     * @var GroupContactRepository - ContactGroup Repository
     */
    protected $groupContactRepository;

    /**
     * @var WhatsAppTemplateRepository - WhatsApp Template Repository
     */
    protected $whatsAppTemplateRepository;

    /**
     * @var WhatsAppApiService - WhatsApp API Service
     */
    protected $whatsAppApiService;

    /**
     * @var WhatsAppMessageLogRepository - Status repository
     */
    protected $whatsAppMessageLogRepository;

    /**
     * @var WhatsAppMessageQueueRepository - WhatsApp Message Queue repository
     */
    protected $whatsAppMessageQueueRepository;
    /**
     * @var CampaignRepository - Campaign repository
     */
    protected $campaignRepository;

    /**
     * @var BotReplyRepository - Bot Reply repository
     */
    protected $botReplyRepository;

    /**
     * @var  BotFlowRepository $botFlowRepository - BotFlow Repository
     */
    protected $botFlowRepository;

    /**
     * @var ContactCustomFieldRepository - ContactGroup Repository
     */
    protected $contactCustomFieldRepository;

    /**
     * Constructor
     *
     * @param  VendorRepository  $vendorRepository  - Vendor Repository
     * @param  UserRepository  $userRepository  - User Repository
     * @param  ContactRepository  $contactRepository  - Contact Repository
     * @param  ContactGroupRepository  $contactGroupRepository  - ContactGroup Repository
     * @param  GroupContactRepository  $groupContactRepository  - Group Contacts Repository
     * @param  WhatsAppTemplateRepository  $whatsAppTemplateRepository  - WhatsApp Templates Repository
     * @param  WhatsAppApiService  $whatsAppApiService  - WhatsApp API Service
     * @param  WhatsAppMessageQueueRepository  $whatsAppMessageQueueRepository  - WhatsApp Message Queue
     * @param  CampaignRepository  $campaignRepository  - Campaign repository
     * @param  BotReplyRepository  $botReplyRepository  - Bot Reply repository
     * @param  BotFlowRepository  $botFlowRepository  - Bot Flow repository
     * @param  ContactCustomFieldRepository  $contactCustomFieldRepository  -Custom Contact Fields repository
     *
     * @return void
     */
    public function __construct(
        VendorRepository $vendorRepository,
        UserRepository $userRepository,
        ContactRepository $contactRepository,
        ContactGroupRepository $contactGroupRepository,
        GroupContactRepository $groupContactRepository,
        WhatsAppTemplateRepository $whatsAppTemplateRepository,
        WhatsAppApiService $whatsAppApiService,
        WhatsAppMessageLogRepository $whatsAppMessageLogRepository,
        WhatsAppMessageQueueRepository $whatsAppMessageQueueRepository,
        CampaignRepository $campaignRepository,
        BotReplyRepository $botReplyRepository,
        BotFlowRepository $botFlowRepository,
        ContactCustomFieldRepository $contactCustomFieldRepository
    ) {
        $this->vendorRepository = $vendorRepository;
        $this->userRepository = $userRepository;
        $this->contactRepository = $contactRepository;
        $this->contactGroupRepository = $contactGroupRepository;
        $this->groupContactRepository = $groupContactRepository;
        $this->whatsAppTemplateRepository = $whatsAppTemplateRepository;
        $this->whatsAppApiService = $whatsAppApiService;
        $this->whatsAppMessageLogRepository = $whatsAppMessageLogRepository;
        $this->whatsAppMessageQueueRepository = $whatsAppMessageQueueRepository;
        $this->campaignRepository = $campaignRepository;
        $this->botReplyRepository = $botReplyRepository;
        $this->botFlowRepository = $botFlowRepository;
        $this->contactCustomFieldRepository = $contactCustomFieldRepository;
    }

    /**
     * Prepare Vendor Dashboard Data
     *
     * @return array
     */
    public function prepareDashboardData()
    {
        return [
            'vendorRegistrations' => $this->vendorRepository->vendorRegistrationsStats(),
            'newVendors' => $this->vendorRepository->newVendors(),
            'totalVendors' => $this->vendorRepository->countIt(),
            'totalContacts' => $this->contactRepository->countIt(),
            'totalCampaigns' => $this->campaignRepository->countIt(),
            'messagesInQueue' => $this->whatsAppMessageQueueRepository->countIt([
                'status' => 1
            ]),
            'totalMessagesProcessed' => $this->whatsAppMessageLogRepository->countIt(),
            'totalActiveVendors' => $this->vendorRepository->countIt([
                'status' => 1,
            ]),
        ];
    }

    /**
     * Prepare Vendor Dashboard Data
     *
     * @return array
     */
    public function prepareVendorDashboardData($vendorId = null)
    {
        if (! $vendorId) {
            $vendorId = getVendorId();
        } else {
            if (is_string($vendorId)) {
                $vendor = $this->vendorRepository->fetchIt($vendorId);
                if (! __isEmpty($vendor)) {
                    $vendorId = $vendor->_id;
                }
            }
        }
        $vendorWhereClause = [
            'vendors__id' => $vendorId
        ];

        return array_merge([
            'firstOfMonth' => Carbon::now()->firstOfMonth(),
            'lastOfMonth' => Carbon::now()->lastOfMonth(),
            'vendorId' => $vendorId,
            'activeTeamMembers' => $this->userRepository->countVendorsActiveUsers($vendorWhereClause),
            'vendorUserData' => $this->userRepository->fetchIt($vendorWhereClause),
            'totalContacts' => $this->contactRepository->countIt($vendorWhereClause),
            'totalGroups' => $this->contactGroupRepository->countIt($vendorWhereClause),
            'totalCampaigns' => $this->campaignRepository->countIt($vendorWhereClause),
            'totalTemplates' => $this->whatsAppTemplateRepository->countIt($vendorWhereClause),
            'totalBotReplies' => $this->botReplyRepository->fetchBotReplyCount(),
            'messagesInQueue' => $this->whatsAppMessageQueueRepository->countIt([
                'status' => 1,
                'vendors__id' => $vendorId
            ]),
            'totalMessagesProcessed' => $this->whatsAppMessageLogRepository->countIt($vendorWhereClause),
        ]);
    }

    /**
     * Check plan uses against the plan
     *
     * @param array $planDetails
     * @param int $vendorId
     * @return string
     */
    function checkPlanUsages($planDetails, $vendorId) {
        $vendorWhereClause = [
            'vendors__id' => $vendorId
        ];
        $featuresLimitUnavailable = [];
        $onOffFeatures = [
            'ai_chat_bot' => getVendorSettings('enable_flowise_ai_bot', null, null, $vendorId),
            'api_access' => getVendorSettings('enable_vendor_webhook', null, null, $vendorId)
        ];
        $subscription = getVendorCurrentActiveSubscription($vendorId);
        $currentBillingCycle = app()->make(\App\Yantrana\Components\WhatsAppService\WhatsAppServiceEngine::class)->getCurrentBillingCycleDates($subscription->created_at ?? getUserAuthInfo('vendor_created_at'));
        $usagesCountCollection = [
            'contacts' => $this->contactRepository->countIt($vendorWhereClause),
            'campaigns' => $this->campaignRepository->countIt([
                'vendors__id' => $vendorId,
                [
                    'created_at', '>=', $currentBillingCycle['start'],
                ], [
                    'created_at', '<=', $currentBillingCycle['end'],
                ]
            ]),
            'bot_replies' => $this->botReplyRepository->fetchBotReplyCount($vendorId),
            'bot_flows' => $this->botFlowRepository->countIt($vendorWhereClause),
            'contact_custom_fields' => $this->contactCustomFieldRepository->countIt($vendorWhereClause),
            'system_users' => $this->userRepository->countIt($vendorWhereClause),
        ];
        foreach ($planDetails['features'] as $planFeatureKey => $planFeature) {
            if(isset($usagesCountCollection[$planFeatureKey])) {
                $vendorPlanDetails = vendorPlanDetails($planFeatureKey, $usagesCountCollection[$planFeatureKey], $vendorId, [
                    'plan_id' => $planDetails['id'],
                    'expiry_check' => false
                ]);
                if(!$vendorPlanDetails->isLimitAvailable()) {
                    $featuresLimitUnavailable[] = $planFeature['description'];
                }
            }
            if(isset($onOffFeatures[$planFeatureKey])) {
                $vendorPlanDetails = vendorPlanDetails($planFeatureKey, 0, $vendorId, [
                    'plan_id' => $planDetails['id']
                ]);
                if($onOffFeatures[$planFeatureKey] and !$vendorPlanDetails->isLimitAvailable()) {
                    $featuresLimitUnavailable[] = $planFeature['description'];
                }
            }
        }
        return implode(', ', $featuresLimitUnavailable ?? []);
    }
}
