<?php

namespace App\Yantrana\__Laraware\Core;

/**
 * Engine Response - 0.3.1 - 10 OCT 2023
 *
 * engine response for Angulara (Laraware) applications
 *
 * @since 0.1.0 - 14 DEC 2021
 *--------------------------------------------------------------------------- */

use ArrayObject;

/**
 * Engine response class
 */
class EngineResponse extends ArrayObject
{
    public function __construct($array = [])
    {
        parent::__construct($array, ArrayObject::ARRAY_AS_PROPS);
    }

    /**
     * Get or check the reaction code for the response
     *
     * @param  int  $checkAgainst
     * @return int|bool
     */
    public function reaction($checkAgainst = null)
    {
        if ($checkAgainst) {
            return $this->reaction_code === $checkAgainst;
        }

        return $this->reaction_code;
    }

    /**
     * Check if reaction code is 1 which seems to bbe success.
     *
     * @since 0.2.0 - 26 DEC 2021
     *
     * @return bool
     */
    public function success()
    {
        return $this->reaction_code === 1;
    }

    /**
     * Check if reaction code is not 1 which seems to be failed.
     *
     * @since 0.3.0 - 28 SEP 2023
     *
     * @return bool
     */
    public function failed()
    {
        return $this->success() !== true;
    }

    /**
     * Get the data from reaction
     *
     * @param  int|string  $item
     * @param  mixed  $default
     * @return mixed
     */
    public function data($item = null, $default = null)
    {
        if ($item) {
            return array_get($this->data, $item, $default);
        }

        return $this->data;
    }

    /**
     * Update Data
     *
     * @param  int|string  $item
     * @param  mixed  $dataUpdate
     * @return mixed
     *
     * @since 0.4.0 - 10 OCT 2023
     */
    public function updateData($item, $dataUpdate)
    {
        if ($item) {
            return array_set($this->data, $item, $dataUpdate);
        }

        return $this->data = $dataUpdate;
    }

    /**
     * Get the reaction message set at engine level
     *
     * @return string|array
     */
    public function message()
    {
        return $this->message;
    }

    /**
     * Get the http status code set for engine reaction
     *
     * @return int
     */
    public function httpCode()
    {
        return $this->http_code;
    }
}
