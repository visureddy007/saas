<?php $isExtendedLicence = (getAppSettings('product_registration', 'licence') === 'dee257a8c3a2656b7d7fbe9a91dd8c7c41d90dc9'); ?>
<!-- Page Heading -->
<!-- Page Heading -->
<h1>
    <?= __tr('WhatsApp Onboarding Setup') ?>
</h1>
    <form class="lw-ajax-form lw-form" method="post"
            action="<?= route('manage.configuration.write', ['pageType' => 'manual_whatsapp_onboarding']) ?>" x-cloak>
            <fieldset>
                <legend>{{  __tr('Manual Onboarding') }}</legend>
                <div class="form-group">
                    <x-lw.checkbox id="lwEnableManualWhatsAppSignup" name="enable_whatsapp_manual_signup" :offValue="0" data-lw-plugin="lwSwitchery" :checked="getAppSettings('enable_whatsapp_manual_signup')" :label="__tr('Enable Manual WhatsApp Onboarding')" />
                </div>
                <div class="form-group">
                    {{-- submit button --}}
                    <button type="submit" href class="btn btn-primary btn-user lw-btn-block-mobile">
                        <?= __tr('Save') ?>
                    </button>
                </div>
            </fieldset>
            </form>
            <form class="lw-ajax-form lw-form" method="post"
            action="<?= route('manage.configuration.write', ['pageType' => 'whatsapp_onboarding']) ?>" x-cloak
            x-data="{embeddedSignUpDataExists: {{ getAppSettings('embedded_signup_app_id') ? 1 : 0 }}}">
            <fieldset x-data="{openEmbeddedHelp:false}" x-cloak >
                <legend><i class="fab fa-facebook"></i> {{  __tr('Embedded Signup Onboarding') }}</legend>
                <fieldset>
                    <legend @click="openEmbeddedHelp = !openEmbeddedHelp">{{  __tr('Requirements and Information') }} <small class="text-muted">{{  __tr('Click to show/hide') }}</small></legend>
                    <div x-show="openEmbeddedHelp">
                        <div class="float-right">
                            <a class="btn btn-info" href="https://developers.facebook.com/docs/whatsapp/embedded-signup/"  target="_blank">{{  __tr('More Information') }}</a>
                            <a class="btn btn-info" href="https://developers.facebook.com/docs/whatsapp/solution-providers/get-started-for-tech-providers" class="float-right" target="_blank">{{  __tr('Get started for Tech Provider') }}</a>
                        </div>
                    <dl>
                        <dt>{{  __tr('Verified Meta Account') }}</dt>
                        <dd>{{  __tr('To use Embedded signup for onboarding your customers you should have Meta Verified account') }}
                        <div >
                            <a target="_blank" href="https://www.facebook.com/business/help/2058515294227817?id=180505742745347">{{  __tr('How to verify?') }}</a>
                        </div>
                        </dd>
                        <dt class="mt-4">{{  __tr('Become a Tech Provider') }}</dt>
                        <dd>{{  __tr('Follow the instructions on the given link to become tech provider so, your customers can use Embedded signup to get their account connected.') }}
                            <div>
                                <a target="_blank" href="https://developers.facebook.com/docs/whatsapp/solution-providers/get-started-for-tech-providers">{{  __tr('How to become Tech Provider?') }}</a>
                            </div>
                            <div>
                                <h4>{{  __tr('Once you become verified Tech provider all 3 items should shown in green checked mark') }}</h4>
                                <div>
                                    <img src="{{ asset('imgs/help/tech-provider.png') }}" alt="">
                                </div>
                                <div class="text-danger my-3 border rounded border-danger p-3 col-sm-12 col-md-6">
                                    <strong>{{  __tr('Important') }}</strong>
                                    <p class="mt-2">
                                        {{  __tr('1) Along with whatsapp_business_management and whatsapp_business_messaging you also needs to get public_profile and email advanced permissions from requests permissions') }}
                                    </p>
                                    <p>
                                        {{  __tr('2) You don\'t need to create Webhook manually on app id and app secret validation it will be created automatically.') }}
                                    </p>
                                </div>
                            </div>
                        </dd>
                        <dt class="mt-4">{{  __tr('Your are almost ready') }}</dt>
                        <dd>{{  __tr('You need to set App ID, App Secret and Config ID') }}</a></dd>
                    </dl>
                    <div class="form-group col-sm-12 col-md-6 mb-4">
                        <hr>
                        @php
                            $hostRoot = request()->root();
                        @endphp
                        <label for="lwHostRoot">{{ __tr('You may need to allow following domain while configuring') }}</label>
                        <div class="input-group">
                            <input type="text" class="form-control" readonly id="lwHostRoot" value="{{ $hostRoot }}">
                            <div class="input-group-append">
                                <button class="btn btn-outline-light" type="button" onclick="lwCopyToClipboard('lwHostRoot')">
                                    <?= __tr('Copy') ?>
                                </button>
                            </div>
                        </div>
                        @if (!Str::startsWith($hostRoot, 'https://'))
                            <div class="alert alert-danger my-3">
                                {{  __tr('Non https url may not accepted by Facebook.') }}
                            </div>
                        @endif
                    </div>
                    </div>
                </fieldset>
                @if(!$isExtendedLicence)
                <div class="alert alert-warning my-3">
                    <strong title="Extended Licence Required"><?= __tr('Extended Licence Required') ?></strong> <br>
                    <?= __tr('To use Embedded Signup you need to buy an Extended licence.') ?>
                </div>
                @endif
            @if($isExtendedLicence)
            <div x-show="embeddedSignUpDataExists"></div>
            <div class="form-group">
                <x-lw.checkbox id="lwEmbeddedSignupEnableField" name="enable_embedded_signup" :offValue="0" data-lw-plugin="lwSwitchery" :checked="getAppSettings('enable_embedded_signup')" :label="__tr('Enable Embedded Signup')" />
            </div>
            <div class="form-group" x-cloak x-show="embeddedSignUpDataExists">
                <div class="btn-group">
                    <button type="button" disabled="true" class="btn btn-success lw-btn">
                        {{ __tr('Embedded Signup Settings are exist') }}
                    </button>
                    <button type="button" @click="embeddedSignUpDataExists = !embeddedSignUpDataExists"
                        class="btn btn-light lw-btn">{{ __tr('Update') }}</button>
                </div>
            </div>
            <template x-if="!embeddedSignUpDataExists">
            <div x-show="!embeddedSignUpDataExists" class="col-sm-12 col-md-6 col-lg-4">
                <a href="https://developers.facebook.com/docs/whatsapp/cloud-api/get-started-for-tech-providers#step-2--create-a-meta-app" class="float-right btn btn-info btn-sm">{{  __tr('Help') }} <i class="fas fa-external-link-alt"></i></a>
                <x-lw.input-field type="text" id="lwEmbeddedSignUpAppId" data-form-group-class="" :label="__tr('App ID')"
                    name="embedded_signup_app_id" />
                <x-lw.input-field type="text" id="lwEmbeddedSignUpAppSecret" data-form-group-class="" :label="__tr('App Secret')" name="embedded_signup_app_secret" />
                <a href="https://developers.facebook.com/docs/whatsapp/embedded-signup/embed-the-flow#step-2--create-facebook-login-for-business-configuration" class="float-right btn btn-info btn-sm mt-3">{{  __tr('Help') }} <i class="fas fa-external-link-alt"></i></a>
                <x-lw.input-field type="text" id="lwEmbeddedSignUpConfigId" data-form-group-class="" :label="__tr('Config ID')" name="embedded_signup_config_id" />
            </div>
        </template>
            <div class="form-group">
                {{-- submit button --}}
                <button type="submit" href class="btn btn-primary btn-user lw-btn-block-mobile">
                    <?= __tr('Save') ?>
                </button>
            </div>
            @endif
        </form>
    @push('appScripts')
    <script>
        "use strict";
    </script>
    @endpush