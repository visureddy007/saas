<x-lw.modal id="lwBusinessProfileUpdate" :header="__tr('Update Business Profile')" :hasForm="true">
    <!--  Edit Contact Form -->
    <x-lw.form id="lwBusinessProfileUpdateForm" :action="route('vendor.whatsapp.business_profile.write')"
        :data-callback-params="['modalId' => '#lwBusinessProfileUpdate']" data-callback="appFuncs.modelSuccessCallback">
        <!-- form body -->
        <div id="lwBusinessProfileUpdateBody" class="lw-form-modal-body"></div>
        <script type="text/template" id="lwBusinessProfileUpdateBody-template">
            <% if(__tData.businessProfile?.profile_picture_url) {  %>
            <img  class="lw-business-profile-image" src="<%- __tData.businessProfile?.profile_picture_url  %>" alt="">
            <% } else {  %>
                <h3>{{ __tr('Profile Image Not available') }}</h3>
            <% }  %>
            <fieldset class="text-center">
                <legend for="">{{  __tr('New Profile Image') }}</legend>
                <input type="hidden" name="phoneNumberId" value="<%- __tData.phoneNumberId %>" />
                <div class="form-group col-sm-12">
                    <input id="lwMediaFileName" type="hidden" value="" name="uploaded_media_file_name" />
                    <input id="lwImageMediaFilepond" type="file" data-allow-revert="true"
                        data-label-idle="{{ __tr('Select Image') }}" class="lw-file-uploader"
                        data-instant-upload="true"
                        data-action="<?= route('media.upload_temp_media', 'whatsapp_image') ?>"
                        id="lwImageField" data-file-input-element="#lwMediaFileName"
                        data-allowed-media='<?= getMediaRestriction('whatsapp_image') ?>' />
                </div>
            </fieldset>
                <!-- form fields -->
                <x-lw.input-field type="text" id="lwAddressField" data-form-group-class="" :label="__tr('Address')" value="<%- __tData.businessProfile?.address %>" name="address" />
                <x-lw.input-field type="text" id="lwDescriptionField" data-form-group-class="" :label="__tr('Description')" value="<%- __tData.businessProfile?.description %>" name="description" />
                    <x-lw.input-field type="selectize" data-lw-plugin="lwSelectize" id="lwVerticalField"
                    data-form-group-class="" data-selected="<%- __tData.businessProfile?.vertical %>" :label="__tr('Industry type')" name="vertical">
                    <x-slot name="selectOptions">
                        <option value="UNDEFINED">{{ __tr('Please Select') }}</option>
                        <option value="OTHER">{{ __tr('OTHER') }}</option>
                        <option value="AUTO">{{ __tr('AUTO') }}</option>
                        <option value="BEAUTY">{{ __tr('BEAUTY') }}</option>
                        <option value="APPAREL">{{ __tr('APPAREL') }}</option>
                        <option value="EDU">{{ __tr('EDUCATION') }}</option>
                        <option value="ENTERTAIN">{{ __tr('ENTERTAIN') }}</option>
                        <option value="EVENT_PLAN">{{ __tr('EVENT PLANNER') }}</option>
                        <option value="FINANCE">{{ __tr('FINANCE') }}</option>
                        <option value="GROCERY">{{ __tr('GROCERY') }}</option>
                        <option value="GOVT">{{ __tr('GOVT') }}</option>
                        <option value="HOTEL">{{ __tr('HOTEL') }}</option>
                        <option value="HEALTH">{{ __tr('HEALTH') }}</option>
                        <option value="NONPROFIT">{{ __tr('NON PROFIT') }}</option>
                        <option value="PROF_SERVICES">{{ __tr('PROFESSIONAL SERVICES') }}</option>
                        <option value="RETAIL">{{ __tr('RETAIL') }}</option>
                        <option value="TRAVEL">{{ __tr('TRAVEL') }}</option>
                        <option value="RESTAURANT">{{ __tr('RESTAURANT') }}</option>
                        <option value="NOT_A_BIZ">{{ __tr('Not a Business') }}</option>
                    </x-slot>
                </x-lw.input-field>
                <x-lw.input-field type="text" id="lwAboutField" data-form-group-class="" :label="__tr('About')" value="<%- __tData.businessProfile?.about %>" name="about" />
                <x-lw.input-field type="email" id="lwEmailField" data-form-group-class="" :label="__tr('Email')" value="<%- __tData.businessProfile?.email %>" name="email" />
                <x-lw.input-field type="url" id="lwUrl1Field" data-form-group-class="" :label="__tr('Website 1')" value="<%- _.get(__tData.businessProfile, 'websites.0') %>" name="websites[0]" />
                <x-lw.input-field type="url" id="lwUrl2Field" data-form-group-class="" :label="__tr('Website 2')" value="<%- _.get(__tData.businessProfile, 'websites.1') %>" name="websites[1]" />
    </script>
        <!-- form footer -->
        <div class="modal-footer">
            <!-- Submit Button -->
            <button type="submit" class="btn btn-primary">{{ __('Update') }}</button>
            <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __tr('Close') }}</button>
        </div>
    </x-lw.form>
    <!--/  Edit Contact Form -->
</x-lw.modal>