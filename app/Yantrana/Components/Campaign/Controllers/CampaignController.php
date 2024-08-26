<?php
/**
* CampaignController.php - Controller file
*
* This file is part of the Campaign component.
*-----------------------------------------------------------------------------*/

namespace App\Yantrana\Components\Campaign\Controllers;

use App\Yantrana\Base\BaseController;
use App\Yantrana\Base\BaseRequest;
use App\Yantrana\Components\Campaign\CampaignEngine;

class CampaignController extends BaseController
{
    /**
     * @var CampaignEngine - Campaign Engine
     */
    protected $campaignEngine;

    /**
     * Constructor
     *
     * @param  CampaignEngine  $campaignEngine  - Campaign Engine
     * @return void
     *-----------------------------------------------------------------------*/
    public function __construct(CampaignEngine $campaignEngine)
    {
        $this->campaignEngine = $campaignEngine;
    }

    /**
     * list of Campaign
     *
     * @return json object
     *---------------------------------------------------------------- */
    public function showCampaignView()
    {
        validateVendorAccess('manage_campaigns');
        // load the view
        return $this->loadView('campaign.list');
    }

    /**
     * Campaign process delete
     *
     * @param  mix  $campaignUid
     * @return json object
     *---------------------------------------------------------------- */
    public function campaignStatusData($campaignUid, BaseRequest $request)
    {
        validateVendorAccess('manage_campaigns');
        // ask engine to process the request
        $processReaction = $this->campaignEngine->prepareCampaignData($campaignUid);
        // get back to controller with engine response
        return $this->processResponse($processReaction, [], [], true);
    }

    /**
     * list of Campaign
     *
     * @return json object
     *---------------------------------------------------------------- */
    public function prepareCampaignList($status)
    {
        validateVendorAccess('manage_campaigns');
        // respond with dataTables preparations
        return $this->campaignEngine->prepareCampaignDataTableSource($status);
    }

    /**
     * Campaign process delete
     *
     * @param  mix  $campaignIdOrUid
     * @return json object
     *---------------------------------------------------------------- */
    public function processCampaignDelete($campaignIdOrUid, BaseRequest $request)
    {
        validateVendorAccess('manage_campaigns');
        // ask engine to process the request
        $processReaction = $this->campaignEngine->processCampaignDelete($campaignIdOrUid);
        // get back to controller with engine response
        return $this->processResponse($processReaction, [], [], true);
    }
     /**
     * Campaign process archive
     *
     * @param  mix  $campaignIdOrUid
     * @return json object
     *---------------------------------------------------------------- */
    public function processCampaignArchive($campaignIdOrUid, BaseRequest $request)
    {
        validateVendorAccess('manage_campaigns');
        // ask engine to process the request
        $processReaction = $this->campaignEngine->processCampaignArchive($campaignIdOrUid);
        // get back to controller with engine response
        return $this->processResponse($processReaction, [], [], true);
    }
     /**
     * Campaign process unarchive
     *
     * @param  mix  $campaignIdOrUid
     * @return json object
     *---------------------------------------------------------------- */
    public function processCampaignUnarchive($campaignIdOrUid, BaseRequest $request)
    {
        validateVendorAccess('manage_campaigns');
        // ask engine to process the request
        $processReaction = $this->campaignEngine->processCampaignUnarchive($campaignIdOrUid);
        // get back to controller with engine response
        return $this->processResponse($processReaction, [], [], true);
    }

    /**
     * Campaign get update data
     *
     * @param  mix  $campaignIdOrUid
     * @return json object
     *---------------------------------------------------------------- */
    public function updateCampaignData($campaignIdOrUid)
    {
        validateVendorAccess('manage_campaigns');
        $processReaction = $this->campaignEngine->prepareCampaignUpdateData($campaignIdOrUid);
        // get back with response
        return $this->processResponse($processReaction, [], [], true);
    }
     /**
     * Campaign get status view
     *
     * @param  mix  $campaignIdOrUid
     * @return json object
     *---------------------------------------------------------------- */
    public function campaignStatusView($campaignUid,$pageType = null)
    {
        validateVendorAccess('manage_campaigns');
        $campaignDataResponse = $this->campaignEngine->prepareCampaignData($campaignUid);
        $gotoPage = 'queue';
        if(!$pageType and ($campaignDataResponse->data('campaignStatus') == 'executed') or ($pageType == 'executed')) {
            $gotoPage = 'executed';
        }

        $campaignDataResponse->updateData(
            'pageType', $gotoPage
        );
        return $this->loadView('whatsapp.campaign-status', $campaignDataResponse->data());
    }

    /**
      * list of campaign queue log
      *
      * @return  json object
      *---------------------------------------------------------------- */

      public function campaignQueueLogListView($campaignIdOrUid)
      {
        validateVendorAccess('manage_campaigns');
        // respond with dataTables preparations
        return $this->campaignEngine->prepareCampaignQueueLogList($campaignIdOrUid);
      }

        /**
      * list of executed queue log
      *
      * @return  json object
      *---------------------------------------------------------------- */

      public function campaignExecutedLogListView($campaignIdOrUid)
      {
        validateVendorAccess('manage_campaigns');
        // respond with dataTables preparations
        return $this->campaignEngine->prepareCampaignExecutedLogList($campaignIdOrUid);
      }

/**
     * campaign Executed report
     *
     * @param string $exportType
     * @return file
     */
    public function processCampaignExecutedReportGenerate($exportType = null,$campaignUid)
    {
        return $this->campaignEngine->processGenerateCampaignExecutedReport($exportType,$campaignUid);
    }

    /**
     * campaign Executed report
     *
     * @param string $exportType
     * @return file
     */
    public function processCampaignQueueLogReportGenerate($exportType = null,$campaignUid)
    {
        return $this->campaignEngine->processGenerateQueuLogCampaignReport($exportType,$campaignUid);
    }
}
