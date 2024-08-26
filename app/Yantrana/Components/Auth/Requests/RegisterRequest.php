<?php

namespace App\Yantrana\Components\Auth\Requests;

use App\Yantrana\Base\BaseRequest;
use App\Yantrana\Components\Auth\Models\AuthModel;


class RegisterRequest extends BaseRequest
{
    /**
     * Secure form
     *------------------------------------------------------------------------ */
    protected $securedForm = true;

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
        $inputData = $this->all();
         // Combine country code and mobile number
         $mobileNumber = $inputData['mobile_number'];
        $rules = [
            'email' => 'required|string|email|unique:users,email' . (getAppSettings('disallow_disposable_emails') ? '|indisposable' : ''),
            'password' => 'required|string|confirmed|min:8',
            'username' => 'required|string|unique:users|alpha_dash|min:2|max:45|unique:users,username',
            'mobile_number' => [
                'required',
                'min:9',
                'max:15',
                function ($attribute, $value, $fail) use ($mobileNumber) {
                    if (str_starts_with($mobileNumber, '0') || str_starts_with($mobileNumber, '+')) {
                        $fail('Mobile number should be a numeric value without prefixing 0 or +.');
                    }
                    $exists = AuthModel::
                    where('mobile_number', $mobileNumber)
                    ->exists();
                if ($exists) {
                    $fail('The mobile number has already been taken with the given country code.');
                }
                }
            ],
            'vendor_title' => 'required|string|min:2|max:100',
            'first_name' => 'required|string|min:1|max:45',
            'last_name' => 'required|string|min:1|max:45',
        ];

        if (getAppSettings('user_terms') or getAppSettings('vendor_terms') or getAppSettings('privacy_policy')) {
            $rules['terms_and_conditions'] = 'accepted';
        }

        return $rules;
    }
}

