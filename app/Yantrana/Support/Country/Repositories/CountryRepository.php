<?php

/**
 * CountryRepository.php - Repository file
 *
 * This file is part of the Country component.
 *-----------------------------------------------------------------------------*/

namespace App\Yantrana\Support\Country\Repositories;

use App\Yantrana\Base\BaseRepository;
use App\Yantrana\Support\Country\Blueprints\CountryRepositoryBlueprint;
use App\Yantrana\Support\Country\Models\Country as CountryModel;

class CountryRepository extends BaseRepository implements CountryRepositoryBlueprint
{
    /**
     * @var CountryModel - Country Model
     */
    protected $countryModel;

    /**
     * primary model instance
     *
     * @var object
     */
    protected $primaryModel = CountryModel::class;

    /**
     * Constructor
     *
     * @param  CountryModel  $countryModel  - Country Model
     * @return void
     *-----------------------------------------------------------------------*/
    public function __construct(?CountryModel $countryModel = null)
    {
        if ($countryModel === null) {
            $this->countryModel = new CountryModel;
        } else {
            $this->countryModel = $countryModel;
        }
    }

    /**
     * Fetch all countries
     *
     * @return eloquent collection object
     *---------------------------------------------------------------- */
    public function fetchAll()
    {
        return $this->countryModel->select('_id as id', 'name')->get();
    }

    /**
     * Fetch by id
     *
     * @param  int  $id
     * @param  array  $fields
     * @return eloquent collection object
     *---------------------------------------------------------------- */
    public function fetchById($id, $fields = [])
    {
        $country = $this->countryModel
            ->where('_id', $id);

        // Check if selected fields needed
        if (! __isEmpty($fields)) {
            return $country->first($fields);
        }

        return $country->first();
    }

    /**
     * Fetch by country code
     *
     * @param  int  $countryShortName
     * @return eloquent collection object
     *---------------------------------------------------------------- */
    public function fetchByCountryCode($countryShortName)
    {
        return $this->countryModel
            ->where('iso_code', $countryShortName)
            ->first();
    }

    /**
     * Store Country
     *
     * @param  int  $countryShortName
     * @return eloquent collection object
     *---------------------------------------------------------------- */
    public function storeCountry($storeData)
    {
        $keyValues = [
            'iso_code',
            'name_capitalized',
            'name',
            'iso3_code',
        ];

        $newCountryModel = new CountryModel;
        if ($newCountryModel->assignInputsAndSave($storeData, $keyValues)) {
            return $newCountryModel;
        }

        return false;
    }
}
