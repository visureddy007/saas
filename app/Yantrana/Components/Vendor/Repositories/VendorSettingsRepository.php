<?php

/**
 * VendorSettingsRepository.php - Repository file
 *
 * This file is part of the Vendor component.
 *-----------------------------------------------------------------------------*/

namespace App\Yantrana\Components\Vendor\Repositories;

use App\Yantrana\Base\BaseRepository;
use App\Yantrana\Components\Vendor\Interfaces\VendorSettingsRepositoryInterface;
use App\Yantrana\Components\Vendor\Models\VendorSettingsModel;

class VendorSettingsRepository extends BaseRepository implements VendorSettingsRepositoryInterface
{
    /**
     * primary model instance
     *
     * @var object
     */
    protected $primaryModel = VendorSettingsModel::class;

    /**
     * Store or update configuration data
     *
     * @param  array  $inputData
     * @return eloquent collection object
     *---------------------------------------------------------------- */
    public function storeOrUpdate($inputData, $vendorId = null)
    {
        $vendorId = $vendorId ?: getVendorId();
        foreach ($inputData as $key => $value) {
            if (($value['data_type'] === 4) and (is_array($value['value']))) {
                $inputData[$key]['value'] = json_encode($value['value']);
            }
            $inputData[$key]['vendors__id'] = $vendorId;
        }

        // Check if data updated or inserted
        if ($this->primaryModel::bunchInsertUpdate($inputData, 'name', [
            'key' => 'vendors__id',
            'value' => $vendorId,
        ])) {
            emptyFlashCache('vendor_setting_all_'.$vendorId);

            return true;
        }

        return false;
    }

    /**
     * Fetch All Record from Cache
     *
     * @param  array  $names
     * @return eloquent collection object
     *---------------------------------------------------------------- */
    public function fetchByNames($names)
    {
        return $this->primaryModel::whereIn('name', $names)
            ->where('vendors__id', getVendorId())
            ->select('name', 'value', 'data_type')
            ->get();
    }

    /**
     * Fetch Record by slug
     *
     * @param  array  $names
     * @return eloquent collection object
     *---------------------------------------------------------------- */

    /* public function fetchBySlug($slug)
    {
        return $this->primaryModel::where([
            'name' => 'vendor_slug',
            'value' => $slug
        ])->first();
    } */

    /**
     * Delete configuration by keys
     *
     * @param  array  $configurationNames
     * @return eloquent collection object
     *---------------------------------------------------------------- */
    public function deleteConfiguration($configurationNames)
    {
        $vendorId = getVendorId();
        if ($this->primaryModel::whereIn('name', $configurationNames)->where('vendors__id', $vendorId)->deleteIt()) {
            emptyFlashCache('vendor_setting_all_'.$vendorId);

            return true;
        }

        return false;
    }
}
