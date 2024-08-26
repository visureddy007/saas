<div class="lw-section-heading-block">
    <!-- main heading -->
    <h3 class="lw-section-heading">
        <span>{{ __tr('Create Campaign') }}</span>
    </h3>
    <!-- /main heading -->
</div>
<!--  Add New Campaign Form -->
<x-lw.form id="lwAddNewCampaignForm" :action="route('vendor.campaign.write.create')" >
    <!-- form body -->
    <div class="lw-form-modal-body">
        <!-- form fields form fields -->
    <!-- Title -->
   <x-lw.input-field type="text" id="lwTitleField" data-form-group-class="" :label="__tr('Title')"  name="title"  required="true"                 />
        <!-- /Title -->
        <!-- Whatsapp_Template -->
        <x-lw.input-field type="selectize" data-lw-plugin="lwSelectize" id="lwWhatsappTemplateField" data-form-group-class="" data-selected=" " :label="__tr('Whatsapp Template')" name="whatsapp_template"  required="true"                >
            <x-slot name="selectOptions">
                <option value=" ">{{ __tr('Whatsapp Template') }}</option>
            </x-slot>
        </x-lw.input-field>
        <!-- /Whatsapp_Template -->
        <!-- Schedule_At -->
 
<x-lw.input-field type="text" data-lw-plugin="lwTimePicker" class="lw-readonly-control" readonly="readonly" id="lwScheduleAtField" data-form-group-class="" :label="__tr('Schedule At')"  name="schedule_at"           />
        <!-- /Schedule_At -->
     </div>
    <!-- form footer -->
    <div class="modal-footer">
        <!-- Submit Button -->
        <button type="submit" class="btn btn-primary">{{ __('Submit') }}</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __tr('Close') }}</button>
    </div>
</x-lw.form>
<!--/  Add New Campaign Form -->