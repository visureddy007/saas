@php
/**
* Component : Contact
* Controller : ContactController
* File : contact.list.blade.php
* ----------------------------------------------------------------------------- */
$currentGroup = $groupUid ? $vendorContactGroups->where('_uid', $groupUid)->first() : null;
@endphp
@extends('layouts.app', ['title' => __tr('Contacts')])
@section('content')
@include('users.partials.header', [
'title' => $groupUid ? __tr('__groupName__ group contacts', [
'__groupName__' => $currentGroup->title
]) : __tr('Contacts'),
// 'description' => $groupUid ? $currentGroup->description : '',
'class' => 'col-lg-7'
])
@php
$groupDescription = $groupUid ? $currentGroup->description : '';
@endphp
<div class="container-fluid mt-lg--6">
    <div class="row">
        <!-- button -->
        <div class="col-xl-12 mb-3">
            <div class="float-right">
                @if ($groupUid)
                <a class="lw-btn btn btn-secondary" href="{{ route('vendor.contact.group.read.list_view') }}">{{
                    __tr('Back to Contact Groups') }}</a>
                @endif
                <button type="button" class="lw-btn btn btn-primary" data-toggle="modal" data-target="#lwAddNewContact">
                    {{ __tr('Create New Contact') }}</button>
                <button type="button" class="lw-btn btn btn-dark" data-toggle="modal" data-target="#lwExportDialog"> {{
                    __tr('Export Contacts') }}</button>
                <button type="button" class="lw-btn btn btn-dark" data-toggle="modal"
                    data-target="#lwImportContactDialog"> {{ __tr('Import Contacts') }}</button>
            </div>
            
        </div>
        <div class="col-xl-12 mb-3">
        <div class="card" style="width:400px">
            <div class="card-body">
                <h4 class="card-title">No of unique :{{$totalUniqueCount}}</h4>
                <h4 class="card-title">No of duplicate : {{$totalDuplicateCount}} </h4>
                <a href="#" class="btn btn-primary">Verify</a>
            </div>
        </div>
        </div>
      

        <!--/ button -->
        {{-- import contacts --}}
        <x-lw.modal id="lwImportContactDialog" :header="__tr('Import Contacts')" :hasForm="true"
            data-pre-callback="appFuncs.clearContainer">
            <x-lw.form id="lwImportContactDialogForm" :action="route('vendor.contact.write.import')"
                :data-callback-params="['modalId' => '#lwImportContactDialog', 'datatableId' => '#lwContactList']"
                data-callback="appFuncs.modelSuccessCallback">
                <div class="lw-form-modal-body">
                    <div class="alert alert-danger">
                        {{ __tr('Please use Template from Export contacts') }}
                    </div>
                    <p>{{ __tr('You can import excel file with new contacts or existing updated.') }}</p>
                    <div class="alert alert-light">
                        <h3>{{ __tr('Conventions') }}</h3>
                        <h4>{{ __tr('Mobile Number') }}</h4>
                        {{ __tr('Mobile number treated as unique entity, it should be with country code without prefixing
                        0 or +, if the Mobile number is found in the records other information for the same will get
                        updated with data from the excel.') }}
                        <div class="mt-3">
                            <h4>{{ __tr('Group') }}</h4>
                            {{ __tr('Use comma separated group title, make sure groups are already exists into the
                            system. Groups won\'t be deleted, only new groups will be assigned.') }}
                        </div>
                    </div>
                    <div class="form-group ">
                        <input id="lwImportDocumentFilepond" type="file" data-allow-revert="true"
                            data-label-idle="{{ __tr('Select XLSX File') }}" class="lw-file-uploader"
                            data-instant-upload="true"
                            data-action="<?= route('media.upload_temp_media', 'vendor_contact_import') ?>"
                            data-file-input-element="#lwImportDocument" data-allowed-media='{{ getMediaRestriction('
                            vendor_contact_import') }}'>
                        <input id="lwImportDocument" type="hidden" value="" name="document_name" />
                    </div>
                </div>
                <!-- form footer -->
                <div class="modal-footer">
                    <!-- Submit Button -->
                    <button type="submit" class="btn btn-primary">{{ __('Process Import') }}</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __tr('Close') }}</button>
                </div>
            </x-lw.form>
        </x-lw.modal>
        {{-- /import contacts --}}
        {{-- export contacts --}}
        <x-lw.modal id="lwExportDialog" :header="__tr('Export Contacts')" :hasForm="true"
            data-pre-callback="appFuncs.clearContainer">
            <div class="lw-form-modal-body p-3">
                <h5>{{ __tr('Export with Data') }}</h5>
                <p>{{ __tr('You can export all contacts excel file and import it back with updated data.') }}</p>
                <a href="{{ route('vendor.contact.write.export', [
                    'exportType' => 'data'
                ]) }}" data-method="post" class="btn btn-primary">{{ __('Export Excel File with Data') }}</a>
                <hr>
                <h5>{{ __tr('Blank Excel Template') }}</h5>
                <p>{{ __tr('You can export blank excel file and fill with data according to column header and import it
                    for updates.') }}</p>
                <a href="{{ route('vendor.contact.write.export') }}" data-method="post" class="btn btn-primary">{{
                    __('Export Blank Template') }}</a>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __tr('Close') }}</button>
            </div>
        </x-lw.modal>
        {{-- /export contacts --}}
        <!-- Add New Contact Modal -->
        <x-lw.modal id="lwAddNewContact" :header="__tr('Add New Contact')" :hasForm="true"
            data-pre-callback="appFuncs.clearContainer">
            <!--  Add New Contact Form -->
            <x-lw.form id="lwAddNewContactForm" :action="route('vendor.contact.write.create')"
                :data-callback-params="['modalId' => '#lwAddNewContact', 'datatableId' => '#lwContactList']"
                data-callback="appFuncs.modelSuccessCallback">
                <!-- form body -->
                <div class="lw-form-modal-body">
                    <!-- form fields form fields -->
                    <!-- First_Name -->
                    <x-lw.input-field type="text" id="lwFirstNameField" data-form-group-class=""
                        :label="__tr('First Name')" name="first_name"  />
                    <!-- /First_Name -->
                    <!-- Last_Name -->
                    <x-lw.input-field type="text" id="lwLastNameField" data-form-group-class=""
                        :label="__tr('Last Name')" name="last_name"  />
                    <!-- /Last_Name -->
                    <!-- Country -->
                    <x-lw.input-field type="selectize" data-lw-plugin="lwSelectize" id="lwCountryField"
                        data-form-group-class="" data-selected=" " :label="__tr('Country')" name="country"
                        >
                        <x-slot name="selectOptions">
                            <option value="">{{ __tr('Country') }}</option>
                            @foreach(getCountryPhoneCodes() as $getCountryCode)
                            <option value="{{ $getCountryCode['_id'] }}">{{ $getCountryCode['name'] }}</option>
                            @endforeach
                        </x-slot>
                    </x-lw.input-field>
                    <!-- /Country -->
                    <!-- Phone_Number -->
                    <x-lw.input-field type="number" id="lwPhoneNumberField" data-form-group-class=""
                        :label="__tr('Mobile Number')" name="phone_number" minlength="9"
                        :helpText="__tr('Number should be with country code without 0 or +')" required="true" />
                    <!-- /Phone_Number -->
                    <!-- Language Code -->
                    <x-lw.input-field type="text" id="lwLanguageCodeField" data-form-group-class=""
                        :label="__tr('Language Code')" name="language_code" />
                    <!-- /Language Code -->
                    <!-- Email -->
                    <x-lw.input-field type="email" id="lwEmailField" data-form-group-class="" :label="__tr('Email')"
                        name="email" />
                    <!-- /Email -->
                    <x-lw.input-field type="selectize" data-lw-plugin="lwSelectize" id="lwSelectGroupsField"
                        data-form-group-class="" data-selected=" " :label="__tr('Groups')" name="contact_groups[]"
                        multiple>
                        <x-slot name="selectOptions">
                            <option value="">{{ __tr('Select Groups') }}</option>
                            @foreach($vendorContactGroups as $vendorContactGroup)
                            <option value="{{ $vendorContactGroup['_id'] }}">{{ $vendorContactGroup['title'] }} {{ $vendorContactGroup['status'] == 5  ? __tr('(Archived)') : '' }}</option>
                            @endforeach
                        </x-slot>
                    </x-lw.input-field>
                    <div class="my-3">
                        <x-lw.checkbox id="lwPromotionalOpt" name="whatsapp_opt_out" data-color="#ff0000" data-size="small" value="1" data-lw-plugin="lwSwitchery" :label="__tr('Opt out Marketing Messages')" />
                    </div>
                    <div class="my-3">
                        @if(getVendorSettings('enable_flowise_ai_bot') and getVendorSettings('flowise_url'))
                        <x-lw.checkbox id="lwAiBotEnable" :checked="getVendorSettings('default_enable_flowise_ai_bot_for_users')" name="enable_ai_bot" value="1"
                            data-lw-plugin="lwSwitchery" :label="__tr('Enable AI Bot')" />
                        @endif
                    </div>
                    <fieldset>
                        <legend>{{ __tr('Other Information') }}</legend>
                        @foreach ($vendorContactCustomFields as $vendorContactCustomField)
                        <x-lw.input-field type="{{ $vendorContactCustomField->input_type }}"
                            id="lwCustomField{{ $vendorContactCustomField->_id }}" data-form-group-class=""
                            :label="$vendorContactCustomField->input_name"
                            name="custom_input_fields[{{ $vendorContactCustomField->_uid }}]" />
                        @endforeach
                    </fieldset>
                </div>
                <!-- form footer -->
                <div class="modal-footer">
                    <!-- Submit Button -->
                    <button type="submit" class="btn btn-primary">{{ __('Submit') }}</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __tr('Close') }}</button>
                </div>
            </x-lw.form>
            <!--/  Add New Contact Form -->
        </x-lw.modal>
        <!--/ Add New Contact Modal -->

        <!-- Details Contact Modal -->
        <x-lw.modal id="lwDetailsContact" :header="__tr('Contact Details')">
            <!--  Details Contact Form -->
            <!-- Details body -->
            <div id="lwDetailsContactBody" class="lw-form-modal-body"></div>
            <script type="text/template" id="lwDetailsContactBody-template">
                <!-- form fields -->
                <div>
                    <label class="small">{{ __tr('First Name') }}:</label>
                    <div class="lw-details-item">
                        <%- __tData.first_name %>
                    </div>
                </div>

                <div>
                    <label class="small">{{ __tr('Last Name') }}:</label>
                    <div class="lw-details-item">
                        <%- __tData.last_name %>
                    </div>
                </div>

                <div>
                    <label class="small">{{ __tr('Country') }}:</label>
                    <div class="lw-details-item">
                        <%- __tData.country?.name %>
                    </div>
                </div>

                <div>
                    <label class="small">{{ __tr('Mobile Number') }}:</label>
                    <div class="lw-details-item">
                        <%- __tData.wa_id %>
                    </div>
                </div>
                <div>
                    <label class="small">{{ __tr('Language Code') }}:</label>
                    <div class="lw-details-item">
                        <%- __tData.language_code %>
                    </div>
                </div>

                <div>
                    <label class="small">{{ __tr('Email') }}:</label>
                    <div class="lw-details-item">
                        <%- __tData.email %>
                    </div>
                </div>

                <fieldset>
                    <legend>{{ __tr('Groups') }}</legend>
                    <% _.forEach(__tData.groups, function(value, key) { %>
                        <span class="badge badge-light">
                            <%- value.title %>
                        </span>
                        <% } ); %>
                </fieldset>
                <fieldset>
                    <legend>{{ __tr('Other Information') }}</legend>
                    @foreach ($vendorContactCustomFields as $vendorContactCustomField)
                    <div class="mb-2">
                        <label class="small">{{ $vendorContactCustomField->input_name }}:</label>
                        <div class="lw-details-item">
                            <%- _.get(_.find(__tData.custom_field_values, {'contact_custom_fields__id' : {{
                                $vendorContactCustomField->_id }} }), 'field_value') %>
                        </div>
                    </div>
                    @endforeach
                </fieldset>
            </script>
            <!--/  Details Contact Form -->
        </x-lw.modal>
        <!--/ Edit Contact Modal -->
         <!--Group description -->
        <div class="ml-3">
            <p class="card-text">{{$groupDescription
            }}</p>
        </div>
         <!--/ Group description -->
        <!-- Edit Contact Modal -->
        @include('contact.contact-edit-modal-partial')
        <!--/ Edit Contact Modal -->
        <div class="col-xl-12" x-cloak x-data="{isSelectedAll:false,selectedContacts: [],selectedGroupsForSelectedContacts:[],
            toggle(id) {
                if (this.selectedContacts.includes(id)) {
                    const index = this.selectedContacts.indexOf(id);
                    this.selectedContacts.splice(index, 1);
                    this.isSelectedAll = false;
                } else {
                    this.selectedContacts.push(id);
                    if($('.dataTables_wrapper table>tbody input[type=checkbox].lw-checkboxes').length == this.selectedContacts.length) {
                        this.isSelectedAll = true;
                    }
                };
            },toggleAll() {
                if(!this.isSelectedAll) {
                    $('.dataTables_wrapper table>tbody input[type=checkbox].lw-checkboxes').not(':checked').trigger('click');
                    this.isSelectedAll = true;
                } else {
                    $('.dataTables_wrapper table>tbody input[type=checkbox].lw-checkboxes:checked').trigger('click');
                    this.isSelectedAll = false;
                }
            },deleteSelectedContacts() {
                var that = this;
                showConfirmation('{{ __tr('Are you sure you want to delete all selected contacts?') }}', function() {
                    __DataRequest.post('{{ route('vendor.contacts.selected.write.delete') }}', {
                        'selected_contacts' : that.selectedContacts
                    });
                }, {
                    confirmButtonText: '{{ __tr('Yes') }}',
                    cancelButtonText: '{{ __tr('No') }}',
                    type: 'error'
                });
            }, assignGroupsToSelectedContacts(){
                var that = this;
                __DataRequest.post('{{ route('vendor.contacts.selected.write.assign_groups') }}', {
                    'selected_contacts' : that.selectedContacts,
                    'selected_groups' : that.selectedGroupsForSelectedContacts
                });
                $('#lwAssignGroups').modal('hide');
                $('.dataTables_wrapper table>tbody input[type=checkbox].lw-checkboxes:checked').trigger('click');
                this.isSelectedAll = false;
            }}" x-init="$('#lwContactList').on( 'draw.dt', function () {
                $('.dataTables_wrapper table>tbody input[type=checkbox].lw-checkboxes:checked').trigger('click');
                isSelectedAll = false;
            } );">
            <button x-show="!isSelectedAll" class="btn btn-dark btn-sm my-2" @click="toggleAll">{{ __tr('Select All')
                }}</button>
            <button x-show="isSelectedAll" class="btn btn-dark btn-sm my-2" @click="toggleAll">{{ __tr('Unselect All')
                }}</button>
            <div class="btn-group">
                <button :class="!selectedContacts.length ? 'disabled' : ''"
                    class="btn btn-danger mt-1 btn-sm dropdown-toggle" type="button" data-toggle="dropdown"
                    aria-expanded="false">
                    {{ __tr('Bulk Actions') }}
                </button>
                <div class="dropdown-menu">
                    <a class="dropdown-item" @click.prevent="deleteSelectedContacts" href="#">{{ __tr('Delete Selected
                        Contacts') }}</a>
                    <a class="dropdown-item" data-toggle="modal" data-target="#lwAssignGroups" href="#">{{ __tr('Assign
                        Group to Selected Contacts') }}</a>
                </div>
            </div>
            <!-- Assign Groups to the selected contacts -->
            <x-lw.modal id="lwAssignGroups" :header="__tr('Assign Groups to Selected Contacts')" :hasForm="true"
                data-pre-callback="appFuncs.clearContainer">
                <!-- form body -->
                <div class="lw-form-modal-body p-4">
                    <!-- form fields form fields -->
                    <x-lw.input-field x-model="selectedGroupsForSelectedContacts" type="selectize"
                        data-lw-plugin="lwSelectize" id="lwSelectGroupsField" data-form-group-class="" data-selected=" "
                        :label="__tr('Groups')" name="contact_groups[]" multiple>
                        <x-slot name="selectOptions">
                            <option value="">{{ __tr('Select Groups') }}</option>
                            @foreach($vendorContactGroups as $vendorContactGroup)
                            <option value="{{ $vendorContactGroup['_id'] }}">{{ $vendorContactGroup['title'] }} {{ $vendorContactGroup['status'] == 5  ? __tr('(Archived)') : '' }}</option>
                            @endforeach
                        </x-slot>
                    </x-lw.input-field>
                </div>
                <!-- form footer -->
                <div class="modal-footer">
                    <!-- Submit Button -->
                    <button type="button" @click="assignGroupsToSelectedContacts" class="btn btn-primary">{{
                        __('Submit') }}</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __tr('Close') }}</button>
                </div>
                <!--/  Add New Contact Form -->
            </x-lw.modal>
            <!--/ Assign Groups to the selected contacts -->
            <x-lw.datatable data-page-length="100" id="lwContactList" :url="route('vendor.contact.read.list', [
                'groupUid' => $groupUid
            ])">
                <th style="width: 1px;padding:0;" data-name="none"></th>
                <th data-name="none" data-template="#lwSelectMultipleContactsCheckbox">{{ __tr('Select') }}</th>
                <!-- <th data-orderable="true" data-name="first_name">{{ __tr('First Name') }}</th>
                <th data-orderable="true" data-name="last_name">{{ __tr('Last Name') }}</th>
                <th data-name="phone_number">{{ __tr('Mobile Number') }}</th>
                <th data-name="language_code">{{ __tr('Language Code') }}</th> -->
                <th data-orderable="true" data-name="created_at">{{ __tr('Created on') }}</th>
                <!-- <th data-name="country_name">{{ __tr('Country') }}</th> -->
                <th data-orderable="true" data-name="email">{{ __tr('Email') }}</th>
                <!-- <th data-orderable="true" data-name="whatsapp_opt_out">{{ __tr('Marketing') }}</th> -->
                <th data-template="#contactActionColumnTemplate" name="null">{{ __tr('Action') }}</th>
            </x-lw.datatable>
        </div>
        <!-- action template -->
        <script type="text/template" id="lwSelectMultipleContactsCheckbox">
            <input @click="toggle('<%- __tData._uid %>')" type="checkbox" name="selected_contacts[]" class="lw-checkboxes custom-checkbox" value="<%- __tData._uid %>">
        </script>
        <script type="text/template" id="contactActionColumnTemplate">
            <a data-pre-callback="appFuncs.clearContainer" title="{{  __tr('Details') }}" class="lw-btn btn btn-sm btn-default lw-ajax-link-action" data-response-template="#lwDetailsContactBody" href="<%= __Utils.apiURL("{{ route('vendor.contact.read.update.data', [ 'contactIdOrUid']) }}", {'contactIdOrUid': __tData._uid}) %>"  data-toggle="modal" data-target="#lwDetailsContact"><i class="fa fa-info-circle"></i> {{  __tr('Details') }}</a>
            <a data-pre-callback="appFuncs.clearContainer" title="{{  __tr('Edit') }}" class="lw-btn btn btn-sm btn-default lw-ajax-link-action" data-response-template="#lwEditContactBody" href="<%= __Utils.apiURL("{{ route('vendor.contact.read.update.data', [ 'contactIdOrUid']) }}", {'contactIdOrUid': __tData._uid}) %>"  data-toggle="modal" data-target="#lwEditContact"><i class="fa fa-edit"></i> {{  __tr('Edit') }}</a>
<!--  Delete Action -->
@if(hasVendorAccess('messaging'))
<a data-pre-callback="appFuncs.clearContainer" title="{{  __tr('Send Template Message') }}" class="lw-btn btn btn-sm btn-primary" href="<%= __Utils.apiURL("{{ route('vendor.template_message.contact.view', ['contactUid']) }}",{'contactUid': __tData._uid}) %>"><i class="fab fa-whatsapp"></i> {{  __tr('Send Template Message') }}</a> <a data-pre-callback="appFuncs.clearContainer" title="{{  __tr('Chat') }}" class="lw-btn btn btn-sm btn-primary" href="<%= __Utils.apiURL("{{ route('vendor.chat_message.contact.view', ['contactUid']) }}",{'contactUid': __tData._uid}) %>"><i class="fab fa-whatsapp"></i> {{  __tr('Chat') }}</a>
@endif
 <a data-method="post" href="<%= __Utils.apiURL("{{ route('vendor.contact.write.delete', [ 'contactIdOrUid']) }}", {'contactIdOrUid': __tData._uid}) %>" class="btn btn-danger btn-sm lw-ajax-link-action-via-confirm" data-confirm="#lwDeleteContact-template" title="{{ __tr('Delete') }}" data-callback-params="{{ json_encode(['datatableId' => '#lwContactList']) }}" data-callback="appFuncs.modelSuccessCallback"><i class="fa fa-trash"></i> {{  __tr('Delete') }}</a>
 <!--  Remove Contact Action -->
 @if($currentGroup!=null)
  <a data-method="post" href="<%= __Utils.apiURL("{{ route('vendor.contact.write.remove',['contactIdOrUid', 'groupUid' => $groupUid]) }}",{ 'contactIdOrUid': __tData._uid }) %>" class="btn btn-warning btn-sm lw-ajax-link-action-via-confirm" data-confirm="#lwRemoveContact-template" title="{{ __tr('Remove contact from group') }}" data-callback-params="{{ json_encode(['datatableId' => '#lwContactList']) }}" data-callback="appFuncs.modelSuccessCallback"><i class="fa fa-user-times"></i> {{  __tr('Remove') }}</a> 
 @endif
 
 <!--  Remove Contact Action  -->
    </script>
        <!-- /action template -->
        <!-- Contact delete template -->
        <script type="text/template" id="lwDeleteContact-template">
            <h2>{{ __tr('Are You Sure!') }}</h2>
            <p>{{ __tr('You want to delete this Contact permanently?') }}</p>
    </script>
        <!-- /Contact delete template -->
         <!-- Contact remove template -->
         <script type="text/template" id="lwRemoveContact-template">
            <h2>{{ __tr('Are You Sure!') }}</h2>
            <p>{{ __tr('You want to remove this Contact from this group?') }}</p>
    </script>
        <!-- /Contact remove template -->
    </div>
</div>
@push('appScripts')
<script>
(function($) {
    'use strict';
    window.onUpdateContactDetails = function(responseData, callbackParams) {
        appFuncs.modelSuccessCallback(responseData, callbackParams);
    }
})(jQuery);
</script>
@endpush
@endsection()