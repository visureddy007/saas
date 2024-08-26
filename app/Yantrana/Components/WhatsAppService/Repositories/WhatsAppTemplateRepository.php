<?php
/**
* WhatsAppTemplateRepository.php - Repository file
*
* This file is part of the WhatsAppService component.
*-----------------------------------------------------------------------------*/

namespace App\Yantrana\Components\WhatsAppService\Repositories;

use App\Yantrana\Base\BaseRepository;
use App\Yantrana\Components\WhatsAppService\Interfaces\WhatsAppTemplateRepositoryInterface;
use App\Yantrana\Components\WhatsAppService\Models\WhatsAppTemplateModel;

class WhatsAppTemplateRepository extends BaseRepository implements WhatsAppTemplateRepositoryInterface
{
    /**
     * primary model instance
     *
     * @var object
     */
    protected $primaryModel = WhatsAppTemplateModel::class;

    public function syncTemplates($templatesData)
    {
        $vendorId = getVendorId();
        $currentTemplateIds = $this->fetchItAll([
            'vendors__id' => $vendorId,
        ], ['template_id'])->pluck('template_id')->toArray();
        $templatesToBeDeleted = array_diff($currentTemplateIds, collect($templatesData)->pluck('template_id')->toArray());
        if (! __isEmpty($templatesToBeDeleted)) {
            $this->deleteItAll($templatesToBeDeleted, 'template_id');
        }
        if(empty($templatesData)) {
            return false;
        }
        return $this->primaryModel::bunchInsertUpdate($templatesData, 'template_id');
    }

    /**
     * Fetch templates datatable source
     *
     * @return mixed
     *---------------------------------------------------------------- */
    public function fetchTemplatesDataTableSource()
    {
        // basic configurations for dataTables data
        $dataTableConfig = [
            // searchable columns
            'searchable' => [
                'template_name',
                'language',
                'category',
                'updated_at',
            ],
        ];

        // get Model result for dataTables
        return $this->primaryModel::where([
            'vendors__id' => getVendorId()
        ])->dataTables($dataTableConfig)->toArray();
    }

    /**
     * Get Approved templates in latest order
     *
     * @return Eloquent
     */
    function getApprovedTemplatesByNewest() {
        return $this->primaryModel::where([
            'status' => 'APPROVED',
            'vendors__id' => getVendorId(),
        ])->latest()->get();
    }
}
