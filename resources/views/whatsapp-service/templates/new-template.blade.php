@extends('layouts.app', ['title' => __tr('Create New Template')])
@section('content')
@include('users.partials.header', [
'title' => __tr('Create New Template'),
'description' => '',
'class' => 'col-lg-7'
])
<div class="container-fluid mt-lg--6">
    <div class="row">
        <div class="col-12 mb-3">
            <div class="float-right">
                <a class="lw-btn btn btn-secondary" href="{{ route('vendor.whatsapp_service.templates.read.list_view') }}">{{
                    __tr('Back to Templates') }}</a>
                    <a href="https://business.facebook.com/business/help/2055875911147364" target="_blank" class="btn btn-default">{{  __tr('Help') }}</a>
            </div>
        </div>
    </div>
    <div class="col-12" x-data="{
    headerType:'',
    header_text_body:'',
    footer_text_body:'',
    text_body:'',
    enableHeaderVariableExample:false,
    newBodyTextInputFields:[],
    buttonModels:{},
    customButtons:{
        totalAllowedButtons:10,
        totalButtonsUsed:0,
        buttonUsesByTypes:{
            URL_BUTTON:0,
            URL_BUTTON_LIMIT:2,
            COPY_CODE:0,
            COPY_CODE_LIMIT:1,
            VOICE_CALL:0,
            VOICE_CALL_LIMIT:1,
            PHONE_NUMBER:0,
            PHONE_NUMBER_LIMIT:1
        },
        totalUrlButtonUsed:0,
        data:{},
    }, addWhatsAppButtonOption : function(buttonType) {
        let uniqueBtnId = _.uniqueId();
        this.customButtons.data[uniqueBtnId] = {
            buttonType : buttonType,
            buttonIndex : uniqueBtnId
        };
        this.customButtons.totalButtonsUsed++;
        if((buttonType == 'URL_BUTTON') || (buttonType == 'DYNAMIC_URL_BUTTON')) {
            this.customButtons.buttonUsesByTypes['URL_BUTTON']++;
        } else if((buttonType == 'COPY_CODE')) {
            this.customButtons.buttonUsesByTypes['COPY_CODE']++;
        } else if((buttonType == 'VOICE_CALL')) {
            this.customButtons.buttonUsesByTypes['VOICE_CALL']++;
        } else if((buttonType == 'PHONE_NUMBER')) {
            this.customButtons.buttonUsesByTypes['PHONE_NUMBER']++;
        }
    }, deleteWhatsAppButtonOption : function(buttonIndex) {
        let buttonType = this.customButtons.data[buttonIndex]['buttonType'];
        if((buttonType == 'URL_BUTTON') || (buttonType == 'DYNAMIC_URL_BUTTON')) {
            this.customButtons.buttonUsesByTypes['URL_BUTTON']--;
        } else if((buttonType == 'COPY_CODE')) {
            this.customButtons.buttonUsesByTypes['COPY_CODE']-- ;
        } else if((buttonType == 'VOICE_CALL')) {
            this.customButtons.buttonUsesByTypes['VOICE_CALL']-- ;
        } else if((buttonType == 'PHONE_NUMBER')) {
            this.customButtons.buttonUsesByTypes['PHONE_NUMBER']-- ;
        }
        delete this.customButtons.data[buttonIndex];
        delete this.buttonModels[buttonIndex];
        this.customButtons.totalButtonsUsed--;
    }}">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-7">
                        <x-lw.form id="lwNewTemplateCreationForm"
                            :action="route('vendor.whatsapp_service.templates.write.create')">
                            <!-- Template Name -->
                            <x-lw.input-field type="text" id="lwTemplateNameField" data-form-group-class=""
                                :label="__tr('Template Name')" name="template_name" required="true" />
                            <span class="form-text text-muted mt-3 text-sm"><a target="_blank"
                                    href="https://developers.facebook.com/docs/whatsapp/message-templates/guidelines/">{{
                                    __tr('Template Formatting Help') }}</a></span>
                            <!-- /Template Name -->
                            <!-- Template Language -->
                            <x-lw.input-field type="text" id="lwTemplateLanguageField" data-form-group-class=""
                                :label="__tr('Template Language Code')" name="language_code" required="true" />
                            <!-- /Template Language -->
                            <x-lw.input-field type="selectize" data-lw-plugin="lwSelectize" id="lwSelectCategoryField"
                                data-form-group-class="" data-selected=" " :label="__tr('Category')" name="category">
                                <x-slot name="selectOptions">
                                    <option value="MARKETING">{{ __tr('MARKETING') }}</option>
                                    <option value="UTILITY">{{ __tr('UTILITY') }}</option>
                                    {{-- <option value="AUTHENTICATION">{{ __tr('AUTHENTICATION') }}</option> --}}
                                </x-slot>
                            </x-lw.input-field>
                            <fieldset>
                                <input id="lwMediaFileName" type="hidden" value="" name="uploaded_media_file_name" />
                                <legend>{{ __tr('Header') }} <small>{{ __tr('(Optional)') }}</small></legend>
                                <x-lw.input-field x-model="headerType" type="selectize" id="lwMediaHeaderType"
                                    data-form-group-class="" data-selected=" " :label="__tr('Header Type')"
                                    name="media_header_type">
                                    <x-slot name="selectOptions">
                                        <option value="0">{{ __tr('None') }}</option>
                                        <optgroup label="{{ __tr('Text') }}">
                                            <option value="text">{{ __tr('Text') }}</option>
                                        </optgroup>
                                        <optgroup label="{{ __tr('Media') }}">
                                            <option value="image">{{ __tr('Image') }}</option>
                                            <option value="video">{{ __tr('Video') }}</option>
                                            <option value="document">{{ __tr('Document') }}</option>
                                            <option value="location">{{ __tr('Location') }}</option>
                                        </optgroup>
                                    </x-slot>
                                </x-lw.input-field>
                                <div class="my-3">
                                    {{-- text --}}
                                    <div x-show="headerType == 'text'" class="form-group col-sm-12">
                                        <x-lw.input-field type="text" id="lwHeaderTextBody" data-form-group-class=""
                                            :label="__tr('Header Text')" x-model="header_text_body" name="header_text_body" />
                                        <div class="form-group text-right">
                                            <button :disabled="enableHeaderVariableExample" id="lwAddSinglePlaceHolder" class="btn btn-dark btn-sm" type="button">
                                                <i class="fa fa-plus"></i> {{ __tr('Add Variable') }}</button>
                                        </div>
                                        <template x-if="enableHeaderVariableExample">
                                            <x-lw.input-field type="text" id="lwHeaderTextBodyExample"
                                                data-form-group-class="" :label="__tr('Header Text Variable Example')"
                                                name="example_header_fields" />
                                        </template>
                                    </div>
                                    {{-- document --}}
                                    <div x-show="headerType == 'document'" class="form-group col-sm-12">
                                        <input id="lwDocumentMediaFilepond" type="file" data-allow-revert="true"
                                            data-label-idle="{{ __tr('Select Document') }}" class="lw-file-uploader"
                                            data-instant-upload="true"
                                            data-action="<?= route('media.upload_temp_media', 'whatsapp_document') ?>"
                                            id="lwDocumentField" data-file-input-element="#lwMediaFileName"
                                            data-allowed-media='<?= getMediaRestriction('whatsapp_document') ?>' />
                                    </div>
                                    {{-- image --}}
                                    <div x-show="headerType == 'image'" class="form-group col-sm-12">
                                        <input id="lwImageMediaFilepond" type="file" data-allow-revert="true"
                                            data-label-idle="{{ __tr('Select Image') }}" class="lw-file-uploader"
                                            data-instant-upload="true"
                                            data-action="<?= route('media.upload_temp_media', 'whatsapp_image') ?>"
                                            id="lwImageField" data-file-input-element="#lwMediaFileName"
                                            data-allowed-media='<?= getMediaRestriction('whatsapp_image') ?>' />
                                    </div>
                                    {{-- video --}}
                                    <div x-show="headerType == 'video'" class="form-group col-sm-12">
                                        <input id="lwVideoMediaFilepond" type="file" data-allow-revert="true"
                                            data-label-idle="{{ __tr('Select Video') }}" class="lw-file-uploader"
                                            data-instant-upload="true"
                                            data-action="<?= route('media.upload_temp_media', 'whatsapp_video') ?>"
                                            id="lwVideoField" data-file-input-element="#lwMediaFileName"
                                            data-allowed-media='<?= getMediaRestriction('whatsapp_video') ?>' />
                                    </div>
                                </div>
                            </fieldset>
                            <fieldset>
                                <legend>{{ __tr('Body') }}</legend>
                                <small>{{ __tr('Enter the text for your message in the language you\'ve selected.')
                                    }}</small>
                                <div class="form-group">
                                    <label for="lwTemplateBody">{{ __tr('Body Text') }}</label>
                                    <textarea name="template_body" id="lwTemplateBody" class="form-control" x-model="text_body" rows="10"></textarea>
                                </div>
                                <div class="form-group text-right">
                                    <button id="lwBoldBtn" class="btn btn-light btn-sm" type="button"> <i
                                            class="fa fa-bold"></i></button>
                                    <button id="lwItalicBtn" class="btn btn-light btn-sm" type="button"> <i
                                            class="fa fa-italic"></i></button>
                                    <button id="lwStrikeThroughBtn" class="btn btn-light btn-sm" type="button"> <i
                                            class="fa fa-strikethrough"></i></button>
                                    <button id="lwCodeBtn" class="btn btn-light btn-sm" type="button"> <i
                                            class="fa fa-code"></i></button>
                                    <button id="lwAddPlaceHolder" class="btn btn-dark btn-sm" type="button"> <i
                                            class="fa fa-plus"></i> {{ __tr('Add Variables') }}</button>
                                </div>
                                <div>
                                    <template x-if="_.size(newBodyTextInputFields)">
                                        <div>
                                            <h4>{{ __tr('Samples Text') }}</h4>
                                            <template x-for="(item, index) in newBodyTextInputFields" :key="index">
                                                <div class="form-group">
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">
                                                                <span x-text="item.text_variable"></span>
                                                            </span>
                                                        </div>
                                                        <input type="text" class="form-control"
                                                            x-bind:name="'example_body_fields[' + index + ']'"
                                                            required="required" />
                                                    </div>
                                                </div>
                                            </template>
                                        </div>
                                    </template>
                                </div>
                            </fieldset>
                            <x-lw.input-field type="text" id="lwTemplateFooter" data-form-group-class=""
                                :label="__tr('Footer (Optional)')" name="template_footer" x-model="footer_text_body" 
                                :helpText="__tr('Add a short line of text to the bottom of your message template.')" />
                            <fieldset>
                                <legend>{{ __tr('Buttons') }} <small>{{ __tr('(Optional)') }}</small></legend>
                                <div class="mb-4 ">
                                    <h3 class="text-muted">{{ __tr('Create buttons that let customers respond to your
                                        message or take action.')
                                        }}</h3>
                                </div>
                                <div class="lw-buttons-container">
                                    <div>
                                        <template x-for="customButtonData in customButtons.data">
                                            <div class="card shadow-none mb-2">
                                                <h3 class="card-header">
                                                    <template x-if="customButtonData.buttonType == 'QUICK_REPLY'">
                                                        <span>{{ __tr('Quick Reply Button') }}</span>
                                                    </template>
                                                    <template x-if="customButtonData.buttonType == 'PHONE_NUMBER'">
                                                        <span>{{ __tr('Phone Number Button') }}</span>
                                                    </template>
                                                    <template x-if="customButtonData.buttonType == 'URL_BUTTON'">
                                                        <span>{{ __tr('URL Button') }}</span>
                                                    </template>
                                                    <template x-if="customButtonData.buttonType == 'DYNAMIC_URL_BUTTON'">
                                                        <span>{{ __tr('Dynamic URL Button') }}</span>
                                                    </template>
                                                    <template x-if="customButtonData.buttonType == 'VOICE_CALL'">
                                                        <span>{{ __tr('WhatsApp Call Button') }}</span>
                                                    </template>
                                                    <template x-if="customButtonData.buttonType == 'COPY_CODE'">
                                                        <span>{{ __tr('Coupon Code Copy Button') }}</span>
                                                    </template>
                                                    {{-- delete button --}}
                                                    <button
                                                        @click.prevent="deleteWhatsAppButtonOption(customButtonData.buttonIndex)"
                                                        class="btn btn-link float-right p-1" type="button"><i class="fa fa-times text-danger"></i></button>
                                                </h3>
                                                <div class="card-body">
                                                    <input type="hidden"
                                                        x-bind:name="'message_buttons['+customButtonData.buttonIndex+'][type]'"
                                                        x-bind:value="customButtonData.buttonType">
                                                    <template
                                                        x-if="_.includes(['QUICK_REPLY','PHONE_NUMBER', 'URL_BUTTON', 'VOICE_CALL','DYNAMIC_URL_BUTTON'], customButtonData.buttonType)">
                                                        <x-lw.input-field x-bind:id="customButtonData.buttonIndex"
                                                            type="text" data-form-group-class="mt-4"
                                                            :label="__tr('Button Text')"
                                                            x-model="buttonModels[customButtonData.buttonIndex]"
                                                            x-bind:name="'message_buttons['+customButtonData.buttonIndex+'][text]'">
                                                            <x-slot name="prepend">
                                                                <span class="input-group-text"><i
                                                                        class="fa fa-font"></i></span>
                                                            </x-slot>
                                                        </x-lw.input-field>
                                                    </template>
                                                    <template x-if="customButtonData.buttonType == 'PHONE_NUMBER'">
                                                        <x-lw.input-field x-bind:id="customButtonData.buttonIndex"
                                                            type="number" data-form-group-class=""
                                                            :label="__tr('Phone Number')"
                                                            x-bind:name="'message_buttons['+customButtonData.buttonIndex+'][phone_number]'">
                                                            <x-slot name="prepend">
                                                                <span class="input-group-text"><i
                                                                        class="fa fa-phone-alt"></i></span>
                                                            </x-slot>
                                                        </x-lw.input-field>
                                                    </template>
                                                    <template x-if="customButtonData.buttonType == 'URL_BUTTON'">
                                                        <x-lw.input-field x-bind:id="customButtonData.buttonIndex"
                                                            type="url" data-form-group-class="mt-4"
                                                            :label="__tr('Website URL')"
                                                            x-bind:name="'message_buttons['+customButtonData.buttonIndex+'][url]'">
                                                            <x-slot name="prepend">
                                                                <span class="input-group-text"><i
                                                                        class="fa fa-link"></i></span>
                                                            </x-slot>
                                                        </x-lw.input-field>
                                                    </template>
                                                    <template x-if="customButtonData.buttonType == 'DYNAMIC_URL_BUTTON'">
                                                        <x-lw.input-field x-bind:id="customButtonData.buttonIndex"
                                                            type="url" data-form-group-class="mt-4"
                                                            :label="__tr('Website URL')"
                                                            x-bind:name="'message_buttons['+customButtonData.buttonIndex+'][url]'">
                                                            <x-slot name="prepend">
                                                                <span class="input-group-text"><i
                                                                        class="fa fa-link"></i></span>
                                                            </x-slot>
                                                            <x-slot name="append">
                                                                <span class="input-group-text">@{{1}}</span>
                                                            </x-slot>
                                                        </x-lw.input-field>
                                                    </template>
                                                    <template
                                                        x-if="_.includes(['COPY_CODE', 'DYNAMIC_URL_BUTTON'],customButtonData.buttonType)">
                                                        <x-lw.input-field x-bind:id="customButtonData.buttonIndex"
                                                            type="text" data-form-group-class="mt-4"
                                                            :label="__tr('Example')"
                                                            x-bind:name="'message_buttons['+customButtonData.buttonIndex+'][example]'" />
                                                    </template>
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                    <div class="mt-4">
                                        <button
                                            :disabled="customButtons.totalButtonsUsed >= customButtons.totalAllowedButtons"
                                            class="btn btn-dark btn-sm" type="button"
                                            @click.prevent="addWhatsAppButtonOption('QUICK_REPLY')"><i
                                                class="fa fa-reply"></i> {{ __tr('Quick Reply Button') }}</button>
                                        <button
                                            :disabled="(customButtons.totalButtonsUsed >= customButtons.totalAllowedButtons) || (customButtons.buttonUsesByTypes.PHONE_NUMBER >= customButtons.buttonUsesByTypes.PHONE_NUMBER_LIMIT)"
                                            class="btn btn-dark btn-sm" type="button"
                                            @click.prevent="addWhatsAppButtonOption('PHONE_NUMBER')"><i
                                                class="fa fa-phone-alt"></i> {{ __tr('Phone Number Button') }}</button>
                                      {{--   <button
                                            :disabled="(customButtons.totalButtonsUsed >= customButtons.totalAllowedButtons) || (customButtons.buttonUsesByTypes.VOICE_CALL >= customButtons.buttonUsesByTypes.VOICE_CALL_LIMIT)"
                                            class="btn btn-dark btn-sm" type="button"
                                            @click.prevent="addWhatsAppButtonOption('VOICE_CALL')"><i
                                                class="fab fa-whatsapp"></i> {{ __tr('WhatsApp Call Button') }}</button> --}}
                                        <button
                                            :disabled="(customButtons.totalButtonsUsed >= customButtons.totalAllowedButtons) || (customButtons.buttonUsesByTypes.COPY_CODE >= customButtons.buttonUsesByTypes.COPY_CODE_LIMIT)"
                                            class="btn btn-dark btn-sm" type="button"
                                            @click.prevent="addWhatsAppButtonOption('COPY_CODE')"><i
                                                class="fa fa-clipboard"></i> {{ __tr('Copy Code Button') }}</button>
                                        <button
                                            :disabled="(customButtons.totalButtonsUsed >= customButtons.totalAllowedButtons) || (customButtons.buttonUsesByTypes.URL_BUTTON >= customButtons.buttonUsesByTypes.URL_BUTTON_LIMIT)"
                                            class="btn btn-dark btn-sm" type="button"
                                            @click.prevent="addWhatsAppButtonOption('URL_BUTTON')"> <i
                                                class="fa fa-link"></i> {{ __tr('URL Button') }}</button>
                                        <button
                                            :disabled="(customButtons.totalButtonsUsed >= customButtons.totalAllowedButtons) || (customButtons.buttonUsesByTypes.URL_BUTTON >= customButtons.buttonUsesByTypes.URL_BUTTON_LIMIT)"
                                            class="btn btn-dark btn-sm" type="button"
                                            @click.prevent="addWhatsAppButtonOption('DYNAMIC_URL_BUTTON')"> <i
                                                class="fa fa-link"></i> {{ __tr('Dynamic URL Button') }}</button>
                                        <template
                                            x-if="customButtons.totalButtonsUsed >= customButtons.totalAllowedButtons">
                                            <div class="alert alert-danger mt-4">
                                                {{ __tr('You have reached maximum buttons allowed by Meta for template') }}
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </fieldset>
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary">{{ __('Submit') }}</button>
                            </div>
                        </x-lw.form>
                    </div>
                    <div class="col-md-1"></div>
                    <div class="col-md-4">
                        <div class="lw-whatsapp-template-create-preview">
                            <h3>{{  __tr('Template Preview') }}</h3>
                            <div class="lw-whatsapp-preview-container">
                                <img class="lw-whatsapp-preview-bg" src="{{ asset('imgs/wa-message-bg.png') }}" alt="">
                                <div class="lw-whatsapp-preview">
                                    <div class="card ">
                                        <div x-show="headerType && (headerType != 'text')" class="lw-whatsapp-header-placeholder">
                                            <i x-show="headerType == 'video'" class="fa fa-5x fa-play-circle text-white"></i>
                                            <i x-show="headerType == 'image'" class="fa fa-5x fa-image text-white"></i>
                                            <i x-show="headerType == 'location'" class="fa fa-5x fa-map-marker-alt text-white"></i>
                                            <i x-show="headerType == 'document'" class="fa fa-5x fa-file-alt text-white"></i>
                                        </div>
                                        <div x-show="headerType == 'location'" class="lw-whatsapp-location-meta bg-secondary p-2">
                                            <small>@{{location_name}}</small><br>
                                            <small>@{{address}}</small>
                                        </div>
                                        <div x-show="headerType == 'text'" class="lw-whatsapp-body mb--3">
                                            <strong x-text="header_text_body"></strong>
                                            </div>
                                        <div class="lw-whatsapp-body lw-ws-pre-line" x-html="appFuncs.formatWhatsAppText(text_body)"></div>
                                        <div class="lw-whatsapp-footer text-muted" x-text="footer_text_body"></div>
                                        <div class="card-footer lw-whatsapp-buttons">
                                            <div class="list-group list-group-flush lw-whatsapp-buttons">
                                                    <template x-for="(customButtonData, index) in customButtons.data" :key="index">
                                                        <div>
                                                            <div class="list-group-item">
                                                                <template x-if="customButtonData.buttonType == 'QUICK_REPLY'">
                                                                    <i class="fa fa-reply"></i>
                                                                </template>
                                                                <template x-if="customButtonData.buttonType == 'PHONE_NUMBER'">
                                                                    <i class="fa fa-phone-alt"></i>
                                                                </template>
                                                                <template x-if="customButtonData.buttonType == 'URL_BUTTON'">
                                                                    <i class="fas fa-external-link-square-alt"></i>
                                                                </template>
                                                                <template x-if="customButtonData.buttonType == 'DYNAMIC_URL_BUTTON'">
                                                                    <i class="fas fa-external-link-square-alt"></i>
                                                                </template>
                                                                <template x-if="customButtonData.buttonType == 'VOICE_CALL'">
                                                                    <i class="fab fa-whatsapp"></i><i class="fa fa-phone-alt"></i>
                                                                </template>
                                                                <template x-if="customButtonData.buttonType == 'COPY_CODE'">
                                                                    <span><i class="fa fa-copy"></i> {{  __tr('Copy Code') }}</span>
                                                                </template>
                                                                <span x-text="buttonModels[customButtonData.buttonIndex]"></span>
                                                            </div>
                                                            <template x-if="index == 3">
                                                                <div class="list-group-item"><i class="fa fa-menu"></i> {{ __tr('See all options') }} <br><small class="text-orange">{{  __tr('More than 3 buttons will be shown in the list by clicking') }}</small></div>
                                                            </template>
                                                        </div>
                                                    </template>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection()
@push('appScripts')
<?= __yesset([
            'dist/js/whatsapp-template.js',
        ],true,
) ?>
@endpush