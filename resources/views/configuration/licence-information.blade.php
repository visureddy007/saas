@extends('layouts.app', ['title' => __tr('License Information')])
@section('content')
@include('users.partials.header', [
'title' => __tr('License Information'),
'description' =>'',
'class' => 'col-lg-7'
])
<div class="container-fluid ">
    <div class="row">
        <!-- button -->
    <div class="col-xl-8 mb-3 offset-xl-2 col-lg-10 offset-lg-1 col-md-12">
@if(getAppSettings('product_registration', 'registration_id'))
<div class="text-center mt-5 card py-5">
	@if(sha1(array_get($_SERVER, 'HTTP_HOST', '') . getAppSettings('product_registration', 'registration_id') . '4.5+') !== getAppSettings('product_registration', 'signature'))
			<div class="my-5 text-danger">
				<i class="fas fa-exclamation-triangle fa-6x mb-4 text-warning"></i>
				<h2> <strong><?= __tr('Invalid Signature') ?></strong></h2>
				<h3><?= __tr('Please remove and verify the licence again.') ?></h3>
			</div>
	@else
	<div class="my-5 text-success">
		<i class="fas fa-award fa-6x mb-4 text-success"></i>
		<h2> <strong><?= __tr('Congratulation') ?></strong></h2>
		<h3><?= __tr('you have successfully verified the licence') ?></h3>
	</div>
	@endif
	<strong><?= __tr('Last verified on') ?></strong> <br> <?= formatDate(getAppSettings('product_registration', 'registered_at')) ?> (<?= formatDiffForHumans(getAppSettings('product_registration', 'registered_at')) ?>)
		<div class="mt-3">
			<strong><?= __tr('Licence') ?></strong> <br> <?= (getAppSettings('product_registration', 'licence') !== 'dee257a8c3a2656b7d7fbe9a91dd8c7c41d90dc9') ? __tr('Regular Licence') : __tr('Extended Licence') ?>
		</div>
		<div class="mt-3">
			<strong><?= __tr('Version') ?></strong> <br> <?= config('lwSystem.version') ?>
		</div>
		<div class="mt-5">
		<a class="lw-ajax-link-action-via-confirm btn btn-danger" data-confirm="<?= __tr('Are you sure you want to remove licence') ?>" href="<?= route('installation.version.create.remove_registration') ?>?pageType=product_registration" data-callback="__Utils.viewReload" id="alertsDropdown" role="button" data-method="post">
            <i class="fas fa-trash"></i> <?= __tr('Remove Licence') ?>
        </a>
	</div>
</div>
@else
<!-- Email Setting Form -->
<div class="col-12 mb-3 alert alert-warning">
<?= __tr('Thank you for purchase of our product. Please activate it using Envato purchase code.') ?> <br><small><a target="_blank" href="https://help.market.envato.com/hc/en-us/articles/202822600-Where-Is-My-Purchase-Code-"><?= __tr('Where Is My Purchase Code?') ?></a></small>
</div>
<div class="lw-container">
	<div class="lw-container-box">
		<?= __tr('Initializing please wait ...') ?>
	</div>
</div>

</div>
</div>
@push('appScripts')
<script>
        (function($) {
        'use strict';
	// Get third party Url from config and customer uid from store setting table
	var appUrl = "<?= config('lwSystem.app_update_url') ?>/api/app-update",
		registrationId = "<?= config('lwSystem.registration_id') ?>",
		version = "<?= config('lwSystem.version') ?>",
		productUid = "<?= config('lwSystem.product_uid') ?>",
		csrfToken = "<?= csrf_token() ?>",
		localRegistrationRoute = "<?= route('installation.version.create.registration') ?>";
	// Set up ajax request header
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': csrfToken,
		}
	});

	// Check if existing customer not found then get product update registration form.
	if (!registrationId) {
		// Request for product update registration
		$.post(appUrl + '/register-purchase-form', {
			'current_version': version,
			'product_uid': productUid
		}, function(data, status) {
			try {
				data = JSON.parse(data);
				$(".lw-container-box").html(data.html);
			} catch (error) {
				$(".lw-container-box").html(data);
			}
			$('#productUpdateForm').validate()
		});

		// Process for register update product
		$('body .lw-container-box').on('submit', '#productUpdateForm', function(e) {
			e.preventDefault();
			$.post(appUrl + '/register-purchase',
				$('#productUpdateForm').serialize(),
				function(responseData) {
					var requestData = responseData.data;
					if ((responseData.reaction === 1)) {
						registrationId = requestData.registration_id;
						$.post(localRegistrationRoute, requestData, function(data) {
							if (data.reaction === 21) {
								window.location = "<?= route('manage.configuration.product_registration') ?>";
							}
						});
					} else {

						$('.lw-error-container-box').remove();

						if (requestData.message) {
							$(".lw-container-box").prepend('<div class="alert alert-danger lw-error-container-box"> ' + requestData.message + ' </div>');
						}

						if (requestData.validation) {
							$.each(requestData.validation, function(key, value) {
								$('#' + key).parent().find('.error').remove();
								$('#' + key).parent().append('<label id="' + key + '-error" class="error" for="' + key + '">' + value + '</label>')
							});
						}
					}
				}, 'JSON');
		});
		// If existing customer then show check for update form.
	}
})(jQuery);
</script>
@endpush
@endif
        </div>
    </div>
</div>
@endsection()