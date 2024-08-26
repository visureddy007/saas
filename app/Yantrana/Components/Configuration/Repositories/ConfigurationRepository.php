<?php
/**
* ConfigurationRepository.php - Repository file
*
* This file is part of the Configuration component.
*-----------------------------------------------------------------------------*/

namespace App\Yantrana\Components\Configuration\Repositories;

use App\Yantrana\Base\BaseRepository;
use App\Yantrana\Components\Configuration\Interfaces\ConfigurationRepositoryInterface;
use App\Yantrana\Components\Configuration\Models\ConfigurationModel;

class ConfigurationRepository extends BaseRepository implements ConfigurationRepositoryInterface
{
    /**
     * Fetch All Record from Cache
     *
     * @param  array  $names
     * @return eloquent collection object
     *---------------------------------------------------------------- */
    public function fetchByName($name)
    {
        return ConfigurationModel::where('name', $name)
            ->select('name', 'value', 'data_type')
            ->first();
    }

    /**
     * Fetch All Record from Cache
     *
     * @param  array  $names
     * @return eloquent collection object
     *---------------------------------------------------------------- */
    public function fetchByNames($names)
    {
        return ConfigurationModel::whereIn('name', $names)
            ->select('name', 'value', 'data_type')
            ->get();
    }

    /**
     * Store or update configuration data
     *
     * @param  array  $inputData
     * @return eloquent collection object
     *---------------------------------------------------------------- */
    public function storeOrUpdate($inputData)
    {
        foreach ($inputData as $key => $value) {
            if (($value['data_type'] === 4) and (is_array($value['value']))) {
                $inputData[$key]['value'] = json_encode($value['value']);
            }
        }
        // Check if data updated or inserted
        if (ConfigurationModel::bunchInsertUpdate($inputData, 'name')) {
            emptyFlashCache('app_setting_all');
            return true;
        }

        return false;
    }

    /**
     * Delete configuration by keys
     *
     * @param  array  $configurationNames
     * @return eloquent collection object
     *---------------------------------------------------------------- */
    public function deleteConfiguration($configurationNames)
    {
        if (ConfigurationModel::whereIn('name', $configurationNames)->deleteIt()) {
            return true;
        }

        return false;
    }

    /**
     * Store Translation Language
     *
     * @param  array  $translationStoreData
     * @return eloquent collection object
     *---------------------------------------------------------------- */
    public function storeTranslationLanguage($translationStoreData)
    {
        $keyValues = [
            'name',
            'value',
            'data_type',
        ];

        $configurationModel = new ConfigurationModel;
        // Check if store translation
        if ($configurationModel->assignInputsAndSave($translationStoreData, $keyValues)) {
            return $configurationModel;
        }

        return false;
    }
}
