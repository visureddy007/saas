<?php

namespace App\Yantrana\Support;

use Carbon\Carbon;

/**
 * This CommonTrait class.
 *---------------------------------------------------------------- */
trait CommonTrait
{
    /*
      * Get User Specification Config Items
      *
      * @return boolean.
      *-------------------------------------------------------- */
    public static function getUserSettingConfigItem()
    {
        return require app_path('Yantrana/Components/UserSetting/Config/userSetting.php');
    }

    /*
      * Get User Specification Config Items
      *
      * @return boolean.
      *-------------------------------------------------------- */
    public function getUserSpecificationConfig()
    {
        return require app_path('Yantrana/Components/UserSetting/Config/specification.php');
    }

    /*
      * Get User Setting Config Items
      *
      * @return boolean.
      *-------------------------------------------------------- */
    public function getUserSettingConfig()
    {
        return self::getUserSettingConfigItem();
    }

    /**
     * Cast User Settings Value
     *
     * @param  int  $dataType
     * @param  string  $itemValue
     * @return array
     *---------------------------------------------------------------- */
    protected function castValue($dataType, $itemValue)
    {
        switch ($dataType) {
            case 1:
                return (string) $itemValue;
                break;
            case 2:
                return (bool) $itemValue;
                break;
            case 3:
                return (int) $itemValue;
                break;
            case 4:
                return ((! __isEmpty($itemValue)) and (! is_array($itemValue)))
                    ? json_decode($itemValue, true) : $itemValue;
                break;
            case 5:
                return ((! __isEmpty($itemValue)) and (is_array($itemValue)))
                    ? json_encode($itemValue) : '';
                break;
            case 6:
                return (float) $itemValue;
                break;
            default:
                return $itemValue;
        }
    }

    /**
     * Prepare Data For Configuration.
     *
     * @param  array  $dbConfigurationSettings
     * @param  array  $defaultSetting
     * @return array
     *---------------------------------------------------------------- */
    protected function prepareDataForConfiguration($dbConfigurationSettings, $defaultSetting)
    {
        $isHideValue = array_get($defaultSetting, 'hide_value', false);
        // Check if a value is hidden for show on view
        if ($isHideValue) {
            return (! __isEmpty(array_get($dbConfigurationSettings, $defaultSetting['key'])))
                ? true : false;
        }

        return array_get($dbConfigurationSettings, $defaultSetting['key'], $defaultSetting['default']);
    }

    /**
     * Get configuration default Settings.
     *
     * @return array
     *---------------------------------------------------------------- */
    protected function getDefaultSettings($configItem)
    {
        $defaultSettings = $configItem;
        // check if default settings exists
        if (__isEmpty($defaultSettings)) {
            return null;
        }

        foreach ($defaultSettings as $settingKey => $settingValue) {
            $defaultSettings[$settingKey]['default'] = $this->castValue($settingValue['data_type'], $settingValue['default']);
        }

        return $defaultSettings;
    }

    /**
     * Prepare User Array Data.
     *
     *-----------------------------------------------------------------------*/
    public function getUserOnlineStatus($userLastActivity)
    {
        $userOnlineStatus = null;
        $dtSubFiveMinute = Carbon::now()->subMinutes(5)->toDateTimeString();
        $dtSubTwoMinute = Carbon::now()->subMinutes(2)->toDateTimeString();
        //check user last login less than sub 2 minute on current datetime condition is false
        //then user is online
        if (! $userLastActivity < $dtSubTwoMinute) {
            $userOnlineStatus = 1;
        }
        //check user last login less than sub 2 minute on current datetime condition is true
        //then user is idle
        if ($userLastActivity < $dtSubTwoMinute) {
            $userOnlineStatus = 2;
        }
        //check user last login less than sub 5 minute on current datetime condition is true
        //then user is offline
        if ($userLastActivity < $dtSubFiveMinute) {
            $userOnlineStatus = 3;
        }

        return $userOnlineStatus;
    }

    /**
     * Get Timezone list
     *
     * @return array
     *---------------------------------------------------------------- */
    public function getTimeZone()
    {
        return getTimezonesArray();
    }

    /**
     * Generate currency array.
     *
     * @param  string  $pageType
     * @param  array  $inputData
     * @return array
     *---------------------------------------------------------------- */
    public function generateCurrenciesArray($currencies)
    {
        $currenciesArray = [];
        foreach ($currencies as $key => $currency) {
            $currenciesArray[] = [
                'currency_code' => $key,
                'currency_name' => $currency['name'],
            ];
        }

        $currenciesArray[] = [
            'currency_code' => 'other',
            'currency_name' => 'other',
        ];

        return $currenciesArray;
    }
}
