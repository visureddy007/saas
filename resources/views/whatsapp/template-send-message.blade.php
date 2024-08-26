@extends('layouts.app', ['title' => $contact ? __tr('Send WhatsApp Template Message') : __tr('Create New Campaign')])
@section('content')
@include('users.partials.header', [
'title' => $contact ? __tr('Send WhatsApp Template Message') : __tr('Create New Campaign'),
'description' => '',
// 'class' => 'col-lg-7'
])

<div class="container-fluid mt-lg--6">
          <div class="row">
              <!-- button -->
    <div class="col-xl-12 mb-3 text-right">
        @if ($contact)
        <a class="lw-btn btn btn-secondary" href="{{ route('vendor.contact.read.list_view') }}">{{ __tr('Back to Contacts') }}</a>
        @endif
        <a class="lw-btn btn btn-secondary lw-ajax-link-action" data-confirm="{{ __tr('On template sync page will be refreshed') }}" data-callback="__Utils.viewReload" data-method="post" href="{{ route('vendor.whatsapp_service.templates.write.sync') }}" > {{ __tr('Sync WhatsApp Templates') }}</a>
        <a class="lw-btn btn btn-dark" href="{{ route('vendor.campaign.read.list_view') }}">{{ __tr('Manage Campaigns') }}</a>
    </div>
    <!--/ button -->
    <div class="col-12">
        <div class="card">
            @if ($contact)
            <div class="card-header">
                <div>{{  __tr('Name') }} : {{ $contact->full_name }}</div>
                <div>{{  __tr('Phone') }} : {{ $contact->wa_id }}</div>
                <div>{{  __tr('Country') }} : {{ $contact->country?->name }}</div>
            </div>
            @else
                @if(!getVendorSettings('test_recipient_contact'))
                <div class="card-body">
                    <div class="alert alert-danger">
                        {{  __tr('Test Contact missing, You need to set the Test Contact first, do it under the WhatsApp Settings') }}
                    </div>
                </div>
                @endif
            @endif
            <div class="card-body" x-data="{selectedTemplate:'' }">
                <div class="col-sm-12 col-md-8 col-lg-6">
                    @if (!$contact)
                    <h2 class="text-warning">{{  __tr('Step 1') }}</h2>
                    @endif
                    <x-lw.form lwSubmitOnChange data-event-callback="lwPrepareUploadPlugIn"
                        :action="route('vendor.request.template.view')" data-pre-callback="clearTemplateContainer">
                        <div x-cloak x-show="!selectedTemplate">
                            <x-lw.input-field x-model="selectedTemplate"
                                placeholder="{!! __tr('Select & Configure Template') !!}" type="selectize"
                                data-lw-plugin="lwSelectize" data-selected=" " type="select"
                                id="lwField_templateSelection" name="template_selection" data-form-group-class=""
                                class="custom-select" data-selected=" " :label="__tr('Select Template')">
                                <x-slot name="selectOptions">
                                    <option value="">{{ __tr('Select & Configure Template') }}</option>
                                    @foreach ($whatsAppTemplates as $whatsAppTemplate)
                                    <option value="{{ $whatsAppTemplate->_uid }}">{{ $whatsAppTemplate->template_name }}
                                        ({{ $whatsAppTemplate->language }})</option>
                                    @endforeach
                                </x-slot>
                            </x-lw.input-field>
                        </div>
                    </x-lw.form>
                </div>
                <div x-cloak class="col-12">
                        @if ($contact)
                        <x-lw.form x-show="selectedTemplate" :action="route('vendor.template_message.contact.process', [
                            'contactUid' => $contact->_uid
                        ])">
                            <input type="hidden" name="contact_uid" value="{{ $contact->_uid }}">
                            <div id="lwTemplateStructureContainer">
                                {!! $template !!}
                            </div>
                             @include('whatsapp.from-phone-number')
                            <button type="submit" class="btn btn-primary mt-4">{{ __('Send') }}</button>
                        </x-lw.form>
                        @else
                        {{-- Campaign Creation --}}
                        <x-lw.form x-show="selectedTemplate" :action="route('vendor.campaign.schedule.process')" data-confirm="#lwScheduleMessageConfirmation">
                            <div id="lwTemplateStructureContainer">
                                {!! $template !!}
                            </div>
                            <h2 class="mt-5 text-warning">{{  __tr('Step 2') }}</h2>
                           <fieldset class="col-sm-12 col-md-8 col-lg-6">
                            <legend>{{  __tr('Contacts and Schedule') }}</legend>
                            <x-lw.input-field type="text" id="lwCampaignTitle" data-form-group-class="" :label="__tr('Campaign Title')" name="title" required="required" />
                             {{-- select group --}}
                             <x-lw.input-field type="selectize" data-lw-plugin="lwSelectize" id="lwSelectGroupsField"
                             data-form-group-class="" data-selected=" " :label="__tr('Groups/Contact')" name="contact_group">
                             <x-slot name="selectOptions">
                                 <option value="">{{ __tr('Select Contacts Group') }}</option>
                                 <option value="all_contacts">{{ __tr('All Contacts') }}</option>
                                 @foreach($vendorContactGroups as $vendorContactGroup)
                                 <option value="{{ $vendorContactGroup['_id'] }}">{{ $vendorContactGroup['title'] }}</option>
                                 @endforeach
                             </x-slot>
                         </x-lw.input-field>
                                 {{-- /select group --}}
                                  <!-- Restrict by Template Language field -->
                                <div class="form-group pt-3">
                                    <label for="lwOnlyForTemplateLanguageMatchingContact">
                                        <input type="checkbox" id="lwOnlyForTemplateLanguageMatchingContact" data-lw-plugin="lwSwitchery" data-color="#ff0000" name="restrict_by_templated_contact_language">
                                       {!! __tr('Restrict by Language Code - Send only to the contacts whose language code matches with template language code.') !!}
                                    </label>
                                </div>
                                <fieldset x-data="{scheduleNow:true}">
                                    <legend>{{  __tr('Schedule') }}</legend>
                                    <div class="form-group pt-3">
                                        <label for="lwNowCampaign">
                                            <input x-model="scheduleNow" type="checkbox" id="lwNowCampaign" data-lw-plugin="lwSwitchery" checked data-color="orange" value="" name="schedule_now">
                                          {{ __tr('Now') }}
                                        </label>
                                    </div>
                                    <div x-show="!scheduleNow">
                                        <x-lw.input-field  type="selectize" data-form-group-class="" name="timezone" :label="__tr('Select your Timezone')" data-selected="{{ getVendorSettings('timezone') }}">
                                            <x-slot name="selectOptions">
                                                @foreach (getTimezonesArray() as $timezone)
                                                    <option value="{{ $timezone['value'] }}">{{ $timezone['text'] }}</option>
                                                @endforeach
                                            </x-slot>
                                        </x-lw.input-field>
                                    </div>
                                    <template x-if="!scheduleNow">
                                        <div x-show="!scheduleNow">
                                            <x-lw.input-field  type="datetime-local" id="lwScheduleAt" data-form-group-class="" min="{{ formatDateTime(now(), 'Y-m-d\TH:i:s') }}" :label="__tr('Schedule At')" name="schedule_at" required />
                                        </div>
                                    </template>
                                </fieldset>
                           </fieldset>
                           @include('whatsapp.from-phone-number')
                           <div class="my-4">
                            <button type="submit" class="btn btn-primary">{{ __('Schedule Campaign') }}</button>
                           </div>
                        </x-lw.form>
                        <template type="text/template" id="lwScheduleMessageConfirmation">
                            <h3>{{  __tr('Are you sure?') }}</h3>
                            <p>{{  __tr('You want to schedule a WhatsApp Template Message. Test message will be sent to your selected test contact immediately and on success it will get scheduled for the selected group contacts ') }}</p>
                        </template>
                        @endif
                </div>
            </div>
        </div>
    </div>
          </div>
</div>
@endsection()
@push('appScripts')
<script>
    (function($){
            'use strict';
            window.clearTemplateContainer = function(inputData) {
                $('#lwTemplateStructureContainer').text('');
                return inputData;
            };
            @if(request()->use_template)
            // Initial Change if required
            __DataRequest.post('{{ route('vendor.request.template.view') }}', {
                'template_selection' : '{{ request()->use_template }}',
            }, function() {
                __DataRequest.updateModels({selectedTemplate:'{{ request()->use_template }}'});
                    _.defer(function(){
                        if ($('#lwTemplateStructureContainer').find('.lw-file-uploader').length) {
                        window.initUploader();
                    }
                });
            }, {
                eventStreamUpdate: true
            });
            @endif
        })(jQuery);
</script>
@endpush