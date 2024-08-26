@php
/**
* Component : Contact
* Controller : ContactGroupController
* File : group.list.blade.php
----------------------------------------------------------------------------- */
@endphp
@extends('layouts.app', ['title' => __tr('Contact Groups')])
@section('content')
@include('users.partials.header', [
'title' => __tr('Contact Groups'),
'description' => '',
'class' => 'col-lg-7'
])
<?php $status = request()->status ?? 'active'; ?>

<div class="container-fluid mt-lg--6">
    <div class="row">
        <!-- button -->
        <div class="col-xl-12 mb-3">
            <button type="button" class="lw-btn btn btn-primary float-right" data-toggle="modal"
                data-target="#lwAddNewGroup"> {{ __tr('Add New Group') }}</button>
        </div>
        <!--/ button -->
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
        },deleteSelectedContactGroups() {
            var that = this;
            showConfirmation('{{ __tr('Are you sure you want to delete all selected groups?') }}', function() {
                __DataRequest.post('{{ route('vendor.contact.group.selected.write.delete') }}', {
                    'selected_groups' : that.selectedContacts
                });
            }, {
                confirmButtonText: '{{ __tr('Yes') }}',
                cancelButtonText: '{{ __tr('No') }}',
                type: 'error'
            });
        },
        archiveSelectedContactGroups() {
            var that = this;
            showConfirmation('{{ __tr('Are you sure you want to archive all selected groups?') }}', function() {
                __DataRequest.post('{{ route('vendor.contact.group.selected.write.archive') }}', {
                    'selected_groups' : that.selectedContacts
                });
            }, {
                confirmButtonText: '{{ __tr('Yes') }}',
                cancelButtonText: '{{ __tr('No') }}',
                type: 'warning'
            });
        },
        unarchiveSelectedContactGroups() {
            var that = this;
            showConfirmation('{{ __tr('Are you sure you want to unarchive all selected groups?') }}', function() {
                __DataRequest.post('{{ route('vendor.contact.group.selected.write.unarchive') }}', {
                    'selected_groups' : that.selectedContacts
                });
            }, {
                confirmButtonText: '{{ __tr('Yes') }}',
                cancelButtonText: '{{ __tr('No') }}',
                type: 'warning'
            });
        },
            }
        " x-init="$('#lwGroupList').on( 'draw.dt', function () {
            $('.dataTables_wrapper table>tbody input[type=checkbox].lw-checkboxes:checked').trigger('click');
            isSelectedAll = false;
        } );">
            <ul class="nav nav-tabs mt-1 ml-1">
                <!-- Active tab -->
                <li class="nav-item">
                    <a class="nav-link <?= $status == 'active' ? 'active' : '' ?>" data-title="{{ __tr('Active ') }}"
                        href="<?= route('vendor.contact.group.read.list_view', ['status' => 'active']) ?>">
                        <?= __tr('Active') ?>
                    </a>
                </li>
                <!-- /Active tab -->

                <!-- Archive tab -->
                <li class="nav-item">
                    <a class="nav-link  <?= $status == 'archived' ? 'active' : '' ?>  "
                        data-title="{{ __tr('Archive') }}"
                        href="<?= route('vendor.contact.group.read.list_view', ['status' => 'archived']) ?>">
                        <?= __tr('Archive') ?>
                    </a>
                </li>
                <!-- /Archive tab -->
            </ul>
            <!-- Add New Group Modal -->
            <x-lw.modal id="lwAddNewGroup" :header="__tr('Add New Group')" :hasForm="true">
                <!--  Add New Group Form -->
                <x-lw.form id="lwAddNewGroupForm" :action="route('vendor.contact.group.write.create')"
                    :data-callback-params="['modalId' => '#lwAddNewGroup', 'datatableId' => '#lwGroupList']"
                    data-callback="appFuncs.modelSuccessCallback">
                    <!-- form body -->
                    <div class="lw-form-modal-body">
                        <!-- form fields form fields -->
                        <!-- Title -->
                        <x-lw.input-field type="text" id="lwTitleField" data-form-group-class="" :label="__tr('Title')"
                            name="title" required="true" />
                        <!-- /Title -->
                        <!-- Description -->
                        <div class="form-group">
                            <label for="lwDescriptionField">{{ __tr('Description') }}</label>
                            <textarea cols="10" rows="3" id="lwDescriptionField" class="lw-form-field form-control"
                                placeholder="{{ __tr('Description') }}" name="description"></textarea>
                        </div>
                        <!-- /Description -->
                    </div>
                    <!-- form footer -->
                    <div class="modal-footer">
                        <!-- Submit Button -->
                        <button type="submit" class="btn btn-primary">{{ __('Submit') }}</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __tr('Close')
                            }}</button>
                    </div>
                </x-lw.form>
                <!--/  Add New Group Form -->
            </x-lw.modal>
            <!--/ Add New Group Modal -->

            <!-- Edit Group Modal -->
            <x-lw.modal id="lwEditGroup" :header="__tr('Edit Group')" :hasForm="true">
                <!--  Edit Group Form -->
                <x-lw.form id="lwEditGroupForm" :action="route('vendor.contact.group.write.update')"
                    :data-callback-params="['modalId' => '#lwEditGroup', 'datatableId' => '#lwGroupList']"
                    data-callback="appFuncs.modelSuccessCallback">
                    <!-- form body -->
                    <div id="lwEditGroupBody" class="lw-form-modal-body"></div>
                    <script type="text/template" id="lwEditGroupBody-template">

                        <input type="hidden" name="contactGroupIdOrUid" value="<%- __tData._uid %>" />
                        <!-- form fields -->
                        <!-- Title -->
           <x-lw.input-field type="text" id="lwTitleEditField" data-form-group-class="" :label="__tr('Title')" value="<%- __tData.title %>" name="title"  required="true"                 />
                <!-- /Title -->
                <!-- Description -->
                <div class="form-group">
                <label for="lwDescriptionEditField">{{ __tr('Description') }}</label>
                <textarea cols="10" rows="3" id="lwDescriptionEditField" value="<%- __tData.description %>" class="lw-form-field form-control" placeholder="{{ __tr('Description') }}" name="description"          ><%- __tData.description %></textarea>
            </div>
                <!-- /Description -->
                     </script>
                    <!-- form footer -->
                    <div class="modal-footer">
                        <!-- Submit Button -->
                        <button type="submit" class="btn btn-primary">{{ __('Submit') }}</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __tr('Close')
                            }}</button>
                    </div>
                </x-lw.form>
                <!--/  Edit Group Form -->
            </x-lw.modal>
            <!--/ Edit Group Modal -->
            {{-- <div class="col-xl-12"> --}}
                <!--datatable -->
                <x-lw.datatable data-page-length="50"  id="lwGroupList" 
                    :url="route('vendor.contact.group.read.list', ['status' => $status])">
                    <div class="">
                        <button x-show="!isSelectedAll" class="btn btn-dark btn-sm mb-2" @click="toggleAll">{{
                            __tr('Select All')
                            }}</button>
                        <button x-show="isSelectedAll" class="btn btn-dark btn-sm mb-2" @click="toggleAll">{{
                            __tr('Unselect All')
                            }}</button>
                        <div class="btn-group mb-2">
                            <!-- bulk action btn -->
                            <button :class="!selectedContacts.length ? 'disabled' : ''"
                                class="btn btn-danger mt-1 btn-sm dropdown-toggle" type="button" data-toggle="dropdown"
                                aria-expanded="false">
                                {{ __tr('Bulk Actions') }}
                            </button>
                            <!-- /bulk action btn -->
                            <div class="dropdown-menu">
                                <!-- delete action btn -->
                                <a class="dropdown-item" @click.prevent="deleteSelectedContactGroups" href="#">{{
                                    __tr('Delete Selected
                                    Groups') }}</a>
                                <!-- /delete action btn -->
                                @if($status == 'active')
                                <!-- archive action btn -->
                                <a class="dropdown-item" @click.prevent="archiveSelectedContactGroups" href="#">{{
                                    __tr('Archive Selected
                                    Groups') }}</a>
                                <!-- /archive action btn -->
                                @else
                                <!-- unarchive action btn -->
                                <a class="dropdown-item" @click.prevent="unarchiveSelectedContactGroups" href="#">{{
                                    __tr('Unarchive Selected
                                    Groups') }}</a>
                                <!-- /unarchive action btn -->
                                @endif
                            </div>
                        </div>
                    </div>
                    <th style="width: 1px;padding:0;" data-name="none"></th>
                    <th  data-name="none" data-template="#lwSelectMultipleContactGroupsCheckbox">{{ __tr('Select') }}
                    </th>
                    <th data-orderable="true" data-name="title">{{ __tr('Title') }}</th>
                    <th data-name="description">{{ __tr('Description') }}</th>
                    <th data-template="#groupActionColumnTemplate" name="null">{{ __tr('Action') }}</th>
                </x-lw.datatable>


            {{-- </div> --}}
            <!--datatable -->
        </div>

        <!-- action template -->
        <script type="text/template" id="groupActionColumnTemplate">
            <a title="{{  __tr('Group Contacts') }}" class="lw-btn btn btn-sm btn-warning" href="<%= __Utils.apiURL("{{ route('vendor.contact.read.list_view', [ 'groupUid']) }}", {'groupUid': __tData._uid}) %>"><i class="fa fa-users"></i> {{  __tr('Group Contacts') }}</a>
            <a data-pre-callback="appFuncs.clearContainer" title="{{  __tr('Edit') }}" class="lw-btn btn btn-sm btn-default lw-ajax-link-action" data-response-template="#lwEditGroupBody" href="<%= __Utils.apiURL("{{ route('vendor.contact.group.read.update.data', [ 'contactGroupIdOrUid']) }}", {'contactGroupIdOrUid': __tData._uid}) %>"  data-toggle="modal" data-target="#lwEditGroup"><i class="fa fa-edit"></i> {{  __tr('Edit') }}</a>
            <!--  Delete Action -->
            <a data-method="post" href="<%= __Utils.apiURL("{{ route('vendor.contact.group.write.delete', [ 'contactGroupIdOrUid']) }}", {'contactGroupIdOrUid': __tData._uid}) %>" class="btn btn-danger btn-sm lw-ajax-link-action-via-confirm" data-confirm="#lwDeleteGroup-template" title="{{ __tr('Delete') }}" data-callback-params="{{ json_encode(['datatableId' => '#lwGroupList']) }}" data-callback="appFuncs.modelSuccessCallback"><i class="fa fa-trash"></i> {{  __tr('Delete') }}</a>
              <!--  Delete Action -->
                <!--  Archive Action -->
            <% if(__tData.status != 5) { %>
                <a data-method="post" href="<%= __Utils.apiURL("{{ route('vendor.contact.group.write.archive', [ 'contactGroupIdOrUid']) }}", {'contactGroupIdOrUid': __tData._uid}) %>" class="btn btn-light btn-sm lw-ajax-link-action-via-confirm" data-confirm="#lwArchiveGroup-template" title="{{ __tr('Archive') }}" data-callback-params="{{ json_encode(['datatableId' => '#lwGroupList']) }}" data-callback="appFuncs.modelSuccessCallback">{{  __tr('Archive') }}</a>
                <% } else { %>
                    <a data-method="post" href="<%= __Utils.apiURL("{{ route('vendor.contact.group.write.unarchive', [ 'contactGroupIdOrUid']) }}", {'contactGroupIdOrUid': __tData._uid}) %>" class="btn btn-light btn-sm lw-ajax-link-action-via-confirm" data-confirm="#lwUnarchiveGroup-template" title="{{ __tr('Unarchive') }}" data-callback-params="{{ json_encode(['datatableId' => '#lwGroupList']) }}" data-callback="appFuncs.modelSuccessCallback">{{  __tr('Unarchive') }}</a>

                <% } %>
                  <!--  Archive Action -->
    </script>
        <!-- /action template -->
        <!-- select multiple -->
        <script type="text/template" id="lwSelectMultipleContactGroupsCheckbox">
            <input @click="toggle('<%- __tData._uid %>')" type="checkbox" name="selected_groups[]" class="lw-checkboxes custom-checkbox" value="<%- __tData._uid %>">
        </script>
        <!-- /select multiple -->

        <!-- Group delete template -->
        <script type="text/template" id="lwDeleteGroup-template">
            <h2>{{ __tr('Are You Sure!') }}</h2>
            <p>{{ __tr('You want to delete this Group?') }}</p>
    </script>
        <!-- /Group delete template -->
        <!-- Group archive template -->
        <script type="text/template" id="lwArchiveGroup-template">
            <h2>{{ __tr('Are You Sure!') }}</h2>
            <p>{{ __tr('You want to archive this Group?') }}</p>
    </script>
        <!-- /Group archive template -->
        <!-- Group unarchive template -->
        <script type="text/template" id="lwUnarchiveGroup-template">
            <h2>{{ __tr('Are You Sure!') }}</h2>
            <p>{{ __tr('You want to unarchive this Group?') }}</p>
    </script>
        <!-- /Group archive template -->
    </div>
</div>
@endsection()