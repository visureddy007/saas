<?php
/**
* CommonPostRequest.php - Request file
*
* This file is part common support.
*-----------------------------------------------------------------------------*/

namespace App\Yantrana\Support;

use App\Yantrana\Base\BaseRequest;

class CommonRequest extends BaseRequest
{
    /**
     * Set if you need form request secured.
     *------------------------------------------------------------------------ */
    protected $securedForm = false;

    /**
     * Authorization for request.
     *
     * @return bool
     *-----------------------------------------------------------------------*/
    public function authorize()
    {
        return true;
    }

    /**
     * Validation rules.
     *
     * @return bool
     *-----------------------------------------------------------------------*/
    public function rules()
    {
        return [];
    }
}
