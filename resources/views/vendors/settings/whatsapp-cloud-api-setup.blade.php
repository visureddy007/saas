<div class="row">
    <div class="col-md-8"
        x-data="{ enableStep2: {{ getVendorSettings('facebook_app_id') ? 1 : 0 }}, enableStep3: {{ getVendorSettings('whatsapp_access_token') ? 1 : 0 }} }"
        x-cloak>
        <!-- Page Heading -->
        <h1>
            <?= __tr('WhatsApp Cloud API Setup') ?>
        </h1>
        <div class="accordion" id="whatsAppSetupSettingsBlock" x-data="{isSetupInProcess:false,newWhatsAppBusinessAccountId:null}" x-cloak>
            <template x-if="isSetupInProcess">
                <div class="text-center">
                    <div class="lw-loading-block col-12 text-center p-6" x-data="{lwProgressText:''}">
                        <img src="{{ asset('imgs/processing.svg') }}" alt="{{ __tr('Connecting WhatsApp API ...') }}">
                        <h2 class="text-danger mt-4 mb-2">{{  __tr('Please wait while we connecting you to the WhatsApp Cloud API, Do not refresh or redirect.') }}</h2>
                        <pre class="p-2" x-cloak x-text="lwProgressText"></pre>
                    </div>
                </div>
            </template>
            <section x-show="!isSetupInProcess">
                @if (isWhatsAppBusinessAccountReady() and getVendorSettings('embedded_setup_done_at'))
                <fieldset class="my-4 py-4">
                        <div class="text-success"><strong>{{ __tr('WhatsApp API connected using Embedded SignUp on __connectedAt__', [
                            '__connectedAt__' => formatDateTime(getVendorSettings('embedded_setup_done_at'))
                            ]) }}</strong></div>
                            </fieldset>
                        @endif
                @if (getAppSettings('enable_embedded_signup') and !getVendorSettings('embedded_setup_done_at') and !getVendorSettings('facebook_app_id'))
                <fieldset x-show="!isSetupInProcess">
                    <legend>{{ __tr('WhatsApp Setup with Facebook') }}</legend>
                    <div class="text-center">
                        @if (!isWhatsAppBusinessAccountReady())
                        <button type="button"
                            style="background-color: #1877f2; border: 0; border-radius: 4px; color: #fff; cursor: pointer; font-family: Helvetica, Arial, sans-serif; "
                            class="btn btn-lg mb-4" onclick="launchWhatsAppSignup()">
                            <i class="fab fa-facebook"></i><span class="h2 text-white">
                                {{ __tr('Connect WhatsApp with Facebook') }}
                                <i class="fa fa-sign-in-alt"></i></span>
                        </button>
                        @endif
                    </div>
                </fieldset>
                @endif
                @if (getAppSettings('enable_embedded_signup') and getAppSettings('enable_whatsapp_manual_signup') and !getVendorSettings('embedded_setup_done_at') and !getVendorSettings('facebook_app_id'))
                <h3 class="text-center mt-5">{{ __tr('OR') }}</h3>
                @endif
                @if (getAppSettings('enable_whatsapp_manual_signup') and !getVendorSettings('embedded_setup_done_at'))
                <fieldset>
                    <legend>{{ __tr('Connect WhatsApp Manually') }}</legend>
                    <fieldset class="lw-fieldset mb-3" @php
                        $isFacebookAppRequirement=getVendorSettings('embedded_setup_done_at') ?
                        getVendorSettings('facebook_app_id') : (getVendorSettings('facebook_app_id')); @endphp
                        x-data="{openForUpdate:false,fbAppIdExists:{{ $isFacebookAppRequirement  ? 1 : 0 }},isWebhookVerified: {{ getVendorSettings('webhook_verified_at') ? 1 : 0 }},isWebhookMessagesFieldVerified: {{ getVendorSettings('webhook_messages_field_verified_at') ? 1 : 0 }}}">
                        <legend data-toggle="collapse" data-target="#lwFacebookAppSettings" aria-expanded="true"
                            aria-controls="lwFacebookAppSettings">{!! __tr('Facebook Developer Account & Facebook App')
                            !!} <small class="text-muted">{{ __tr('Click to expand/collapse') }}</small>
                        </legend>
                        <div class="collapse {{ $isFacebookAppRequirement ? '' : 'show' }}" id="lwFacebookAppSettings" data-parent="#whatsAppSetupSettingsBlock">
                            <!-- whatsapp cloud api setup form -->
                            <form id="lwWhatsAppFacebookAppForm" class="lw-ajax-form lw-form"
                                name="whatsapp_setup_facebook_app_form" method="post"
                                action="<?= route('vendor.settings.write.update') ?>">
                                <input type="hidden" name="pageType" value="whatsapp_cloud_api_setup">
                                <!-- set hidden input field with form type -->
                                <input type="hidden" name="form_type" value="whatsapp_setup_facebook_app_form" />
                                <a href="https://developers.facebook.com/docs/whatsapp/cloud-api/get-started#set-up-developer-assets"
                                    target="_blank" class="float-right">{!! __tr('Help & More Information') !!} <i class="fas fa-external-link-alt"></i></a>
                                <p>
                                    {!! __tr('To get started you should have __facebookApp__, you mostly need to select  Business as type of your app.', [
                                    '__facebookApp__' => '<strong>' . __tr('Facebook App') . '</strong>',
                                    ],
                                    ) !!}
                                <div>
                                    <a target="_blank" href="https://developers.facebook.com/apps/"
                                        class="btn btn-dark">{{
                                        __tr('Create or Select Facebook App') }}
                                        <i class="fas fa-external-link-alt"></i></a>
                                </div>
                                </p>
                                <p>
                                    {!! __tr('Once you have the Facebook app, add your App ID below, you will find it in App Settings > Basic') !!}
                                </p>
                                <div>
                                    <div x-show="!fbAppIdExists">
                                        <x-lw.input-field type="text" id="lwFacebookAppId"
                                            data-form-group-class="col-md-12 col-lg-4" :label="__tr('Facebook App ID')"
                                            name="facebook_app_id"
                                            placeholder="{{ getVendorSettings('facebook_app_id') ? __tr('App ID exists, add new to update') : __tr('Your Facebook App ID') }}" >
                                            @if (getVendorSettings('facebook_app_id'))
                                                <x-slot name="prependText">
                                                    <i class="fa fa-check text-success"></i>
                                                </x-slot>
                                            @else
                                            <x-slot name="prependText">
                                                <i class="fa fa-times text-danger"></i>
                                            </x-slot>
                                            @endif
                                        </x-lw.input-field>
                                        <x-lw.input-field type="text" id="lwFacebookAppSecret"
                                            data-form-group-class="col-md-12 col-lg-4"
                                            :label="__tr('Facebook App Secret')"
                                            placeholder="{{ getVendorSettings('facebook_app_secret') ? __tr('App Secret exists, add new to update') : __tr('Add your Facebook App Secret') }}"
                                            name="facebook_app_secret">
                                            @if (getVendorSettings('facebook_app_secret'))
                                                <x-slot name="prependText">
                                                    <i class="fa fa-check text-success"></i>
                                                </x-slot>
                                            @else
                                                <x-slot name="prependText">
                                                    <i class="fa fa-times text-danger"></i>
                                                </x-slot>
                                            @endif
                                        </x-lw.input-field>
                                        <div class="form-group mt-3 col">
                                            <!-- Update Button -->
                                            <button type="submit" class="btn btn-primary lw-btn-block-mobile">
                                                <?= __tr('Save') ?>
                                            </button>
                                            <!-- /Update Button -->
                                        </div>
                                        <div class="text-orange my-4">{{  __tr('Once you submit app id and app secret webhook will be created automatically') }}</div>
                                    </div>
                                    <button x-show="fbAppIdExists"
                                        @click.prevent="fbAppIdExists = !fbAppIdExists,openForUpdate=true" type="button"
                                        class="btn btn-warning">{{ __tr('Click Here to Update') }}</button>
                                </div>
                            </form>
                            <!-- / whatsapp cloud api setup form -->
                        </div>
                        <div class="badge badge-success py-1 mt-2" x-show="fbAppIdExists"><i class="fa fa-2x fa-check-square"></i>
                            <span class="lw-configured-badge">{{ __tr('Configured') }}</span>
                        </div>
                        <div class="badge badge-danger py-1 mt-2" x-show="!fbAppIdExists && !openForUpdate">
                            <i class="fas fa-exclamation-circle fa-2x"></i> <span class="lw-configured-badge">{{ __tr('Not Configured') }}</span>
                        </div>
                        <div x-show="isWebhookVerified">
                        <div class="badge badge-success py-1 mt-2">
                            <i class="fa fa-2x fa-check-square"></i> <span class="lw-configured-badge">{{ __tr('Webhook Configured') }}</span>
                        </div>
                        @if(!getVendorSettings('embedded_setup_done_at'))
                        <a x-show="fbAppIdExists" href="{{ route('vendor.webhook.disconnect.write') }}" data-method="post" class="btn btn-danger btn-sm lw-ajax-link-action">{{  __tr('Disconnect Webhook') }}</a>
                        @endif
                    </div>
                        <div x-show="!isWebhookVerified">
                            <div class="badge badge-danger py-1 mt-2" >
                                <i class="fas fa-exclamation-circle fa-2x"></i> <span class="lw-configured-badge">{{ __tr('Webhook Not Configured') }}</span>
                            </div>
                            @if(!getVendorSettings('embedded_setup_done_at'))
                            <a x-show="fbAppIdExists" href="{{ route('vendor.webhook.connect.write') }}" data-method="post" class="btn btn-success btn-sm lw-ajax-link-action">{{  __tr('Connect Webhook') }}</a>
                            @endif
                        </div>
                    </fieldset>
                    <fieldset class="lw-fieldset my-4"
                        x-data="{openForUpdate:false,whatsAppSettings:{{ (getVendorSettings('whatsapp_access_token') and !getVendorSettings('whatsapp_access_token_expired')) ? 1 : 0 }},lwWhatsAppHelpBlockOpened:false}">
                        <legend data-toggle="collapse" data-target="#lwWhatsAppSettingsBlock" aria-expanded="false"
                            aria-controls="lwWhatsAppSettingsBlock">{!! __tr('WhatsApp Integration Setup') !!} <small
                                class="text-muted">{{ __tr('Click to expand/collapse') }}</small></legend>
                        <div class="collapse" :class="(!enableStep2) ? 'lw-disabled-block-content' : ''"
                            id="lwWhatsAppSettingsBlock" data-parent="#whatsAppSetupSettingsBlock">
                            <div class="border-danger rounded">
                                {{  __tr('You should have whatsapp_business_management and whatsapp_business_messaging permission') }}
                            </div>
                            <div class="col-12 mb-4">
                                <button class="btn btn-dark btn-sm" type="button" @click="lwWhatsAppHelpBlockOpened = !lwWhatsAppHelpBlockOpened">{{  __tr('Quick Help') }}</button>
                            </div>
                            <div x-show="lwWhatsAppHelpBlockOpened" >
                                <p>{{ __tr('Once you created your app you now need to choose WhatsApp from list click on the setup as shown in the below screenshot') }}
                                    <div class="col-12 col-md-3 col-xl-2">
                                        <img class="img-fluid" src="https://i.imgur.com/aeDwghR.png"
                                            alt="{{ __tr('WhatsApp Integration') }}">
                                    </div>
                                    </p>
                                    <p>
                                        {{ __tr('You may need to select or setup Meta Business Account, once done go to API setup from sidebar under the WhatsApp menu item as shown in the below screenshot') }}
                                    <div class="col-12 col-md-3 col-xl-2">
                                        <img class="img-fluid" src="https://i.imgur.com/G4fMiT9.png"
                                            alt="{{ __tr('WhatsApp API Setup') }}">
                                    </div>
                                    </p>
                            </div>
                            <div>
                                <!-- whatsapp cloud api setup form -->
                                <form x-show="!whatsAppSettings" id="lwWhatsAppSetupBusinessForm"
                                    class="lw-ajax-form lw-form" name="whatsapp_setup_business_form" method="post"
                                    action="<?= route('vendor.settings.write.update') ?>">
                                    <input type="hidden" name="pageType" value="whatsapp_cloud_api_setup">
                                    <!-- set hidden input field with form type -->
                                    <input type="hidden" name="form_type" value="whatsapp_setup_business_form" />
                                    <div x-data="{newAccessToken:''}">
                                        <div class="float-right">
                                            <a target="_blank"
                                                href="https://developers.facebook.com/docs/whatsapp/business-management-api/get-started#1--acquire-an-access-token-using-a-system-user-or-facebook-login"
                                                class="">{!! __tr('Help & More Information') !!} <i
                                                    class="fas fa-external-link-alt"></i></a> | <a target="_blank"
                                                href="https://www.cloudperitus.com/blog/whatsapp-cloud-api-integration-generating-permanent-access-token"
                                                class="">{!! __tr('External Help') !!} <i
                                                    class="fas fa-external-link-alt"></i></a>
                                        </div>
                                        {{-- Access Token --}}
                                        @if (getVendorSettings('whatsapp_access_token_expired'))
                                        <div class="alert alert-white border-danger text-danger my-3">
                                            {{ __tr('Your token seems to be expired, Generate new token, prefer creating
                                            permanent token') }}
                                        </div>
                                        @endif
                                        <x-lw.input-field
                                            placeholder="{{ getVendorSettings('whatsapp_access_token') ? __tr('Token exists, add new to update') : __tr('Your Access Token') }}"
                                            type="text" id="lwAccessToken" data-form-group-class="col-md-12 col-lg-8"
                                            :label="__tr('Access Token')" name="whatsapp_access_token" x-model="newAccessToken" :helpText="__tr(
                            'You can either use Temporary access token or Permanent Access token, as the Temporary token expires in 24 hours its strongly recommended that you should create Permanent token.',
                        )" >
                                            @if(getVendorSettings('whatsapp_access_token') and !getVendorSettings('whatsapp_access_token_expired'))
                                                <x-slot name="prependText">
                                                    <i class="fa fa-check text-success"></i>
                                                </x-slot>
                                            @else
                                                <x-slot name="prependText">
                                                    <i class="fa fa-times text-danger"></i>
                                                </x-slot>
                                            @endif
                                            <x-slot name="append">
                                                <a target="_blank" x-bind:href="'https://developers.facebook.com/tools/debug/accesstoken/?access_token='+newAccessToken" x-bind:class="!newAccessToken ? 'disabled' : ''" class="btn btn-light">{{  __tr('Debug Token') }} <i class="fas fa-external-link-alt"></i></a>
                                            </x-slot>
                                        </x-lw.input-field>
                                        {{-- /Access Token ID --}}
                                    </div>
                                    <div class="col-md-12 col-lg-4">
                                        {{-- WhatsApp Business Account ID --}}
                                        <x-lw.input-field
                                            placeholder="{{ getVendorSettings('whatsapp_business_account_id') ? __tr('ID exists, add new to update') : __tr('Your Business Account ID') }}"
                                            type="text" id="lwBusinessAccountId" data-form-group-class=""
                                            :label="__tr('WhatsApp Business Account ID')"
                                            name="whatsapp_business_account_id" x-model="newWhatsAppBusinessAccountId" >
                                            @if(getVendorSettings('whatsapp_business_account_id'))
                                                <x-slot name="prependText">
                                                    <i class="fa fa-check text-success"></i>
                                                </x-slot>
                                            @else
                                                <x-slot name="prependText">
                                                    <i class="fa fa-times text-danger"></i>
                                                </x-slot>
                                            @endif
                                        </x-lw.input-field>
                                        {{-- /WhatsApp Business Account ID --}}
                                    </div>
                                    <div class="form-group mt-3 col">
                                        <!-- Update Button -->
                                        <button type="submit" class="btn btn-primary lw-btn-block-mobile">
                                            <?= __tr('Save') ?>
                                        </button>
                                        <!-- /Update Button -->
                                    </div>
                                </form>
                                <button x-show="whatsAppSettings" @click.prevent="whatsAppSettings = !whatsAppSettings, openForUpdate = true" type="button" class="btn btn-warning">{{ __tr('Click here to Update') }}</button>
                            </div>
                        </div>
                        <div class="badge badge-success py-1 mt-2" x-show="whatsAppSettings"><i
                                class="fa fa-2x fa-check-square"></i> <span class="lw-configured-badge">{{ __tr('Configured') }}</span></div>
                        <div class="badge badge-danger py-1 mt-2" x-show="!whatsAppSettings && !openForUpdate"><i
                                class="fas fa-exclamation-circle fa-2x"></i> <span class="lw-configured-badge">{{ __tr('Not Configured') }}</span></div>
                    </fieldset>
                </fieldset>
                @if (getVendorSettings('whatsapp_token_info_data'))
                <fieldset>
                    <legend>{{  __tr('Access Token Information') }}</legend>
                    @if (isDemo() and isDemoVendorAccount())
                        <div class="alert alert-dark">
                            {{ __tr('Information Hidden for Demo Account') }}
                        </div>
                    @else
                    <dl>
                        <dt>{{  __tr('Permission scopes') }}</dt>
                        <dd>{{ implode(', ', getVendorSettings('whatsapp_token_info_data', 'scopes')) }}</dd>
                        <dt>{{  __tr('Issued at') }}</dt>
                        <dd>{{ getVendorSettings('whatsapp_token_info_data', 'issued_at') ? formatDateTime(getVendorSettings('whatsapp_token_info_data', 'issued_at')) : __tr('N/A') }}</dd>
                        <dt>{{  __tr('Expiry at') }}</dt>
                        @if (getVendorSettings('whatsapp_token_info_data', 'expires_at'))
                            <dd>{{ formatDateTime(getVendorSettings('whatsapp_token_info_data', 'expires_at')) }} <small class="text-muted">({{ formatDiffForHumans(getVendorSettings('whatsapp_token_info_data', 'expires_at'), 5) }})</small>
                                @if(\Carbon\Carbon::parse(getVendorSettings('whatsapp_token_info_data', 'expires_at')) < now())
                                <small class="text-danger"> - {{  __tr('Expired') }}</small>
                                @endif</dd>
                        @else
                        <dd>{{ __tr('N/A') }}</dd>
                        @endif
                        <dd>
                            <hr>
                            <a target="_blank" href="https://developers.facebook.com/tools/debug/accesstoken/?access_token={{ getVendorSettings('whatsapp_access_token') }}" class="btn btn-light btn-sm">{{  __tr('Debug Token') }} <i class="fas fa-external-link-alt"></i></a>
                        </dd>
                    </dl>
                    @endif
                </fieldset>
                @endif
                @endif
                    @if (getVendorSettings('whatsapp_business_account_id'))
                    <fieldset>
                        <legend>{{  __tr('Default Phone Number') }}</legend>
                        <div :class="(newWhatsAppBusinessAccountId) ? 'lw-disabled-block-content' : ''" class="col-md-12 col-lg-4">
                            <form id="lwWhatsAppSetupBusinessForm"
                                    class="lw-ajax-form lw-form" name="whatsapp_setup_business_form" method="post"
                                    action="<?= route('vendor.settings.write.update') ?>">
                                    <input type="hidden" name="pageType" value="whatsapp_cloud_api_setup">
                                    <input type="hidden" name="whatsapp_access_token" value="">
                            {{-- From Phone Number ID --}}
                            <x-lw.input-field type="selectize" data-form-group-class="" name="current_phone_number_id" :label="__tr('Select Default Phone Number')" data-selected="{{ getVendorSettings('current_phone_number_id') }}">
                            <x-slot name="selectOptions">
                                @if(!empty(getVendorSettings('whatsapp_phone_numbers')))
                                @foreach (getVendorSettings('whatsapp_phone_numbers') as $whatsappPhoneNumber)
                                <option value="{{ $whatsappPhoneNumber['id'] }}">{{ $whatsappPhoneNumber['display_phone_number'] }}</option>
                                @endforeach
                                @elseif(getVendorSettings('current_phone_number_id'))
                                <option value="{{ getVendorSettings('current_phone_number_id') }}">{{ getVendorSettings('current_phone_number_number') }}</option>
                                @endif
                            </x-slot>
                        </x-lw.input-field>
                            {{-- /From Phone Number ID --}}
                            <div class="form-group mt-3">
                                <!-- Update Button -->
                                <button type="submit" class="btn btn-primary lw-btn-block-mobile">
                                    <?= __tr('Save') ?>
                                </button>
                                <!-- /Update Button -->
                            </div>
                            </form>
                        </div>
                    </fieldset>
                    @endif
                <fieldset class="lw-fieldset mb-3"
                    x-data="{openForUpdate:false,testContactExists: {{ getVendorSettings('test_recipient_contact') ? 1 : 0 }}}">
                    <legend data-toggle="collapse" data-target="#lwWhatsAppTestContactBlock" aria-expanded="false"
                        aria-controls="lwWhatsAppTestContactBlock">{!! __tr('Test Contact for Campaign') !!} <small
                            class="text-muted">{{ __tr('Click to expand/collapse') }}</small></legend>
                    <div class="collapse" :class="(!enableStep3) ? 'lw-disabled-block-content' : ''"
                        id="lwWhatsAppTestContactBlock" data-parent="#whatsAppSetupSettingsBlock">
                        <!-- whatsapp cloud api setup form -->
                        <form id="lwWhatsAppTestContact" class="lw-ajax-form lw-form" name="whatsapp_setup_test_contact"
                            method="post" action="<?= route('vendor.settings.write.update') ?>">
                            <input type="hidden" name="pageType" value="whatsapp_cloud_api_setup">
                            <!-- set hidden input field with form type -->
                            <input type="hidden" name="form_type" value="whatsapp_setup_test_contact" />
                            <div class="row">
                                <div class="col-md-12 col-lg-6">
                                    <x-lw.input-field type="number" data-form-group-class=""
                                        name="test_recipient_contact" :label="__tr('Test Contact Number')"
                                        value="{{ $configurationData['testContact'] }}" :helpText="__tr('WhatsApp number to test, It should be with country code without 0 or +')" />
                                </div>
                            </div>
                            <div class="form-group mt-3 col p-0">
                                <!-- Update Button -->
                                <button type="submit" class="btn btn-primary lw-btn-block-mobile">
                                    <?= __tr('Save') ?>
                                </button>
                                <!-- /Update Button -->
                            </div>
                        </form>
                    </div>
                    <div class="badge badge-success py-1 mt-2" x-show="testContactExists"><i
                            class="fa fa-2x fa-check-square"></i> <span class="lw-configured-badge">{{
                            __tr('Configured')
                            }}</span></div>
                    <div class="badge badge-danger py-1 mt-2" x-show="!testContactExists"><i
                            class="fas fa-exclamation-circle fa-2x"></i> <span class="lw-configured-badge">{{ __tr('Not
                            Configured') }}</span></div>
                </fieldset>
                <div class="alert mb-3" :class="(!enableStep3) ? 'lw-disabled-block-content' : ''">
                    <legend>{!! __tr('It\'s ready') !!}</legend>
                    <div>
                        {{ __tr('In order to send template message you should have created and approved templates for WhatsApp Business.') }}
                        <div class="my-3">
                            <a class="lw-btn btn btn-light" href="{{ route('vendor.whatsapp_service.templates.read.list_view') }}">
                                {{ __tr('Manage Templates') }}</a>
                            <a class="lw-btn btn btn-light" href="{{ route('vendor.contact.read.list_view') }}">
                                {{ __tr('Manage Contacts') }}</a>
                        <a class="lw-btn btn btn-default" href="{{ route('vendor.campaign.new.view') }}">{{ __tr('Create New Campaign') }}</a>
                        <a data-confirm="#lwDisconnectAccount-template" href="{{ route('vendor.account.disconnect.write') }}" data-method="post" class="btn btn-danger lw-ajax-link-action">{{  __tr('Disconnect Account') }}</a>
                        <script type="text/template" id="lwDisconnectAccount-template">
                            <h2>{{ __tr('Are You Sure!') }}</h2>
                            <p>{{ __tr('Do you want to disconnect WhatsApp, you won\'t be able to send and receive messages') }}</p>
                    </script>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
    <div class="col-md-4">
        <fieldset x-data="initializedWhatsAppData">
            <legend>{{ __tr('WhatsApp Business Info') }}</legend>
            @if (isDemo() and isDemoVendorAccount())
            <div class="alert alert-dark">
                {{ __tr('Information Hidden for Demo Account') }}
            </div>
            @else
            <fieldset>
                <legend>{{  __tr('Phone Numbers') }}</legend>
                    <div class="card shadow-none border-0">
                        <template x-for="whatsAppPhoneNumber in whatsAppPhoneNumbers">
                        <div class="card-header">
                            <dl>
                                <dt>{{ __tr('Phone Number ID') }}</dt>
                                <dd x-text="whatsAppPhoneNumber.id"></dd>
                                <dt>{{ __tr('Verified Name') }}</dt>
                                <dd x-text="whatsAppPhoneNumber.verified_name"></dd>
                             {{--    <dt>{{ __tr('Code Verification Status') }}</dt>
                                <dd x-text="whatsAppPhoneNumber.code_verification_status"></dd> --}}
                                <dt>{{ __tr('Display Phone Number') }}</dt>
                                <dd x-text="whatsAppPhoneNumber.display_phone_number"></dd>
                                <dt>{{ __tr('Quality Rating') }}</dt>
                                <dd x-bind:class="'text-' + whatsAppPhoneNumber.quality_rating.toLowerCase()" x-text="whatsAppPhoneNumber.quality_rating"></dd>
                                <dt x-show="whatsAppPhoneNumber?.name_status">{{ __tr('Name Status') }}</dt>
                                <dd x-show="whatsAppPhoneNumber?.name_status" x-text="whatsAppPhoneNumber?.name_status"></dd>
                                <dt x-show="whatsAppPhoneNumber?.new_name_status">{{ __tr('New Name Status') }}</dt>
                                <dd x-show="whatsAppPhoneNumber?.new_name_status" x-text="whatsAppPhoneNumber?.new_name_status"></dd>
                                <dd>
                                    <a data-pre-callback="appFuncs.clearContainer" title="{{  __tr('Update Business Profile') }}" class="lw-btn btn btn-sm btn-outline-default lw-ajax-link-action" data-response-template="#lwBusinessProfileUpdateBody" x-bind:href="__Utils.apiURL('{{ route('vendor.whatsapp.business_profile.read', [ 'phoneNUmberId']) }}', {'phoneNUmberId': whatsAppPhoneNumber.id})"  data-toggle="modal" data-target="#lwBusinessProfileUpdate"><i class="fa fa-edit"></i> {{  __tr('Update Business Profile') }}</a>
                                </dd>
                            </dl>
                        </div>
                    </template>
                    </div>
                    <div class="mt-4">
                        <a href="{{ route('vendor.whatsapp.sync_phone_numbers') }}" class="btn btn-primary btn-sm lw-ajax-link-action {{ !getVendorSettings('whatsapp_access_token') ? 'disabled' : '' }}"
            data-method="post">{{ __tr('Re-sync Phone Numbers') }}</a>
            <a target="_blank" href="https://business.facebook.com/wa/manage/phone-numbers/?waba_id={{ getVendorSettings('whatsapp_business_account_id') }}"
            class="btn btn-dark btn-sm {{ !getVendorSettings('whatsapp_access_token') ? 'disabled' : '' }}" data-method="post">{{ __tr('Manage Phone Numbers') }} <i class="fas fa-external-link-alt"></i></a>
                    </div>
            </fieldset>
            <fieldset>
                <legend>{{  __tr('Overall Health') }}</legend>
            <dl>
                <dt>{{ __tr('WhatsApp Business ID') }}</dt>
                <dd x-text="healthStatusData?.whatsapp_business_account_id"></dd>
                <dt>{{ __tr('Status as at') }}</dt>
                <dd x-text="healthStatusData?.health_status_updated_at_formatted"></dd>
                <dt>{{ __tr('Overall Health') }}</dt>
                <dd x-text="healthStatusData?.health_data?.health_status.can_send_message"></dd>
            </fieldset>
                <template x-for="healthEntity in healthStatusData?.health_data?.health_status.entities">
                        <fieldset>
                            <legend> <span x-text="healthEntity.entity_type"></span>@if (getVendorSettings('embedded_setup_done_at')) <template x-if="healthEntity.entity_type != 'APP'"> - <span
                                    x-text="healthEntity.id"></span> </template> @else - <span
                                    x-text="healthEntity.id"></span> @endif </legend>
                            <dl>
                                <dt>{{ __tr('Can Send Message') }}</dt>
                                <dd x-text="healthEntity.can_send_message"></dd>
                                <template x-if="healthEntity.additional_info ?? ''">
                                    <div class="alert alert-dark">
                                        <strong>{{ __tr('Additional Info from Meta') }}</strong>
                                        <p x-text="healthEntity.additional_info ?? ''"></p>
                                    </div>
                                </template>
                                <template x-for="errorItem in healthEntity.errors">
                                    <dl>
                                        <dt>{{ __tr('Error Description') }}</dt>
                                        <dd class="text-danger"
                                            x-text="' (' + errorItem.error_code + ') ' + errorItem.error_description">
                                        </dd>
                                        <dt>{{ __tr('Possible Solution') }}</dt>
                                        <dd class="text-success" x-text="errorItem.possible_solution"></dd>
                                    </dl>
                                </template>
                            </dl>
                        </fieldset>
                    </template>
            </dl>
            <a href="{{ route('vendor.whatsapp.health.status') }}"
                class="btn btn-primary mt-4 btn-sm lw-ajax-link-action {{ !getVendorSettings('whatsapp_access_token') ? 'disabled' : '' }}"
                data-method="post">{{ __tr('Refresh Status') }}</a>
            @endif
        </fieldset>
    </div>
</div>
<script>
    (function() {
       'use strict';
       @if(getVendorSettings('whatsapp_business_account_id'))
        document.addEventListener('alpine:init', () => {
            Alpine.data('initializedWhatsAppData', () => ({
                healthStatusData: @if(getVendorSettings('whatsapp_health_status_data'))@json(getVendorSettings('whatsapp_health_status_data'))[{{ getVendorSettings('whatsapp_business_account_id') }}]@else{}@endif,
                whatsAppPhoneNumbers: @json(getVendorSettings('whatsapp_phone_numbers'))
            }));
       @else
       document.addEventListener('alpine:init', () => {
        Alpine.data('initializedWhatsAppData', () => ({
            healthStatusData: {},
            whatsAppPhoneNumbers: [],
        }));
       @endif
   });
})();
</script>
@if(getAppSettings('enable_embedded_signup'))
<script>
    (function() {
       'use strict';
  window.fbAsyncInit = function() {
    FB.init({
      appId            : '{{ getAppSettings('embedded_signup_app_id') }}',
      autoLogAppEvents : true,
      xfbml:    true, // parse social plugins on this page
      version          : 'v20.0'
    });
  };
  })();
</script>
<script async defer crossorigin="anonymous" src="https://connect.facebook.net/en_US/sdk.js">
</script>
<script>
    (function() {
       'use strict';
  // Facebook Login with JavaScript SDK
   window.launchWhatsAppSignup = function() {
    __DataRequest.updateModels({isSetupInProcess:true});
    // Conversion tracking code
    // fbq && fbq('trackCustom', 'WhatsAppOnboardingStart', {appId: 'your-facebook-app-id', feature: 'whatsapp_embedded_signup'});
    var tempAccessCode = '',
        phoneNumberId = '',
        waBaId = '';
    // Launch Facebook login
    FB.login(function (response) {
      if (response.authResponse) {
        //Use this token to call the debug_token API and get the shared WABA's ID
        // const accessToken = response.authResponse.accessToken;
        tempAccessCode = response.authResponse.code;
        if(tempAccessCode) {
            __DataRequest.post('{{ route('vendor.whatsapp_setup.embedded_signup.write') }}', {
                'request_code' : tempAccessCode,
                'waba_id' : waBaId,
                'phone_number_id' : phoneNumberId
            }, function() {
                __DataRequest.updateModels({isSetupInProcess:false});
            }, {
                eventStreamUpdate: true
            });
        } else {
            __DataRequest.updateModels({isSetupInProcess:false});
        }
      } else {
        alert('User cancelled login or did not fully authorize.');
        __DataRequest.updateModels({isSetupInProcess:false});
      }
    }, {
      config_id: '{{ getAppSettings('embedded_signup_config_id') }}', // configuration ID obtained in the previous step goes here
      response_type: 'code',     // must be set to 'code' for System User access token
      override_default_response_type: true,
      extras: {
        "sessionInfoVersion": 2,  //  Receive Session Logging Info
        setup: {
        //   ... // Prefilled data can go here
        }
      }
    });
    const sessionInfoListener = (event) => {
  if (event.origin !== "https://www.facebook.com") return;
  try {
    const data = JSON.parse(event.data);
    if (data.type === 'WA_EMBEDDED_SIGNUP') {
      // if user finishes the Embedded Signup flow
      if (data.event === 'FINISH') {
        const {phone_number_id, waba_id} = data.data;
        phoneNumberId = phone_number_id;
        waBaId = waba_id;
      }
      // if user cancels the Embedded Signup flow
      else {
       const{current_step} = data.data;
      }
    }
  } catch {
    // Don’t parse info that’s not a JSON
    // console.log('Non JSON Response', event.data);
  }
};

window.addEventListener('message', sessionInfoListener);
  }
  })();
</script>
@endif