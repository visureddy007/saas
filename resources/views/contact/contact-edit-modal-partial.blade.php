<x-lw.modal id="lwEditContact" :header="__tr('Edit Contact')" :hasForm="true">
    <!--  Edit Contact Form -->
    <x-lw.form id="lwEditContactForm" :action="route('vendor.contact.write.update')"
        :data-callback-params="['modalId' => '#lwEditContact', 'datatableId' => '#lwContactList']"
        data-callback="onUpdateContactDetails">
        <!-- form body -->
        <div id="lwEditContactBody" class="lw-form-modal-body"></div>
        <script type="text/template" id="lwEditContactBody-template">

            <input type="hidden" name="contactIdOrUid" value="<%- __tData._uid %>" />
                <!-- form fields -->
                <!-- First_Name -->
                <x-lw.input-field type="text" id="lwFirstNameEditField" data-form-group-class="" :label="__tr('First Name')" value="<%- __tData.first_name %>" name="first_name"   />
                <!-- /First_Name -->
                <!-- Last_Name -->
                <x-lw.input-field type="text" id="lwLastNameEditField" data-form-group-class="" :label="__tr('Last Name')" value="<%- __tData.last_name %>" name="last_name"   />
                <!-- /Last_Name -->
        <!-- Country -->
                <x-lw.input-field type="selectize" data-lw-plugin="lwSelectize" id="lwCountryEditField" data-form-group-class="" data-selected="<%- __tData.countries__id %>" :label="__tr('Country')" name="country"  >
            <x-slot name="selectOptions">
                <option value=" ">{{ __tr('Country') }}</option>
                @foreach(getCountryPhoneCodes() as $getCountryCode)
                <option value="{{ $getCountryCode['_id'] }}">{{ $getCountryCode['name'] }}</option>
                @endforeach
            </x-slot>
        </x-lw.input-field>
        <!-- /Country -->
        <!-- Phone_Number -->
       <x-lw.input-field class="disabled" disabled type="text" id="lwPhoneNumberEditField"  minlength="9" data-form-group-class="" :label="__tr('Mobile Number')" value="<%- __tData.wa_id %>" name="phone_number"  />
        <!-- /Phone_Number -->
        <!-- Language Code -->
        <x-lw.input-field type="text" id="lwLanguageCodeEditField" data-form-group-class=""
        :label="__tr('Language Code')" name="language_code" value="<%- __tData.language_code %>" />
    <!-- /Language Code -->
        <!-- Email -->
        <x-lw.input-field type="email" id="lwEmailEditField" data-form-group-class="" :label="__tr('Email')" value="<%- __tData.email %>" name="email"/>
        <!-- /Email -->
        <x-lw.input-field type="selectize" data-lw-plugin="lwSelectize" id="lwEditSelectGroupsField" data-form-group-class="" data-selected="<%- __tData.existingGroupIds %>" :label="__tr('Groups')" name="contact_groups[]" multiple >
        <x-slot name="selectOptions">
            <option value="">{{ __tr('Select Groups') }}</option>
            @foreach($vendorContactGroups as $vendorContactGroup)
            <option value="{{ $vendorContactGroup['_id'] }}">{{ $vendorContactGroup['title'] }} {{ $vendorContactGroup['status'] == 5  ? __tr('(Archived)') : '' }}</option>
            @endforeach
        </x-slot>
    </x-lw.input-field>
    <label for="lwEditOptOutMarketingMessages" class="flex items-center my-3">
        <input id="lwEditOptOutMarketingMessages" type="checkbox" <%- (__tData.whatsapp_opt_out) ? 'checked' : '' %> name="whatsapp_opt_out" data-color="#ff0000" data-size="small" class="form-checkbox" data-lw-plugin="lwSwitchery"> <span class="ml-2 text-gray-600">{{  __tr('Opt out Marketing Messages') }}</span>
        </label>
    @if(getVendorSettings('enable_flowise_ai_bot') and getVendorSettings('flowise_url'))
    <label for="lwEnableAiChatBot" class="flex items-center my-3">
    <input id="lwEnableAiChatBot" type="checkbox" <%- (!__tData.disable_ai_bot) ? 'checked' : '' %> name="enable_ai_bot" class="form-checkbox" data-lw-plugin="lwSwitchery"> <span class="ml-2 text-gray-600">{{  __tr('Enable AI Bot') }}</span>
    </label>
    @endif
    <fieldset>
        <legend>{{  __tr('Other Information') }}</legend>
        @foreach ($vendorContactCustomFields as $vendorContactCustomField)
        <x-lw.input-field type="{{ $vendorContactCustomField->input_type }}" id="lwCustomField{{ $vendorContactCustomField->_id }}" data-form-group-class="" value="<%- _.get(_.find(__tData.custom_field_values, {'contact_custom_fields__id' : {{ $vendorContactCustomField->_id }} }), 'field_value') %>" :label="$vendorContactCustomField->input_name" name="custom_input_fields[{{ $vendorContactCustomField->_uid }}]" />
        @endforeach
    </fieldset>
    </script>
        <!-- form footer -->
        <div class="modal-footer">
            <!-- Submit Button -->
            <button type="submit" class="btn btn-primary">{{ __('Submit') }}</button>
            <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __tr('Close') }}</button>
        </div>
    </x-lw.form>
    <!--/  Edit Contact Form -->
</x-lw.modal>