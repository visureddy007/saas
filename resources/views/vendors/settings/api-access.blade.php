@php
$vendorId = getVendorId();
// check the feature limit
$vendorPlanDetails = vendorPlanDetails('api_access', 0, $vendorId);
@endphp
<div class="row">
    <div class="col-md-8" x-cloak>
    <!-- Page Heading -->
    <h1>
        <?= __tr('API Access & Webhook') ?>
    </h1>
    <fieldset>
        <legend>{{  __tr('Webhook Endpoint') }}</legend>
        <p>{{  __tr('WhatsApp webhook payload will be forwarded to following endpoint via POST method') }}</p>
        @if ($vendorPlanDetails['is_limit_available'])
        <form class="lw-ajax-form lw-form" method="post" action="<?= route('vendor.settings.write.update', ['pageType' => 'internals']) ?>" >
            <div class="my-4" x-cloak x-data="{lwVendorEndpointShow:{{ getVendorSettings('enable_vendor_webhook') ? 1 : 0 }}}">
                <x-lw.checkbox @click="lwVendorEndpointShow = !lwVendorEndpointShow" id="enableWebhookEndpoint" name="enable_vendor_webhook" :checked="getVendorSettings('enable_vendor_webhook')" data-lw-plugin="lwSwitchery" :label="__tr('Enable Webhook Endpoint')" />
                <div x-show="lwVendorEndpointShow">
                    <x-lw.input-field type="text" id="lwWebhookEndpoint" data-form-group-class="" value="{{ getVendorSettings('vendor_webhook_endpoint') }}" :label="__tr('Webhook Endpoint')" name="vendor_webhook_endpoint"/>
                </div>
                {{-- https://wac0124.test/temp-webhook --}}
                {{-- submit button --}}
                <div>
                    <button type="submit" href class="mt-2 btn btn-primary btn-user lw-btn-block-mobile">{{ __tr('Save') }}</button>
                </div>
            </div>
        </form>
        @else
            <div class="alert alert-danger">
                {{  __tr('API/Webhook Access is not available in your plan, please upgrade your subscription plan.') }}
            </div>
        @endif
        <h3>{{  __tr('Example Webhook Response') }}</h3>
<pre>
<code>
{
    "contact": {
        "status": "existing/updated/new",
        "uid": "contact uid",
        "first_name": "abc",
        "last_name": "xyz",
        "email": "email@domain.com",
        "language_code": "en",
        "country": "India"
    },
    "whatsapp_webhook_payload": {
        // WhatsApp webhook data
    }
}
</code>
</pre>
    </fieldset>
    <fieldset class="lw-fieldset mb-3" >
        <legend>{{  __tr('Your Account Access API') }}</legend>
        <p>{{  __tr('Access token is required to use available APIs') }}</p>
            <p>
                {{  __tr('You need to pass access token as bearer token in header or \'token\' as your url parameter') }}
            </p>
            <div>
                @if ($vendorPlanDetails['is_limit_available'])
                <div class="col-12">
                    <h3>{!! __tr('API Access Token') !!}</h3>
                    @if(getVendorSettings('vendor_api_access_token'))
                    <div class="input-group">
                        <input type="text" class="form-control" readonly id="lwAccessToken" value='{{ getVendorSettings('vendor_api_access_token') }}'>
                        <div class="input-group-append">
                            <button class="btn btn-outline-light" type="button" onclick="lwCopyToClipboard('lwAccessToken')">
                                <?= __tr('Copy') ?>
                            </button>
                        </div>
                    </div>
                    @else
                    <div class="alert alert-light">
                        {{  __tr('No token generated yet.') }}
                    </div>
                    @endif
                </div>
                <script type="text/template" id="lwRegenerateTokenAlert">
                    <h3>{{  __tr('Generate New Token?') }}</h3>
                    <p>{{  __tr('Your existing tokens will be void immediately') }}</p>
                </script>
                <form class="lw-ajax-form lw-form" @if(getVendorSettings('vendor_api_access_token')) data-confirm="#lwRegenerateTokenAlert" @endif method="post" action="<?= route('vendor.settings.write.update', ['pageType' => 'internals']) ?>" >
                    <div class="my-4">
                        <input type="hidden" name="vendor_api_access_token" value="{{ Str::random(64) }}">
                        {{-- submit button --}}
                        <button type="submit" href class="ml-3 btn btn-primary btn-user lw-btn-block-mobile">
                            <i class="fa fa-key"></i> {{ __tr('Generate New Token') }}
                        </button>
                    </div>
                    </form>
                    <div>
                        <hr>
                        <h3>{{  __tr('API Endpoint Information') }}</h3>
                        <div class="form-group">
                            <label for="lwApiBaseUrl">{{  __tr('API Base URL') }}</label>
                            <div class="input-group">
                                <input type="text" class="form-control" readonly id="lwApiBaseUrl" value='{{ route('api.base_url') }}'>
                                <div class="input-group-append">
                                    <button class="btn btn-outline-light" type="button" onclick="lwCopyToClipboard('lwApiBaseUrl')">
                                        <?= __tr('Copy') ?>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="lwVendorUid">{{  __tr('Your Vendor UID') }}</label>
                            <div class="input-group">
                                <input type="text" class="form-control" readonly id="lwVendorUid" value='{{ getVendorUid() }}'>
                                <div class="input-group-append">
                                    <button class="btn btn-outline-light" type="button" onclick="lwCopyToClipboard('lwVendorUid')">
                                        <?= __tr('Copy') ?>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="lwExampleEndpoint">{{  __tr('Example Endpoint for Send Message it will consist of API base url, vendor uid and token. You can also pass token as Bearer Token') }}</label>
                            <div class="input-group">
                                <input type="text" class="form-control" readonly id="lwExampleEndpoint" value='{{ route('api.vendor.chat_message.send.process', [
                                    'vendorUid' => getVendorUid()
                                ]) }}?token={{  getVendorSettings('vendor_api_access_token') }}'>
                                <div class="input-group-append">
                                    <button class="btn btn-outline-light" type="button" onclick="lwCopyToClipboard('lwExampleEndpoint')">
                                        <?= __tr('Copy') ?>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="alert alert-danger">
                        {{  __tr('API/Webhook Access is not available in your plan, please upgrade your subscription plan.') }}
                    </div>
                @endif
            </div>
        </fieldset>
        <fieldset>
            <legend>{{  __tr('API Documentation') }}</legend>
            <fieldset>
                <legend>{{  __tr('Variables and Parameters') }}</legend>
                <h4>{{  __tr('Contact Related Dynamic Parameters') }}</h4>
                <div class="help-text my-3 border p-3">{{  __tr('You are free to use following dynamic variables for parameters excluding phone_number, template_name, template_language, which will get replaced with contact\'s concerned field value.') }} <div><code>{{ implode(' ', $dynamicFields) }}</code></div></div>
                <h3>{{  __tr('Example Parameters') }}</h3>
<pre>
<code>
{
    "from_phone_number_id": "phone number id from which you would like to send message, if not provided default one will be used",
    "phone_number": "phone number with country code without prefixing + or 0",
    "template_name" : "your_template_name",
    "template_language" : "en",
    "header_image" : "https://cdn.pixabay.com/photo/2015/01/07/15/51/woman-591576_1280.jpg",
    "header_video" : "https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/ForBiggerEscapes.mp4",
    "header_document" : "https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/ForBiggerEscapes.mp4",
    "header_document_name" : "{full_name}",
    "header_field_1" : "{full_name}",
    "location_latitude" : "22.22",
    "location_longitude" : "22.22",
    "location_name" : "Example Name",
    "location_address" : "Example address",
    "field_1" : "{first_name}",
    "field_2" : "{last_name}",
    "field_3" : "{email}",
    "field_4" : "{country}",
    "field_5" : "{language_code}",
    "button_0" : "{full_name}",
    "button_1" : "{phone_number}",
    "copy_code" : "EXAMPLE_CODE"
}
</code>
</pre>
            </fieldset>
            <div class="my-4">
                <h3>{{  __tr('Click on the button below for API information') }}</h3>
            <a target="_blank" href="{{ getAppSettings('api_documentation_url') }}" class="btn btn-info lw-white-space-normal"> <i class="fa fa-book"></i> {{  __tr('API Documentation') }} - {{ getAppSettings('api_documentation_url') }}</a>
            </div>
        </fieldset>
</div>
</div>