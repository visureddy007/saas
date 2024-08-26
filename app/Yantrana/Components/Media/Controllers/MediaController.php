<?php
/**
* MediaController.php - Controller file
*
* This file is part of the Media component.
*-----------------------------------------------------------------------------*/

namespace App\Yantrana\Components\Media\Controllers;

use App\Yantrana\Base\BaseController;
use App\Yantrana\Components\Configuration\ConfigurationEngine;
use App\Yantrana\Components\Media\MediaEngine;
use App\Yantrana\Components\Vendor\VendorSettingsEngine;
use Illuminate\Http\Request;

class MediaController extends BaseController
{
    /**
     * @var MediaEngine - Media Engine
     */
    protected $mediaEngine;

    /**
     * @var ConfigurationEngine - Configuration Engine
     */
    protected $configurationEngine;

    /**
     * @var VendorSettingsEngine - Vendor Engine
     */
    protected $vendorSettingsEngine;

    /**
     * Constructor
     *
     * @param  MediaEngine  $mediaEngine  - Media Engine
     * @return void
     *-----------------------------------------------------------------------*/
    public function __construct(
        MediaEngine $mediaEngine,
        ConfigurationEngine $configurationEngine,
        VendorSettingsEngine $vendorSettingsEngine
    ) {
        $this->mediaEngine = $mediaEngine;
        $this->configurationEngine = $configurationEngine;
        $this->vendorSettingsEngine = $vendorSettingsEngine;
    }

    /**
     * Upload Temp Media.
     *
     * @param object Request $request
     * @return json object
     *---------------------------------------------------------------- */
    public function uploadTempMedia(Request $request, $uploadItem = 'all')
    {
        $processReaction = $this->mediaEngine
            ->processUploadTempMedia($request->all(), $uploadItem);

        return $this->processResponse($processReaction, [], [], true, $processReaction->success() ? 200 : 406);
    }

    /**
     * Upload Logo.
     *
     * @param object Request $request
     * @return json object
     *---------------------------------------------------------------- */
    public function uploadLogo(Request $request)
    {
        $processReaction = $this->mediaEngine
            ->processUploadLogo($request->all(), 'logo');
       
        // Check if file uploaded successfully
        if ($processReaction->success()) {
            $this->configurationEngine->processConfigurationsStore('general', [
                'logo_name' => $processReaction['data']['fileName'],
            ], true);
        }

        return $this->processResponse($processReaction, [], [], true, $processReaction->success() ? 200 : 406);
    }

    /**
     * Upload Logo.
     *
     * @param object Request $request
     * @return json object
     *---------------------------------------------------------------- */
    public function uploadFavicon(Request $request)
    {
        $processReaction = $this->mediaEngine
            ->processUploadFavicon($request->all(), 'favicon');

        // Check if file uploaded successfully
        if ($processReaction->success()) {
            $this->configurationEngine->processConfigurationsStore('general', [
                'favicon_name' => $processReaction['data']['fileName'],
            ], true);
        }

        return $this->processResponse($processReaction, [], [], true, $processReaction->success() ? 200 : 406);
    }

    /**
     * Upload Logo.
     *
     * @param object Request $request
     * @return json object
     *---------------------------------------------------------------- */
    public function vendorUpload(Request $request, $uploadItem)
    {
        $allowedItems = [
            'vendor_logo' => 'logo_name',
            'vendor_small_logo' => 'small_logo_name',
            'vendor_favicon' => 'favicon_name',
        ];
        $processReaction = $this->mediaEngine
            ->processVendorUpload($request->all(), $uploadItem, $allowedItems);
        // Check if file uploaded successfully
        if ($processReaction->success()) {
            $this->vendorSettingsEngine->updateBasicSettingsProcess([
                $allowedItems[$uploadItem] => $processReaction['data']['fileName'],
            ]);

            return $this->processResponse($processReaction, [], [], true);
        }

        return $this->processResponse($processReaction, [], [], true, $processReaction->success() ? 200 : 406);
    }

    /**
     * Upload  Small Logo.
     *
     * @param object Request $request
     * @return json object
     *---------------------------------------------------------------- */
    public function uploadSmallLogo(Request $request)
    {
        $processReaction = $this->mediaEngine->processUploadSmallLogo($request->all(), 'small_logo');
    // Check if file uploaded successfully
    if ($processReaction->success()) {
        $this->configurationEngine->processConfigurationsStore('general', [
            'small_logo_name' => $processReaction['data']['fileName'],
        ], true);
    }

    return $this->processResponse($processReaction, [], [], true, $processReaction->success() ? 200 : 406);
    }
}
