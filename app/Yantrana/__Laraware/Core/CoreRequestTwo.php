<?php

namespace App\Yantrana\__Laraware\Core;

/**
 * NOTE - 24 JUL 2021 - ******************************************************************************************
 *  Use this for projects after 2021 do not use it for earlier projects even upgraded as
 *  It may create problems for the request
 * ***************************************************************************************************************
 *
 * Core Request Two - 2.2.3 - 20 JAN 2024
 * Based on - Core Request - 1.2.1 - 24 APR 2021
 *
 * core request for Laravel applications
 *
 *
 * Dependencies:
 *
 * Laravel     5.0 +     - http://laravel.com
 *
 *
 *--------------------------------------------------------------------------- */

use Exception;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use YesSecurity;

abstract class CoreRequestTwo extends FormRequest
{
    /**
     * Set if you need form request secured.
     *------------------------------------------------------------------------ */
    protected $securedForm = false;

    /**
     * Unsecured/Non-encrypted form fields.
     *------------------------------------------------------------------------ */
    protected $unsecuredFields = [];

    /**
     * perform sanitization on input or not.
     *------------------------------------------------------------------------ */
    protected $sanitization = true;

    /**
     * the FILTER to Sanitize string along with Strip tags
     *
     * @since 1.2.0 - 01 MAR 2021
     *------------------------------------------------------------------------ */
    protected $strictSanitization = false;

    /**
     * Loosely sanitize fields - should be key value based array
     *------------------------------------------------------------------------ */
    protected $looseSanitizationFields = [];

    /**
     * Default Sanitization allowed tags.
     *------------------------------------------------------------------------ */
    protected $defaultSanitizationAllowedTags = '<p><br><br/><img><img/><ul><ol>
                    <li><strong><a><small><blockquote><em><h1><h2><h3><h4><h5>
                    <hr><hr/><address><dd><table><td><tr><th><thead><tbody><dl>
                    <dt><div><span>';

    /**
     * Call before validation process
     *
     * @example uses
    }
     * @return void
     *------------------------------------------------------------------------ */
    protected function processBefore()
    {
    }

    /**
     * Modify validate.
     *
     *
     * @since 2.0.0 - 24 JUL 2021
     *
     * @removed - validator function
     *
     * @return array|object
     *------------------------------------------------------------------------ */
    public function validate(array $rules, ...$params)
    {
        // normalize/decrypt form fields
        if ($this->securedForm === true) {
            $this->normalizeEncryptedInput();
        }

        // sanitization
        if ($this->sanitization === true) {
            $this->sanitizeInputs($this->input());
        }

        $this->processBefore();

        return parent::validate($rules, ...$params);
    }

    /**
     * normalize/decrypt form fields.
     *
     * @updated 2.2.3 - 01 MAR 2021
     *------------------------------------------------------------------------ */
    protected function normalizeEncryptedInput($passedInputs = [], $returnOnly = false)
    {
        if (empty($passedInputs)) {
            $allInputs = $this->input();
        } else {
            $allInputs = $passedInputs;
        }
        $nullItemValueCounts = 0;

        foreach ($allInputs as $key => $value) {
            // check if item in unsecured fields
            if (is_string($key) and in_array($key, $this->unsecuredFields)) {
                continue;
            }
            $decryptedKey = YesSecurity::decryptRSA($key);
            $encryptedKey = null;

            if ($decryptedKey or ($decryptedKey === '')) {
                $encryptedKey = $key;
                if ($decryptedKey === '') {
                    $decryptedKey = 0;
                }
                $key = $decryptedKey;
            }

            if (
                (in_array($key, $this->unsecuredFields) === false)
                and (is_array($value) === false)
            ) {
                if ($decryptedKey === 0) {
                    $allInputs[] = empty($value) ? $value : YesSecurity::decryptLongRSA($value);
                } else {
                    $allInputs[$key] = empty($value) ? $value : YesSecurity::decryptLongRSA($value);
                }
                unset($allInputs[$encryptedKey]);
            } elseif (
                (in_array($key, $this->unsecuredFields) === false)
                and (is_array($value) === true)
                and (empty($value) == true)
            ) {
                $allInputs[$key] = $value;
                unset($allInputs[$encryptedKey]);
            } elseif (
                (in_array($key, $this->unsecuredFields) === false)
                and (is_array($value) === true)
                and (empty($value) == false)
            ) {
                $allInputs[$key] = $this->normalizeEncryptedInput($value, true);
                unset($allInputs[$encryptedKey]);
            }

            if (! $key or ! isset($allInputs[$key])) {
                continue;
            }

            if ($allInputs[$key] === 'true') {
                $allInputs[$key] = true;
            }

            if ($allInputs[$key] === 'false') {
                $allInputs[$key] = false;
            }

            // if found null
            if ($allInputs[$key] === null) {
                $nullItemValueCounts++;
            }
        }

        if ($returnOnly === true) {
            return $allInputs;
        }

        // check if decryption fails & update accordingly
        if (count($allInputs) === $nullItemValueCounts) {
            $message = __('Invalid Request Inputs ... !!');

            if ($this->ajax()) { // if its an ajax request send response
                echo json_encode(__response([
                    'message' => $message,
                ], 3));
                // finish execution
                exit();
            } else { // if not an ajax request finish execution
                exit($message);
            }
        }

        if (! empty($passedInputs)) {
            return $allInputs;
        }

        unset($allInputs[YesSecurity::getFormSecurityID()]);

        $this->replace($allInputs);
    }

    /**
     * Sanitize Inputs.
     *------------------------------------------------------------------------ */
    protected function sanitizeInputs(array $inputs)
    {
        foreach ($inputs as $key => $value) {
            if (is_array($value)) {
                $this->sanitizeInputs($value);
            } elseif (is_string($value)) {
                // check if some tags are allowed
                // if yes concat with defaults
                if (array_key_exists($key, $this->looseSanitizationFields)) {
                    if (is_string($this->looseSanitizationFields[$key])) {
                        // strip tags except default & required allowed
                        $inputs[$key] = strip_tags($value, $this->defaultSanitizationAllowedTags
                            .$this->looseSanitizationFields[$key]);
                        if ($this->strictSanitization === true) {
                            $inputs[$key] = htmlspecialchars($inputs[$key]);
                        }
                    } elseif ($this->looseSanitizationFields[$key] === true) {
                        $inputs[$key] = $value;
                    }
                } else {
                    // strip all tags
                    $inputs[$key] = strip_tags($value);
                }
            }
        }

        // update inputs
        $this->replace($inputs);
    }

    /**
     * Secure for by encrypting/decrypting form payload
     *
     * @param  array  $unsecuredFields  - non encrypted fields
     * @return $this
     *
     * @since 1.2.0 - 01 MAR 2021
     */
    public function decryptPayload(array $unsecuredFields = [])
    {
        $this->securedForm = true;
        $this->unsecuredFields = array_merge($this->unsecuredFields, $unsecuredFields);
        $this->normalizeEncryptedInput();

        return $this;
    }

    /**
     * Prevent from decrypting the payload
     *
     * @return $this
     *
     * @since 1.2.0 - 01 MAR 2021
     */
    public function skipPayloadDecryption()
    {
        $this->securedForm = false;

        return $this;
    }

    /**
     * Set loose sanitization fields
     *
     * @param  array  $looseSanitizationFields  - should be key value based array
     * @return $this
     *
     * @since 1.2.0 - 01 MAR 2021
     *
     * @updated - 2.0.0 - 24 JUL 2021
     */
    public function looseSanitizationFields(array $looseSanitizationFields = [])
    {
        $this->looseSanitizationFields = array_merge($this->looseSanitizationFields, $looseSanitizationFields);
        if (empty($this->looseSanitizationFields) === false) {
            array_map(function ($item) {
                if (is_string($item) === false) {
                    throw new Exception('Array items should contain key value array, You can keep the value blank');
                }
            }, array_keys($this->looseSanitizationFields));
        }

        return $this;
    }

    /**
     * Prevent the form payload sanitization
     *
     * @return $this
     *
     * @since 1.2.0 - 01 MAR 2021
     */
    public function preventSanitization()
    {
        $this->sanitization = false;

        return $this;
    }

    /**
     * Apply the FILTER to Sanitize string along with Strip tags
     *
     * @return $this
     *
     * @since 1.2.0 - 01 MAR 2021
     */
    public function strictSanitization()
    {
        $this->strictSanitization = true;

        return $this;
    }

    /**
     * Mark the request as secured/encrypted
     *
     * @param  array  $unsecuredFields  - non encrypted fields
     * @return $this
     *
     * @since 2.2.3 - 01 MAR 2021
     */
    public function securedForm(array $unsecuredFields = [])
    {
        $this->securedForm = true;
        $this->unsecuredFields = array_merge($this->unsecuredFields, $unsecuredFields);

        return $this;
    }
}
