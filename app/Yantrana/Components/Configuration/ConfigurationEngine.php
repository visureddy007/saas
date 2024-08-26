<?php

/**
 * ConfigurationEngine.php - Main component file
 *
 * This file is part of the Configuration component.
 *-----------------------------------------------------------------------------*/

namespace App\Yantrana\Components\Configuration;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use App\Yantrana\Base\BaseEngine;
use App\Yantrana\Support\CommonTrait;
use Illuminate\Support\Facades\Artisan;
use App\Yantrana\Components\Media\MediaEngine;
use App\Yantrana\Components\Configuration\Repositories\ConfigurationRepository;
use App\Yantrana\Components\WhatsAppService\Services\WhatsAppConnectApiService;
use App\Yantrana\Components\Configuration\Interfaces\ConfigurationEngineInterface;


class ConfigurationEngine extends BaseEngine implements ConfigurationEngineInterface
{
    /**
     * @var CommonTrait - Common Trait
     */
    use CommonTrait;

    /**
     * @var ConfigurationRepository - Configuration Repository
     */
    protected $configurationRepository;

    /**
     * @var MediaEngine - Media Engine
     */
    protected $mediaEngine;
    /**
     * @var WhatsAppConnectApiService - WhatsApp Connect Api Service
     */
    protected $whatsAppConnectApiService;

    /**
     * Constructor
     *
     * @param  ConfigurationRepository  $configurationRepository  - Configuration Repository
     * @param  MediaEngine  $mediaEngine  - Media Engine
     * @param  WhatsAppConnectApiService  $whatsAppConnectApiService  - WhatsAppConnectApiService
     * @return void
     *-----------------------------------------------------------------------*/
    public function __construct(
        ConfigurationRepository $configurationRepository,
        MediaEngine $mediaEngine,
        WhatsAppConnectApiService $whatsAppConnectApiService
        )
    {
        $this->configurationRepository = $configurationRepository;
        $this->mediaEngine = $mediaEngine;
        $this->whatsAppConnectApiService = $whatsAppConnectApiService;
    }

    /**
     * Prepare Configuration.
     *
     * @param  string  $pageType
     * @return object
     *---------------------------------------------------------------- */
    public function prepareConfigurations($pageType)
    {
        // Get settings from config
        $defaultSettings = $this->getDefaultSettings(config('__settings.items.'.$pageType));
        // check if default settings exists
        if (__isEmpty($defaultSettings)) {
            return $this->engineResponse(18, null, __tr('Invalid page type.'));
        }
        $configurationSettings = $dbConfigurationSettings = [];
        // Check if default settings exists
        if (! __isEmpty($defaultSettings)) {
            // Get selected default settings
            $configurationCollection = $this->configurationRepository->fetchByNames(array_keys($defaultSettings));
            // check if configuration collection exists
            if (! __isEmpty($configurationCollection)) {
                foreach ($configurationCollection as $configuration) {
                    $dbConfigurationSettings[$configuration->name] = $this->castValue($configuration->data_type, $configuration->value);
                }
            }
            // Loop over the default settings
            foreach ($defaultSettings as $defaultSetting) {
                $configurationSettings[$defaultSetting['key']] = $this->prepareDataForConfiguration($dbConfigurationSettings, $defaultSetting);
            }
        }
        //check page type is currency
        if ($pageType == 'general') {
            $configurationSettings['timezone_list'] = $this->getTimeZone();
            $languages = getAppSettings('translation_languages');
            //set default language
            $languageList[] = [
                'id' => 'en',
                'name' => __tr('System Language (English)'),
                'status' => true,
            ];

            //check is not empty
            if (! __isEmpty($languages)) {
                foreach ($languages as $key => $language) {
                    if ($language['status']) {
                        $languageList[] = [
                            'id' => $language['id'],
                            'name' => $language['name'],
                            'status' => $language['status'],
                        ];
                    }
                }
            }
            $configurationSettings['languageList'] = $languageList;
        } elseif ($pageType == 'currency') {
            $configurationSettings['currencies'] = config('__currencies.currencies');
            $configurationSettings['currency_options'] = $this->generateCurrenciesArray($configurationSettings['currencies']['details']);
        } elseif ($pageType == 'premium-plans') {
            $defaultPlanDuration = $defaultSettings['plan_duration']['default'];
            $dbPlanDuration = $configurationSettings['plan_duration'];
            $configurationSettings['plan_duration'] = combineArray($defaultPlanDuration, $dbPlanDuration);
        } elseif ($pageType == 'premium-feature') {
            $defaultFeaturePlans = $defaultSettings['feature_plans']['default'];
            $dbFeaturePlans = $configurationSettings['feature_plans'];
            $configurationSettings['feature_plans'] = combineArray($defaultFeaturePlans, $dbFeaturePlans);
        } elseif ($pageType == 'email') {
            $configurationSettings['mail_drivers'] = configItem('mail_drivers');
            $configurationSettings['mail_encryption_types'] = configItem('mail_encryption_types');
        } elseif ($pageType == 'user') {
            $configurationSettings['admin_choice_display_mobile_number'] = configItem('admin_choice_display_mobile_number');
        }

        return $this->engineSuccessResponse([
            'configurationData' => $configurationSettings,
        ]);
    }

    /**
     * Process Configuration Store.
     *
     * @param  string  $pageType
     * @param  array  $inputData
     * @return object
     *---------------------------------------------------------------- */
    public function processConfigurationsStore($pageType, $inputData, $ignoreOtherFields = false)
    {
        $dataForStoreOrUpdate = $configurationKeysForDelete = [];
        $isDataAddedOrUpdated = false;

        // Get settings from config
        $defaultSettings = $this->getDefaultSettings(config('__settings.items.'.$pageType));

        // check if default settings exists
        if (__isEmpty($defaultSettings)) {
            return $this->engineResponse(18, ['show_message' => true], __tr('Invalid page type.'));
        }

        $isExtendedLicense = (getAppSettings('product_registration', 'licence') === 'dee257a8c3a2656b7d7fbe9a91dd8c7c41d90dc9');
        // Check if input data exists
        if (! __isEmpty($inputData)) {
            // Get selected default settings
            $configurationCollection = $this->configurationRepository->fetchByNames(array_keys($defaultSettings))->pluck('value', 'name')->toArray();
            //  loop through all the default items
            foreach ($defaultSettings as $defaultInputKey => $defaultInputValue) {
                $inputKey = $defaultInputKey;

                $inputValue = Arr::get($inputData, $inputKey);
                // check if want to ignore other default fields so it can not be set as blank
                // ($ignoreOtherFields === true) and 
                if ((array_key_exists($inputKey, $inputData) === false)) {
                    continue;
                }
                // ignore the item for saving/updating if sent the empty values sent
                if (Arr::get($defaultSettings, "$inputKey.ignore_empty") and (!$inputValue and (!Str::startsWith($inputKey, [
                    'enable_' , 'allow_'
                ])))) {
                    continue;
                }

                $castValues = $this->castValue(
                    ($defaultSettings[$inputKey]['data_type'] == 4)
                        ? 5 : $defaultSettings[$inputKey]['data_type'], // for Encode purpose only
                    $inputValue
                );
                if (array_get($defaultSettings[$inputKey], 'hide_value') and ! __isEmpty($inputValue)) {
                    $dataForStoreOrUpdate[] = [
                        'name' => $inputKey,
                        'value' => ($castValues and is_string($castValues) or is_numeric($castValues)) ? encrypt($castValues) : $castValues,
                        'data_type' => $defaultSettings[$inputKey]['data_type'],
                    ];
                } elseif (! array_get($defaultSettings[$inputKey], 'hide_value')) {
                    $dataForStoreOrUpdate[] = [
                        'name' => $inputKey,
                        'value' => $castValues,
                        'data_type' => $defaultSettings[$inputKey]['data_type'],
                    ];
                } elseif (array_get($defaultSettings[$inputKey], 'hide_value') and __isEmpty($inputValue)) {
                    $dataForStoreOrUpdate[] = [
                        'name' => $inputKey,
                        'value' => $castValues,
                        'data_type' => $defaultSettings[$inputKey]['data_type'],
                    ];
                }

                if (in_array($inputKey, [
                    'stripe_test_secret_key',
                    'stripe_test_publishable_key',
                    ]) and $inputValue) {
                        if(!Str::contains($inputValue, '_test_')) {
                            return $this->engineFailedResponse([
                                'show_message' => true
                            ], __tr('Only test keys are accepted.'));
                    }
                }
                if (in_array($inputKey, [
                    'stripe_live_secret_key',
                    'stripe_live_publishable_key',
                    ]) and $inputValue) {
                        if(!Str::contains($inputValue, '_live_')) {
                            return $this->engineFailedResponse([
                                'show_message' => true
                            ], __tr('Only live keys are accepted.'));
                    }
                }

                 if (!$isExtendedLicense and in_array($inputKey, [
                        'stripe_live_secret_key',
                        'stripe_live_publishable_key',
                        ]) and $inputValue) {
                    return $this->engineFailedResponse([
                        'show_message' => true
                    ], __tr('You need to purchase extended license to use live keys.'));
                }

                 if (!$isExtendedLicense and in_array($inputKey, [
                        'embedded_signup_app_id',
                        'embedded_signup_app_secret',
                        'embedded_signup_config_id',
                        ]) and $inputValue) {
                    return $this->engineFailedResponse([
                        'show_message' => true
                    ], __tr('You need to purchase extended license to use Embedded Signup.'));
                }
                 if ($isExtendedLicense and in_array($inputKey, [
                        'embedded_signup_app_id',
                        'embedded_signup_config_id',
                        'embedded_signup_app_secret',
                        ]) and $inputValue) {
                    // connect webhook
                    $connectedWebhookSetup = $this->whatsAppConnectApiService->connectBaseWebhook(
                        $inputData['embedded_signup_app_id'],
                        $inputData['embedded_signup_app_secret']);
                    if(!isset($connectedWebhookSetup['success']) or !$connectedWebhookSetup['success']) {
                        return $this->engineFailedResponse([
                            'show_message' => true
                        ], __tr('Failed to register Webhook.'));
                    }
                }
            }
            // Send data for store or update
            if (
                ! __isEmpty($dataForStoreOrUpdate)
                and $this->configurationRepository->storeOrUpdate($dataForStoreOrUpdate)
            ) {
                activityLog('Site configuration settings stored / updated.');
                $isDataAddedOrUpdated = true;
            }

            // Check if deleted keys deleted successfully
            if (
                ! __isEmpty($configurationKeysForDelete)
                and $this->configurationRepository->deleteConfiguration($configurationKeysForDelete)
            ) {
                $isDataAddedOrUpdated = true;
            }

            // Check if data added / updated or deleted
            if ($isDataAddedOrUpdated) {
                return $this->engineResponse(21,[
                    'show_message' => true,
                    'messageType' => 'success',
                    'reloadPage' => true
                ], __tr('Settings updated successfully ... reloading'));
            }

            return $this->engineResponse(14, ['show_message' => true], __tr('Nothing updated.'));
        }

        return $this->engineFailedResponse(['show_message' => true], __tr('Something went wrong on server.'));
    }

    /**
     * Process product registration
     *
     * @param  array  $inputData
     * @return void
     *---------------------------------------------------------------- */
    public function processProductRegistration($inputData = [])
    {
        return $this->processConfigurationsStore('product_registration', [
            'product_registration' => [
                'registration_id' => array_get($inputData, 'registration_id', ''),
                'email' => array_get($inputData, 'your_email', ''),
                'licence' => array_get($inputData, 'licence_type', ''),
                'registered_at' => now(),
                'signature' => sha1(
                    array_get($_SERVER, 'HTTP_HOST', '').
                        array_get($inputData, 'registration_id', '') . '4.5+'
                ),
            ],
        ]);
    }

    /**
     * Process product registration removal
     *
     * @return void
     *---------------------------------------------------------------- */
    public function processProductRegistrationRemoval()
    {
        try {
            // Initialize a cURL session
            $curl = curl_init();
            // Define the URL where you want to send the POST request
            $url = config('lwSystem.app_update_url') . "/api/app-update/deactivate-license"; // Replace with the actual URL
            // Define the POST fields, including the 'registration_id' parameter
            $postData = [
                'registration_id' => getAppSettings('product_registration', 'registration_id'), // Replace with the actual registration ID
            ];
            // Set the Origin header
            $headers = [
                'Origin: ' . array_get($_SERVER, 'HTTP_ORIGIN', ''), // Replace with your actual origin
            ];
            // Set cURL options
            curl_setopt($curl, CURLOPT_URL, $url); // Set the URL
            curl_setopt($curl, CURLOPT_POST, true); // Specify the request method as POST
            curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($postData)); // Attach the POST fields
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers); // Attach the Origin header
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true); // Return the response as a string
            // Execute the cURL request
            $response = curl_exec($curl);
            // Check for errors
            if ($response === false) {
                $error = curl_error($curl);
                // echo "cURL Error: $error";
            } else {
                // Handle the response as needed
                // echo "Response: $response";
                // __logDebug($response);
            }
            // Close the cURL session
            curl_close($curl);
        } catch (\Throwable $th) {
            //throw $th;
        }

        return $this->processConfigurationsStore('product_registration', [
            'product_registration' => [
                'registration_id' => '',
                'email' => '',
                'licence' => '',
                'registered_at' => now(),
                'signature' => '',
            ],
        ]);
    }

    /**
     * Process and Save Subscription Plans
     *
     * @param  array  $inputData
     * @return object
     */
    public function processSubscriptionPlans($inputData)
    {
        // get plan for the request is made
        $configPlanId = array_get($inputData, 'config_plan_id');
        // set as paid plan default
        $planType = 'paid';
        // get paid plan structure from config
        $plan = getConfigPaidPlans($configPlanId);
        // if not found then it may be free plan
        if (__isEmpty($plan)) {
            // set it as free
            $planType = 'free';
        }
        // get the stored subscription plan
        $storedSubscriptionPlans = getAppSettings('subscription_plans');
        // get the extended subscription plan with store settings
        $existingSubscriptionPlans = getPlans();
        // if the plan is free get it & its features
        if ($planType == 'free') {
            $existingSubscriptionPlan = $existingSubscriptionPlans[$planType] ?? [];
            $features = getConfigFreePlan('features');
            $planCharges = null;
        } else {
            // if the plan is paid get it & its features
            $existingSubscriptionPlan = $existingSubscriptionPlans[$planType][$configPlanId] ?? [];
            $features = getConfigPaidPlans("$configPlanId.features");
            $planCharges = getConfigPaidPlans("$configPlanId.charges");
        }
        // if the enabled is not sent by request then it must be disabled
        if (! isset($inputData['enabled'])) {
            $inputData['enabled'] = 0; // false
        }
        // collect & build features
        $featuresArray = [];
        if (! __isEmpty($features)) {
            // go through each feature
            foreach ($features as $featureKey => $feature) {
                $featuresArray[$featureKey] = [
                    // feature description
                    'description' => $featureKey.'_description',
                    // feature limit
                    'limit' => $featureKey.'_limit',
                ];
            }
        }
        // assign inputs to plan array to update
        arraySetAndGet($existingSubscriptionPlan, $inputData, [
            // plan title
            'title' => 'title',
            // plan enable or disable
            'enabled' => 'enabled',
            // plan features
            'features' => $featuresArray,
        ]);
        // Get charges off particular plan
        if (! __isEmpty($planCharges)) {
            foreach ($planCharges as $chargeKey => $chargeItem) {
                // if the enabled is not sent by request then it must be disabled
                if (! isset($inputData[$chargeKey.'_enabled'])) {
                    $inputData[$chargeKey.'_enabled'] = 0; // false
                }
                // assign charges inputs to plan array to update
                $inputData[$chargeKey.'_charge'] = (float) $inputData[$chargeKey.'_charge'];
                arraySetAndGet($existingSubscriptionPlan, $inputData, [
                    // is charge enabled
                    "charges.$chargeKey.enabled" => $chargeKey.'_enabled',
                    // price id
                    "charges.$chargeKey.price_id" => $chargeKey.'_plan_price_id',
                    // charges
                    "charges.$chargeKey.charge" => $chargeKey.'_charge',
                ]);
            }
        }
        // assign it to the existing data based on Plan Type
        if ($planType == 'free') {
            $storedSubscriptionPlans[$planType] = $existingSubscriptionPlan;
        } else {
            $storedSubscriptionPlans[$planType][$configPlanId] = $existingSubscriptionPlan;
        }
        // ask to store the updated data
        if ($this->configurationRepository->storeOrUpdate([
            [
                'name' => 'subscription_plans',
                'value' => $storedSubscriptionPlans,
                'data_type' => 4, // json
            ],
        ])) {
            return $this->engineSuccessResponse(['show_message' => true], __tr('Plan info updated.'));
        }

        return $this->engineResponse(14, ['show_message' => true], __tr('Nothing updated.'));
    }
}
