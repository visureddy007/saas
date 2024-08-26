<?php
/**
 * CampaignEngine.php - Main component file
 *
 * This file is part of the Campaign component.
 *-----------------------------------------------------------------------------*/

namespace App\Yantrana\Components\Campaign;

use App\Yantrana\Base\BaseEngine;
use App\Yantrana\Components\Campaign\Interfaces\CampaignEngineInterface;
use App\Yantrana\Components\Campaign\Repositories\CampaignRepository;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Request;
use XLSXWriter;


class CampaignEngine extends BaseEngine implements CampaignEngineInterface
{
    /**
     * @var CampaignRepository - Campaign Repository
     */
    protected $campaignRepository;

    /**
     * Constructor
     *
     * @param  CampaignRepository  $campaignRepository  - Campaign Repository
     * @return void
     *-----------------------------------------------------------------------*/
    public function __construct(CampaignRepository $campaignRepository)
    {
        $this->campaignRepository = $campaignRepository;
    }

    /**
     * Campaign datatable source
     *
     * @return array
     *---------------------------------------------------------------- */
    public function prepareCampaignDataTableSource($status)
    {
        $campaignCollection = $this->campaignRepository->fetchCampaignDataTableSource($status);
        $timeNow = now();
        // required columns for DataTables
        $requireColumns = [
            '_id',
            '_uid',
            'title',
            'template_name',
            'template_language',
            'created_at' => function ($rowData) {
                return formatDateTime($rowData['created_at']);
            },
            'scheduled_at' => function ($rowData) {
                return (!$rowData['scheduled_at'] or ($rowData['scheduled_at'] != $rowData['created_at'])) ? '<span>📅 </span>' . formatDateTime($rowData['scheduled_at']) : '<span title="' . __tr('Instant') . '">⚡ </span>' . formatDateTime($rowData['scheduled_at']);
            },
            'status',
            'scheduled_status' => function ($rowData) use (&$timeNow) {
                $statusText = __tr('Upcoming');
                if(Carbon::parse($rowData['scheduled_at']) < $timeNow) {
                    $statusText = __tr('Awaiting Execution');
                    if(($rowData['queue_pending_messages_count'] or $rowData['queue_processing_messages_count']) and $rowData['message_log_count']) {
                        $statusText = __tr('Processing');
                    } elseif(!$rowData['queue_pending_messages_count'] and !$rowData['queue_processing_messages_count']) {
                        $statusText = __tr('Executed');
                    } elseif(!$rowData['queue_pending_messages_count'] and !$rowData['message_log_count']) {
                        $statusText = __tr('NA');
                    }
                }
                return $statusText;
            },
            'delete_allowed' => function ($rowData) use (&$timeNow) {
                return (Carbon::parse($rowData['scheduled_at']) > $timeNow);
            },
        ];

        // prepare data for the DataTables
        return $this->dataTableResponse($campaignCollection, $requireColumns);
    }

    /**
     * Campaign delete process
     *
     * @param  mix  $campaignIdOrUid
     * @return array
     *---------------------------------------------------------------- */
    public function processCampaignDelete($campaignIdOrUid)
    {
        // fetch the record
        $campaign = $this->campaignRepository->fetchIt($campaignIdOrUid);
        // check if the record found
        if (__isEmpty($campaign)) {
            // if not found
            return $this->engineResponse(18, null, __tr('Campaign not found'));
        }
        // older campaigns can not be deleted
        if ($campaign->messageLog()->count()) {
            return $this->engineResponse(18, null, __tr('Executed Campaign can not be deleted'));
        }
        // ask to delete the record
        if ($this->campaignRepository->deleteIt($campaign)) {
            // if successful
            return $this->engineSuccessResponse([], __tr('Campaign deleted successfully'));
        }

        // if failed to delete
        return $this->engineFailedResponse([], __tr('Failed to delete Campaign'));
    }
     /**
     * Campaign archive process
     *
     * @param  mix  $campaignIdOrUid
     * @return array
     *---------------------------------------------------------------- */
    public function processCampaignArchive($campaignIdOrUid)
    {
        // fetch the record
        $campaign = $this->campaignRepository->fetchIt($campaignIdOrUid);
        // check if the record found
        if (__isEmpty($campaign)) {
            // if not found
            return $this->engineResponse(18, null, __tr('Campaign not found'));
        }
        // Prepare Update Package data
        $updateData = [
            'status' => 5,
        ];
        //Check if package archive
        if ($this->campaignRepository->updateIt($campaign,$updateData)) {
            return $this->engineSuccessResponse([], __tr('Campaign Archived successfully'));
        }

        // if failed to archive
        return $this->engineFailedResponse([], __tr('Failed to Archive Campaign'));
    }
     /**
     * Campaign unarchive process
     *
     * @param  mix  $campaignIdOrUid
     * @return array
     *---------------------------------------------------------------- */
    public function processCampaignUnarchive($campaignIdOrUid)
    {
        // fetch the record
        $campaign = $this->campaignRepository->fetchIt($campaignIdOrUid);
        // check if the record found
        if (__isEmpty($campaign)) {
            // if not found
            return $this->engineResponse(18, null, __tr('Campaign not found'));
        }
        // Prepare Update Package data
        $updateData = [
            'status' => 1,
        ];
        //Check if package archive
        if ($this->campaignRepository->updateIt($campaign,$updateData)) {
            return $this->engineSuccessResponse([], __tr('Campaign Unarchived successfully'));
        }
       

        // if failed to archive
        return $this->engineFailedResponse([], __tr('Failed to Unarchive Campaign'));
    }

    /**
     * Campaign prepare update data
     *
     * @param  mix  $campaignIdOrUid
     * @return object
     *---------------------------------------------------------------- */
    public function prepareCampaignUpdateData($campaignIdOrUid)
    {
        // data fetch request
        $campaign = $this->campaignRepository->fetchIt($campaignIdOrUid);
        // check if record found
        if (__isEmpty($campaign)) {
            // if record not found
            return $this->engineResponse(18, null, __tr('Campaign not found.'));
        }

        // if record found
        return $this->engineSuccessResponse($campaign->toArray());
    }

    /**
     * Campaign prepare update data
     *
     * @param  mix  $campaignIdOrUid
     * @return object
     *---------------------------------------------------------------- */
    public function prepareCampaignData($campaignIdOrUid)
    {
        // data fetch request
        // $campaign = $this->campaignRepository->with(['messageLog', 'queueMessages'])->fetchIt($campaignIdOrUid);
        $campaign = $this->campaignRepository->getCampaignData($campaignIdOrUid);
        // if record found
        abortIf(__isEmpty($campaign));
        $rawTime = Carbon::parse($campaign->scheduled_at, 'UTC');
        $scheduleAt = $rawTime->setTimezone($campaign->timezone);
        $campaign->scheduled_at_by_timezone = $scheduleAt;
        $statusText = __tr('Upcoming');
        $campaignStatus = 'upcoming';
        $queueFailedCount = 0;
        $timeNow = now();
        if(Carbon::parse($campaign->scheduled_at) < $timeNow) {
            $statusText = __tr('Awaiting Execution');
            if(($campaign->queue_pending_messages_count or $campaign->queue_processing_messages_count) and $campaign->message_log_count) {
                $statusText = __tr('Processing');
                $campaignStatus = 'processing';
            } elseif(!$campaign->queue_pending_messages_count and !$campaign->queue_processing_messages_count) {
                $statusText = __tr('Executed');
                $campaignStatus = 'executed';
            } elseif(!$campaign->queue_pending_messages_count and !$campaign->message_log_count) {
                $statusText = __tr('NA');
                $campaignStatus = 'na';
            }
        }
        $queueMessages = $campaign->queueMessages;
        $queueFailedCount = $queueMessages->where('status', 2)->count();
        if (Request::ajax() === true) {
            $messageLog = $campaign->messageLog;
            $campaignData = $campaign->__data;
            $totalContacts = (int) Arr::get($campaignData, 'total_contacts');
            $totalRead = $messageLog->where('status', 'read')->count();
            $totalReadInPercent = round($totalRead / $totalContacts * 100, 2) . '%';
            $totalDelivered = $messageLog->where('status', 'delivered')->count() + $totalRead;
            $totalDeliveredInPercent = round($totalDelivered / $totalContacts * 100, 2) . '%';
            $totalFailed = $queueFailedCount + $messageLog->where('status', 'failed')->count();
            $totalFailedInPercent = round($totalFailed / $totalContacts * 100, 2) . '%';
            updateClientModels([
                'totalDelivered' => $totalDelivered,
                'totalDeliveredInPercent' => __tr($totalDeliveredInPercent),
                'totalRead' => $totalRead,
                'totalReadInPercent' => __tr($totalReadInPercent),
                'totalFailed' => $totalFailed,
                'statusText' => $statusText,
                'campaignStatus' => $campaignStatus,
                'queueFailedCount' => $queueFailedCount,
                'totalFailedInPercent' => __tr($totalFailedInPercent),
                'executedCount' => $campaign->messageLog->count() ?? 0,
                'inQueuedCount' => $campaign->queueMessages->where('status', 1)->count() ?? 0,
            ]);
            return $this->engineSuccessResponse([
                'statusText' => $statusText,
                'campaignStatus' => $campaignStatus,
                'queueFailedCount' => $queueFailedCount,
            ]);
        }
        // if record found
        return $this->engineSuccessResponse([
            'campaign' => $campaign,
            'statusText' => $statusText,
            'campaignStatus' => $campaignStatus,
            'queueFailedCount' => $queueFailedCount,
        ]);
    }
    /**
     * Campaign prepare queue log data
     *
     * @param  mix  $campaignIdOrUid
     * @return object
     *---------------------------------------------------------------- */
    public function prepareCampaignQueueLogList($campaignIdOrUid)
    {
        // data fetch request
        $campaign = $this->campaignRepository->fetchIt($campaignIdOrUid);
        // data fetch request
        $campaignCollection = $this->campaignRepository->fetchCampaignQueueLogTableSource($campaign->_id);

        $requireColumns = [
            '_id',
            '_uid',
            'status',
            'whatsapp_message_error',
            'phone_with_country_code',
            'full_name' => function ($rowData) {
                return str_replace('null', '', $rowData['full_name']);
            },
            'updated_at' => function ($rowData) {
                return $rowData['formatted_updated_time'];
            },
        ];
        $this->prepareCampaignData($campaignIdOrUid);
        // prepare data for the DataTables
        return $this->dataTableResponse($campaignCollection, $requireColumns);
    }
    /**
     * Campaign prepare executed log data
     *
     * @param  mix  $campaignIdOrUid
     * @return object
     *---------------------------------------------------------------- */
    public function prepareCampaignExecutedLogList($campaignIdOrUid)
    {
        // data fetch request
        $campaign = $this->campaignRepository->fetchIt($campaignIdOrUid);
        abortIf(__isEmpty($campaign));
        // data fetch request
        $campaignCollection = $this->campaignRepository->fetchCampaignExecutedLogTableSource($campaign->_id);
     
        $requireColumns = [
            '_id',
            '_uid',
            'whatsapp_message_error',
            'contact_wa_id',
            'status',
            'full_name' => function ($rowData) {
                return str_replace('null', '', $rowData['full_name']);//manage full name null value
            },
            'messaged_at' => function ($rowData) {
                return $rowData['formatted_message_time'];
            },
            'updated_at' => function ($rowData) {
                return $rowData['formatted_updated_time'];
            },
        ];
        $this->prepareCampaignData($campaignIdOrUid);
        // prepare data for the DataTables
        return $this->dataTableResponse($campaignCollection, $requireColumns);
    }
      /**
     * campaign report
     *
     * @param string $exportType
     * @return Download File
     */
    public function processGenerateCampaignExecutedReport($exportType ='blank',$campaignUid)
    {
       // fetch the campaign record
       $campaign = $this->campaignRepository->fetchIt($campaignUid);
        $header = [];
        $campaignId = $campaign->_id;
        // required data like fields and groups
        $campaignExecutedData = $this->campaignRepository->fetchCampaignExecutedData($campaign->_id);
        // create header array
        foreach ($campaignExecutedData as $executedLogData) {
            $header[$executedLogData->input_name] = 'string';
        }
        $data = [];
        if($exportType == 'data') {
            if(isDemo() and isDemoVendorAccount()) {
                abort(403, __tr('Exporting Contacts data has been disabled for demo'));
            }
            // go though each contact and prepare item for export
            foreach ($campaignExecutedData as $contact) {
                $dataItem = [
                    $contact->full_name,
                    $contact->contact_wa_id,  // phone number
                    $contact->status,
                    $contact->formatted_updated_time,
                ];
                $data[] = $dataItem;
            }
        }
         // Excel campaign  data
         $campaignData = [
            'campaign_name' => 'Campaign Name:'.'  '.$campaign->title,
            'template_name'  =>'Template Name:'.'  '.$campaign->template_name,
            'template_language'         => 'Template Language:'.'  '.$campaign->template_language,
            'scheduled_at'  => 'Campaign Executed On:'.'  '. formatDateTime($campaign->scheduled_at).'      '.'Report Generated On:'.'  '.formatDateTime(now()),
            'design_manage'  =>'  ',
        ];

        //create temp path for store excel file
        $tempFile = tempnam(sys_get_temp_dir(), "Campaign_Execution_Report_{$campaignId}.xlsx");
        $writer = new XLSXWriter();

        $sheet1 = 'Campaign Execution Report';

        //set header column string
        $header = array("string", "string", "string", "string", "string");
        // topHeader for header web site name row set css styles
        $topHeader = array('halign' => 'center', 'valign' => 'center', 'font-size' => 12, 'font-style' => 'bold', 'height' => 26);

        // Style 1 for header title set css styles
        $styles1 = array('halign' => 'center', 'font-size' => 12,  'height' => 20);
        // Style 2 for Column title set css styles
        $styles2 = array('halign' => 'left', 'font-style' => 'bold', 'font-size' => 10, 'height' => 15, 'border' => 'left,right,top,bottom', 'border-style' => 'thin');
        //Style 4 for Total Contact Record
        $styles3 = array(
            ['halign' => 'left', 'border' => 'left,right,top,bottom', 'border-style' => 'thin'], //first_name
            ['halign' => 'left', 'border' => 'left,right,top,bottom', 'border-style' => 'thin'], //phone_number
            ['halign' => 'left', 'border' => 'left,right,top,bottom', 'border-style' => 'thin'], // message_delivery_status
            ['halign' => 'left', 'border' => 'left,right,top,bottom', 'border-style' => 'thin'], // status
            ['halign' => 'left', 'border' => 'left,right,top,bottom', 'border-style' => 'thin'], // last_status_updated_at
            'height' => 17,
        );

         //Main Column Header
         $writer->writeSheetHeader(
            $sheet1,
            $header,
            $col_options = [
                'suppress_row' => true,
                'widths' => [
                    25, //full_name
                    25, //phone no.
                    40, // message_delivery_status
                    50, // last_status_updated_at
                ], // Status width  set
            ]
        );
        //template_name and template_value Row
        $writer->writeSheetRow($sheet1,['Execution Log Report'], $topHeader);
           $writer->writeSheetRow($sheet1,[$campaignData['campaign_name']],$styles1);
             //template_name Row
           $writer->writeSheetRow($sheet1,[$campaignData['template_name']],$styles1);
           //template_language Row
           $writer->writeSheetRow($sheet1, [$campaignData['template_language']], $styles1);
          //scheduled_at Row
        $writer->writeSheetRow($sheet1, [$campaignData['scheduled_at']], $styles1);
          //scheduled_at Row
          $writer->writeSheetRow($sheet1, [$campaignData['design_manage']], $styles1);
         //Generated Todays date Row
           //Column Title row
        $writer->writeSheetRow($sheet1, ['Full Name' ,
            'Phone Number' ,
            'Message Delivery Status',
            // 'Status' ,
            'Last Status Updated At' ,], $styles2);

             //create row data
        $rows = $data;
        $dynamicDataCount = count($rows);

        //Total Order Payment Record Data
        foreach ($rows as $key => $row) {
            //Create sheet fetch data row dynamically
            $writer->writeSheetRow($sheet1, $row, $styles3);
        }

          //Merge two cells for set title & generated date in center
          $writer->markMergedCell($sheet1, $start_row = 0, $start_col = 0, $end_row = 0, $end_col = 4);
          $writer->markMergedCell($sheet1, $start_row = 1, $start_col = 0, $end_row = 1, $end_col = 4);
          $writer->markMergedCell($sheet1, $start_row = 2, $start_col = 0, $end_row = 2, $end_col = 4);
          $writer->markMergedCell($sheet1, $start_row = 3, $start_col = 0, $end_row = 3, $end_col = 4);
          $writer->markMergedCell($sheet1, $start_row = 4, $start_col = 0, $end_row = 4, $end_col = 4);
          $writer->markMergedCell($sheet1, $start_row = 5, $start_col = 0, $end_row = 5, $end_col = 4);
        // write to file
        $writer->writeToFile($tempFile);
        // file name
        $dateTime = str_slug(now()->format('Y-m-d-H-i-s'));
        // get back with response
        return response()->download($tempFile, "Campaign_Execution_Report-{$exportType}-{$dateTime}.xlsx", [
            'Content-Transfer-Encoding: binary',
            'Content-Type: application/octet-stream',
        ])->deleteFileAfterSend();
    }
       /**
     * campaign queue log report
     *
     * @param string $exportType
     * @return Download File
     */
    public function processGenerateQueuLogCampaignReport($exportType ='blank',$campaignUid)
    {
       // fetch the record of campaign
        $campaign = $this->campaignRepository->fetchIt($campaignUid);
        $header = [];
        $campaignId = $campaign->_id;
        // required data like fields and groups
        $campaignQueueLogData = $this->campaignRepository->fetchCampaignQueueLogData($campaign->_id);
        // create header array
        $data = [];
        if($exportType == 'data') {
            if(isDemo() and isDemoVendorAccount()) {
                abort(403, __tr('Exporting Contacts data has been disabled for demo'));
            }
            // go though each contact and prepare item for export
            foreach ($campaignQueueLogData as $contact) {

                $dataItem = [
                    $contact['full_name']=str_replace('null', '', $contact['full_name']),//manage full name null value
                    $contact['phone_with_country_code'],  // phone number
                    $contact['formatted_updated_time'],
                    $contact['whatsapp_message_error'],
                ];
                $data[] = $dataItem;
            }
        }
      
         // Excel campaign  data
         $campaignData = [
            'title' => 'Campaign Name:'.' '.$campaign->title,
            'template_name'         =>'Template Name:'.' '. $campaign->template_name,
            'template_language'         => 'Template Language:'.' '.$campaign->template_language,
            'scheduled_at'  => 'Campaign Executed On:'.' '. formatDateTime($campaign->scheduled_at).'    '.'Report Generated On:'.'  '.formatDateTime(now()),
            'design_manage'  =>'  ',
        ];
        //create temp path for store excel file
        $tempFile = tempnam(sys_get_temp_dir(), "Campaign_queue_log_Report_{$campaignId}.xlsx");
        $writer = new XLSXWriter();

        $sheet1 = 'Campaign Queue Log Report';

        //set header column string
        $header = array("string", "string", "string", "string", "string");

        // topHeader for header web site name row set css styles
        $topHeader = array('halign' => 'center', 'valign' => 'center', 'font-size' => 12, 'font-style' => 'bold', 'height' => 26);

        // Style 1 for header title set css styles
        $styles1 = array('halign' => 'center', 'font-size' => 12,  'height' => 20);

        // Style 2 for Column title set css styles
        $styles2 = array('halign' => 'left', 'font-style' => 'bold', 'font-size' => 10, 'height' => 15, 'border' => 'left,right,top,bottom', 'border-style' => 'thin');

        //Style 4 for Total Contact Record
        $styles4 = array(
            ['halign' => 'left', 'border' => 'left,right,top,bottom', 'border-style' => 'thin'], //first_name
            ['halign' => 'left', 'border' => 'left,right,top,bottom', 'border-style' => 'thin'], //phone_number
            ['halign' => 'left', 'border' => 'left,right,top,bottom', 'border-style' => 'thin'], // last_status_updated_at
            ['halign' => 'left', 'border' => 'left,right,top,bottom', 'border-style' => 'thin'], // messages
            'height' => 17,
        );

         //Main Column Header
         $writer->writeSheetHeader(
            $sheet1,
            $header,
            $col_options = [
                'suppress_row' => true,
                'widths' => [
                    25, //first_name
                    25, //phone_number
                    40, // last_status_updated_at
                    70, // messages
                ], // Status width  set
            ]
        );
        $writer->writeSheetRow($sheet1,['Queue Log Report'], $topHeader);
           //Website name Row
           $writer->writeSheetRow($sheet1,[$campaignData['title']], $styles1);
             //template_name Row
        $writer->writeSheetRow($sheet1, [$campaignData['template_name']], $styles1);
          //template_language Row
          $writer->writeSheetRow($sheet1, [$campaignData['template_language']], $styles1);
          //scheduled_at Row
        $writer->writeSheetRow($sheet1, [$campaignData['scheduled_at']], $styles1);
        //scheduled_at Row
        $writer->writeSheetRow($sheet1, [$campaignData['design_manage']], $styles1);
         //Generated Todays date Row
           //Column Title row
        $writer->writeSheetRow($sheet1, ['Full Name' ,
            'Phone Number' ,
            'Last Status Updated At',
            'Messages'], $styles2);

             //create row data
        $rows = $data;
        $dynamicDataCount = count($rows);

        //Total Order Payment Record Data
        foreach ($rows as $key => $row) {
            //Create sheet fetch data row dynamically
            $writer->writeSheetRow($sheet1, $row, $styles4);
        }

          //Merge two cells for set title & generated date in center
          $writer->markMergedCell($sheet1, $start_row = 0, $start_col = 0, $end_row = 0, $end_col = 4,);
          $writer->markMergedCell($sheet1, $start_row = 1, $start_col = 0, $end_row = 1, $end_col = 4);
          $writer->markMergedCell($sheet1, $start_row = 2, $start_col = 0, $end_row = 2, $end_col = 4);
          $writer->markMergedCell($sheet1, $start_row = 3, $start_col = 0, $end_row = 3, $end_col = 4);
          $writer->markMergedCell($sheet1, $start_row = 4, $start_col = 0, $end_row = 4, $end_col = 4);
          $writer->markMergedCell($sheet1, $start_row = 5, $start_col = 0, $end_row = 5, $end_col = 4);
        // write to file
        $writer->writeToFile($tempFile);
        // file name
        $dateTime = str_slug(now()->format('Y-m-d-H-i-s'));
        // get back with response
        return response()->download($tempFile, "Campaign_queue_log_Report-{$exportType}-{$dateTime}.xlsx", [
            'Content-Transfer-Encoding: binary',
            'Content-Type: application/octet-stream',
        ])->deleteFileAfterSend();
    }
}
