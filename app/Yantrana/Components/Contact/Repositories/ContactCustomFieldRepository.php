<?php
/**
* ContactCustomFieldRepository.php - Repository file
*
* This file is part of the Contact component.
*-----------------------------------------------------------------------------*/

namespace App\Yantrana\Components\Contact\Repositories;

use App\Yantrana\Base\BaseRepository;
use App\Yantrana\Components\Contact\Models\ContactCustomFieldModel;
use App\Yantrana\Components\Contact\Models\ContactCustomFieldValueModel;
use App\Yantrana\Components\Contact\Interfaces\ContactCustomFieldRepositoryInterface;

class ContactCustomFieldRepository extends BaseRepository implements ContactCustomFieldRepositoryInterface
{
    /**
     * primary model instance
     *
     * @var  object
     */
    protected $primaryModel = ContactCustomFieldModel::class;


    /**
      * Fetch customField datatable source
      *
      * @return  mixed
      *---------------------------------------------------------------- */
    public function fetchCustomFieldDataTableSource()
    {
        // basic configurations for dataTables data
        $dataTableConfig = [
            // searchable columns
            'searchable' => [
                'input_name',
                'input_type'

            ]
        ];
        // get Model result for dataTables
        return ContactCustomFieldModel::where([
            'vendors__id' => getVendorId()
        ])->dataTables($dataTableConfig)->toArray();
    }

    /**
      * Delete $customField record and return response
      *
      * @param  object $inputData
      *
      * @return  mixed
      *---------------------------------------------------------------- */

    public function deleteCustomField($customField)
    {
        // Check if $customField deleted
        if ($customField->deleteIt()) {
            // if deleted
            return true;
        }
        // if failed to delete
        return false;
    }

    /**
      * Store new customField record and return response
      *
      * @param  array $inputData
      *
      * @return  mixed
      *---------------------------------------------------------------- */

    public function storeCustomField($inputData)
    {
        // prepare data to store
        $keyValues = [
            'input_name',
            'input_type',
            'vendors__id' => getVendorId(),
        ];
        return $this->storeIt($inputData, $keyValues);
    }

    function storeCustomValues($values, $index = null, $whereIn = null) {
        if($index) {
            return (new ContactCustomFieldValueModel())->bunchInsertUpdate($values, $index, $whereIn);
        }
        return (new ContactCustomFieldValueModel())->prepareAndInsert($values);
    }
}
