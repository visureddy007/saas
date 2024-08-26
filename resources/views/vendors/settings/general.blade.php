<!-- Page Heading -->
<h2>
    <?= __tr('General Settings') ?>
</h2>
<!-- Page Heading -->
<!-- General setting form -->
<div class="row">
</div>
<fieldset>
    <legend>{{ __tr('Basic Settings') }}</legend>
    <form class="lw-ajax-form lw-form" data-show-processing="true" method="post"
        action="<?= route('vendor.settings_basic.write.update') ?>">
        <!-- Vendor Name -->
        <div class="form-group">
            <label for="lwVendorName">
                <?= __tr('Vendor Title') ?>
            </label>
            <input type="text" class="form-control form-control-user" name="store_name" id="lwVendorName"
                value="{{ $basicSettings['title'] }}" required>
        </div>
        <!-- /Vendor Name -->
        <div class="col-12 text-right">
            <!-- Update Button -->
            <button type="submit" class="btn btn-primary btn-user lw-btn-block-mobile">
                <?= __tr('Save') ?>
            </button>
            <!-- /Update Button -->
    </form>
</fieldset>
<fieldset>
    <legend>{{ __tr('Business Information') }}</legend>
    <form class="lw-ajax-form lw-form" data-show-processing="true" method="post"
        action="<?= route('vendor.settings.write.update') ?>">
        <input type="hidden" name="pageType" value="{{ $pageType }}">
        {{-- address --}}
        <fieldset>
            <legend>{!! __tr('Address & Contact') !!}</legend>
            <div class="row">
                <div class="col-md-4">
                    <x-lw.input-field data-form-group-class="" name="address" :label="__tr('Address line')"
                        value="{{ $configurationData['address'] }}" required />
                </div>
                <div class="col-md-4">
                    <x-lw.input-field data-form-group-class="" name="postal_code" :label="__tr('Postal Code')"
                        value="{{ $configurationData['postal_code'] }}" required />
                </div>
                <div class="col-md-4">
                    <x-lw.input-field data-form-group-class="" name="city" :label="__tr('City')"
                        value="{{ $configurationData['city'] }}" required />
                </div>
                <div class="col-md-4">
                    <x-lw.input-field data-form-group-class="" name="state" :label="__tr('State')"
                        value="{{ $configurationData['state'] }}" required />
                </div>
                <div class="col-md-4">
                    <!-- Select country -->
                    <x-lw.input-field type="selectize" data-lw-plugin="lwSelectize" data-form-group-class=""
                        required="true" name="country" :label="__tr('Select Country')" required>
                        <x-slot name="selectOptions">
                            <option value="">{{ __tr('Select Country') }}</option>
                            @foreach ($configurationData['countries_list'] as $country)
                            <option value="<?= $country['id'] ?>" <?=$configurationData['country']==$country['id']
                                ? 'selected' : '' ?>>
                                <?= $country['name'] ?>
                            </option>
                            @endforeach
                        </x-slot>
                    </x-lw.input-field>
                    <!-- /Select country -->
                </div>
                <div class="col-md-4">
                    <x-lw.input-field data-form-group-class="" type="number" name="contact_phone"
                        :label="__tr('Business Phone')" value="{{ $configurationData['contact_phone'] }}" required />
                </div>
                <div class="col-md-4">
                    <!-- Contact Email -->
                    <x-lw.input-field id="lwContactEmail" data-form-group-class="" type="email" name="contact_email"
                        :label="__tr('Contact Email')" value="{{ $configurationData['contact_email'] }}" required />
                    <!-- /Contact Email -->
                </div>
        </fieldset>
        <fieldset>
            <legend>{{ __tr('Other') }}</legend>
            <div class="row">
                <!-- Select Timezone -->
                <x-lw.input-field type="selectize" data-form-group-class="col-md-4" name="timezone"
                    :label="__tr('Select Timezone')" data-selected="{{ getVendorSettings('timezone') }}" required>
                    <x-slot name="selectOptions">
                        @foreach ($configurationData['timezone_list'] as $timezone)
                        <option value="<?= $timezone['value'] ?>">
                            <?= $timezone['text'] ?>
                        </option>
                        @endforeach
                    </x-slot>
                </x-lw.input-field>
                <!-- /Select Timezone -->
                <!-- Select Default language -->
                <x-lw.input-field type="selectize" data-form-group-class="col-md-4" name="default_language"
                    :label="__tr('Default Language')"  data-selected="{{ getVendorSettings('default_language') }}" placeholder="{{ __tr('Select default language') }}" required>
                    <x-slot name="selectOptions">
                        @if (!__isEmpty($configurationData['languageList']))
                        @foreach ($configurationData['languageList'] as $key => $language)
                        <option value="<?= $language['id'] ?>">
                            <?= $language['name'] ?>
                        </option>
                        @endforeach
                        @endif
                    </x-slot>
                </x-lw.input-field>
                <!-- /Select Default language -->
            </div>
        </fieldset>
        <div class="row">
            <div class="col-12 text-right mt-5">
                <!-- Update Button -->
                <button type="submit" class="btn btn-primary btn-user lw-btn-block-mobile">
                    <?= __tr('Save') ?>
                </button>
                <!-- /Update Button -->
            </div>
        </div>
    </form>
</fieldset>
@if (getAppSettings('pusher_by_vendor'))
<!--Pusher key -->
<fieldset id="pusherKeysConfiguration">
    <legend>{{ __tr('Pusher - required for realtime updates') }}</legend>
    <form class="lw-ajax-form lw-form" method="post" action="<?= route('vendor.settings.write.update') ?>" x-cloak
        x-data="{pusherSettingsExists: {{ getVendorSettings('pusher_app_id') ? 1 : 0 }}}">
        <input type="hidden" name="pageType" value="pusher">
        <div x-show="pusherSettingsExists"></div>
        <div class="form-group" x-cloak x-show="pusherSettingsExists">
            <div class="btn-group">
                <button type="button" disabled="true" class="btn btn-success lw-btn">
                    {{ __tr('Pusher Settings are exist') }}
                </button>
                <button type="button" @click="pusherSettingsExists = !pusherSettingsExists"
                    class="btn btn-light lw-btn">{{ __tr('Update') }}</button>
            </div>
        </div>
        <div x-show="!pusherSettingsExists" >
            <div class="col-sm-12 col-md-6 col-lg-4">
            <x-lw.input-field type="text" id="lwPusherAppId" data-form-group-class="" :label="__tr('App ID')"
                name="pusher_app_id" required="true" />
            <x-lw.input-field type="text" id="lwPusherKey" data-form-group-class="" :label="__tr('App Key')"
                name="pusher_app_key" required="true" />
            <x-lw.input-field type="text" id="lwPusherAppSecret" data-form-group-class="" :label="__tr('App Secret')"
                name="pusher_app_secret" required="true" />
            <x-lw.input-field type="text" id="lwPusherAppCluster" data-form-group-class="" :label="__tr('App Cluster')"
                name="pusher_app_cluster" required="true" />
            </div>
            <!--  Button -->
            <div class="col-sm-12 col-md-6 col-lg-4 mt-3">
                    <!-- Update Button -->
                    <button type="submit" class="btn btn-primary btn-user lw-btn-block-mobile">
                        <?= __tr('Save') ?>
                    </button>
                    <!-- /Update Button -->
            </div>
            <!-- / Button -->
        </div>
    </form>
</fieldset>
<!--/Pusher key -->
@endif

<!-- /General setting form -->
@push('appScripts')
<script>
    (function($) {
        'use strict';
        // After file successfully uploaded then this function is called
        window.afterUploadedFile = function (responseData) {
            var requestData = responseData.data;
            $('#lwUploadedLogo').attr('src', requestData.path);
        }
    })(jQuery);
</script>
@endpush