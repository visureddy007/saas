<?php
/**
* TranslationUpdateRequest.php - Request file
*
* This file is part of the Translation component.
*-----------------------------------------------------------------------------*/

namespace App\Yantrana\Components\Translation\Requests;

use App\Yantrana\Base\BaseRequest;

class TranslationUpdateRequest extends BaseRequest
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
        $rules = [
            'message_id' => 'required',
            'message_str' => '',
            'language_id' => 'required|alpha_dash',
        ];

        return $rules;
    }
}
