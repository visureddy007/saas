@php
$onlyTemplatePreview = request()->has('only-preview');
@endphp
<div class="row" x-cloak>
    @if(!$onlyTemplatePreview)
    <div class="col-sm-12 col-md-8 col-lg-6 lw-template-structure-form">
        <input type="hidden" name="template_uid" value="{{ $template->_uid }}">
        <fieldset>
            <legend>{{ __tr('Template') }} <template x-if="selectedTemplate">
                    <button class="btn btn-secondary btn-sm" @click.prevent="selectedTemplate = ''">{{ __tr('Change')
                        }}</button>
                </template></legend>
            <h3><strong>{{ $template->template_name }}</strong></h3>
            <h4>{{ __tr('Language Code') }}: <strong>{{ $template->language }}</strong></h4>
            <h4>{{ __tr('Category') }}: <strong>{{ $template->category }}</strong></h4>
        </fieldset>
        {{-- Header --}}
        @if($headerFormat)
        <fieldset class="lw-template-header-variables-container">
            <legend>{{ __tr('Header') }}</legend>
            @if($headerFormat == 'LOCATION')
            <h3>{{ __tr('Location Details') }}</h3>
            @include('whatsapp-service.template-partial', [
            'parameters' => [
            'location_latitude',
            'location_longitude',
            'location_name',
            'location_address',
            ],
            'subjectType' => 'header',
            ])
            @elseif($headerFormat == 'TEXT' and !__isEmpty($headerParameters))
            @include('whatsapp-service.template-partial', [
            'parameters' => $headerParameters,
            'subjectType' => 'header',
            ])
            @elseif($headerFormat == 'TEXT' and __isEmpty($headerParameters))
            <div class="alert alert-info">{{  __tr('No variables available for header text.') }}</div>
            <style>
                .lw-template-header-variables-container{display:none;}
            </style>
            @elseif($headerFormat == 'IMAGE')
            <div class="form-group col-md-4 col-sm-12">
                <label for="lwHeaderImageFilepond">{{ __tr('Select Image') }}</label>
                <input id="lwHeaderImageFilepond" type="file" data-allow-revert="true"
                    data-label-idle="{{ __tr('Select Image') }}" class="lw-file-uploader" data-instant-upload="true"
                    data-action="<?= route('media.upload_temp_media', 'whatsapp_image') ?>" data-allowed-media='{{ getMediaRestriction('whatsapp_image') }}'
                    data-file-input-element="#lwHeaderImage">
                <input id="lwHeaderImage" type="hidden" value="" name="header_image" />
            </div>
            @elseif($headerFormat == 'VIDEO')
            <div class="form-group col-md-4 col-sm-12">
                <label for="lwHeaderVideoFilepond">{{ __tr('Select Video') }}</label>
                <input id="lwHeaderVideoFilepond" type="file" data-allow-revert="true"
                    data-label-idle="{{ __tr('Select Video') }}" class="lw-file-uploader" data-instant-upload="true"
                    data-action="<?= route('media.upload_temp_media', 'whatsapp_video') ?>" data-allowed-media='{{ getMediaRestriction('whatsapp_video') }}'
                    data-file-input-element="#lwHeaderVideo">
                <input id="lwHeaderVideo" type="hidden" value="" name="header_video" />
            </div>
            @elseif($headerFormat == 'DOCUMENT')
            <div class="form-group col-md-4 col-sm-12">
                <label for="lwHeaderDocumentFilepond">{{ __tr('Select Document') }}</label>
                <input id="lwHeaderDocumentFilepond" type="file" data-allow-revert="true"
                    data-label-idle="{{ __tr('Select Document') }}" class="lw-file-uploader" data-instant-upload="true"
                    data-action="<?= route('media.upload_temp_media', 'whatsapp_document') ?>" data-allowed-media='{{ getMediaRestriction('whatsapp_document') }}'
                    data-file-input-element="#lwHeaderDocument">
                <input id="lwHeaderDocument" type="hidden" value="" name="header_document" />
            </div>
            @include('whatsapp-service.template-partial', [
            'parameters' => [
            'header_document_name'
            ],
            'subjectType' => 'header',
            ])
            @endif
        </fieldset>
        @endif
        {{-- /Header --}}
        {{-- Body Variables --}}
        @if(!__isEmpty($bodyParameters))
        <fieldset>
            <legend>{{ __tr('Body') }}</legend>
            @include('whatsapp-service.template-partial', [
            'parameters' => $bodyParameters,
            'subjectType' => 'body',
            ])
        </fieldset>
        @endif
        {{-- /Body Variables --}}
        {{-- Button Variables --}}
        @if(!__isEmpty($buttonParameters) or !__isEmpty($buttonItems))
        <fieldset>
            <legend>{{ __tr('Buttons') }}</legend>
            @include('whatsapp-service.template-partial', [
            'parameters' => $buttonParameters,
            'buttonItems' => $buttonItems,
            'subjectType' => 'button',
            ])
            @if(array_key_exists('COPY_CODE', $buttonItems))
            <label for="">{{ __tr('Code for Copy') }}</label>
            @include('whatsapp-service.template-partial', [
            'parameters' => [
            'copy_code'
            ],
            'buttonItems' => [],
            'subjectType' => 'button',
            ])
            @endif
        </fieldset>
        @endif
        {{-- /Button Variables --}}
    </div>
    {{-- Message Preview --}}
    <div class="col-sm-12 col-md-8 col-lg-6">
        <fieldset class="position-absolute w-100">
            <legend>{{ __tr('Message Preview') }}</legend>
            <div class="card">
                <div class="card-body">
                    @else
                    <div class="col-12">
                    @endif
                    @include('whatsapp-service.template-preview-partial', [
            'bodyComponentText' => $bodyComponentText,
            'parameters' => $bodyParameters,
            'subjectType' => 'body',
            'templateComponents' => $templateComponents
            ])
 </div>
@if(!$onlyTemplatePreview)
            </div>
            <div class="alert alert-light mt-5">
                <strong>{{  __tr('Please note:') }}</strong>
               {!! __tr('Words like @{{1}}, @{{abc}} etc are dynamic variables and will be replaced based on your selections.') !!}
            </div>
            @endif
        </fieldset>
    </div>
</div>