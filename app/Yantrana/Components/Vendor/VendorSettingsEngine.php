<?php

/**
 * VendorSettingsEngine.php - Main component file
 *
 * This file is part of the Vendor component.
 *-----------------------------------------------------------------------------*/

namespace App\Yantrana\Components\Vendor;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use App\Yantrana\Base\BaseEngine;
use App\Yantrana\Support\CommonTrait;
use App\Yantrana\Support\Country\Repositories\CountryRepository;
use App\Yantrana\Components\Vendor\Repositories\VendorRepository;
use App\Yantrana\Components\Contact\Repositories\ContactRepository;
use App\Yantrana\Components\Vendor\Repositories\VendorSettingsRepository;
use App\Yantrana\Components\Vendor\Interfaces\VendorSettingsEngineInterface;
use App\Yantrana\Components\WhatsAppService\Services\WhatsAppConnectApiService;

class VendorSettingsEngine extends BaseEngine implements VendorSettingsEngineInterface
{
    /**
     * @var CommonTrait - Common Trait
     */
    use CommonTrait;

    /**
     * @var VendorSettingsRepository - VendorSettings Repository
     */
    protected $vendorSettingsRepository;

    /**
     * @var ContactRepository - Contact Repository
     */
    protected $contactRepository;

    /**
     * @var CountryRepository - Country Repository
     */
    protected $countryRepository;

    /**
     * @var VendorRepository - Vendor Repository
     */
    protected $vendorRepository;

    /**
     * @var WhatsAppConnectApiService - WhatsApp Connect Api Service
     */
    protected $whatsAppConnectApiService;

    /**
     * Constructor
     *
     * @param  VendorSettingsRepository  $vendorSettingsRepository  - VendorSettings Repository
     * @param  CountryRepository  $countryRepository  - Country Repository
     * @param  VendorRepository  $vendorRepository  - Vendor Repository
     * @param  ContactRepository  $contactRepository  - Contacts Repository
     * @param  WhatsAppConnectApiService  $whatsAppConnectApiService  - WhatsApp Connect Api Service
     * @return void
     *-----------------------------------------------------------------------*/

    public function __construct(
        VendorSettingsRepository $vendorSettingsRepository,
        CountryRepository $countryRepository,
        VendorRepository $vendorRepository,
        ContactRepository $contactRepository,
        WhatsAppConnectApiService $whatsAppConnectApiService
    ) {
        $this->vendorSettingsRepository = $vendorSettingsRepository;
        $this->countryRepository = $countryRepository;
        $this->vendorRepository = $vendorRepository;
        $this->contactRepository = $contactRepository;
        $this->whatsAppConnectApiService = $whatsAppConnectApiService;
    }

    /**
     * Prepare Configuration.
     *
     * @param  string  $pageType
     * @return array
     *---------------------------------------------------------------- */
    public function prepareConfigurations($pageType)
    {
        // Get settings from config
        $defaultSettings = $this->getDefaultSettings(config('__vendor-settings.items.' . $pageType));

        // check if default settings exists
        if (__isEmpty($defaultSettings)) {
            return $this->engineResponse(18, null, __tr('Invalid page type.'));
        }
        $configurationSettings = $dbConfigurationSettings = [];
        // Check if default settings exists
        if (! __isEmpty($defaultSettings)) {
            // Get selected default settings
            $configurationCollection = $this->vendorSettingsRepository->fetchByNames(array_keys($defaultSettings));
            // check if configuration collection exists
            if (! __isEmpty($configurationCollection)) {
                foreach ($configurationCollection as $configuration) {
                    $dbConfigurationSettings[$configuration->name] = $this->castValue($configuration->data_type, $configuration->value);
                }
            }
            // Loop over the default settings
            foreach ($defaultSettings as $defaultSetting) {
                $configurationSettings[$defaultSetting['key']] = $this->prepareDataForConfiguration($dbConfigurationSettings, $defaultSetting);
                $configurationSettings[$defaultSetting['key'] . '_options'] = array_get($defaultSetting, 'options', []);
            }
        }
        //check page type is currency
        if ($pageType == 'general') {
            $configurationSettings['timezone_list'] = $this->getTimeZone();
            $configurationSettings['countries_list'] = $this->countryRepository->fetchAll()->toArray();
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
        } elseif ($pageType == 'email') {
            $configurationSettings['mail_drivers'] = configItem('mail_drivers');
            $configurationSettings['mail_encryption_types'] = configItem('mail_encryption_types');
        } elseif ($pageType == 'whatsapp_cloud_api_setup') {
            $configurationSettings['testContact'] = $this->contactRepository->fetchIt([
                '_uid' => getVendorSettings('test_recipient_contact'),
                'vendors__id' => getVendorId()
            ], [
                '_uid',
                'first_name',
                'last_name',
            ])->wa_id ?? null;
        }
        return $this->engineSuccessResponse([
            'configurationData' => $configurationSettings,
        ]);
    }

    /**
     * Delete configuration Item
     *
     * @param  string|array  $item
     * @param  int  $vendorId
     * @return EngineResponse
     *---------------------------------------------------------------- */
    public function deleteItemProcess($items, $vendorId = null)
    {
        if(!$vendorId) {
            $vendorId = getVendorId();
        }
        if(!$items) {
            return $this->engineFailedResponse([], __tr('Item name is required'));
        }
        if(!is_array($items)) {
            $items = [$items];
        }
        $totalSuccessItems = 0;
        foreach ($items as $item) {
            if($itemToDelete = $this->vendorSettingsRepository->fetchIt([
                'name' => $item,
                'vendors__id' => $vendorId,
            ])) {
                if(!__isEmpty($itemToDelete)) {
                    if($this->vendorSettingsRepository->deleteIt($itemToDelete)) {
                        $totalSuccessItems++;
                    }
                }
            }
        }
        if($totalSuccessItems) {
            return $this->engineSuccessResponse([], __tr("__totalSuccessItems__ items has been deleted", [
                '__totalSuccessItems__' => $totalSuccessItems
            ]));
        }
        return $this->engineFailedResponse([], __tr('Northing to disconnect'));
    }

    /**
     * Process Configuration Store.
     *
     * @param  string  $pageType
     * @param  array  $inputData
     * @return EngineResponse
     *---------------------------------------------------------------- */
    public function updateProcess($pageType, $inputData, $vendorId = null)
    {
        $dataForStoreOrUpdate = $configurationKeysForDelete = [];
        $isDataAddedOrUpdated = false;
        $vendorId =  $vendorId ?: getVendorId();
        // Get settings from config
        $defaultSettings = $this->getDefaultSettings(config('__vendor-settings.items.' . $pageType));

        // check if default settings exists
        if (__isEmpty($defaultSettings)) {
            return $this->engineResponse(18, ['show_message' => true], __tr('Invalid page type.'));
        }
        // Get selected default settings
        $configurationCollection = $this->vendorSettingsRepository->fetchByNames(array_keys($defaultSettings))->pluck('value', 'name')->toArray();
        // Check if input data exists
        if (! __isEmpty($inputData)) {
            foreach ($defaultSettings as $defaultInputKey => $defaultInputValue) {
                $inputKey = $defaultInputKey;
                // pre modify
                // test contact
                if (in_array($inputKey, [
                   'test_recipient_contact',
                   ]) and isset($inputData['test_recipient_contact']) and $inputData['test_recipient_contact']) {
                    $testContact = $this->contactRepository->fetchIt([
                        'wa_id' => $inputData['test_recipient_contact'],
                        'vendors__id' => $vendorId
                    ]);
                    if(__isEmpty($testContact)) {
                        $testContact = $this->contactRepository->storeContact([
                            'first_name' => 'Test',
                            'last_name' => 'Contact',
                            'phone_number' => $inputData['test_recipient_contact'],
                        ]);
                    }
                    if(!__isEmpty($testContact)) {
                        $inputData['test_recipient_contact'] = $testContact->_uid;
                    };
                }
                // < pre modify

                $inputValue = Arr::get($inputData, $inputKey);

                // ignore the item for saving/updating if sent the empty values sent
                if (Arr::get($defaultSettings, "$inputKey.ignore_empty") and ! $inputValue) {
                    continue;
                }

                // Check if default text and form text not same
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
                }
                // if not done through Embedded signup
                if(!array_key_exists('embedded_setup_done_at', $inputData)) {
                    // register webhook
                    if (in_array($inputKey, [
                        // 'facebook_app_id',
                        'facebook_app_secret',
                        ]) and $inputValue) {
                        // connect webhook
                        $connectedWebhookSetup = $this->whatsAppConnectApiService->connectBaseWebhook(
                            $inputData['facebook_app_id'],
                            $inputData['facebook_app_secret'],
                            getVendorUid()
                        );
                        if(!isset($connectedWebhookSetup['success']) or !$connectedWebhookSetup['success']) {
                            return $this->engineFailedResponse([
                                'show_message' => true
                            ], __tr('Failed to register Webhook.'));
                        }
                    }
                    if (in_array($inputKey, [
                        'whatsapp_access_token',
                        ]) and $inputValue) {
                            // get debug token info
                            $debugTokenInfo = $this->whatsAppConnectApiService->debugTokenInfo(getVendorSettings('facebook_app_id'), getVendorSettings('facebook_app_secret'), $inputValue);
                            if(Arr::get($debugTokenInfo, 'data.error')) {
                                return $this->engineFailedResponse([
                                    'show_message' => true
                                ], Arr::get($debugTokenInfo, 'data.error.message'));
                            }
                            $debugTokenInfo = $debugTokenInfo['data'];
                            $requiredPermissions = [
                                'whatsapp_business_management',
                                'whatsapp_business_messaging',
                                'public_profile',
                            ];
                            $checkPermissions = array_diff($requiredPermissions, $debugTokenInfo['scopes']);
                            if(!empty($checkPermissions)) {
                                return $this->engineFailedResponse(['show_message' => true], __tr('Access token missing following required permissions: __requiredPermissions__', [
                                    '__requiredPermissions__' => implode(',', $checkPermissions)
                                ]));
                            }
                            // store token info in system
                            if(!$this->updateProcess('whatsapp_cloud_api_setup', [
                                'whatsapp_token_info_data' => $debugTokenInfo
                            ], $vendorId)) {
                                return $this->engineFailedResponse(['show_message' => true], __tr('Failed to update token info'));
                            };
                    }
                    // business account and access key setup
                    if (in_array($inputKey, [
                        'whatsapp_business_account_id',
                        ]) and $inputValue) {
                        if(isset($inputData['current_phone_number_id']) and $inputData['current_phone_number_id']) {
                            unset($inputData['current_phone_number_id']);
                        }
                        $accessToken = (isset($inputData['whatsapp_access_token']) and $inputData['whatsapp_access_token']) ? $inputData['whatsapp_access_token'] : getVendorSettings('whatsapp_access_token');
                        $this->whatsAppConnectApiService->removeExistingWebhooks($inputData['whatsapp_business_account_id'], $accessToken);
                        $connectedWebhookSetup = $this->whatsAppConnectApiService->connectBaseWebhook(getVendorSettings('facebook_app_id'), getVendorSettings('facebook_app_secret'), getVendorUid());
                        if(!isset($connectedWebhookSetup['success']) or !$connectedWebhookSetup['success']) {
                            return $this->engineFailedResponse([
                                'show_message' => true
                            ], __tr('Failed to update Webhook.'));
                        }
                        $phoneNumbers = $this->whatsAppConnectApiService->getPhoneNumbers($inputData['whatsapp_business_account_id'], (isset($inputData['whatsapp_access_token']) and $inputData['whatsapp_access_token']) ? $inputData['whatsapp_access_token'] : getVendorSettings('whatsapp_access_token'))['data'] ?? [];
                        if(empty($phoneNumbers)) {
                            return $this->engineFailedResponse(['show_message' => true], __tr('Phone numbers not found'));
                        }
                        $phoneNumberRecord = $phoneNumbers[0];
                        if(!$this->updateProcess('whatsapp_cloud_api_setup', [
                            'current_phone_number_number' => cleanDisplayPhoneNumber($phoneNumberRecord['display_phone_number']),
                            'current_phone_number_id' => $phoneNumberRecord['id'],
                            'whatsapp_phone_numbers' => $phoneNumbers,
                        ], $vendorId)) {
                            return $this->engineFailedResponse(['show_message' => true], __tr('Failed to update Phone Numbers.'));
                        };
                    } elseif(in_array($inputKey, [
                        'current_phone_number_id', // it will get automatically updated
                    ])
                    and !in_array('whatsapp_phone_numbers', array_keys($inputData))
                    and (!$inputData['whatsapp_access_token'])
                    and $inputValue) { // get and set phone number
                        $phoneNumbers = getVendorSettings('whatsapp_phone_numbers');
                        if(!$phoneNumbers) {
                            $phoneNumbers = $this->whatsAppConnectApiService->getPhoneNumbers(getVendorSettings('whatsapp_business_account_id'), getVendorSettings('whatsapp_access_token'))['data'] ?? [];
                            if(empty($phoneNumbers)) {
                                return $this->engineFailedResponse(['show_message' => true], __tr('Phone numbers not found'));
                            }
                        }
                        // get the record of current requested phone number id
                        $phoneNumberRecord = Arr::first(($phoneNumbers ?? []), function ($value, $key) use (&$inputValue) {
                            return $value['id'] == $inputValue;
                        });
                        if(!is_array($phoneNumberRecord)) {
                            return $this->engineFailedResponse(['show_message' => true], __tr('Please re-sync phone numbers'));
                        }
                        if(!$this->updateProcess('whatsapp_cloud_api_setup', [
                            'current_phone_number_number' => cleanDisplayPhoneNumber($phoneNumberRecord['display_phone_number']),
                        ], $vendorId)) {
                            return $this->engineFailedResponse(['show_message' => true], __tr('Failed to update Phone Numbers.'));
                        };
                    }
                }
            } // loop ends
            // Send data for store or update
            if (
                ! __isEmpty($dataForStoreOrUpdate)
                and $this->vendorSettingsRepository->storeOrUpdate($dataForStoreOrUpdate, $vendorId)
            ) {
                activityLog('vendor settings updated');
                $isDataAddedOrUpdated = true;
                // sync templates
                if (isset($inputData['whatsapp_business_account_id']) and $inputData['whatsapp_business_account_id']) {
                    // sync templates
                    app()->make(\App\Yantrana\Components\WhatsAppService\WhatsAppTemplateEngine::class)->processSyncTemplates();
                    app()->make(\App\Yantrana\Components\WhatsAppService\WhatsAppServiceEngine::class)->refreshHealthStatus();
                }
            }

            // Check if data added / updated or deleted
            if ($isDataAddedOrUpdated) {
                // set token is not expired
                if(isset($inputData['whatsapp_access_token']) and $inputData['whatsapp_access_token']) {
                    $this->deleteItemProcess('whatsapp_access_token_expired', $vendorId);
                }

                return $this->engineResponse(21, [
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
     * Process Vendor basic details update
     *
     * @param [type] $inputData
     * @return void
     */
    public function updateBasicSettingsProcess($inputData)
    {
        $updateData = [];
        if (Arr::get($inputData, 'store_name')) {
            $updateData['title'] = $inputData['store_name'];
        }

        if (Arr::get($inputData, 'logo_name')) {
            $updateData['logo_image'] = $inputData['logo_name'];
        }

        if (Arr::get($inputData, 'favicon_name')) {
            $updateData['favicon'] = $inputData['favicon_name'];
        }

        if ($this->vendorRepository->updateIt(getVendorUid(), $updateData)) {
            return $this->engineSuccessResponse(['show_message' => true], __tr('Settings updated successfully.'));
        }

        return $this->engineResponse(14, ['show_message' => true], __tr('Nothing updated.'));
    }
}
