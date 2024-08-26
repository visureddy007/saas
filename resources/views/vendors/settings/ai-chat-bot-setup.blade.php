<div class="row">
    <div class="col-md-8"
    x-data="{ enableStep2: {{ getVendorSettings('facebook_app_id') ? 1 : 0 }}, enableStep3: {{ getVendorSettings('whatsapp_access_token') ? 1 : 0 }} }"
    x-cloak>
    <!-- Page Heading -->
    <h1>
        <img src="{{ asset('imgs/flowise-ai-logo.png') }}" alt="{{ __tr('FlowiseAI') }}">
        <?= __tr('FlowiseAI ChatBot Setup') ?>
    </h1>
    <fieldset>
        <legend>{{  __tr('FlowiseAI Introduction') }}</legend>
        <p>{{  __tr('FlowiseAI is a platform designed to simplify the creation and management of chatbots by leveraging OpenAI\'s powerful AI models, including GPT (Generative Pre-trained Transformer). It provides users with tools to design, build, and deploy AI-powered chatbots tailored to a wide range of applications, from customer service and support to personalized interactions and engagement. FlowiseAI aims to make the development of intelligent chatbots accessible to businesses and developers of all sizes, emphasizing ease of use, scalability, and integration capabilities. By utilizing FlowiseAI, organizations can enhance their customer experience, automate responses to frequently asked questions, and offer real-time assistance without the need for extensive coding knowledge.') }}</p>
        <p>{{  __tr('You can learn more about flowiseAI from links given below') }}</p>
        <p>
            <a class="btn btn-light" href="https://flowiseai.com/" target="_blank">{{  __tr('Official Website') }}</a>
            <a class="btn btn-danger" href="https://www.youtube.com/watch?v=tD6fwQyUIJE&list=PL4HikwTaYE0HDOuXMm5sU6DH6_ZrHBLSJ" target="_blank"><i class="fab fa-youtube"></i> {{  __tr('Video Tutorials') }}</a>
        </p>
        <p>
            {{  __tr('Whatever the bot you create using FlowiseAI you need to grab the url from CURL option, and needs to place it under following url input field') }}
            <div>
                <img class="img-fluid" src="{{ asset('imgs/flowise-ai-curl-url.png') }}" alt="{{ __tr('Get FlowiseAI URL') }}">
            </div>
        </p>
    </fieldset>
    <div class="alert alert-warning my-4">
        {{  __tr('Enabled Chat bot only get triggered if manual chat bot did not respond and contact has enabled for AI Bot reply.') }}
    </div>
    <fieldset class="lw-fieldset mb-3" >
            <legend>{!! __tr('Configure to use FlowiseAI for Chat Bot') !!}</legend>
            <div>
                @php
                $vendorId = getVendorId();
                // check the feature limit
                $vendorPlanDetails = vendorPlanDetails('ai_chat_bot', 0, $vendorId);
                @endphp
                @if ($vendorPlanDetails['is_limit_available'])
                <!-- whatsapp cloud api setup form -->
                <form id="lwWhatsAppFacebookAppForm" class="lw-ajax-form lw-form"
                    name="ai_bot_setup_page" method="post"
                    action="<?= route('vendor.settings.write.update') ?>" x-data="{lwFlowiseUrlExists:{{ getVendorSettings('flowise_url') ? 1 : 0 }}}">
                    <input type="hidden" name="pageType" value="flowise_ai_bot_setup">
                    <!-- set hidden input field with form type -->
                    <input type="hidden" name="form_type" value="ai_bot_setup_page" />

                    <x-lw.checkbox id="enableFlowiseAiBot" name="enable_flowise_ai_bot" :checked="getVendorSettings('enable_flowise_ai_bot')" data-lw-plugin="lwSwitchery" :label="__tr('Enable FlowiseAI Chat Bot')" />
                    <div class="form-group" x-cloak x-show="lwFlowiseUrlExists">
                        <div class="btn-group">
                            <button type="button" disabled="true" class="btn btn-success lw-btn">
                                {{ __tr('FlowiseAI Settings are exist') }}
                            </button>
                            <button type="button" @click="lwFlowiseUrlExists = !lwFlowiseUrlExists"
                                class="btn btn-light lw-btn">{{ __tr('Update') }}</button>
                        </div>
                    </div>
                    <div class="mt-4">
                        <x-lw.checkbox id="enableFlowiseAiBotByDefaultForAllUsers" name="default_enable_flowise_ai_bot_for_users" :checked="getVendorSettings('default_enable_flowise_ai_bot_for_users')" data-lw-plugin="lwSwitchery" :label="__tr('Enable FlowiseAI Chat Bot by default for All New Contacts')" />
                        <div class="help-text text-muted">{{  __tr('It will enable for AI Chat bot for contacts created using incoming messages, import etc.') }}</div>
                    </div>
                <div x-show="!lwFlowiseUrlExists">

                    {{-- flowise ai chat url --}}
                    <x-lw.input-field placeholder="{{ __tr('Your Flowise Bot URL') }}"
                        type="text" id="lwFlowiseAiUrl" data-form-group-class="col-md-12 col-lg-8"
                        :label="__tr('Your Flowise Bot URL')" name="flowise_url" :helpText="__tr('You need to get this url from the your FlowiseAi Chat CURL tab.')" />
                    {{-- flowise ai chat access token if required --}}
                    <x-lw.input-field placeholder="{{ __tr('Authorization Bearer Token (optional)') }}"
                    type="text" id="lwFlowiseAiUrl" data-form-group-class="col-md-12 col-lg-8"
                    :label="__tr('Authorization Bearer Token (optional)')" name="flowise_access_token" :helpText="__tr('If you have added authorization using bearer token, you need to add it here.')" />
                    <x-lw.input-field placeholder="{{ __tr('Message on AI Bot Failed') }}" type="text" id="lwFlowiseFailedMessage" data-form-group-class="col-md-12 col-lg-8"
                    :label="__tr('Message on AI Bot Failed')" value="{{ getVendorSettings('flowise_failed_message') }}" name="flowise_failed_message" :helpText="__tr('If for some reason AI Bot failed to respond this error message will be sent to contact WhatsApp, Leave blank if you do not want to send such a message.')" />
                </div>
                <hr>
                <div class="form-group m-3">
                    <!-- Update Button -->
                    <button type="submit" class="btn btn-primary btn-user lw-btn-block-mobile">
                        <?= __tr('Save') ?>
                    </button>
                    <!-- /Update Button -->
                </div>
                </form>
                <!-- / whatsapp cloud api setup form -->
                @else
                    <div class="alert alert-danger">
                        {{  __tr('This Feature is not available in your plan, please upgrade your subscription plan.') }}
                    </div>
                @endif
            </div>
        </fieldset>
</div>
</div>