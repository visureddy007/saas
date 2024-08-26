@extends('layouts.app', ['title' => __tr('Proceed to Pay for Subscription')])
@section('content')
    @include('users.partials.header', [
    'title' => __tr('Proceed to Pay for Subscription'),
    'description' => '',
    'class' => 'col-lg-7'
    ])
@php
$paymentMethod = $subscriptionRequestRecord->__data['manual_txn_details']['selected_payment_method'] ?? null;
@endphp
<div class="container-fluid">
    <div class="row">
        <div class="col-12 text-center">
           <div class="card col-sm-12">
            @if($existingRequestExist)
            <div class="alert alert-danger my-4">
                {{  __tr('You already have existing initiated/pending request.') }}
            </div>
            @endif
            <div class="card-body">
                <h1 class="text-primary">{{ $planDetails['title'] }}</h1>
                @foreach ($planDetails['features'] as $featureKey => $featureValue)
                    @php
                        $structureFeatureValue = $featureValue;
                        $featureValue = $featureValue;
                    @endphp
                    <div class="my-2">
                     @if (isset($featureValue['type']) and ($featureValue['type'] == 'switch'))
                         @if (isset($featureValue['limit']) and $featureValue['limit'])
                         <i class="fa fa-check mr-2 text-success"></i>
                         @else
                         <i class="fa fa-times mr-2 text-danger"></i>
                         @endif
                         {{ ($structureFeatureValue['description']) }}
                         @else
                        <i class="fa fa-check text-success mr-2"></i>
                        @if (isset($featureValue['limit']) and $featureValue['limit'] < 0)
                            {{ __tr('Unlimited') }}
                        @elseif(isset($featureValue['limit']))
                            {{ $featureValue['limit'] }}
                        @endif
                        {{ ($structureFeatureValue['description']) }}
                        @if(isset($featureValue['limit_duration_title']))
                             {{ ($featureValue['limit_duration_title']) }}
                         @endif
                         @endif
                    </div>
                @endforeach
                <hr>
                @if ($checkPlanUsages)
                    <div class="text-center">
                        <div class="alert alert-danger">
                            <h3 class="text-white">{!! __tr('Due to the over use of following features __checkPlanUsages__ as per the selected plan so this plan can not be subscribed as it has lower limits, Please choose different plan OR reduce your usages.', [
                                '__checkPlanUsages__' => "<strong>$checkPlanUsages</strong>"
                            ]) !!}</h3>
                        </div>
                        <a class="btn btn-dark"
                            href="{{ route('subscription.read.show') }}">
                            <i class="fa fa-arrow-left"></i>
                            {{ __tr('Go back to My Subscription') }}
                        </a>
                    </div>
                @else
                <h1 class="text-primary">
                    {{ $planFrequencyTitle }} {{ $planChargesFormatted }}
                    <div class="text-dark">
                        <small>{{  __tr('No auto debit') }}</small>
                    </div>
                    <div class="mt-2">
                        <h3>
                            <dl>
                                <dt>{{  __tr('New Expiry Date will be on') }}</dt>
                                <dd class="text-danger">{{ $expiryDateFormatted }}</dd>
                            </dl>
                        </h3>
                        @if ($existingPlanDaysAdjustments)
                            <h5>{{  __tr('Your current plan balance has been adjusted') }}</h5>
                        @endif
                    </div>
                </h1>
                @if (($paymentMethod == 'upi') and ($subscriptionRequestRecord->status == 'initiated'))
                <fieldset class="col-sm-12 col-lg-8 offset-lg-2">
                    <legend>{{  __tr('UPI Payments') }}</legend>
                   <div >
                        <img height="100" class="my-1" src="{{ asset('imgs/upi-icon.png') }}">
                   </div>
                    <img class="img-responsive" src="{{ $upiPaymentQRImageUrl }}" alt="{{ __tr('UPI Payment') }}">
                    <h2 class="text-danger">
                        {{  __tr('Scan QR code and make the payment using any UPI app, once the payment made submit your transaction ID') }}
                    </h2>
                    <p>
                        {{ getAppSettings('payment_upi_customer_notes') }}
                    </p>
                </fieldset>
                @elseif ($paymentMethod == 'bank_transfer' and ($subscriptionRequestRecord->status == 'initiated'))
                <fieldset class="col-sm-12 col-lg-8 offset-lg-2">
                    <legend>{{  __tr('Bank Transfer') }}</legend>
                <h2 class="text-danger">
                    {{  __tr('Deposit Instructions are given below') }}
                </h2>
                <p class="lw-ws-pre-line ">
                    {{ getAppSettings('bank_transfer_instructions') }}
                  </p>
                </fieldset>
                @endif
            </div>
            @if ($paymentMethod == 'paypal' and ($subscriptionRequestRecord->status == 'initiated'))
            <fieldset class="col-sm-12 col-lg-6 offset-lg-3 paypal-padding">
                <legend>{{  __tr('PayPal') }}</legend>
                <div id="paypal-button-container"></div>
            </fieldset>
            <div class="alert">
                <a class="lw-ajax-link-action btn btn-danger" data-show-processing="true" data-method="post" href="{{ route('vendor.subscription_manual_pay.delete_request') }}">{{  __tr('Cancel Request') }}</a>
            </div>
            @else
            <div class="card-body">
               <div class="col-sm-12 col-lg-8 offset-lg-2">
                <div class="alert">
                    <a class="lw-ajax-link-action btn btn-danger" data-show-processing="true" data-method="post" href="{{ route('vendor.subscription_manual_pay.delete_request') }}">{{  __tr('Cancel Request') }}</a>
                </div>
                <fieldset class="mb-5">
                    <legend>{{  __tr('Payment Details') }}</legend>
                    @if ($subscriptionRequestRecord->status == 'initiated')
                    <x-lw.form id="lwEditManualSubscriptionForm" :action="route('vendor.subscription_manual_pay.send_payment_details')">
                        <input type="hidden" name="manual_subscription_uid" value="{{ $subscriptionRequestRecord->_uid }}">
                        <x-lw.input-field type="text" id="lwTxnRef" data-form-group-class="" :label="__tr('Transaction Reference')" name="txn_reference" required="true" />
                        <x-lw.input-field type="date" id="lwTxnDate" data-form-group-class="" :label="__tr('Transaction Date')" name="txn_date" required="true" />
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">{{ __('Submit') }}</button>
                        </div>
                    </x-lw.form>
                    @elseif ($subscriptionRequestRecord->status == 'pending')
                    <div class="alert alert-success">
                        <p>
                            {{  __tr('We have already recorded your details. Please wait while admin confirm it. If needed you contact us using following link') }}
                        </p>
                        <a class="text-white" href="{{ route('user.contact.form') }}">{{  __tr('Contact Us') }}</a>
                    </div>
                    @endif
                </fieldset>
               </div>
            </div>
            @endif
            @endif
           </div>
        </div>
    </div>
</div>
@endsection
@if(!$checkPlanUsages)
{{-- include paypal blade --}}
@include('subscription.manual-subscription.paypal-partial')
@endif