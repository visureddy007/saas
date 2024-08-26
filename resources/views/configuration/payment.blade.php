<!-- Page Heading -->
<h1>
    <?= __tr('Payment Settings') ?>
</h1>
<!-- /Page Heading -->
<?php $isExtendedLicence = (getAppSettings('product_registration', 'licence') === 'dee257a8c3a2656b7d7fbe9a91dd8c7c41d90dc9'); ?>
<!-- Payment Setting Form -->
@if(!$isExtendedLicence)
<div class="alert alert-warning my-3">
	<strong title="Extended Licence Required"><?= __tr('Extended Licence Required') ?></strong> <br>
	<?= __tr('To charge customers you need to buy an Extended licence. While you can test Stripe Gateway with Regular licence, But you need to purchase Extended licence to use Live Keys.') ?>
</div>
@endif
<hr>
        <!-- stripe settings -->
        <fieldset class="lw-fieldset mb-3" x-data x-cloak>
            <legend class="lw-fieldset-legend">
                <i class="fab fa-stripe"></i> <?= __tr('Stripe Gateway for Subscription (Recurring - Auto Debit)') ?>
            </legend>
            <!-- Payment Setting Form -->
            <form class="lw-ajax-form lw-form" method="post" data-callback="onPaymentGatewayFormCallback"
                action="<?= route('manage.configuration.write', ['pageType' => request()->pageType]) ?>">
                <!-- input field body -->
                <div class="form-group mt-2">

                    <!-- Enable stripe Checkout field -->
                    <div class="form-group pt-3">
                        <label for="lwEnableStripe">
                            <input type="hidden" name="enable_stripe" value="0">
                            <input type="checkbox" id="lwEnableStripe" data-lw-plugin="lwSwitchery" name="enable_stripe"
                                <?=$configurationData['enable_stripe']==true ? 'checked' : '' ?>>
                            <?= __tr('Enable Stripe Subscription Checkout') ?>
                        </label>
                    </div>
                    <!-- / Enable stripe Checkout field -->
                    <span id="lwStripeCheckoutContainer">
                        <div class="p-2 border rounded">
                            <h3>{{  __tr('Options') }}</h3>
                            <span class="mr-4">
                                <x-lw.checkbox id="lwEnableCalculateTaxes" data-size="small" data-color="orange" name="stripe_enable_calculate_taxes" :offValue="0" :checked="getAppSettings('stripe_enable_calculate_taxes')" data-lw-plugin="lwSwitchery" :label="__tr('Calculate Taxes by Stripe')" />
                            </span>
                            <x-lw.checkbox id="lwEnableInvoices" data-size="small" data-color="orange" name="stripe_enable_invoice_list" :offValue="0" :checked="getAppSettings('stripe_enable_invoice_list')" data-lw-plugin="lwSwitchery" :label="__tr('Enable Stripe Invoices List Table')" />
                        </div>
                        <!-- use testing stripe checkout input fieldset -->
                        <fieldset class="lw-fieldset mb-3">
                            <!-- use testing input radio field -->
                            <legend class="lw-fieldset-legend">
                                <div class="custom-control custom-radio custom-control-inline">
                                    <input type="radio" id="lwUseStripeCheckoutTest" name="use_test_stripe"
                                        class="custom-control-input" value="1" <?=$configurationData['use_test_stripe']==true
                                        ? 'checked' : '' ?>>
                                    <label class="custom-control-label" for="lwUseStripeCheckoutTest">
                                        <?= __tr('Use Testing') ?>
                                    </label>
                                </div>
                    </legend>
                    <!-- /use testing input radio field -->

                    <!-- show after added testing stipe checkout information -->
                    <div class="btn-group" id="lwTestStripeCheckoutExists">
                        <button type="button" disabled="true" class="btn btn-success lw-btn">
                            <?= __tr('Testing Stripe Checkout keys are installed.') ?>
                        </button>
                        <button type="button" class="btn btn-light lw-btn" id="lwUpdateTestStripeCheckout">
                            <?= __tr('Update') ?>
                        </button>
                    </div>
                    <!-- show after added testing stipe checkout information -->

                    <!-- stripe test secret key exists hidden field -->
                    <input type="hidden" name="stripe_test_keys_exist" id="lwStripeTestKeysExist"
                        value="<?= $configurationData['stripe_testing_secret_key'] ?>" />
                    <!-- stripe test secret key exists hidden field -->

                    <div id="lwTestStripeInputField">
                        <!-- Testing Secret Key Key -->
                        <div class="mb-3">
                            <label for="lwStripeTestSecretKey">
                                <?= __tr('Secret Key') ?>
                            </label>
                            <input type="text" class="form-control form-control-user" value=""
                                id="lwStripeTestSecretKey" name="stripe_testing_secret_key"
                                placeholder="<?= __tr('Secret Key') ?>">
                        </div>
                        <!-- / Testing Secret Key Key -->

                        <!-- Testing Publish Key -->
                        <div class="mb-3">
                            <label for="lwStripeTestPublishKey">
                                <?= __tr('Publish Key') ?>
                            </label>
                            <input type="text" class="form-control form-control-user" value=""
                                id="lwStripeTestPublishKey" name="stripe_testing_publishable_key"
                                placeholder="<?= __tr('Publish Key') ?>">
                        </div>
                        <!-- / Testing Publish Key -->

                        <!-- Stripe Webhook Secret (optional) -->
                        <div class="mb-3">
                            <label for="lwStripeTestWebhookSecret">
                                <?= __tr('Stripe Webhook Secret (optional)') ?>
                            </label>
                            <input type="text" class="form-control form-control-user" value=""
                                id="lwStripeTestWebhookSecret" name="stripe_testing_webhook_secret"
                                placeholder="<?= __tr('Stripe Webhook Secret (optional)') ?>">
                        </div>
                        <!-- / Stripe Webhook Secret (optional) -->
                    </div>
                </fieldset>
                <!-- /use testing paypal checkout input fieldset -->

                <!-- use live stripe checkout input fieldset -->
                <fieldset class="lw-fieldset mb-3">
                    <!-- use live input radio field -->
                    <legend class="lw-fieldset-legend">
                        <div class="custom-control custom-radio custom-control-inline">
                            <input type="radio" id="lwUseStripeCheckoutLive" name="use_test_stripe"
                                class="custom-control-input" value="0" <?=$configurationData['use_test_stripe']==false
                                ? 'checked' : '' ?>>
                            <label class="custom-control-label" for="lwUseStripeCheckoutLive">
                                <?= __tr('Use Live') ?>
                            </label>
                        </div>
                    </legend>
                    <!-- /use live input radio field -->
                    @if($isExtendedLicence)
                    <!-- show after added Live stripe checkout information -->
                    <div class="btn-group" id="lwLiveStripeCheckoutExists">
                        <button type="button" disabled="true" class="btn btn-success lw-btn">
                            <?= __tr('Live Stripe Checkout keys are installed.') ?>
                        </button>
                        <button type="button" class="btn btn-light lw-btn" id="lwUpdateLiveStripeCheckout">
                            <?= __tr('Update') ?>
                        </button>
                    </div>
                    <!-- show after added Live stripe checkout information -->

                    <!-- stripe live secret key exists hidden field -->
                    <input type="hidden" name="stripe_live_keys_exist" id="lwStripeLiveKeysExist"
                        value="<?= $configurationData['stripe_live_secret_key'] ?>" />
                    <!-- stripe live secret key exists hidden field -->

                    <div id="lwLiveStripeInputField">
                        <div class="alert border-danger text-danger">
                            {{  __tr('While going live you may need to clear your existing subscription. It only may required if you are switching from test mode.') }}
                           <div>
                            <a class="btn btn-danger btn-sm lw-ajax-link-action mt-4" data-show-processing="true" data-method="post" data-confirm="{{ __tr('Are you sure? You want to delete all the subscriptions entries.') }}" href="{{ route('central.subscription.write.delete_all_entries') }}"> <i class="fa fa-cog"></i> {{  __tr('Delete existing subscription entries') }}</a>
                           </div>
                        </div>
                        <!-- Live Secret Key Key -->
                        <div class="mb-3">
                            <label for="lwStripeLiveSecretKey">
                                <?= __tr('Secret Key') ?>
                            </label>
                            <input type="text" class="form-control form-control-user" value=""
                                id="lwStripeLiveSecretKey" name="stripe_live_secret_key"
                                placeholder="<?= __tr('Secret Key') ?>">
                        </div>
                        <!-- / Live Secret Key Key -->

                        <!-- Live Publish Key -->
                        <div class="mb-3">
                            <label for="lwStripeLivePublishKey">
                                <?= __tr('Publish Key') ?>
                            </label>
                            <input type="text" class="form-control form-control-user" value=""
                                id="lwStripeLivePublishKey" name="stripe_live_publishable_key"
                                placeholder="<?= __tr('Publish Key') ?>">
                        </div>
                        <!-- / Live Publish Key -->

                        <!-- Live Stripe Webhook Secret (optional) -->
                        <div class="mb-3">
                            <label for="lwStripeLiveWebhookSecret">
                                <?= __tr('Stripe Webhook Secret (optional)') ?>
                            </label>
                            <input type="text" class="form-control form-control-user" value=""
                                id="lwStripeLiveWebhookSecret" name="stripe_live_webhook_secret"
                                placeholder="<?= __tr('Stripe Webhook Secret (optional)') ?>">
                        </div>
                        <!-- / Live Stripe Webhook Secret (optional) -->
                    </div>
                    @else
					<div class="alert alert-danger">
						{{  __tr('Extended licence required to use live keys') }}
					</div>
					@endif
                </fieldset>
                <!-- /use live stripe checkout input fieldset -->
                <div class="form-group">
                        <!-- Update Button -->
                <a href class="lw-ajax-form-submit-action btn btn-primary btn-user lw-btn-block-mobile">
                    <?= __tr('Save') ?>
                </a>
                <!-- /Update Button -->
                </div>
                <fieldset>
                    <legend>{{  __tr('Auto Stripe Webhook Creation (Recommended)') }}</legend>
                    <div>
                        @if(!config('cashier.secret'))
                            <div class="alert alert-dark">
                                {{  __tr('Stripe keys should be present to create webhook automatically.') }}
                            </div>
                        @endif
                        <div class="alert border-success text-success">
                            {{  __tr('Clicking on this button will create webhook in your stripe account with all required events.') }}
                        <div class="mt-4">
                            <a class="btn btn-success lw-ajax-link-action @if(!config('cashier.secret')) disabled @endif" data-show-processing="true" data-method="post" data-confirm="{{ __tr('Are you sure? It will call Stripe API to create webhook with events and will store it\'s secret into the system.') }}" href="{{ route('manage.configuration.create_stripe_webhook') }}"> <i class="fa fa-cog"></i> {{  __tr('Create Stripe Webhook Automatically') }}</a>
                        </div>
                        </div>
                        @php
                            $testWebHookCreated = getAppSettings('payment_gateway_info', 'auto_stripe_webhook_info.testing.created_at');
                            $liveWebHookCreated = getAppSettings('payment_gateway_info', 'auto_stripe_webhook_info.live.created_at');
                        @endphp
                        <div x-cloak x-data="{lastTestWebhookCreatedAt:'{{ $testWebHookCreated ? formatDateTime($testWebHookCreated) : '' }}',lastLiveWebhookCreatedAt:'{{ $liveWebHookCreated ? formatDateTime($liveWebHookCreated) : '' }}'}" class="my-3">
                            <div x-show="lastTestWebhookCreatedAt">
                                {!! __tr('Last Test Webhook created at __createdAt__', [
                                '__createdAt__' => '<span x-text="lastTestWebhookCreatedAt"></span>'
                            ]) !!}
                            </div>
                            <div x-show="lastLiveWebhookCreatedAt">
                                {!! __tr('Last Live Webhook created at __createdAt__', [
                                '__createdAt__' => '<span x-text="lastLiveWebhookCreatedAt"></span>'
                            ]) !!}
                            </div>
                        </div>
                    </div>
                </fieldset>
                <h2 class="col-12 text-center text-muted my-4">{{  __tr('-- OR --') }}</h2>
                <fieldset>
                    <legend>{{  __tr('Manual Stripe Webhook Creation') }}</legend>
                    <div class="form-group">
                        <label for="lwStripeWebhookUrl">{{ __tr('Stripe Webhook') }}</label>
                        <div class="input-group">
                            <input type="text" class="form-control" readonly id="lwStripeWebhookUrl" value="{{ getViaSharedUrl(route('cashier.webhook')) }}">
                            <div class="input-group-append">
                                <button class="btn btn-outline-light" type="button" onclick="lwCopyToClipboard('lwStripeWebhookUrl')">
                                    <?= __tr('Copy') ?>
                                </button>
                            </div>
                        </div>
                        <div class="alert alert-light my-3">
                            <h3>{{  __tr('Select following events whiles creating webhook') }}</h3>
                            <p>customer.subscription.created, customer.subscription.updated, customer.subscription.deleted, customer.updated, customer.deleted, payment_method.automatically_updated, invoice.payment_action_required, invoice.payment_succeeded</p>
                        </div>
                        <div class="text-danger help-text mt-2 text-sm">{{  __tr('IMPORTANT: It is very important that you should add this Webhook to Stripe account, as all the payment information gets updated using this webhook.') }}</div>
                    </div>
                </fieldset>
                 <!-- / stripe settings -->
            <fieldset>
                <legend>{{  __tr('Note') }}</legend>
                {{  __tr('Please make sure you have enabled billing portal link in your Stripe account') }}
                <a target="_blank" href="https://dashboard.stripe.com/settings/billing/portal">https://dashboard.stripe.com/settings/billing/portal</a>
            </fieldset>
            </span>
            <hr class="my-4">
            <div class="form-group">
                        <!-- Update Button -->
                <a href class="lw-ajax-form-submit-action btn btn-primary btn-user lw-btn-block-mobile">
                    <?= __tr('Save') ?>
                </a>
                <!-- /Update Button -->
                </div>
            </div>
            <!-- / input field body -->
            </form>
        </fieldset>
        <!-- /Payment Setting Form -->

        <!-- Paypal checkout start -->
          <fieldset class="lw-fieldset mb-3" x-data x-cloak>
            <legend class="lw-fieldset-legend">
                <i class="fab fa-paypal"></i> <?= __tr('PayPal') ?>
            </legend>
            <!-- Payment Setting Form -->
            <form class="lw-ajax-form lw-form" method="post" data-callback="onPaymentGatewayFormCallback"
                action="<?= route('manage.configuration.write', ['pageType' => 'paypal_payment']) ?>">
                <!-- input field body -->
                <div class="form-group mt-2">

                    <!-- Enable Paypal Checkout field -->
                    <div class="form-group pt-3">
                        <label for="lwEnablePaypal">
                            <input type="hidden" name="enable_paypal" value="0">
                            <input type="checkbox" id="lwEnablePaypal" data-lw-plugin="lwSwitchery" name="enable_paypal"
                                <?= getAppSettings('enable_paypal')==true ? 'checked' : '' ?>>
                            <?= __tr('Enable PayPal Checkout') ?>
                        </label>
                    </div>
                    <div>
                        <p class="mt-3 ml-3">You can create PayPal credential <a href="https://www.paypal.com/signin?returnUri=https%3A%2F%2Fdeveloper.paypal.com%2Fdeveloper%2Fapplications&intent=developer" target="_blank">click here</a>.</p>

                     </div>
                    <!-- / Enable Paypal Checkout field -->
                    <span id="lwPayPalCheckoutContainer">
                        <!-- use testing Paypal checkout input fieldset -->
                        <fieldset class="lw-fieldset mb-3">
                            <!-- use testing input radio field -->
                            <legend class="lw-fieldset-legend">
                                <div class="custom-control custom-radio custom-control-inline">
                                    <input type="radio" id="lwUsePaypalCheckoutTest"  name="use_test_paypal_checkout"
                                        class="custom-control-input" value="1" <?= getAppSettings('use_test_paypal_checkout')==true
                                        ? 'checked' : '' ?>>
                                    <label class="custom-control-label" for="lwUsePaypalCheckoutTest">
                                        <?= __tr('Use Testing') ?>
                                    </label>
                                </div>
                    </legend>
                    <!-- /use testing input radio field -->

                    <!-- show after added testing Paypal checkout information -->
                    <div class="btn-group" id="lwTestPaypalCheckoutExists">
                        <button type="button" disabled="true" class="btn btn-success lw-btn">
                            <?= __tr('Testing PayPal Checkout keys are installed.') ?>
                        </button>
                        <button type="button" class="btn btn-light lw-btn" id="lwUpdateTestPaypalCheckout">
                            <?= __tr('Update') ?>
                        </button>
                    </div>
                    <!-- show after added testing Paypal checkout information -->

                             <!-- paypal test secret key exists hidden field -->
                             <input type="hidden" name="paypal_test_keys_exist" id="lwPaypalTestKeysExist"
                             value="<?= getAppSettings('paypal_checkout_testing_secret_key') ?>" />
                         <!-- paypal test secret key exists hidden field -->


                    <div id="lwTestPaypalInputField">

                        <!-- Testing Publish Key -->
                        <div class="mb-3">
                            <label for="lwPaypalTestPublishKey">
                                <?= __tr('Client Key') ?>
                            </label>
                            <input type="text" class="form-control form-control-user" value=""
                                id="lwPaypalTestPublishKey" name="paypal_checkout_testing_publishable_key"
                                placeholder="<?= __tr('Client Key') ?>">
                        </div>
                        <!-- / Testing Publish Key -->
                           <!-- Testing Secret Key Key -->
                           <div class="mb-3">
                            <label for="lwPaypalTestSecretKey">
                                <?= __tr('Secret Key') ?>
                            </label>
                            <input type="text" class="form-control form-control-user" value=""
                                id="lwPaypalTestSecretKey" name="paypal_checkout_testing_secret_key"
                                placeholder="<?= __tr('Secret Key') ?>">
                        </div>
                        <!-- / Testing Secret Key Key -->
                    </div>
                </fieldset>
                <!-- /use testing paypal checkout input fieldset -->

                <!-- use live Paypal checkout input fieldset -->
                <fieldset class="lw-fieldset mb-3">
                    <!-- use live input radio field -->
                    <legend class="lw-fieldset-legend">
                        <div class="custom-control custom-radio custom-control-inline">
                            <input type="radio" id="lwUsePaypalCheckoutLive" name="use_test_paypal_checkout"
                                class="custom-control-input" value="0" <?= getAppSettings('use_test_paypal_checkout')==false
                                ? 'checked' : '' ?>>
                            <label class="custom-control-label" for="lwUsePaypalCheckoutLive">
                                <?= __tr('Use Live') ?>
                            </label>
                        </div>
                    </legend>
                    <!-- /use live input radio field -->
                    @if($isExtendedLicence)
                    <!-- show after added Live Paypal checkout information -->
                    <div class="btn-group" id="lwLivePaypalCheckoutExists">
                        <button type="button" disabled="true" class="btn btn-success lw-btn">
                            <?= __tr('Live PayPal Checkout keys are installed.') ?>
                        </button>
                        <button type="button" class="btn btn-light lw-btn" id="lwUpdateLivePaypalCheckout">
                            <?= __tr('Update') ?>
                        </button>
                    </div>
                    <!-- show after added Live Paypal checkout information -->


                    <div id="lwLivePaypalInputField">
                        <!-- Live Publish Key -->
                        <div class="mb-3">
                            <label for="lwPaypalLivePublishKey">
                                <?= __tr('Client Key') ?>
                            </label>
                            <input type="text" class="form-control form-control-user" value=""
                                id="lwPaypalLivePublishKey" name="paypal_checkout_live_publishable_key"
                                placeholder="<?= __tr('Client Key') ?>">
                        </div>
                        <!-- / Live Client Key -->
                           <!-- Live Secret Key Key -->
                           <div class="mb-3">
                            <label for="lwPaypalLiveSecretKey">
                                <?= __tr('Secret Key') ?>
                            </label>
                            <input type="text" class="form-control form-control-user" value=""
                                id="lwPaypalLiveSecretKey" name="paypal_checkout_live_secret_key"
                                placeholder="<?= __tr('Secret Key') ?>">
                        </div>
                        <!-- / Live Secret Key Key -->
                    </div>
                    @else
					<div class="alert alert-danger">
						{{  __tr('Extended licence required to use live keys') }}
					</div>
					@endif
                </fieldset>
                <!-- /use live Paypal checkout input fieldset -->
            </span>
            <hr class="my-4">
            <div class="form-group">
                        <!-- Update Button -->
                <a href class="lw-ajax-form-submit-action btn btn-primary btn-user lw-btn-block-mobile">
                    <?= __tr('Save') ?>
                </a>
                <!-- /Update Button -->
                </div>
            </div> 
            <!-- / input field body -->
            </form>
        </fieldset>
        <!-- /Payment Setting Form -->
        <!-- /Paypal checkout -->
        <fieldset>
            <legend> <img height="50" src="{{ asset('imgs/upi-icon.png') }}"> {{ __tr('UPI Payments for India - Offline/Manual') }}</legend>
            <div class="alert alert-light my-3">{{  __tr('User will add payment details you need to confirm it manually and update subscription as active etc for the particular vendor from manual subscriptions.') }}</div>
            @if($isExtendedLicence)
            <form class="lw-ajax-form lw-form" method="post"
            action="<?= route('manage.configuration.write', ['pageType' => 'upi_payment']) ?>" x-cloak x-data="{}">
            <div class="col-sm-12 col-md-6 col-lg-4">
                <div class="my-4">
                    <x-lw.checkbox id="lwEnableUpiPayment" :offValue="0" :checked="getAppSettings('enable_upi_payment')" name="enable_upi_payment" data-lw-plugin="lwSwitchery" :label="__tr('Enable UPI Payments')" />
                </div>
                <x-lw.input-field type="text" id="lwUpiPaymentAddress" value="{{ getAppSettings('payment_upi_address') }}" data-form-group-class="" :label="__tr('UPI Address')" name="payment_upi_address" />
                <div class="form-group">
                    <label for="lwUpiCustomerInstructions">{{  __tr('Customer Instructions or Notes') }}</label>
                    <textarea class="form-control" name="payment_upi_customer_notes" id="lwUpiCustomerInstructions" rows="4">{{ getAppSettings('payment_upi_customer_notes') }}</textarea>
                </div>
            <div class="form-group">
                {{-- submit button --}}
            <button type="submit" href class="btn btn-primary btn-user lw-btn-block-mobile">
                <?= __tr('Save') ?>
            </button>
            </div>
         </div>
            </form>
            @else
            <div class="alert alert-danger">
                {{  __tr('Extended licence required enable it') }}
            </div>
            @endif
        </fieldset>
        <fieldset>
            <legend> <i class="fas fa-university"></i> {{ __tr('Bank Transfer - Offline/Manual') }}</legend>
            <div class="alert alert-light my-3">{{  __tr('User will add payment details you need to confirm it manually and update subscription as active etc for the particular vendor from manual subscriptions.') }}</div>
            @if($isExtendedLicence)
            <form class="lw-ajax-form lw-form" method="post"
            action="<?= route('manage.configuration.write', ['pageType' => 'bank_transfer']) ?>" x-cloak x-data="{}">
            <div class="col-sm-12 col-lg-8">
                <div class="my-4">
                    <x-lw.checkbox id="lwEnableBankTransfer" :offValue="0" :checked="getAppSettings('enable_bank_transfer')" name="enable_bank_transfer" data-lw-plugin="lwSwitchery" :label="__tr('Enable Bank Transfer')" />
                </div>
                <div class="form-group">
                    <label for="lwBankTransferInstructions">{{  __tr('Bank Transfer Instructions') }}</label>
                    <textarea class="form-control" name="bank_transfer_instructions" id="lwBankTransferInstructions" rows="10">{{ getAppSettings('bank_transfer_instructions') }}</textarea>
                </div>
            <div class="form-group">
                {{-- submit button --}}
            <button type="submit" href class="btn btn-primary btn-user lw-btn-block-mobile">
                <?= __tr('Save') ?>
            </button>
            </div>
        </div>
            </form>
            @else
            <div class="alert alert-danger">
                {{  __tr('Extended licence required enable it') }}
            </div>
            @endif
        </fieldset>
@push('appScripts')
<script>
        (function($) {
        'use strict';
    /*********** Stripe Enable / Disable Checkout start here ***********/
	var isStripeCheckoutEnable = $('#lwEnableStripe').is(':checked'),
		isUseStripeCheckoutTest = $("#lwUseStripeCheckoutTest").is(':checked'),
		isUseStripeCheckoutLive = $("#lwUseStripeCheckoutLive").is(':checked');

	if (!isStripeCheckoutEnable) {
		$('#lwStripeCheckoutContainer').addClass('lw-disabled-block-content d-none');
	}
	$("#lwEnableStripe").on('change', function(event) {
		isStripeCheckoutEnable = $(this).is(":checked");
		//check is enable false then add class
		if (!isStripeCheckoutEnable) {
			$("#lwStripeCheckoutContainer").addClass('lw-disabled-block-content d-none');
			//else remove class
		} else {
			$("#lwStripeCheckoutContainer").removeClass('lw-disabled-block-content d-none');
		}
	});
  
	//check stripe test mode is true then disable stripe live input field
	if (isUseStripeCheckoutTest) {
		$('#lwUpdateLiveStripeCheckout').attr('disabled', true);
		$('#lwLiveStripeInputField').addClass('lw-disabled-block-content');
		//check stripe test mode is false then disable stripe test input field
	} else if (isUseStripeCheckoutLive) {
		$('#lwUpdateTestStripeCheckout').attr('disabled', true);
		$('#lwTestStripeInputField').addClass('lw-disabled-block-content');
	}

	//check stripe test mode is true on change
	//then disable stripe live input field
	$("#lwUseStripeCheckoutTest").on('change', function(event) {
		var isUseStripeCheckoutTest = $(this).is(':checked');
		if (isUseStripeCheckoutTest) {
			$('#lwUpdateLiveStripeCheckout').attr('disabled', true);
			$('#lwUpdateTestStripeCheckout').attr('disabled', false);
			$('#lwTestStripeInputField').removeClass('lw-disabled-block-content');
			$('#lwLiveStripeInputField').addClass('lw-disabled-block-content');
		}
	});

	//check stripe test mode is false on change
	//then disable stripe test input field
	$("#lwUseStripeCheckoutLive").on('change', function(event) {
		var isUseStripeCheckoutLive = $(this).is(':checked');
		if (isUseStripeCheckoutLive) {
			$('#lwUpdateTestStripeCheckout').attr('disabled', true);
			$('#lwUpdateLiveStripeCheckout').attr('disabled', false);
			$('#lwLiveStripeInputField').removeClass('lw-disabled-block-content');
			$('#lwTestStripeInputField').addClass('lw-disabled-block-content');
		}
	});
	/*********** Stripe Enable / Disable Checkout end here ***********/

	/*********** Stripe Testing Keys setting start here ***********/
	var isTestStripeKeysInstalled = "<?= $configurationData['stripe_testing_publishable_key'] ?>",
		lwTestStripeInputField = $('#lwTestStripeInputField'),
		lwTestStripeCheckoutExists = $('#lwTestStripeCheckoutExists');

	// Check if test stripe keys are installed
	if (isTestStripeKeysInstalled) {
		lwTestStripeInputField.hide();
	} else {
		lwTestStripeCheckoutExists.hide();
	}
	// Update stripe checkout testing keys
	$('#lwUpdateTestStripeCheckout').on('click', function() {
		$("#lwStripeTestKeysExist").val(0);
		lwTestStripeInputField.show();
		lwTestStripeCheckoutExists.hide();
	});
	/*********** Stripe Testing Keys setting end here ***********/

	/*********** Stripe Live Keys setting start here ***********/
	var isLiveStripePublishKeysInstalled = "<?= $configurationData['stripe_live_publishable_key'] ?>",
		lwLiveStripeInputField = $('#lwLiveStripeInputField'),
		lwLiveStripeCheckoutExists = $('#lwLiveStripeCheckoutExists');

	// Check if test Stripe keys are installed
	if (isLiveStripePublishKeysInstalled) {
		lwLiveStripeInputField.hide();
	} else {
		lwLiveStripeCheckoutExists.hide();
	}
	// Update Stripe checkout testing keys
	$('#lwUpdateLiveStripeCheckout').on('click', function() {
		$("#lwStripeLiveKeysExist").val(0);
		lwLiveStripeInputField.show();
		lwLiveStripeCheckoutExists.hide();
	});
	/*********** Stripe Live Keys setting end here ***********/
    //==========================================
      /*********** PayPal Enable / Disable Checkout start here ***********/
	var isPaypalCheckoutEnable = $('#lwEnablePaypal').is(':checked'),
		isUsePaypalCheckoutTest = $("#lwUsePaypalCheckoutTest").is(':checked'),
		isUsePaypalCheckoutLive = $("#lwUsePaypalCheckoutLive").is(':checked');

	if (!isPaypalCheckoutEnable) {
		$('#lwPayPalCheckoutContainer').addClass('lw-disabled-block-content d-none');
	}
	$("#lwEnablePaypal").on('change', function(event) {
		isPaypalCheckoutEnable = $(this).is(":checked");
		//check is enable false then add class
		if (!isPaypalCheckoutEnable) {
			$("#lwPayPalCheckoutContainer").addClass('lw-disabled-block-content d-none');
			//else remove class
		} else {
			$("#lwPayPalCheckoutContainer").removeClass('lw-disabled-block-content d-none');
		}
	});
  
	//check paypal test mode is true then disable paypal live input field
	if (isUsePaypalCheckoutTest) {
		$('#lwUpdateLivePaypalCheckout').attr('disabled', true);
		$('#lwLivePaypalInputField').addClass('lw-disabled-block-content');
		//check paypal test mode is false then disable paypal test input field
	} else if (isUsePaypalCheckoutLive) {
		$('#lwUpdateTestPaypalCheckout').attr('disabled', true);
		$('#lwTestPaypalInputField').addClass('lw-disabled-block-content');
	}

	//check paypal test mode is true on change
	//then disable paypal live input field
	$("#lwUsePaypalCheckoutTest").on('change', function(event) {
		var isUsePaypalCheckoutTest = $(this).is(':checked');
		if (isUsePaypalCheckoutTest) {
			$('#lwUpdateLivePaypalCheckout').attr('disabled', true);
			$('#lwUpdateTestPaypalCheckout').attr('disabled', false);
			$('#lwTestPaypalInputField').removeClass('lw-disabled-block-content');
			$('#lwLivePaypalInputField').addClass('lw-disabled-block-content');
		}
	});

	//check paypal test mode is false on change
	//then disable paypal test input field
	$("#lwUsePaypalCheckoutLive").on('change', function(event) {
		var isUsePaypalCheckoutLive = $(this).is(':checked');
		if (isUsePaypalCheckoutLive) {
			$('#lwUpdateTestPaypalCheckout').attr('disabled', true);
			$('#lwUpdateLivePaypalCheckout').attr('disabled', false);
			$('#lwLivePaypalInputField').removeClass('lw-disabled-block-content');
			$('#lwTestPaypalInputField').addClass('lw-disabled-block-content');
		}
	});
	/*********** PayPal Enable / Disable Checkout end here ***********/
//=========================================================================-============-============-=
    /*********** PayPal Testing Keys setting start here ***********/
	var isTestPaypalKeysInstalled = "<?= getAppSettings('paypal_checkout_testing_publishable_key') ?>",
    lwTestPaypalInputField = $('#lwTestPaypalInputField'),
    lwTestPaypalCheckoutExists = $('#lwTestPaypalCheckoutExists');

	// Check if test PayPal keys are installed
	if (isTestPaypalKeysInstalled) {
		lwTestPaypalInputField.hide();
	} else {
		lwTestPaypalCheckoutExists.hide();
	}
	// Update PayPal checkout testing keys
	$('#lwUpdateTestPaypalCheckout').on('click', function() {
		$("#lwPaypalTestKeysExist").val(0);
		lwTestPaypalInputField.show();
		lwTestPaypalCheckoutExists.hide();
	});
	/*********** PayPal Testing Keys setting end here ***********/

	/*********** PayPal Live Keys setting start here ***********/
	var isLiveStripePublishKeysInstalled = "<?= getAppSettings('paypal_checkout_live_publishable_key') ?>",
    lwLivePaypalInputField = $('#lwLivePaypalInputField'),
    lwLivePaypalCheckoutExists = $('#lwLivePaypalCheckoutExists');

	// Check if test PayPal keys are installed
	if (isLiveStripePublishKeysInstalled) {
		lwLivePaypalInputField.hide();
	} else {
		lwLivePaypalCheckoutExists.hide();
	}
	// Update PayPal checkout testing keys
	$('#lwUpdateLivePaypalCheckout').on('click', function() {
		$("#lwPaypalLiveKeysExist").val(0);
		lwLivePaypalInputField.show();
		lwLivePaypalCheckoutExists.hide();
	});
	/*********** PayPal Live Keys setting end here ***********/
    /**********====================== /PayPal chceckout ============================**********/

	/*********** Razorpay Enable / Disable Checkout end here ***********/

	//on payment setting success callback function
	window.onPaymentGatewayFormCallback = function(responseData) {
		//check reaction code is 1 then reload view
		if (responseData.reaction == 1) {
			showConfirmation('Settings Updated Successfully', function() {
                __Utils.viewReload();
            });
		}
	};
})(jQuery);
</script>
@endpush