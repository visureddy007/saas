<?php

/**
 * VendorSettingsRequest.php - Request file
 *
 * This file is part of the Configuration component.
 *-----------------------------------------------------------------------------*/

namespace App\Yantrana\Components\Vendor\Requests;

use App\Yantrana\Base\BaseRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class VendorSettingsRequest extends BaseRequest
{
    protected $looseSanitizationFields = [
        'info_terms_and_conditions' => '',
        'info_refund_policy' => '',
    ];

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
     * Get the validation rules that apply to the user register request.
     *
     * @return bool
     *-----------------------------------------------------------------------*/
    public function rules()
    {
        $requestData = new Request;
        $inputData = $this->all();

        $pageType = request()->pageType;

        $rules = [];
        // check validation via page type
        switch ($pageType) {
            case 'general':
                $rules = [
                    // 'name'              => 'required',
                    'contact_email' => 'required|email',
                ];
                break;

            case 'payment':
                $enablePaypal = Arr::get($inputData, 'enable_paypal');
                $enableStripe = Arr::get($inputData, 'enable_stripe');
                $enableRazorpay = Arr::get($inputData, 'enable_razorpay');
                // check if paypal checkout is enable
                if ($enablePaypal) {
                    $paypalTestMode = $inputData['use_test_paypal_checkout'];
                    // Check if paypal test mode is enable
                    if ($paypalTestMode and ! $inputData['paypal_test_keys_exist']) {
                        $rules = [
                            'paypal_checkout_testing_publishable_key' => 'required',
                            'paypal_checkout_testing_secret_key' => 'required',
                        ];
                    } elseif (! $paypalTestMode and ! array_get($inputData, 'paypal_live_keys_exist')) {
                        $rules = [
                            'paypal_checkout_live_publishable_key' => 'required',
                            'paypal_checkout_live_secret_key' => 'required',
                        ];
                    }
                }
                // check if stripe payment enable
                if ($enableStripe) {
                    $stripeTestMode = $inputData['use_test_stripe'];
                    // Check if stripe test mode is enable
                    if ($stripeTestMode and ! $inputData['stripe_test_keys_exist']) {
                        $rules = [
                            'stripe_testing_secret_key' => 'required',
                            'stripe_testing_publishable_key' => 'required',
                        ];
                    } elseif (! $stripeTestMode and ! array_get($inputData, 'stripe_live_keys_exist')) {
                        $rules = [
                            'stripe_live_secret_key' => 'required',
                            'stripe_live_publishable_key' => 'required',
                        ];
                    }
                }
                // check if razorpay payment enable
                if ($enableRazorpay) {
                    $razorpayTestMode = $inputData['use_test_razorpay'];
                    // Check if stripe test mode is enable
                    if ($razorpayTestMode and ! $inputData['razorpay_test_keys_exist']) {
                        $rules = [
                            'razorpay_testing_key' => 'required',
                            'razorpay_testing_secret_key' => 'required',
                        ];
                    } elseif (! $razorpayTestMode and ! array_get($inputData, 'razorpay_live_keys_exist')) {
                        $rules = [
                            'razorpay_live_key' => 'required',
                            'razorpay_live_secret_key' => 'required',
                        ];
                    }
                }

                break;
            default:
                $rules = [];
                break;
        }

        return $rules;
    }
}
