<!-- Page Heading -->
<h1><?= __tr('Email Settings') ?></h1>
<!-- Page Heading -->
<hr>
<!-- Email Setting Form -->
<form class="lw-ajax-form lw-form" method="post" action="<?= route('manage.configuration.write', ['pageType' => request()->pageType]) ?>">

	<div class="form-group mt-2">
        <div class="custom-control custom-checkbox">
            <!-- Enable google login hidden field -->
            <input type="hidden" name="use_env_default_email_settings" value="0" />
            <!-- /Enable google login hidden field -->
            <input type="checkbox" class="custom-control-input" {{ $configurationData['use_env_default_email_settings'] == true ? 'checked' : '' }} id="forEnvDefaultSettings"  name="use_env_default_email_settings" value="1">
            <label class="custom-control-label" for="forEnvDefaultSettings">
               {{ __tr('Use mail settings from .env file\'s') }}
            </label>
        </div>
	</div>

	<div id="lwAllFormFieldsBlock">

		<div class="form-group row">
			<!-- Mail From Address -->
			<div class="col-sm-6 mb-3 mb-sm-0">
				<label for="lwMailFromAddress"><?= __tr('Mail From Address') ?></label>
				<input type="text" name="mail_from_address" class="form-control form-control-user" id="lwMailFromAddress" required value="<?= $configurationData['mail_from_address'] ?>">
			</div>
			<!-- / Mail From Address -->
			<!-- Number of users -->
			<div class="col-sm-6 mb-3 mb-sm-0">
				<label for="lwMailFromName"><?= __tr('Mail From Name') ?></label>
				<input type="text" name="mail_from_name" class="form-control form-control-user" id="lwMailFromName" required value="<?= $configurationData['mail_from_name'] ?>">
			</div>
			<!-- / Number of users -->
		</div>
		<div class="form-group row">
			<!-- Mail Driver -->
			<div class="col-sm-12 mb-3 mb-sm-0">
				<label for="lwMailDriver"><?= __tr('Mail Driver') ?></label>
				<select id="lwMailDriver" class="form-control" placeholder="<?= __tr('Mail Driver') ?>" name="mail_driver" required>
					@if(!__isEmpty($configurationData['mail_drivers']))
					@foreach($configurationData['mail_drivers'] as $key => $driver)
					<option value="<?= $driver['id'] ?>" <?= $driver['id'] == $configurationData['mail_driver'] ? 'selected' : '' ?>><?= $driver['name'] ?></option>
					@endforeach
					@endif
				</select>
			</div>
			<!-- / Mail Driver -->
		</div>

		<!-- Smtp Block -->
		<div id="lwSmtpBlock">
			<fieldset class="lw-fieldset mb-3">
				<legend class="lw-fieldset-legend"><i class="fas fa-cog"></i></legend>
				<div class="form-group row">
					<!-- Mail Host -->
					<div class="col-sm-4 mb-3 mb-sm-0">
						<label for="lwMailHost"><?= __tr('Mail Host') ?></label>
						<input type="text" name="smtp_mail_host" class="form-control form-control-user" required value="<?= $configurationData['smtp_mail_host'] ?>" id="lwMailHost">
					</div>
					<!-- / Mail Host -->

					<!-- Mail Port -->
					<div class="col-sm-4 mb-3 mb-sm-0">
						<label for="lwMailPort"><?= __tr('Mail Port') ?></label>
						<input type="number" name="smtp_mail_port" class="form-control form-control-user" required min="0" value="<?= $configurationData['smtp_mail_port'] ?>" id="lwMailPort">
					</div>
					<!-- / Mail Port -->

					<!-- Mail Encryption -->
					<div class="col-sm-4 mb-3 mb-sm-0">
						<label for="lwMailEncryption"><?= __tr('Mail Encryption') ?></label>
						<select id="lwMailEncryption" class="form-control" placeholder="<?= __tr('Mail Encryption') ?>" name="smtp_mail_encryption" required>
							@if(!__isEmpty($configurationData['mail_encryption_types']))
							@foreach($configurationData['mail_encryption_types'] as $key => $value)
							<option value="<?= $key ?>" <?= $key == $configurationData['smtp_mail_encryption'] ? 'selected' : '' ?>><?= $value ?></option>
							@endforeach
							@endif
						</select>
					</div>
					<!-- / Mail Encryption -->
				</div>
				<div class="form-group row">
					<!-- Mail Username -->
					<div class="col-sm-6 mb-3 mb-sm-0">
						<label for="lwMailUsername"><?= __tr('Mail Username') ?></label>
						<input type="text" name="smtp_mail_username" class="form-control form-control-user" required value="<?= $configurationData['smtp_mail_username'] ?>" id="lwMailUsername">
					</div>
					<!-- / Mail Username -->
					<!-- Mail Password/Api Key -->
					<div class="col-sm-6 mb-3 mb-sm-0">
						<label for="lwMailPasswordKey"><?= __tr('Mail Password/Api Key') ?></label>
						<input type="text" name="smtp_mail_password_or_apikey" class="form-control form-control-user" required value="<?= $configurationData['smtp_mail_password_or_apikey'] ?>" id="lwMailPasswordKey">
					</div>
					<!-- / Mail Password/Api Key -->
				</div>
			</fieldset>
		</div>
		<!-- Smtp Block -->

		<!-- Sparkpost Block -->
		<div id="lwSparkpostBlock">
			<fieldset class="lw-fieldset mb-3">
				<legend class="lw-fieldset-legend"><i class="fas fa-cog"></i></legend>
				<div class="form-group row">
					<!-- Sparkpost Key -->
					<div class="col-sm-12 mb-3 mb-sm-0">
						<label for="lwSparkpostKey"><?= __tr('Sparkpost Key') ?></label>
						<input type="text" name="sparkpost_mail_password_or_apikey" class="form-control form-control-user" required value="<?= $configurationData['sparkpost_mail_password_or_apikey'] ?>" id="lwSparkpostKey">
					</div>
					<!-- / Sparkpost Key -->
				</div>
			</fieldset>
		</div>
		<!-- Sparkpost Block -->

		<!-- Mailgun Block -->
		<div id="lwMailgunBlock">

			<fieldset class="lw-fieldset mb-3">

				<legend class="lw-fieldset-legend"><i class="fas fa-cog"></i></legend>

				<div class="form-group row">
					<!-- Mailgun Domain -->
					<div class="col-sm-6 mb-3 mb-sm-0">
						<label for="lwMailgunDomain"><?= __tr('Mailgun Domain') ?></label>
						<input type="text" name="mailgun_domain" class="form-control form-control-user" required value="<?= $configurationData['mailgun_domain'] ?>" id="lwMailgunDomain">
					</div>
					<!-- / Mailgun Domain -->

					<!-- Mailgun Endpoint -->
					<div class="col-sm-6 mb-3 mb-sm-0">
						<label for="lwMailgunEndpoint"><?= __tr('Mailgun Endpoint') ?></label>
						<input type="text" name="mailgun_domain" class="form-control form-control-user" required value="<?= $configurationData['mailgun_domain'] ?>" id="lwMailgunEndpoint">
					</div>
					<!-- / Mailgun Endpoint -->
				</div>
				<div class="form-group row">
					<!-- Mailgun Secret -->
					<div class="col-sm-12 mb-3 mb-sm-0">
						<label for="lwMailgunSecret"><?= __tr('Mailgun Secret') ?></label>
						<input type="text" name="mailgun_domain" class="form-control form-control-user" required value="<?= $configurationData['mailgun_domain'] ?>" id="lwMailgunSecret">
					</div>
					<!-- / Mailgun Secret -->
				</div>
			</fieldset>
		</div>
		<!-- Mailgun Block -->
	</div>
    <hr class="my-4">
	<!-- Update Button -->
	<a href class="lw-ajax-form-submit-action btn btn-primary btn-user lw-btn-block-mobile">
		<?= __tr('Save') ?>
	</a>
	<!-- /Update Button -->
</form>
<!-- /Email Setting Form -->

@push('appScripts')
<script type="text/javascript">
    (function($) {
        'use strict';
	window.toggleFormOptions = function(value) {
		switch (value) {
			case 'smtp':
				$('#lwSparkpostBlock, #lwMailgunBlock').hide();
				$('#lwSmtpBlock').show();
				break;
			case 'sparkpost':
				$('#lwSmtpBlock, #lwMailgunBlock').hide();
				$('#lwSparkpostBlock').show();
				break;
			case 'mailgun':
				$('#lwSparkpostBlock, #lwSmtpBlock').hide();
				$('#lwMailgunBlock').show();
				break;
			default:
		}
	};

	//for all form fields
	window.toggleFormByEnvSettings = function(value) {
		if (value == true) {
			$('#lwAllFormFieldsBlock').hide();
		} else {
			$('#lwAllFormFieldsBlock').show();
		}
	};

	toggleFormByEnvSettings(Boolean("<?= $configurationData['use_env_default_email_settings'] ?>"));

	toggleFormOptions("<?= $configurationData['mail_driver'] ?>");

	$('#forEnvDefaultSettings:checkbox').change(function(value) {
		toggleFormByEnvSettings(this.checked);
	});

	//initialize selectize element
	$('#lwMailDriver').selectize({
        onChange: function(value) {
            toggleFormOptions(value);
        }
    });

	//initialize selectize element
	$('#lwMailEncryption').selectize({});
})(jQuery);
</script>
@endpush