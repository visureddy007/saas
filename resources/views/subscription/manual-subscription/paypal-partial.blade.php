@if ($paymentMethod == 'paypal' and ($subscriptionRequestRecord->status == 'initiated'))
@if (getAppSettings('enable_paypal'))
@if (getAppSettings('use_test_paypal_checkout'))
<script
    src="https://www.paypal.com/sdk/js?client-id=<?= getAppSettings('paypal_checkout_testing_publishable_key') ?>&currency=<?= getAppSettings('currency') ?>">
</script>
@else
<script
    src="https://www.paypal.com/sdk/js?client-id=<?= getAppSettings('paypal_checkout_live_publishable_key') ?>&currency=<?= getAppSettings('currency') ?>">
</script>
@endif
@endif
@push('appScripts')
<script type="text/javascript">
(function() {
    'use strict';
    try {
        var manualSubscriptionUid = "{{ $subscriptionRequestRecord->_uid }}";
        var paypalResponse = @json($paypalResponse);
        paypal.Buttons({
            // Call your server to set up the transaction
            createOrder: function(data, actions) {
                return paypalResponse.data.createPaypalOrder.id;
            },
            // Finalize the transaction on the server after payer approval
            onApprove: function(data) {
                return fetch("{{ route('capture.paypal.checkout') }}", {
                    method: "POST",
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{csrf_token() }}',
                    },
                    body: JSON.stringify({
                        "orderUID": data.orderID,
                        "manualSubscriptionUid": manualSubscriptionUid,
                    })
                })
                .then((response) => {
                    return response.json();
                })
                .then((orderData) => {
                    // Successful capture! For dev/demo purposes:
                    window.location = orderData.data.redirectRoute;
                });
            },
            onError: function(err) {
                // Show an error page here, when an error occurs
                showAlert(JSON.stringify(err.message, null, 2), 'error');
            },
            onCancel: function(data) {
                showAlert("{{ __tr('User cancelled payment.') }}", 'error');
                // Optionally show a cancellation message or update the UI
            }
        }).render('#paypal-button-container');
    } catch (error) {
            if ('{{ getAppSettings('enable_paypal') }}') {
                showAlert(error, 'error');
            }
        }
    })();
    </script>
    @endpush
@endif
