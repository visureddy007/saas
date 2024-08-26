<div class="lw-section-heading-block">
    <!-- main heading -->
    <h3 class="lw-section-heading">
        <span>{{ __tr('Campaign Edit') }}</span>
    </h3>
    <!-- /main heading -->
</div>
    <!--  Edit Campaign Form -->
    <x-lw.form id="lwEditCampaignForm" :action="route('vendor.campaign.write.update')">
        <!-- form body -->
        <div id="lwEditCampaignBody" class="lw-form-modal-body"></div>
            <input type="hidden" name="campaignIdOrUid" value="<%- __tData._uid %>" />
            <!-- form fields -->
            <!-- Title -->
<x-lw.input-field type="text" id="lwTitleEditField" data-form-group-class="" :label="__tr('Title')" value="<%- __tData.title %>" name="title"  required="true"                 />
    <!-- /Title -->
    <!-- Whatsapp_Template -->
            <x-lw.input-field type="selectize" data-lw-plugin="lwSelectize" id="lwWhatsappTemplateEditField" data-form-group-class="" data-selected=" " :label="__tr('Whatsapp Template')" name="whatsapp_template"  required="true"                >
        <x-slot name="selectOptions">
            <option value=" ">{{ __tr('Whatsapp Template') }}</option>
        </x-slot>
    </x-lw.input-field>
    <!-- /Whatsapp_Template -->
    <!-- Schedule_At -->

<x-lw.input-field type="text" data-lw-plugin="lwTimePicker" class="lw-readonly-control" readonly="readonly" id="lwScheduleAtEditField" data-form-group-class="" :label="__tr('Schedule At')" value="<%- __tData.schedule_at %>" name="schedule_at"           />
    <!-- /Schedule_At -->
        <!-- form footer -->
        <div class="modal-footer">
            <!-- Submit Button -->
            <button type="submit" class="btn btn-primary">{{ __('Submit') }}</button>
            <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __tr('Close') }}</button>
        </div>
    </x-lw.form>
    <!--/  Edit Campaign Form -->