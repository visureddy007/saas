<?php

namespace App\Yantrana\Base;

use App\Yantrana\__Laraware\Core\CoreRequestTwo;

class BaseRequestTwo extends CoreRequestTwo
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [];
    }
}
