<?php
/**
* LanguageAddRequest.php - Request file
*
* This file is part of the Translation component.
*-----------------------------------------------------------------------------*/

namespace App\Yantrana\Components\Translation\Requests;

use App\Yantrana\Base\BaseRequest;

class LanguageAddRequest extends BaseRequest
{
    /**
     * Loosely sanitize fields.
     *------------------------------------------------------------------------ */
    protected $looseSanitizationFields = [];

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     *-----------------------------------------------------------------------*/
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the add author client post request.
     *
     * @return bool
     *-----------------------------------------------------------------------*/
    public function rules()
    {
        \Validator::extend('unique_language_name', function ($attribute, $value) {
            $translationLanguages = getAppSettings('translation_languages');
            if (! __isEmpty($translationLanguages)) {
                if (in_array(strtolower($value), array_map('strtolower', array_column($translationLanguages, 'name')))) {
                    return false;
                }
            }

            return true;
        });

        \Validator::extend('unique_language_id', function ($attribute, $value) {
            $translationLanguages = getAppSettings('translation_languages');
            if (! __isEmpty($translationLanguages)) {
                if (array_key_exists($value, $translationLanguages)) {
                    return false;
                }
            }

            return true;
        });

        $rules = [
            'language_name' => 'required|min:3|max:15|unique_language_name',
            'language_id' => 'required|min:2|max:15|alpha_dash|unique_language_id',
        ];

        return $rules;
    }

    /**
     * Set custom msg for field
     *
     * @return array
     *-----------------------------------------------------------------------*/
    public function messages()
    {
        return [
            'language_name.unique_language_name' => __tr('The :attribute has already been taken'),
            'language_id.unique_language_id' => __tr('The :attribute has already been taken'),
        ];
    }
}
