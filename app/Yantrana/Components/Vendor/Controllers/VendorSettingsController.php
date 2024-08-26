<?php

/**
 * VendorSettingsController.php - Controller file
 *
 * This file is part of the Vendor component.
 *-----------------------------------------------------------------------------*/

namespace App\Yantrana\Components\Vendor\Controllers;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use App\Yantrana\Base\BaseRequest;
use App\Yantrana\Base\BaseController;
use App\Yantrana\Components\Vendor\VendorEngine;
use App\Yantrana\Components\BotReply\BotReplyEngine;
use App\Yantrana\Components\Vendor\VendorSettingsEngine;
use App\Yantrana\Components\Vendor\Requests\VendorSettingsRequest;

class VendorSettingsController extends BaseController
{
    /**
     * @var VendorSettingsEngine - VendorSettings Engine
     */
    protected $vendorSettingsEngine;

    /**
     * @var VendorEngine - VendorSettings Engine
     */
    protected $vendorEngine;

    /**
     * @var  BotReplyEngine $botReplyEngine - BotReply Engine
     */
    protected $botReplyEngine;

    /**
     * Constructor
     *
     * @param  VendorSettingsEngine  $vendorSettingsEngine  - VendorSettings Engine
     * @param  BotReplyEngine $botReplyEngine - BotReply Engine

     * @return void
     *-----------------------------------------------------------------------*/
    public function __construct(
        VendorSettingsEngine $vendorSettingsEngine,
        VendorEngine $vendorEngine,
        BotReplyEngine $botReplyEngine)
    {
        $this->vendorSettingsEngine = $vendorSettingsEngine;
        $this->vendorEngine = $vendorEngine;
        $this->botReplyEngine = $botReplyEngine;
    }

    /**
     * Vendor Settings View
     *
     * @return view
     *---------------------------------------------------------------- */
    public function index($pageType = 'general')
    {
        validateVendorAccess('administrative');
        $basicSettings = $this->vendorEngine->getBasicSettings();
        $processReaction = $this->vendorSettingsEngine->prepareConfigurations(Str::of($pageType)->slug('_'));
        $otherData = [];
        if($pageType == 'api-access') {
            // dynamicFields
            $otherData['dynamicFields'] = $this->botReplyEngine->preDataForBots()->data('dynamicFields');
        }
        // check if settings available
        abortIf(!file_exists(resource_path("views/vendors/settings/$pageType.blade.php")));
        // load view
        return $this->loadView('vendors.settings.index', array_merge([
            'pageType' => $pageType,
            'basicSettings' => $basicSettings,
        ], $processReaction['data'], $otherData), [
            'compress_page' => false
        ]);
    }

    /**
     * Get Configuration Data.
     *
     * @param  BaseRequest  $request
     * @return json object
     *---------------------------------------------------------------- */
    public function update(VendorSettingsRequest $request)
    {
        validateVendorAccess('administrative');
        // restrict demo user
        if(isDemo() and isDemoVendorAccount()) {
            return $this->processResponse(22, [
                22 => __tr('Functionality is disabled in this demo.')
            ], [], true);
        }

        $validationRules = [
            'pageType' => 'required',
        ];
        $request->validate($this->settingsValidationRules($request->pageType, $validationRules,$request->all()));
        $processReaction = $this->vendorSettingsEngine->updateProcess($request->pageType, $request->all());

        return $this->responseAction($this->processResponse($processReaction, [], [], true));
    }

    /**
     * Get Configuration Data.
     *
     *
     * @return json object
     *---------------------------------------------------------------- */
    public function updateBasicSettings(BaseRequest $request)
    {
        validateVendorAccess('administrative');
        // restrict demo user
        if(isDemo() and isDemoVendorAccount()) {
            return $this->processResponse(22, [
                22 => __tr('Functionality is disabled in this demo.')
            ], [], true);
        }

        $validationRules = [
            'store_name' => [
                'required',
                'max:200',
            ],
        ];
        $request->validate($this->settingsValidationRules($request->pageType, $validationRules,$request->all()));
        $processReaction = $this->vendorSettingsEngine->updateBasicSettingsProcess($request->all());

        return $this->responseAction($this->processResponse($processReaction, [], [], true));
    }

    /**
     * Setup validation array
     *
     * @param  string  $pageType
     * @param  array  $validationRules
     * @param  array  $inputFields
     * @return mixed
     */
    protected function settingsValidationRules($pageType, $validationRules = [], $inputFields = [])
    {
        if (! $pageType) {
            return $validationRules;
        }
        foreach (config('__vendor-settings.items.' . $pageType) as $settingItemKey => $settingItemValue) {
            $settingsValidationRules = Arr::get($settingItemValue, 'validation_rules', []);
            $isValueHidden = Arr::get($settingItemValue, 'hide_value');
            if ($settingsValidationRules) {
                // skip validation if hidden value item and empty and the value is already set
                if(!array_key_exists($settingItemKey, $inputFields) or ($isValueHidden and empty(Arr::get($inputFields, $settingItemKey)) and getVendorSettings($settingItemKey))) {
                    continue;
                }
                $existingItemRules = Arr::get($validationRules, $settingItemKey, []);
                $validationRules[$settingItemKey] = array_merge(
                    ! is_array($existingItemRules) ? [$existingItemRules] : $existingItemRules,
                    $settingsValidationRules
                );
            }
        }
        return $validationRules;
    }



    public function disableSoundForMessageNotification() {
        // as it cones from flash memory so it won't be fresh data in single request
        // thats why we have applied reverse logic here
        $isSoundDisabled = getVendorSettings('is_disabled_message_sound_notification');
        $this->vendorSettingsEngine->updateProcess('internals', [
            'is_disabled_message_sound_notification' => $isSoundDisabled ? false : true
        ]);
        updateClientModels([
            'disableSoundForMessageNotification' =>  !$isSoundDisabled
        ]);
        return $this->processResponse(1, [
            1 => $isSoundDisabled ? __tr('Sound for message notification enabled') : __tr('Sound for message notification disabled')
        ], [], true);
    }
}
