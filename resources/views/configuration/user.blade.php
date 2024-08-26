<!-- Page Heading -->
<h1><?= __tr('User & Vendor Settings') ?></h1>
<!-- /Page Heading -->
<hr>
<!-- User Setting Form -->
<form class="lw-ajax-form lw-form" method="post" action="<?= route('manage.configuration.write', ['pageType' => request()->pageType]) ?>">
    <fieldset class="mb-4" x-data="{isNewVendorRegistrationEnabled: {{ getAppSettings('enable_vendor_registration') ? 1 : 0 }} }">
        <legend>{{  __tr('New Vendor Registration') }}</legend>
        <x-lw.checkbox :offValue="0" id="lwEnableVendorRegistration" name="enable_vendor_registration" data-lw-plugin="lwSwitchery" @click="isNewVendorRegistrationEnabled = !isNewVendorRegistrationEnabled" :checked="getAppSettings('enable_vendor_registration')" :label="__tr('Enable Vendor Registration')" value="1" />
        <div class="my-4 mb-sm-0" x-show="!isNewVendorRegistrationEnabled">
            <div class="help-text my-4">{{  __tr('If you want to disable new vendor registration, You can do it from here. Also you can place the message for the users, like contact info etc so they can contact you directly and then you can add them as vendor manually. Leave Blank if you do not want to show Register link or any Message') }}</div>
            <textarea rows="10" id="lwMessageIfNewVendorRegistrationIsOff" class="lw-form-field form-control" placeholder="{{ __tr('Leave Blank If not required') }}" name="message_for_disabled_registration">{!! getAppSettings('message_for_disabled_registration') !!}</textarea>
        </div>
    <!-- Activation Required For New User -->
	<div class="form-group mt-4" x-show="isNewVendorRegistrationEnabled">
		<!-- Activation required for new user -->
		<label><?= __tr('User Email Activation required for New Vendor') ?></label>
		<!-- /Activation required for new user -->
		<!-- Yes -->
		<div class="custom-control custom-radio custom-control-inline">
			<input type="radio" id="activation_required_yes" name="activation_required_for_new_user" class="custom-control-input" value="1" <?= $configurationData['activation_required_for_new_user'] == true ? 'checked' : '' ?>>
			<label class="custom-control-label" for="activation_required_yes"><?= __tr('Yes') ?></label>
		</div>
		<!-- /Yes -->
		<!-- No -->
		<div class="custom-control custom-radio custom-control-inline">
			<input type="radio" id="activation_required_no" name="activation_required_for_new_user" class="custom-control-input" value="0" <?= $configurationData['activation_required_for_new_user'] == false ? 'checked' : '' ?>>
			<label class="custom-control-label" for="activation_required_no"><?= __tr('No') ?></label>
		</div>
		<!-- /No -->
	</div>
	<!-- /Activation Required For New User -->
	 <!-- Welcome email For New User -->
	 <div class="mt-3" x-show="isNewVendorRegistrationEnabled" x-data="{isWelcomeEmailEnabled: {{ getAppSettings('send_welcome_email') ? 1 : 0 }} }">
		<x-lw.checkbox :offValue="0" id="lwEnableWelcomeEmail" name="send_welcome_email" data-lw-plugin="lwSwitchery"  @click="isWelcomeEmailEnabled = !isWelcomeEmailEnabled" :checked="getAppSettings('send_welcome_email')" :label="__tr('Send Welcome email to newly registered vendor')" value="1" />
		<div class="my-4 mb-sm-0" x-show="isWelcomeEmailEnabled">
			<div class="help-text my-4">{{  __tr('Add welcome email text here') }}</div>
			  <!-- Welcome email textarea -->
			<textarea rows="10" id="lwEnableWelcomeEmailContent" class="lw-form-field form-control" placeholder="{{ __tr('Leave Blank If not required') }}" name="welcome_email_content">{!! getAppSettings('welcome_email_content') !!}</textarea>
			  <!-- /Welcome email textarea -->
		</div>
	 </div>
	  <!-- /Welcome email For New User -->
    </fieldset>
    <fieldset class="mb-4">
        <legend>{{  __tr('Disposable Email Usages') }}</legend>
        <x-lw.checkbox :offValue="0" id="lwDisallowDisposableEmail" name="disallow_disposable_emails" data-lw-plugin="lwSwitchery" data-color="orange" :checked="getAppSettings('disallow_disposable_emails')" :label="__tr('Disallow Disposable Emails Usages')" value="1" />
        <small class="mt-3 text-muted d-block">
            <strong>{{  __tr('Note:') }}</strong> {{  __tr('It will disallow users to use disposable emails like Mailinator, Guerillamail etc for user registration, contact form etc') }}
        </small>
    </fieldset>
	{{-- User terms and conditions --}}
    <div class="form-group row">
		<!-- User Terms -->
		<div class="col-sm-12 mb-3 mb-sm-0">
			<label for="termsAndConditionsUrl"><?= __tr('User Terms And Conditions') ?></label>
			<textarea rows="10" name="user_terms" class="form-control form-control-user" id="termsAndConditionsUrl"><?= $configurationData['user_terms'] ?></textarea>
            <small class="form-text text-muted">{{  __tr('User needs to accept it while registering.') }}</small>
            <div class="my-3 text-muted">
                <small>{{ __tr('Public link : ') }} <a target="_blank" href="{{ route('app.terms_and_policies', [
                    'contentName' => 'user_terms'
                ]) }}">{{ route('app.terms_and_policies', [
                    'contentName' => 'user_terms'
                ]) }}</a></small>
            </div>
		</div>
		<!-- / User Terms -->
	</div>
    <div class="form-group row">
		<!-- Vendor Terms -->
		<div class="col-sm-12 mb-3 mb-sm-0">
			<label for="lwVendorTerms"><?= __tr('Vendor Terms And Conditions') ?></label>
			<textarea rows="10" name="vendor_terms" class="form-control form-control-user" id="lwVendorTerms"><?= $configurationData['vendor_terms'] ?></textarea>
            <small class="form-text text-muted">{{  __tr('Vendor needs to accept it while registering.') }}</small>
            <div class="my-3 text-muted">
                <small>{{ __tr('Public link : ') }} <a target="_blank" href="{{ route('app.terms_and_policies', [
                    'contentName' => 'vendor_terms'
                ]) }}">{{ route('app.terms_and_policies', [
                    'contentName' => 'vendor_terms'
                ]) }}</a></small>
            </div>
		</div>
		<!-- / Vendor Terms -->
	</div>
    <div class="form-group row">
		<!-- Privacy Policy Terms -->
		<div class="col-sm-12 mb-3 mb-sm-0">
			<label for="lwPrivacy PolicyTerms"><?= __tr('Privacy Policy') ?></label>
			<textarea rows="10" name="privacy_policy" class="form-control form-control-user" id="lwPrivacy PolicyTerms"><?= $configurationData['privacy_policy'] ?></textarea>
            <small class="form-text text-muted">{{  __tr('It will be your Privacy Policy') }}</small>
            <div class="my-3 text-muted">
                <small>{{ __tr('Public link : ') }} <a target="_blank" href="{{ route('app.terms_and_policies', [
                    'contentName' => 'privacy_policy'
                ]) }}">{{ route('app.terms_and_policies', [
                    'contentName' => 'privacy_policy'
                ]) }}</a></small>
            </div>
		</div>
		<!-- / Privacy Policy Terms -->
	</div>
	<!-- Update Button -->
	<a href class="lw-ajax-form-submit-action btn btn-primary btn-user lw-btn-block-mobile mt-2">
		<?= __tr('Save') ?>
	</a>
	<!-- /Update Button -->
</form>
<!-- /User Setting Form -->