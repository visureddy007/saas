@extends('layouts.app', ['title' => __tr('My Subscription')])
@section('content')
    @include('users.partials.header', [
    'title' => __tr('My Subscription'),
    'description' => '',
    'class' => 'col-lg-7'
    ])
    <?php $isExtendedLicence = (getAppSettings('product_registration', 'licence') === 'dee257a8c3a2656b7d7fbe9a91dd8c7c41d90dc9'); ?>
   @if(isset($message))
    <div class="container">
        <div class="alert alert-danger">
            {{ $message }}
        </div>
    </div>
   @else
   @php
   $subscriptionPlans = getAppSettings('subscription_plans');
   $isManualSubscription = $currentSubscription?->plan_id ? true : false;
   $isCashierSubscription = $currentSubscription?->stripe_status ? true : false;
   $hasPlansForPurchase = false;

   foreach ($planStructure as $planKey => $plan) {
       $plan = $planDetails[$planKey];
       if (!$plan['enabled']) {
           continue;
       }
       $hasPlansForPurchase = true;
       break;
   }
   @endphp
   <div class="container-fluid pb-5">
       <div class="row">
           <div class="col-xl-12">
            @if ($errors->any())
            <div class="alert alert-danger">
                @foreach ($errors->all() as $error)
                    {{ $error }}
                    @endforeach
                </div>
            @endif
            @if(getAppSettings('enable_stripe') and !$isValidStripeKeys)
               <div class="alert alert-danger">
                    {{  __tr('Stripe is not correctly configured, Invalid Keys. Please contact administrator') }}
               </div>
               @endif
               <div class="card mb-4">
                   <div class="card-body">
                       @if (request('success') == true and request('message'))
                           <div class="alert alert-success">
                               {{ request('message') }}
                           </div>
                       @elseif(request('message'))
                           <div class="alert alert-warning">
                               {{ request('message') }}
                           </div>
                       @endif
                       @if ($currentPlan and ($isManualSubscription or $subscriber->subscribed($currentPlan['id'])))
                           @if ($subscriber->subscription($currentPlan['id'])?->onTrial())
                               <div class="alert alert-warning">
                                   {{  __tr('You are on trial until __trialEndsAt__', [
                                       '__trialEndsAt__' => formatDateTime($subscriber->trialEndsAt($currentPlan['id']))
                                   ]) }}
                               </div>
                           @endif
                           @if ($subscriber->subscription($currentPlan['id'])?->onGracePeriod())
                               <div class="alert alert-warning">
                                   {{  __tr('Subscription has been cancelled and you are on the grace period till __endsAt__', [
                                       '__endsAt__' => formatDateTime($subscriber->subscription($currentPlan['id'])->ends_at)
                                   ]) }}
                               </div>
                           @endif
                           <fieldset class="mb-4">
                               <legend>{{ __tr('Current Plan') }}</legend>
                               <h2 class="text-primary">{{ $planDetails[$currentPlan['id']]['title'] }}</h2>
                               @foreach ($planStructure[$currentPlan['id']]['features'] as $featureKey => $featureValue)
                                   @php
                                        $structureFeatureValue = $featureValue;
                                       $featureValue = $planDetails[$currentPlan['id']]['features'][$featureKey];
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
                               @if($currentSubscription->charges)
                               <label class="my-3 text-primary">
                                {{ formatAmount($currentSubscription->charges, true) }} / {{ $planDetails[$currentPlan['id']]['charges'][$currentSubscription->charges_frequency]['title'] ?? $currentSubscription->charges_frequency ?? '' }}
                            </label>
                               @else
                               @foreach ($planDetails[$currentPlan['id']]['charges'] as $itemKey => $itemValue)
                                   @if ($planSelectorId === $currentPlan['id'] . '___' . $itemKey)
                                       <label class="my-3 text-primary">
                                           {{ formatAmount($itemValue['charge'], true) }} /
                                           {{ $itemKey }}
                                       </label>
                                   @endif
                               @endforeach
                               @endif
                           </fieldset>
                           @if ($currentSubscription->ends_at ?? null)
                               <h4 class="text-orange">{{  __tr('Expiry Date: __expiryDate__', [
                                '__expiryDate__' => formatDate($currentSubscription->ends_at)
                               ]) }}</h4>
                           @endif
                           @if ($isCashierSubscription and !$subscriber->subscription($currentPlan['id'])?->canceled())
                               <a data-show-processing="true" class="lw-ajax-link-action btn btn-danger" href="{{ route('subscription.write.cancel') }}">
                                   {{ __tr('Cancel Subscription') }}
                               </a>
                           @endif
                       @else
                           @if ($freePlanDetails['enabled'])
                               <fieldset class="mb-4">
                                   <legend>{{ __tr('Current Plan') }}</legend>
                                   <h2 class="text-primary">{{ $freePlanDetails['title'] }}</h2>
                                   @foreach ($freePlanStructure['features'] as $featureKey => $featureValue)
                                       @php
                                            $structureFeatureValue = $featureValue;
                                           $featureValue = $freePlanDetails['features'][$featureKey];
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
                               </fieldset>
                           @else
                               <div class="alert alert-warning">
                                   {{ __tr('There are no active plan') }}
                               </div>
                           @endif
                       @endif
                       @if(getAppSettings('enable_stripe') and $isValidStripeKeys and $isCashierSubscription)
                       <a class="btn btn-default" href="{{ route('subscription.read.billing_portal') }}">
                           {{ __tr('Go to Billing Portal') }}
                       </a>
                       @if ($currentPlan and $isCashierSubscription and $subscriber->subscription($currentPlan['id']) and $subscriber->subscription($currentPlan['id'])->canceled() and $subscriber->subscription($currentPlan['id'])->onGracePeriod())
                           <a data-show-processing="true" class="lw-ajax-link-action btn btn-success"
                               href="{{ route('subscription.write.resume') }}">
                               {{ __tr('Resume Subscription') }}
                           </a>
                           <div class="note text-muted my-2">
                               {{ __tr('You will be able to resume subscription while on grace period') }}</div>
                       @endif

                       @if ($currentPlan and $isCashierSubscription and $subscriber->hasIncompletePayment($currentPlan['id']))
                           <div>
                               <a data-show-processing="true" class="lw-ajax-link-action btn btn-primary"
                                   href="{{ route('cashier.payment', $subscriber->latestPayment()->id) }}">
                                   {{ __tr('Please confirm your payment.') }}
                               </a>
                           </div>
                       @endif
                       @endif
                   </div>
               </div>
               @if ($currentPlan and $hasPlansForPurchase)
                   <div class="card">
                       <div class="card-header">
                           {{ __tr('Change Plan') }}
                       </div>
                       <div class="card-body" x-data="{selectedPlanFrequencyNew:null}">
                            <div class="row">
                               @foreach ($planStructure as $planKey => $plan)
                                   @php
                                       $planId = $plan['id'];
                                       $features = $plan['features'];
                                       $plan = $planDetails[$planKey];
                                       if (!$plan['enabled']) {
                                           continue;
                                       }
                                   @endphp
                                   <div class="col-xl-4 col-sm-12">
                                    <fieldset class="">
                                        <legend>{{ $plan['title'] }}</legend>
                                        @foreach ($features as $featureKey => $featureValue)
                                            @php
                                                $structureFeatureValue = $featureValue;
                                                $featureValue = $planDetails[$planKey]['features'][$featureKey];
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
                                                    @if(isset($featureValue['limit_duration']))
                                                 {{ ($featureValue['limit_duration']) }}
                                             @endif
                                                @endif
                                                {{ ($structureFeatureValue['description']) }}
                                                @endif
                                            </div>
                                        @endforeach
                                        <div class="text-lg mt-4">
                                            @foreach ($plan['charges'] as $itemKey => $itemValue)
                                            @php
                                                if(!$itemValue['enabled']) {
                                                    continue;
                                                }
                                            @endphp
                                                @if ($planSelectorId !== $planId . '___' . $itemKey)
                                                    <div class="form-group my-2 text-primary">
                                                        <label class="control-label" for="{{ $planId }}{{ $itemKey }}">
                                                            <input x-model="selectedPlanFrequencyNew" class="form-control-radio" type="radio" name="plan"
                                                            id="{{ $planId }}{{ $itemKey }}"
                                                            value="{{ $planId }}___{{ $itemKey }}">
                                                            {{ formatAmount($itemValue['charge'], true) }} /
                                                            {{ $itemKey }}
                                                        </label>
                                                    </div>
                                                @endif
                                            @endforeach
                                        </div>
                                    </fieldset>
                                   </div>
                               @endforeach
                            </div>
                               @if ($isCashierSubscription && getAppSettings('enable_stripe') and $isValidStripeKeys)
                               <form class="lw-ajax-form" data-show-processing="true" action="{{ route('subscription.write.change') }}" method="post">
                                @csrf
                                <input type="hidden" name="plan" x-model="selectedPlanFrequencyNew">
                                <button value="stripe" type="submit" class="mt-4 btn btn-primary">
                                    {{ __tr('Change Plan') }}
                                </button>
                                </form>
                               @else
                               @if (getAppSettings('enable_upi_payment') or getAppSettings('enable_bank_transfer'))
                               <fieldset>
                                <!-- ----------change Subscription -------------- -->
                                <legend>{{  __tr('Manual/Prepaid Subscription') }}</legend>
                                @if ($existingManualSubscriptionPendingRequest)
                                <div class="alert alert-danger my-5">
                                 {{  __tr('Your already have pending request for manual subscription change, please wait once it get confirmed') }}
                                </div>
                                @else
                                  <form action="{{ route('vendor.subscription_manual_pay') }}" method="post" id="manual-pay-form">
                                        @csrf
                                        {{-- managed value using alpine --}}
                                        <input type="hidden" name="selected_plan" x-model="selectedPlanFrequencyNew">
                                        {{--  paypal payment button --}}
                                        @if (getAppSettings('enable_paypal'))
                                        <label for="lPaypalPaymentOption" class="mr-4"><h3><input type="radio" id="lPaypalPaymentOption" name="payment_method" value="paypal"><img height="100" src="{{ asset('imgs/pay_pal.png') }}"> </h3></label>
                                    @endif
                                    {{--  /paypal payment button --}}
                                    {{--  UPI payment button --}}
                                        @if (getAppSettings('enable_upi_payment'))
                                            <label for="lwUpiPaymentOption" class="mr-4"><h3><input type="radio" id="lwUpiPaymentOption" name="payment_method" value="upi"> {{  __tr('Pay with any UPI') }} <img height="100" src="{{ asset('imgs/upi-icon.png') }}"> </h3></label>
                                        @endif
                                        {{--  /UPI payment button --}}
                                        {{--  bank transfer payment button --}}
                                        @if (getAppSettings('enable_bank_transfer'))
                                            <label for="lwBankTransferPaymentOption" class="mr-4"><h3><input type="radio" id="lwBankTransferPaymentOption" name="payment_method" value="bank_transfer"> {{  __tr('Pay with Bank Transfer') }} <img height="100" src="{{ asset('imgs/bank-transfer.svg') }}"> </h3></label>
                                        @endif
                                        {{--  /bank transfer payment button --}}
                                        <div class="my-3">
                                            <hr>
                                            <button type="submit" class="btn btn-primary btn-lg " href="">{{  __tr('Continue') }}</button>
                                           </div>
                                    </form>
                                    @endif
                                    <!-- ----------/change Subscription -------------- -->
                                </fieldset>
                                @endif
                               @endif
                       </div>
                   </div>
               @endif
               @if (!$currentPlan)
                   <div class="card">
                    <div class="card-header">
                        {{  __tr('Subscribe Paid Plans') }}
                    </div>
                       <div class="card-body py-0" x-data="{selectedPlanFrequencyNew:null}">
                               <div class="form-group">
                                   <div class="mb-4 row">
                                       @foreach ($planStructure as $planKey => $plan)
                                           @php
                                               $planId = $plan['id'];
                                               $features = $plan['features'];
                                               $charges = $planDetails[$planKey]['charges'];
                                               if (!$planDetails[$planKey]['enabled']) {
                                                   continue;
                                               }
                                           @endphp
                                           <div class="col-xl-4 col-sm-12">
                                           <fieldset class="">
                                               <legend>{{ $planDetails[$planKey]['title'] }}</legend>
                                               @foreach ($features as $featureKey => $featureValue)
                                                   @php
                                                       $featureValue = $planDetails[$planKey]['features'][$featureKey];
                                                   @endphp
                                                   <div class="my-2">
                                                    @if (isset($featureValue['type']) and ($featureValue['type'] == 'switch'))
                                                    @if (isset($featureValue['limit']) and $featureValue['limit'])
                                                    <i class="fa fa-check mr-2 text-success"></i>
                                                    @else
                                                    <i class="fa fa-times mr-2 text-danger"></i>
                                                    @endif
                                                    {{ ($featureValue['description']) }}
                                                    @else
                                                       <i class="fa fa-check text-success mr-2"></i>
                                                       @if (isset($featureValue['limit']) and $featureValue['limit'] < 0)
                                                           {{ __tr('Unlimited') }}
                                                       @elseif(isset($featureValue['limit']))
                                                           {{ $featureValue['limit'] }}
                                                           @if(isset($featureValue['limit_duration']))
                                                                {{ ($featureValue['limit_duration']) }}
                                                            @endif
                                                        @endif
                                                        {{ ($featureValue['description']) }}
                                                        </div>
                                                        @endif
                                                    @endforeach
                                                    <div class="text-lg mt-4">
                                                        @foreach ($charges as $itemKey => $itemValue)
                                                            @php
                                                                if(!$itemValue['enabled']) {
                                                                    continue;
                                                                }
                                                            @endphp
                                                            @if ($planSelectorId !== $planId . '___' . $itemKey)
                                                                <div class="my-2 text-primary">
                                                                    <input x-model="selectedPlanFrequencyNew" type="radio" name="plan"
                                                                        id="{{ $planId }}{{ $itemKey }}"
                                                                        value="{{ $planId }}___{{ $itemKey }}">
                                                                    <label for="{{ $planId }}{{ $itemKey }}">
                                                                        {{ formatAmount($itemValue['charge'], true) }} /
                                                                        {{ $itemKey }}</label>
                                                                </div>
                                                            @endif
                                                        @endforeach
                                                    </div>
                                                </fieldset>
                                            </div>
                                       @endforeach
                                   </div>
                                   @if($hasPlansForPurchase)
                                   @if ($existingManualSubscriptionPendingRequest)
                                   <div class="alert alert-danger my-5">
                                    {{  __tr('Your already have pending request for manual subscription, please wait once it get confirmed') }}
                                   </div>
                                   @else
                                   @if (getAppSettings('enable_stripe') and $isValidStripeKeys)
                                   <fieldset>
                                        <legend for="card-element">
                                            <i class="fab fa-cc-stripe"></i> {{ __tr('Auto Subscription - Credit or Debit card') }}
                                        </legend>
                                        <form action="{{ route('subscription.write.create') }}" method="post" id="payment-form"  data-secret="{{ $intent->client_secret }}">
                                            @csrf
                                            {{-- managed value using alpine --}}
                                            <input type="hidden" name="plan" x-model="selectedPlanFrequencyNew">
                                            <div id="card-element" class="rounded d-block mb-3 border p-3 col-sm-6 bg-white">
                                                <!-- A Stripe Element will be inserted here. -->
                                            </div>
                                            <!-- Used to display form errors. -->
                                            <div id="card-errors" role="alert" class="alert alert-danger d-none"></div>
                                            <button type="submit" class="mt-4 btn btn-primary">
                                                {{ __tr('Subscribe Now') }}
                                            </button>
                                    </form>
                                    </fieldset>
                                    @endif
                            <fieldset>
                                 <!--  -----------New Subscription -------------  -->
                               <legend>{{  __tr('Manual/Prepaid Subscription') }}</legend>
                               @if($isExtendedLicence)
                                 <form action="{{ route('vendor.subscription_manual_pay') }}" method="post" id="manual-pay-form">
                                    @csrf
                                    {{-- managed value using alpine --}}
                                    <input type="hidden" name="selected_plan" x-model="selectedPlanFrequencyNew">
                                    {{--  paypal payment button --}}
                              @if (getAppSettings('enable_paypal'))
                                    <label for="lPaypalPaymentOption" class="mr-4"><h3><input type="radio" id="lPaypalPaymentOption" name="payment_method" value="paypal"><img height="100" src="{{ asset('imgs/pay_pal.png') }}"> </h3></label>
                              @endif
                              {{--  /paypal payment button --}}
                               {{-- UPI payment button  --}}
                               @if (getAppSettings('enable_upi_payment'))
                                    <label for="lwUpiPaymentOption" class="mr-4"><h3><input type="radio" id="lwUpiPaymentOption" name="payment_method" value="upi"> {{  __tr('Pay with any UPI') }} <img height="100" src="{{ asset('imgs/upi-icon.png') }}"> </h3></label>
                               @endif
                                {{-- /UPI payment button  --}}
                                 {{-- bank transfer payment button  --}}
                               @if (getAppSettings('enable_bank_transfer'))
                                    <label for="lwBankTransferPaymentOption" class="mr-4"><h3><input type="radio" id="lwBankTransferPaymentOption" name="payment_method" value="bank_transfer"> {{  __tr('Pay with Bank Transfer') }} <img height="100" src="{{ asset('imgs/bank-transfer.svg') }}"> </h3></label>
                               @endif
                               {{-- /bank transfer payment button  --}}
                               <div class="my-3">
                                <hr>
                                <button type="submit" class="btn btn-primary btn-lg " href="">{{  __tr('Continue') }}</button>
                               </div>
                                </form>
                                @else
                                <div class="alert alert-light">
                                    {{  __tr('Not available, please contact administrator') }}
                                </div>
                                @endif
                                 <!--  ----------/New Subscription -------------  -->
                            </fieldset>
                            @endif
                               @else
                               <div class="alert alert-warning">
                                   {{  __tr('No plans to subscribe') }}
                               </div>
                               @endif
                       </div>
                   </div>
           </div>
               @endif
               @if (getAppSettings('enable_stripe') and getAppSettings('stripe_enable_invoice_list') and $isValidStripeKeys)
               <div class="card mt-4">
                <div class="card-header">
                    {{  __tr('Stripe Invoices') }}
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table mt-4 table-striped" id="invoicesTable">
                            <thead>
                                <tr>
                                    <th>{{  __tr('Number') }}</th>
                                    <th>{{  __tr('Date') }}</th>
                                    <th>{{  __tr('Total') }}</th>
                                    <th>{{  __tr('Invoice Download') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                            @foreach ($invoices as $invoice)
                                <tr>
                                    <td>{{ $invoice->number }}</td>
                                    <td>{{ $invoice->date()->toDayDateTimeString() }}</td>
                                    <td>{{ $invoice->total() }}</td>
                                    <td><a
                                            href="{{ route('subscription.read.download_invoice', [$invoice->id]) }}">{{ __tr('Download') }}</a>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif
           </div>
       </div>
   </div>
   @endif
@endsection
@if(!isset($message))
@push('appScripts')
<script>
    $(document).ready(function() {
    'use strict';
    $('#invoicesTable').DataTable({
            ajax:false,
            serverSide: false,
            // "ordering": false,
            "order": [[ 1, "desc" ]],
            processing: false,
            formatNumber: function (numberValue) {
                return __Utils.formatAsLocaleNumber(numberValue);
            },
        });
    } );
</script>
@if(getAppSettings('enable_stripe') and $isValidStripeKeys and !$existingManualSubscriptionPendingRequest)
    @if(!$currentPlan and $hasPlansForPurchase)
    <script src="https://js.stripe.com/v3/"></script>
    @endif
    <script>
        (function(){
        'use strict';
        @if(!$currentPlan and $hasPlansForPurchase)
        var stripe = Stripe('{{ config("cashier.key") }}');
        // Create an instance of Elements.
        var elements = stripe.elements();
        // Custom styling can be passed to options when creating an Element.
        // (Note that this demo uses a wider set of styles than the guide below.)
        var style = {
            base: {
                color: '#32325d',
                fontFamily: '"Helvetica Neue", Helvetica, sans-serif',
                fontSmoothing: 'antialiased',
                fontSize: '16px',
                '::placeholder': {
                    color: '#aab7c4'
                }
            },
            invalid: {
                color: '#fa755a',
                iconColor: '#fa755a'
            }
        };

        // Create an instance of the card Element.
        var card = elements.create('card', {
            style: style
        });

        // Add an instance of the card Element into the `card-element` <div>.
        card.mount('#card-element');
        // Handle real-time validation errors from the card Element.
        card.on('change', function(event) {
            var displayError = document.getElementById('card-errors');
            if (event.error) {
                displayError.textContent = event.error.message;
            } else {
                displayError.textContent = '';
            }
        });

        // Handle form submission.
        var form = document.getElementById('payment-form'),
            cardHolderName = document.getElementById('cardholder-name'),
            clientSecret = form.dataset.secret;

        form.addEventListener('submit', async function(event) {
            event.preventDefault();
            if ($(form).serializeArray().length < 2) {
                showWarnMessage('{{ __tr('Please select plan first') }}');
                return;
            }
            // check if has required information
            @if (!getVendorSettings('city') or !getVendorSettings('country_code') or !getVendorSettings('address') or !getVendorSettings('postal_code') or !getVendorSettings('state') or !getVendorSettings('contact_email') or !getVendorSettings('contact_phone'))
                showAlert("{{ __tr('Please check that you have fulfilled business information in General settings like address, city etc') }}", 'error');
                __Utils.throwError("{{ __tr('Missing business information.') }}");
            @endif

            const {
                setupIntent,
                error
            } = await stripe.confirmCardSetup(
                clientSecret, {
                    payment_method: {
                        card,
                         billing_details: {
                             "address": {
                                    "city": "{{ getVendorSettings('city') }}",
                                    "country": "{{ getVendorSettings('country_code') }}",
                                    "line1": "{{ getVendorSettings('address') }}",
                                    // "line2": null,
                                    "postal_code": "{{ getVendorSettings('postal_code') }}",
                                    "state": "{{ getVendorSettings('state') }}"
                                },
                                "email": "{{ getVendorSettings('contact_email') }}",
                                name: "{{ getUserAuthInfo('profile.full_name') }}",
                                "phone": "{{ getVendorSettings('contact_phone') }}"
                         }
                    }
                }
            );

            if (error) {
                // Inform the user if there was an error.
                var errorElement = document.getElementById('card-errors');
                errorElement.textContent = error.message;
                showAlert(error.message, "error");
            } else {
                // Send the token to your server.
                stripeTokenHandler(setupIntent);
            }
        });

        // Submit the form with the token ID.
        function stripeTokenHandler(setupIntent) {
            // Insert the token ID into the form so it gets submitted to the server
            var form = document.getElementById('payment-form');
            var hiddenInput = document.createElement('input');
            hiddenInput.setAttribute('type', 'hidden');
            hiddenInput.setAttribute('name', 'paymentMethod');
            hiddenInput.setAttribute('value', setupIntent.payment_method);
            form.appendChild(hiddenInput);
            // Submit the form
            form.submit();
        }
        @endif
    })();
    </script>
     @endif
@endpush
@endif