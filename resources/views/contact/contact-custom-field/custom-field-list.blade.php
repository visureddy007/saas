@php
/**
* Component : Contact
* Controller : ContactCustomFieldController
* File : Custom Field.list.blade.php
* ----------------------------------------------------------------------------- */
@endphp
@extends('layouts.app', ['title' => __tr('Contact Custom Fields')])
@section('content')
@include('users.partials.header', [
'title' => __tr('Contact Custom Fields'),
'description' => '',
'class' => 'col-lg-7'
])
<div class="container-fluid mt-lg--6">
    <div class="row">
        <!-- button -->
        <div class="col-xl-12 mb-3">
            <button type="button" class="lw-btn btn btn-primary float-right" data-toggle="modal"
                data-target="#lwAddNewCustomField"> {{ __tr('Add New Custom Field') }}</button>
        </div>
        <!--/ button -->
        <!-- Add New Custom Field Modal -->
        <x-lw.modal id="lwAddNewCustomField" :header="__tr('Add New Custom Field')" :hasForm="true">
            <!--  Add New Custom Field Form -->
            <x-lw.form id="lwAddNewCustomFieldForm"
                :action="route('vendor.contact.custom_field.write.create')"
                :data-callback-params="['modalId' => '#lwAddNewCustomField', 'datatableId' => '#lwCustomFieldList']"
                data-callback="appFuncs.modelSuccessCallback">
                <!-- form body -->
                <div class="lw-form-modal-body">
                    <!-- form fields form fields -->
                    <!-- Input_Name -->
                    <x-lw.input-field type="text" id="lwInputNameField" data-form-group-class=""
                        :label="__tr('Input Name')" name="input_name" required="true" />
                    <!-- /Input_Name -->
                    <!-- Input_Type -->
                    <x-lw.input-field type="selectize" id="lwInputTypeField" data-form-group-class="" data-selected=" "
                        :label="__tr('Input Type')" name="input_type" required="true">
                        <x-slot name="selectOptions">
                            @foreach (configItem('contact_custom_input_types') as $inputTypeKey => $inputTypeValue)
                            <option value="{{ $inputTypeKey }}">{{ $inputTypeValue }}</option>
                            @endforeach
                        </x-slot>
                    </x-lw.input-field>
                    <!-- /Input_Type -->
                </div>
                <!-- form footer -->
                <div class="modal-footer">
                    <!-- Submit Button -->
                    <button type="submit" class="btn btn-primary">{{ __('Submit') }}</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __tr('Close') }}</button>
                </div>
            </x-lw.form>
            <!--/  Add New Custom Field Form -->
        </x-lw.modal>
        <!--/ Add New Custom Field Modal -->

        <!-- Edit Custom Field Modal -->
        <x-lw.modal id="lwEditCustomField" :header="__tr('Edit Custom Field')" :hasForm="true">
            <!--  Edit Custom Field Form -->
            <x-lw.form id="lwEditCustomFieldForm"
                :action="route('vendor.contact.custom_field.write.update')"
                :data-callback-params="['modalId' => '#lwEditCustomField', 'datatableId' => '#lwCustomFieldList']"
                data-callback="appFuncs.modelSuccessCallback">
                <!-- form body -->
                <div id="lwEditCustomFieldBody" class="lw-form-modal-body"></div>
                <script type="text/template" id="lwEditCustomFieldBody-template">

                    <input type="hidden" name="contactCustomFieldIdOrUid" value="<%- __tData._uid %>" />
                        <!-- form fields -->
                        <!-- Input_Name -->
           <x-lw.input-field type="text" id="lwInputNameEditField" data-form-group-class="" :label="__tr('Input Name')" value="<%- __tData.input_name %>" name="input_name"  required="true"                 />
                <!-- /Input_Name -->
                <!-- Input_Type -->
                 <x-lw.input-field type="selectize" id="lwInputTypeEditField" data-form-group-class="" data-selected="<%- __tData.input_type %>" :label="__tr('Input Type')" name="input_type"  required="true"                >
                    <x-slot name="selectOptions">
                    <option value="">{{ __tr('Input Type') }}</option>
                    @foreach (configItem('contact_custom_input_types') as $inputTypeKey => $inputTypeValue)
                        <option value="{{ $inputTypeKey }}">{{ $inputTypeValue }}</option>
                    @endforeach
                </x-slot>
            </x-lw.input-field>
                <!-- /Input_Type -->
                     </script>
                <!-- form footer -->
                <div class="modal-footer">
                    <!-- Submit Button -->
                    <button type="submit" class="btn btn-primary">{{ __('Submit') }}</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __tr('Close') }}</button>
                </div>
            </x-lw.form>
            <!--/  Edit Custom Field Form -->
        </x-lw.modal>
        <!--/ Edit Custom Field Modal -->
        <div class="col-xl-12">
            <x-lw.datatable data-page-length="100" id="lwCustomFieldList"
                :url="route('vendor.contact.custom_field.read.list')">
                <th data-orderable="true" data-name="input_name">{{ __tr('Input Name') }}</th>
                <th data-orderable="true" data-name="input_type">{{ __tr('Input Type') }}</th>
                <th data-template="#customFieldActionColumnTemplate" name="null">{{ __tr('Action') }}</th>
            </x-lw.datatable>


        </div>

        <!-- action template -->
        <script type="text/template" id="customFieldActionColumnTemplate">
            <a data-pre-callback="appFuncs.clearContainer" title="{{  __tr('Edit') }}" class="lw-btn btn btn-sm btn-default lw-ajax-link-action" data-response-template="#lwEditCustomFieldBody" href="<%= __Utils.apiURL("{{ route('vendor.contact.custom_field.read.update.data', [ 'contactCustomFieldIdOrUid']) }}", {'contactCustomFieldIdOrUid': __tData._uid}) %>"  data-toggle="modal" data-target="#lwEditCustomField"><i class="fa fa-edit"></i> {{  __tr('Edit') }}</a>

<!--  Delete Action -->
<a data-method="post" href="<%= __Utils.apiURL("{{ route('vendor.contact.custom_field.write.delete', [ 'contactCustomFieldIdOrUid']) }}", {'contactCustomFieldIdOrUid': __tData._uid}) %>" class="btn btn-danger btn-sm lw-ajax-link-action-via-confirm" data-confirm="#lwDeleteCustomField-template" title="{{ __tr('Delete') }}" data-callback-params="{{ json_encode(['datatableId' => '#lwCustomFieldList']) }}" data-callback="appFuncs.modelSuccessCallback"><i class="fa fa-trash"></i> {{  __tr('Delete') }}</a>
    </script>
        <!-- /action template -->

        <!-- Custom Field delete template -->
        <script type="text/template" id="lwDeleteCustomField-template">
            <h2>{{ __tr('Are You Sure!') }}</h2>
            <p>{{ __tr('You want to delete this Custom Field?') }}</p>
    </script>
        <!-- /CustomField delete template -->
    </div>
</div>
@endsection()