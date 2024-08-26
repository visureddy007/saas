<!-- Page Heading -->
<h1><?= __tr('Currency') ?></h1>
<!-- /Page Heading -->
<hr>
<fieldset class="lw-fieldset mb-3">
	<!-- Currency Setting Form -->
	<form id="form1" class="lw-ajax-form lw-form" name="currency_setting_form" method="post" action="<?= route('manage.configuration.write', ['pageType' => request()->pageType]) ?>">
		<!-- set hidden input field with form type currencies -->
		<input type="hidden" name="form_type" value="currency_form" />
		<!-- / set hidden input field with form type currencies -->

		<div class="form-group mt-2">
			<label for="lwSelectCurrency"><?= __tr('Select Currency') ?></label>
			<select id="lwSelectCurrency" placeholder="<?= __tr('Select a Currency ...') ?>" name="currency">
				@if(!__isEmpty($configurationData['currency_options']))
				@foreach($configurationData['currency_options'] as $key => $currency)
				<option value="<?= $currency['currency_code'] ?>" <?= $configurationData['currency'] == $currency['currency_code'] ? 'selected' : '' ?> required><?= $currency['currency_name'] ?></option>
				@endforeach
				@endif
			</select>
		</div>
		<div class="form-group row">
			<!-- Currency Code field -->
			<div class="col-sm-6 mb-3 mb-sm-0">
				<label for="lwCurrencyCode"><?= __tr('Currency Code') ?></label>
				<input type="text" class="form-control form-control-user" value="<?= $configurationData['currency_value'] ?>" id="lwCurrencyCode" name="currency_value" id="lwCurrencyCode" required>
			</div>
			<!-- / Currency Code field -->

			<!-- Currency Symbol field -->
			<div class="col-sm-6 mb-3 mb-sm-0">
				<label for="lwCurrencySymbol"><?= __tr('Currency Symbol') ?></label>
				<div class="input-group">
					<input type="text" class="form-control form-control-user" value="<?= htmlentities($configurationData['currency_symbol']) ?>" id="lwCurrencySymbol" name="currency_symbol" id="lwCurrencySymbol" required>
					<div class="input-group-append">
						<span class="input-group-text" id="lwCurrencySymbolAddon"><?= $configurationData['currency_symbol'] ?></span>
					</div>
				</div>
			</div>
			<!-- Currency Symbol field -->
		</div>
		<!-- Update Button -->
		<button type="submit" class="lw-ajax-form-submit-action btn btn-primary btn-user lw-btn-block-mobile">
			<?= __tr('Save') ?>
		</button>
		<!-- /Update Button -->
	</form>
	<!-- / Currency Setting Form -->
</fieldset>
@push('appScripts')
<script>
        (function($) {
        'use strict';
	/***********  Currency block start here ***********/
	var isZeroDecimalCurrency = false, //set by default zero decimal currency false
		zeroDecimal = $("#lwZeroDecimalSwitch").is(':checked');

	//if zero decimal currency check 
	if (zeroDecimal) {
		$("#lwZeroDecimalExist").show();
		$("#lwZeroDecimalNotExist").hide();
	}

	//zero decimal currency on change event
	$(function() {
		$('#lwZeroDecimalSwitch').on('change', function(event) {
			var zeroDecimalValue = event.target.checked;
			//is checked show warning message or error message
			if (zeroDecimalValue) {
				$("#lwZeroDecimalExist").show();
				$("#lwZeroDecimalNotExist").hide();
			} else {
				$("#lwZeroDecimalExist").hide();
				$("#lwZeroDecimalNotExist").show();
			}
		})
	});

	//initialize selectize element
	$('#lwSelectCurrency').selectize({
        valueField: 'currency_code',
        labelField: 'currency_name',
        searchField: ['currency_code', 'currency_name']
    });

	//on change currency input field value
	$('#lwSelectCurrency').on('change', function(event) {
		var selectedCurrency = event.target.value,
			currencies = <?= json_encode($configurationData['currencies']['details']) ?>,
			zeroDecimalCurrency = <?= json_encode($configurationData['currencies']['zero_decimal']) ?>,
			isMatch = _.filter(zeroDecimalCurrency, function(value, key) {
				return (key === selectedCurrency);
			});

		isZeroDecimalCurrency = Boolean(isMatch.length);

		//if zero decimal currency is false or blank
		if (isZeroDecimalCurrency) {
			$("#lwIsZeroDecimalCurrency").show();
		} else {
			$("#lwIsZeroDecimalCurrency").hide();
		}

		//on change currency symbol and currency code input field value
		if (!_.isEmpty(selectedCurrency) && selectedCurrency != 'other') {
			if (currencies[selectedCurrency]) {
				$('#lwCurrencyCode').val(selectedCurrency);
				$('#lwCurrencySymbol').val(currencies[selectedCurrency].ASCII);
				$("#lwCurrencySymbolAddon").show();
				$("#lwCurrencySymbolAddon").html(currencies[selectedCurrency].symbol);
			}
		} else {
			$('#lwCurrencyCode').val('');
			$('#lwCurrencySymbol').val('');
			$("#lwCurrencySymbolAddon").hide();
		}
	});
	/***********  Currency block end here ***********/
    if($('#lwSelectCurrency').val() != 'other') {
        $('#lwSelectCurrency').trigger('change');
    }
    _.delay(function() {
        $('#lwZeroDecimalSwitch').trigger('change');
    }, 300);
})(jQuery);
</script>
@endpush